<?php

namespace Drupal\customize_form\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class SiteInformationRouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    // Check if the route is of specific collection.
    if ($route = $collection->get('system.site_information_settings')) {
      // Invoke a form alter for Site Information Form.
      $route->setDefault('_form', 'Drupal\customize_form\Form\SiteInformationFormAlter');
    }
  }

}
