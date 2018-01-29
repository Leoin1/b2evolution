<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

load_class( '_core/ui/_uiwidget.class.php', 'Table' );
load_class( 'dbase/model/_columnmeta.class.php', 'ColumnMeta' );

/**
 * TableMeta class
 * This class represents table metadata
 */
class TableMeta extends Table
{
	/**
	 * Reserved column names such as ID and name
	 * @var array
	 */
	var $reserver_column_names;

	/**
	 * Column metas list
	 * @var array
	 */
	var $ColumnMetas;

	/**
	 * Current order parameter
	 * @var integer
	 */
	var $current_order;

	/**
	 * Current toggle parameter
	 * @var string
	 */
	var $current_togle;

	/**
	 * Name meta data
	 * @var array
	 */
	var $name_meta;


	/**
	 * Constructor
	 *
	 * @param reserved column names
	 */
	function __construct( $reserver_column_names = array( 'id', 'name' ) )
	{
		// Call parent constructor:
		parent::__construct();

		$this->reserver_column_names = $reserver_column_names;

		$this->load_columns_meta();
	}


	/**
	 * Initialize things in order to be ready for displaying.
	 *
	 * This is useful when manually displaying, i-e: not by using Results::display()
 	 *
	 * @param array ***please document***
	 * @param array Fadeout settings array( 'key column' => array of values ) or 'session'
 	 */
	/*
		function display_init( $display_params = NULL, $fadeout = NULL )
	{
		global $AdminUI;
		if( empty( $this->params ) && isset( $AdminUI ) )
		{ // Use default params from Admin Skin:
			$this->params = $AdminUI->get_template( 'Results' );
		}

		if( empty( $this->params ) )
		{
			$this->params = array();
		}

		// Initialize default params
		$this->params = array_merge( array(), $this->params );
	}

	function display_head()
	{
		pre_dump( $this->params['head_title'] );
		parent::display_head();
	}
  */

	/**
	 * Load columns meta data
	 *
	 * @return array
	 */
	function load_columns_meta()
	{
		global $DB, $current_DbTable;

		if( empty( $this->ColumnMetas ) )
		{
			// Load visualization info such as label, position in table and form and etc.
			$SQL = new SQL();
			$SQL->SELECT( '*' );
			$SQL->FROM( $current_DbTable->meta_table_name );

			$meta_prefix = $current_DbTable->meta_prefix;
			$field_name = $meta_prefix.'fieldname';

			$vis_list = array();
			foreach( $DB->get_results( $SQL->get() ) as $row )
			{
				foreach( $row as $key => $value )
				{
					$vis_list[$row->$field_name][$key] = $value;
				}
			}

			// Load meta info
			$this->ColumnMetas = array();
			foreach( $DB->get_results( 'SHOW COLUMNS FROM '.$current_DbTable->data_table_name ) as $row )
			{
				$column_name = strtolower( substr( $row->Field, strlen( $current_DbTable->prefix ) ) );

				if( !in_array( $column_name, $this->reserver_column_names ) )
				{
					// Can add column to meta table
					if( array_key_exists( $row->Field, $vis_list ) )
					{
						$vis = $vis_list[$row->Field];
					}

					$row->Field = $column_name;

					if( !empty( $vis ) )
					{
						// Column has meta information in meta table
						$ColumnMeta = new ColumnMeta( $row, 	$vis[$meta_prefix.'type'],
																$vis[$meta_prefix.'label'],
																$vis[$meta_prefix.'column_number'],
																$vis[$meta_prefix.'order'],
																$vis[$meta_prefix.'fileroot'],
																$vis[$meta_prefix.'fk_table'],
																$vis[$meta_prefix.'fk_prefix'],
																$vis[$meta_prefix.'fk_pri_name'],
																$vis[$meta_prefix.'fk_name'] );
					}
					else
					{	// Column has no meta information in meta table
						$ColumnMeta = new ColumnMeta( $row );
					}

					$ColumnMeta->TableMeta = &$this;
					$this->ColumnMetas[$column_name] = $ColumnMeta;
				}
				elseif( $column_name == 'name' )
				{
					$this->name_meta = get_metadata( $row );
				}
			}
		}

		return $this->ColumnMetas;
	}


	/**
	 * Create type string
	 * @param instance of ColumnMeta class
	 * @param true if create for database queries
	 * @return string
	 */
	function create_type( &$ColumnMeta, $db = true )
	{
		if( $db )
		{	// Get database type
			$Type = $ColumnMeta->get_db_type();
		}
		else
		{
			// Get user type if it exists
			$Type = $ColumnMeta->Type;
		}

		$strtype = $Type->name;

		// Column type length
		if( $Type->is_variable() )
		{
			$strtype .= '('.$ColumnMeta->lenght.')';
		}

		// Column type unsigned or signed
		if( $Type->is_signed() && $ColumnMeta->is_unsigned() )
		{
			$strtype .= ' unsigned';
		}

		if( $db )
		{
			// Colum NULL or NOT NULL
			if( $ColumnMeta->is_required() )
			{
				$strtype .= ' NOT NULL';
			}
			else
			{
				$strtype .= ' NULL';
			}

			// Column default
			if( $Type->is_default() )
			{
				if( $Type->is_quoted() && !empty( $ColumnMeta->default ) )
				{
					$strtype .= ' default \''.$ColumnMeta->default.'\'';
				}
				elseif( $Type->is_signed() && is_numeric( $ColumnMeta->default ))
				{
					$strtype .= ' default '.$ColumnMeta->default;
				}
			}
		}

		return $strtype;
	}


