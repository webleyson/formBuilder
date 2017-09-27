<?php 
include('include/layout/header.php');
include('include/form.class.php');
?>
<!doctype html>
	<html lang="en">
	<head>

	</head>
	<body>
		<?php 
			$form = new Form(79);
		?>
		<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
	</body>
</html>


<?php

// echo "<hr />";echo "<hr />";


// $form = new Form('send.php', 1);
// $userField = new TextFormElement("What is your name?");
// $userField->addAttribute('name', 'username');
// $userField->addAttribute('placeholder', 'Username');

// $dropDown =	new DropDownElement("Choose a veg",  "MyDropdown", "dropdownID");
// $dropDown->addAttribute('1', 'potato');
// $dropDown->addAttribute('2', 'carrot');
// $dropDown->addAttribute('sausage', 'sausage');

// $textArea = new TextArea("Tell us a little more about yourself");
// $textArea->addAttribute('name', 'message');

// $submitButton = new SubmitFormElement();
// $submitButton->addAttribute('name', 'submit');
// $submitButton->addAttribute('value', 'Send!');

// $form->addElement($userField);
// $form->addElement($dropDown);
// $form->addElement($textArea);
// $form->addElement($submitButton);
// echo $form->build();

?>
