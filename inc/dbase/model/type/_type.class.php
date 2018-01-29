<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * This base class for MySQL data type classes or user type classes
 */
class Type
{
	/**
	 * Type name
	 * @var string
	 */
	var $name;


	/**
	 * Type label
	 * @var string
	 */
	var $label;


	/**
	 * Constructor
	 * @param name
	 * @param label
	 */
	function Type( $name, $label )
	{
		$this->name = $name;
		$this->label = $label;
	}


	/**
	 * Return true if type can be signed or unsigned
	 * @return boolean
	 */
	function is_signed()
	{
		return true;
	}


	/**
	 * Return true if type length is variable
	 * @return boolean
	 */
	function is_variable()
	{
		return true;
	}


	/**
	 * Return true if type can have default value
	 * @return boolean
	 */
	function is_default()
	{
		return true;
	}


	/**
	 * Return true if type must be quoted before database insert/update
	 * @return boolean
	 */
	function is_quoted()
	{
		return false;
	}

	/**
	 * Return true if type is text
	 * @return boolean
	 */
	function is_text()
	{
		return false;
	}


	/**
	 * Get validator function
	 * @return string
	 */
	function get_validator()
	{
		return NULL;
	}


	/**
	 * Return true if user type
	 * @return boolean
	 */
	function is_user_type()
	{
		return false;
	}


	/**
	 * Get parent type name
	 * @return string
	 */
	function get_parent_name()
	{
		return NULL;
	}
}

?>