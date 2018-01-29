<?php


/**
 * Create form field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Type = & $ColumnMeta->Type;

	$function = 'dbase_form_'.$Type->name;

	if( function_exists( $function ) )
	{
		return $function( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
	}
	else
	{
		debug_die( 'Function '.$validator.'() does not exist!' );
	}
}


/**
 * Tinyint field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_tinyint( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$maxlength = $ColumnMeta->lenght;
	if( !$ColumnMeta->is_unsigned() )
	{
		$maxlength++;
	}
	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 25, $label, $TableMeta->create_type( $ColumnMeta, false ), array( 'maxlength' => $maxlength, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Checkbox field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_checkbox( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$checked = get_value( $Data, $ColumnMeta ) > 0 ? true : false;
	$Form->checkbox_input( $ColumnMeta->get_db_name(), $checked, $label, array( 'value' => 1 ) );
}


/**
 * Smallint field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_smallint( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_tinyint( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Mediumint field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_mediumint( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_tinyint( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Int field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_int( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_tinyint( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Bigint field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_bigint( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_tinyint( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Char field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_char( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 50, $label, $TableMeta->create_type( $ColumnMeta, false ), array( 'maxlength' => $ColumnMeta->lenght, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Varchar field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_varchar( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_char( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Text field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_text( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Form->textarea_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 7, $label, array( 'cols' => 80, 'required' => $ColumnMeta->is_required(), 'note' => $TableMeta->create_type( $ColumnMeta, false ) ) );
}


/**
 * Date field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_date( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 10, $label, '(hh:mm:ss)', array( 'maxlength' => 8, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Time field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_time( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 8, $label, '(yyyy-mm-dd)', array( 'maxlength' => 10, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Datetime field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_datetime( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 19, $label, '(yyyy-mm-dd hh:mm:ss)', array( 'maxlength' => 19, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Timestamp field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_timestamp( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_datetime( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * Email field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_email( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	if( !empty( $value ) && is_email( $value ) )
	{
		$note = action_icon( T_('Email').': '.$value, 'email', 'mailto:'.$value, T_('Email') );
	}
	else
	{
		$note = $TableMeta->create_type( $ColumnMeta, false );
	}

	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 50, $label, $note, array( 'maxlength' => $ColumnMeta->lenght, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Phone field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_phone( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_char( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * URL field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_url( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	if( !empty( $value ) && is_url( $value ) )
	{
		$note = action_icon( T_('URL').': '.$value, 'link', $value, T_('URL') );
	}
	else
	{
		$note = $TableMeta->create_type( $ColumnMeta, false );
	}

	$Form->text_input( $ColumnMeta->get_db_name(), get_value( $Data, $ColumnMeta ), 50, $label, $note, array( 'maxlength' => $ColumnMeta->lenght, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Word field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_word( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	dbase_form_char( $Data, $ColumnMeta, $TableMeta, $Form, $label, $action );
}


/**
 * File field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_file( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$value = get_value( $Data, $ColumnMeta );
	$info = get_file_image_selector( $Data->ID, $ColumnMeta->name, $value, get_file_view_link( $value ), $action );

	$Form->info( $label, $info );
	$Form->hidden( $ColumnMeta->get_db_name(), $value );
}


/**
 * Image field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_image( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	$value = get_value( $Data, $ColumnMeta );
	$info = get_file_image_selector( $Data->ID, $ColumnMeta->name, $value, get_image_view_link( $value, $ColumnMeta ), $action, $ColumnMeta->fileroot );

	$Form->info( $label, $info );
	$Form->hidden( $ColumnMeta->get_db_name(), $value );
}


/**
 * Country field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_country( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	form_country_list( $Form, $ColumnMeta->get_db_name(), $label, get_value( $Data, $ColumnMeta ), $ColumnMeta->is_required() );
}


/**
 * Foreign key field
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @param instance of TableMeta class
 * @param instance of Form class
 * @param field label
 * @param action
 */
