<?php

include ('_ResourceBase.class.php');

class GroupResource extends _ResourceBase
{

 	function __construct($strResource = null,$intResourceID = null,$strSecondResource = null,$arrVariables = null)
 	{
 		parent::__construct($strResource,$intResourceID,$strSecondResource,$arrVariables);
 			 		
 			
  	}
 	
	function getContact($objData,$arrVariables = null)
	{
		return $objData->GetContactArray();
		
	}
	
	
}