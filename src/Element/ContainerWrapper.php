<?php

namespace Drupal\uswds_blb_configuration\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a container wapper render element.
 *
 * @RenderElement("uswds_container_wrapper")
 */
class ContainerWrapper extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'uswds_container_wrapper',
      '#attributes' => [],
      '#children' => [],
    ];
  }

}
