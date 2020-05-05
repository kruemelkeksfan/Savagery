<?php
class Table
	{
	private $page;
	private $title;
	private $tableclass;
	private $cellclass;
	private $borderclass;
	private $columnclasses;
	private $columns;
	private $data;
		
	function __construct(Page $page = null, string $title = null,
			string $tableclass = 'table', string $cellclass = 'tablecell',
			string $borderclass = 'tableborder',
			array $columnclasses = array('tablecolumnmedium'))
		{
		$this->page = $page;
		$this->title = $title;
		$this->tableclass = $tableclass;
		$this->cellclass = $cellclass;
		$this->borderclass = $borderclass;
		if(count($columnclasses) > 0)
			{
			$this->columnclasses = $columnclasses;
			}
		else
			{
			ErrorHandler::handle_error('No Column Widths supplied for new Table ' . $title . '!');
			}
		
		$this->columns = array();
		$this->data = array();
		}
	
	function add_columns(string ...$columns)
		{
		$this->columns[] = $columns;
		}
	
	function add_data_row(...$datarow)
		{
		$this->data[] = $datarow;
		}
		
	function add_data(array $data)
		{
		if(count($data) > 0)
			{	
			if(is_array($data[array_keys($data)[0]]))
				{	
				foreach($data as $datarow)
					{
					$this->data[] = $datarow;
					}
				}
			else
				{
				$this->data[] = $data;
				}
			}
		}
		
	function print()
		{
		echo('<div class="' . $this->tableclass . '">');
		if(!empty($this->page) && !empty($this->title))
			{
			$this->page->print_heading($this->title);
			}
		echo('<table class="' . $this->borderclass . '">');
	
		// Table Headers
		foreach($this->columns as $columnrow)
			{
			echo('<tr>');
			for($i = 0; $i < count($columnrow); ++$i)
				{
				echo('<th class="' . $this->borderclass . '">');
				echo('<div class="' . $this->cellclass . ' ' . $this->columnclasses[$i % count($this->columnclasses)] . '">');
				echo($columnrow[$i]);
				echo('</div>');
				echo('</th>');
				}
			echo('</tr>');
			}
	
		// Table Contents
		foreach($this->data as $datarow)
			{
			echo('<tr>');
			for($i = 0; $i < count($datarow); ++$i)
				{
				echo('<td class="' . $this->borderclass . '">');
				echo('<div class="' . $this->cellclass . ' ' . $this->columnclasses[$i % count($this->columnclasses)] . '">');
				if(is_string($datarow[$i]) || is_numeric($datarow[$i]) || is_bool($datarow[$i]))
					{
					echo($datarow[$i]);
					}
				else if(method_exists($datarow[$i], 'print'))
					{
					$datarow[$i]->print();
					}
				else
					{
					echo('Invalid Table Content');
					}
				echo('</div>');
				echo('</td>');
				}
			echo('</tr>');
			}
	
		echo('</table>');
		echo('</div>');
		}
		
	function print_query_result(Database $database, string $query, array $parameters = array())
		{
		$data = $database->query($query, $parameters);
		if(is_array($data) && count($data) > 0)
			{
			$columnnames = array_keys($data[0]);
			$multi = false;
			if(is_numeric($columnnames[0]))
				{
				$columnnames = array_keys($data[0][0]);
				$multi = true;
				}

			foreach($columnnames as $columnindex => $columnname)
				{
				$columnnames[$columnindex] = ucfirst($columnname);
				}
			$this->add_columns(...$columnnames);
			
			foreach($data as $datarow)
				{
				if(!$multi)
					{
					$this->add_data($datarow);
					}
				else
					{
					foreach($datarow as $datasubrow)
						{
						$this->add_data($datasubrow);
						}
					}
				}
				
			$this->print();
			}
		}
	}
?>