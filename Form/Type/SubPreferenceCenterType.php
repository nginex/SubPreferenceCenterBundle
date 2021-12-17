<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Form\Type;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\PageBundle\Form\Type\PageListType;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SubPreferenceCenterType extends AbstractType {

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $em;

  /**
   * SubPreferenceCenterType constructor.
   */
  public function __construct(EntityManager $em) {
    $this->em = $em;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(FormBuilderInterface $builder, array $options) {
    $builder->add('buttons', FormButtonsType::class);

    $builder->add(
      'name',
      TextType::class,
      [
        'label' => 'mautic.core.name',
        'required' => TRUE,
        'label_attr' => ['class' => 'control-label'],
        'attr' => ['class' => 'form-control'],
      ]
    );

    $builder->add(
      'token',
      TextType::class,
      [
        'label' => 'mautic.subpreference_center.field.token',
        'required' => TRUE,
        'label_attr' => ['class' => 'control-label'],
        'attr' => [
          'class' => 'form-control',
          'tooltip' => 'mautic.subpreference_center.field.token.tooltip',
        ],
      ]
    );

    $transformer = new IdToEntityModelTransformer($this->em, 'MauticPageBundle:Page', 'id');

    $builder->add(
      $builder->create(
        'page',
        PageListType::class,
        [
          'label' => 'mautic.subpreference_center.field.page',
          'label_attr' => ['class' => 'control-label'],
          'required' => TRUE,
          'attr' => [
            'class' => 'form-control',
            'tooltip' => 'mautic.subpreference_center.field.page.tooltip',
          ],
          'multiple' => FALSE,
          'placeholder' => '',
          'published_only' => TRUE,
        ]
      )->addModelTransformer($transformer)
    );

    if (!empty($options['action'])) {
      $builder->setAction($options['action']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function configureOptions(OptionsResolver $resolver) {
    $resolver->setDefaults([
      'data_class' => SubPreferenceCenter::class,
    ]);
  }

}
