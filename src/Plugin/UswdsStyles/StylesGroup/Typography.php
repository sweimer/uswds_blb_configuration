<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;

/**
 * Typography style group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "typography",
 *   title = @Translation("Typography"),
 *   weight = 2,
 *   icon = "uswds_blb_configuration/images/plugins/typography-icon.svg"
 * )
 */
class Typography extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['typography'] = [
      '#type' => 'details',
      '#title' => $this->t('Typography'),
      '#open' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    return $form;
  }

}
