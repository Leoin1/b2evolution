<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * ColumnMeta class
 * This class represents column metadata *
 */
class ColumnMeta
{
	/**
	 * Field Name
	 * @var string
	 */
	var $name;

	/**
	 * Field Length
	 * @var integer
	 */
	var $lenght;

	/**
	 * Signed / Unsigned
	 * @var integer
	 */
	var $unsigned;

	/**
	 * Required or not required
	 * @var boolean
	 */
	var $null;

	/**
	 * Default value
	 * @var mixed
	 */
	var $default;

	/**
	 * True if current column has meta data
	 * @var boolean
	 */
	var $meta;

	/**
	 * Field label
	 * @var string
	 */
	var $field_label;

	/**
	 * Table order
	 * @var float
	 */
	var $table_order;

	/**
	 * Form order
	 * @var float
	 */
	var $form_order;

	/**
	 * File root for image type
	 * @var string
	 */
	var $fileroot;

	/**
	 * Foreign key table
	 * @var string
	 */
	var $fk_table;

	/**
	 * Foreign key table prefix
	 * @var string
	 */
	var $fk_prefix;

	/**
	 * Foreign key table primary column name
	 * @var string
	 */
	var $fk_pri_name;


	/**
	 * Foreign key table name column
	 * @var string
	 */
	var $fk_name;

	/**
	 * Instance of Type class
	 * @var string
	 */
	var $Type;

	/**
	 * Temporary column name
	 * @var string
	 */
	var $tmp_name;


	/**
	 * Constructor
	 *
	 * @param database row
	 * @param string field type
	 * @param string field label
	 * @param float table order
	 * @param float form order
	 * @param string file root for image type
	 * @param string foreign key table name
	 * @param string foreign key table prefix
	 * @param string foreign key table primary column name
	 * @param string foreign key table name column
	 */
	function __construct( 	$db_row = NULL, $field_type = NULL, $field_label = NULL, $table_order = NULL, $form_order = NULL,
							$fileroot = NULL, $fk_table = NULL, $fk_prefix = NULL, $fk_pri_name = NULL, $fk_name = NULL )
	{
		global $dbase_types;

		if( $db_row != NULL )
		{
			$meta_data = get_metadata( $db_row );

			// Set name
			$this->name = $meta_data['name'];

			// Set type
			if( $field_type != NULL )
			{
				$this->Type = & $dbase_types[$field_type];
			}
			else
			{
				$this->Type = & $dbase_types[$meta_data['type']];
			}

			// Set length
			if( array_key_exists( 'length', $meta_data ) )
			{
				$this->lenght = $meta_data['length'];
			}

			// Set unsigned or signed
			$this->unsigned = $meta_data['unsigned'];

			// Set NULL or NOT NULL
			$this->null = $meta_data['null'];

			// Set default
			$this->default = $meta_data['default'];

			// Set visualization parameters
			if( $field_label != NULL )
			{
				$this->meta = true;
				$this->field_label = $field_label;
				$this->table_order = $table_order;
				$this->form_order = $form_order;

				$this->fileroot = $fileroot;

				$this->fk_table = $fk_table;
				$this->fk_prefix = $fk_prefix;
				$this->fk_pri_name = $fk_pri_name;
				$this->fk_name = $fk_name;
			}
		}
		else
		{
			$this->Type = & $dbase_types['varchar'];;
			$this->lenght = 50;
			$this->unsigned = 1;
			$this->null = 1;

			$this->set_default_order();
		}
	}


