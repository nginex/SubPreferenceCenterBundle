<?php

$view->extend('MauticCoreBundle:Default:content.html.php');
$view['slots']->set('mauticContent', 'subPreferenceCenter');
?>

<?php
$header = ($entity->getId())
  ? $view['translator']->trans('mautic.subpreference_center.menu.edit', ['%name%' => $view['translator']->trans($entity->getName())])
  : $view['translator']->trans('mautic.subpreference_center.menu.new');

$view['slots']->set('headerTitle', $header);
?>

<?php echo $view['form']->start($form); ?>

<div class="box-layout">
  <div class="col-xs-12 bg-auto height-auto">
    <div class="pa-md">
      <div class="row">
        <div class="col-xs-6">
          <?php echo $view['form']->row($form['name']); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <?php echo $view['form']->row($form['token']); ?>
        </div>
      </div>
      <div class="row">
        <div class="col-xs-6">
          <?php echo $view['form']->row($form['page']); ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php echo $view['form']->end($form); ?>
