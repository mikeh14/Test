# Test

When running index_test.php, the API URL it hits is as follows:

?apikey=bO8v9hfPq+aLq/R44wWTOCx0AaLnQ5Z0+QThiDQAarXsFnVsmllXXziDPJuVDWIBSKlME7lkvHRosbVapfUf2kv/8YTueL/F+eFd9EEU2D+ddWEoEMHNCT4h9MzOSYC9&action=SELECT%20%2A%20FROM%20%28%20SELECT%20ROW_NUMBER%28%29%20OVER%28ORDER%20BY%20dp.first_name%2C%20dp.middle_name%2C%20dp.last_name%20ASC%29%20AS%20row_number%2C%20%2F%2AROW_NUMBER%28%29%20OVER%28ORDER%20BY%20dp.donor_id%20ASC%29%20AS%20row_number%2C%2A%2F%20dp.donor_id%2C%20dp.first_name%2C%20dp.middle_name%2C%20dp.last_name%2C%20dp.email%2C%20dp.address%2C%20dp.address2%2C%20dp.city%2C%20dp.state%2C%20dp.zip%2C%20dp.country%2C%20dp.gift_total%2C%20dpudf.re_constituent_id%20FROM%20dp%20LEFT%20JOIN%20dpudf%20ON%20dpudf.donor_id%20%3D%20dp.donor_id%20WHERE%20%28dp.nomail_reason%20%21%3D%20%27IA%27%20AND%20dp.nomail_reason%20%21%3D%20%27DE%27%29%20OR%20dp.nomail_reason%20IS%20NULL%20%2F%2AORDER%20BY%20dp.first_name%2C%20dp.middle_name%2Cdp.last_name%20ASC%2A%2F%20%29%20AS%20tmp%20WHERE%20tmp.row_number%20BETWEEN%201%20AND%20500


I receive the following response back:
<result>
  <field name="success" id="success" reason="invalid: Array cannot be null.rnParameter name: bytes" value="false"/>
</result>



I also tried a saveDonor this way, but receive the same invalid error:

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
			'email' => 'ninetysix@gmail.com',
			'org_rec' => '',
			'donor_type' => '',
			'nomail' => '',
			'nomail_reason' => '',
			'narrative' => '',
			'user_id' => ''
	);
  $saveTestDonor = DonorPerfect::saveDonor($donor_params);
