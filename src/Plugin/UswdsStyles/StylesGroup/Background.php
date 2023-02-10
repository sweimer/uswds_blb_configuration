<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Background styles group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "background",
 *   title = @Translation("Background"),
 *   weight = 1,
 *   icon = "uswds_blb_configuration/images/plugins/background-icon.svg"
 * )
 */
class Background extends StylesGroupPluginBase {
  // phpcs:disable
  // @todo uncomment when responsive is fixed.
  // use ResponsiveTrait;
  // phpcs:enable

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['background'] = [
      '#type' => 'details',
      '#title' => $this->t('Background'),
      '#open' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    // phpcs:disable
    // @todo uncomment when responsive is fixed.
    // Responsive.
    // $this->buildBreakpointsFields($form, 'background');
    // phpcs:enable

    $form['background_type'] = [
      '#type' => 'radios',
      '#options' => [],
      '#title' => $this->t('Background type'),
      '#title_display' => 'invisible',
      '#default_value' => NULL,
      '#validated' => TRUE,
      '#attributes' => [
        'class' => ['bs_col--full', 'uswds_background--type'],
      ],
      '#disable_live_preview' => TRUE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitStyleFormElements(array $group_elements) {
    return [
      'background' => [
        'background_type' => $group_elements['background_type'],
      ],
    ];
  }

}
