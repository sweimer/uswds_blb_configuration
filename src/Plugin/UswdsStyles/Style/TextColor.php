<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\Style;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\Style\StylePluginBase;

/**
 * TextColor style plugin.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\Style
 *
 * @Style(
 *   id = "text_color",
 *   title = @Translation("Text Color"),
 *   group_id = "typography",
 *   weight = 1
 * )
 */
class TextColor extends StylePluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $config = $this->config();

    $form['typography']['text_colors'] = [
      '#type' => 'textarea',
      '#default_value' => $config->get('text_colors'),
      '#title' => $this->t('Text colors (classes)'),
      '#description' => $this->t('<p>Enter one value per line, in the format <b>key|label</b> where <em>key</em> is the CSS class name (without the .), and <em>label</em> is the human readable name of the text.</p>'),
      '#cols' => 60,
      '#rows' => 5,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->config()
      ->set('text_colors', $form_state->getValue('text_colors'))
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {

    $form['text_color'] = [
      '#type' => 'radios',
      '#options' => $this->getStyleOptions('text_colors'),
      '#title' => $this->t('Text Color'),
      '#default_value' => $storage['text_color']['class'] ?? NULL,
      '#validated' => TRUE,
      '#attributes' => [
        'class' => [
          'field-text-color',
          'uswds_input-circles',
          'with-selected-gradient',
        ],
      ],
    ];

    // Attach the Layout Builder form style for this plugin.
    $form['#attached']['library'][] = 'uswds_blb_configuration/plugin.text_color.layout_builder_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'text_color' => [
        'class' => $group_elements['text_color'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build(array $build, array $storage, $theme_wrapper = NULL) {
    $classes = [];
    if (isset($storage['text_color']['class'])) {
      $classes[] = $storage['text_color']['class'];
    }

    // Add the classes to the build.
    return $this->addClassesToBuild($build, $classes, $theme_wrapper);
  }

}
