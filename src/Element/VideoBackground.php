<?php

namespace Drupal\uswds_blb_configuration\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * Provides a video background render element.
 *
 * @RenderElement("uswds_video_background")
 */
class VideoBackground extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'uswds_video_background',
      '#attributes' => [],
      '#video_background_url' => '',
      '#children' => [],
    ];
  }

}