	/**
	 * Load data from request
	 */
	function load_from_Request()
	{
		global $current_DbTable, $dbase_types;

		// Fiel Label
		$this->field_label = param( $current_DbTable->meta_prefix.'field_label', 'string', NULL );
		param_check_not_empty( $current_DbTable->meta_prefix.'field_label' );

		// Table Order
		$this->table_order = param( $current_DbTable->meta_prefix.'table_order', 'string', NULL );
		param_check_decimal( $current_DbTable->meta_prefix.'table_order', T_('Value must be float or integer.'), false );

		// Form Order
		$this->form_order = param( $current_DbTable->meta_prefix.'form_order', 'string' );
		param_check_decimal( $current_DbTable->meta_prefix.'form_order', T_('Value must be float or integer.'), false );

		// Column Name
		if( param( $current_DbTable->meta_prefix.'tmp_name', 'string', NULL ) == NULL )
		{
			$this->name = strtolower ( param( $current_DbTable->meta_prefix.'name', 'string' ) );
			param_check_regexp( $current_DbTable->meta_prefix.'name', '#^[A-Za-z_]{1,}$#', T_( 'Name must be non empty letters parameter.' ) );
			$this->param_check_column_exists( $current_DbTable->meta_prefix.'name', $this->name );
		}
		else
		{
			$this->tmp_name = strtolower ( param( $current_DbTable->meta_prefix.'tmp_name', 'string' ) );
			param_check_regexp( $current_DbTable->meta_prefix.'tmp_name', '#^[A-Za-z_]{1,}$#', T_( 'Name must be non empty letters parameter.' ) );
			if( $this->name != $this->tmp_name )
			{
				$this->param_check_column_exists( $current_DbTable->meta_prefix.'tmp_name', $this->tmp_name );
			}
		}

		// Column Type
		$this->Type = & $dbase_types[param( $current_DbTable->meta_prefix.'type', 'string' )];

		// Column Unsigned/Signed
		$this->unsigned = param( $current_DbTable->meta_prefix.'unsigned', 'integer' );

		// Column Length
		if( $this->Type->is_variable() )
		{
			$this->lenght = param( $current_DbTable->meta_prefix.'length', 'string' );
			$this->param_check_type_length( $current_DbTable->meta_prefix.'length' );
		}

		// Column NULL
		$this->null = param( $current_DbTable->meta_prefix.'null', 'integer' );

		// Column Default
		if( $this->Type->is_default() )
		{
			$this->default = param_check_db_value( $current_DbTable->meta_prefix.'default', $this->Type, $this->lenght, $this->is_unsigned(), false );
		}

		// Specific params

		switch( $this->Type->name )
		{
			case 'image':
				$this->fileroot = param( $current_DbTable->meta_prefix.'fileroot', 'string' );
				break;
			case 'country':
				$this->default = param( $current_DbTable->meta_prefix.'default_country', 'string' );
				break;
			case 'foreign':
				$foreign_key = explode( '|', param( $current_DbTable->meta_prefix.'table', 'string' ) );
				$this->fk_table = $foreign_key[0];
				$this->fk_prefix = $foreign_key[1];
				$this->fk_pri_name = $foreign_key[2];
				$this->fk_name = $foreign_key[3];
				break;
		}

		return ! param_errors_detected();
	}


	/**
	 * Check is column name exist in the current table
	 *
	 * @param variable name
	 * @param field name
	 */
	function param_check_column_exists( $var, $name )
	{
		global $TableMeta, $current_DbTable;

		if( array_key_exists( $name, $TableMeta->ColumnMetas ) || in_array( $name, $TableMeta->reserver_column_names ) )
		{
			param_error( $var, T_( 'This field is already exists in the table.' ) );
		}
	}


	/**
	 * Check type length.
	 */
	function param_check_type_length( $var_name )
	{
		if( param_check_number( $var_name, T_('Length must be integer.' ), true ) )
		{
			$max_length = $this->get_max_length();
			if( $max_length != NULL && ( $this->lenght < 1 || $this->lenght > $max_length ))
			{
				param_error( $var_name, T_( 'The ').$this->Type->name.T_( ' length must be between 1 and ' ).$max_length.T_( '.' ) );
			}
		}
	}


	/**
	 * Set default order
	 */
	function set_default_order()
	{
		global $DB, $current_DbTable;

		$SQL = new SQL();
		$SQL->SELECT( 	'MAX('.$current_DbTable->meta_prefix.'column_number) + 1 AS table_order,
						 MAX('.$current_DbTable->meta_prefix.'order) + 1 AS form_order' );

		$SQL->FROM( $current_DbTable->meta_table_name );

		$row = $DB->get_row( $SQL->get() );

		$this->table_order = $row->table_order;
		$this->form_order = $row->form_order;

		if( empty( $this->table_order ) )
		{
			$this->table_order = '1.0';
		}

		if( empty( $this->form_order ) )
		{
			$this->form_order = '1.0';
		}
	}


	/**
	 * Get minimum length for current type
	 * @return integer
	 */
	function get_min_length()
	{
		if ( !$this->Type->is_variable() )
		{
			return NULL;
		}

		if( !empty( $this->lenght ) )
		{
			return $this->lenght;
		}
		else
		{
			return 1;
		}
	}


	/**
	 * Get maximum length for current type
	 * @return integer
	 */
	function get_max_length()
	{
		if ( !$this->Type->is_variable() )
		{
			return NULL;
		}

		if( $this->Type->is_signed() )
		{
			if( $this->is_unsigned() )
			{
				return $this->Type->unsigned_lenght;
			}
			else
			{
				return $this->Type->signed_lenght;
			}
		}
		else
		{
			return $this->Type->max_length;
		}
	}


	/**
	 * Return true if numeric value is unsigned
	 * @return boolean
	 */
	function is_unsigned()
	{
		if( !empty( $this->unsigned ) &&  $this->unsigned == 1 )
		{
			return true;
		}
		return false;
	}


	/**
	 * Return true if current column is NOT NULL
	 * @return boolean
	 */
	function is_required()
	{
		if( $this->null == 1 )
		{
			return false;
		}
		return true;
	}


	/**
	 * Get database type
	 * @return unknown_type
	 */
	function get_db_type()
	{
		global $dbase_types;

		if( $this->Type->is_user_type() )
		{
			return $dbase_types[$this->Type->get_parent_name()];
		}
		else
		{
			return $this->Type;
		}
	}


	/**
	 * Get database name
	 * @return string
	 */
	function get_db_name()
	{
		global $current_DbTable;
		return $current_DbTable->prefix.$this->name;
	}
}

?>