<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $action, $localtimenow;

global $TableMeta, $current_DbTable, $edited_Data;

$creating = is_create_action( $action );

$Form = new Form( NULL, 'dbase_data', 'post', 'compact' );

if( !$creating )
{
	$Form->global_icon( T_( 'Delete this entry!' ), 'delete', regenerate_url( 'action', 'action=delete' ) );
}

$Form->global_icon( T_( 'Cancel editing!' ), 'close', regenerate_url( 'action' ) );

if( $creating )
{
	$Form->begin_form( 'fform', T_( 'New entry' ) );
}
else
{
	$Form->begin_form( 'fform', T_( 'Edit entry' ) );
}

$memo = get_memorized( 'action'.( $creating ? ',db_ID' : '' ) );
$Form->add_crumb( 'dbdata' );
$Form->hiddens_by_key( $memo );

$Form->text_input( $current_DbTable->prefix.'name', $edited_Data->name, 50, T_( 'Name' ), '', array( 'maxlength'=> 255, 'required'=>true ) );

// Create SQL query and load metadata

$SQL = new SQL();
$SQL->SELECT( $current_DbTable->meta_prefix.'fieldname, '.$current_DbTable->meta_prefix.'label' );
$SQL->FROM( $current_DbTable->meta_table_name );
$SQL->WHERE( $current_DbTable->meta_prefix.'order IS NOT NULL' );
$SQL->ORDER_BY( $current_DbTable->meta_prefix.'order DESC' );

$field_name = $current_DbTable->meta_prefix.'fieldname';
$label_name = $current_DbTable->meta_prefix.'label';

$field_list = array();
foreach( $DB->get_results( $SQL->get() ) as $row )
{
	$field_list[$row->$field_name] = $row->$label_name;
}


// Display visible fields of the form
foreach( $field_list as $key => $value )
{
	$ColumnMeta = $TableMeta->ColumnMetas[substr( $key, strlen( $current_DbTable->prefix ) )];

	$Type = & $ColumnMeta->Type;

	dbase_form( $edited_Data, $ColumnMeta, $TableMeta, $Form, $value, $action );
}

// Default values for hidden columns
foreach( $TableMeta->ColumnMetas as $ColumnMeta )
{
	if( !array_key_exists( $current_DbTable->prefix.$ColumnMeta->name, $field_list ) && $ColumnMeta->is_required() )
	{
		$Type = & $ColumnMeta->Type;
		if( $Type->is_signed() )
		{
			$var = 1;
		}
		elseif( $Type->is_variable() || $Type->is_text() )
		{
			$var = '-';
		}
		else
		{
			switch ( $Type->name )
			{
				case 'date':
					$var = date( 'Y-m-d', $localtimenow );
					break;
				case 'time':
					$var = date( 'H:i:s', $localtimenow );
					break;
				case 'datetime':
					$var = date( 'Y-m-d H:i:s', $localtimenow );
					break;
			}
		}
		$Form->hidden( $current_DbTable->prefix.$ColumnMeta->name, $var );
	}
}

if( $creating )
{
	$Form->end_form( array( array( 'submit', 'actionArray[create]', T_('Record'), 'SaveButton' ),
												array( 'reset', '', T_('Reset'), 'ResetButton' ) ) );
}
else
{
	$Form->end_form( array( array( 'submit', 'actionArray[update]', T_('Record'), 'SaveButton' ),
												array( 'reset', '', T_('Reset'), 'ResetButton' ) ) );
}

?>