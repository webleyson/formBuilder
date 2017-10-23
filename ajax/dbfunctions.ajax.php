<?php 
require_once('../include/connectDB.php'); 
require_once('../include/generic_functions.php'); 
require_once('../include/db.class.php');
define("SITE_ID", 1);

$do = isset($_POST['do']) ? $_POST['do'] : false;
switch($do){
	default:
        json_response("error", "Invalid Request");
        break;
    case 'getAllSets':
		getAllQuestionSets();
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
	case 'deleteRedundantData':
		deleteRedundantData();
		break;
	case 'showReport':
		showReport();
		break;
	case 'showUsers':
		showUsers();
		break;
	case 'checkOptions':
		checkOptions();
		break;
	case 'formAnswers':
		saveAnswers();
		break;
}


function showUsers(){
	$qid = json_decode($_POST['questionId']);
}

function showReport(){
	$qid = json_decode($_POST['questionId']);
	$sql = "SELECT  questions.question, questions.id, answers.user_id, nameids.question_set_name, nameids.question_set_id,questions.input_type, answers.answer FROM questions
			JOIN nameids ON nameids.question_set_id = questions.question_set 
			RIGHT JOIN answers ON answers.question_id = questions.id 
			WHERE nameids.question_set_id = $qid
			ORDER BY question, user_id";
			

	$query = DB::query($sql, false);
	if ($query->rowCount()>0){
		$data = array();
		while($row = $query->fetchObject()){
			array_push($data, $row);
		}

		json_response('ok', 'New question set', $data);
	}else{
		json_response('ok', 'No sets exitst');
		return;
	}
}
function getAllQuestionSets(){
	$sql = "SELECT nameids.question_set_name, nameids.question_set_id, count(questions.question_set) FROM nameids LEFT JOIN questions ON nameids.question_set_id = questions.question_set GROUP BY nameids.question_set_name, nameids.question_set_id";

	$query = DB::query($sql, false);
	
	if ($query->rowCount()>0){
		$data = array();
		while($row = $query->fetchObject()){
			array_push($data, $row);
		}
		$responseCount = "";
		$sqlCount = "SELECT COUNT(DISTINCT user_id) FROM answers";
		$queryCount = DB::query($sqlCount, false);
		if ($queryCount->rowCount()>0){

			while($row = $queryCount->fetchObject()){
				$responseCount = $row->count;
			}
		}else{
			$responseCount = 0;
		}
	}else{
		json_response('ok', 'No sets exitst');
		return;
	}	
	json_response('ok', 'New question set', $data);
}

function deleteFormAndAssociatedQuestions(){
	$setId = json_decode($_POST['question_set_id']);

	$sql = "DELETE FROM nameids WHERE question_set_id = '$setId'";
	$query = DB::query($sql, false);

	$select = "SELECT id FROM questions WHERE question_set = '$setId'";
	$query = DB::query($select, false);
	

	if ($query->rowCount()>0){
		$sql = "DELETE FROM questions WHERE question_set = '$setId'";
		$query = DB::query($sql, false);
	}

	if($query){
		json_response('ok', 'Form deleted', $result);
	}else{
		json_response('error', 'No sets exist');
	}
}

function deleteOption(){
	$optionId = json_decode($_POST['option_id']);
	$sql = "DELETE FROM options WHERE id = '$optionId'";
	$query = DB::query($sql, false);

	if($query){
		json_response('ok', 'Option deleted', $result);
	}else{
		json_response('error', 'Option doesnt exist');
	}
}

function newFormSet(){
	$sql = "SELECT question_set_id FROM nameids ORDER BY question_set_id DESC LIMIT 1";

	$query = DB::query($sql, false);

	if ($query->rowCount()>0){
		while($row = $query->fetchObject()){
			$thisRow = $row->question_set_id;
			$newRow = $thisRow +1;
		  	json_response('ok', 'New question set', $newRow);
		  }
	}else{
		json_response('ok', 'New question set', 1);
	}	
}


function saveTitle(){
	$details = json_decode($_POST['data']);
	$data = array('question_set_name' => $details->title);
		
	if (isset($details->set_id)){
		$where = "question_set_id = {$details->set_id}";
		$result = DB::update("nameids", $data, $where);
	}else{
		$result = DB::insert("nameids", $data);
	}

	$sql = "SELECT question_set_id FROM nameids WHERE id = $result";
	$query = DB::query($sql, false);
	
	if ($query->rowCount()>0){
		while($row = $query->fetchObject()){
			$data = array('question_set' => ($row->question_set_id));
			json_response('ok', 'Question set Created', $data);
		  }
	}else{
		$data = array('question_set' => null);
		json_response('Error', 'Question set couldnt be created', $data);
	}
}

function getExisting(){
	$setId = json_decode($_POST['question_set_id']);

	$oldQuery = "SELECT * FROM questions RIGHT JOIN nameids ON questions.question_set = nameids.question_set_id  WHERE nameids.question_set_id = '$setId' ORDER BY position";
	$sql = "SELECT nameids.question_set_name, nameids.question_set_id, nameids.site_id, nameids.id AS nameId, questions.id, questions.input_type, questions.question 
			FROM nameids 
			LEFT JOIN questions ON questions.question_set = nameids.question_set_id 
			WHERE nameids.question_set_id = '$setId' ORDER BY position";

	$query = DB::query($sql, false);

	if ($query->rowCount()>0){
		$data = array();
		while($row = $query->fetchObject()){
		array_push($data, $row);
		}
		json_response('ok', 'Question set loaded', $data);
	}else{
		json_response('error', 'No sets exist');
	}	
	
}

