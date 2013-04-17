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