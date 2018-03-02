<?php

namespace Drupal\simple_access_log\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SimpleAccessLogSubscriber implements EventSubscriberInterface {

  /**
   * Execute some code.
   */
  public function executeLogFunction() {
    simple_access_log__log_request();
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('executeLogFunction');
    return $events;
  }
}