function dbase_form_foreign( &$Data, &$ColumnMeta, &$TableMeta, &$Form, $label, $action )
{
	global $DB;

	$pri_column_name = $ColumnMeta->fk_prefix.$ColumnMeta->fk_pri_name;
	$column_name = $ColumnMeta->fk_prefix.$ColumnMeta->fk_name;

	$SQL = new SQL();
	$SQL->SELECT( $pri_column_name.','.$column_name );
	$SQL->FROM( $ColumnMeta->fk_table );

	$foreign_keys = array();
	$foreign_keys[''] = 'Unknown';
	foreach( $DB->get_results( $SQL->get() ) as $row )
	{
		$foreign_keys[ $row->$pri_column_name ] = $row->$column_name;
	}

	$foreign_keys_options = Form::get_select_options_string( $foreign_keys, get_value( $Data, $ColumnMeta ), true );

	$Form->select_input_options( $ColumnMeta->get_db_name(), $foreign_keys_options, $label, '', $field_params = array( 'allow_none' => true, 'required' => $ColumnMeta->is_required() ) );
}


/**
 * Get field value
 * @param instance of Data class
 * @param instance of ColumnMeta class
 * @return mixed
 */
function get_value( &$Data, &$ColumnMeta )
{
	if( array_key_exists( $ColumnMeta->get_db_name(), $Data->values ) )
	{
		$value = $Data->values[$ColumnMeta->get_db_name()];
		if( empty( $value ) )
		{
			$value = $ColumnMeta->default;
		}
		return $value;
	}
	else
	{
		return $ColumnMeta->default;
	}
}


/**
 * Get image/file select controll
 * @param data ID
 * @param column name
 * @param value
 * @param view link
 * @param action
 * @param root
 * @return string controll
 */
function get_file_image_selector( $id, $name, $value, $link, $action, $root = NULL )
{
	global $current_User, $current_DbTable;

	$linkdata = '&amp;linkdata='.$id.'-'.$name.'-'.$action.'-'.$current_DbTable->ID;
	$url_parameters = 'ctrl=files&amp;linkctrl=dbdata'.$linkdata;
	if( $root != NULL )
	{
		$url_parameters .= '&amp;root='.$root;
	}

	if( !empty($value) )
	{
		$info = $link;
		$info .= ' '.action_icon( T_('Remove'), 'delete', '?ctrl=dbdata'.$linkdata.'&amp;file_ID=0', T_('Remove') );
		if( $current_User->check_perm( 'files', 'view' ) )
		{
			$info .= ' '.action_icon( T_('Change'), 'link', '?'.$url_parameters, T_('Change' ).' &raquo;', 5, 5 );
		}
	}
	elseif( $current_User->check_perm( 'files', 'view' ) )
	{
		$info = action_icon( T_('Upload or choose a file'), 'link', '?'.$url_parameters, T_('Upload/Select' ).' &raquo;', 5, 5 );
	}

	return $info;
}


/**
 * Get file view link
 * @param value
 * @return string
 */
function get_file_view_link( $value )
{
	if( !empty( $value ) )
	{
		$FileCache = & get_FileCache( );
		$File = & $FileCache->get_by_ID( $value, false, false );
		return get_view_link( $File );
	}
	else
	{
		return '';
	}
}


/**
 * Get image view link
 * @param value
 * @param instance of ColumnMeta class
 * @return string
 */
function get_image_view_link( $value, &$ColumnMeta )
{
	if( !empty ( $value ) )
	{
		$FileRootCache = & get_FileRootCache();
		$FileCache = & get_FileCache( );

		$FileRoot = & $FileRootCache->get_by_ID( $ColumnMeta->fileroot );
		$File = & $FileCache->get_by_root_and_path( $FileRoot->type, $FileRoot->in_type_ID, $value );

		return get_view_link( $File );
	}
	else
	{
		return '';
	}
}


/**
 * Get file link
 * @param instance of File class
 * @return string
 */
function get_view_link( &$File )
{
	global $UserSettings;

	if( $File != NULL )
	{
		if( $UserSettings->get( 'fm_imglistpreview' ) )
		{	// Image preview OR full type:
			if( $File->is_dir() )
			{ // Navigate into Directory
				return '<a href="'.$File->get_view_url().'" title="'.T_('Change into this directory').'">'.$File->get_icon().' '.T_('Directory').'</a>';
			}
			else
			{
				return $File->get_preview_thumb( 'fulltype' );
			}
		}
		else
		{	// No image preview, small type:
	 		if( $File->is_dir() )
			{ // Navigate into Directory
				return '<a href="'.$File->get_view_url().'" title="'.T_('Change into this directory').'">'.$File->get_icon().'</a>';
			}
			else
			{ // File
				return $File->get_view_link( $File->get_icon(), NULL, $File->get_icon() );
			}
		}
	}
	else
	{
		return T_( 'File not found.' );
	}
}


/**
 * Add country list into form
 * @param instance of Form
 * @param field name
 * @param field label
 * @param value
 * @param true if required, instead false
 */
