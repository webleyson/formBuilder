<?php
class Form
{
    private $action;
    
    private $method;
    
    private $elements = array();
    
    public function __construct($action, $method = 'post')
    {
        $this->action = $action;
        $this->method = $method;

    }
    
    public function addElement(FormElement $element)
    {
        $this->elements[] = $element;
    }
    
    public function build()
    {
    	$elements= "";
        foreach ($this->elements as $element)
        {
            $elements .= $element->build();
        }
        return '<form action="' . $this->action . '" method="' . $this->method . '"> ' . $elements . ' </form>';
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
    	$text = '<p>' . $this->question . '</p>';
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }
        
        $text .=  '<input type="text" '.$atttributes.'/>';
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
    	$text = '<p>' . $this->question . '</p>';
        foreach ($this->atttributes as $name => $val)
        {
            $atttributes .= $name .'="'.$val.'"';
        }

       $text .=  '<textarea '.$atttributes.'></textarea>';
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
            $atttributes .= '<option value='. $val .'>'.$name.'</option>';
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