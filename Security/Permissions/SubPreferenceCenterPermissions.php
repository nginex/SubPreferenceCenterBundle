<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Security\Permissions;

use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

class SubPreferenceCenterPermissions extends AbstractPermissions {

  /**
   * {@inheritdoc}
   */
  public function __construct(CoreParametersHelper $coreParametersHelper) {
    parent::__construct($coreParametersHelper->all());
  }

  /**
   * {@inheritdoc}
   */
  public function definePermissions() {
    $this->addExtendedPermissions(['subPreferenceCenter']);
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'subPreferenceCenter';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface &$builder, array $options, array $data) {
    $this->addExtendedFormFields('subPreferenceCenter', 'subPreferenceCenter', $builder, $data);
  }

}
