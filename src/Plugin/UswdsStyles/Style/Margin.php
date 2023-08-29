<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\Style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\ResponsiveTrait;
use Drupal\uswds_blb_configuration\Style\StylePluginBase;

/**
 * Margin style plugin.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\Style
 *
 * @Style(
 *   id = "margin",
 *   title = @Translation("Margin"),
 *   group_id = "spacing",
 *   weight = 2
 * )
 */
class Margin extends StylePluginBase {

  use ResponsiveTrait;

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->config();

    $directions = [
      'left',
      'top',
      'right',
      'bottom',
    ];

    $form['spacing']['margin_description'] = [
      '#type' => 'item',
      '#title' => $this->t('Margin'),
      '#markup' => $this->t('<p>Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the .), and <em>label</em> is the human readable name of the margin. <br /> <b>Note:</b> These options will be reflected on the range slider at the Layout Builder form, so make sure to sort them from lowest to greatest.</p>'),
    ];

    $form['spacing']['margin_group'] = [
      '#type' => 'container',
      '#title' => $this->t('Margin'),
      '#title_display' => 'invisible',
      '#tree' => FALSE,
      '#attributes' => [
        'class' => [
          'uswds-admin-group-form-item-lg-ml',
        ],
      ],
    ];

    // Responsive.
    // Loop through the breakpoints keys.
    foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
      $this->createBreakpointFormField($form, $breakpoint_key, 'margin_group', ['spacing']);
    }

    $form['spacing']['margin_group']['margin'] = [
      '#type' => 'textarea',
      '#default_value' => $config->get('margin'),
      '#title' => $this->t('Margin (classes)'),
      '#cols' => 60,
      '#rows' => 5,
    ];

    // Responsive.
    // Loop through the breakpoints keys.
    foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
      // Then create a field for breakpoint.
      $this->createBreakpointFormField(
        $form,
        $breakpoint_key,
        'margin',
        [
          'spacing',
          'margin_group',
        ],
        [
          'spacing',
          'margin_group_' . $breakpoint_key,
        ]
      );
    }

    for ($i = 0; $i < 4; $i++) {
      $form['spacing']['margin_group']['margin_' . $directions[$i]] = [
        '#type' => 'textarea',
        '#default_value' => $config->get('margin_' . $directions[$i]),
        '#title' => $this->t('Margin @direction (classes)', ['@direction' => $directions[$i]]),
        '#cols' => 60,
        '#rows' => 5,
      ];

      // Responsive.
      // Loop through the breakpoints keys.
      foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
        // Then create a field for each breakpoint.
        $this->createBreakpointFormField(
          $form,
          $breakpoint_key,
          'margin_' . $directions[$i],
          [
            'spacing',
            'margin_group',
          ],
          [
            'spacing',
            'margin_group_' . $breakpoint_key,
          ],
        );
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->config()
      ->set('margin', $form_state->getValue('margin'))
      ->set('margin_left', $form_state->getValue('margin_left'))
      ->set('margin_top', $form_state->getValue('margin_top'))
      ->set('margin_right', $form_state->getValue('margin_right'))
      ->set('margin_bottom', $form_state->getValue('margin_bottom'))
      ->save();

    // Responsive.
    $fields = [
      'margin',
      'margin_left',
      'margin_top',
      'margin_right',
      'margin_bottom',
    ];

    $this->submitBreakpointsConfigurationForm($form_state, $fields);
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $directions = [
      'left',
      'top',
      'right',
      'bottom',
    ];

    // This only for frontend no storage needed for this field.
    $form['margin_type'] = [
      '#type' => 'radios',
      '#options' => [
        'margin' => $this->t('Margin') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('All') . '</div>',
        'margin_left' => $this->t('Left') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Left') . '</div>',
        'margin_top' => $this->t('Top') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Top') . '</div>',
        'margin_right' => $this->t('Right') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Right') . '</div>',
        'margin_bottom' => $this->t('Bottom') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Bottom') . '</div>',
      ],
      '#title' => $this->t('margin type'),
      '#title_display' => 'invisible',
      '#default_value' => 'margin',
      '#validated' => TRUE,
      '#attributes' => [
        'class' => [
          'uswds_input-boxes',
          'uswds_input-boxes--box-model',
          'uswds_margin--type',
        ],
      ],
      '#disable_live_preview' => TRUE,
    ];

    $default_value = 0;
    if (isset($storage['margin']['class'])) {
      $default_value = $this->getStyleOptionIndexByClass('margin', $storage['margin']['class']);
    }

    $form['margin'] = [
      '#type' => 'range',
      '#title' => $this->t('Margin'),
      '#min' => 0,
      '#max' => $this->getStyleOptionsCount('margin'),
      '#step' => 1,
      '#default_value' => $default_value,
      '#attributes' => [
        'class' => ['uswds-field-margin'],
      ],
      '#states' => [
        'visible' => [
          ':input.uswds_margin--type' => ['value' => 'margin'],
        ],
      ],
    ];

    // Responsive.
    $this->createBreakpointsStyleFormClassIndexBasedFields($form, 'margin', 'spacing', $storage);

    // Loop through the directions.
    for ($i = 0; $i < 4; $i++) {
      $default_value = 0;
      if (isset($storage['margin_' . $directions[$i]]['class'])) {
        $default_value = $this->getStyleOptionIndexByClass('margin_' . $directions[$i], $storage['margin_' . $directions[$i]]['class']);
      }

      $form['margin_' . $directions[$i]] = [
        '#type' => 'range',
        '#title' => $this->t('Margin @direction', ['@direction' => $directions[$i]]),
        '#min' => 0,
        '#max' => $this->getStyleOptionsCount('margin_' . $directions[$i]),
        '#step' => 1,
        '#default_value' => $default_value,
        '#attributes' => [
          'class' => ['uswds-field-margin-' . $directions[$i]],
        ],
        '#states' => [
          'visible' => [
            ':input.uswds_margin--type' => ['value' => 'margin_' . $directions[$i]],
          ],
        ],
      ];

      // Responsive.
      $this->createBreakpointsStyleFormClassIndexBasedFields($form, 'margin_' . $directions[$i], 'spacing', $storage);
    }

    // Pass margin options to drupal settings.
    $margin_options = [];
    $margin_options['margin'] = array_keys($this->getStyleOptions('margin'));

    // Responsive.
    foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
      $margin_options['margin_' . $breakpoint_key] = array_keys($this->getStyleOptions('margin_' . $breakpoint_key));
    }

    for ($i = 0; $i < 4; $i++) {
      $margin_options['margin_' . $directions[$i]] = array_keys($this->getStyleOptions('margin_' . $directions[$i]));

      // Responsive.
      foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
        $margin_options['margin_' . $directions[$i] . '_' . $breakpoint_key] = array_keys($this->getStyleOptions('margin_' . $directions[$i] . '_' . $breakpoint_key));
      }
    }
    $form['#attached']['drupalSettings']['uswds_blb_configuration']['spacing']['margin_classes_options'] = $margin_options;
    // Responsive.
    $form['#attached']['drupalSettings']['uswds_blb_configuration']['breakpoints'] = $this->getBreakpointsKeys();

    // Attach the Layout Builder form style for this plugin.
    $form['#attached']['library'][] = 'uswds_blb_configuration/plugin.margin.layout_builder_form';
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    $directions = [
      'left',
      'top',
      'right',
      'bottom',
    ];

    $schema = [
      'margin' => [
        'class' => $this->getStyleOptionClassByIndex('margin', $group_elements['margin']),
      ],
    ];

    // Responsive.
    $responsive_target_fields = [
      'margin',
    ];

    for ($i = 0; $i < 4; $i++) {
      $schema['margin_' . $directions[$i]]['class'] = $this->getStyleOptionClassByIndex('margin_' . $directions[$i], $group_elements['margin_' . $directions[$i]]);
      // Responsive.
      $responsive_target_fields[] = 'margin_' . $directions[$i];
    }

    // Responsive.
    $this->saveBreakpointsStyleFormClassIndexBasedFields($group_elements, $schema, $responsive_target_fields);

    return $schema;
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    $classes = [];
    $directions = [
      'left',
      'top',
      'right',
      'bottom',
    ];

    // Responsive.
    $responsive_target_fields = [
      'margin',
    ];

    if (isset($storage['margin']['class'])) {
      $classes[] = $storage['margin']['class'];
    }

    for ($i = 0; $i < 4; $i++) {
      if (isset($storage['margin_' . $directions[$i]]['class'])) {
        $classes[] = $storage['margin_' . $directions[$i]]['class'];
        // Responsive.
        $responsive_target_fields[] = 'margin_' . $directions[$i];
      }
    }

    // Responsive.
    $this->buildBreakpoints($classes, $storage, $responsive_target_fields);

    // Add the classes to the build.
    return $this->addClassesToBuild($build, $classes, $theme_wrapper);
  }

}
