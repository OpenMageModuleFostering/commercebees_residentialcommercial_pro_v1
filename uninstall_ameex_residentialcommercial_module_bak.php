<?php
	require_once 'app/Mage.php';
	umask(0);
	Mage::app('default');
	
	$setup = Mage::getModel('eav/entity_setup',  'core_setup');
	$setup->startSetup();
	$setup->removeAttribute('customer_address', 'residentialcommercial');
	
	$setup->run("DELETE FROM {$setup->getTable('core/resource')} WHERE code='residentialcommercial_setup';
				 ALTER TABLE sales_flat_quote_address DROP COLUMN residentialcommercial;
				 ALTER TABLE sales_flat_order_address DROP COLUMN residentialcommercial;
				");
	$setup->endSetup();
	
	echo 'Residential/Commercial module has been uninstalled completely';
