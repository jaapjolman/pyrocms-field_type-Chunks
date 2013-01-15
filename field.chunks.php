<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * PyroStreams Chunks Field Type
 *
 * @package		PyroStreams
 * @author		42_antoine
 * @copyright	Copyright (c) 2013, 42_antoine
 * @license		http://cavaencoreparlerdebits.fr
 * @link		http://cavaencoreparlerdebits.fr
 */
class Field_chunks
{
	public $field_type_slug			= 'chunks';
	
	public $alt_process				= true;
	
	public $db_col_type				= false;

	public $custom_parameters		= array();

	public $version					= '0.1';

	public $author					= array('name'=>'42_antoine', 'url'=>'http://cavaencoreparlerdebits.fr');

	// --------------------------------------------------------------------------

	/**
	 * Process before saving to database
	 *
	 * @access	public
	 * @param	string
	 * @param	obj
	 * @param	obj
	 * @param	int
	 * @return	void
	 */
	public function pre_save($input, $field, $stream, $id)
	{
		$table_name		= $stream->stream_prefix.$stream->stream_slug.'_chunks';
		
		$chunk_slug		= $this->CI->input->post('chunk_slug');
		$chunk_class	= $this->CI->input->post('chunk_class');
		$chunk_type		= $this->CI->input->post('chunk_type');
		$chunk_body		= $this->CI->input->post('chunk_body');
		
		$i = 0;
		$chunks = array();
		foreach ($chunk_slug as $key => $chunk)
		{
			$chunks[$key]['sort']	= ++$i;
			$chunks[$key]['slug']	= $chunk;
			$chunks[$key]['class']	= $chunk_class[$key];
			$chunks[$key]['type']	= $chunk_type[$key];
			$chunks[$key]['container_id']	= $id;
			
			if ($key == $stream->title_column)
			{
				// allow field/form validation
				$chunks[$key]['body'] = $this->CI->input->post($stream->title_column);
			}
			else
			{
				$chunks[$key]['body'] = $chunk_body[$key];	
			}
			
			// have to parse content
			$chunks[$key]['parsed'] = ($chunks[$key]['type'] == 'markdown') ? parse_markdown($chunks[$key]['body']) : '';
		}

/*
		if (is_numeric($row_id = $this->CI->input->post('row_edit_id')))
		{
			$this->CI->db->where('row_id', $this->CI->input->post('row_edit_id'))->delete($table_name);
		}
		else
		{
			$row_id = $id;
		}
*/

		// transaction
		foreach ($chunks as $chunk)
		{
			$this->CI->db->insert($table_name, $chunk);
			// rollback
		}
		// endtransaction
	}

	// --------------------------------------------------------------------------

	/**
	 * Process before outputting to the backend
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function alt_pre_output($row_id, $extra, $type, $stream)
	{
		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Alt Plugin Process
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function alt_process_plugin($data)
	{
		
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Join multiple hook
	 */
	public function join_multiple($data)
	{
		
	}

	// --------------------------------------------------------------------------

