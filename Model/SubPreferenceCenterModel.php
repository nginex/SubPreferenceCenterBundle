<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Model;

use Mautic\CoreBundle\Model\FormModel;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Form\Type\SubPreferenceCenterType;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class SubPreferenceCenterModel extends FormModel {

  /**
   * {@inheritdoc}
   */
  public function getRepository() {
    return $this->em->getRepository(SubPreferenceCenter::class);
  }

  /**
   * {@inheritdoc}
   */
  public function getPermissionBase() {
    return 'subPreferenceCenter:subPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  public function createForm($entity, $formFactory, $action = NULL, $options = []) {
    if (!$entity instanceof SubPreferenceCenter) {
      throw new MethodNotAllowedHttpException(['SubPreferenceCenter']);
    }

    if (!empty($action)) {
      $options['action'] = $action;
    }

    return $formFactory->create(SubPreferenceCenterType::class, $entity, $options);
  }

  /**
   * Get a specific entity or generate a new one if id is empty.
   *
   * @param int $id
   *
   * @return \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter
   */
  public function getEntity($id = NULL) {
    return is_null($id) ? (new SubPreferenceCenter()) : parent::getEntity($id);
  }

}
