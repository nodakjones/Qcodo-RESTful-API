<?php

include ('_ResourceBase.class.php');

class ContactResource extends _ResourceBase
{

 	function __construct()
 	{
 		parent::__construct();	
 			
  	}
 	
 	function getMembership($objData,$arrVariables = null)
 	{
 		return $objData->GetMembershipArray();
 	}
 	
 	function getAddress($objData,$arrVariables = null)
 	{
 		return $objData->GetAddressArray();
 	}

	
}