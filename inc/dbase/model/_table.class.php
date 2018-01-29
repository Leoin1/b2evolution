<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

load_class( '_core/model/dataobjects/_dataobject.class.php', 'DataObject' );

/**
 * DbTable class
 */
class DbTable extends DataObject
{
	/**
	 * Name
	 * @var string
	 */
	var $name;

	/**
	 * Description
	 * @var string
	 */
	var $description;

	/**
	 * Table name
	 * @var string
	 */
	var $table;

	/**
	 * Data column prefix
	 * @var string
	 */
	var $prefix;

	/**
	 * Meta column prefix
	 * @var string
	 */
	var $meta_prefix;

	/**
	 * Default order column
	 * @var string
	 */
	var $order;

	/**
	 * Data table name
	 * @var string
	 */
	var $data_table_name;

	/**
	 * Meta table name
	 * @var string
	 */
	var $meta_table_name;

	/**
	 * Temporary table name
	 * @var string
	 */
	var $tmp_table;


	/**
	 * Constructor
	 *
	 * @param object database row
	 */
	function __construct( $db_row = NULL )
	{
		// Call parent constructor:
		parent::__construct( 'T_dbase__table', 'dbt_', 'dbt_ID' );

		if( $db_row )
		{
			$this->ID            = $db_row->dbt_ID;
			$this->name          = $db_row->dbt_name;
			$this->description   = $db_row->dbt_description;
			$this->table         = $db_row->dbt_table;
			$this->tmp_table     = $db_row->dbt_table;
			$this->prefix        = $db_row->dbt_prefix;
			$this->order         = $db_row->dbt_order;
		}
		else
		{
			$this->set_param( 'prefix', 'string', 'db_', true );
			$this->set_default_order();
		}

		// efy-maxim> Meta table prefix can be defined in the same way as data table prefix.
		// efy-maxim> To implement configurable meta table prefix, add additional meta prefix field to DB.
		$this->meta_prefix = 'dbm_';

		$this->create_table_names();
	}


	/**
	 * Load data from Request form fields.
	 *
	 * @return boolean true if loaded data seems valid.
	 */
	function load_from_Request()
	{
		// Name
		$this->set_string_from_param( 'name', true );

		// Description
		$this->set_string_from_param( 'description', false );

		// Table
		$this->set_string_from_param( 'table', false );
		param_check_regexp( 'dbt_table', '#^[A-Za-z]{1,}$#', T_( 'Table name must be non empty letters parameter.' ) );

		// Prefix
		$this->set_string_from_param( 'prefix', false );
		param_check_regexp( 'dbt_prefix', '#^[A-Za-z_]{1,}$#', T_( 'Prefix must be non empty letters parameter.' ) );

		// Order
		$this->set_string_from_param( 'order', false );
		param_check_decimal( 'dbt_order', T_( 'Value must be float.' ), false );

		$this->create_table_names();

		return ! param_errors_detected();
	}


	/**
	 * Set param value
	 *
	 * By default, all values will be considered strings
	 *
	 * @param string parameter name
	 * @param mixed parameter value
	 * @param boolean true to set to NULL if empty value
	 * @return boolean true, if a value has been set; false if it has not changed
	 */
	function set( $parname, $parvalue, $make_null = false )
	{
		switch( $parname )
		{
			case 'table':
			case 'prefix':
				$parvalue = strtolower( $parvalue );
			default:
				return $this->set_param( $parname, 'string', $parvalue, $make_null );
		}
	}


	/**
	 * Create table names
	 */
	function create_table_names()
	{
		global $db_table_prefix, $db_data_table_suffix, $db_meta_table_suffix;

		$this->data_table_name = $db_table_prefix.$this->table.$db_data_table_suffix;
		$this->meta_table_name = $db_table_prefix.$this->table.$db_meta_table_suffix;
	}


	/**
	 * Check existence of specified currency code in curr_code unique field.
	 *
	 * @return int ID if currency code exists otherwise NULL/false
	 */
	function dbexists( $unique_fields, $values )
	{
		return parent::dbexists( 'dbt_table', $this->table );
	}


