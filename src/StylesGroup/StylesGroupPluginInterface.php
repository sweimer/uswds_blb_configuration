<?php

namespace Drupal\uswds_blb_configuration\StylesGroup;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Provides an interface defining a Styles Group.
 */
interface StylesGroupPluginInterface extends PluginInspectionInterface {

  /**
   * Return the title of the Styles Group form plugin.
   *
   * @return string
   *   The title of styles group.
   */
  public function getTitle();

}
