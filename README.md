USPS Codeigniter Library
========================

A Codeigniter library which integrates with the USPS shipping and address verification APIs

Installation
------------
1. Unpack the contents into your system folder.
2. Edit the /application/config/usps.php and change the "user_id" to the user id supplied by USPS when you registered for your WebTools account.

***Note:*** *the Test API is enabled by default. Set "test" to FALSE to enable the production API*

Examples
--------

### Address Standardization ###
	$this->load->library('USPS');

	//CREATE AN ARRAY OF ADDRESSES (MAX 5)
	$addresses = array(
		'0' => array(
			'firm_name' => 'XYZ Company',
			'address1' => '1234 Fake St.',
			'address2' => 'Apt #1234',
			'city' => 'Testingville',
			'state' => 'AZ',
			'zip5' => '12345',
			'zip4' => '1234'
		),
		'1' => array(
			'firm_name' => 'ABC Company',
			'address1' => '1234 Real St.',
			'address2' => 'Suite #1234',
			'city' => 'Realville',
			'state' => 'AZ',
			'zip5' => '54321',
			'zip4' => '4321'
		)
	);

	//RUN ADDRESS STANDARDIZATION REQUEST
	$verified_address = $this->usps->address_standardization($addresses);

	//OUTPUT RESULTS
	print_r($verified_address);

### Zip Code Lookup ###
	$this->load->library('USPS');

	//CREATE AN ARRAY OF ADDRESSES (MAX 5)
	$addresses = array(
		'0' => array(
			'firm_name' => 'XYZ Company',
			'address1' => '1234 Fake St.',
			'address2' => 'Apt #1234',
			'city' => 'Testingville',
			'state' => 'AZ'
		),
		'1' => array(
			'firm_name' => 'ABC Company',
			'address1' => '1234 Real St.',
			'address2' => 'Suite #1234',
			'city' => 'Realville',
			'state' => 'AZ'
		)
	);

	//RUN ZIP CODE LOOKUP	
	$zip_code_lookup = $this->usps->zipcode_lookup($addresses);

	//OUTPUT RESULTS
	print_r($zip_code_lookup);

### City/State Lookup ###
	$this->load->library('USPS');

	//CREATE AN ARRAY OF ZIPCODES (MAX 5)
	$addresses = array(
		'0' => array(
			'zip5' => '12345',
		),
		'1' => array(
			'zip5' => '54321',
		)
	);

	//RUN CITY/STATE LOOKUP
	$city_state_lookup = $this->usps->city_state_lookup($addresses);

	//OUTPUT RESULTS
	print_r($city_state_lookup);

License
-------

View the [OSL-3.0](http://opensource.org/licenses/OSL-3.0)

Documentation
----------------------
View the [USPS API Documentation](https://www.usps.com/business/web-tools-apis/address-information-v3-1d.htm)