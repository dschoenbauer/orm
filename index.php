<?php

include './vendor/autoload.php';

use DSchoenbauer\Orm\Framework\XmlToArrayParser;
/* 
 * The MIT License
 *
 * Copyright 2017 David Schoenbauer.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

$url = "http://www.abstractsonline.com/WS/OASISWS.asmx?wsdl";
$uri = "CTT.OASIS.WS/OasisWSGetReport2";

$name_space = "CTT.OASIS.WS";
$username = "solson@coetruman.com";
$password = "wapiti_45";
$method = "GetReport2";
$meetingKey = "58C700DF-810C-4036-8E3C-590E0CE4B960";
$reportName = "Session Report";


$DomDoc = new DOMDocument('1.0', 'utf-8');
$DomDoc->preserveWhiteSpace = false;
$DomDoc->formatOutput = true;
$CurrentElement = $DomDoc->createElement("soap:Envelope");
$CurrentElement->setAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
$CurrentElement->setAttribute("xmlns:xsd","http://www.w3.org/2001/XMLSchema");
$CurrentElement->setAttribute("xmlns:soap","http://schemas.xmlsoap.org/soap/envelope/");
$OasisNode = $DomDoc->appendChild($CurrentElement);
$AddNode = $OasisNode ->appendChild($DomDoc->createElement("soap:Header"));
$mNode = $AddNode ->appendChild($DomDoc->createElement("AuthenticationHeader"));
$mNode->setAttribute("xmlns",$name_space);
$AuthNode = $mNode ->appendChild($DomDoc->createElement("SecurityTokenKey",$username));
$AuthNode = $mNode ->appendChild($DomDoc->createElement("SecurityTokenValue",$password));

$BodyNode = $OasisNode ->appendChild($DomDoc->createElement("soap:Body"));
$mNode = $BodyNode ->appendChild($DomDoc->createElement("OasisWSGetReport2"));
$mNode->setAttribute("xmlns",$name_space);
$AuthNode = $mNode ->appendChild($DomDoc->createElement("meetingKey",$meetingKey));
$AuthNode = $mNode ->appendChild($DomDoc->createElement("reportName",$reportName));
$request = $DomDoc->saveXML();
echo "<pre>";
print htmlentities($request);
echo "</pre>";

$client_array = array(
    'trace'=>true,
    'encoding'=>'UTF-8',
    'exceptions'=>true
);

$client = new SoapClient($url,$client_array);

$authReqParams = array('meetingKey'=>$meetingKey, 'reportName' => $reportName);
$responseHeaders = '';

try{
	//run the soap call to get it - with the headers.
	$response = $client->__doRequest($request,$url, $uri,null);
} catch(SoapFault $exception) {
	throw $exception;
}
//var_dump($response);
$domObj = new XmlToArrayParser();
$domArr = $domObj->convert(/*$response*/ $request);

if($domObj->parse_error){
    echo $domObj->get_xml_error();
}else{
    print "<pre>";

    print_r($domArr);
    print "</pre>";
}
