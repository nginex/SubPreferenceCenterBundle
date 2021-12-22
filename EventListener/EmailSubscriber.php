<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EmailSubscriber implements EventSubscriberInterface {

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $em;

  /**
   * @var \Mautic\CoreBundle\Helper\CoreParametersHelper
   */
  protected $coreParametersHelper;

  /**
   * @var \Symfony\Component\Routing\RouterInterface
   */
  protected $router;

  /**
   * @var \Symfony\Contracts\Translation\TranslatorInterface
   */
  protected $translator;

  /**
   * EmailSubscriber constructor.
   */
  public function __construct(EntityManagerInterface $em, CoreParametersHelper $core_parameters_helper, RouterInterface $router, TranslatorInterface $translator) {
    $this->em = $em;
    $this->coreParametersHelper = $core_parameters_helper;
    $this->router = $router;
    $this->translator = $translator;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      EmailEvents::EMAIL_ON_BUILD => ['onEmailBuild', 0],
      EmailEvents::EMAIL_ON_SEND => ['onEmailGenerate', 0],
      EmailEvents::EMAIL_ON_DISPLAY => ['onEmailGenerate', 0],
    ];
  }

  /**
   * Get list of all sub preference center entities.
   *
   * @return \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter[]
   */
  protected function getSubPreferenceCenters() {
    $repo = $this->em->getRepository(SubPreferenceCenter::class);

    return $repo->getEntities();
  }

  /**
   * @param \Mautic\EmailBundle\Event\EmailBuilderEvent $event
   */
  public function onEmailBuild(EmailBuilderEvent $event) {
    $tokens = [];

    foreach ($this->getSubPreferenceCenters() as $entity) {
      $tokens['{' . $entity->getToken() . '}'] = $this->translator->trans('mautic.subpreference_center.token.unsubscribe');
    }

    if ($event->tokensRequested(array_keys($tokens))) {
      $event->addTokens($event->filterTokens($tokens));
    }
  }

  /**
   * @param \Mautic\EmailBundle\Event\EmailSendEvent $event
   */
  public function onEmailGenerate(EmailSendEvent $event) {
    $id_hash = $event->getIdHash() ?? uniqid();

    foreach ($this->getSubPreferenceCenters() as $entity) {
      $event->addToken('{' . $entity->getToken() . '}', $this->getUnsubscribeLink($entity, $id_hash));
    }
  }

  /**
   * Get generated link for unsubscribe token.
   *
   * @param \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $entity
   *   The sub preference center entity.
   * @param string $id_hash
   *   Unique email hash.
   *
   * @return string
   *   The generated link.
   */
  protected function getUnsubscribeLink(SubPreferenceCenter $entity, $id_hash) {
    $unsubscribe_text = $this->coreParametersHelper->get('unsubscribe_text');

    if (!$unsubscribe_text) {
      $unsubscribe_text = $this->translator->trans('mautic.email.unsubscribe.text', ['%link%' => '|URL|']);
    }

    $params = [
      'hash' => implode('-', [$id_hash, $entity->getId()]),
    ];
    $url = $this->router->generate('mautic_subpreference_center_contact_unsubscribe', $params, UrlGeneratorInterface::ABSOLUTE_URL);

    return str_replace('|URL|', $url, $unsubscribe_text);
  }

}
