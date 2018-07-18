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
	function __construct( $name, $label )
	{
		// Call parent constructor
		parent::__construct( $name, $label );
	}
}

?>