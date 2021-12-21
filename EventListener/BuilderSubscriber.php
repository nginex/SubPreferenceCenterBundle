<?php

namespace MauticPlugin\SubPreferenceCenterBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CoreBundle\Factory\ModelFactory;
use Mautic\CoreBundle\Helper\TemplatingHelper;
use Mautic\CoreBundle\Templating\Helper\FormHelper;
use Mautic\PageBundle\Event\PageBuilderEvent;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PageBundle\PageEvents;
use MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Form\Type\ContactSegmentsType;
use MauticPlugin\SubPreferenceCenterBundle\Form\Type\SlotSubSegmentListType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\Translation\TranslatorInterface;

class BuilderSubscriber implements EventSubscriberInterface {

  const subSegmentListRegex = '{subsegmentlist}';
  const successmessage = '{successmessage}';

  /**
   * @var \Symfony\Contracts\Translation\TranslatorInterface
   */
  protected $translator;

  /**
   * @var \Mautic\CoreBundle\Helper\TemplatingHelper
   */
  protected $templating;

  /**
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * @var \Doctrine\ORM\EntityManagerInterface
   */
  protected $em;

  /**
   * @var \Mautic\CoreBundle\Factory\ModelFactory
   */
  protected $modelFactory;

  /**
   * @var \Symfony\Component\Form\FormFactoryInterface
   */
  protected $formFactory;
  /**
   * @var \Mautic\CoreBundle\Templating\Helper\FormHelper
   */
  protected $formHelper;

