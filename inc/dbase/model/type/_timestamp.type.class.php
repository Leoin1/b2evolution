<?php

/**
 * TimestampType
 */
class TimestampType extends DateTimeType
{
	/**
	 * Constructor
	 * @param name
	 * @param label
	 */
	function TimestampType( $name, $label )
	{
		// Call parrent constructor
		parent::DateTimeType( $name, $label );
	}
}

?>