<?php

namespace Drupal\status_user;


/**
 * Class TabPageFonction.
 */
trait TabPageFonction {

  /**
   * getCurrentFieldPermission.
   *
   * @return int
   */
  public static function getCurrentFieldPermission() {
    $route = \Drupal::routeMatch()->getRouteName();
    $route = explode(".", $route);
    $route = end($route);
    return (string)$route;
  }

  /**
   * getOwnerPage.
   *
   * @return int
   *   Return owner Id of the current page string.
   */
  public static function getOwnerCurrentPage() {
    // Set the variable
    $owner = 0;
    // resquest with route if is a page user
    if (\Drupal::routeMatch()->getParameters()->has('user')) {
      // Get the user id
      $owner = \Drupal::routeMatch()->getParameters()->get('user');
      if (is_object($owner)) {
        $owner = $owner->id();
      }
    }
    // return the user id
    return (int)$owner;
  }

  /**
   * getCurrentUserLogged.
   *
   * @return int
   *   Return current user ID int.
   */
  public static function getCurrentUserLogged() {
    $accountProxy = \Drupal::currentUser();
    return (int)$accountProxy->id();
  }

  /**
   * getCurrentAccessPermissiogetCurrentUserLogged()n.
   *
   * @return int
   *   Return current Access for the page int.
   */
  public static function getCurrentAccessPermission($permission) {
    if ($permission === 0) {
      return 1;
    }elseif ($permission === 1 && self::getCurrentUserLogged() === self::getOwnerCurrentPage()) {
      return 2;
    }elseif ($permission === 1 && self::getCurrentUserLogged() !== self::getOwnerCurrentPage()) {
      return 3;
    }elseif ($permission === 2) {
      return 4;
    }else{
      return 5;
    }
  }

}