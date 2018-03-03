<?php

namespace Drupal\simple_access_log\EventSubscriber;

use Drupal\simple_access_log\Controller\SimpleAccessLog;
use Drupal\simple_access_log\SimpleAccessLogDatabaseStorage;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SimpleAccessLogSubscriber implements EventSubscriberInterface {

  /**
   * Execute some code.
   */
  public function executeLogFunction() {
    $sal =  New SimpleAccessLog;
    $values = $sal->logValues();
    if(!empty($values)){
        $storage = New SimpleAccessLogDatabaseStorage();
        $storage->logAccess($values);
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('executeLogFunction');
    return $events;
  }
}
