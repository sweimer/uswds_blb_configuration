<?php

namespace Drupal\uswds_blb_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\uswds_blb_configuration\BreakpointInterface;

/**
 * Defines the Breakpoint config entity.
 *
 * @ConfigEntityType(
 *   id = "uswds_breakpoint",
 *   label = @Translation("USWDS Layout Builder Breakpoint"),
 *   label_collection = @Translation("USWDS Layout Builder Breakpoints"),
 *   label_plural = @Translation("USWDS Layout Builder Breakpoint"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     },
 *     "list_builder" = "Drupal\uswds_blb_configuration\BreakpointListBuilder",
 *     "form" = {
 *       "add" = "Drupal\uswds_blb_configuration\Form\BreakpointForm",
 *       "edit" = "Drupal\uswds_blb_configuration\Form\BreakpointForm",
 *       "delete" = "Drupal\uswds_blb_configuration\Form\BreakpointDeleteForm"
 *     }
 *   },
 *   config_prefix = "breakpoint",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "weight" = "weight",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "base_class" = "base_class",
 *     "status" = "status",
 *     "weight" = "weight",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "edit-form" = "/admin/config/uswds-layout-builder/breakpoints/{uswds_breakpoint}/edit",
 *     "delete-form" = "/admin/config/uswds-layout-builder/breakpoints/{uswds_breakpoint}/delete",
 *     "collection" = "/admin/config/uswds-layout-builder/breakpoints",
 *     "add-form" = "/admin/config/uswds-layout-builder/breakpoints/add"
 *   }
 * )
 */
class Breakpoint extends ConfigEntityBase implements BreakpointInterface {

  /**
   * The USWDS layout Builder breakpoint ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The USWDS layout Builder breakpoint label.
   *
   * @var string
   */
  protected $label;

  /**
   * The breakpoint base class.
   *
   * @var string
   */
  protected $base_class;

  /**
   * The breakpoint status.
   *
   * @var bool
   */
  protected $status;

  /**
   * Order of breakpoints on the config page & Layout Builder add/update forms.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * {@inheritdoc}
   */
  public function getBaseClass() {
    return $this->base_class;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayoutOptions($layout_id) {
    $options = [];
    $query = $this->entityTypeManager()->getStorage('uswds_layout_option')->getQuery();
    $uswds_blb_options = $query->condition('layout_id', $layout_id)->sort('weight', 'ASC')->execute();
    foreach ($uswds_blb_options as $option_id) {
      $option = $this->entityTypeManager()->getStorage('uswds_layout_option')->load($option_id);
      if (!in_array($this->id(), $option->getBreakpointsIds())) {
        continue;
      }
      $options[$option->getStructureId()] = $option->label();
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function getClassByPosition($key, $strucutre_id) {
    $strucutre = substr($strucutre_id, strlen('blb_col_'));
    $strucutre = explode('_', $strucutre);
    // Full width case.
    $sufix = '12';
    if (count($strucutre) > 1) {
      $sufix = (isset($strucutre[$key])) ? $strucutre[$key] : $strucutre[0];
    }
    return $this->getBaseClass() . '-' . $sufix;
  }

}
