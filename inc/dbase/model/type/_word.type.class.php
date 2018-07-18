<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * WordType
 */
class WordType extends StringType
{

	/**
	 * Constructor
	 * @param name
	 * @param label
	 */
	function __construct( $name, $label )
	{
		// Call parent constructor
		parent::__construct( $name, $label, 255 );
	}


	/**
	 * Get parent type name
	 * @return string
	 */
	function get_parent_name()
	{
		return 'varchar';
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
	 * Return true as this type must be quoted
	 * @return boolean
	 */
	function is_quoted()
	{
		return true;
	}


	/**
	 * Get validator
	 * @return string
	 */
	function get_validator()
	{
		return 'check_is_word';
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