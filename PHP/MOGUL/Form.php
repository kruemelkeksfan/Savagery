<?php
class Form
	{
	private $action;
	private $method;
	private $page;
	private $title;
	private $fields;
	private $pattern;
	private $columnclasses;
	private $formclass;
	private $fieldclass;

	function __construct(string $action, string $method = 'get', Page $page = null, string $title = null,
		array $columnclasses = array('formcolumn width300px'), string $formclass = 'form width300px', string $fieldclass = 'formfield')
		{
		$this->action = $action;
		$this->method = $method;
		$this->page = $page;
		$this->title = $title;
		if(count($columnclasses) > 0)
			{
			$this->columnclasses = $columnclasses;
			}
		else
			{
			ErrorHandler::handle_error('No Column Widths supplied for new Form ' . $title . '!');
			}
		$this->formclass = $formclass;
		$this->fieldclass = $fieldclass;
		
		$this->fields = array();
		$this->fields[] = array();
		$this->columnwidths = array();
		
		$this->unset_pattern();
		}

	function set_pattern(int $min = 1, int $max = 16, bool $upper = true, bool $lower = true, bool $digits = true,
		bool $space = false, bool $minus = false, bool $underscore = false)
		{
		$this->pattern = sprintf('^[%s%s%s%s%s%s]{%d,%d}" title="%d-%d characters%s%s%s%s%s%s',
			($upper ? 'A-Z' : ''),
			($lower ? 'a-z' : ''),
			($digits ? '0-9' : ''),
			($space ? ' ' : ''),
			($minus ? '-' : ''),
			($underscore ? '_' : ''),
			$min,
			$max,
			$min,
			$max,
			($upper ? ', A-Z' : ''),
			($lower ? ', a-z' : ''),
			($digits ? ', 0-9' : ''),
			($space ? ', Space' : ''),
			($minus ? ', -' : ''),
			($underscore ? ', _' : ''));
		}
		
	// This is an extra Method and not the Default for Security Reasons
	function set_unrestricted_pattern()
		{
		$this->pattern = '.{1,2048}';
		}
		
	final function unset_pattern()
		{
		$this->set_pattern(1, 2048);
		}
		
	function add_column_break()
		{
		$this->fields[] = array();
		}
	
	function add_spacer(string $class = 'spacer')
		{
		$this->fields[count($this->fields) - 1][] = '<div class="' . $class . '"> </div>';
		}
		
	function add_label(string $name = null, string $for = null)
		{
		if(!empty($name))
			{
			$this->fields[count($this->fields) - 1][] = '<label ' . (!empty($for) ? ('for="' . $for . '"') : '') . '>' . $name . ':</label>';
			}
		}
	
	// TODO: Number Field as extra Method
	function add_field(string $name = null, bool $required = true, string $type = 'text', $value = null, bool $singleline = false, float $step = null,
		string $class = '', int $min = null, int $max = null)
		{			
		$this->add_label($name, $name);
		
		$field = '<input type="' . $type  . '" '
			. 'name="' . (empty($name) ? ('field_' . (count($this->fields) - 1) . '_' . count($this->fields[count($this->fields) - 1])) : $name) . '" '
			. ($required ? 'required="required" ' : '')
			. (isset($value) ? 'value="' . $value . '" ' : '')
			. (($type !== 'number') ? 'pattern="' . $this->pattern . '" ' : '')
			. (($type === 'number' && !empty($step)) ? 'step="' . $step . '" ' : '')
			. (($type === 'number' && isset($min)) ? 'min="' . $min . '" ' : '')
			. (($type === 'number' && isset($max)) ? 'max="' . $max . '" ' : '')
			. 'id="' . $name . '" '
			. 'class="'
			. ($singleline ? 'singleline' : '')
			. (!empty($class) ? (' ' . $class) : '')
			. '" />';
		
		if($singleline && !empty($name))
			{
			$columnindex = count($this->fields) - 1;
			$fieldindex = count($this->fields[$columnindex]) - 1;
			$this->fields[$columnindex][$fieldindex] = '<div class="formlabelcontainer">'
				. $this->fields[$columnindex][$fieldindex] . $field . '</div>';
			}
		else
			{				
			$this->fields[count($this->fields) - 1][] = $field;
			}
		}
		
	function add_toggle_field(string $name = null, bool $required = true, bool $value = false, bool $singleline = false)
		{
		$this->add_label($name, (empty($name) ? ('field_' . (count($this->fields) - 1) . '_' . count($this->fields[count($this->fields) - 1])) : $name) . '_on');
	
		$field = '<div><label'
			. 'for="' . (empty($name) ? ('field_' . (count($this->fields) - 1) . '_' . count($this->fields[count($this->fields) - 1])) : $name) . '_on' . '"'
			. '>on</label>'
			. '<input type="radio" name="' . $name . '" ' . ($required ? 'required="required" ' : '')
			. 'value=true ' . ($value ? 'checked="checked" ' : '') . 'id="' . $name . '_on" ' . '>'
			. '<label for="' . $name . '_off' . '">off</label>'
			. '<input type="radio" name="' . $name . '" ' . ($required ? 'required="required" ' : '')
			. 'value=false ' . (!$value ? 'checked="checked" ' : '') . 'id="' . $name . '_off" ' . '></div>';
	
		if($singleline && !empty($name))
			{
			$columnindex = count($this->fields) - 1;
			$fieldindex = count($this->fields[$columnindex]) - 1;
			$this->fields[$columnindex][$fieldindex] = '<div class="formlabelcontainer">'
				. $this->fields[$columnindex][$fieldindex] . $field . '</div>';
			}
		else
			{
			$this->fields[count($this->fields) - 1][] = $field;
			}
		}
		
	function add_dropdown_field(string $name = null, array $options, bool $required = true, bool $singleline = false)
		{
		$this->add_label($name, $name);
		
		$field = '<select name="'
			. (empty($name) ? ('field_' . (count($this->fields) - 1) . '_' . count($this->fields[count($this->fields) - 1])) : $name) . '" '
			. ($required ? 'required="required" ' : '') . 'id="' . $name . '" ' . '>';
		if(!current($options))
			{
			reset($options);
			}
		foreach($options as $optionindex => $option)
			{
			$field .= '<option value="' . $optionindex . '" ' . (($option == current($options)) ? 'selected="selected"' : '') . '>' . $option . '</option>';
			}
		$field .= '</select>';
		
		if($singleline && !empty($name))
			{
			$columnindex = count($this->fields) - 1;
			$fieldindex = count($this->fields[$columnindex]) - 1;
			$this->fields[$columnindex][$fieldindex] = '<div class="formlabelcontainer">'
				. $this->fields[$columnindex][$fieldindex] . $field . '</div>';
			}
		else
			{
			$this->fields[count($this->fields) - 1][] = $field;
			}
		}
		
	function add_submit(string $text = 'Submit')
		{
		$this->fields[count($this->fields) - 1][] = '<input type="submit" value="' . $text . '" />';
		}
		
	function print()
		{
		echo("\n" . '<div class="' . $this->formclass . '">' . "\n");
		echo('<form action="' . $this->action . '" method="' . $this->method . '">' . "\n");
		
		if(!empty($this->page) && !empty($this->title))
			{
			$this->page->print_heading($this->title);
			}
		
		foreach($this->fields as $columnindex => $column)
			{
			echo('<div class="' . $this->columnclasses[$columnindex % count($this->columnclasses)] . '">' . "\n");
			foreach($column as $field)
				{
				echo('<div class="' . $this->fieldclass . '">' . "\n");
				echo($field . "\n");
				echo('</div>' . "\n");
				}
			echo('</div>' . "\n");
			}
		
		echo('</form>' . "\n");
		echo('</div>' . "\n");
		}
	}
?>