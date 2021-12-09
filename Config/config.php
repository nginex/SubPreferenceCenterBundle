<?php

return [
  'name' => 'Sub Preference Center',
  'description' => 'Set up multiple preference centers in your application.',
  'version' => '1.0',
  'author' => 'Dropsolid',
  'routes' => [
    'main' => [
      'mautic_subpreference_center_index' => [
        'path' => '/subpreference-centers/{page}',
        'controller' => 'SubPreferenceCenterBundle:SubPreferenceCenter:index',
      ],
      'mautic_subpreference_center_action' => [
        'path' => '/subpreference-centers/{objectAction}/{objectId}',
        'controller' => 'SubPreferenceCenterBundle:SubPreferenceCenter:execute',
      ],
    ],
  ],
  'services' => [
    'repositories' => [
      'mautic.subPreferenceCenter.repository.subPreferenceCenter' => [
        'class' => Doctrine\ORM\EntityRepository::class,
        'factory' => ['@doctrine.orm.entity_manager', 'getRepository'],
        'arguments' => [
          \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter::class,
        ],
      ],
    ],
    'forms' => [
      'mautic.subPreferenceCenter.form.type.subPreferenceCenter' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Form\Type\SubPreferenceCenterEntityType',
        'arguments' => [
          'doctrine.orm.entity_manager',
        ],
      ],
    ],
    'events' => [
      'mautic.subPreferenceCenter.form.subscriber' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\EventListener\FormSubscriber',
      ],
    ],
    'models' => [
      'mautic.subPreferenceCenter.model.subPreferenceCenter' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Model\SubPreferenceCenterModel',
      ],
    ],
  ],
  'menu' => [
    'main' => [
      'mautic.subpreference_center.menu.index' => [
        'route' => 'mautic_subpreference_center_index',
        'access' => 'subPreferenceCenter:subPreferenceCenter:view',
        'iconClass' => 'fa-tasks',
        'priority' => 1,
      ],
    ],
  ],
];
