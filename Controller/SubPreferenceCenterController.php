<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Controller;

use Mautic\CoreBundle\Controller\AbstractStandardFormController;

class SubPreferenceCenterController extends AbstractStandardFormController {

  /**
   * {@inheritdoc}
   */
  protected function getModelName() {
    return 'subPreferenceCenter.subPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  protected function getJsLoadMethodPrefix() {
    return 'subPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  protected function getRouteBase() {
    return 'mautic_subpreference_center';
  }

  /**
   * {@inheritdoc}
   */
  protected function getSessionBase($objectId = NULL) {
    return 'mautic_subpreference_center';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTemplateBase() {
    return 'SubPreferenceCenterBundle:SubPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  protected function getControllerBase() {
    return 'SubPreferenceCenterBundle:SubPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  protected function getTranslationBase() {
    return 'mautic.subPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  protected function getPermissionBase() {
    return 'subPreferenceCenter:subPreferenceCenter';
  }

  /**
   * Builds list of entities.
   *
   * @param int $page
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function indexAction($page = 1) {
    return parent::indexStandard($page);
  }

  /**
   * Generates new form and processes post data.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function newAction() {
    return parent::newStandard();
  }

  /**
   * Generates edit form and processes post data.
   *
   * @param int $objectId
   * @param bool $ignorePost
   *
   * @return \Symfony\Component\HttpFoundation\Response
   */
  public function editAction($objectId, $ignorePost = FALSE) {
    return parent::editStandard($objectId, $ignorePost);
  }

  /**
   * Loads a specific form into the detailed panel.
   *
   * @param $objectId
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\Response
   */
  public function viewAction($objectId) {
    return parent::indexStandard(1);
  }

  /**
   * Deletes the entity.
   *
   * @param int $objectId
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function deleteAction($objectId) {
    return parent::deleteStandard($objectId);
  }

  /**
   * Deletes a group of entities.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
   */
  public function batchDeleteAction() {
    return parent::batchDeleteStandard();
  }

}
