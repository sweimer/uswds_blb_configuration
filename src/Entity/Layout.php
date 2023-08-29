<?php

namespace Drupal\uswds_blb_configuration\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\uswds_blb_configuration\LayoutInterface;

/**
 * Defines the layout configuration entity.
 *
 * @ConfigEntityType(
 *   id = "uswds_layout",
 *   label = @Translation("USWDS Layout Builder Layout"),
 *   label_collection = @Translation("USWDS Layout Builder Layouts"),
 *   label_plural = @Translation("USWDS Layout Builder Layout"),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider"
 *     },
 *     "list_builder" = "Drupal\uswds_blb_configuration\LayoutListBuilder",
 *     "form" = {
 *       "options" = "Drupal\uswds_blb_configuration\Form\LayoutOptionsForm",
 *       "edit" = "Drupal\uswds_blb_configuration\Form\LayoutForm"
 *     }
 *   },
 *   config_prefix = "layout",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "number_of_columns" = "number_of_columns",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "id" = "id",
 *     "label" = "label",
 *     "number_of_columns" = "number_of_columns",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "options-form" = "/admin/config/uswds-layout-builder/layouts/{uswds_layout}/options",
 *     "edit-form" = "/admin/config/uswds-layout-builder/layouts/{uswds_layout}",
 *     "collection" = "/admin/config/uswds-layout-builder/layouts",
 *   }
 * )
 */
class Layout extends ConfigEntityBase implements LayoutInterface {

  /**
   * The machine name for the configuration entity.
   */
  protected string $id;

  /**
   * The human-readable name of the configuration entity.
   */
  protected string $label;

  /**
   * The number of layout columns.
   */
  protected string $number_of_columns;

  /**
   * {@inheritdoc}
   */
  public function getNumberOfColumns() {
    return $this->number_of_columns;
  }

  /**
   * {@inheritdoc}
   */
  public function setNumberOfColumns($number_of_columns) {
    $this->number_of_columns = $number_of_columns;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLayoutOptions() {
    $options = $this->entityTypeManager()->getStorage('uswds_layout_option')->loadByProperties(['layout_id' => $this->id()]);
    uasort($options, function ($a, $b) {
      $a_weight = $a->getWeight();
      $b_weight = $b->getWeight();
      if ($a_weight == $b_weight) {
        return strnatcasecmp($a->label(), $b->label());
      }
      return ($a_weight < $b_weight) ? -1 : 1;
    });
    return $options;
  }

}
