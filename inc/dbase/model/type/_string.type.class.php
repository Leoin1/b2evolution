<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * StringType
 */
class StringType extends Type
{
	/**
	 * Maximum length
	 * @var intefer
	 */
	var $max_length;


	/**
	 * Constructor
	 * @param name
	 * @param label
	 * @param maximum length
	 */
	function StringType( $name, $label, $max_length )
	{
		// Call parrent constructor
		parent::Type( $name, $label );

		$this->max_length = $max_length;
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
}

?>