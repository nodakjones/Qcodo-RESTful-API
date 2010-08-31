<?php

// SPECIFY PATH TO API FILES AND PRIMARY RESOURCE
$url 				= "http://www.didev.us/share/api/1.1/index.php/group";

// SPECIFY SOME DATA
$strPost			= 'Name=NewGroup1&ParentGroupID=0';

// SETUP CURL
$curl 				= curl_init();
 
curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl, CURLOPT_POST      ,1);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS    ,$strPost);
curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
curl_setopt($curl, CURLOPT_URL, $url);
 
// EXECUTE CURL AND PRINT RESULTS
$result = curl_exec($curl);
echo('<pre>');
print_r($result,false);
echo('</pre>');
exit;

?>