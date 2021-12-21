<?php if (isset($form['lead_lists']) && count($form['lead_lists'])):?>
  <?php echo '<script src="'. $view['assets']->getUrl('plugins/SubPreferenceCenterBundle/Assets/js/prefcenter.js') . '"></script>'; ?>

  <div class="contact-segments">
    <div class="text-left">
      <label class="control-label"><?php echo $label_text ?? $view['translator']->trans('mautic.lead.form.list'); ?></label>
    </div>
    <?php foreach ($form['lead_lists'] as $key => $leadList): ?>
      <div id="segment-<?php echo $key; ?>" class="text-left">
        <?php echo $view['form']->widget($leadList); ?>
        <?php echo $view['form']->label($leadList); ?>
      </div>
    <?php endforeach; ?>
    <?php unset($form['lead_lists']); ?>
  </div>
<?php endif; ?>
