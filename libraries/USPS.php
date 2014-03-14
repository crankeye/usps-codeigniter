<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * USPS Codeigniter Library
 *
 * A Codeigniter library which integrates with the USPS shipping and address verification APIs
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Open Software License version 3.0
 *
 * This source file is subject to the Open Software License (OSL 3.0) that is
 * bundled with this package in the files license.txt.  It is
 * also available through the world wide web at this URL:
 * http://opensource.org/licenses/OSL-3.0
 *
 * @package	 USPS Codeigniter Library
 * @author	 Neal Lambert
 * @license	 http://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * @link	 https://github.com/crankeye/usps-codeigniter
 * @docs	 https://www.usps.com/business/web-tools-apis/address-information-v3-1d.htm
 */
class USPS {
  
  var $user_id            = '';
  var $secure             = TRUE;
  var $test               = TRUE;
  var $host               = 'production.shippingapis.com';
  var $secure_host        = 'secure.shippingapis.com';
  var $test_api           = 'ShippingAPITest.dll';
  var $prod_api           = 'ShippingAPI.dll';
 
  /**
   * Constructor - Sets USPS Preferences
   *
   * The constructor can be passed an array of config values
   */
  function USPS($config = array())
  {
    $this->initialize($config);
    log_message('debug', 'USPS Class Initialized');
  }
  
  /**
   * Initialize preferences
   *
   * @access  public
   * @param   array
   * @return  void
   */
  function initialize($config = array())
  {
    if(count($config) > 0)
	{
      foreach($config as $key => $val)
	  {
        if(isset($this->$key))
		{
          $this->$key = $val;
        }
      }
    }
  }

   /**
   * Address Standardization / Verification Request
   * @desc: Verify or standardize and address. Make sure to view the docs for more detailed information.
   * 		The website list invalid xml code. The Adddress ID is required as well as the tag <FirmName>.
   * @docs: https://www.usps.com/business/web-tools-apis/address-information-v3-1d.htm#_Toc131231403
   * @parameters: array of addresses() (max 5) each address contains an array:
   *				array (
   *					'address1' => '1234 Fake St.',
   *					'address2' => 'Suite #123',
   *					'city' => 'Coolsville',
   *					'state' => 'FL',
   *					'zip5' => '12345',
   *					'zip4' => '1234',
   *					'firm_name' => 'XYZ Corp'
   *				);
   * @access: public
   * @return: simple xml object
   */
   
  function address_standardization($addresses = array())
  {
	$index = 0;
	
	$xml = '<AddressValidateRequest USERID="'.$this->user_id.'">';
	
	//ADDRESS 1 IS THE APARTMENT/SUITE NUMBER IN THE API
	//ADDRESS 2 IS THE STREET ADDRESS IN THE API
	foreach($addresses as $address)
	{
		$xml .= '<Address ID="'.$index.'"><FirmName>'.(!empty($address['firm_name']) ? $address['firm_name'] : '' ).'</FirmName><Address1>'.(!empty($address['address2']) ? $address['address2'] : '' ).'</Address1>';
		$xml .= '<Address2>'.(!empty($address['address1']) ? $address['address1'] : '' ).'</Address2>';
		$xml .= '<City>'.(!empty($address['city']) ? $address['city'] : '' ).'</City><State>'.(!empty($address['state']) ? $address['state'] : '' ).'</State>';
		$xml .= '<Zip5>'.(!empty($address['zip5']) ? $address['zip5'] : '' ).'</Zip5><Zip4>'.(!empty($address['zip4']) ? $address['zip4'] : '' ).'</Zip4></Address>';
	
		$index++;
	}
	
	$xml .= '</AddressValidateRequest>';
	
	return $this->_request('Verify',$xml);
  }
  
  /**
   * Zipcode Lookup
   * @desc: Returns the ZIP code and the ZIP 4 code for a given address, city, and state.
   * @docs: https://www.usps.com/business/web-tools-apis/address-information-v3-1d.htm#_Toc131231406
   * @parameters: array of addresses() (max 5) each address contains an
   *				array (
   *					'address1' => '1234 Fake St.',
   *					'address2' => 'Suite #123',
   *					'city' => 'Coolsville',
   *					'state' => 'FL',
   *					'firm_name' => 'XYZ Corp'
   *				);
   * @access:  public
   * @return:  simple xml object
   */
   
  function zipcode_lookup($addresses = array())
  {
	$index = 0;
	$xml = '<ZipCodeLookupRequest USERID="'.$this->user_id.'">';
	
	//ADDRESS 1 IS THE APARTMENT/SUITE NUMBER IN THE API
	//ADDRESS 2 IS THE STREET ADDRESS IN THE API
	foreach($addresses as $address)
	{
		$xml .= '<Address ID="'.$index.'"><FirmName>'.(!empty($address['firm_name']) ? $address['firm_name'] : '' ).'</FirmName><Address1>'.(!empty($address['adddress2']) ? $address['adddress2'] : '' ).'</Address1>';
		$xml .= '<Address2>'.(!empty($address['adddress1']) ? $address['adddress1'] : '' ).'</Address2>';
		$xml .= '<City>'.(!empty($address['city']) ? $address['city'] : '' ).'</City><State>'.(!empty($address['state']) ? $address['state'] : '' ).'</State>';
		$xml .= '</Address>';
		
		$index++;
	}
	
	$xml .= '</ZipCodeLookupRequest>';
	
	return $this->_request('ZipCodeLookup',$xml);
  }
  
