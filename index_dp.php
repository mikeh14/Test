<?php
	set_time_limit(0);
	include_once('form_fields.php');
	include_once('webrequest.php');
	include_once('DonorPerfect.php');

	error_reporting(E_ALL);

	$donor_params = array(
			'donor_id' => '0',
			'first_name' => 'Mike',
			'last_name' => 'Henderson',
			'middle_name' => 'P',
			'suffix' => '',
			'title' => '',
			'salutation' => '',
			'prof_title' => 'Tester',
			'opt_line' => '',
			'address' => '1234 Test St',
			'address2' => '#2',
			'city' => 'Bend',
			'state' => 'OR',
			'zip' => '97701',
			'country' => 'US',
			'address_type' => '',
			'home_phone' => '1234567890',
			'business_phone' => '',
			'fax_phone' => '',
			'mobile_phone' => '',
			'email' => 'mike@zurigroup.com',
			'org_rec' => '',
			'donor_type' => '',
			'nomail' => '',
			'nomail_reason' => '',
			'narrative' => '',
			'user_id' => ''
	);


	//$saveTestDonor = DonorPerfect::saveDonor($donor_params);
	//echo $saveTestDonor;
	//exit;

	$donors = DonorPerfect::listDonors();
	print_r($donors);
	exit;

	$req = new WebRequest('test');

	$donation_form = "https://secure2.convio.net/zuri/site/Donation2;jsessionid=5F21992D6F99D40AAC1D018131B64D1B.app260a?df_id=1480&1480.donation=form1";
	
		
	// downloading data from API
	
	$curl = curl_init();
	// Set options
	curl_setopt($curl, CURLOPT_VERBOSE, 1);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); 
	curl_setopt($curl, CURLOPT_SSLVERSION, 'TLSv1_2');
	curl_setopt($curl, CURLOPT_URL, 'https://fox-review.mobilecause.com/api/v2/reports/transactions.json?status=collected,pending&transaction_type=sale,credit&amount_min=10.00');
	curl_setopt($curl, CURLOPT_HTTPHEADER, array('Authorization: Token token="hNikiWB7yvg9sYyLFPvB"', 'Accept: application/json', 'Content-type: application/json'));
	$resp = curl_exec($curl);

	// Send the request & save response to $resp
	$response = json_decode($resp);

	//echo $resp;
	// wait while report is building
	while (true) {
	
		sleep(2);

		curl_setopt($curl, CURLOPT_URL, 'https://fox-review.mobilecause.com/api/v2/reports/results.json?id='.$response->id);		
		$data = curl_exec($curl);
		
		//echo $data;
		if(!empty($data)){
			break;	
		}
	
	}
	

	
	curl_close($curl);

	$data = json_decode($data, true);
	print_r($data);
	exit;	
	echo count($data)." records found \n";
	
	//print_r($data);	
	//exit();

	// Pusing data to convio
	foreach ($data as $record) {

		$form = array_slice($form_fields,0);
	
		foreach ($record as $key => $value){
			
			if(isset($fieldmap[$key])){
				$value = str_replace('$','', $value);			
				$form[$fieldmap[$key]] = urlencode(trim($value));			
			}	
		}
		
		// set dates
		$donation = strtotime($record['donation_date']);
			
		$form['donation_date_date_DAY'] = date('j',$donation);
		$form['donation_date_date_MONTH'] = date('n',$donation);
		$form['donation_date_date_YEAR'] = date('Y',$donation);
		
		$trnasction = strtotime($record['transaction_date']);
		
		$form['transaction_date_date_DAY'] = date('j', $trnasction);
		$form['transaction_date_date_MONTH'] = date('n', $trnasction);
		$form['transaction_date_date_YEAR'] = date('Y', $trnasction);
		
		// credit card exp date
		$exp = strtotime("+1 month");
		$form['responsive_payment_typecc_exp_date_DAY'] = date('j', $exp);
		$form['responsive_payment_typecc_exp_date_MONTH'] = date('n', $exp);
		$form['responsive_payment_typecc_exp_date_YEAR'] = date('Y', $exp);
		
		$str = "";	
		
		
		
		$req->clear();
		$page = $req->get($donation_form);
		
		// extract idb value
		$parts = explode('id="idb" value="', $page);
		$parts = explode('"',$parts[1]);
		
		$form['idb'] = $parts[0];
		
		foreach ($form as $field => $value){
			$str .= $field."=".$value."&";	
		}
		//echo $parts[0];
		
		$headers = array('Host:secure2.convio.net','Referer:'.$donation_form);
		
		$res = $req->Post('https://secure2.convio.net/zuri/site/Donation2', $str, $headers);
		
		$amount =  str_replace('$','', $record['collected_amount']);
		
		$post = '1480.donation=form2&company_min_matching_amt=&currency_locale=en_US&df_id=1480&idb='.$parts[0].'&pstep_finish=Process&user_donation_amt=%24'.$amount;
		
		$res = $req->post('https://secure2.convio.net/zuri/site/Donation2',$post);
		
		echo $record['first_name']." ".$record['last_name']." (".$record['email'].") ".'$'.$amount." Added\n";
		
	}
	
	

?>