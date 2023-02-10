<?php

namespace Drupal\uswds_blb_configuration;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for USWDS Layout Builder breakpoints entities.
 */
interface BreakpointInterface extends ConfigEntityInterface {

  /**
   * Returns the base class fo the breakpoint.
   *
   * @return string
   *   The base class of the breakpoint.
   */
  public function getBaseClass();

  /**
   * Returns the status of the breakpoint.
   *
   * @return bool
   *   Either "enabled" or "disabled".
   */
  public function getStatus();

  /**
   * Returns region class of a breakpoint.
   *
   * @param string $key
   *   The position of region.
   * @param string $strucutre_id
   *   The layout option structure id.
   *
   * @return string
   *   The class of a region.
   */
  public function getClassByPosition($key, $strucutre_id);

}
