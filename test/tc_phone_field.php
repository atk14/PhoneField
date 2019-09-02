<?php
class TcPhoneField extends TcBase {

	function test(){
		$this->field = new PhoneField(array("default_country_code" => "+420"));

		// Valid numbers
		foreach([
			"+420.605111222" => "+420.605111222",
			"+420605111223" => "+420.605111223",
			"420605111224" => "+420.605111224",
			"420 605 111 225" => "+420.605111225",

			"605 333 444" => "+420.605333444",
			"605333445" => "+420.605333445",

			"+421.605333444" => "+421.605333444",
			"+421 605 333 445" => "+421.605333445",
			"421605333446" => "+421.605333446",
		] as $input => $phone){
			$cleaned_phone = $this->assertValid($input);
			$this->assertEquals($phone,$cleaned_phone);
		}
	}
}
