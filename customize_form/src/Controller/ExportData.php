<?php

namespace Drupal\customize_form\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Path\CurrentPathStack;

/**
 * Provides a callback to export Node details as JSON Data.
 */
class ExportData extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  private $database;

  /**
   * Constructs a ExportData object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Drupal\Core\Path\CurrentPathStack $current_path
   *   The current path.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, Connection $database, CurrentPathStack $current_path) {
    $this->entityTypeManager = $entity_type_manager;
    $this->configFactory = $config_factory;
    $this->database = $database;
    $this->currentPath = $current_path;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory'),
      $container->get('database'),
      $container->get('path.current')
    );
  }

  /**
   * Retrieve a JSON object containing the relevant node details.
   *
   * @param string $api_key
   *   The Site API Key.
   * @param int $nid
   *   The Node id.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   The Node details in JsonResponse object.
   */
  public function getJsonData($api_key, $nid) {
    // Initialize the Json array with default value.
    $json_array['data'] = ['access_denied'];
    // Get the current path.
    $path = $this->currentPath->getPath();

    // Explode the path and get the arguments.
    $path_arg = explode('/', $path);
    $api_key = $path_arg[2];
    $id = $path_arg[3];

    // Get the saved Site API Key from config.
    $config = $this->config('system.site');
    $site_api_key = $config->get('siteapikey');

    // Check the value of argument 1 is present & it is numeric.
    // Check the value of argument 2 with config Site API key value.
    if (!empty($id) && is_numeric($id) && !empty($site_api_key) && $api_key == $site_api_key) {
      // Returns the nid if node exist and it is of type 'page'.
      $query = $this->database->select('node', 'n');
      $query->addField('n', 'nid', 'nid');
      $query->condition('n.nid', $id, '=');
      $query->condition('n.type', 'page', '=');
      $query->range(0, 1);
      $nid = $query->execute()->fetchField();

      if (!empty($nid)) {
        // Load the Node details.
        $node = $this->entityTypeManager->getStorage('node')->load($nid);

        // Replace the Json array with node details.
        $json_array['data'] = [
          'type' => $node->getType(),
          'id' => $node->id(),
          'title' => $node->getTitle(),
          'content' => $node->get('body')->value,
        ];
      }
    }

    return new JsonResponse($json_array);
  }

}