	/**
	 * Event
	 *
	 * @access	public
	 * @return	void
	 */
	public function event()
	{
		$this->CI->type->add_css('chunks', 'chunks.css');
		
		if (defined('ADMIN_THEME'))
		{
			$this->CI->type->add_misc($this->CI->type->load_view('wysiwyg', 'wysiwyg_admin', null));
		}
		else
		{
			$this->CI->type->add_misc($this->CI->type->load_view('wysiwyg', 'wysiwyg_entry_form', null));
		}
		
		$this->CI->type->add_js('chunks', 'chunks.js');
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Process for when adding field assignment
	 */
	public function field_assignment_construct($field, $stream)
	{
		$this->CI->load->dbforge();

		// Make a table
		$table_name = $stream->stream_prefix.$stream->stream_slug.'_chunks';

		$fields = array(
	        'id' => array(
	                 'type' => 'INT',
	                 'constraint' => 11, 
	                 'unsigned' => true,
	                 'auto_increment' => true),
	        'slug' => array(
	                 'type' => 'VARCHAR',
	                 'constraint' => 255),
	        'class' => array(
	                 'type' => 'VARCHAR',
	                 'constraint' => 255),
	        'container_id' => array(
	                 'type' => 'INT',
	                 'constraint' => 11),
	        'body' => array(
	                 'type' => 'TEXT'),
	        'parsed' => array(
	                 'type' => 'TEXT',
	                 'null' => 'true'),
	        'type' => array(
	         		 'type' => 'ENUM',
	         		 'constraint' => array('html','markdown','wysiwyg-advanced','wysiwyg-simple'),
	         		 'default' => 'wysiwyg-advanced'),
	        'sort' => array(
	                 'type' => 'INT',
	                 'constraint' => 11)
		);
		
		$this->CI->dbforge->add_field($fields);
		$this->CI->dbforge->add_key('id', TRUE);
		
		$this->CI->dbforge->create_table($table_name);
	}

	// --------------------------------------------------------------------------

	/**
	 * Process for when removing field assignment
	 *
	 * @access	public
	 * @param	obj
	 * @param	obj
	 * @return	void
	 */
	public function field_assignment_destruct($field, $stream)
	{
		// Get the table name
		$table_name = $stream->stream_prefix.$stream->stream_slug.'_chunks';
		
		// Remove the table
		$this->CI->dbforge->drop_table($table_name);
	}

	// --------------------------------------------------------------------------

	/**
	 * Entry delete
	 *
	 * @access	public
	 * @param	obj
	 * @param	obj
	 * @return	void
	 */
	public function entry_destruct($entry, $field, $stream)
	{

	}

	// --------------------------------------------------------------------------

	/**
	 * Process renaming column
	 *
	 * @access	public
	 * @param	obj
	 * @param	obj
	 * @return	void
	 */
	public function alt_rename_column($field, $stream)
	{
		return null;
	}

	// --------------------------------------------------------------------------

	/**
	 * Output form input
	 *
	 * @access	public
	 * @param	array
	 * @return	string
	 */
	public function form_output($data, $entry_id, $field)
	{
		$chunk['type']	= 'wysiwyg-advanced';
	
		$chunk['slug'] 	= $data['form_slug'];
		$chunk['id']	= $data['form_slug'];
		$chunk['body']	= $data['value'];
		$chunk['class']	= '';
		
		$options = array(
			'id'	=> $chunk['slug'].'_'.$chunk['id'],
			'name'	=> $chunk['id'],
			'value'	=> $chunk['body'],
			'rows'	=> 20,
			'class'	=> $chunk['type'],
			'style'	=> 'width:100%'
		);
		
		//var_dump($data); var_dump($entry_id); var_dump($field); exit();
		
		$html = "<ul id='chunks'>";
		
		$html .= "<li class='chunk'>
					".form_input('chunk_slug['.$chunk['id'].']', $chunk['slug'], 'class="label" placeholder="id"')."
					".form_input('chunk_class['.$chunk['id'].']', $chunk['class'], 'class="label" placeholder="class"')."
					".form_dropdown('chunk_type['.$chunk['id'].']', array(
						'html' => 'html',
						'markdown' => 'markdown',
						'wysiwyg-simple' => 'wysiwyg-simple',
						'wysiwyg-advanced' => 'wysiwyg-advanced',
					), $chunk['type']) ."
					<div class='alignright'>
						<a href='javascript:void(0)' class='remove-chunk btn red'>".lang('global:remove')."</a>
						<span class='sort-handle'></span>
					</div>
					<br style='clear:both' />
					<span class='chunky'>
						".form_textarea($options)."
					</span>
				</li>";
				
		$html .= "</ul><a class='add-chunk btn orange' href='#'>".lang('streams.add_chunk')."</a>";
			
		return $html;
	}
}

/* End of file field.chunks.php */