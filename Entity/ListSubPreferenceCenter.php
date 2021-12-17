<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\LeadBundle\Entity\LeadList;

class ListSubPreferenceCenter {

  /**
   * @var \Mautic\LeadBundle\Entity\LeadList
   **/
  private $list;

  /**
   * @var \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter
   */
  private $subPreferenceCenter;

  /**
   * @var \DateTime
   */
  private $dateAdded;

  public static function loadMetadata(ORM\ClassMetadata $metadata) {
    $builder = new ClassMetadataBuilder($metadata);

    $builder->setTable('lead_lists_subpreference_centers')
      ->setCustomRepositoryClass(ListSubPreferenceCenterRepository::class);

    $builder->createOneToOne('list', LeadList::class)
      ->makePrimaryKey()
      ->addJoinColumn('leadlist_id', 'id', FALSE, FALSE, 'CASCADE')
      ->build();

    $builder->createManyToOne('subPreferenceCenter', SubPreferenceCenter::class)
      ->addJoinColumn('subpreference_id', 'id', TRUE, FALSE, 'SET NULL')
      ->build();

    $builder->addDateAdded();
  }

  /**
   * @return \DateTime
   */
  public function getDateAdded() {
    return $this->dateAdded;
  }

  /**
   * @param \DateTime $date
   */
  public function setDateAdded($date) {
    $this->dateAdded = $date;
  }

  /**
   * @return \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter
   */
  public function getSubPreferenceCenter() {
    return $this->subPreferenceCenter;
  }

  /**
   * @param \MauticPlugin\SubPreferenceCenterBundle\Entity\SubPreferenceCenter $lead
   *
   * @return $this
   */
  public function setSubPreferenceCenter(SubPreferenceCenter $sub_preference_center) {
    $this->subPreferenceCenter = $sub_preference_center;

    return $this;
  }

  /**
   * @return \Mautic\LeadBundle\Entity\LeadList
   */
  public function getList() {
    return $this->list;
  }

  /**
   * @param \Mautic\LeadBundle\Entity\LeadList $list
   *
   * @return $this
   */
  public function setList(LeadList $list) {
    $this->list = $list;

    return $this;
  }

}
