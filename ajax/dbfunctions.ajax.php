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
	case 'formAnswers':
		saveAnswers();
		break;

}

function getAllQuestionSets(){
	$sql = "SELECT nameids.question_set_name, nameids.question_set_id, count(questions.question_set) FROM nameids LEFT JOIN questions ON nameids.question_set_id = questions.question_set GROUP BY nameids.question_set_name, nameids.question_set_id";

	$query = DB::query($sql, false);
	
	if ($query->rowCount()>0){
		$data = array();
		while($row = $query->fetchObject()){
			array_push($data, $row);
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

	if($result){
		json_response('ok', 'Form deleted', $result);
	}else{
		json_response('error', 'No sets exist');
	}
}

function deleteOption(){
	$optionId = json_decode($_POST['option_id']);
	$sql = "DELETE FROM options WHERE id = '$optionId'";
	$query = DB::query($sql, false);

	if($result){
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

			$thisRow = $row[0];
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
	$sql = "SELECT nameids.question_set_name, nameids.question_set_id, nameids.site_id, nameids.id AS nameId, questions.id, questions.question 
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
		while($row = $query->fetchObject()){
		$data = array();
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
				'position'		=> $inputs->position
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

	$arr = cleanMe($arr);


	$query = "SELECT id FROM answers WHERE question_id = $questionId AND  USER_ID = $userId";

	$row = pg_fetch_assoc(pg_query($query));
	if($row){
		foreach ($arr as $value => $answer){
			$id = substr($value, strpos($value, "_") + 1);
			$query = "UPDATE answers SET ANSWER = $answer WHERE QUESTION_ID = $id AND USER_ID = $userId";
			$result = pg_query($query);
			$insert_row = pg_fetch_row($result);
			
		}
		json_response('ok', 'Your answers have been updated');
	}else{
		foreach ($arr as $value => $answer){
			$id = substr($value, strpos($value, "_") + 1);
			$query = "INSERT INTO answers(answer, question_id, user_id) VALUES ($answer, $id, $userId)";
			$result = pg_query($query);
			$insert_row = pg_fetch_row($result);	
			
		}

		json_response('ok', 'Your answers have been saved');
	}
}




function createAndGetOptionId(){
	$qid = json_decode($_POST['question_id']);
	$qid = pg_escape_string($qid);
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




function createAndGetId(){
	$inputs = array();
	$qsid = json_decode($_POST['question_set_id']);

	$query = "INSERT INTO questions(QUESTION_SET) VALUES ('$qsid') RETURNING id, question_set, position ";

	$result = pg_query($query); 
	$insert_row = pg_fetch_row($result);
	
	if (!$result){
		echo "Error creating question";
	}else{
		$data = array('last_row' => ($insert_row[0]), 'question_set' => ($insert_row[1]), 'position' => ($insert_row[2]));
		json_response('ok', 'Question set Created', $data);
	}	
}

?>