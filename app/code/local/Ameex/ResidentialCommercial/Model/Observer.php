<?php
class Ameex_ResidentialCommercial_Model_Observer
{
	public function setResidentialcommercialToQuoteAddress(Varien_Event_Observer $observer)
	{
		$residentialcommercial = $observer->getControllerAction()->getRequest()->getPost('residentialcommercial');
		Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setResidentialcommercial($residentialcommercial)->save();		
	}
}
