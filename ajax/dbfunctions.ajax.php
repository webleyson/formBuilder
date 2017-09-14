<?php 
require_once('../include/connectDB.php'); 
require_once('../include/generic_functions.php'); 

$do = isset($_POST['do']) ? $_POST['do'] : false;

switch($do){
	default:
        json_response("error", "Invalid Request");
        break;
    case 'saveQuestion':
        saveQuestion();
        break;
    case 'saveTitle':
        saveTitle();
        break;
	case 'createAndGetId':
	    createAndGetId();
	   	break;
	case 'newFormSet':
	    newFormSet();
	   	break;
	case 'getExisting':
	    getExisting();
	   	break;
	case 'getAllSets':
		getAllQuestionSets();
}



function getExisting(){
	$setId = json_decode($_POST['question_set_id']);
	$query = "SELECT * FROM questions RIGHT JOIN nameids ON questions.question_set = nameids.question_set_id  WHERE nameids.question_set_id = '$setId'";

	$result = pg_query($query); 
	if ($result){
		$data = array();
		while ($row = pg_fetch_assoc($result)) {
		array_push($data, $row);
		}
	}else{
		json_response('ok', 'No sets exist');
	}	
	json_response('ok', 'Question set loaded', $data);
}


function getAllQuestionSets(){
	$query = "SELECT nameids.question_set_name, nameids.question_set_id, count(questions.question_set) FROM nameids LEFT JOIN questions ON nameids.question_set_id = questions.question_set GROUP BY nameids.question_set_name, nameids.question_set_id";

	$result = pg_query($query); 
	
	if ($result){
		$data = array();
		while ($row = pg_fetch_row($result)) {
		array_push($data, $row);
		}
	}else{
		json_response('ok', 'No sets exitst');
		return;
	}	
	json_response('ok', 'New question set', $data);
}

function saveTitle(){
	$details = json_decode($_POST['data']);

	if (isset($details->set_id)){
		$query = "UPDATE nameids SET QUESTION_SET_NAME = '$details->title' WHERE QUESTION_SET_ID = '$details->set_id'";
	}else{
		$query = "INSERT INTO nameids(QUESTION_SET_NAME) VALUES ('$details->title') RETURNING question_set_id";
	}
	$result = pg_query($query); 

	if (!$result){
		echo json_response('error', 'Unable to save form title');
	}else{

        json_response('ok', 'Form title saved...', $result);
	}
	
}



function saveQuestion(){
	$inputs = array();
	$key = array();
	$questions = json_decode($_POST['data']);

	foreach($questions as $item => $value) {
		$key[$item] = $value;
	}



	foreach ($key as $inputs) {
		
		if($inputs->question_id==='null'){
	    	$query = "INSERT INTO questions(QUESTION, INPUT_TYPE, QUESTION_SET) VALUES ('pg_escape_string($inputs->question)', 'pg_escape_string($inputs->replyType)', 'pg_escape_string($inputs->question_set_id)')";
		}else{
			$query = "UPDATE questions SET QUESTION = '$inputs->question', INPUT_TYPE = '$inputs->replyType', QUESTION_SET = '$inputs->question_set_id' WHERE ID = '$inputs->question_id'";
		}
		$result = pg_query($query); 
	}
	
	
	if (!$result){
		json_response('error', 'Unable to process question');
	}else{
		$data =json_encode($result);
        json_response('ok', 'Questions Updated', $inputs);
	}
}

function newFormSet(){
	$query = "select question_set_id from nameids order by question_set_id desc limit 1";

	$result = pg_query($query); 
	$row = pg_fetch_row($result);

	if ($row){
		$thisRow = $row[0];
		$newRow = $thisRow +1;
	  	json_response('ok', 'New question set', $newRow);
	}else{
		json_response('ok', 'New question set', 1);
	}	
		

}

function createAndGetId(){
	$inputs = array();
	$qsid = json_decode($_POST['question_set_id']);

	$query = "INSERT INTO questions(QUESTION_SET) VALUES ('$qsid') RETURNING id, question_set ";

	$result = pg_query($query); 
	$insert_row = pg_fetch_row($result);
	
	if (!$result){
		echo "Error creating question";
	}else{
		$data = array('last_row' => ($insert_row[0]), 'question_set' => ($insert_row[1]));
		json_response('ok', 'Question set Created', $data);
	}	
}

?>