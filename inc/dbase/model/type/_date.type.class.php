<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * DateType
 */
class DateType extends Type
{
	/**
	 * Constructor
	 * @param name
	 * @param label
	 */
	function __construct( $name, $label )
	{
		// Call parent constructor
		parent::__construct( $name, $label );
	}


	/**
	 * Return false as this type is unsigned
	 * @return boolean
	 */
	function is_signed()
	{
		return false;
	}


	/**
	 * Return false as the type length is not variable
	 * @return boolean
	 */
	function is_variable()
	{
		return false;
	}


	/**
	 * Return true as the type can have default value
	 * @return boolean
	 */
	function is_default()
	{
		return true;
	}


	/**
	 * Return true as this type must be quoted
	 * @return boolean
	 */
	function is_quoted()
	{
		return true;
	}
}

?>