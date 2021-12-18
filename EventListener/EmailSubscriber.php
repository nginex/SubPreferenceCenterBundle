<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Helper\CoreParametersHelper;
use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use Mautic\PageBundle\Entity\Page;
use Mautic\PageBundle\Model\PageModel;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
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
   * @var \Mautic\PageBundle\Model\PageModel
   */
  protected $pageModel;

  /**
   * @var \Symfony\Contracts\Translation\TranslatorInterface
   */
  protected $translator;

  /**
   * SubPreferenceCenterType constructor.
   */
  public function __construct(EntityManagerInterface $em, CoreParametersHelper $core_parameters_helper, PageModel $page_model, TranslatorInterface $translator) {
    $this->em = $em;
    $this->coreParametersHelper = $core_parameters_helper;
    $this->pageModel = $page_model;
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
    foreach ($this->getSubPreferenceCenters() as $entity) {
      $event->addToken('{' . $entity->getToken() . '}', $this->getUnsubscribeLinkByPage($entity->getPage()));
    }
  }

  /**
   * Get generated link for unsubscribe token per page.
   *
   * @param \Mautic\PageBundle\Entity\Page $page
   *   The landing page entity.
   *
   * @return string
   *   The generated link.
   */
  protected function getUnsubscribeLinkByPage(Page $page) {
    $unsubscribe_text = $this->coreParametersHelper->get('unsubscribe_text');

    if (!$unsubscribe_text) {
      $unsubscribe_text = $this->translator->trans('mautic.email.unsubscribe.text', ['%link%' => '|URL|']);
    }

    $page_public_url = $this->pageModel->generateUrl($page);
    return str_replace('|URL|', $page_public_url, $unsubscribe_text);
  }

}
