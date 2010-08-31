<?php

abstract class _ResourceBase
{

	function __construct()
	{
	
	}
	
 	function getData($strResource = null,$intResourceID = null,$strSecondResource = null,$arrVariables = null)
 	{
 		// SET VARIABLES
 		$strError								= null;
 		$objData								= null;
 		
 		// GET PRIMARY RESOURCE...IF WE HAVE A SECONDARY RESOURCE, VARIABLES DON'T GET PASSED TO THE PRIMARY
 		if ($strSecondResource)
 			$objPrimaryData						= $this->getPrimaryResource($strResource,$intResourceID,null);
 		else
 			$objPrimaryData						= $this->getPrimaryResource($strResource,$intResourceID,$arrVariables);
 		 		
 		// SET PRIMARY RESOURCE
 		if (is_object($objPrimaryData) || !empty($objPrimaryData))
 			$objData							= $objPrimaryData;
 		else
 			return Array('ERROR:  This resource was not found',$objData);
 		
 		// GET SECONDARY RESOURCE IF NEEDED
 		if ($strSecondResource)
 		{
 			$strSecondResource					= 'get' . $strSecondResource;
 			
 			// DO WE HAVE A METHOD FOR THIS RESOURCE...REMEMBER THIS IS AN ABSTRACT CLASS
 			if (method_exists($this,$strSecondResource))
 			{
 				$objSecondaryResource			= $this->$strSecondResource($objData,$arrVariables);
 				
 				if (is_object($objSecondaryResource) || !empty($objSecondaryResource))
 					$objData					= $objSecondaryResource;
 				else
 					$strError					= 'ERROR:  No data was found on the secondary resource';
 			}
 			else
 				$strError						= 'ERROR:  The secondary resource was not found';
 		}
 		
 		// RETURN ERROR MESSAGE AND DATA
 		return Array($strError,$objData);
 	}
 	
 	function getPrimaryResource($strResourceName,$intResourceID,$arrVariables)
 	{
 		if ($intResourceID) 
 			$objPrimaryData 				= call_user_func(Array(ucwords($strResourceName),'Load'),$intResourceID);
 		else
 		{
 			// IF VARIABLES PASSED, USE IN QUERY
	 		if (!empty($arrVariables))
	 		{
				$arrSearch					= Array();
	 			
	 			// LOOP THROUGH VARIABLES
	 			foreach($arrVariables as $strField=>$strValue)
	 			{
	 				// IF NOT _VARIABLE
	 				if (substr($strField,0,1) != '_')
	 				{	
	 					// ADD VARIABLE TO SEARCH ARRAY;
	 					$objQQN				= call_user_func(Array('QQN',$strResourceName));
	 					$objQQNode			= $objQQN->{ucwords($strField)};
	 					$arrSearch[] 		= QQ::Equal($objQQNode,$strValue);
	 				}
	 			}	
	 			
	 			if (!empty($arrSearch))
	 				$objPrimaryData			= call_user_func(Array($strResourceName,'QueryArray'),call_user_func(Array('QQ','AndCondition'),$arrSearch));
	 			else
	 				$objPrimaryData			= call_user_func(Array($strResourceName,'LoadAll'));
	 			
			}
			
	 		// NO VARIABLES
	 		else
	 			$objPrimaryData	 			= call_user_func(Array($strResourceName,'LoadAll'));
	 	
	 	}
	 	
	 	return $objPrimaryData;
 	}

	function postData($strResourceName,$intResourceID,$arrVariables)
	{
		$objData							= null;
		$strStatus							= null;
		$blnError							= 0;
		
		// IF WE HAVE A RESOURCE ID, ATTEMPT TO FIND OBJECT
		if ($intResourceID) 
 			$objData 						= call_user_func(Array(ucwords($strResourceName),'Load'),$intResourceID);
 		
 		// DIDN'T FIND RESOURCE, ASSUME WE NEED TO CREATE IT 		
 		if (!is_object($objData))
 		{
 			$objData						= new $strResourceName();
 			$strStatus						= 'Created';
 		}
 		else
 			$strStatus						= 'Updated';
 		
 		// UPDATE RESOURCE RECORD
 		foreach($arrVariables as $strField=>$strValue)
		{
			// FITLER OUT VARIABLE STARTING WITH _
			if (substr($strField,0,1) != '_')
			{
				try { 
					$objData->$strField	= $strValue;
				} 
				
				catch (QCallerException $objExc) {
					$blnError				= 1;
					$strMessage 			= 'ERROR: Field name ' . $strField . ' is invalid';
				}
									
			}
		}
 		
 			try { 
				 // SAVE
 				$objData->Save();

			} 
			
			catch (QCallerException $objExc) {
				$blnError				= 1;
				$strMessage 			= 'ERROR: ' . $objExc;
			}

 			
 		if (!$blnError)
 			$strMessage					= 'Resource ' . $objData->{$strResourceName . 'ID'} . ' ' . $strStatus;
 			
		// RETURN MESSAGE
 		return Array($blnError,$strMessage);
	}
	
}