<?php
defined("PHONE_FIELD_DEFAULT_COUNTRY_CODE") || define("PHONE_FIELD_DEFAULT_COUNTRY_CODE","CZ"); // "CZ", "+420"
defined("PHONE_FIELD_SAMPLE_PHONE_NUMBER") || define("PHONE_FIELD_SAMPLE_PHONE_NUMBER","");

class PhoneField extends RegexField{

	protected static $SAMPLE_PHONE_NUMBERS = [
		"AR" => "+54 9 XXX XXX XXXX",
		"AT" => "+43 1234 56789",
		"BE" => "+32 81 12 34 56",
		"BG" => "+359 2 123 4567",
		"CA" => "+1 604 555 5555",
		"CH" => "+41 61 XXX XXXX",
		"CN" => "+86 139 1099 8888",
		"CZ" => "+420 605 123 456",
		"DE" => "+49 69 1234 5678",
		"DK" => "+45 123 4567 9",
		"EE" => "+372 58 XXXXXX",
		"EG" => "+20 2 XXXXXXXX", // ???
		"GB" => "+44 7911 123456",
		"HR" => "+385 43 XXX XXXX",
		"HU" => "+36 52 123 4567",
		"IL" => "+972 558 5556 42",
		"IN" => "+91 123 4567 8910",
		"JP" => "+81 123 5678 9101",
		"KZ" => "+997 727 123 46",
		"LT" => "+370 XXXX XXXX",
		"LV" => "+371 6678 1234",
		"MA" => "+212 520 XXXXXX",
		"MD" => "+373 1234 5678",
		"MX" => "+52 55 1254 5678",
		"NO" => "+47 815 XX XXX",
		"NL" => "+31 20 123 4567",
		"NZ" => "+64 9 123 4567",
		"PE" => "+51 1234 567",
    "PL" => "+48 605 555 555",
		"RO" => "+40 0 XXX XXX XXX",
		"RS" => "+381 11 222 5522",
		"RU" => "+7 123 4567 901",
		"SI" => "+386 1 XXX XX XX",
		"SK" => "+421 905 123456",
		"SM" => "+378 XXXXXX",
		"TN" => "+216 98123456",
		"TR" => "+90 212 1234567",
		"UA" => "+380 XX XXX XX XX",
		"US" => "+1 123 345 6789",
		"ZA" => "+27 123 456 789",
	];

	function __construct($options = array()){
		$options += array(
			"error_messages" => array(),
			"help_text" => _("Enter phone number in format %sample_phone_number%"),
			"null_empty_output" => true,
			"default_country_code" => PHONE_FIELD_DEFAULT_COUNTRY_CODE, // "421" or "+420" or even "CZ", "SK", "AT"...
			"sample_phone_number" => PHONE_FIELD_SAMPLE_PHONE_NUMBER,
		);

		if(preg_match('/^[A-Z]{2}$/',$options["default_country_code"])){
			$country = $options["default_country_code"];
			if(!isset(self::$SAMPLE_PHONE_NUMBERS[$country])){
				trigger_error(sprintf('PhoneField: Sample phone number not set for country "%s", using "CZ"',$country));
				$country = "CZ";
			}
			$sample_phone_number = self::$SAMPLE_PHONE_NUMBERS[$country];
			!$options["sample_phone_number"] && ($options["sample_phone_number"] = $sample_phone_number);
			$options["default_country_code"] = preg_replace('/ .*/','',$sample_phone_number);
		}

		!$options["sample_phone_number"] && ($options["sample_phone_number"] = self::$SAMPLE_PHONE_NUMBERS["CZ"]);

		$options += array(
			"initial" => $options["default_country_code"] ? $options["default_country_code"]." " : "",
		);

		$options["error_messages"] += array(
			"invalid" => _("Enter valid phone number (%sample_phone_number%)"),
			"required" => _("Enter phone number"),
		);

		$this->default_country_code = $options["default_country_code"];
		unset($options["default_country_code"]);

		$sample_phone_number = $options["sample_phone_number"];
		unset($options["sample_phone_number"]);

		// TODO: jsou cisla, ktera zacinaji nulou: +044.1425838079
		parent::__construct('/^\+[1-9][0-9]{0,3}\.[0-9]{6,12}$/',$options);

		foreach($this->messages as $k => &$v){
			$v = str_replace("%sample_phone_number%",$sample_phone_number,$v);
		}
		$this->help_text = str_replace("%sample_phone_number%",$sample_phone_number,$this->help_text);
	}

