<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\PageBundle\Event\PageDisplayEvent;
use Mautic\PageBundle\EventListener\BuilderSubscriber;
use Mautic\PageBundle\PageEvents;
use MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter;
use MauticPlugin\SubPreferenceCenterBundle\Form\Type\ContactSegmentsType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContactPreferencesController extends CommonController {

  public function unsubscribeAction($hash) {
    [$id_hash, $sub_pref_id] = explode('-', $hash);

    /** @var \Mautic\EmailBundle\Model\EmailModel $email_model */
    $email_model = $this->getModel('email');
    $stat = $email_model->getEmailStatus($id_hash);

    if (empty($stat)) {
      throw new NotFoundHttpException('Email stat could not be found by provided id hash.');
    }

    /** @var \MauticPlugin\SubPreferenceCenterBundle\Model\SubPreferenceCenterModel $sub_pref_model */
    $sub_pref_model = $this->getModel('subPreferenceCenter');
    $sub_pref = $sub_pref_model->getEntity($sub_pref_id);

    if (!$sub_pref) {
      throw new NotFoundHttpException('Sub preference center could not be found by provided hash.');
    }

    $page = $sub_pref->getPage();

    if (!$page->getIsPreferenceCenter()) {
      throw new NotFoundHttpException('Landing page is not preference center page.');
    }

    $lead = $stat->getLead();

    if (!$lead) {
      throw new NotFoundHttpException('Contact could not be found by provided email.');
    }

    // Set the lead as current lead.
    $this->get('mautic.tracker.contact')->setTrackedContact($lead);

    // Set lead lang.
    if ($lead->getPreferredLocale()) {
      $this->get('translator')->setLocale($lead->getPreferredLocale());
    }

    if ($this->isFormSubmitted()) {
      /** @var \Mautic\LeadBundle\Model\LeadModel $lead_model */
      $lead_model = $this->getModel('lead');
      $form_data = $this->request->request->get('contact_segments');
      $selected_lists = $form_data['lead_lists'] ?? [];
      $available_options = array_column($this->getAvailableListOptions($sub_pref), 'id');

      if (count($selected_lists)) {
        $lead_model->addToLists($lead, $selected_lists);
      }

      $to_be_removed = array_diff($available_options, $selected_lists);

      if (count($to_be_removed)) {
        $lead_model->removeFromLists($lead, array_values($to_be_removed));
      }
    }

    $view_parameters = [
      'lead' => $lead,
      'idHash' => $id_hash,
      'showContactSegments' => $this->get('mautic.helper.core_parameters')->get('show_contact_segments'),
    ];

    $form = $this->getContactPreferencesForm($sub_pref, $lead, $hash);
    $form_view = $form->createView();
    $content = $page->getCustomHtml();

    /** @var \Mautic\CoreBundle\Templating\Helper\FormHelper $form_helper */
    $form_helper = $this->get('templating.helper.form');
    $params = array_merge(
      $view_parameters,
      [
        'form' => $form_view,
        'startform' => $form_helper->start($form_view),
        'custom_tag' => '<a name="end-' . $form_view->vars['id'] . '"></a>',
        'showContactSegments' => FALSE !== strpos($content, 'data-slot="segmentlist"') || FALSE !== strpos($content, BuilderSubscriber::segmentListRegex),
      ]
    );

    // Replace tokens in preference center page.
    $event = new PageDisplayEvent($content, $page, $params);
    $this->get('event_dispatcher')->dispatch(PageEvents::PAGE_ON_DISPLAY, $event);
    $content = $event->getContent();
    $this->adjustSuccessMessage($content);

    $template = $this->coreParametersHelper->get('theme');
    $theme = $this->factory->getTheme($template);
    if ($theme->getTheme() !== $template) {
      $template = $theme->getTheme();
    }

    $view_parameters = [
      'template' => $template,
      'message'  => $content,
    ];

    $content_template = $this->factory->getHelper('theme')->checkForTwigTemplate(':'.$template.':message.html.php');

    return $this->render($content_template, $view_parameters);
  }

  /**
   * @return bool
   */
  protected function isFormSubmitted() {
    return $this->request->getMethod() !== 'GET' && $this->request->query->get('saved_preferences', 0);
  }

  /**
   * @param string $content
   */
  protected function adjustSuccessMessage(&$content) {
    $visibility = $this->isFormSubmitted() ? 'block' : 'none';

    $success_message_data_slots = [
      'data-slot="successmessage"',
      'class="pref-successmessage"',
    ];
    $success_message_data_slots_hidden = [];
    foreach ($success_message_data_slots as $successMessageDataSlot) {
      $success_message_data_slots_hidden[] = $successMessageDataSlot . ' style=display:' . $visibility;
    }
    $content = str_replace(
      $success_message_data_slots,
      $success_message_data_slots_hidden,
      $content
    );
  }

  protected function getContactPreferencesForm(SubPreferenceCenter $sub_pref, Lead $lead, $hash) {
    $route_params = [
      'hash' => $hash,
      'saved_preferences' => 1,
    ];
    $action = $this->get('router')->generate('mautic_subpreference_center_contact_unsubscribe', $route_params, UrlGeneratorInterface::ABSOLUTE_URL);

    $available_lists = $this->getAvailableListOptions($sub_pref);
    $lead_lists = $this->getModel('lead')->getLists($lead, TRUE, TRUE, TRUE, TRUE);

    $selected_lists = array_filter($lead_lists, function (array $list) use ($available_lists) {
      return in_array($list['id'], array_column($available_lists, 'id'));
    });

    $data['lead_lists'] = array_column($selected_lists, 'id');

    $form = $this->get('form.factory')->create(
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
    $list_sub_pref_repo = $this->get('doctrine.orm.entity_manager')->getRepository(ListSubPreferenceCenter::class);

    return $list_sub_pref_repo->getListIdsBySubPreferenceCenter($entity);
  }

}
