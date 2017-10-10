
<?php include('db.class.php');?>
<?php
class Form
{
    private $action;
    
    private $method;
    
    private $elements = array();
    
    public function __construct($questionSetId, $userId=null, $action = "send.php", $method = 'post')
    {
        $this->questionSetId = $questionSetId;
        $this->action = $action;
        $this->method = $method;
        $this->userId = $userId;
        $questionSet = $this->getQuestionSet($this->questionSetId, $this->userId);
        $this->title = $questionSet[0]->question_set_name;
        $this->buildForm($this->getOptions($questionSet));
      
    }
    


    public function buildForm($questions){
        foreach ($questions as $question){

            switch ($question->input_type) {
                case 'select':
                    $dropDown = new DropDownElement($question->question,  'select_'. $question->id .'',  'select_'. $question->id .'', $question->answer); 
                    foreach ($question->options as $key => $value) {
                        $dropDown->addAttribute($value, $value);
                    }
                    $this->addElement($dropDown);

                    break;


                case 'checkbox':
                    $checkbox = new CheckboxElement($question->question,  'checkbox_'. $question->id .'',  'checkbox_'. $question->id .'', $question->answer);
                    foreach ($question->options as $key => $value) {
                        $checkbox->addAttribute($value, $value);
                    }
                    $this->addElement($checkbox);

                    break;

                 case 'radio':
                    $radio = new RadioButtonElement($question->question, 'radio'. $question->id .'',  'radio_'. $question->id .'', $question->answer);
                    foreach ($question->options as $key => $value) {
                        $radio->addAttribute($value, $value);
                    }
                    $this->addElement($radio);
                    
                    break;

                case 'text':

                    $textElement = new TextFormElement($question->question, $question->answer);
                    $textElement->addAttribute('name', 'textfield_'. $question->id .'');
                    $this->addElement($textElement);
                
                    break;

                case 'textarea':
                    $textArea = new TextArea($question->question, $question->answer);
                    $textArea->addAttribute('name', 'textarea_'. $question->id .'');
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


    public function getQuestionSet($setId, $userId)
    {

        $sql = "SELECT DISTINCT questions.id, questions.question, questions.input_type, questions.position, questions.question_set, question_set_name, question_set_id, answer FROM questions LEFT JOIN nameids ON questions.question_set = nameids.question_set_id LEFT JOIN answers ON questions.id = answers.question_id WHERE nameids.question_set_id = '$setId' ORDER BY position";
       
        $query = DB::query($sql, false);


        if ($query->rowCount()>0){
        $data = array();
            while($row = $query->fetchObject()){
                array_push($data, $row);
            }
            return $data;
        }else{
            return false;
        }   

    }

    public function getOptions($questionSet)
    {
        $completeQuestions = array();
        foreach($questionSet as $question){

            $sql = "SELECT answer_option FROM options WHERE question_id = {$question->id}";
            $query = DB::query($sql, false);

                $options = array();
                while($row = $query->fetchObject()){
                    array_push($options, $row->answer_option);
                }

            $question->options = $options;
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
        return '<input type="hidden" name="userId" id="userId" value="'.$this->userId.'"><div class="container"><h2>'.$this->title.'</h2><form id="myForm" action="' . $this->action . '" method="' . $this->method . '"> ' . $elements . '  <button type="submit" class="btn btn-primary">Submit form</button></form></div>';
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
    public function __construct($question, $answer){
        $this->question = $question;
        $this->answer = $answer;
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
        
        $text .=  '<input class="form-control" value="'.$this->answer.'" type="text" '.$atttributes.'/>';
        $text .= "</div>";
        return $text;
    }
}

class TextArea extends FormElement
{

    public function __construct($question, $answer){
        $this->question = $question;
        $this->answer = $answer;
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

       $text .=  '<textarea class="form-control" '.$atttributes.'>'. $this->answer .'</textarea>';
       $text .= "</div>";
       return $text;
    }
}

class CheckboxElement extends FormElement
{
     public function __construct($question, $name, $id, $answers)
    {   
        $this->question = $question;
        $this->name = $name;
        $this->id = $id;
        $this->answers = $answers;


    }
    public function build()
    {

        $atttributes= "";
        $text = '<label>' . $this->question . '</label>';
        if ($this->atttributes){
            foreach ($this->atttributes as $val => $name)
            {
                $answersArray = array_map('trim', explode(',',$this->answers));
                $checked = (in_array($name, $answersArray) ? "checked" : "");
                $atttributes .= '<div class="checkbox"><label><input type="checkbox" '. $checked .' name="'.$this->id.'" id="'. $this->id .'"  value="'. $val .'">'. $name .'</label></div>';
            }
        }else{
            $atttributes .= '<div class="checkbox"><label><input type="checkbox" name="null" id="0"  value="null">No options specified</label></div>';
        }
        
       $text.= $atttributes;
       return $text;
    }
}

class RadioButtonElement extends FormElement
{
     public function __construct($question, $name, $id, $answer)
    {   
        $this->question = $question;
        $this->name = $name;
        $this->id = $id;
        $this->answer = $answer;
      

    }
    public function build()
    {
        $atttributes= "";
        $text = '<label>' . $this->question . '</label>';
        if ($this->atttributes){
            foreach ($this->atttributes as $val => $name)
            {

                $checked = ($this->answer == $name ? "checked" : "");

                $atttributes .= '<div class="radio"><label><input type="radio"'. $checked .' name="'. $this->id .'"  value="'. $val .'">'. $name .'</label></div>';
            }
        }else{
            $atttributes .= '<div class="radio"><label><input type="radio" name="null"  value="null">No option specified</label></div>';
        }        
       $text.= $atttributes;

       return $text;
    }
}


class DropDownElement extends FormElement
{
     public function __construct($question, $selectName, $id, $answer)
    {   
        $this->question = $question;
        $this->selectName = $selectName;
        $this->id = $id;
        $this->answer = $answer;
    }

    public function build()
    {
        $atttributes= "";
        $text = '<label>' . $this->question . '</label>';
        if($this->atttributes){
             foreach ($this->atttributes as $val => $name)
            {
                $checked = ($this->answer == str_replace(" ","_",$name) ? "selected" : "");
                $val = str_replace(' ', '_', $val);
                $atttributes .= '<option '. $checked .' class="form-control" value='. $val .'>'.$name.'</option>';
            }
            $text.= '<select class="form-control" id="'. $this->id .'" name="'. $this->selectName .'">'. $atttributes . '</select>';
        }else{
            $text.= '<select class="form-control" id="" name="null"><option>No select options available</option></select>';
        }
       
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