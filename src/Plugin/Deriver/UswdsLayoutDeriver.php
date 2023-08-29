<?php

namespace Drupal\uswds_blb_configuration\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Layout\LayoutDefinition;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\uswds_blb_configuration\Plugin\Layout\UswdsLayout;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Makes a uswds layout for each layout config entity.
 */
class UswdsLayoutDeriver extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Constructs a new UswdsLayoutDeriver object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $layouts = $this->entityTypeManager->getStorage('uswds_layout')->getQuery()->sort('number_of_columns', 'ASC')->execute();
    if ($layouts) {
      foreach ($layouts as $layout_id) {
        $layout = $this->entityTypeManager->getStorage('uswds_layout')->load($layout_id);
        $this->derivatives[$layout->id()] = new LayoutDefinition([
          'class' => UswdsLayout::class,
          'label' => $layout->label(),
          'id' => $layout->id(),
          'category' => 'USWDS',
          'regions' => $this->getRegions($layout->getNumberOfColumns()),
          'theme_hook' => 'uswds_section',
          'icon_map' => $this->getIconMap($layout->getNumberOfColumns()),
          'provider' => 'uswds_blb_configuration',
        ]);
      }
    }

    return $this->derivatives;
  }

  /**
   * Convert intger to number in letters.
   *
   * @param int $num
   *   The number that needed to be converted.
   *
   * @return string
   *   The number in letters.
   */
  private function formatNumberInLetters(int $num) {
    $numbers = [
      1 => "one",
      2 => "two",
      3 => "three",
      4 => "four",
      5 => "five",
      6 => "six",
      7 => "seven",
      8 => "eight",
      9 => "nine",
      10 => "ten",
      11 => "eleven",
      12 => "twelve",
    ];
    return $numbers[$num];
  }

  /**
   * Get the formated array of row regions based on columns count.
   *
   * @param int $columns_count
   *   The count of row columns.
   *
   * @return array
   *   The row columns 'regions'.
   */
  private function getRegions(int $columns_count) {
    $regions = [];

    for ($i = 1; $i <= $columns_count; $i++) {
      $key = 'uswds_region_col_' . $i;
      $regions[$key] = [
        'label' => $this->t('Col') . ' ' . $i,
      ];
    }

    return $regions;
  }

  /**
   * Get the icon map array based on columns_count.
   *
   * @param int $columns_count
   *   The count of row columns.
   *
   * @return array
   *   The icon map array.
   */
  private function getIconMap(int $columns_count) {
    $row = [];

    for ($i = 1; $i <= $columns_count; $i++) {
      $row[] = 'square_' . $this->formatNumberInLetters($i);
    }

    $icon_map = [$row];
    return $icon_map;
  }

}
