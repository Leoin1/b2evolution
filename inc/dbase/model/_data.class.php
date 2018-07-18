<?php


if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

load_class( '_core/model/dataobjects/_dataobject.class.php', 'DataObject' );

/**
 * Data class
 * This class represents data
 *
 */
class Data extends DataObject
{
	/**
	 * Data name
	 * @var string
	 */
	var $name;

	/**
	 * Values
	 * @var array
	 */
	var $values;


	/**
	 * Constructor
	 *
	 * @param database row
	 */
	function __construct( $db_row = NULL )
	{
		global $current_DbTable;

		// Call parent constructor:
		parent::__construct( $current_DbTable->data_table_name, $current_DbTable->prefix, $current_DbTable->prefix.'ID' );

		$this->values = array();
		if( $db_row != NULL )
		{	// Set data from database
			foreach ( $db_row as $key => $value )
			{
				if( $key == $current_DbTable->prefix.'ID')				{

					$this->ID = $value;
				}
				elseif ( $key == $current_DbTable->prefix.'name' )
				{
					$this->name = $value;
				}
				else
				{
					$this->values[$key] = $value;
				}
			}
		}
	}


	/**
	 * Load data from request
	 */
	function load_from_Request()
	{
		global $TableMeta, $current_DbTable;

		// Name
		$this->name = param( $current_DbTable->prefix.'name', 'string' );
		param_check_not_empty( $current_DbTable->prefix.'name', T_(' The name cannot be empty.') );

		// Field/Columns
		$this->values = array();
		foreach( $TableMeta->ColumnMetas as $ColumnMeta )
		{
				$var_name = $ColumnMeta->get_db_name();

				$this->values[$var_name] = param_check_db_value( $var_name, $ColumnMeta->Type, $ColumnMeta->length, $ColumnMeta->is_unsigned(), $ColumnMeta->is_required() );
		}

		return !param_errors_detected();
	}


	/**
	 * Prepare column values to DB query
	 *
	 * @return values array
	 */
	function prepare_column_values()
	{
		global $TableMeta, $current_DbTable;

		$column_values = array();
		$column_values[$current_DbTable->prefix.'name'] = db_val( $this->name, true );
		foreach( $this->values as $key => $value )
		{
			$ColumnMeta = & $TableMeta->ColumnMetas[substr( $key, strlen( $current_DbTable->prefix ) )];
			$column_values[$key] = db_val( $value, $ColumnMeta->Type->is_quoted() );
		}
		return $column_values;
	}


	/**
	 * Insert or update data in database
	 *
	 * @return boolean
	 */
	function dbsave()
	{
		if( $this->ID == 0 )
		{
			return $this->dbinsert();
		}
		else
		{
			return $this->dbupdate();
		}
	}


	/**
	 * Insert data to database
	 *
	 * @return boolean
	 */
	function dbinsert()
	{
		global $DB, $current_DbTable;

		if( $this->ID != 0 ) { debug_die( 'Existing object/object with an ID cannot be inserted!' ); }

		$values = $this->prepare_column_values();

		$sql = 'INSERT INTO '.$current_DbTable->data_table_name.' ('.implode( ',', array_keys( $values ) ).') ';
		$sql .= 'VALUES ('.implode( ',', $values ).')';

		if( ! $DB->query( $sql, 'Data::dbinsert()' ) )
		{
			return false;
		}

		$this->ID = $DB->insert_id;

		return true;
	}


	/**
	 * Update data in database
	 *
	 * @return boolean
	 */
	function dbupdate()
	{
		global $DB, $current_DbTable;

		if( $this->ID == 0 ) { debug_die( 'New object cannot be updated!' ); }

		$set_values = array();
		foreach( $this->prepare_column_values() as $key => $value )
		{
			$set_values[] = $key.'='.$value;
		}

		$sql = 'UPDATE '.$current_DbTable->data_table_name.' SET '.implode( ',', $set_values ).' ';
		$sql .= 'WHERE '.$current_DbTable->prefix.'ID = '.$this->ID;

		if( ! $DB->query( $sql, 'Data::dbupdate()' ) )
		{
			return false;
		}

		return true;
	}


	/**
	 * Delete data from database
	 *
	 * @param boolean
	 */
	function dbdelete()
	{
		global $DB, $current_DbTable, $Messages, $db_config;

		if( $this->ID == 0 ) { debug_die( 'Non persistant object cannot be deleted!' ); }

		$sql = 'DELETE FROM '.$current_DbTable->data_table_name.' WHERE '.$current_DbTable->prefix.'ID = '.$this->ID;

		$DB->query( $sql );

		$this->ID = 0;

		return true;
	}


	/**
	 * Check existence of specified name in db_name unique field.
	 *
	 * @return int ID if name exists otherwise NULL/false
	 */
	function dbexists( $unique_fields, $values )
	{
		global $current_DbTable;

		return parent::dbexists( $current_DbTable->prefix.'name', $this->name );
	}
}

?>