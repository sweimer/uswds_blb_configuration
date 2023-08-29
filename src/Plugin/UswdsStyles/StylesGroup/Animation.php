<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;

/**
 * Animation styles group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "animation",
 *   title = @Translation("Animation"),
 *   weight = 6,
 *   icon = "uswds_blb_configuration/images/plugins/animation-icon.svg"
 * )
 */
class Animation extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['animation'] = [
      '#type' => 'details',
      '#title' => $this->t('Animation'),
      '#open' => FALSE,
    ];

    return $form;
  }

}
