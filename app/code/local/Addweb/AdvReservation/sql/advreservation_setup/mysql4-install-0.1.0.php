<?php
$installer = $this;
$installer->startSetup();
$sql=<<<SQLTEXT
ALTER TABLE `{$this->getTable('catalog/product_option_type_value')}` ADD `max_adults` VARCHAR(128) NULL DEFAULT NULL , ADD `max_childs` VARCHAR(128) NULL DEFAULT NULL;
SQLTEXT;

$installer->run($sql);
//demo 
//Mage::getModel('core/url_rewrite')->setId(null);
//demo 
$installer->endSetup();
	 