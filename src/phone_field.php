<?php
class PhoneField extends RegexField{
	function __construct($options = array()){
		$options = array_merge(array(
			"error_message" => _("Enter valid phone number (+420.605123456)"),
			"null_empty_output" => true,
			"help_text" => _("Enter phone number in format +420.605123456"),
		),$options);
		// TODO: jsou cisla, ktera zacinaji nulou: +044.1425838079
		parent::__construct('/^\+[1-9][0-9]{0,3}\.[0-9]{6,12}$/',$options);
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

		$value = preg_replace('/[\s-]/','',$value);
		if(preg_match('/^[0-9]{9}$/',$value)){
			$value = "+420.$value";
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
