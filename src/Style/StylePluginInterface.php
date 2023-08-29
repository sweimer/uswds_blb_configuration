<?php

namespace Drupal\uswds_blb_configuration\Style;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides an interface defining a Styles Group.
 */
interface StylePluginInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {

  /**
   * Return the name of the Styles Group form plugin.
   *
   * @return string
   *   The name of styles group.
   */
  public function getTitle();

}
