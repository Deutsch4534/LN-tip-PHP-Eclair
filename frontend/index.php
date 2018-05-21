<?php
/*
///////////////////////////////////////////////////////////////
CREDIT
 This extends the excellent work done by michael1011 at 
    https://github.com/michael1011/lightningtip
 and another excellent work done by robclark56 at
 	https://github.com/robclark56/lightningtip-PHP
///////////////////////////////////////////////////////////////
	
lightningTip.php
	Companion file for lightningTip.js

FUNCTIONS
If called with 
	* no POST parameters:
		Displays the lightningTip HTML page
	* with POST parameters
		Performs backend functions. I.e. querying the Eclair instance and passing results back to lightningTip.js
   
SYNTAX:  
	https://your.domain/lightningTip.php
		Displays lightningTip HTML
			   
	https://your.domain/lightningTip.php
		Method: POST
		Parameters: 
			Action    = 'getinvoice'
			Amount    = xxxxx (satoshis)
			Message   = 'Some Text'
		Returns: JSON {"Invoice":"xxxx","Expiry":"yyyy"}
			xxxx = LN Payment request
			yyyy = expiry seconds
						
	https://your.domain/lightningTip.php
		Method: POST
		Parameters: 
			Action     = 'invoicesettled'
			Invoice = LN Payment request
		Returns: JSON <Invoice>			  
*/

///////// CHANGE ME ////////
// These constants should be identical to values found in eclair.conf
define('ECLAIR_PASS', 'pass');
define('ECLAIR_IP', '127.0.0.1');
define('ECLAIR_PORT', '8080');
define('EXPIRY', '3600');
///////// END CHANGE ME ////////


function getPaymentRequest($description='', $satoshi=0, $expiry=EXPIRY) {

	$eclair_ip = ECLAIR_IP;
	$eclair_port = ECLAIR_PORT;
	$eclair_pass = ECLAIR_PASS;
	 
	$data = json_encode(
	 	array(
	 		"method" => "receive",
			"params" => array($satoshi * 1000, $description)
		),
		JSON_NUMERIC_CHECK
	);

	$ch = curl_init("http://$eclair_ip:$eclair_port/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, "eclair:$eclair_pass");
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response);
}

function lookupInvoice($r_hash_str){

	$eclair_ip = ECLAIR_IP;
	$eclair_port = ECLAIR_PORT;
	$eclair_pass = ECLAIR_PASS;

	$data = json_encode(
	 	array(
	 		"method" => "checkpayment",
			"params" => array($r_hash_str)
		)
	);
					 
	$ch = curl_init("http://$eclair_ip:$eclair_port/");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_USERPWD, "eclair:$eclair_pass");
	$response = curl_exec($ch);
	curl_close($ch);
	return json_decode($response);
}

switch( $_POST['Action'] ) {

	case 'getinvoice':
		$PR = getPaymentRequest($_POST['Message'], $_POST['Amount'], EXPIRY);
		
		if ($PR->result != null) {
			echo json_encode(
				array(
					'Invoice' => $PR->result,
					'Expiry' => EXPIRY
				)
			);
		}
		
	exit;

	case 'invoicesettled':
		$Invoice = lookupInvoice($_POST['Invoice']);
		echo json_encode($Invoice);
		exit;

	default:
		// fall through to displaying the HTML
}
?>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="lightningTip.css">
    <script async defer src="https://cdn.rawgit.com/kazuhikoarase/qrcode-generator/886a247e/js/qrcode.js"></script>
    <script async defer src="lightningTip.js"></script>
    <title>⚡ Lightning Tip</title>
</head>

<div id="lightningTip">
	<p id="lightningTipLogo">
		<img src="logo.png">
	</p>

    <a>Send a tip via Lightning</a>

    <div id="lightningTipInputs">
        <input type="number" class="lightningTipInput" id="lightningTipAmount" placeholder="Amount in Satoshi">
        <br>
        <div class="lightningTipInput" id="lightningTipMessage" placeholder="A message you want to add" oninput="divRestorePlaceholder(this)" onblur="divRestorePlaceholder(this)" contenteditable></div>

        <button class="lightningTipButton" id="lightningTipGetInvoice" onclick="getInvoice()">Get request</button>

        <div>
            <a id="lightningTipError"></a>
        </div>

    </div>

</div>
