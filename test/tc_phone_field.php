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

		$field = $form->get_field("phone");
		$this->assertEquals('<input required="required" type="text" name="phone" class="text form-control" id="id_phone" value="+420 " />',$field->as_widget());

		$field = $form->get_field("phone2");
		$this->assertEquals('<input type="text" name="phone2" class="text form-control" id="id_phone2" value="421 " />',$field->as_widget());

		$field = $form->get_field("phone3");
		$this->assertEquals('<input required="required" type="text" name="phone3" class="text form-control" id="id_phone3" />',$field->as_widget());
	}

	function test_format_initial_data(){
		$field = new PhoneField();

		$this->assertEquals("+420 605 123 456",$field->format_initial_data("+420.605123456"));
		$this->assertEquals("+420 605 123 456 78",$field->format_initial_data("+420.60512345678"));
		$this->assertEquals("+420",$field->format_initial_data("+420"));
		$this->assertEquals("+420.",$field->format_initial_data("+420."));
		$this->assertEquals("+420 1",$field->format_initial_data("+420.1"));
		$this->assertEquals("+1345 123 456",$field->format_initial_data("+1345.123456"));
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
			"+420" => null,
			"420" => null,
			"421" => null,
		]);

		// Invalid values

		$this->field = new PhoneField(array("default_country_code" => "+420"));

		$err = $this->assertInvalid("");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("+420");
		$this->assertEquals("Enter phone number",$err);

		$err = $this->assertInvalid("xx");
		$this->assertEquals("Enter valid phone number (+420 605 123 456)",$err);
	}

	function _testValidValues($ary){
		foreach($ary as $input => $phone){
			$cleaned_phone = $this->assertValid($input);
			$this->assertTrue($phone === $cleaned_phone);
		}
	}
}
