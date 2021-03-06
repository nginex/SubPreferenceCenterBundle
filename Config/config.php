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
    'public' => [
      'mautic_subpreference_center_contact_unsubscribe' => [
        'path' => '/contact/preferences/{hash}/unsubscribe',
        'controller' => 'SubPreferenceCenterBundle:ContactPreferences:unsubscribe',
      ],
    ],
  ],
  'services' => [
    'permissions' => [
      'mautic.subPreferenceCenter.permissions' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Security\Permissions\SubPreferenceCenterPermissions',
        'arguments' => [
          'mautic.helper.core_parameters',
        ],
      ],
    ],
    'repositories' => [
      'mautic.subPreferenceCenter.repository.subPreferenceCenter' => [
        'class' => Doctrine\ORM\EntityRepository::class,
        'factory' => ['@doctrine.orm.entity_manager', 'getRepository'],
        'arguments' => [
          'MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter',
        ],
      ],
      'mautic.subPreferenceCenter.repository.listSubPreferenceCenter' => [
        'class' => Doctrine\ORM\EntityRepository::class,
        'factory' => ['@doctrine.orm.entity_manager', 'getRepository'],
        'arguments' => [
          'MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter',
        ],
      ],
    ],
    'forms' => [
      'mautic.subPreferenceCenter.form.type.subPreferenceCenter' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Form\Type\SubPreferenceCenterType',
        'arguments' => [
          'doctrine.orm.entity_manager',
        ],
      ],
      'mautic.subPreferenceCenter.form.type.subPreferenceCenter.list' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Form\Type\SubPreferenceCenterListType',
        'arguments' => [
          'mautic.subPreferenceCenter.model.subPreferenceCenter',
        ],
      ],
      'mautic.subPreferenceCenter.form.type.slot.subsegmentlist' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Form\Type\SlotSubSegmentListType',
        'arguments' => [
          'translator',
        ],
      ],
      'mautic.subPreferenceCenter.form.type.contact_segments' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\Form\Type\ContactSegmentsType',
      ],
    ],
    'events' => [
      'mautic.subPreferenceCenter.form.type.subscriber' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\EventListener\FormTypeSubscriber',
        'arguments' => [
          'doctrine.orm.entity_manager',
        ],
      ],
      'mautic.subPreferenceCenter.lead.list.subscriber' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\EventListener\LeadListSubscriber',
        'arguments' => [
          'request_stack',
          'doctrine.orm.entity_manager',
        ],
      ],
      'mautic.subPreferenceCenter.view.subscriber' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\EventListener\ViewSubscriber',
      ],
      'mautic.subPreferenceCenter.email.subscriber' => [
        'class' => 'MauticPlugin\SubPreferenceCenterBundle\EventListener\EmailSubscriber',
        'arguments' => [
          'doctrine.orm.entity_manager',
          'mautic.helper.core_parameters',
          'router',
          'translator',
        ],
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
      'items' => [
        'mautic.subpreference_center.menu.index' => [
          'route' => 'mautic_subpreference_center_index',
          'access' => 'subPreferenceCenter:subPreferenceCenter:view',
          'parent' => 'mautic.core.components',
          'priority' => 150,
        ],
      ],
    ],
  ],
];
