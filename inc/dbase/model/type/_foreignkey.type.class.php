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
	function __construct( $name, $label, $unsigned_length = 10, $signed_length = 10, $min_unsigned = 0, $max_unsigned = 4294967295, $min_signed = -2147483648, $max_signed = 2147483647 )
	{
		// Call parent constructor
		parent::__construct( $name, $label, $unsigned_length, $signed_length, $min_unsigned, $max_unsigned, $min_signed, $max_signed );
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