<?php

namespace Drupal\uswds_blb_configuration\Plugin\Layout;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Layout\LayoutDefault;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\Core\Serialization\Yaml;
use Drupal\uswds_blb_configuration\Ajax\RefreshResponsive;
use Drupal\uswds_blb_configuration\ResponsiveTrait;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A layout from our uswds  layout builder.
 *
 * @Layout(
 *   id = "uswds_blb_configuration",
 *   deriver =
 *   "Drupal\uswds_blb_configuration\Plugin\Deriver\UswdsLayoutDeriver"
 * )
 */
class UswdsLayout extends LayoutDefault implements ContainerFactoryPluginInterface {

  use ResponsiveTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The styles group plugin manager.
   *
   * @var \Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager
   */
  protected $stylesGroupManager;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param object $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Config factory service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager $styles_group_manager
   *   The styles group plugin manager.
   */
  public function __construct(array $configuration, $plugin_id, object $plugin_definition, ConfigFactoryInterface $config_factory, EntityTypeManagerInterface $entity_type_manager, StylesGroupManager $styles_group_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->stylesGroupManager = $styles_group_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.uswds_styles_group')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $regions) {
    $build = parent::build($regions);

    // Row classes and attributes.
    $section_classes = [];
    if ($this->configuration['section_classes']) {
      $section_classes = explode(' ', $this->configuration['section_classes']);
      $build['#attributes']['class'] = $section_classes;
    }

    if (!empty($this->configuration['section_attributes'])) {
      $section_attributes = $this->configuration['section_attributes'];
      $build['#attributes'] = NestedArray::mergeDeep($build['#attributes'] ?? [], $section_attributes);
    }

    // The default one col layout class.
    if (count($this->getPluginDefinition()->getRegionNames()) == 1) {
      $config = $this->configFactory->get('uswds_blb_configuration.settings');
      $one_col_layout_class = 'col-12';
      if ($config->get('one_col_layout_class')) {
        $one_col_layout_class = $config->get('one_col_layout_class');
      }
      $this->configuration['layout_regions_classes']['uswds_region_col_1'][] = $one_col_layout_class;
    }

    // Regions classes and attributes.
    if ($this->configuration['regions_classes']) {
      foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
        $region_classes = $this->configuration['regions_classes'][$region_name];
        if ($this->configuration['layout_regions_classes'] && isset($this->configuration['layout_regions_classes'][$region_name])) {
          $build[$region_name]['#attributes']['class'] = $this->configuration['layout_regions_classes'][$region_name];
        }
        $build[$region_name]['#attributes']['class'][] = $region_classes;
      }
    }