function getAdditionalOptions(){
	$qid = json_decode($_POST['question_id']);
	$sql = "SELECT * FROM options WHERE question_id = '$qid'";
	$query = DB::query($sql, false);

	if ($query->rowCount()>0){
		$data = array();
		while($row = $query->fetchObject()){
		
		array_push($data, $row);
		}
		json_response('ok', 'Additional Options', $data);
	}else{
		json_response('error', 'No options exist', null);
	}
}

function saveQuestion(){
	$key = array();
	$questions = json_decode($_POST['data']);

	foreach($questions as $item => $value){
		$key[$item] = $value;
	}

	foreach ($key as $inputs) {

		if($inputs->question_id==='null'){
	    	$data = array(
				'question'		=> $inputs->question,
				'input_type'	=> $inputs->replyType,
				'question_set'	=> $inputs->question_set_id,
			);
	    	$result = DB::insert("questions", $data);
		}else{
			$data = array(
				'question'		=> $inputs->question,
				'input_type'	=> $inputs->replyType,
				'question_set'	=> $inputs->question_set_id,
				'position'		=> $inputs->position,
			);
			$where = "id = {$inputs->question_id}";
			$result = DB::update("questions", $data, $where);
		}
	}

	if (!$result){
		json_response('error', 'Unable to process question');
	}else{
        json_response('ok', 'Questions Updated', $data);
	}
}

function createAndGetOptionId(){
	$qid = json_decode($_POST['question_id']);
	
	$data = array(
		'question_id' => $qid
	);

	$result = DB::insert("options", $data);

	if($result){
		$sql = "SELECT question_id FROM options WHERE id = '$result'";
		$query = DB::query($sql, false);
		if ($query->rowCount()>0){
			while($row = $query->fetchObject()){
				$data=array(
					'question_set_id' 	=> $row->question_id,
					'id'				=> $result,
				);
			}
			json_response('ok', 'Additional option field', $data);
		}
	}else{
		json_response('error', 'Cannot add option');   
	}
}

function updateOption(){
	$details = json_decode($_POST['data']);
	$data = array(
		'answer_option' 	=> $details->option_value,
		'question_id'		=> $details->question_id,
	);
	$where = "id = {$details->option_id}";
	$result = DB::update("options", $data, $where);

	if (!$result){
		echo json_response('error', 'Unable to save form title');
	}else{
        json_response('ok', 'Option added...',$result);
	}
}

function deleteQuestion(){
	$questionId = json_decode($_POST['question_id']);
	$sql = "DELETE FROM options WHERE question_id = '$questionId'";
	$query = DB::query($sql, false);
	if($query){
		$delete = "DELETE FROM questions WHERE id = '$questionId'";
		$result = DB::query($delete, false);
		json_response('ok', 'Question deleted', $result);
	}else{
		json_response('error', 'Question doesnt exist');
	}
}






function deleteRedundantData(){
	$idsToDelete = json_decode($_POST['data']);

	$ids = implode(', ', $idsToDelete);

	$query = "DELETE FROM options WHERE question_id IN ({$ids})";
	$result = DB::query($query, false);
	if($result){
		json_response('ok', 'Your form is ready to go!', $result);
	}else{
		json_response('ok', 'Form is published', $result);
	}
}



function createAndGetId(){
	$inputs = array();
	$qsid = json_decode($_POST['question_set_id']);
	$data = array(
				'question_set'	=> $qsid
			);

	$result = DB::insert("questions", $data);

	$sql = "SELECT * FROM questions WHERE id = $result";
	$query = DB::query($sql, false);

	if ($query->rowCount() > 0) {
		$row = $query->fetchObject();
		$data = array('last_row' => $row->id, 'question_set' => $row->question_set, 'position' => $row->position);
		json_response('ok', 'Question set Created', $data);
	}else{
		echo "Error creating question";
	}
	
}

function saveAnswers(){
	$userId = isset($_POST['userId']) ? $_POST['userId'] : false;
	$data = isset($_POST['data']) ? $_POST['data'] : false;
	$questionId = substr($data[0]['name'], strpos($data[0]['name'], "_") + 1);
	$arr = array();
	foreach ($data as $k => $v){
		if (!array_key_exists($v['name'], $arr)) {
        	$arr[$v['name']] = $v['value'];
    	}else{
          	$arr[$v['name']] .=  ", " .$v['value'];
    	}
	}

	$sql = "SELECT id FROM answers WHERE question_id = $questionId AND  USER_ID = $userId";
	$query = DB::query($sql, false);
	if($query->rowCount() > 0){
		foreach ($arr as $value => $answer){
			$id = substr($value, strpos($value, "_") + 1);
			$data = array(
				'answer'		=> $answer,
			);
			$where = "question_id = {$id} AND user_id = {$userId}";
			$result = DB::update("answers", $data, $where);	
		}
			json_response('ok', 'Your answers have been updated');
	}else{
		foreach ($arr as $value => $answer){
			$id = substr($value, strpos($value, "_") + 1);
			$data = array(
				'answer'		=> $answer,
				'question_id'	=> $id,
				'user_id'		=> $userId,
			);
			$result = DB::insert("answers", $data);
			$insert_row = $result;	
		}
		json_response('ok', 'Your answers have been saved');
	}
}


?>