<?php

namespace Drupal\status_user\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\UserInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\status_user\TabPageFonction;

/**
 * Class PageStatusController.
 */
class PageStatusController extends ControllerBase {

  use TabPageFonction;

  /**
   * showStatusPage().
   *
   * @return string
   */
  public function showStatusPage() {

    return [
      '#type' => 'markup',
      '#markup' => $this->t('Status Page'),
    ];
  }

  /**
   * getCurrentPagePermission.
   * 
   * Check If the user has the waited permission
   *
   * @return int
   */
  public function getCurrentPagePermission() {

    $user = \Drupal::entityTypeManager()->getStorage('user')->load(TabPageFonction::getOwnerCurrentPage());

    $permission = 0;

    if (
      $user->hasField('field_su_active') 
      && $user->get('field_su_active')->isEmpty() != true
      && $user->get('field_su_active')->getValue()[0]
    ) {
      $permission = $user->get('field_su_active')->getValue()[0]['value'];
    }
    
    return (int)$permission;
  }

  /**
   *
   * @param AccountInterface $account
   *
   * @return AccessResultInterface
   */
  public function access(AccountInterface $account) {

    $access = TabPageFonction::getCurrentAccessPermission($this->getCurrentPagePermission());

     switch ($access) {
      case 1:
        return AccessResult::forbidden();
        break;
      case 2:
        return AccessResult::allowed();
        break;
      case 3:
        return AccessResult::forbidden();
        break;
      case 4:
        return AccessResult::allowed();
        break;
      case 5:
        return AccessResult::allowed();
        break;
    }
  }

}
