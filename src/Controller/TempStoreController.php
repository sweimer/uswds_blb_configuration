<?php

namespace Drupal\uswds_blb_configuration\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Set or get any value to uswds_styles temp store.
 */
class TempStoreController extends ControllerBase {

  /**
   * The tempstore service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $tempStore;

  /**
   * Constructs a TempStoreController object.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $temp_store_factory
   *   The tempstore service.
   */
  public function __construct(PrivateTempStoreFactory $temp_store_factory) {
    $this->tempStore = $temp_store_factory->get('uswds_blb_configuration');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('tempstore.private')
    );
  }

  /**
   * Set a tempStore value by key.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request of the page.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function set(Request $request) {
    $key = $request->request->get('key');
    $value = $request->request->get('value');
    $this->tempStore->set($key, $value);

    // Return an empty JSON response.
    return new JsonResponse();
  }

  /**
   * Get temp store value by key.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The JSON response.
   */
  public function get(Request $request) {
    $key = $request->request->get('key');
    $response = $this->tempStore->get($key);

    return new JsonResponse($response);
  }

}