	/**
	 * Add new column to the table
	 * @param instance of ColumnMeta class
	 * @param after which column
	 */
	function db_add_column( &$ColumnMeta, $after_column_name = NULL )
	{
		global $DB, $current_DbTable;

		if( empty( $ColumnMeta->name ) )
		{
			return false;
		}

		$DB->begin();

		// Add column
		$sql = 'ALTER TABLE '.$current_DbTable->data_table_name.'
					ADD COLUMN '.$ColumnMeta->get_db_name().' '.$this->create_type( $ColumnMeta );

		if( !empty( $after_column_name ) )
		{	// Add current column afer defined column
			$sql .= ' AFTER '.$current_DbTable->prefix.$after_column_name;
		}

		$DB->query( $sql );

		$this->db_save_meta( $ColumnMeta );

		$DB->commit();

		return true;
	}


	/**
	 * Modify column
	 * @param intance of ColumnMeta class
	 */
	function db_modify_column( &$ColumnMeta )
	{
		global $DB, $current_DbTable;

		$DB->begin();

		//echo $ColumnMeta->name.'------'.$ColumnMeta->tmp_name;

		if( !empty( $ColumnMeta->tmp_name ) && $ColumnMeta->name != $ColumnMeta->tmp_name )
		{
			$sql_action = 'CHANGE';
			$new_column_name = $current_DbTable->prefix.$ColumnMeta->tmp_name;
		}
		else
		{
			$sql_action = 'MODIFY';
			$new_column_name = '';

		}

		$sql = 'ALTER TABLE '.$current_DbTable->data_table_name.'
						'.$sql_action.' '.$ColumnMeta->get_db_name().' '.$new_column_name.' '.$this->create_type( $ColumnMeta );

		$DB->query( $sql );

		$this->db_save_meta( $ColumnMeta );

		$DB->commit();

		return true;
	}


	/**
	 * Save meta
	 * @param instance of ColumnMeta class
	 * @return boolean
	 */
	function db_save_meta( &$ColumnMeta )
	{
		global $DB, $current_DbTable;

		$Type = & $ColumnMeta->Type;

		if( $Type->is_user_type() )
		{
			$field_type = '\''.$Type->name.'\'';
		}
		else
		{
			$field_type = 'NULL';
		}

		// Construct SQL queries paraneters
		$meta_values = array();

		if( !empty( $ColumnMeta->tmp_name ) && $ColumnMeta->name != $ColumnMeta->tmp_name )
		{
			$meta_values[$current_DbTable->meta_prefix.'fieldname'] = db_val( $current_DbTable->prefix.$ColumnMeta->tmp_name, true );
		}
		else
		{
			$meta_values[$current_DbTable->meta_prefix.'fieldname'] = db_val( $ColumnMeta->get_db_name(), true );
		}

		$meta_values[$current_DbTable->meta_prefix.'label'] = db_val( $ColumnMeta->field_label, true );
		$meta_values[$current_DbTable->meta_prefix.'type'] = $field_type;
		$meta_values[$current_DbTable->meta_prefix.'column_number'] = db_val( $ColumnMeta->table_order );
		$meta_values[$current_DbTable->meta_prefix.'order'] = db_val( $ColumnMeta->form_order );
		$meta_values[$current_DbTable->meta_prefix.'fileroot'] = db_val( $ColumnMeta->fileroot, true );
		$meta_values[$current_DbTable->meta_prefix.'fk_table'] = db_val( $ColumnMeta->fk_table, true );
		$meta_values[$current_DbTable->meta_prefix.'fk_prefix'] = db_val( $ColumnMeta->fk_prefix, true );
		$meta_values[$current_DbTable->meta_prefix.'fk_pri_name'] = db_val( $ColumnMeta->fk_pri_name, true );
		$meta_values[$current_DbTable->meta_prefix.'fk_name'] = db_val( $ColumnMeta->fk_name, true );


		if( !empty( $ColumnMeta->meta ) )
		{	// Update existing meta
			$set_values = array();
			foreach( $meta_values as $key => $value )
			{
				$set_values[] = $key.'='.$value;
			}

			$sql = 'UPDATE '.$current_DbTable->meta_table_name.'
						SET '.implode( ',', $set_values ).'
						WHERE '.$current_DbTable->meta_prefix.'fieldname = '.db_val( $current_DbTable->prefix.$ColumnMeta->name, true );
		}
		else
		{	// Insert new meta
			$sql = 'INSERT INTO '.$current_DbTable->meta_table_name.' ('.implode( ',', array_keys( $meta_values ) ).')
							VALUES ('.implode( ',', $meta_values ).')';
		}

		$DB->query( $sql );

		return true;
	}


