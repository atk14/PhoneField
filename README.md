PhoneField
==========

ATK14 form field for entering phone numbers.

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/phone-field dev-master

Optionaly you can link the field source file to the regular location

    cd path/to/your/atk14/project/
    ln -s ../../vendor/atk14/phone-field/src/phone_field.php app/fields/phone_field.php

Usage
-----

    <?php
    // file: app/forms/users/create_new_form.php
    class CreateNewForm extends ApplicationForm {

      function set_up(){
        // ...

        $this->add_field("phone", new PhoneField([
          "label" => "Phone",
          "default_country_code" => "CZ", // "CZ", "SK", "AT", "PL"... or "+420"
          // "sample_phone_number" => "+420 777 123 456",
          // "help_text" => "Enter phone number in format %sample_phone_number%"
        ]));

        // ...
      }
    }

License
-------

PhoneField is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

[//]: # ( vim: set ts=2 et: )
