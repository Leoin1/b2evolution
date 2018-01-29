<?php
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * ForeignKeyType
 */
class ForeignKeyType extends NumericType
{

	/**
	 * Constructor
	 * @param name
	 * @param label
	 * @param maximum unsigned length
	 * @param maximum signed length
	 * @param maximum value for unsigned numeric type
	 * @param minimum value for unsigned numeric type
	 * @param maximum value for signed numeric type
	 * @param minimum value for signed numeric type
	 */
	function ForeignKeyType( $name, $label, $unsigned_lenght = 10, $signed_lenght = 10, $min_unsigned = 0, $max_unsigned = 4294967295, $min_signed = -2147483648, $max_signed = 2147483647 )
	{
		// Call parrent constructor
		parent::NumericType( $name, $label, $unsigned_lenght, $signed_lenght, $min_unsigned, $max_unsigned, $min_signed, $max_signed );
	}


	/**
	 * Get parent type name
	 * @return string
	 */
	function get_parent_name()
	{
		return 'int';
	}


	/**
	 * Return true as the type is user type
	 * @return boolean
	 */
	function is_user_type()
	{
		return true;
	}
}

?>