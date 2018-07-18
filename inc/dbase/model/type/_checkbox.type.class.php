<?php

/**
 * CheckboxType
 */
class CheckboxType extends NumericType
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
	function __construct( $name, $label, $unsigned_length = 3, $signed_length = 3, $min_unsigned = 0, $max_unsigned = 255, $min_signed = -128, $max_signed = 127 )
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
		return 'tinyint';
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