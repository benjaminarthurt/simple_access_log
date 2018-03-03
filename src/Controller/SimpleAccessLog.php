<?php

namespace Drupal\simple_access_log\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

/**
 * Class SimpleAccessLog
 * @package Drupal\simple_access_log\Controller
 *
 * Basic log value gathering and cleanup functions
 */
class SimpleAccessLog extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @return array|bool
   *
   * returns an array of the current values to be logged or false if the current config prevents logging this request.
   */
  public function logValues() {
    //Load module settings
    $settings = \Drupal::config('simple_access_log.settings');
    $access_log = [];
    //request Time
    $access_log['timestamp'] = \Drupal::time()->getRequestTime();

    //User or Anon
    $access_log['uid'] = \Drupal::currentUser()->id();

    // skip logging user 0 if enabled
    if ($settings->get('do_not_log_0') && $access_log['uid'] == 0) {
      return false;
    }

    // skip logging user 1 if enabled
    if ($settings->get('do_not_log_1') && $access_log['uid'] == 1) {
      return false;
    }

    //Skip logging all users with administrator role if enabled.
    if ($settings->get('do_not_log_admin')) {
      $roles = \Drupal::currentUser()->getRoles();
      foreach ($roles as $rid => $role) {
        if ($role == 'administrator') {
          return false;
        }
      }
    }

    //Path
    $access_log['path'] = $current_path = \Drupal::service('path.current')->getPath();

    //Exclude Admin Paths
    if ($settings->get('not_admin_paths') && substr($access_log['path'], 0, 7) == '/admin/') {
      return false;
    }

    //Client IP
    //@todo There may be a better to get this that takes into account load balancers etc that may be in-front of a host.
    $access_log['remote_host'] = $_SERVER['REMOTE_ADDR'];

    //Get hostname or host IP
    $access_log['host'] = (strlen($_SERVER['SERVER_NAME']) > 1 ? $_SERVER['SERVER_NAME'] : $_SERVER['SERVER_ADDR']);

    //Page Title
    $request = \Drupal::request();
    $access_log['title'] = '';
    if ($route = $request->attributes->get(\Symfony\Cmf\Component\Routing\RouteObjectInterface::ROUTE_OBJECT)) {
      $access_log['title'] .= \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    //URI
    $access_log['uri'] = \Drupal::request()->getRequestUri();


    //Referer
    $access_log['referer'] = $_SERVER['HTTP_REFERER'];

    //User Agent
    $access_log['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    return $access_log;
  }

  /**
   * @return int number of records deleted
   *
   * clean up old records from the database
   * @todo this should probably be partially moved the the DatabaseStorage class.
   */
  public function cleanup() {
    $settings = \Drupal::config('simple_access_log.settings');
    $oldest = time() - $settings->get('delete_log_after');
    $db = \Drupal::database();
    $num_deleted = $db->delete('simple_access_log')
      ->condition('timestamp', $oldest, '<')
      ->execute();
    return $num_deleted;
  }

}