  /**
   * BuilderSubscriber constructor.
   */
  public function __construct(TranslatorInterface $translator, TemplatingHelper $templating, RequestStack $request_stack, EntityManagerInterface $em, ModelFactory $model_factory, FormFactoryInterface $form_factory, FormHelper $form_helper) {
    $this->translator = $translator;
    $this->templating = $templating;
    $this->requestStack = $request_stack;
    $this->em = $em;
    $this->modelFactory = $model_factory;
    $this->formFactory = $form_factory;
    $this->formHelper = $form_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      PageEvents::PAGE_ON_DISPLAY => ['onPageDisplay', 0],
      PageEvents::PAGE_ON_BUILD => ['onPageBuild', 0],
    ];
  }

  public function onPageBuild(PageBuilderEvent $event) {
    $event->addSlotType(
      'subsegmentlist',
      $this->translator->trans('mautic.subpreference_center.slot.label.subsegmentlist'),
      'list-alt',
      'SubPreferenceCenterBundle:Slots:subsegmentlist.html.php',
      SlotSubSegmentListType::class,
      600
    );
  }

  public function onPageDisplay(PageDisplayEvent $event) {
    $content = $event->getContent();
    $params = $event->getParams();

    $request = $this->requestStack->getCurrentRequest();
    $token = $request->query->get('sub_pref_token');
    $id_hash = $request->query->get('id_hash');
    $saved_pref = $request->query->get('saved_preferences', 0);

    if (!$token || !$id_hash) {
      return;
    }

    /** @var \Mautic\EmailBundle\Model\EmailModel $email_model */
    $email_model = $this->modelFactory->getModel('email');
    $stat = $email_model->getEmailStatus($id_hash);

    if (!$stat) {
      return;
    }

    $lead = $stat->getLead();

    if (!$lead) {
      return;
    }

    $sub_pref_repo = $this->em->getRepository(SubPreferenceCenter::class);

    /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $entity */
    $entity = $sub_pref_repo->findOneBy(['token' => $token]);

    if (!$entity) {
      return;
    }
    $params['message_visible'] = FALSE;

    // Save settings.
    if ($saved_pref && $request->getMethod() !== 'GET') {
      /** @var \Mautic\LeadBundle\Model\LeadModel $lead_model */
      $lead_model = $this->modelFactory->getModel('lead');
      $form_data = $request->request->get('contact_segments');
      $selected_lists = $form_data['lead_lists'] ?? [];
      $available_options = array_column($this->getAvailableListOptions($entity), 'id');

      if (count($selected_lists)) {
        $lead_model->addToLists($lead, $selected_lists);
      }

      $to_be_removed = array_diff($available_options, $selected_lists);

      if (count($to_be_removed)) {
        $lead_model->removeFromLists($lead, array_values($to_be_removed));
      }

      $params['message_visible'] = TRUE;
    }

    $params['entity'] = $entity;
    $params['lead'] = $lead;
    $params['id_hash'] = $id_hash;

    // Replace token.
    if (strpos($content, self::subSegmentListRegex) !== FALSE) {
      $list = $this->renderSubSegmentList($params);
      $content = str_ireplace(self::subSegmentListRegex, $list, $content);
    }

    $successMessageDataSlots = [
      'data-slot="successmessage"',
      'class="pref-successmessage"',
    ];
    $successMessageDataSlotsHidden = [];
    $visibility = $params['message_visible'] ? 'block' : 'none';

    foreach ($successMessageDataSlots as $successMessageDataSlot) {
      $successMessageDataSlotsHidden[] = $successMessageDataSlot . ' style=display:' . $visibility;
    }
    $content = str_replace(
      $successMessageDataSlots,
      $successMessageDataSlotsHidden,
      $content
    );

    $event->setContent($content);
  }

  /**
   * Renders the HTML for the sub segment list.
   *
   * @return string
   */
  private function renderSubSegmentList(array $params = []) {
    static $content = '';

    if (empty($content)) {
      $form = $this->getSubSegmentListForm($params);
      $form_view = $form->createView();
      $params['form'] = $form_view;

      $content = $this->formHelper->start($form_view);
      $content .= '<div class="pref-subsegmentlist">';
      $content .= $this->templating->getTemplating()->render('SubPreferenceCenterBundle:Slots:subsegmentlist.html.php', $params);
      $content .= '</div>';
    }

    return $content;
  }

  /**
   * @param array $params
   *   Contains lead, id_hash and entity (sub preference center).
   *
   * @return \Symfony\Component\Form\FormInterface
   */
  protected function getSubSegmentListForm(array $params = []) {
    /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $entity */
    $entity = $params['entity'];
    $page = $entity->getPage();

    /** @var \Mautic\PageBundle\Model\PageModel $page_model */
    $page_model = $this->modelFactory->getModel('page');

    /** @var \Mautic\LeadBundle\Model\LeadModel $lead_model */
    $lead_model = $this->modelFactory->getModel('lead');

    $route_params = [
      'slug' => $page_model->generateSlug($page),
      'id_hash' => $params['id_hash'],
      'sub_pref_token' => $entity->getToken(),
      'saved_preferences' => 1,
    ];
    $action = $page_model->buildUrl('mautic_page_public', $route_params);

    $available_lists = $this->getAvailableListOptions($entity);
    $lead_lists = $lead_model->getLists($params['lead'], TRUE, TRUE, TRUE, TRUE);

    $selected_lists = array_filter($lead_lists, function (array $list) use ($available_lists) {
      return in_array($list['id'], array_column($available_lists, 'id'));
    });

    $data['lead_lists'] = array_column($selected_lists, 'id');

    $form = $this->formFactory->create(
      ContactSegmentsType::class,
      $data,
      [
        'action' => $action,
        'filtered_choices' => array_column($available_lists, 'id', 'name'),
        'allow_extra_fields' => TRUE,
      ]
    );

    return $form;
  }

  /**
   * @param \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $entity
   *
   * @return array
   */
  protected function getAvailableListOptions(SubPreferenceCenter $entity) {
    /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenterRepository $list_sub_pref_repo */
    $list_sub_pref_repo = $this->em->getRepository(ListSubPreferenceCenter::class);

    return $list_sub_pref_repo->getListIdsBySubPreferenceCenter($entity);
  }

}
