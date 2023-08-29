<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;

/**
 * Shadow styles group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "shadow",
 *   title = @Translation("Shadow"),
 *   weight = 5,
 *   icon = "uswds_blb_configuration/images/plugins/shadow-icon.svg"
 * )
 */
class Shadow extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['shadow'] = [
      '#type' => 'details',
      '#title' => $this->t('Shadow'),
      '#open' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $form['shadow_preview'] = [
      '#theme' => 'shadow_preview',
    ];

    return $form;
  }

}