    if ($this->configuration['regions_attributes']) {
      foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
        $region_attributes = $this->configuration['regions_attributes'][$region_name];
        if (!empty($region_attributes)) {
          $build[$region_name]['#attributes'] = NestedArray::mergeDeep($build[$region_name]['#attributes'] ?? [], $region_attributes);
        }
      }
    }

    // Container.
    if ($this->configuration['container']) {
      $theme_wrappers = [
        'uswds_container' => [
          '#attributes' => [
            'class' => [$this->configuration['container']],
          ],
        ],
        'uswds_container_wrapper' => [
          '#attributes' => [
            'class' => [],
          ],
        ],
      ];

      if ($this->configuration['container_wrapper_classes']) {
        $theme_wrappers['uswds_container_wrapper']['#attributes']['class'][] = $this->configuration['container_wrapper_classes'];
      }

      if (!empty($this->configuration['container_wrapper_attributes'])) {
        $wrapper_attributes = $this->configuration['container_wrapper_attributes'];
        $theme_wrappers['uswds_container_wrapper']['#attributes'] = NestedArray::mergeDeep($theme_wrappers['uswds_container_wrapper']['#attributes'] ?? [], $wrapper_attributes);
      }

      $build['#theme_wrappers'] = $theme_wrappers;

      // Build dynamic styles.
      $build = $this->stylesGroupManager->buildStyles(
        $build,
        // storage.
        $this->configuration['container_wrapper']['uswds_blb_configuration'],
        // Theme wrapper that we need to apply styles to it.
        'uswds_container_wrapper'
      );
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $default_configuration = parent::defaultConfiguration();

    $regions_classes = $regions_attributes = [];
    foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
      $regions_classes[$region_name] = '';
      $regions_attributes[$region_name] = [];
    }

    return $default_configuration + [
      // Container wrapper commonly used on container background and
      // minor styling.
      'container_wrapper_classes' => '',
      'container_wrapper_attributes' => [],
      // Container wrapper.
      'container_wrapper' => [
        'uswds_blb_configuration' => [],
      ],
      // Add background color to container wrapper.
      'container_wrapper_bg_color_class' => '',
      // Add background media to container wrapper.
      'container_wrapper_bg_media' => NULL,
      // Container is the section wrapper.
      // Empty means no container else it reflects container type.
      'container' => '',
      // Section refer to the div that contains row in uswds.
      'section_classes' => '',
      'section_attributes' => [],
      // Region refer to the div that contains Col in uswds "Advanced mode".
      'regions_classes' => $regions_classes,
      'regions_attributes' => $regions_attributes,
      // Array of breakpoints and the value of its option.
      'breakpoints' => [],
      // The region refer to the div that contains Col in uswds.
      'layout_regions_classes' => [],
    ];
  }

  /**
   * Helper function to get section settings show/hide status.
   *
   * @return bool
   *   Section settings status.
   */
  public function sectionSettingsIsHidden() {
    $config = $this->configFactory->get('uswds_blb_configuration.settings');
    $hide_section_settings = FALSE;
    if ($config->get('hide_section_settings')) {
      $hide_section_settings = (bool) $config->get('hide_section_settings');
    }
    return $hide_section_settings;
  }

  /**
   * Helper function to get live preview status.
   *
   * @return bool
   *   Live preview status.
   */
  public function livePreviewIsEnabled() {
    $config = $this->configFactory->get('uswds_blb_configuration.settings');
    $live_preview = FALSE;
    if ($config->get('live_preview')) {
      $live_preview = (bool) $config->get('live_preview');
    }
    return $live_preview;
  }

  /**
   * Helper function to get responsive status.
   *
   * @return bool
   *   responsive status.
   */
  public function responsiveIsEnabled() {
    $config = $this->configFactory->get('uswds_blb_configuration.settings');
    $responsive = FALSE;
    if ($config->get('responsive')) {
      $responsive = (bool) $config->get('responsive');
    }
    return $responsive;
  }

  /**
   * Helper function to get the options of given style name.
   *
   * @param string $name
   *   A config style name like background_color.
   *
   * @return array
   *   Array of key => value of style name options.
   */
  public function getStyleOptions(string $name) {
    $config = $this->configFactory->get('uswds_blb_configuration.settings');
    $config_options = $config->get($name);

    $options = ['_none' => $this->t('N/A')];
    $lines = explode(PHP_EOL, $config_options);
    foreach ($lines as $line) {
      $line = explode('|', $line);
      if ($line && isset($line[0]) && isset($line[1])) {
        $options[$line[0]] = $line[1];
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    // Our main set of tabs.
    $form['ui'] = [
      '#type' => 'container',
      '#weight' => -100,
      '#attributes' => [
        'id' => 'uswds_lb_ui',
      ],
    ];

    $tabs = [
      [
        'machine_name' => 'layout',
        'icon' => 'layout.svg',
        'title' => $this->t('Layout'),
        'active' => TRUE,
      ],
      [
        'machine_name' => 'appearance',
        'icon' => 'appearance.svg',
        'title' => $this->t('Style'),
      ],
      [
        'machine_name' => 'settings',
        'icon' => 'settings.svg',
        'title' => $this->t('Settings'),
      ],
    ];

    // Create our tabs from above.
    $form['ui']['nav_tabs'] = [
      '#type' => 'html_tag',
      '#tag' => 'ul',
      '#attributes' => [
        'class' => 'uswds_nav-tabs',
        'id' => 'uswds_nav-tabs',
        'role' => 'tablist',
      ],
    ];

    $form['ui']['tab_content'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'uswds_tab-content',
        'id' => 'uswds_tabContent',
      ],
    ];

    // Create our tab & tab panes.
    foreach ($tabs as $tab) {
      $form['ui']['nav_tabs'][$tab['machine_name']] = [
        '#type' => 'inline_template',
        '#template' => '<li><a data-target="{{ target|clean_class }}" class="{{active}}"><span role="img">{% include icon %}</span><div class="uswds_tooltip" data-placement="bottom" role="tooltip">{{ title }}</div></a></li>',
        '#context' => [
          'title' => $tab['title'],
          'target' => $tab['machine_name'],
          'active' => isset($tab['active']) && $tab['active'] ? 'active' : '',
          'icon' => \Drupal::service('extension.list.module')->getPath('uswds_blb_configuration') . '/images/ui/' . ($tab['icon'] ?: 'default.svg'),
        ],
      ];

      $form['ui']['tab_content'][$tab['machine_name']] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => [
            'uswds_tab-pane',
            'uswds_tab-pane--' . $tab['machine_name'],
            isset($tab['active']) && $tab['active'] == TRUE ? 'active' : '',
          ],
        ],
      ];
    }

    // Get Bootstrap Layout Builder deafult layout options and defaults.
    $uswds_layout_defaults = \Drupal::config('uswds_blb_configuration.layout_defaults');

    // Container type defaults.
    $container_type_defaults = $uswds_layout_defaults->get('container_type');

    // Container types.
    $container_types = [];
    if (isset($container_type_defaults['form_options'])) {
      $container_types = $container_type_defaults['form_options'];
    }

    // Container type default value.
    $container_type_default_value = '';
    if (!empty($this->configuration['container'])) {
      $container_type_default_value = $this->configuration['container'];
    }
    else {
      if (isset($container_type_defaults['default_value'])) {
        $container_type_default_value = $container_type_defaults['default_value'];
      }
    }

    // Container type weight.
    $container_type_weight = '';
    if (isset($container_type_defaults['weight'])) {
      $container_type_weight = $container_type_defaults['weight'];
    }

    $form['ui']['tab_content']['layout']['container_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Container type'),
      '#options' => $container_types,
      '#default_value' => $container_type_default_value,
      '#attributes' => [
        'class' => ['uswds_lb_container_type'],
      ],
      "#weight" => $container_type_weight,
    ];

    // Add icons to the container types.
    foreach ($form['ui']['tab_content']['layout']['container_type']['#options'] as $key => $value) {
      $form['ui']['tab_content']['layout']['container_type']['#options'][$key] = '<span class="input-icon ' . $key . '"></span>' . $value;
    }

    // Remove gutters defaults.
    $remove_gutters_defaults = $uswds_layout_defaults->get('remove_gutters');

    // Gutters types.
    $gutter_types = [];
    if (isset($remove_gutters_defaults['form_options'])) {
      $gutter_types = $remove_gutters_defaults['form_options'];
    }

    // Remove gutters default value.
    $remove_gutters_default_value = 0;
    if (isset($this->configuration['remove_gutters'])) {
      $remove_gutters_default_value = (int) $this->configuration['remove_gutters'];
    }
    else {
      if (isset($remove_gutters_defaults['default_value'])) {
        $remove_gutters_default_value = (int) $remove_gutters_defaults['default_value'];
      }
    }

    // Remove gutters weight.
    $remove_gutters_weight = '';
    if (isset($remove_gutters_defaults['weight'])) {
      $remove_gutters_weight = $remove_gutters_defaults['weight'];
    }

    $form['ui']['tab_content']['layout']['remove_gutters'] = [
      '#type' => 'radios',
      '#title' => $this->t('Gutters'),
      '#options' => $gutter_types,
      '#default_value' => $remove_gutters_default_value,
      '#attributes' => [
        'class' => ['blb_gutter_type'],
      ],
      '#weight' => $remove_gutters_weight,
    ];

    // Add icons to the gutter types.
    foreach ($form['ui']['tab_content']['layout']['remove_gutters']['#options'] as $key => $value) {
      $form['ui']['tab_content']['layout']['remove_gutters']['#options'][$key] = '<span class="input-icon gutter-icon-' . $key . '"></span>' . $value;
    }

    $layout_id = $this->getPluginDefinition()->id();
    $breakpoints = $this->entityTypeManager->getStorage('uswds_breakpoint')
      ->getQuery()
      ->sort('weight', 'ASC')
      ->execute();
    foreach ($breakpoints as $breakpoint_id) {
      $breakpoint = $this->entityTypeManager->getStorage('uswds_breakpoint')
        ->load($breakpoint_id);
      $layout_options = $breakpoint->getLayoutOptions($layout_id);
      if ($layout_options) {
        $default_value = NULL;
        if ($this->configuration['breakpoints'] && isset($this->configuration['breakpoints'][$breakpoint_id])) {
          $default_value = $this->configuration['breakpoints'][$breakpoint_id];
        }
        else {
          $options = $this->entityTypeManager->getStorage('uswds_layout_option')
            ->loadByProperties(['layout_id' => $layout_id]);
          foreach ($options as $layoutOption) {
            if (array_search($breakpoint->id(), $layoutOption->getDefaultBreakpointsIds()) !== FALSE) {
              $default_value = $layoutOption->getStructureId();
            }
          }
        }
        $form['ui']['tab_content']['layout']['breakpoints'][$breakpoint_id] = [
          '#type' => 'radios',
          '#title' => $breakpoint->label(),
          '#options' => $layout_options,
          '#default_value' => $default_value,
          '#validated' => TRUE,
          '#attributes' => [
            'class' => ['uswds_breakpoint_cols'],
          ],
        ];

        // Check if the live preview enabled.
        if ($this->livePreviewIsEnabled()) {
          $form['ui']['tab_content']['layout']['breakpoints'][$breakpoint_id]['#ajax']['callback'] = [
            $this,
            'livePreviewCallback',
          ];
          $form['ui']['tab_content']['layout']['breakpoints'][$breakpoint_id]['#ajax']['event'] = 'click';
          $form['ui']['tab_content']['layout']['breakpoints'][$breakpoint_id]['#ajax']['progress'] = ['type' => 'none'];
        }
      }
    }

    if (isset($form['ui']['tab_content']['layout']['breakpoints'])) {
      // Breakpoints defaults.
      $breakpoints_defaults = $uswds_layout_defaults->get('breakpoints');

      // Breakpoints weight.
      if (isset($breakpoints_defaults['weight'])) {
        $form['ui']['tab_content']['layout']['breakpoints']['#weight'] = $breakpoints_defaults['weight'];
      }
    }

    // Container wrapper styling.
    $form['ui']['tab_content']['appearance'] = $this->stylesGroupManager->buildStylesFormElements($form['ui']['tab_content']['appearance'], $form_state, $this->configuration['container_wrapper']['uswds_blb_configuration'], 'uswds_blb_configuration.sections_styles');

    // Move default admin label input to setting tab.
    $form['ui']['tab_content']['settings']['label'] = $form['label'];
    unset($form['label']);

    // Advanced Settings.
    if (!$this->sectionSettingsIsHidden()) {
      $form['ui']['tab_content']['settings']['container'] = [
        '#type' => 'details',
        '#title' => $this->t('Container Settings'),
        '#open' => FALSE,
      ];

      $form['ui']['tab_content']['settings']['container']['container_wrapper_classes'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Container wrapper classes'),
        '#description' => $this->t('Add classes separated by space. Ex: bg-warning py-5.'),
        '#default_value' => $this->configuration['container_wrapper_classes'],
      ];

      $container_attributes = $this->configuration['container_wrapper_attributes'];
      $form['ui']['tab_content']['settings']['container']['container_wrapper_attributes'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Container wrapper attributes (YAML)'),
        '#default_value' => empty($container_attributes) ? '' : Yaml::encode($container_attributes),
        '#attributes' => ['class' => ['blb-auto-size']],
        '#rows' => 1,
        '#element_validate' => [[$this, 'validateYaml']],
      ];

      $form['ui']['tab_content']['settings']['row'] = [
        '#type' => 'details',
        '#title' => $this->t('Row Settings'),
        '#description' => $this->t('Add classes separated by space. Ex: col mb-5 py-3.'),
        '#open' => FALSE,
      ];

      $form['ui']['tab_content']['settings']['row']['section_classes'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Row classes'),
        '#description' => $this->t('Row has "row" class, you can add more classes separated by space. Ex: no-gutters py-3.'),
        '#default_value' => $this->configuration['section_classes'],
      ];

      $row_attributes = $this->configuration['section_attributes'];
      $form['ui']['tab_content']['settings']['row']['section_attributes'] = [
        '#type' => 'textarea',
        '#title' => $this->t('Row attributes (YAML)'),
        '#default_value' => empty($row_attributes) ? '' : Yaml::encode($row_attributes),
        '#attributes' => ['class' => ['auto-size']],
        '#rows' => 1,
        '#element_validate' => [[$this, 'validateYaml']],
      ];

      $form['ui']['tab_content']['settings']['regions'] = [
        '#type' => 'details',
        '#title' => $this->t('Columns Settings'),
        '#description' => $this->t('Add classes separated by space. Ex: col mb-5 py-3.'),
        '#open' => FALSE,
      ];

      foreach ($this->getPluginDefinition()->getRegionNames() as $region_name) {
        $form['ui']['tab_content']['settings']['regions'][$region_name . '_classes'] = [
          '#type' => 'textfield',
          '#title' => $this->getPluginDefinition()->getRegionLabels()[$region_name] . ' ' . $this->t('classes'),
          '#default_value' => $this->configuration['regions_classes'][$region_name],
        ];

        $region_attributes = $this->configuration['regions_attributes'][$region_name];
        $form['ui']['tab_content']['settings']['regions'][$region_name . '_attributes'] = [
          '#type' => 'textarea',
          '#title' => $this->getPluginDefinition()->getRegionLabels()[$region_name] . ' ' . $this->t('attributes (YAML)'),
          '#default_value' => empty($region_attributes) ? '' : Yaml::encode($region_attributes),
          '#attributes' => ['class' => ['auto-size']],
          '#rows' => 1,
          '#element_validate' => [[$this, 'validateYaml']],
        ];

      }
    }

    // Check if the live preview enabled.
    if ($this->livePreviewIsEnabled()) {
      // Add the ajax live preview to form elements.
      $this->addAjaxLivePreviewToElements($form['ui']['tab_content']);
    }
    // Check if the responsive enabled.
    if ($this->responsiveIsEnabled()) {
      // Attach USWDS Styles base library.
      $form['#attached']['library'][] = 'uswds_blb_configuration/layout_builder_form_style';
    }

    return $form;
  }

  /**
   * Add ajax preview element.
   */
  public function addAjaxLivePreviewToElements(array &$element) {
    $types = [
      'radios',
      'radio',
      'checkbox',
      'textfield',
      'textarea',
      'range',
    ];

    if (!isset($element['#type'])) {
      return;
    }

    if (in_array($element['#type'], $types) && !isset($element['#ajax']) && !isset($element['#disable_live_preview'])) {
      $element['#ajax']['callback'] = [$this, 'livePreviewCallback'];
      $element['#ajax']['event'] = 'change';
      $element['#ajax']['progress'] = ['type' => 'none'];
    }

    if (Element::children($element)) {
      foreach (Element::children($element) as $key) {
        $this->addAjaxLivePreviewToElements($element[$key]);
      }
    }
  }

  /**
   * Live preview callback.
   */
  public function livePreviewCallback(array $form, FormStateInterface $form_state) {
    $form_state->getFormObject()->submitForm($form, $form_state);
    $layout = [
      '#type' => 'layout_builder',
      '#section_storage' => $form_state->getFormObject()->getSectionStorage(),
    ];

    $data = [];
    $tempstore = \Drupal::service('tempstore.private')
      ->get('uswds_blb_configuration');
    $data['active_device'] = $tempstore->get('active_device');

    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand('#layout-builder', $layout));
    $response->addCommand(new RefreshResponsive('#layout-builder', NULL, $data));

    return $response;
  }

  /**
   * Returns region class of a breakpoint.
   *
   * @param int $key
   *   The position of region.
   * @param array $breakpoints
   *   The layout active breakpoints.
   *
   * @return array
   *   The region classes of all breakpoints.
   */
  public function getRegionClasses(int $key, array $breakpoints) {
    $classes = [];
    foreach ($breakpoints as $breakpoint_id => $strucutre_id) {
      $breakpoint = $this->entityTypeManager->getStorage('uswds_breakpoint')
        ->load($breakpoint_id);
      $classes[] = $breakpoint->getClassByPosition($key, $strucutre_id);
    }
    return $classes;
  }

  /**
   * Save breakpoints to the configuration.
   *
   * @param array $breakpoints
   *   The layout active breakpoints.
   */
  public function saveBreakpoints(array $breakpoints) {
    $this->configuration['breakpoints'] = $breakpoints;
  }

  /**
   * {@inheritdoc}
   */
  public function validateYaml($element, FormStateInterface $form_state, array $form) {
    $value = $element['#value'];
    try {
      $array_values = Yaml::decode($value);

      // Fix Classes as strings.
      if (isset($array_values['class']) && !is_array($array_values['class'])) {
        $array_values['class'] = explode(' ', $array_values['class']);
      }
      $form_state->setValueForElement($element, Yaml::encode($array_values));
    }
    catch (\Exception $exception) {
      $form_state->setError($element, $this->t('Invalid YAML entered for %field', ['%field' => $element['#title']]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    // The tabs structure.
    $layout_tab = ['ui', 'tab_content', 'layout'];
    $style_tab = ['ui', 'tab_content', 'appearance'];
    $settings_tab = ['ui', 'tab_content', 'settings'];

    // Save section label.
    $this->configuration['label'] = $form_state->getValue(array_merge($settings_tab, ['label']));

    // Container type.
    $this->configuration['container'] = $form_state->getValue(array_merge($layout_tab, ['container_type']));

    // Styles tab.
    $this->configuration['container_wrapper']['uswds_blb_configuration'] = $this->stylesGroupManager->submitStylesFormElements($form['ui']['tab_content']['appearance'], $form_state, $style_tab, $this->configuration['container_wrapper']['uswds_blb_configuration'], 'uswds_blb_configuration.sections_styles');

    // Container classes from advanced mode.
    if (!$this->sectionSettingsIsHidden()) {
      $this->configuration['container_wrapper_classes'] = $form_state->getValue(array_merge($settings_tab, [
        'container',
        'container_wrapper_classes',
      ]));
      $this->configuration['container_wrapper_attributes'] = Yaml::decode($form_state->getValue(array_merge($settings_tab, [
        'container',
        'container_wrapper_attributes',
      ])));
    }

    // Gutter Classes.
    $this->configuration['remove_gutters'] = $form_state->getValue(array_merge($layout_tab, ['remove_gutters']));

    // Row classes from advanced mode.
    if (!$this->sectionSettingsIsHidden()) {
      $this->configuration['section_classes'] = $form_state->getValue(array_merge($settings_tab, [
        'row',
        'section_classes',
      ]));
      $this->configuration['section_attributes'] = Yaml::decode($form_state->getValue(array_merge($settings_tab, [
        'row',
        'section_attributes',
      ])));
    }

    $breakpoints = $form_state->getValue(array_merge($layout_tab, ['breakpoints']));
    // Save breakpoints configuration.
    if ($breakpoints) {
      $this->saveBreakpoints($breakpoints);
      foreach ($this->getPluginDefinition()->getRegionNames() as $key => $region_name) {
        // Save layout region classes.
        $this->configuration['layout_regions_classes'][$region_name] = $this->getRegionClasses($key, $breakpoints);
        // Cols classes from advanced mode.
        if (!$this->sectionSettingsIsHidden()) {
          $this->configuration['regions_classes'][$region_name] = $form_state->getValue(array_merge($settings_tab, [
            'regions',
            $region_name . '_classes',
          ]));
          $this->configuration['regions_attributes'][$region_name] = Yaml::decode($form_state->getValue(array_merge($settings_tab, [
            'regions',
            $region_name . '_attributes',
          ])));
        }
      }
    }
    else {
      foreach ($this->getPluginDefinition()->getRegionNames() as $key => $region_name) {
        // Cols classes from advanced mode.
        if (!$this->sectionSettingsIsHidden()) {
          $this->configuration['regions_classes'][$region_name] = $form_state->getValue(array_merge($settings_tab, [
            'regions',
            $region_name . '_classes',
          ]));
          $this->configuration['regions_attributes'][$region_name] = Yaml::decode($form_state->getValue(array_merge($settings_tab, [
            'regions',
            $region_name . '_attributes',
          ])));
        }
      }
    }
  }

}
