<?php

namespace Drupal\uswds_blb_configuration\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent;
use Drupal\layout_builder\LayoutBuilderEvents;
use Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class subscriber for uswds blocks.
 */
class BlockComponentRenderArraySubscriber implements EventSubscriberInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected ConfigFactoryInterface $configFactory;


  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager
   */
  protected StylesGroupManager $stylesGroupManager;

  /**
   * BlockComponentRenderArraySubscriber constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Access configuration.
   * @param \Drupal\uswds_blb_configuration\StylesGroup\StylesGroupManager $styles_group_manager
   *   The styles group plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, ConfigFactoryInterface $config_factory, StylesGroupManager $styles_group_manager) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $config_factory;
    $this->stylesGroupManager = $styles_group_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[LayoutBuilderEvents::SECTION_COMPONENT_BUILD_RENDER_ARRAY] = [
      'onBuildRender',
      50,
    ];
    return $events;
  }

  /**
   * Add each component's block styles to the render array.
   *
   * @param \Drupal\layout_builder\Event\SectionComponentBuildRenderArrayEvent $event
   *   The section component render event.
   */
  public function onBuildRender(SectionComponentBuildRenderArrayEvent $event) {
    $build = $event->getBuild();

    $block_style_configs = [];
    if ($uswds_styles = $event->getComponent()->get('uswds_styles')) {
      if (isset($uswds_styles['block_style'])) {
        $block_style_configs = $uswds_styles['block_style'];
      }
    }

    // Build dynamic styles.
    if (count($block_style_configs) > 0) {
      $build = $this->stylesGroupManager->buildStyles(
        $build,
        // Storage.
        $block_style_configs,
      );
    }

    $event->setBuild($build);
  }

}
