<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\Style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\ResponsiveTrait;
use Drupal\uswds_blb_configuration\Style\StylePluginBase;

/**
 * Padding style plugin.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\Style
 *
 * @Style(
 *   id = "padding",
 *   title = @Translation("Padding"),
 *   group_id = "spacing",
 *   weight = 1
 * )
 */
class Padding extends StylePluginBase {

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

    $form['spacing']['padding_description'] = [
      '#type' => 'item',
      '#title' => $this->t('Padding'),
      '#markup' => $this->t('<p>Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the .), and <em>label</em> is the human readable name of the padding. <br /> <b>Note:</b> These options will be reflected on the range slider at the Layout Builder form, so make sure to sort them from lowest to greatest.</p>'),
    ];

    $form['spacing']['padding_group'] = [
      '#type' => 'container',
      '#title' => $this->t('Padding'),
      '#title_display' => 'invisible',
      '#tree' => FALSE,
      '#attributes' => [
        'class' => [],
      ],
    ];

    // Responsive.
    // Loop through the breakpoints keys.
    foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
      $this->createBreakpointFormField($form, $breakpoint_key, 'padding_group', ['spacing']);
    }

    $form['spacing']['padding_group']['padding'] = [
      '#type' => 'textarea',
      '#default_value' => $config->get('padding'),
      '#title' => $this->t('Padding (classes)'),
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
        'padding',
        [
          'spacing',
          'padding_group',
        ],
        [
          'spacing',
          'padding_group_' . $breakpoint_key,
        ]
      );
    }

    for ($i = 0; $i < 4; $i++) {
      $form['spacing']['padding_group']['padding_' . $directions[$i]] = [
        '#type' => 'textarea',
        '#default_value' => $config->get('padding_' . $directions[$i]),
        '#title' => $this->t('Padding @direction (classes)', ['@direction' => $directions[$i]]),
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
          'padding_' . $directions[$i],
          [
            'spacing',
            'padding_group',
          ],
          [
            'spacing',
            'padding_group_' . $breakpoint_key,
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
      ->set('padding', $form_state->getValue('padding'))
      ->set('padding_left', $form_state->getValue('padding_left'))
      ->set('padding_top', $form_state->getValue('padding_top'))
      ->set('padding_right', $form_state->getValue('padding_right'))
      ->set('padding_bottom', $form_state->getValue('padding_bottom'))
      ->save();

    // Responsive.
    $fields = [
      'padding',
      'padding_left',
      'padding_top',
      'padding_right',
      'padding_bottom',
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
    $form['padding_type'] = [
      '#type' => 'radios',
      '#options' => [
        'padding' => $this->t('Padding') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('All') . '</div>',
        'padding_left' => $this->t('Left') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Left') . '</div>',
        'padding_top' => $this->t('Top') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Top') . '</div>',
        'padding_right' => $this->t('Right') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Right') . '</div>',
        'padding_bottom' => $this->t('Bottom') . '<div class="uswds_tooltip" data-placement="top" role="tooltip">' . $this->t('Bottom') . '</div>',
      ],
      '#title' => $this->t('padding type'),
      '#title_display' => 'invisible',
      '#default_value' => 'padding',
      '#validated' => TRUE,
      '#attributes' => [
        'class' => [
          'uswds_input-boxes',
          'uswds_input-boxes--box-model',
          'uswds_padding--type',
        ],
      ],
      '#disable_live_preview' => TRUE,
    ];

    $default_value = 0;
    if (isset($storage['padding']['class'])) {
      $default_value = $this->getStyleOptionIndexByClass('padding', $storage['padding']['class']);
    }

    $form['padding'] = [
      '#type' => 'range',
      '#title' => $this->t('Padding'),
      '#min' => 0,
      '#max' => $this->getStyleOptionsCount('padding'),
      '#step' => 1,
      '#default_value' => $default_value,
      '#attributes' => [
        'class' => ['uswds-field-padding'],
      ],
      '#states' => [
        'visible' => [
          ':input.uswds_padding--type' => ['value' => 'padding'],
        ],
      ],
    ];

    // Responsive.
    $this->createBreakpointsStyleFormClassIndexBasedFields($form, 'padding', 'spacing', $storage);

    // Loop through the directions.
    for ($i = 0; $i < 4; $i++) {
      $default_value = 0;
      if (isset($storage['padding_' . $directions[$i]]['class'])) {
        $default_value = $this->getStyleOptionIndexByClass('padding_' . $directions[$i], $storage['padding_' . $directions[$i]]['class']);
      }

      $form['padding_' . $directions[$i]] = [
        '#type' => 'range',
        '#title' => $this->t('Padding @direction', ['@direction' => $directions[$i]]),
        '#min' => 0,
        '#max' => $this->getStyleOptionsCount('padding_' . $directions[$i]),
        '#step' => 1,
        '#default_value' => $default_value,
        '#attributes' => [
          'class' => ['uswds-field-padding-' . $directions[$i]],
        ],
        '#states' => [
          'visible' => [
            ':input.uswds_padding--type' => ['value' => 'padding_' . $directions[$i]],
          ],
        ],
      ];

      // Responsive.
      $this->createBreakpointsStyleFormClassIndexBasedFields($form, 'padding_' . $directions[$i], 'spacing', $storage);
    }

    // Pass padding options to drupal settings.
    $padding_options = [];
    $padding_options['padding'] = array_keys($this->getStyleOptions('padding'));

    // Responsive.
    foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
      $padding_options['padding_' . $breakpoint_key] = array_keys($this->getStyleOptions('padding_' . $breakpoint_key));
    }

    for ($i = 0; $i < 4; $i++) {
      $padding_options['padding_' . $directions[$i]] = array_keys($this->getStyleOptions('padding_' . $directions[$i]));

      // Responsive.
      foreach ($this->getBreakpointsKeys() as $breakpoint_key) {
        $padding_options['padding_' . $directions[$i] . '_' . $breakpoint_key] = array_keys($this->getStyleOptions('padding_' . $directions[$i] . '_' . $breakpoint_key));
      }
    }
    $form['#attached']['drupalSettings']['uswds_blb_configuration']['spacing']['padding_classes_options'] = $padding_options;
    // Responsive.
    $form['#attached']['drupalSettings']['uswds_blb_configuration']['breakpoints'] = $this->getBreakpointsKeys();

    // Attach the Layout Builder form style for this plugin.
    $form['#attached']['library'][] = 'uswds_blb_configuration/plugin.padding.layout_builder_form';

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

    $storage = [
      'padding' => [
        'class' => $this->getStyleOptionClassByIndex('padding', $group_elements['padding']),
      ],
    ];

    // Responsive.
    $responsive_target_fields = [
      'padding',
    ];

    for ($i = 0; $i < 4; $i++) {
      $storage['padding_' . $directions[$i]]['class'] = $this->getStyleOptionClassByIndex('padding_' . $directions[$i], $group_elements['padding_' . $directions[$i]]);
      // Responsive.
      $responsive_target_fields[] = 'padding_' . $directions[$i];
    }

    // Responsive.
    $this->saveBreakpointsStyleFormClassIndexBasedFields($group_elements, $storage, $responsive_target_fields);

    return $storage;
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

    if (isset($storage['padding']['class'])) {
      $classes[] = $storage['padding']['class'];
    }

    // Responsive.
    $responsive_target_fields = [
      'padding',
    ];

    for ($i = 0; $i < 4; $i++) {
      if (isset($storage['padding_' . $directions[$i]]['class'])) {
        $classes[] = $storage['padding_' . $directions[$i]]['class'];
        // Responsive.
        $responsive_target_fields[] = 'padding_' . $directions[$i];
      }
    }

    // Responsive.
    $this->buildBreakpoints($classes, $storage, $responsive_target_fields);

    // Add the classes to the build.
    return $this->addClassesToBuild($build, $classes, $theme_wrapper);
  }

}
