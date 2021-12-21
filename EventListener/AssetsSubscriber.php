<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomAssetsEvent;
use Mautic\InstallBundle\Install\InstallService;
use MauticPlugin\GrapesJsBuilderBundle\Integration\Config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetsSubscriber implements EventSubscriberInterface {

  /**
   * @var Config
   */
  protected $config;

  /**
   * @var \Mautic\InstallBundle\Install\InstallService
   */
  protected $installer;

  public function __construct(Config $config, InstallService $installer) {
    $this->config = $config;
    $this->installer = $installer;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      CoreEvents::VIEW_INJECT_CUSTOM_ASSETS => ['injectAssets', 0],
    ];
  }

  public function injectAssets(CustomAssetsEvent $assetsEvent) {
    if (!$this->installer->checkIfInstalled()) {
      return;
    }
    if ($this->config->isPublished()) {
      $assetsEvent->addScript('plugins/SubPreferenceCenterBundle/Assets/js/builder.js');
    }
  }

}
