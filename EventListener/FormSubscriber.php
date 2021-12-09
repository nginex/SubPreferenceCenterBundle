<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Mautic\FormBundle\Event\FormBuilderEvent;
use Mautic\FormBundle\FormEvents;
use Mautic\LeadBundle\Form\Type\ListType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FormSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      FormEvents::FORM_ON_BUILD => ['onFormBuild', 0],
    ];
  }

  public function onFormBuild(FormBuilderEvent $event) {
    $action = [
      'label' => 'mautic.subpreference_center.form.list',
      'formType' => ListType::class,
      'template' => 'SubPreferenceCenterBundle:SubscribedEvents\FormField:sub_preference_center_list.html.php',
    ];

    $event->addFormField('plugin.subPreferenceCenter', $action);
  }

}
