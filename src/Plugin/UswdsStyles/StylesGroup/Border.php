<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;

/**
 * Border styles group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "border",
 *   title = @Translation("Border"),
 *   weight = 4,
 *   icon = "uswds_blb_configuration/images/plugins/border-icon.svg"
 * )
 */
class Border extends StylesGroupPluginBase {

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['border'] = [
      '#type' => 'details',
      '#title' => $this->t('Border'),
      '#open' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $form['border_preview'] = [
      '#theme' => 'border_preview',
    ];

    return $form;
  }

}
