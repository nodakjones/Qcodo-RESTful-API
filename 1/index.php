<?php

// SET PREPEND AND CREATE REST UTILITIES CLASS
require ('../../../share/qcodo/includes/prepend.inc.php');
include ('utilities/RestUtils.class.php');

$objREST = RestUtils::processRequest();

// IP AUTHENTICATION
/*
$objEntity								= QApplication::GetEntity();
$arrIP									= $objEntity->GetEntityIPArray();

if (!empty($arrIP))
{
	$strIncomingIP						= $_SERVER['REMOTE_ADDR'];
	$blnDenied							= true;
	foreach($arrIP as $objIP)
	{	
		if($strIncomingIP == $objIP->Ip)
			$blnDenied					= false;
	}
	
	if ($blnDenied)
		RestUtils::sendResponse(	'ERROR:  The IP address this request is coming from is not authorized',401);
}
else
	RestUtils::sendResponse(			'ERROR:  IP security has not been set for this entity',401);
*/
// GET EXTRA PATH INFO FOR ROUTING
$arrExtraPath							= QApplication::GetExtraPathInfo();

if (QApplication::PathInfo(0) != '')
{
	// IS SPECIFIED RESOURCE VALID
	$strResourceName					= ucwords(QApplication::PathInfo(0));
	$strResourceClass					= $strResourceName . 'Resource';
	$strResourceFile					= 'resources/' . $strResourceClass . '.class.php';
	
	if (file_exists($strResourceFile))
		include($strResourceFile);
	else
		RestUtils::sendResponse(		'ERROR:  This resource was not found',404);


	// IS VALID RESOURCE ID
	if (QApplication::PathInfo(1) != '')
		$intResourceID					= QApplication::PathInfo(1);
	else
		$intResourceID					= null;
		
	
	// DOES SECONDARY RESOURCE EXIST
	if (QApplication::PathInfo(2) != '')
		$strSecondResource				= ucwords(QApplication::PathInfo(2));
	else
		$strSecondResource				= null;
}
else
	RestUtils::sendResponse(			'ERROR:  This resource was not found',404);


switch($objREST->getMethod())
{
	// GET
	case 'get':
		
		// SET VARIABLES
		$arrVariables					= $_GET;
		
		// BASED ON ROUTING INFO, CALL ORM FUNCTION
		$objResource					= new $strResourceClass();
		$mixData						= $objResource->getData($strResourceName,$intResourceID,$strSecondResource,$arrVariables);
		
		if ($mixData[0])
			RestUtils::sendResponse(	$mixData[0],404);
			
		// IF NO RESULTS, RETURN 404
		if (is_null($mixData[1]))
			RestUtils::sendResponse(	'ERROR:   No resource data found',404);

		// FORMAT RESULTS TO JSON
		$strJson						= RestUtils::getJson($mixData[1]);
		
		// SEND RESULT			
		RestUtils::sendResponse(		$strJson,200);

		break;
	
	// POST
	case 'post':
	
		// SET VARIABLES
		$arrVariables					= $_POST; 
		
		// BASED ON ROUTING INFO, CALL ORM FUNCTION
		$objResource					= new $strResourceClass();
		$txtResponse					= $objResource->postData($strResourceName,$intResourceID,$arrVariables);
		
		// SEND RESPONSE
		if ($txtResponse[0])
			RestUtils::sendResponse($txtResponse[1],500);
		else
			RestUtils::sendResponse($txtResponse[1],201);
			
		break;
		
	// PUT
	case 'put':
	
		break;
	
	// DELETE
	case 'delete':
	
		break;
}


?>