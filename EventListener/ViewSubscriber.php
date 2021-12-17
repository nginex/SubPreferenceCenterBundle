<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomTemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ViewSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      CoreEvents::VIEW_INJECT_CUSTOM_TEMPLATE => ['onTemplateRender', 0],
    ];
  }

  /**
   * Replace template for lead list form.
   */
  public function onTemplateRender(CustomTemplateEvent $event) {
    if ($event->getTemplate() === 'MauticLeadBundle:List:form.html.php') {
      $event->setTemplate('SubPreferenceCenterBundle:List:form.html.php');
    }
  }

}
