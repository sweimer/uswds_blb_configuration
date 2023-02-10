<?php

namespace Drupal\uswds_blb_configuration;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Defines a class to build uswds_blb_configuration layout entities.
 *
 * @see \Drupal\uswds_blb_configuration\Entity\LayoutListBuilder
 */
class LayoutListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Machine name');
    $header['number_of_columns'] = $this->t('Number of columns');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function getDefaultOperations(EntityInterface $entity) {
    $operations = parent::getDefaultOperations($entity);

    $operations['list'] = [
      'title' => $this->t('List options'),
      'url' => $entity->toUrl('options-form'),
    ];
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['number_of_columns'] = $entity->getNumberOfColumns();
    return $row + parent::buildRow($entity);
  }

}
