<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

dbase_ctrl_init();

// Load classes
load_class( 'dbase/model/_tablemeta.class.php', 'TableMeta' );

global $TableMeta, $ColumnMeta;
global $current_User, $current_DbTable;

// Check minimum permission:
$current_User->check_perm( 'options', 'edit', true );

// Load related data from cache
$DbTableCache = & get_DbTableCache();
$current_DbTable = $DbTableCache->get_by_ID( $dbt_ID, false );

// Set options path:
$AdminUI->set_path( 'dbase', $current_DbTable->ID, 'dbsettings' );

// Create table meta
$TableMeta = new TableMeta();

// Get action parameter from request:
param_action();

// Get column name
$column_name = param( $current_DbTable->meta_prefix.'name', 'string' );

switch( $action )
{
	case 'new':
		$ColumnMeta = new ColumnMeta();
		break;

	case 'create':
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbsettings' );

		$ColumnMeta = new ColumnMeta();
		if( $ColumnMeta->load_from_Request() )
		{
			$TableMeta->db_add_column( $ColumnMeta );
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbsettings&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
			// We have EXITed already at this point!!
		}
		break;

	case 'edit':
		$ColumnMeta = & $TableMeta->ColumnMetas[$column_name];
		break;

	case 'delete':
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbsettings' );

		$ColumnMeta = & $TableMeta->ColumnMetas[$column_name];
		if( param( 'confirm', 'integer', 0 ) )
		{	// confirmed, Delete from DB:
			$msg = sprintf( T_('Field &laquo;%s&raquo; deleted.'), $column_name );
			$TableMeta->db_delete_column( $column_name );
			$Messages->add( $msg, 'success' );
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbsettings&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
			// We have EXITed already at this point!!
		}
		break;

	case 'update':
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbsettings' );

		$ColumnMeta = & $TableMeta->ColumnMetas[$column_name];
		if( $ColumnMeta->load_from_Request() )
		{
			$TableMeta->db_modify_column( $ColumnMeta );
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbsettings&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
			//
		}
		else
		{
			$action = 'edit';
		}
		break;
}

// Display <html><head>...</head> section! (Note: should be done early if actions do not redirect)
$AdminUI->disp_html_head();

// Display title, menu, messages, etc. (Note: messages MUST be displayed AFTER the actions)
$AdminUI->disp_body_top();

$AdminUI->disp_payload_begin();

flush();

/**
 * Display payload:
 */
switch( $action )
{
	case 'nil':
		// Do nothing
		break;

	case 'delete':
		// We need to ask for confirmation:
		$TableMeta->confirm_delete(
				sprintf( T_('Delete field &laquo;%s&raquo;?'), $column_name ), 'dbsettings',
				$action, array( $current_DbTable->meta_prefix.'name' => $column_name, 'ctrl' => 'dbsettings', 'dbt_ID' => $current_DbTable->ID ) );

	case 'new':
	case 'create':
	case 'edit':
	case 'update':
		$AdminUI->disp_view( 'dbase/views/_settings.form.php' );
		break;

	default:
		// Display contacts:
		$AdminUI->disp_view( 'dbase/views/_settings_list.view.php' );
		break;
}

$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();

?>