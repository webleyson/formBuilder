<?php
include('include/form.class.php');

$form = new Form('send.php');
$userField = new TextFormElement("What is your name?");
$userField->addAttribute('name', 'username');
$userField->addAttribute('placeholder', 'Username');

$dropDown =	new DropDownElement("Choose a veg",  "MyDropdown", "dropdownID");
$dropDown->addAttribute('1', 'potato');
$dropDown->addAttribute('2', 'carrot');
$dropDown->addAttribute('sausage', 'sausage');

$textArea = new TextArea("Tell us a little more about yourself");
$textArea->addAttribute('name', 'message');

$submitButton = new SubmitFormElement();
$submitButton->addAttribute('name', 'submit');
$submitButton->addAttribute('value', 'Send!');

$form->addElement($userField);
$form->addElement($dropDown);
$form->addElement($textArea);
$form->addElement($submitButton);
echo $form->build();


