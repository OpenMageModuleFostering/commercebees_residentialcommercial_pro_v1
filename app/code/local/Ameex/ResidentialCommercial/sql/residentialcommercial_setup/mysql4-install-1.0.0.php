<?php
$installer = $this;
 
$installer->startSetup();
 
$installer->addAttribute('customer_address', 'residentialcommercial', array(
    'type' => 'int',
    'input' => 'select',
    'label' => 'Address Type',
    'global' => 1,
    'visible' => 1,
    'required' => 0,
    'user_defined' => 1,
    'visible_on_front' => 1
));
$installer->updateAttribute(
    'customer_address',
    'residentialcommercial',
    'source_model',
    'residentialcommercial/source'
);
Mage::getSingleton('eav/config')
    ->getAttribute('customer_address', 'residentialcommercial')
    ->setData('used_in_forms', array('customer_register_address','customer_address_edit','adminhtml_customer_address'))
    ->save();
	
$tablequote = $this->getTable('sales/quote_address');
$installer->run("ALTER TABLE $tablequote ADD COLUMN residentialcommercial INT(2) NOT NULL");

$tablequote = $this->getTable('sales/order_address');
$installer->run("ALTER TABLE $tablequote ADD COLUMN residentialcommercial INT(2) NOT NULL");
$installer->endSetup();
