<?php

namespace Drupal\simple_access_log\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SimpleAccessLog.
 *
 * @package Drupal\simple_access_log\Controller
 *
 * Basic log value gathering and cleanup functions.
 */
class SimpleAccessLog extends ControllerBase implements ContainerInjectionInterface {

  /**
   * logValues function.
   *
   * @return array|bool
   *
   * Returns an array of the current values to be logged or false if the current
   * config prevents logging this request.
   */
  public function logValues() {
    // Load module settings.
    $settings = \Drupal::config('simple_access_log.settings');
    $access_log = [];
    //request Time
    $access_log['timestamp'] = \Drupal::time()->getRequestTime();

    // User or Anon?
    $access_log['uid'] = \Drupal::currentUser()->id();

    // skip logging user 0 if enabled
    if ($settings->get('do_not_log_0') && $access_log['uid'] == 0) {
      return false;
    }

    // Skip logging user 1 if enabled.
    if ($settings->get('do_not_log_1') && $access_log['uid'] == 1) {
      return false;
    }

    // Skip logging all users with administrator role if enabled.
    if ($settings->get('do_not_log_admin')) {
      $roles = \Drupal::currentUser()->getRoles();
      foreach ($roles as $rid => $role) {
        if ($role == 'administrator') {
          return false;
        }
      }
    }

    // Get the current path.
    $access_log['path'] = $current_path = \Drupal::service('path.current')->getPath();

    //Exclude Admin Paths
    if ($settings->get('not_admin_paths') && substr($access_log['path'], 0, 7) == '/admin/') {
      return false;
    }

    // Get the request itself, this should supply most of the rest of the needed values.
    $request = \Drupal::request();


    // Get the Client IP.
    $access_log['remote_host'] = $request->getClientIp();

    // Get hostname or host IP.
    // @todo see if these values can be pulled from the request variable?
    $access_log['host'] = (strlen($_SERVER['SERVER_NAME']) > 1 ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR']);

    // Get Page Title from the current route.
    $access_log['title'] = '';
    if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
      $access_log['title'] .= \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    // Ge the URI requested.
    $access_log['uri'] = $request->getRequestUri();


    // Get the Referer variable.
    // @todo see if these values can be pulled from the request variable?
    $access_log['referer'] = $_SERVER['HTTP_REFERER'];

    // Get the clietn's User Agent.
    // @todo see if these values can be pulled from the request variable?
    $access_log['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    // Return our loaded access log array.
    return $access_log;
  }

  /**
   * @return int number of records deleted
   *
   * Clean up old records from the database.
   * @todo this should probably be partially moved the the DatabaseStorage class.
   */
  public function cleanup() {
    // Get the module's settings.
    $settings = \Drupal::config('simple_access_log.settings');
    // Set the oldest timestamp to be the time now minus the difference setting.
    $oldest = time() - $settings->get('delete_log_after');
    // Get a database connection.
    $db = \Drupal::database();
    // Delete values older than the oldest timestamp.
    $num_deleted = $db->delete('simple_access_log')
      ->condition('timestamp', $oldest, '<')
      ->execute();
    // Return the number of records deleted. I don't think we're
    // actually doing anything with this value.
    return $num_deleted;
  }

}