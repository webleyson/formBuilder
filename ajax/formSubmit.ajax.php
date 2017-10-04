<?php
require_once('../include/generic_functions.php'); 
$do = isset($_POST['do']) ? $_POST['do'] : false;
switch($do){
	default:
        json_response("error", "Invalid Request");
        break;
    case 'formAnswers':
        saveAnswer();
        break;
}

function saveAnswer(){
	$data = isset($_POST['data']) ? $_POST['data'] : false;
	$arr = array();
	foreach ($data as $k => $v){

		if (!array_key_exists($v['name'], $arr)) {
        	$arr[$v['name']] = $v['value'];
    	}else{
          $arr[$v['name']] .=  ", " .$v['value'];
    	}
	}	

	foreach ($arr as $answer => $value){



		$id = substr($answer, strpos($answer, "_") + 1);
		$query = "INSERT INTO answers(answer, question_id) VALUES ('$answer', '$value')";

		$result = pg_query($query);
		$insert_row = pg_fetch_row($result);
		
	}
}




?>