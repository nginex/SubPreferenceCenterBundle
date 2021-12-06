<?php

namespace MauticPlugin\SubPreferenceCenterBundle\Entity;

use Mautic\CoreBundle\Entity\CommonRepository;

class SubPreferenceCenterRepository extends CommonRepository {

  /**
   * @return array
   */
  protected function getDefaultOrder() {
    return [
      ['sc.name', 'ASC'],
    ];
  }

  /**
   * @return string
   */
  public function getTableAlias() {
    return 'sc';
  }

}
