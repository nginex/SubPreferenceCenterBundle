<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Event\LeadListEvent;
use Mautic\LeadBundle\LeadEvents;
use MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LeadListSubscriber implements EventSubscriberInterface {

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $em;

  /**
   * LeadListSubscriber constructor.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack service.
   * @param \Doctrine\ORM\EntityManagerInterface $em
   *   The entity manager.
   */
  public function __construct(RequestStack $request_stack, EntityManagerInterface $em) {
    $this->requestStack = $request_stack;
    $this->em = $em;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      LeadEvents::LIST_PRE_SAVE => ['onListPreSave', 0],
    ];
  }

  /**
   * Save relation between segment and sub preference center.
   *
   * @param \Mautic\LeadBundle\Event\LeadListEvent $event
   *   The lead list event.
   */
  public function onListPreSave(LeadListEvent $event) {
    // @TODO find more elegant way to get value from the form.
    $request = $this->requestStack->getCurrentRequest();
    if ($values = $request->get('leadlist')) {
      /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenterRepository $center_repository */
      $center_repository = $this->em->getRepository(SubPreferenceCenter::class);
      /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenterRepository $list_center_repository */
      $list_center_repository = $this->em->getRepository(ListSubPreferenceCenter::class);

      $sub_center_id = $values['subPreferenceCenter'];

      // Remove relation.
      if (!$sub_center_id) {
        $list_center_repository->deleteSubPreferenceCenterFromList($event->getList());
      }
      else {
        /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $center */
        $center = $center_repository->getEntity($sub_center_id);
        $list_center_repository->setSubPreferenceCenterForList($event->getList(), $center);
      }
    }
  }

}
