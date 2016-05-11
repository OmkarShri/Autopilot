<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml customer grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Company_Web_Block_Adminhtml_Productlist extends Mage_Adminhtml_Block_Widget_Grid
{

   
    protected function _prepareCollection()
    {

        $collection = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('cost')
            ->addAttributeToSelect('automode') 
            ->addAttributeToSelect('auto_rulename')           
            ->addAttributeToSelect('comp_1')
            ->addAttributeToSelect('comp_2')
            ->addAttributeToSelect('comp_3')
            ->addAttributeToSelect('comp_4')
            ->addAttributeToSelect('comp_5')
            ->addAttributeToSelect('comp_6')
            ->addAttributeToSelect('comp_7')
            ->addAttributeToSelect('comp_8')
            ->addAttributeToSelect('comp_9')
            ->addAttributeToSelect('comp_10');

             $this->setCollection($collection);

        return parent::_prepareCollection();
	}

    protected function _prepareColumns()
    {
       
        $this->addColumn('entity_id',
            array(
                'header'=> Mage::helper('catalog')->__('ID'),
                'width' => '40px',
                'type'  => 'number',
                'index' => 'entity_id',
        ));

        $this->addColumn('name',
            array(
                'header'=> Mage::helper('catalog')->__('Name'),
                'index' => 'name',
                'width' => '200px',
        ));

        $this->addColumn('sku',
            array(
                'header'=> Mage::helper('catalog')->__('SKU'),
                'width' => '80px',
                'index' => 'sku',
        ));

        $currency_code = Mage::app()->getStore()->getCurrentCurrencyCode();
        $this->addColumn('price',
            array(
                'header'=> Mage::helper('catalog')->__('Actual Price'),
                'type'  => 'price',
                'currency_code' => $currency_code,
                'width' => '150px',
                'index' => 'price',
        ));

        


        // competitors based on competitors staus -> enabled
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');
        $writeConnection = $resource->getConnection('core_write');

        $query = "select status,competitor from auto_competitor where status='yes' order by id";
        $res = $readConnection->fetchAll($query);
       
        $i =1;
        foreach ($res as $row) {
            
        $col = 'comp_'.$i;

        $this->addColumn($col,
            array(
                'header'=> Mage::helper('catalog')->__($row['competitor']),
                'width' => '80px',
                'type'  => 'price',
                'currency_code' => $currency_code,
                'index' => $col,
            ));


        $i++;
       
        } // ============= foreach ends here

         $code = Mage::app()->getStore()->getCurrentCurrencyCode();
         $this->addColumn('cost',
            array(
                'header'=> Mage::helper('catalog')->__('Cost'),
                'type'  => 'price',
                'currency_code' => $code,
                'width' => '150px',
                'index' => 'cost',
        ));

        $this->addColumn('auto_rulename',
            array(
                'header'=> Mage::helper('catalog')->__('Rule'),
                'width' => '80px',
                'index' => 'auto_rulename',                
        ));

        $this->addColumn('automode',
            array(
                'header'=> Mage::helper('catalog')->__('Status'),
                'width' => '80px',
                'index' => 'automode',
        ));


  //       $this->addColumn('entity_id', array(
		//     'header_css_class' => 'a-center',
		//     'header' => Mage::helper('catalog')->__('Action'),
		//     'index' => 'entity_id',
		//     'type' => 'checkbox',
		//     'align' => 'center',
		//     'values' => array('1', '2')
		// ));
        
        return parent::_prepareColumns();
    }

    protected function _getHelper()
    {
        return Mage::helper('company_web');
    }

	
    protected function _prepareMassaction()
	{
		$this->setMassactionIdField('entity_id');
		$this->getMassactionBlock()->setFormFieldName('pid');

		$resource = Mage::getSingleton('core/resource');
		$readConnection = $resource->getConnection('core_read');
		$query = "select * from auto_rulemanager";
		$res = $readConnection->fetchAll($query);

		foreach($res as $row){

		$this->getMassactionBlock()->addItem($row['ruleid'], array(
		'label'=> Mage::helper('catalog')->__($row['name']),		
		'url'  => $this->getUrl('*/*/massstatus', array('rule'=>$row['ruleid'])),
		'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));


		}

		
		// $this->getMassactionBlock()->addItem('test', array(
		// 'label'=> Mage::helper('catalog')->__('Rule1'),		
		// 'url'  => $this->getUrl('*/*/massStatus', array('rule'=>'test')),
		// 'confirm' => Mage::helper('catalog')->__('Are you sure?')
		// ));

		// $this->getMassactionBlock()->addItem('test2', array(
		// 'label'=> Mage::helper('catalog')->__('Rule2'),		
		// 'url'  => $this->getUrl('*/*/massStatus', array('rule'=>'test2')),
		// 'confirm' => Mage::helper('catalog')->__('Are you sure?')
		// ));

		return $this;

		// $this->setMassactionIdField('entity_id');

		// $this->getMassactionBlock()->setFormFieldName('pid');

		// $statuses = Mage::getSingleton('web/status')->getOptionArray();

		
		  //       array_unshift($statuses, array('label'=>'', 'value'=>''));
		  //       $this->getMassactionBlock()->addItem('status', array(
		  //            'label'=> Mage::helper('web')->__('Change status'),
		  //            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
		  //            'additional' => array(
		  //                   'visibility' => array(
		  //                        'name' => 'status',
		  //                        'type' => 'select',
		  //                        'class' => 'required-entry',
		  //                        'label' => Mage::helper('web')->__('Status'),
		  //                        'values' => $statuses
		  //                    )
		  //            )
		  //       ));
		  //       return $this;

	}


}
