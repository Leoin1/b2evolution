<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

/**
 * @var DbTable
 */
global $edited_DbTable;

// Determine if we are creating or updating...
global $action;
$creating = is_create_action( $action );

$Form = new Form( NULL, 'dbtable_changes', 'post', 'compact' );

if( !$creating )
{
	$Form->global_icon( T_( 'Delete this table!' ), 'delete', regenerate_url( 'action', 'action=delete' ) );
}

$Form->global_icon( T_( 'Cancel editing!' ), 'close', regenerate_url( 'action' ) );

$Form->begin_form( 'fform', $creating ?  T_( 'New table' ) : T_( 'Table' ) );

$Form->add_crumb( 'dbtable' );

$Form->hiddens_by_key( get_memorized( 'action'.( $creating ? ',dbt_ID' : '' ) ) ); // (this allows to come back to the right list order & page)

$Form->text_input( 'dbt_table', $edited_DbTable->table, 20, T_( 'DB Name' ), '', array( 'maxlength'=> 20, 'required' => true ) );

if( $creating )
{
	$Form->text_input( 'dbt_prefix', $edited_DbTable->prefix, 5, T_( 'DB Prefix' ), '', array( 'maxlength'=> 5, 'required' => false ) );
}
else
{
	$Form->info( T_( 'DB Prefix' ), $edited_DbTable->prefix );
	$Form->hidden( 'dbt_prefix', $edited_DbTable->prefix );
}

$Form->text_input( 'dbt_name', $edited_DbTable->name, 30, T_( 'UI Label' ), '', array( 'maxlength' => 50, 'required' => true ) );

$Form->text_input( 'dbt_description', $edited_DbTable->description, 50, T_( 'Description' ), '', array( 'maxlength' => 255, 'required' => false ) );

$Form->text_input( 'dbt_order', $edited_DbTable->order, 5, T_( 'Tab order' ), '', array( 'maxlength'=> 5 ) );

if( $creating )
{
	$Form->end_form( array( array( 'submit', 'actionArray[create]', T_('Record'), 'SaveButton' ),
													array( 'reset', '', T_('Reset'), 'ResetButton' ) ) );
}
else
{
	$Form->end_form( array( array( 'submit', 'actionArray[update]', T_('Update'), 'SaveButton' ),
													array( 'reset', '', T_('Reset'), 'ResetButton' ) ) );
}

?>