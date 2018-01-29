<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );


/**
 * TextType
 */
class TextType extends Type
{

	/**
	 * Constructor
	 * @param name
	 * @param label
	 */
	function TextType( $name, $label )
	{
		// Call parrent constructor
		parent::Type( $name, $label );
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
	 * Return true as the type is text
	 * @return boolean
	 */
	function is_text()
	{
		return true;
	}


	/**
	 * Return false as the type can't have default value
	 * @return boolean
	 */
	function is_default()
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