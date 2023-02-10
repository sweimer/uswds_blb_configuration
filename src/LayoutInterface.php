<?php

namespace Drupal\uswds_blb_configuration;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines an interface for uswds_layout entity storage classes.
 */
interface LayoutInterface extends ConfigEntityInterface {

  /**
   * Returns all the options from a layout options sorted correctly.
   *
   * @return \Drupal\uswds_blb_configuration\LayoutOptionInterface[]
   *   An array of layout options entities.
   */
  public function getLayoutOptions();

  /**
   * Returns the number of columns at the layout.
   */
  public function getNumberOfColumns();

}
