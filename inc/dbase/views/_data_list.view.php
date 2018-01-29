<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $DB, $dispatcher;

global $TableMeta, $current_DbTable;

// Create SQL query and load metadata

$SQL = new SQL();
$SQL->SELECT( $current_DbTable->meta_prefix.'fieldname, '.$current_DbTable->meta_prefix.'label' );
$SQL->FROM( $current_DbTable->meta_table_name );
$SQL->WHERE( $current_DbTable->meta_prefix.'column_number IS NOT NULL' );
$SQL->ORDER_BY( $current_DbTable->meta_prefix.'column_number DESC' );

$field_name = $current_DbTable->meta_prefix.'fieldname';
$label_name = $current_DbTable->meta_prefix.'label';

$field_list = array();
foreach( $DB->get_results( $SQL->get() ) as $row )
{
	$field_list[$row->$field_name] = $row->$label_name;
}

//Create SQL query and load data
$fields = '';
if( count( $field_list ) )
{
	$fields .= ','.implode( ',', array_keys( $field_list ) );
}

$SQL = new SQL();
$SQL->SELECT( $current_DbTable->prefix.'ID,'.$current_DbTable->prefix.'name'.$fields );
$SQL->FROM( $current_DbTable->data_table_name );

// Create result set:
$Results = new Results( $SQL->get() );

$Results->Cache = & get_DataCache();

$Results->title = T_('Entries');

$Results->cols[] = array(
							'th' => T_( 'ID' ),
							'th_class' => 'shrinkwrap',
							'td_class' => 'shrinkwrap',
							'order' => $current_DbTable->prefix.'ID',
							'td' => '$'.$current_DbTable->prefix.'ID$',
						);

$Results->cols[] = array(
							'th' => T_( 'Name' ),
							'order' => $current_DbTable->prefix.'name',
							'td' => '<strong>$'.$current_DbTable->prefix.'name$</strong>',
						);

foreach( $field_list as $key => $value )
{
	$ColumnMeta = $TableMeta->ColumnMetas[substr( $key, strlen( $current_DbTable->prefix ) )];

	$type_name = $ColumnMeta->Type->name;

	$Results->cols[] = array(
								'th' => $value,
								'order' => $key,
								'td_class' => dbase_css( $type_name ),
								'td' => '%dbase_table(#'.$key.'#,\''.$ColumnMeta->name.'\',\''.$type_name.'\')%',
							);
}

if( $current_User->check_perm( 'options', 'edit', false ) )
{ // We have permission to modify:
	$Results->cols[] = array(
							'th' => T_('Actions'),
							'th_class' => 'shrinkwrap',
							'td_class' => 'shrinkwrap',
							'td' => action_icon( T_('Edit this entry...'), 'edit',
	                        '%regenerate_url( \'action\', \'db_ID=$'.$current_DbTable->prefix.'ID$&amp;action=edit\')%' )
	                    .action_icon( T_('Duplicate this entry...'), 'copy',
	                        '%regenerate_url( \'action\', \'db_ID=$'.$current_DbTable->prefix.'ID$&amp;action=new\')%' )
	                    .action_icon( T_('Delete this entry!'), 'delete',
	                        '%regenerate_url( \'action\', \'db_ID=$'.$current_DbTable->prefix.'ID$&amp;action=delete&amp;\'.url_crumb( \'dbdata\' ) )%' ),
						);


	$Results->global_icon( T_('Create a new entry...'), 'new', regenerate_url( 'action', 'action=new'), T_('New entry').' &raquo;', 3, 4  );
}

$Results->display();

?>