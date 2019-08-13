<?php

class Ameex_ResidentialCommercial_Model_FedEx extends Mage_Usa_Model_Shipping_Carrier_Fedex    
{
    protected function _formRateRequest($purpose)
    {
        $strpos = strpos(Mage::helper('core/url')->getCurrentUrl(),'admin');
		if(!$strpos)
			$quote = Mage::getSingleton('checkout/session')->getQuote();
		else
			$quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
		$residentialCommercial = $quote->getShippingAddress()->getResidentialcommercial();
        
        $r = $this->_rawRequest;
        $ratesRequest = array(
            'WebAuthenticationDetail' => array(
                'UserCredential' => array(
                    'Key'      => $r->getKey(),
                    'Password' => $r->getPassword()
                )
            ),
            'ClientDetail' => array(
                'AccountNumber' => $r->getAccount(),
                'MeterNumber'   => $r->getMeterNumber()
            ),
            'Version' => $this->getVersionInfo(),
            'RequestedShipment' => array(
                'DropoffType'   => $r->getDropoffType(),
                'ShipTimestamp' => date('c'),
                'PackagingType' => $r->getPackaging(),
                'TotalInsuredValue' => array(
                    'Amount'  => $r->getValue(),
                    'Currency' => $this->getCurrencyCode()
                ),
                'Shipper' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getOrigPostal(),
                        'CountryCode' => $r->getOrigCountry()
                    )
                ),
                'Recipient' => array(
                    'Address' => array(
                        'PostalCode'  => $r->getDestPostal(),
                        'CountryCode' => $r->getDestCountry(),
                        //'Residential' => (bool)$this->getConfigData('residence_delivery') //Edited By Ameex
                        'Residential' =>($residentialCommercial-1)
                    )
                ),
                'ShippingChargesPayment' => array(
                    'PaymentType' => 'SENDER',
                    'Payor' => array(
                        'AccountNumber' => $r->getAccount(),
                        'CountryCode'   => $r->getOrigCountry()
                    )
                ),
                'CustomsClearanceDetail' => array(
                    'CustomsValue' => array(
                        'Amount' => $r->getValue(),
                        'Currency' => $this->getCurrencyCode()
                    )
                ),
                'RateRequestTypes' => 'LIST',
                'PackageCount'     => '1',
                'PackageDetail'    => 'INDIVIDUAL_PACKAGES',
                'RequestedPackageLineItems' => array(
                    '0' => array(
                        'Weight' => array(
                            'Value' => (float)$r->getWeight(),
                            'Units' => $this->getConfigData('unit_of_measure')
                        ),
                        'GroupPackageCount' => 1,
                    )
                )
            )
        );

        if ($purpose == self::RATE_REQUEST_GENERAL) {
            $ratesRequest['RequestedShipment']['RequestedPackageLineItems'][0]['InsuredValue'] = array(
                'Amount'  => $r->getValue(),
                'Currency' => $this->getCurrencyCode()
            );
        } else if ($purpose == self::RATE_REQUEST_SMARTPOST) {
            $ratesRequest['RequestedShipment']['ServiceType'] = self::RATE_REQUEST_SMARTPOST;
            $ratesRequest['RequestedShipment']['SmartPostDetail'] = array(
                'Indicia' => ((float)$r->getWeight() >= 1) ? 'PARCEL_SELECT' : 'PRESORTED_STANDARD',
                'HubId' => $this->getConfigData('smartpost_hubid')
            );
        }
        return $ratesRequest;
    }   
   
}
