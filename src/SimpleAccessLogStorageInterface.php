<?php

namespace Drupal\simple_access_log;

/**
 * Provides an interface defining Simple Access Log Storage.
 *
 * Stores basic details of each access request
 */
interface SimpleAccessLogStorageInterface {

  /**
   * Count a entity view.
   *
   * @param array $values
   *   An array of keyed values to be logged
   *
   * @return bool
   *   TRUE if the access has been logged.
   */
  public function logAccess($values);

}