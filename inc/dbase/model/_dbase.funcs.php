<?php

/**
 * Init dbase module parameters.
 * This functions should be used for each dbase controller.
 */
function dbase_ctrl_init()
{
	// Get current table's name (without prefixes)

	$table = param( 'dbt_ID', 'string', '', true );

	$linkdata = param( 'linkdata', 'string', '' );
	if( !empty( $linkdata ) )
	{
		$linkparams = explode( '-', $linkdata );
		$dbt_ID = $linkparams[3];
	}
	else
	{
		$dbt_ID = param( 'dbt_ID', 'integer', '' );
	}

	if( $dbt_ID )
	{	// Memorize param
		set_param( 'dbt_ID', $dbt_ID );
	}

	// Load PHP functions
	load_funcs( 'dbase/model/_dbase.types.php' );
	load_funcs( 'dbase/views/_dbase.form.views.php' );
	load_funcs( 'dbase/views/_dbase.table.views.php' );
}


/**
 * Get database metadata
 *
 * @param array database row
 * @return array
 */
function get_metadata( $db_row )
{
	$meta_data = array();

	$meta_data['name'] = $db_row->Field;

	preg_match( '/^(?<type>\w*)\({0,1}(?<length>\d*)\){0,1}\s{0,1}(?<unsigned>\w*)$/i', $db_row->Type, $match );

	$meta_data['type'] = $match['type'];

	if( ! empty( $match['length'] ) )
	{
		$meta_data['length'] = $match['length'];
	}

	$meta_data['unsigned'] = !empty( $match['unsigned'] ) && $match['unsigned'] == 'unsigned' ? 1 : 0;

	$meta_data['null'] = $db_row->Null == 'YES' ? 1 : 0;

	$meta_data['default'] = $db_row->Default;

	return $meta_data;
}


/**
 * Get the DbTableCache
 *
 * @return DbTableCache
 */
function & get_DbTableCache()
{
	global $DbTableCache;

	if( ! isset( $DbTableCache ) )
	{	// Cache doesn't exist yet:
		$DbTableCache = new DataObjectCache( 'DbTable', true, 'T_dbase__table', 'dbt_', 'dbt_ID', 'dbt_name', 'dbt_order ASC' );
	}

	return $DbTableCache;
}


/**
 * Get the DataCache
 *
 * @return DataCache
 */
function & get_DataCache()
{
	global $DataCache, $current_DbTable;

	if( ! isset( $DataCache ) && isset( $current_DbTable ) )
	{	// Cache doesn't exist yet:
		$DataCache = new DataObjectCache( 'Data', true, $current_DbTable->data_table_name,
								$current_DbTable->prefix, $current_DbTable->prefix.'ID' );
	}

	return $DataCache;
}


/**
 * Check numeric database value (tinyint, int, and etc.)
 *
 * @param variable name
 * @param variable value
 * @param max value length
 * @param unsigned or not
 * @param instance of Type class
 * @param required or not
 */
function param_check_db_number( $var_name, $var_value, $length, $unsigned, &$Type, $required = false )
{
	$numeric = param_check_number( $var_name, T_( 'The value must be numeric.' ), $required );

	if( $numeric && ( !empty( $var_value ) || $required ) )
	{	// Check numeric value

		if( $unsigned )
		{	// Set maximum and minimum values if unsigned type
			$min_value = $Type->min_unsigned;
			$max_value = $Type->max_unsigned;
		}
		else
		{	// Set maximum and minimum values if signed type
			$min_value = $Type->min_signed;
			$max_value = $Type->max_signed;
		}

		if( $var_value == NULL )
		{	// Value is empty
			param_error( $var_name, T_( 'The numeric field cannot be empty.' ) );
		}
		elseif( strlen( abs( $var_value ) ) > $length )
		{	// Value length exceed maximum allowed length
			param_error( $var_name, T_( 'Value length must be between 1 and ' ).$length.T_('.') );
		}
		elseif( $var_value < $min_value || $var_value > $max_value )
		{	// Value is not in allowed range
			param_error( $var_name, T_( 'Value must be between ' ).$min_value.T_( ' and ' ).$max_value.T_('.') );
		}
	}
}


/**
 * Check string database value (char, varchar)
 *
 * @param variable name
 * @param variable value
 * @param maximum length
 * @param required or not
 */
function param_check_db_string( $var_name, $var_value, $max_length = NULL, $required = false )
{
	if( !empty( $var_value ) || $required )
	{
		if( empty( $var_value ) && $required )
		{	// Value is empty
			param_error( $var_name, T_( 'The field cannot be empty.' ) );
		}
		elseif( $max_length != NULL && ( strlen( $var_value ) > $max_length ) )
		{	// Value length exceed maximum allowed length
			param_error( $var_name, T_( 'Value length must not exceed ' ).$max_length.'.' );
		}
	}
}


/**
 * Check database value
 *
 * @param variable name
 * @param instance of Type class
 * @return variable value
 */
function param_check_db_value( $var_name, &$Type, $length, $unsigned, $required )
{
	$validator = $Type->get_validator();

	$var_value = param( $var_name, 'string', '' );

	// Validate value using defined validator
	if( $validator == NULL || param_validate( $var_name, $validator, $required ) )
	{	// Validae value as database type
		if( $Type->is_signed() )
		{	// Validate numeric value
			$var_value = param( $var_name, 'string', NULL );
			param_check_db_number( $var_name, $var_value, $length, $unsigned, $Type, $required );
		}
		elseif( $Type->is_variable() )
		{	// Validate string value
			param_check_db_string( $var_name, $var_value, $length, $required );
		}
		else
		{	// Validate text value
			$var_value = param( $var_name, 'text', '' );
			param_check_db_string( $var_name, $var_value, $length, $required );
		}
	}

	return $var_value;
}


/**
 * Check type length
 * @param variable name
 * @param min length
 * @param max length
 * @param true if required
 */
function param_check_db_type_length( $var_name, $min_length, $max_length, $required = true )
{
	$var_value = param( $var_name, 'string', NULL );

	if( param_check_number( $var_name, T_('Length must be integer.' ), $required ) && ( $var_value != NULL ||  $required ) )
	{
		if( $var_value < $min_length || $var_value > $max_length )
		{	// Value length must be between max and min length
			param_error( $var_name, T_('Length can be between ').$min_length.T_(' and ').$max_length.T_('.') );
		}
	}

	return $var_value;
}


/**
 * Convert to db value
 * @param variable
 * @return mixed
 */
function db_val( $var, $string = false )
{
	if( $var == NULL )
	{
		return 'NULL';
	}
	else if( $string )
	{
		return '\''.$var.'\'';
	}
	else
	{
		return $var;
	}
}

?>