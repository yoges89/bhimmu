<?php

/**
 * @file
 * Contains BhimmuController Class.
 */

namespace Drupal\bhimmu\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

/**
 * Class BhimmuController.
 */
class BhimmuController extends ControllerBase {

  /**
   * Display json representation of the node.
   *
   * @return string
   *   Return node as json string.
   */
  public function index($api_key, $node_id) {
    $flag = FALSE;
    $node = [];

    // Parse parameter to integer.
    $node_id = (int) $node_id;

    // Read default Site API Key value.
    $config = \Drupal::config('bhimmu.siteapi');

    // If given Site API Key is valid and not default value.
    if (!empty($api_key) && $api_key != 'No API Key yet' && $api_key == $config->get('siteapikey')) {
      $flag = TRUE;
    }

    // Load node from given node id.
    if (!empty($node_id) && $flag) {
      $serializer = \Drupal::service('serializer');
      $node = Node::load($node_id);

      // Check if node type is page.
      if ($node != NULL && $node->bundle() == 'page') {
        $node = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
      }
      else {
        throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
      }
    }
    else {
      throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
    }

    return [
      '#type' => 'markup',
      '#markup' => $node,
    ];
  }
}