function form_country_list( &$Form, $field_name, $field_label, $value = NULL, $required = false )
{
	load_class( 'regional/model/_country.class.php', 'Country' );

	$CountryCache = & get_CountryCache();

	$country_codes = array();
	$country_codes[''] = 'Unknown';
	foreach( $CountryCache->get_option_array() as $country_id => $country_name )
	{
		$Country = & $CountryCache->get_by_ID( $country_id, false );
		$country_codes[$Country->code] = $country_name;
	}

	$country_options = Form::get_select_options_string( $country_codes, $value );

	$Form->select_input_options( $field_name, $country_options, $field_label, '', $field_params = array( 'allow_none' => true, 'required' => $required ) );
}


/**
 * Add tables list with primary keys to form
 * @param instnace of Form
 * @param field name
 * @param field label
 * @param value
 * @param true if required, instead false
 */
function form_table_list( &$Form, $field_name, $field_label, $value = NULL, $required = true )
{
	global $DB, $tableprefix;

	$tables_list = array();
	foreach( $DB->get_results( 'SHOW TABLES' ) as $table )
	{
		$fk_table = NULL;
		$fk_prefix = NULL;
		$fk_pri = NULL;
		$fk_name = NULL;

		foreach( $table as $table_name )
		{
			$fk_table = $table_name;

			foreach ( $DB->get_results( 'SHOW COLUMNS FROM '.$table_name ) as $column )
			{
				if( empty( $fk_prefix ) )
				{	// Get prefix
					$position = strrpos( $column->Field, '_' );
					if( !empty( $position ) )
					{
						$fk_prefix = substr( $column->Field, 0, $position ).'_';
					}
				}

				if( !empty( $fk_prefix ) )
				{
					$name = substr( $column->Field, strlen( $fk_prefix ) );

					if( $column->Key == 'PRI' )
					{ 	// check PK
						preg_match( '/^(?<type>\w*)\({0,1}(?<length>\d*)\){0,1}\s{0,1}(?<unsigned>\w*)$/i', $column->Type, $match );

						if( array_key_exists('type', $match) && array_key_exists('unsigned', $match)
								&& $match['type'] == 'int' && $match['unsigned'] == 'unsigned' )
						{
							$fk_pri = $name;
						}
					}
					elseif( strtolower( $name ) == 'name' )
					{	// Table has name field
						$fk_name = $name;
					}
				}
			}
		}

		if( !empty( $fk_table ) && !empty( $fk_prefix ) && !empty( $fk_pri ) && !empty( $fk_name ) )
		{	// Can add this table to FK tables list
			$fk_table = 'T_'.substr( $fk_table, strlen( $tableprefix ) );
			$tables_list[$fk_table.'|'.$fk_prefix.'|'.$fk_pri.'|'.$fk_name] = $fk_table;
		}
	}

	$tables_options = Form::get_select_options_string( $tables_list, $value );
	$Form->select_input_options( $field_name, $tables_options, $field_label, '', $field_params = array( 'allow_none' => true, 'required' => $required ) );
}

/**
 * Add fileroot list to form
 * @param instnace of Form
 * @param field name
 * @param field label
 * @param true if required, instead false
 */
function form_fileroot_list( &$Form, $field_name, $field_label, $value = NULL, $required = true )
{
	$FileRootCache = & get_FileRootCache();

	$rootlist = $FileRootCache->get_available_FileRoots();

	if( count($rootlist) > 1 )
	{
		$options = '';
		$optgroup = '';
		foreach( $rootlist as $l_FileRoot )
		{
			if( ($typegroupname = $l_FileRoot->get_typegroupname()) != $optgroup )
			{ // We're entering a new group:
				if( ! empty($optgroup) )
				{
					$options .= '</optgroup>';
				}
				$options .= '<optgroup label="'.T_($typegroupname).'">';
				$optgroup = $typegroupname;
			}
			$options .= '<option value="'.$l_FileRoot->ID.'"';

			if( !empty( $value ) && $l_FileRoot->ID == $value )
			{
				$options .= ' selected="selected"';
			}

			$options .= '>'.format_to_output( $l_FileRoot->name )."</option>\n";
		}
		if( ! empty($optgroup) )
		{
			$options .= '</optgroup>';
		}

		$Form->select_input_options( $field_name, $options, $field_label, '', $field_params = array( 'allow_none' => true, 'required' => $required ) );
	}
}


?>