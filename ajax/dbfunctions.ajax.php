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
		break;
	case 'deleteForm':
		deleteFormAndAssociatedQuestions();
		break;
	case 'createAndGetOptionId':
		createAndGetOptionId();
		break;
	case 'getOptions':
		getAdditionalOptions();
		break;
	case 'updateOption':
		updateOption();
		break;
	case 'deleteOption':
		deleteOption();
		break;
	case 'deleteQuestion':
		deleteQuestion();
		break;

}


function getAdditionalOptions(){
	$qid = json_decode($_POST['question_id']);


	$query = "SELECT * FROM options WHERE question_id = '$qid'";

	$result = pg_query($query); 
	
	if ($result){
		$data = array();
		while ($row = pg_fetch_assoc($result)) {
			array_push($data, $row);
		}
		json_response('ok', 'Additional Options', $data);
	}else{
		json_response('ok', 'No options exist', $data);
	}
}


function createAndGetOptionId(){
	$qid = json_decode($_POST['question_id']);

	$query = "INSERT INTO options(QUESTION_ID) VALUES ('$qid') RETURNING id ";

	$result = pg_query($query);
	$insert_row = pg_fetch_row($result);

	if (!$result){
		echo json_response('error', 'Unable to save form title');
	}else{

        json_response('ok', 'Form title saved...',$insert_row[0]);
	}
}


function updateOption(){
	$details = json_decode($_POST['data']);
	$query = "UPDATE options SET ANSWER_OPTION = '$details->option_value', QUESTION_ID = '$details->question_id' WHERE ID = '$details->option_id'";


	$result = pg_query($query);
	$insert_row = pg_fetch_row($result);

	if (!$result){
		echo json_response('error', 'Unable to save form title');
	}else{

        json_response('ok', 'Option added...',$insert_row[0]);
	}
}


function deleteOption(){
	$optionId = json_decode($_POST['option_id']);
	$query = "DELETE FROM options WHERE id = '$optionId'";
	$result = pg_query($query);

	if($result){
		json_response('ok', 'Option deleted', $result);
	}else{
		json_response('error', 'Option doesnt exist');
	}
}

function deleteQuestion(){
	$questionId = json_decode($_POST['question_id']);
	$query = "DELETE FROM options WHERE question_id = '$questionId'";
	$result = pg_query($query);
	if($result){
		$query = "DELETE FROM questions WHERE id = '$questionId'";
		$result = pg_query($query);
		json_response('ok', 'Question deleted', $result);
	}else{
		json_response('error', 'Question doesnt exist');
	}
}

function deleteFormAndAssociatedQuestions(){
	$setId = json_decode($_POST['question_set_id']);
	$query = "DELETE FROM nameids WHERE question_set_id = '$setId'";
	$result = pg_query($query);

	$select = "SELECT id FROM questions WHERE question_set = '$setId'";
	$result = pg_query($query);
	if ($result){
		$query = "DELETE FROM questions WHERE question_set = '$setId'";
		$result = pg_query($query);
	}

	if($result){
		json_response('ok', 'Form deleted', $result);
	}else{
		json_response('error', 'No sets exist');
	}
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
		json_response('error', 'No sets exist');
	}	
	json_response('ok', 'Question set loaded', $data);
}


function getAllQuestionSets(){
	$query = "SELECT nameids.question_set_name, nameids.question_set_id, count(questions.question_set) FROM nameids LEFT JOIN questions ON nameids.question_set_id = questions.question_set GROUP BY nameids.question_set_name, nameids.question_set_id";

	$result = pg_query($query); 
	
	if ($result){
		$data = array();
		while ($row = pg_fetch_assoc($result)) {
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

	$insert_row = pg_fetch_row($result);

	
	if (!$result){
		echo "Error creating question";
	}else{
		$data = array('question_set' => ($insert_row[0]));
		json_response('ok', 'Question set Created', $data);
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
	    	$query = "INSERT INTO questions(QUESTION, INPUT_TYPE, QUESTION_SET) VALUES ('$inputs->question', '$inputs->replyType', '$inputs->question_set_id')";
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
	$query = "SELECT question_set_id FROM nameids ORDER BY question_set_id DESC LIMIT 1";

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