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
	var $unsigned_lenght;

	/**
	 * Maximum and minimum values for signed numeric type
	 * @var integer
	 */
	var $min_signed;
	var $max_signed;
	var $signed_lenght;


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
	function NumericType( $name, $label, $unsigned_lenght, $signed_lenght, $min_unsigned, $max_unsigned, $min_signed, $max_signed )
	{
		// Call parrent constructor
		parent::Type( $name, $label );

		$this->unsigned_lenght = $unsigned_lenght;
		$this->signed_lenght = $signed_lenght;

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