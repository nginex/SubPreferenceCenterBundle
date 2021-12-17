<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Form\Type;

use MauticPlugin\SubPreferenceCenterBundle\Model\SubPreferenceCenterModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubPreferenceCenterListType extends AbstractType {

  /**
   * @var \MauticPlugin\SubPreferenceCenterBundle\Model\SubPreferenceCenterModel
   */
  protected $model;

  /**
   * SubPreferenceCenterListType constructor.
   */
  public function __construct(SubPreferenceCenterModel $model) {
    $this->model = $model;
  }

  /**
   * {@inheritdoc}
   */
  public function getBlockPrefix() {
    return 'subpreference_center_list';
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    return ChoiceType::class;
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'choices' => $this->getSubPreferenceCenterOptions(),
      'label' => 'mautic.subpreference_center.form.list',
      'label_attr' => ['class' => 'control-label'],
      'multiple' => FALSE,
      'placeholder' => 'mautic.subpreference_center.form.list.placeholder',
      'required' => FALSE,
      'attr' => [
        'class' => 'form-control',
        'data-show-on' => '{"leadlist_isPreferenceCenter_1":"checked"}',
      ],
      'return_entity' => TRUE,
    ]);
  }

  /**
   * Get available options for the list element.
   *
   * @return array
   *   List of sub preference centers.
   */
  protected function getSubPreferenceCenterOptions() {
    /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter[] $entities */
    $entities = $this->model->getEntities();
    $options = [];
    foreach ($entities as $entity) {
      $options[$entity->getName()] = $entity->getId();
    }

    return $options;
  }

}
