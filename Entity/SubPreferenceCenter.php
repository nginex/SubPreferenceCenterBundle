<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\CoreBundle\Entity\CommonEntity;
use Mautic\PageBundle\Entity\Page;
use Mautic\UserBundle\Entity\User;

class SubPreferenceCenter extends CommonEntity {

  /**
   * @var int
   */
  private $id;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $token;

  /**
   * @var \Mautic\PageBundle\Entity\Page
   */
  private $page;

  /**
   * @var int|null
   */
  private $createdBy;

  /**
   * @var string|null
   */
  private $createdByUser;

  public static function loadMetadata(ORM\ClassMetadata $metadata) {
    $builder = new ClassMetadataBuilder($metadata);

    $builder->setTable('subpreference_centers')
      ->setCustomRepositoryClass(SubPreferenceCenterRepository::class);

    $builder->addIdColumns('name', NULL);

    $builder->createField('token', 'string')
      ->nullable()
      ->build();

    $builder->createManyToOne('page', Page::class)
      ->addJoinColumn('page_id', 'id', TRUE, FALSE, 'SET NULL')
      ->build();

    $builder->createField('createdBy', 'integer')
      ->columnName('created_by')
      ->nullable()
      ->build();

    $builder->createField('createdByUser', 'string')
      ->columnName('created_by_user')
      ->nullable()
      ->build();
  }

  /**
   * Get id.
   *
   * @return int
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @param int $id
   *
   * @return $this
   */
  public function setId($id) {
    $this->id = $id;

    return $this;
  }

  /**
   * Get name.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set name.
   *
   * @param string $name
   *
   * @return $this
   */
  public function setName($name) {
    $this->name = $name;

    return $this;
  }

  /**
   * @return string
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * @param mixed $token
   *
   * @return $this
   */
  public function setToken($token) {
    $this->token = $token;

    return $this;
  }

  /**
   * @return \Mautic\PageBundle\Entity\Page
   */
  public function getPage() {
    return $this->page;
  }

  /**
   * @return $this
   */
  public function setPage(Page $page) {
    $this->page = $page;

    return $this;
  }

  /**
   * Set createdBy.
   *
   * @param User $createdBy
   *
   * @return $this
   */
  public function setCreatedBy($createdBy = NULL) {
    if (NULL != $createdBy && !$createdBy instanceof User) {
      $this->createdBy = $createdBy;
    }
    else {
      $this->createdBy = (NULL != $createdBy) ? $createdBy->getId() : NULL;
      if (NULL != $createdBy) {
        $this->createdByUser = $createdBy->getName();
      }
    }

    return $this;
  }

  /**
   * Get createdBy.
   *
   * @return int
   */
  public function getCreatedBy() {
    return $this->createdBy;
  }

  /**
   * @return string
   */
  public function getCreatedByUser() {
    return $this->createdByUser;
  }

  /**
   * @param mixed $createdByUser
   *
   * @return $this
   */
  public function setCreatedByUser($createdByUser) {
    $this->createdByUser = $createdByUser;

    return $this;
  }

}
