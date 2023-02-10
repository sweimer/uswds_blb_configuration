<?php

namespace Drupal\uswds_blb_configuration\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a container wrapper element.
 *
 * @RenderElement("uswds_container")
 */
class Container extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'uswds_container',
      '#attributes' => [],
      '#children' => [],
    ];
  }

}
