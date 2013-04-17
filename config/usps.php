<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| USPS USER ID
| -------------------------------------------------------------------------
| The User ID for your oganization provided by USPS when you registered
| to use the WebTools API.
|
*/
$config['user_id'] = '';

/*
| -------------------------------------------------------------------------
| USPS TEST API
| -------------------------------------------------------------------------
| Set it TRUE to use the USPS test API and FALSE to use the USPS production API.
| HTTPS is disabled while using the test server.
|
*/
$config['test'] = TRUE;

/*
| -------------------------------------------------------------------------
| USE HTTPS CONNECTION
| -------------------------------------------------------------------------
| Will use the secure HTTPS protical when using the production API. Recomeneded to set to TRUE.
|
*/
$config['secure'] 		= TRUE;

/*
| -------------------------------------------------------------------------
| USPS HOST
| -------------------------------------------------------------------------
| Overwrite these only if you need to change them from the defaults
|
*/
$config['host'] 		= 'production.shippingapis.com';
$config['secure_host'] 	= 'secure.shippingapis.com';

/* End of file usps.php */
/* Location: ./system/application/config/usps.php */