	/**
	 * Set sorting options from request
	 */
	function set_col_sort_values()
	{
		$this->current_order = param( 'orderby', 'integer', 6 );
		$this->current_togle = param( 'order', 'string', 'DESC' );
	}


	/**
	 * Get sorting options
	 * @param key column
	 * @return array
	 */
	function get_col_sort_values( $key )
	{
		$col_sort_values['order_asc'] = regenerate_url( array( 'orderby', 'order' ), 'order=ASC&amp;orderby='.$key );
		$col_sort_values['order_desc'] = regenerate_url( array( 'orderby', 'order' ), 'order=DESC&amp;orderby='.$key );

		if( $key == $this->current_order )
		{
			$col_sort_values['current_order'] = $this->current_togle;
			if( $this->current_togle == 'ASC' )
			{
				$col_sort_values['order_toggle'] = $col_sort_values['order_desc'];
			}
			elseif( $this->current_togle == 'DESC' )
			{
				$col_sort_values['order_toggle'] = $col_sort_values['order_asc'];
			}
		}
		else
		{
			$col_sort_values['current_order'] = '';
			$col_sort_values['order_toggle'] = $col_sort_values['order_asc'];
		}

		return $col_sort_values;
	}


	/**
	 * Get sorted columns
	 * @return array
	 */
	function get_sorted_column_metas()
	{
		$this->osort( $this->ColumnMetas, $this->cols[$this->current_order]['order'], $this->current_togle );
		return $this->ColumnMetas;
	}


	/**
	 * Multidimensional object sorting
	 * @param object array
	 * @param field name
	 * @param direction e.g. ASC or DESC
	 */
	function osort( &$oarray, $field, $dir )
	{
    	usort( $oarray, create_function( '$a,$b', 'if ( $a->'.$field.' == $b->'
    		.$field.' ) return 0; if( \'ASC\' == \''.$dir.'\' ) { return ( $a->'.$field.' < $b->'
    		.$field.' ) ? -1 : 1; } else { return ( $a->'.$field.' > $b->'.$field.' ) ? -1 : 1; }' ) );
	}


	/**
	 * Delete column by its name
	 * @param column name
	 */
	function db_delete_column( $column_name )
	{
		global $DB, $current_DbTable;

		$DB->begin();

		$DB->query( 'ALTER TABLE '.$current_DbTable->data_table_name.' DROP COLUMN '.$current_DbTable->prefix.$column_name );
		$DB->query( 'DELETE FROM '.$current_DbTable->meta_table_name.' WHERE '.$current_DbTable->meta_prefix.'fieldname = \''.$current_DbTable->prefix.$column_name.'\'' );

		$DB->commit();

		return true;
	}


	/**
	 * Displays form to confirm deletion of column
	 *
	 * @param string Title for confirmation
	 * @param string "action" param value to use (hidden field)
	 * @param array Hidden keys (apart from "action")
	 * @param string most of the time we don't need a cancel action since we'll want to return to the default display
	 */
	function confirm_delete( $confirm_title, $crumb_name, $delete_action, $hiddens, $cancel_action = NULL )
	{
		global $Messages;

		$block_item_Widget = new Widget( 'block_item' );

		$block_item_Widget->title = $confirm_title;

		//echo str_replace( 'panel-default', 'panel-danger', $block_item_Widget->disp_template_replaced( 'block_start' ) );
		echo str_replace( 'panel-default', 'panel-danger', $block_item_Widget->replace_vars( $block_item_Widget->params[ 'block_start' ] ) );

		echo '<p class="warning text-danger">'.$confirm_title.'</p>';
		echo '<p class="warning text-danger">'.T_('THIS CANNOT BE UNDONE!').'</p>';

		$Form = new Form( '', 'form_confirm', 'get', '' );

		$Form->begin_form( 'inline' );
			$Form->add_crumb( $crumb_name );
			$Form->hiddens_by_key( $hiddens );
			$Form->hidden( 'action', $delete_action );
			$Form->hidden( 'confirm', 1 );
			$Form->button( array( 'submit', '', T_('I am sure!'), 'DeleteButton btn-danger' ) );
		$Form->end_form();

		$Form = new Form( '', 'form_cancel', 'get', '' );

		$Form->begin_form( 'inline' );
			$Form->hiddens_by_key( $hiddens );
			if( !empty( $cancel_action ) )
			{
				$Form->hidden( 'action', $cancel_action );
			}
			$Form->button( array( 'submit', '', T_('CANCEL'), 'CancelButton' ) );
		$Form->end_form();

		$block_item_Widget->disp_template_replaced( 'block_end' );
		return true;
	}
}

?>