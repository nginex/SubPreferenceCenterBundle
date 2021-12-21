<?php

if ('index' == $tmpl) {
  $view->extend('SubPreferenceCenterBundle:SubPreferenceCenter:index.html.php');
}
?>
<?php if (count($items)): ?>
  <div class="table-responsive">
    <table class="table table-hover table-striped table-bordered" id="subPreferenceCenterTable">
      <thead>
      <tr>
        <?php
        echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
            'checkall' => 'true',
            'target' => '#subPreferenceCenterTable',
            'langVar' => 'subpreference_center.header',
            'routeBase' => 'mautic_subpreference_center',
            'templateButtons' => [
              'delete' => $view['security']->isGranted('subPreferenceCenter:subPreferenceCenter:delete'),
            ],
          ]
        );

        echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
            'sessionVar' => 'mautic_subpreference_center',
            'orderBy' => 'sc.id',
            'text' => 'mautic.core.id',
            'class' => 'visible-md visible-lg col-asset-id',
          ]
        );

        echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
            'sessionVar' => 'mautic_subpreference_center',
            'orderBy' => 'sc.name',
            'text' => 'mautic.core.name',
            'default' => TRUE,
          ]
        );

        echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
            'sessionVar' => 'mautic_subpreference_center',
            'orderBy' => 'sc.token',
            'text' => 'mautic.subpreference_center.field.token',
          ]
        );

        echo $view->render(
          'MauticCoreBundle:Helper:tableheader.html.php',
          [
            'sessionVar' => 'mautic_subpreference_center',
            'orderBy' => 'sc.page',
            'text' => 'mautic.subpreference_center.field.page',
          ]
        );
        ?>
      </tr>
      </thead>
      <tbody>
      <?php foreach ($items as $k => $item): ?>
        <tr>
          <td>
            <?php
            echo $view->render(
              'MauticCoreBundle:Helper:list_actions.html.php',
              [
                'item' => $item,
                'templateButtons' => [
                  'edit' => $view['security']->isGranted('subPreferenceCenter:subPreferenceCenter:edit'),
                  'delete' => $view['security']->isGranted('subPreferenceCenter:subPreferenceCenter:delete'),
                ],
                'routeBase' => 'mautic_subpreference_center',
                'langVar' => 'subpreference_center',
                'nameGetter' => 'getName',
              ]
            );
            ?>
          </td>
          <td class="visible-md visible-lg"><?php echo $item->getId(); ?></td>
          <td>
            <div>
              <a href="<?php echo $view['router']->path(
                'mautic_subpreference_center_action',
                ['objectAction' => 'edit', 'objectId' => $item->getId()]
              ); ?>"
                 data-toggle="ajax">
                <?php echo $item->getName(); ?>
              </a>
            </div>
          </td>
          <td class="visible-md visible-lg"><?php echo $item->getToken(); ?></td>
          <td class="visible-md visible-lg">
            <?php if ($page = $item->getPage()): ?>
              <div>
                <a href="<?php echo $view['router']->path(
                  'mautic_page_action',
                  ['objectAction' => 'view', 'objectId' => $page->getId()]
                ); ?>"
                   data-toggle="ajax">
                  <?php echo $page->getTitle(); ?>
                </a>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="panel-footer">
    <?php echo $view->render(
      'MauticCoreBundle:Helper:pagination.html.php',
      [
        'totalItems' => count($items),
        'page' => $page,
        'limit' => $limit,
        'menuLinkId' => 'mautic_subpreference_center_index',
        'baseUrl' => $view['router']->path('mautic_subpreference_center_index'),
        'sessionVar' => 'mautic_subpreference_center',
        'routeBase' => 'mautic_subpreference_center',
      ]
    ); ?>
  </div>
<?php else: ?>
  <?php echo $view->render('MauticCoreBundle:Helper:noresults.html.php'); ?>
<?php endif; ?>