	function format_initial_data($phone){
		if(preg_match('/^(\+\d+)\.(\d+)$/',(string)$phone,$matches)){
			$cc = $matches[1];
			$number = $matches[2];
			$number_ar = array();
			while(strlen($part = substr($number,0,3))){
				$number_ar[] = $part;
				$number = substr($number,strlen($part));
			}
			$number = join(" ",$number_ar);
			$phone = "$cc $number";
		}
		return $phone;
	}

	function clean($value){
		// cerpano odsud: http://countrycode.org/
		$country_phone_codes = array(
			'1', '1242', '1246', '1264', '1268', '1284', '1340', '1345', '1441', '1473', '1599', '1649', '1664', '1670', '1671', '1684', '1758', '1767', '1784', '1809', '1868',
			'1869', '1876', '20', '212', '213', '216', '218', '220', '221', '222', '223', '224', '225', '226', '227', '228', '229', '230', '231', '232', '233', '234', '235',
			'236', '237', '238', '239', '240', '241', '242', '243', '244', '245', '248', '249', '250', '251', '252', '253', '254', '255', '256', '257', '258', '260', '261',
			'262', '263', '264', '265', '266', '267', '268', '269', '27', '290', '291', '297', '298', '299', '30', '31', '32', '33', '34', '350', '351', '352', '353', '354',
			'355', '356', '357', '358', '359', '36', '370', '371', '372', '373', '374', '375', '376', '377', '378', '380', '381', '382', '385', '386', '387', '389', '39', '40',
			'41', '420', '421', '423', '43', '44', '45', '46', '47', '48', '49', '500', '501', '502', '503', '504', '505', '506', '507', '508', '509', '51', '52', '53', '54',
			'55', '56', '57', '58', '590', '591', '592', '593', '595', '597', '598', '599', '60', '61', '62', '63', '64', '65', '66', '670', '672', '673', '674', '675', '676',
			'677', '678', '679', '680', '681', '682', '683', '685', '686', '687', '688', '689', '690', '691', '692', '7', '81', '82', '84', '850', '852', '853', '855', '856',
			'86', '870', '880', '886', '90', '91', '92', '93', '94', '95', '960', '961', '962', '963', '964', '965', '966', '967', '968', '970', '971', '972', '973', '974',
			'975', '976', '977', '98', '992', '993', '994', '995', '996', '998'
		);

		$dcc = $this->default_country_code;
		$dcc = preg_replace('/^\+/','',$dcc); // "+420" -> "420"

		$value = preg_replace('/[\s-]/','',$value);
		$value = str_replace(html_entity_decode('&nbsp;'),'',$value); // removing non-breaking space

		// if the value is only a country code, it means that it is no number
		if(preg_match('/^\+?('.join('|',$country_phone_codes).')\.?$/',$value)){
			$value = "";
		}

		if(preg_match('/^[0-9]{9}$/',$value)){
			$value = "+$dcc.$value";
		}

		if(preg_match('/^\+?('.join('|',$country_phone_codes).')([0-9]{6,12})$/',$value,$matches)){
			$value = "+$matches[1].$matches[2]";
		}

		// +420605123456 -> +420.605123456
		if(preg_match('/^\+420([0-9]{9})$/',$value,$matches)){
			$value = "+420.$matches[1]";
		}
		return parent::clean($value);
	}
}
