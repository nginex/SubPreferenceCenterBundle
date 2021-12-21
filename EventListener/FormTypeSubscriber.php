<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomFormEvent;
use Mautic\CoreBundle\Form\DataTransformer\IdToEntityModelTransformer;
use Mautic\LeadBundle\Entity\LeadList;
use MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Form\Type\SubPreferenceCenterListType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormTypeSubscriber implements EventSubscriberInterface {

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $em;

  /**
   * FormTypeSubscriber constructor.
   */
  public function __construct(EntityManager $em) {
    $this->em = $em;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      CoreEvents::ON_FORM_TYPE_BUILD => ['onFormTypeBuild', 0],
    ];
  }

  /**
   * Alter \Mautic\LeadBundle\Form\Type\ListType form to add a custom field.
   *
   * @param \Mautic\CoreBundle\Event\CustomFormEvent $event
   *   The custom form event.
   */
  public function onFormTypeBuild(CustomFormEvent $event) {
    if ($event->getFormType() === 'leadlist') {
      $transformer = new IdToEntityModelTransformer($this->em, 'SubPreferenceCenterBundle:SubPreferenceCenter', 'id');
      $builder = $event->getFormBuilder();
      $entity = $builder->getData();

      $center = NULL;
      if ($entity instanceof LeadList) {
        /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenterRepository $list_center_repository */
        $list_center_repository = $this->em->getRepository(ListSubPreferenceCenter::class);
        $center = $list_center_repository->getSubPreferenceCenterByList($entity);
      }

      $builder->add(
        $builder
          ->create('subPreferenceCenter', SubPreferenceCenterListType::class, [
            'mapped' => FALSE,
            'data' => $center,
            'row_attr' => [
              'class' => 'test',
            ],
          ])
          ->addModelTransformer($transformer)
      );
    }
  }

}
