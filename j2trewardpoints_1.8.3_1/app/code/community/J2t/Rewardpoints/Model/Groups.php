<?php

/**
 * J2T RewardsPoint2
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@j2t-design.com so we can send you a copy immediately.
 *
 * @category   Magento extension
 * @package    RewardsPoint2
 * @copyright  Copyright (c) 2009 J2T DESIGN. (http://www.j2t-design.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class J2t_Rewardpoints_Model_Groups {

    public function toOptionArray() {
        $return = Mage::getResourceModel('customer/group_collection')
                        ->load()->toOptionArray();

        $first = array(array('label' => Mage::helper('rewardpoints')->__('No Exclusion'), 'value' => '-1'));
        $return = array_merge($first, $return);
        return $return;
    }

}
