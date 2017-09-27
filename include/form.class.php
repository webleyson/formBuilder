<?php
class Form
{
    private $action;
    
    private $method;
    
    private $elements = array();
    
    public function __construct($questionSetId, $action = "send.php", $method = 'post')
    {
        $this->questionSetId = $questionSetId;
        $this->action = $action;
        $this->method = $method;
       

        $questionSet = $this->getQuestionSet($this->questionSetId);
         $this->title = $questionSet[0]['question_set_name'];

        $questionsWithOptions = $this->getOptions($questionSet);

        $this->buildForm($questionsWithOptions);


    }
    

    public function buildForm($questions){
        foreach ($questions as $question){
            switch ($question['input_type']) {
                case 'select':

                    $dropDown = new DropDownElement($question['question'],  'select_'. $question['id'] .'',  'select_'. $question['id'] .''); 
                    foreach ($question['options'] as $key => $value) {
                        $dropDown->addAttribute($value, $value);
                    }
                    $this->addElement($dropDown);

                    break;
                case 'text':
                    $textElement = new TextFormElement($question['question']);
                    $textElement->addAttribute('name', 'textfield_'. $question['id'] .'');
                    $this->addElement($textElement);
                break;

                case 'textarea':
                    $textArea = new TextArea($question['question']);
                    $textArea->addAttribute('name', 'textarea_'. $question['id'] .'');
                    $this->addElement($textArea);
                break;
                
                default:
                    # code...
                    break;
            }

        }
       echo $this->build();

    }

    public function addElement(FormElement $element)
    {
        $this->elements[] = $element;
    }


    public function getQuestionSet($setId)
    {

        $query = "SELECT * FROM questions JOIN nameids ON questions.question_set = nameids.question_set_id  WHERE nameids.question_set_id = '$setId'";

        $result = pg_query($query); 
        if ($result){
            $data = array();
            while ($row = pg_fetch_assoc($result)) {
            array_push($data, $row);
            }
        }else{
            return false;
        }   
        return $data;

    }

    public function getOptions($questionSet)
    {

        $completeQuestions = array();
        foreach($questionSet as $question){

            $query = "SELECT answer_option FROM options WHERE question_id = {$question['id']}";
            $result = pg_query($query); 
            $options = array();
            while ($row = pg_fetch_assoc($result)) {
                array_push($options, $row['answer_option']);
            }
            $question['options'] = $options;
            $completeQuestions[] = $question;
         }

            return $completeQuestions;
    }
    
    public function build()
    {
    	$elements= "";
        foreach ($this->elements as $element)
        {
            $elements .= $element->build();
        }
        return '<div class="container"><h2>'.$this->title.'</h2><form action="' . $this->action . '" method="' . $this->method . '"> ' . $elements . ' </form></div>';
    }
}

abstract class FormElement
{

    public $atttributes;
    
    public function addAttribute($name, $value)
    {
        $this->atttributes[$name] = $value;
    }
    
    abstract public function build();
}



class TextFormElement extends FormElement
{
	public function __construct($question){
		$this->question = $question;
	}

    public function build()
    {
    	$atttributes= "";
        $text = "<div class='form-group'>";
    	$text .= '<label>' . $this->question . '</label>';
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }
        
        $text .=  '<input class="form-control" type="text" '.$atttributes.'/>';
        $text .= "</div>";
       	return $text;
    }
}

class TextArea extends FormElement
{

	public function __construct($question){
		$this->question = $question;
	}
	 public function build()
    {
    	$atttributes= "";
    	 $text = "<div class='form-group'>";
        $text .= '<label>' . $this->question . '</label>';
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }

       $text .=  '<textarea class="form-control" '.$atttributes.'></textarea>';
       return $text;
    }
}


class RadioButtonElement extends FormElement
{
	 public function __construct($question, $name, $id)
    {	
    	$this->question = $question;
        $this->name = $name;
        $this->id = $id;
      

    }
    public function build()
    {
    	$atttributes= "";
    	$text = '<p>' . $this->question . '</p>';
        foreach ($this->atttributes as $val => $name)
        {
            $atttributes .= '<input type="radio" name="'. $name .'"  value="'. $val .'">';
        }
       $text.= $atttributes;
       return $text;
    }
}


class DropDownElement extends FormElement
{
	 public function __construct($question, $selectName, $id)
    {	
    	$this->question = $question;
        $this->selectName = $selectName;
        $this->id = $id;
      

    }
    public function build()
    {
    	$atttributes= "";
    	$text = '<p>' . $this->question . '</p>';
        foreach ($this->atttributes as $val => $name)
        {
            $atttributes .= '<option class="form-control" value='. $val .'>'.$name.'</option>';
        }
       $text.= '<select id="'. $this->id .'" name="'. $this->selectName .'">'. $atttributes . '</select>';
       return $text;
    }
}

class PasswordFormElement extends FormElement
{
	public function __construct($question){
		$this->question = $question;
	}
    public function build()
    {

    	$text = '<p>' . $this->question . '</p>';
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }
        $text.= '<input type="password" '.$atttributes.'/>';
        return $text;
    }
}

class SubmitFormElement extends FormElement
{
    public function build()
    {	
		$atttributes = "";
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }
        return '<input type="submit" '.$atttributes.'/>';
    }
}

?>