   /**
   * City/State Lookup
   * @desc: Verify or standardize and address. Make sure to view the docs for more detailed information.
   * @docs: https://www.usps.com/business/web-tools-apis/address-information-v3-1d.htm#_Toc131231416
   * @parameters:  array of addresses() (max 5_ each address contains an array ('zip5' => '12345');
   * @access:  public
   * @return:  simple xml object
   */
   
  function city_state_lookup($addresses)
  {
	$index = 0;
	$xml = '<CityStateLookupRequest USERID="'.$this->user_id.'">';
	
	foreach($addresses as $address)
	{
		$xml .= '<ZipCode ID="'.$index.'">';
		$xml .= '<Zip5>'.(!empty($zip5) ? $zip5 : '' ).'</Zip5></ZipCode>';
	
		$index++;
	}
	
	$xml .= '</CityStateLookupRequest>';
	
	return $this->_request('CityStateLookup',$xml);
  }
  
  /**
   * Shipping Rate Lookup
   * @docs: https://www.usps.com/business/web-tools-apis/rate-calculators-v1-7a.htm
   * @docs: https://www.usps.com/business/web-tools-apis/price-calculators.htm
   * @parameters: arrays of packages containing: service type (if first class, first_class_type as well),
   * zip origination, zip destination, pounds, ounces, container type, size
   * size = REGULAR or LARGE
   * service = PRIORITY, EXPRESS, FIRST CLASS, MEDIA, etc.
   * if service == FIRST CLASS, then: first_class_type is required
   * container = RECTANGULAR, NONRECTANGULAR or FLAT RATE BOX
   * zip origination
   * zip destination
   * pounds
   * ounces
   * @access public
   * @return simple xml object
   */
  function shipping_rate_lookup($packages = array())
  {	
	$xml = '<RateV4Request USERID="'.$this->user_id.'">';
        $xml .= '<Revision/>';
	$index = 1;
	foreach($packages as $package)
	{
                $xml .= '<Package ID="'.$index.'">';
		$xml .= '<Service>'.strtoupper($package['service']).'</Service>';
                if (strtoupper($package['service']) == "FIRST CLASS"){
                    $xml .= '<FirstClassMailType>'.(!empty($package['first_class_type']) ? strtoupper($package['first_class_type']) : 'PACKAGE SERVICE').'</FirstClassMailType>';
                }
		$xml .= '<ZipOrigination>'.$package['zip_origination'].'</ZipOrigination>';
                $xml .= '<ZipDestination>'.$package['zip_destination'].'</ZipDestination>';
                $xml .= '<Pounds>'.(!empty($package['pounds']) ? $package['pounds'] : '0').'</Pounds>';
                $xml .= '<Ounces>'.(!empty($package['ounces']) ? $package['ounces'] : '0').'</Ounces>';
                $xml .= '<Container>'.strtoupper($package['container']).'</Container>';
                $xml .= '<Size>'.strtoupper($package['size']).'</Size>';
                
                if (!empty($package['width']) && $package['width'] != 0){
                    $xml .= '<Width>'.$package['width'].'</Width>';
                }
                if (!empty($package['length']) && $package['length'] != 0){
                    $xml .= '<Length>'.$package['length'].'</Length>';
                }
                if (!empty($package['height']) && $package['height'] != 0){
                    $xml .= '<Height>'.$package['height'].'</Height>';
                }
                
                $xml .= '</Package>';
		$index++;
	}

	$xml .= '</RateV4Request>';
	return $this->_request('RateV4',$xml);
  }
  
  
 /**
   * Shipping Labels
   * @desc Gets PDF generated shipping label and tracking information
   * @docs https://www.usps.com/business/web-tools-apis/usps-tracking-v3-3.htm
   * @parameters $from (array), $to (array), $weight_in_ounces, $service_type, $dimensions (array), $container, $size, $insured_amount
   * $from = array(
   *            'from_name' => 'John Smith',
   *            'from_firm' => 'ABC Inc.',
   *            'from_address1' => '123 Main St.',
   *            'from_address2' => 'Suite 100',
   *            'from_city' => 'Anytown',
   *            'from_state' => 'PA',
   *            'from_zip5' => '12345');
   * 
   *  $to = array(
   *            'to_name' => 'Mike Smith',
   *            'to_firm' => 'XYZ Inc.',
   *            'to_address1' => '456 2nd St.',
   *            'to_address2' => 'Apt B',
   *            'to_city' => 'Othertown',
   *            'to_state' => 'NY',
   *            'to_zip5' => '67890');
   * 
   * $dimensions = array(
   *            'width' => 5.5,
   *            'length' => 11,
   *            'height' => 11,
   *            'girth' => 11);
   * 
   * $service_type can be Priority, First Class, Standard Post, Medial Mail or Library Mail
   * $container can be VARIABLE, RECTANGULAR, NONRECTANGULAR, FLAT RATE ENVELOPE, FLAT RATE BOX, etc.
   * $dimensions array is REQUIRED when SIZE == LARGE
   * @access public
   * @return simple xml object - the <DeliveryConfirmationLabel> node of the response XML is a base64 binary image (the label)
   */
   function get_shipping_label($from = array(), $to = array(), $weight_in_ounces, $service_type, $dimensions = array(), $container = "VARIABLE", $size = "REGULAR", $insured_amount = 0)
  {
	$xml = '<DelivConfirmCertifyV4.0Request USERID="'.$this->user_id.'">';
	$xml .= '<Revision>2</Revision>';
	$xml .= '<ImageParameters />'; //Not yet implemented by this function
        
        /**
         * The FROM (origination) information
         */
        $xml .= '<FromName>'.$from['from_name'].'</FromName>';
        $xml .= '<FromFirm>'.isset($from['from_firm'])? $from['from_firm'] : ''.'</FromFirm>'; //can be blank
        //Address 1 and Address 2 are reverse from what is typically standard
        $xml .= '<FromAddress1>'.isset($from['from_address2']) ? $from['from_address2'] : ''.'</FromAddress1>';
	$xml .= '<FromAddress2>'.$from['from_address1'].'</FromAddress2>';
        $xml .= '<FromCity>'.$from['from_city'].'</FromCity>';
        $xml .= '<FromState>'.strtoupper($from['from_state']).'</FromState>';
        $xml .= '<FromZip5>'.$from['from_zip5'].'</FromZip5>';
        $xml .= '<FromZip4/>'; //Not yet implemented
        
        /**
         * The TO (destination) information
         */
        $xml .= '<ToName>'.$to['to_name'].'</ToName>';
        $xml .= '<ToFirm>'.isset($to['to_firm'])? $to['to_firm'] : ''.'</ToFirm>'; //can be blank
        //Address 1 and Address 2 are reverse from what is typically standard
        $xml .= '<ToAddress1>'.isset($to['to_address2']) ? $to['to_address2'] : ''.'</ToAddress1>';
	$xml .= '<ToAddress2>'.$to['to_address1'].'</ToAddress2>';
        $xml .= '<ToCity>'.$to['to_city'].'</ToCity>';
        $xml .= '<ToState>'.strtoupper($to['to_state']).'</ToState>';
        $xml .= '<ToZip5>'.$to['to_zip5'].'</ToZip5>';
        $xml .= '<ToZip4/>'; //Not yet implemented
        
        /**
         * Container Type, Weight and Size plus requested Service
         */
        $xml .= '<WeightInOunces>'.$weight_in_ounces.'</WeightInOunces>';
        $xml .= '<ServiceType>'.$service_type.'</ServiceType>';
        if ($insured_amount > 0) $xml .= '<InsuredAmount>'.$insured_amount.'</InsuredAmount>';
        $xml .= '<ImageType>PDF</ImageType>'; //Eventually could make this an option in the parameters - either PDF, TIF or GIF is accepted by API
        $xml .= '<Container>'.strtoupper($container).'</Container>';
        $xml .= '<Size>'.strtoupper($size).'</Size>';
        
        if (!empty($dimensions) || strtoupper($size) == "LARGE"){
            //just in case $dimensions is not set and SIZE is LARGE, some default dimensions are provided to prevent failure
            $xml .= '<Width>'.isset($dimensions['width']) ? $dimensions['width'] : '12'.'</Width>';
            $xml .= '<Length>'.isset($dimensions['length']) ? $dimensions['width'] : '12'.'</Length>';
            $xml .= '<Height>'.isset($dimensions['height']) ? $dimensions['height'] : '12'.'</Height>';
            $xml .= '<Girth>'.isset($dimensions['girth']) ? $dimensions['girth'] : '60'.'</Girth>';
        }
        
	$xml .= '</DelivConfirmCertifyV4.0Request>';
	
	return $this->_request('DelivConfirmCertifyV4Request',$xml);
  }
  
  
   /**
   * Request
   * @desc: Make a request to the USPS API
   * @access:  private
   * @return:  object
   */
  function _request($function,$xml)
  {
	$protocol = (($this->secure AND !$this->test) ? 'https' : 'http');
	$host = (($this->secure AND !$this->test) ? $this->secure_host : $this->host);
	$api = ($this->test ? $this->test_api : $this->prod_api);
    	$ch = curl_init($protocol. '://'. $host .'/'. $api .'/?API='. $function .'&XML='.urlencode($xml));
    	curl_setopt($ch, CURLOPT_HEADER, false);
    	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    	$output = curl_exec($ch);
    	curl_close($ch);
    
	print_r($protocol. '://'. $host .'/'. $api .'/?API='. $function .'&XML='.urlencode($xml));
	print_r($output);
	
	return new SimpleXMLElement($output);
  }
  
  
  
}

/* End of file USPS.php */
/* Location: ./system/application/libraries/USPS.php */