	/**
	 * Get currency unique name (code).
	 *
	 * @return string currency code
	 */
	function get_name()
	{
		return $this->name;
	}


	/**
	 * Set default order
	 */
	function set_default_order()
	{
		global $DB;

		$SQL = new SQL();
		$SQL->SELECT( 'MAX(dbt_order) + 1 AS tab_order' );
		$SQL->FROM( 'T_dbase__table' );

		$row = $DB->get_row( $SQL->get() );

		$order = $row->tab_order;
		if( empty( $order ) )
		{
			$order = '1.0';
		}

		$this->set_param( 'order', 'string', $order, true );
	}


	/**
	 * Create data and meta tables and also insert instance of DbTable class  into database
	 */
	function dbinsert()
	{
		global $DB, $db_table_prefix;
		global $db_data_table_suffix, $db_meta_table_suffix;

		// Create data table
		$DB->query( 'CREATE TABLE '.$db_table_prefix.$this->table.$db_data_table_suffix.' (
						'.$this->prefix.'ID int(10) unsigned NOT NULL auto_increment,
						'.$this->prefix.'name varchar(255) NOT NULL,
						PRIMARY KEY '.$this->prefix.'ID ('.$this->prefix.'ID),
						UNIQUE ('.$this->prefix.'name)
					) ENGINE = innodb' );

		// Create meta table
		$DB->query( 'CREATE TABLE '.$db_table_prefix.$this->table.$db_meta_table_suffix.' (
						'.$this->meta_prefix.'fieldname varchar(30) NOT NULL,
						'.$this->meta_prefix.'label varchar(40) NOT NULL,
						'.$this->meta_prefix.'type varchar(20) NULL,
						'.$this->meta_prefix.'column_number float(4,1) unsigned NULL,
						'.$this->meta_prefix.'order float(4,1) unsigned NULL,
						'.$this->meta_prefix.'fileroot varchar(50) NULL,
						'.$this->meta_prefix.'fk_table varchar(50) NULL,
						'.$this->meta_prefix.'fk_prefix varchar(10) NULL,
						'.$this->meta_prefix.'fk_pri_name varchar(10) NULL,
						'.$this->meta_prefix.'fk_name varchar(40) NULL,
						INDEX('.$this->meta_prefix.'fieldname)
					) ENGINE = innodb' );

		// Insert table to tables list in database
		if( ! parent::dbinsert() )
		{
			$DB->rollback();
			return false;
		}

		return true;
	}


	/**
	 * Update table(s)
	 */
	function dbupdate()
	{
		global $DB, $db_table_prefix;
		global $db_data_table_suffix, $db_meta_table_suffix;

		// Rename tables
		if( $this->table != $this->tmp_table )
		{
			$DB->query( 'RENAME TABLE '.$db_table_prefix.$this->tmp_table.$db_data_table_suffix.
							' TO '.$db_table_prefix.$this->table.$db_data_table_suffix );

			$DB->query( 'RENAME TABLE '.$db_table_prefix.$this->tmp_table.$db_meta_table_suffix.
							' TO '.$db_table_prefix.$this->table.$db_meta_table_suffix );
		}

		// Update table in database
		if( ! parent::dbupdate() )
		{
			$DB->rollback();
			return false;
		}

		return true;
	}


	/**
	 * Delete data and meta tables and also delete instance of DbTable class from database
	 */
	function dbdelete()
	{
		global $DB, $db_table_prefix;
		global $db_data_table_suffix, $db_meta_table_suffix;

		$DB->begin();

		// Drop data table
		$DB->query( 'DROP TABLE '.$db_table_prefix.$this->table.$db_data_table_suffix );

		// Drop meta table
		$DB->query( 'DROP TABLE '.$db_table_prefix.$this->table.$db_meta_table_suffix );

		// Delete table from tables list in database
		if( ! parent::dbdelete() )
		{
			$DB->rollback();
			return false;
		}

		$DB->commit();
		return true;
	}
}

?>