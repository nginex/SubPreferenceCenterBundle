<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;
use Mautic\LeadBundle\Entity\LeadList;

class ListSubPreferenceCenterRepository extends CommonRepository {

  /**
   * Get sub preference center from the list.
   *
   * @param \Mautic\LeadBundle\Entity\LeadList $list
   *   The lead list entity.
   *
   * @return \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter|null
   *   The sub preference center.
   */
  public function getSubPreferenceCenterByList(LeadList $list) {
    $entity = $this->getEntity($list->getId());

    if (!$entity) {
      return NULL;
    }

    return $entity->getSubPreferenceCenter();
  }

  public function getListIdsBySubPreferenceCenter(SubPreferenceCenter $center) {
    /** @var \MauticPlugin\SubPreferenceCenterBundle\Entity\ListSubPreferenceCenter[] $entities */
    $entities = $this->findBy(['subPreferenceCenter' => $center]);

    $lists = [];
    foreach ($entities as $entity) {
      $list = $entity->getList();
      $lists[] = [
        'id' => $list->getId(),
        'name' => $list->getName(),
      ];
    }

    return $lists;
  }

  /**
   * Save connection between sub preference center and lead list.
   *
   * @param \Mautic\LeadBundle\Entity\LeadList $list
   *   The lead list entity.
   * @param \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $center
   *   The sub preference center entity.
   */
  public function setSubPreferenceCenterForList(LeadList $list, SubPreferenceCenter $center) {
    $entity = $this->getEntity($list->getId());

    if (!$entity) {
      $entity = new ListSubPreferenceCenter();
      $entity->setList($list);
      $entity->setDateAdded(new \DateTime());
    }

    $entity->setSubPreferenceCenter($center);

    $this->saveEntity($entity);
  }

  /**
   * Clean up connection between sub preference center and lead list.
   *
   * @param \Mautic\LeadBundle\Entity\LeadList $list
   *   The lead list entity.
   */
  public function deleteSubPreferenceCenterFromList(LeadList $list) {
    if ($entity = $this->getEntity($list->getId())) {
      $this->deleteEntity($entity);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getTableAlias() {
    return 'ls';
  }

}
