<?php
class TcPhoneField extends TcBase {

	function testWidget(){
		$form = new Atk14Form();
		$form->add_field("phone", new PhoneField());
		$form->add_field("phone2", new PhoneField(array(
			"default_country_code" => "421",
			"required" => false,
		)));
		$form->add_field("phone3", new PhoneField(array(
			"initial" => "",
		)));
		$form->add_field("phone3b", new PhoneField(array(
			"default_country_code" => "",
			"initial" => "",
		)));

		$form->add_field("phone_at", new PhoneField(array(
			"default_country_code" => "AT"
		)));

		$form->add_field("phone4", new PhoneField(array(
			"default_country_code" => "CZ",
			"initial" => "+421.6045123456",
		)));

		$field = $form->get_field("phone");
		$this->assertEquals('<input required="required" type="text" name="phone" class="text form-control" id="id_phone" value="+420 " />',$field->as_widget());

		$field = $form->get_field("phone2");
		$this->assertEquals('<input type="text" name="phone2" class="text form-control" id="id_phone2" value="421 " />',$field->as_widget());

		$field = $form->get_field("phone3");
		$this->assertEquals('<input required="required" type="text" name="phone3" class="text form-control" id="id_phone3" value="+420 " />',$field->as_widget());

		$field = $form->get_field("phone3b");
		$this->assertEquals('<input required="required" type="text" name="phone3b" class="text form-control" id="id_phone3b" />',$field->as_widget());

		$field = $form->get_field("phone_at");
		$this->assertEquals('<input required="required" type="text" name="phone_at" class="text form-control" id="id_phone_at" value="+43 " />',$field->as_widget());

		$field = $form->get_field("phone4");
		$this->assertEquals('<input required="required" type="text" name="phone4" class="text form-control" id="id_phone4" value="+421 604 512 345 6" />',$field->as_widget());
	}

	function test_format_initial_data(){
		$field = new PhoneField();

		$this->assertEquals("+420 605 123 456",$field->format_initial_data("+420.605123456"));
		$this->assertEquals("+420 605 123 456 78",$field->format_initial_data("+420.60512345678"));
		$this->assertEquals("+420",$field->format_initial_data("+420"));
		$this->assertEquals("+420 ",$field->format_initial_data(""));
		$this->assertEquals("+420.",$field->format_initial_data("+420."));
		$this->assertEquals("+420 1",$field->format_initial_data("+420.1"));
		$this->assertEquals("+1345 123 456",$field->format_initial_data("+1345.123456"));

		//

		$field = new PhoneField(array("default_country_code" => null));

		$this->assertEquals("+420 605 123 456",$field->format_initial_data("+420.605123456"));
		$this->assertEquals("+420",$field->format_initial_data("+420"));
		$this->assertEquals("",$field->format_initial_data(""));
	}

	function test(){
		// Help text

		$field = new PhoneField();
		$this->assertEquals("Enter phone number in format +420 605 123 456",$field->help_text);

		$field = new PhoneField(array("sample_phone_number" => "421 111 222 333"));
		$this->assertEquals("Enter phone number in format 421 111 222 333",$field->help_text);

		// Valid values

		$this->field = new PhoneField(array("default_country_code" => "+420"));
		$this->_testValidValues([
			"+420.605111222" => "+420.605111222",
			"+420605111223" => "+420.605111223",
			"420605111224" => "+420.605111224",
			"420 605 111 225" => "+420.605111225",

			"605 333 444" => "+420.605333444",
			"605333445" => "+420.605333445",

			"+421.605333444" => "+421.605333444",
			"+421 605 333 445" => "+421.605333445",
			"421605333446" => "+421.605333446",
		]);

		$this->field = new PhoneField(array("default_country_code" => "+421"));
		$this->_testValidValues([
			"+420.605111222" => "+420.605111222",
			"+420605111223" => "+420.605111223",
			"420605111224" => "+420.605111224",
			"420 605 111 225" => "+420.605111225",

			"605 333 444" => "+421.605333444",
			"605333445" => "+421.605333445",

			"+421.605333444" => "+421.605333444",
			"+421 605 333 445" => "+421.605333445",
			"421605333446" => "+421.605333446",
		]);

		// Nulls (valid values)

		$this->field = new PhoneField(array("default_country_code" => "+420", "required" => false));
		$this->_testValidValues([
			"" => null,
			" " => null,
			"420" => null,
			"420." => null,
			"+420" => null,
			"+420." => null,
			" + 420 . " => null,
			"420" => null,
			"421" => null,
		]);

		// Invalid values

		$this->field = new PhoneField(array("default_country_code" => "+420"));

		$err = $this->assertInvalid("");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("+420");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("+421");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("xx");
		$this->assertEquals("Enter valid phone number (+420 605 123 456)",$err);

		// Using ISO-2 country code in the default_country_code option

		$this->field = new PhoneField(array("default_country_code" => "SK"));

		$err = $this->assertInvalid("+420");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("+421");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("xx");
		$this->assertEquals("Enter valid phone number (+421 905 123456)",$err);
	}

	function test__get_sample_by_phone_number(){
		$pf = new PhoneField();

		$this->assertEquals(null,$pf->_get_sample_by_phone_number("9999999",$country,$prefix,$number));
		$this->assertEquals(null,$country);
		$this->assertEquals(null,$prefix);
		$this->assertEquals(null,$number);

		$this->assertEquals("+420 605 123 456",$pf->_get_sample_by_phone_number("+420.605111222",$country,$prefix,$number));
		$this->assertEquals("CZ",$country);
		$this->assertEquals("+420",$prefix);
		$this->assertEquals("605111222",$number);

		$this->assertEquals("+421 905 123456",$pf->_get_sample_by_phone_number("421605111333",$country,$prefix,$number));
		$this->assertEquals("SK",$country);
		$this->assertEquals("+421",$prefix);
		$this->assertEquals("605111333",$number);

		$this->assertEquals(null,$pf->_get_sample_by_phone_number("998123456",$country,$prefix,$number));
		$this->assertEquals(null,$country);
		$this->assertEquals("+998",$prefix);
		$this->assertEquals("123456",$number);
	}

	function test__format_by_sample(){
		$pf = new PhoneField();
		$this->assertEquals("+420 603 345 678",$pf->_format_by_sample("+420.603345678"));
		$this->assertEquals("+420 603 345 678",$pf->_format_by_sample("+420603345678"));
		$this->assertEquals("+420 603 345 678 999",$pf->_format_by_sample("+420.603345678999"));
		$this->assertEquals("+1 877 448 4820",$pf->_format_by_sample("+18774484820"));
		$this->assertEquals("+1 877 448 48",$pf->_format_by_sample("+187744848"));
		$this->assertEquals("+998 123456",$pf->_format_by_sample("+998123456")); // known prefix, no sample
		$this->assertEquals("+99999999",$pf->_format_by_sample("+99999999")); // uknown prefix
	}

	function _testValidValues($ary){
		foreach($ary as $input => $phone){
			$cleaned_phone = $this->assertValid($input);
			$this->assertTrue($phone === $cleaned_phone);
		}
	}
}
