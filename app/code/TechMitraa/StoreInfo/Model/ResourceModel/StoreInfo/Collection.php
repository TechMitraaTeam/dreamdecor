<?php
/**
 * @category   TechMitraa
 * @package    TechMitraa_StoreInfo
 * @author     bhavi.techmitraa.@gmail.com
 * @copyright  This file was generated by using Module Creator(http://code.vky.co.in/magento-2-module-creator/) provided by VKY <viky.031290@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace TechMitraa\StoreInfo\Model\ResourceModel\StoreInfo;
 
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            'TechMitraa\StoreInfo\Model\StoreInfo',
            'TechMitraa\StoreInfo\Model\ResourceModel\StoreInfo'
        );
    }
}