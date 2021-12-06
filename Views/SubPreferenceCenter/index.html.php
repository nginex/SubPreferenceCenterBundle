<?php

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'subPreferenceCenter');
$view['slots']->set('headerTitle', $view['translator']->trans('mautic.subpreference_center.header'));

$view['slots']->set(
  'actions',
  $view->render(
    'MauticCoreBundle:Helper:page_actions.html.php',
    [
      'templateButtons' => [
        'new' => $view['security']->isGranted('subPreferenceCenter:subPreferenceCenter:create'),
      ],
      'routeBase' => 'mautic_subpreference_center',
      'langVar' => 'subpreference_center',
    ]
  )
);
?>

<div class="panel panel-default bdr-t-wdh-0 mb-0">
  <?php echo $view->render(
    'MauticCoreBundle:Helper:list_toolbar.html.php',
    [
      'searchValue' => $searchValue,
      'action' => $currentRoute,
    ]
  ); ?>
  <div class="page-list">
    <?php $view['slots']->output('_content'); ?>
  </div>
</div>
