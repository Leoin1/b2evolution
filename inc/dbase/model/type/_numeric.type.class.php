<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * NumericType
 */
class NumericType extends Type
{
	/**
	 * Maximum and minimum values for unsigned numeric type
	 * @var integer
	 */
	var $min_unsigned;
	var $max_unsigned;
	var $unsigned_length;

	/**
	 * Maximum and minimum values for signed numeric type
	 * @var integer
	 */
	var $min_signed;
	var $max_signed;
	var $signed_length;


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
	function __construct( $name, $label, $unsigned_length, $signed_length, $min_unsigned, $max_unsigned, $min_signed, $max_signed )
	{
		// Call parent constructor
		parent::__construct( $name, $label );

		$this->unsigned_length = $unsigned_length;
		$this->signed_length = $signed_length;

		$this->min_unsigned = $min_unsigned;
		$this->max_unsigned = $max_unsigned;

		$this->min_signed = $min_signed;
		$this->max_signed = $max_signed;
	}


	/**
	 * Get validator
	 * @return string
	 */
	function get_validator()
	{
		return 'check_is_number';
	}
}

?>