<?php

namespace Drupal\uswds_blb_configuration\Plugin\UswdsStyles\StylesGroup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uswds_blb_configuration\ResponsiveTrait;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupPluginBase;

/**
 * Spacing styles group.
 *
 * @package Drupal\uswds_blb_configuration\Plugin\StylesGroup
 *
 * @StylesGroup(
 *   id = "spacing",
 *   title = @Translation("Spacing"),
 *   weight = 3,
 *   icon = "uswds_blb_configuration/images/plugins/spacing-icon.svg"
 * )
 */
class Spacing extends StylesGroupPluginBase {

  use ResponsiveTrait;

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['spacing'] = [
      '#type' => 'details',
      '#title' => $this->t('Spacing'),
      '#open' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function buildStyleFormElements(array &$form, FormStateInterface $form_state, $storage) {
    $this->buildBreakpointsFields($form, 'spacing');

    $form['spacing_preview'] = [
      '#theme' => 'spacing_preview',
    ];

    return $form;
  }

}
