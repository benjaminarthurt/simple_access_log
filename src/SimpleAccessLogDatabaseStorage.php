<?php

namespace Drupal\simple_access_log;

/**
 * Provides the default database storage backend for statistics.
 */
class SimpleAccessLogDatabaseStorage implements SimpleAccessLogStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function logAccess($values) {
    $db = \Drupal::database();
    if ($db) {
      $db->insert('simple_access_log')->fields($values)->execute();
    }else {
      \Drupal::logger('simple_access_log')->error('Could not write to database.');
    }
  }

}
