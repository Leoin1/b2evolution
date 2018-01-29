<?php


/**
 * Get presentation of variable for the table
 * @param value
 * @param column
 * @param type name
 * @return string
 */
function dbase_table( $value, $column, $type_name )
{
	$function = 'dbase_table_'.$type_name;

	if( function_exists( $function ) )
	{
		return $function( $value, $column );
	}
	else
	{
		debug_die( 'Function '.$validator.'() does not exist!' );
	}
}


/**
 * Get tinyint table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_tinyint( $value, $column )
{
	if( !empty( $value ) )
	{
		return $value;
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get checkbox table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_checkbox( $value, $column )
{
	$checked = $value > 0 ? 'checked' : '';
	return '<input type="checkbox" '.$checked.' disabled="true"/>';
}


/**
 * Get smallint table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_smallint( $value, $column )
{
	return dbase_table_tinyint( $value, $column );
}


/**
 * Get mediumint table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_mediumint( $value, $column )
{
	return dbase_table_tinyint( $value, $column );
}


/**
 * Get int table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_int( $value, $column )
{
	return dbase_table_tinyint( $value, $column );
}


/**
 * Get bigint table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_bigint( $value, $column )
{
	return dbase_table_tinyint( $value, $column );
}


/**
 * Get char table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_char( $value, $column )
{
	if( !empty( $value ) )
	{
		return $value;
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get varchar table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_varchar( $value, $column )
{
	return dbase_table_char( $value, $column );
}


/**
 * Get text table presentation
 * @param $value
 * @param column
 * @return unknown_type
 */
function dbase_table_text( $value, $column )
{
	if( !empty( $value ) )
	{
		return nl2br( $value );
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get date table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_date( $value, $column )
{
	if( !empty( $value ) )
	{
		return $value;
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get time table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_time( $value, $column )
{
	return dbase_table_date( $value, $column );
}


/**
 * Get datetime table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_datetime( $value, $column )
{
	return dbase_table_date( $value, $column );
}


/**
 * Get timestamp table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_timestamp( $value, $column )
{
	return dbase_table_datetime( $value, $column );
}


/**
 * Get email table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_email( $value, $column )
{
	if( !empty( $value ) )
	{
		return action_icon( T_('Email').': '.$value, 'email', 'mailto:'.$value, T_('Email') );
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get phone table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_phone( $value, $column )
{
	return dbase_table_char( $value, $column );
}


/**
 * Get url table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_url( $value, $column )
{
	if( !empty( $value ) )
	{
		return '<a href="'.$value.'" target="_blank">'.$value.'</a>';
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get word table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_word( $value, $column )
{
	return dbase_table_char( $value, $column );
}


/**
 * Get file table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_file( $value, $column )
{
	return get_file_view_link( $value );
}


/**
 * Get image table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_image( $value, $column )
{
	global $TableMeta;

	if( !empty( $value ) )
	{
		$ColumnMeta = & $TableMeta->ColumnMetas[$column];
		return get_image_view_link( $value, $ColumnMeta );
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get country table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_country( $value, $column )
{
	return dbase_table_char( $value, $column );
}


/**
 * Get foreign key table presentation
 * @param value
 * @param column
 * @return string
 */
function dbase_table_foreign( $value, $column )
{
	global $DB, $TableMeta;

	if( !empty( $value ) )
	{
		$ColumnMeta = & $TableMeta->ColumnMetas[$column];

		$pri_column_name = $ColumnMeta->fk_prefix.$ColumnMeta->fk_pri_name;
		$column_name = $ColumnMeta->fk_prefix.$ColumnMeta->fk_name;

		$SQL = new SQL();
		$SQL->SELECT( $column_name );
		$SQL->FROM( $ColumnMeta->fk_table );
		$SQL->WHERE( $pri_column_name.'='.$value );

		return $DB->get_var( $SQL->get() );
	}
	else
	{
		return '&nbsp;';
	}
}


/**
 * Get css class by column type
 * @param type name
 * @return sring
 */
function dbase_css( $type_name )
{
	switch ( $type_name )
	{
		case 'tinyint':
		case 'smallint':
		case 'mediumint':
		case 'int':
		case 'bigint':
			// Align right
			return 'right';
			break;

		case 'date':
		case 'time':
		case 'datetime':
		case 'timestamp':
		case 'email':
		case 'url':
		case 'file':
		case 'image':
		case 'country':
		case 'foreign':
		case 'checkbox':
			// Align center
			return 'center';
			break;

		case 'varchar':
		case 'char':
		case 'text':
		case 'phone':
		case 'word':
		default:
			// Default align left
			return 'left';
			break;
	}
}


?>