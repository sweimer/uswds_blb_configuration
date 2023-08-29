<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\Style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\Style\StylePluginBase;

/**
 * Scroll effect style plugin.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\Style
 *
 * @Style(
 *   id = "scroll_effects",
 *   title = @Translation("Scroll Effects"),
 *   group_id = "animation",
 *   weight = 3
 * )
 */
class ScrollEffects extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->config();

    // Library assets.
    $form['animation']['scroll_effects_library_type'] = [
      '#type' => 'select',
      '#default_value' => $config->get('scroll_effects_library_type'),
      '#title' => $this->t('How should we load the animation library?'),
      '#required' => TRUE,
      '#options' => [
        'local' => $this->t('Do nothing, let my theme handle it'),
        'external' => $this->t('Add the library for me please'),
      ],
      '#description' => $this->t('<p>Default uses the AOS library: <a href="https://michalsnik.github.io/aos" target="_blank">https://michalsnik.github.io/aos</a><br/> You can override the animation library in your theme by using <br /><code>libraries-override:<br />&nbsp;&nbsp;uswds_blb_configuration/plugin.scroll_effects.build: your_theme/your_new_library_definition</code><br /><small>For more information, please check: <a href="https://www.drupal.org/node/2497313" target="_blank">https://www.drupal.org/node/2497313</a></small></p>'),
    ];

    $form['animation']['scroll_effects'] = [
      '#type' => 'textarea',
      '#default_value' => $config->get('scroll_effects'),
      '#title' => $this->t('Scroll Effects'),
      '#description' => $this->t("<p>Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the attribute's value, and <em>label</em> is the human-readable name of the effect.</p>"),
      '#cols' => 60,
      '#rows' => 5,
    ];

    $form['animation']['scroll_effects_attr_type'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('scroll_effects_attr_type'),
      '#title' => $this->t('Use data attribute instead of class.'),
    ];

    $form['animation']['scroll_effects_data_key'] = [
      '#type' => 'textfield',
      '#default_value' => $config->get('scroll_effects_data_key'),
      '#title' => $this->t('Data Key'),
      '#description' => $this->t('<p>The <strong>data_key</strong> will be used as the data attribute. Example: <code>data_key="key"</code>. <strong>>Default:</strong> <code>data-aos</code>".</p>'),
      '#states' => [
        'visible' => [
          [
            [':input[name="scroll_effects_attr_type"]' => ['checked' => TRUE]],
          ],
        ],
      ],
    ];

    $form['animation']['scroll_effects_advanced_settings'] = [
      '#type' => 'checkbox',
      '#default_value' => $config->get('scroll_effects_advanced_settings'),
      '#title' => $this->t('Show advanced animation settings.'),
      '#description' => $this->t('<p>Includes additional <a href="https://github.com/michalsnik/aos#2-set-animation-using-data-aos-attribute" target="_blank">advanced animation settings</a>. <em>(Offset, delay, easing type, mirror, once and placement.)</em></p>'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->config()
      ->set('scroll_effects_library_type', $form_state->getValue('scroll_effects_library_type'))
      ->set('scroll_effects_attr_type', (bool) $form_state->getValue('scroll_effects_attr_type'))
      ->set('scroll_effects_data_key', $form_state->getValue('scroll_effects_data_key'))
      ->set('scroll_effects', $form_state->getValue('scroll_effects'))
      ->set('scroll_effects_advanced_settings', (bool) $form_state->getValue('scroll_effects_advanced_settings'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $show_advanced_settings = $this->config()
      ->get('scroll_effects_advanced_settings');

    $form['scroll_effects'] = [
      '#type' => 'radios',
      '#options' => $this->getStyleOptions('scroll_effects'),
      '#title' => $this->t('Scroll Effects'),
      '#default_value' => $storage['scroll_effects']['class'] ?? NULL,
      '#validated' => TRUE,
      '#attributes' => [
        'class' => ['field-scroll-effects'],
      ],
    ];

    // Add icons to the effets.
    foreach ($form['scroll_effects']['#options'] as $key => $value) {
      $form['scroll_effects']['#options'][$key] = '<span class="input-icon ' . $key . '"></span>' . $value;
    }

    // Our advanced animation settings.
    if (isset($show_advanced_settings)) {
      $form['advanced'] = [
        '#type' => 'details',
        '#title' => $this->t('Advanced Options'),
        '#open' => FALSE,
      ];

      $form['advanced']['options'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['uswds_row'],
        ],
      ];

      // Anchor placements.
      $form['advanced']['options']['anchor_placement'] = [
        '#type' => 'select',
        '#title' => $this->t('Anchor Placement') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Defines which position of the element (relative to the window), should trigger the animation. Default: Top Bottom') . '</div>',
        '#options' => [
          'top-bottom' => $this->t('Top Bottom'),
          'top-center' => $this->t('Top Center'),
          'top-top' => $this->t('Top Top'),
          'center-bottom' => $this->t('Center Bottom'),
          'center-center' => $this->t('Center Center'),
          'center-top' => $this->t('Center Top'),
          'bottom-bottom' => $this->t('Bottom Bottom'),
          'bottom-center' => $this->t('Bottom Center'),
          'bottom-top' => $this->t('Bottom Top'),
          'custom' => $this->t('Custom Element'),
        ],
        '#default_value' => $storage['scroll_effects']['anchor_placement'] ?? NULL,
        '#validated' => TRUE,
        '#prefix' => '<div class="uswds_col uswds_col--100 uswds_full-label">',
        '#suffix' => '</div>',
      ];

      // Easing functions.
      $form['advanced']['options']['anchor_element'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Anchor Element') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Trigger animation on one element, while you scroll to another - useful in animating fixed elements.') . '</div>',
        '#default_value' => $storage['scroll_effects']['anchor_element'] ?? NULL,
        '#validated' => TRUE,
        '#pattern' => '[a-zA-Z][a-zA-Z0-9-_.]',
        '#prefix' => '<div class="uswds_col uswds_col--100 uswds_full-label">',
        '#suffix' => '</div>',
        '#states' => [
          'visible' => [
            [
              [':input[name="layout_settings[ui][tab_content][appearance][animation][advanced][options][anchor_placement]"]' => ['value' => 'custom']],
              [':input[name="appearance[animation][advanced][options][anchor_placement]"]' => ['value' => 'custom']],
            ],
          ],
          'required' => [
            [
              [':input[name="layout_settings[ui][tab_content][appearance][animation][advanced][options][anchor_placement]"]' => ['value' => 'custom']],
              [':input[name="appearance[animation][advanced][options][anchor_placement]"]' => ['value' => 'custom']],
            ],
          ],
        ],
      ];

      // Easing functions.
      $form['advanced']['options']['easing_functions'] = [
        '#type' => 'select',
        '#title' => $this->t('Easing Type') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Default easing for AOS animations. Default: Ease') . '</div>',
        '#options' => [
          'ease' => $this->t('Ease'),
          'ease-in' => $this->t('Ease In'),
          'ease-out' => $this->t('Ease Out'),
          'ease-in-back' => $this->t('Ease In Back'),
          'ease-out-back' => $this->t('Ease Out Back'),
          'ease-in-out-back' => $this->t('Ease In Out Back'),
          'ease-in-sine' => $this->t('Ease In Sine'),
          'ease-out-sine' => $this->t('Ease Out Sine'),
          'ease-in-out-sine' => $this->t('Ease In Out Sine'),
          'ease-in-quad' => $this->t('Ease In Quad'),
          'ease-out-quad' => $this->t('Ease Out Quad'),
          'ease-in-out-quad' => $this->t('Ease In Out Quad'),
          'ease-in-cubic' => $this->t('Ease In Cubic'),
          'ease-out-cubic' => $this->t('Ease Out Cubic'),
          'ease-in-out-cubic' => $this->t('Ease In Out Cubic'),
          'ease-in-quart' => $this->t('Ease in Quart'),
          'ease-out-quart' => $this->t('Ease Out Quart'),
          'ease-in-out-quart' => $this->t('Ease In Out Quart'),
          'linear' => $this->t('Linear'),
        ],
        '#default_value' => $storage['scroll_effects']['easing_functions'] ?? NULL,
        '#validated' => TRUE,
        '#prefix' => '<div class="uswds_col uswds_col--100 uswds_full-label">',
        '#suffix' => '</div>',
      ];

      // Offset.
      $form['advanced']['options']['offset'] = [
        '#type' => 'number',
        '#title' => $this->t('Offset (px)') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Offset (in px) from the original trigger point.') . '</div>',
        '#default_value' => $storage['scroll_effects']['offset'] ?? NULL,
        '#validated' => TRUE,
        '#placeholder' => 0,
        '#prefix' => '<div class="uswds_col uswds_col--100 uswds_full-label">',
        '#suffix' => '</div>',
      ];

      // Duration.
      $form['advanced']['options']['duration'] = [
        '#type' => 'number',
        '#title' => $this->t('Duration') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Values from 0 to 3000, with step 50ms. Default: 400') . '</div>',
        '#default_value' => $storage['scroll_effects']['duration'] ?? NULL,
        '#validated' => TRUE,
        '#placeholder' => 400,
        '#min' => 0,
        '#max' => 3000,
        '#step' => 50,
        '#prefix' => '<div class="uswds_col uswds_col--50">',
        '#suffix' => '</div>',
      ];

      // Delay.
      $form['advanced']['options']['delay'] = [
        '#type' => 'number',
        '#title' => $this->t('Delay') . ' <span class="uswds-icon uswds-icon--more-info"></span><div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Values from 0 to 3000, with step 50ms. Default: 0') . '</div>',
        '#default_value' => $storage['scroll_effects']['delay'] ?? NULL,
        '#validated' => TRUE,
        '#placeholder' => 0,
        '#min' => 0,
        '#max' => 3000,
        '#step' => 50,
        '#prefix' => '<div class="uswds_col uswds_col--50">',
        '#suffix' => '</div>',
      ];

      // AOS Mirror Option.
      $form['advanced']['options']['mirror'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Mirror') . '<div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Whether elements should animate out while scrolling past them.') . '</div>',
        '#default_value' => $storage['scroll_effects']['mirror'] ?? NULL,
        '#validated' => TRUE,
        '#return_value' => 'true',
        '#prefix' => '<div class="uswds_col uswds_col--50">',
        '#suffix' => '</div>',
      ];

      // AOS Once.
      $form['advanced']['options']['once'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('Once') . '<div class="uswds_tooltip uswds_tooltip-lg" data-placement="top" role="tooltip">' . $this->t('Whether animation should happen only once - while scrolling down.') . '</div>',
        '#default_value' => $storage['scroll_effects']['once'] ?? NULL,
        '#validated' => TRUE,
        '#return_value' => 'true',
        '#prefix' => '<div class="uswds_col uswds_col--50">',
        '#suffix' => '</div>',
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'scroll_effects' => [
        'class' => $group_elements['scroll_effects'],
        'anchor_placement' => $group_elements['advanced']['options']['anchor_placement'],
        'anchor_element' => $group_elements['advanced']['options']['anchor_element'],
        'easing_functions' => $group_elements['advanced']['options']['easing_functions'],
        'offset' => $group_elements['advanced']['options']['offset'],
        'duration' => $group_elements['advanced']['options']['duration'],
        'delay' => $group_elements['advanced']['options']['delay'],
        'mirror' => $group_elements['advanced']['options']['mirror'],
        'once' => $group_elements['advanced']['options']['once'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {

    $library_type = $this->config()->get('scroll_effects_library_type');
    $attribute_type = $this->config()->get('scroll_effects_attr_type');
    $data_key = $this->config()->get('scroll_effects_data_key');
    $advanced_settings = $this->config()->get('scroll_effects_advanced_settings');

    // Assign the style to element or its theme wrapper if existed.
    if ($theme_wrapper && isset($build['#theme_wrappers'][$theme_wrapper])) {
      if (isset($attribute_type)) {
        // Output some sort of data attribute.
        $build['#theme_wrappers'][$theme_wrapper]['#attributes'][$data_key][] = $storage['scroll_effects']['class'];
      }
      else {
        // Output classes.
        $build['#theme_wrappers'][$theme_wrapper]['#attributes']['class'][] = $storage['scroll_effects']['class'];
      }

      // Advanced animation settings.
      if (isset($advanced_settings)) {

        // Anchor placement/element.
        if (isset($storage['scroll_effects']['anchor_placement'])) {
          if ($storage['scroll_effects']['anchor_placement'] !== 'custom') {
            $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-anchor-placement'][] = $storage['scroll_effects']['anchor_placement'];
          }
          else {
            if (isset($storage['scroll_effects']['anchor_element'])) {
              $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-anchor'][] = strip_tags(str_replace(' ', '', $storage['scroll_effects']['anchor_element']));
            }
          }
        }

        // Easing.
        if (isset($storage['scroll_effects']['easing_functions'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-easing'][] = $storage['scroll_effects']['easing_functions'];
        }

        // Offset.
        if (isset($storage['scroll_effects']['offset'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-offset'][] = $storage['scroll_effects']['offset'];
        }

        // Duration.
        if (isset($storage['scroll_effects']['duration'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-duration'][] = $storage['scroll_effects']['duration'];
        }

        // Delay.
        if (isset($storage['scroll_effects']['delay'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-delay'][] = $storage['scroll_effects']['delay'];
        }

        // Mirror.
        if (isset($storage['scroll_effects']['mirror'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-mirror'][] = $storage['scroll_effects']['mirror'];
        }

        // Once.
        if (isset($storage['scroll_effects']['once'])) {
          $build['#theme_wrappers'][$theme_wrapper]['#attributes']['data-aos-once'][] = $storage['scroll_effects']['once'];
        }
      }
    }
    else {
      if (isset($attribute_type)) {
        // Output some sort of data attribute.
        $build['#attributes'][$data_key][] = $storage['scroll_effects']['class'];
      }
      else {
        // Output classes.
        $build['#attributes']['class'][] = $storage['scroll_effects']['class'];
      }

      // Advanced animation settings.
      if (isset($advanced_settings)) {

        // Anchor placement/element.
        if (isset($storage['scroll_effects']['anchor_placement'])) {
          if ($storage['scroll_effects']['anchor_placement'] !== 'custom') {
            $build['#attributes']['data-aos-anchor-placement'][] = $storage['scroll_effects']['anchor_placement'];
          }
          else {
            if (isset($storage['scroll_effects']['anchor_element'])) {
              $build['#attributes']['data-aos-anchor'][] = strip_tags(str_replace(' ', '', $storage['scroll_effects']['anchor_element']));
            }
          }
        }

        // Easing.
        if (isset($storage['scroll_effects']['easing_functions'])) {
          $build['#attributes']['data-aos-easing'][] = $storage['scroll_effects']['easing_functions'];
        }

        // Offset.
        if (isset($storage['scroll_effects']['offset'])) {
          $build['#attributes']['data-aos-offset'][] = $storage['scroll_effects']['offset'];
        }

        // Duration.
        if (isset($storage['scroll_effects']['duration'])) {
          $build['#attributes']['data-aos-duration'][] = $storage['scroll_effects']['duration'];
        }

        // Delay.
        if (isset($storage['scroll_effects']['delay'])) {
          $build['#attributes']['data-aos-delay'][] = $storage['scroll_effects']['delay'];
        }

        // Mirror.
        if (isset($storage['scroll_effects']['mirror'])) {
          $build['#attributes']['data-aos-mirror'][] = $storage['scroll_effects']['mirror'];
        }

        // Once.
        if (isset($storage['scroll_effects']['once'])) {
          $build['#attributes']['data-aos-once'][] = $storage['scroll_effects']['once'];
        }
      }
    }

    if (isset($library_type) && $library_type === 'external') {
      $build['#attached']['library'][] = 'uswds_blb_configuration/plugin.scroll_effects.build';
    }

    return $build;
  }

}
