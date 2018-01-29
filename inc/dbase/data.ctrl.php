<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

dbase_ctrl_init();

// Load classes
load_class( 'dbase/model/_data.class.php', 'Data' );
load_class( 'dbase/model/_tablemeta.class.php', 'TableMeta' );

global $TableMeta;
global $current_User, $current_DbTable;

// Check minimum permission:
$current_User->check_perm( 'options', 'edit', true );

// Load related data from cache
$DbTableCache = & get_DbTableCache();
$current_DbTable = $DbTableCache->get_by_ID( $dbt_ID, false );

// Set options path:
$AdminUI->set_path( 'dbase', $current_DbTable->ID, 'dbdata' );

// Create table meta
$TableMeta = new TableMeta();

// Get action parameter from request:
param_action();

// Get data ID parameter from request
param( 'db_ID', 'integer', '', true);

$linkdata = param( 'linkdata', 'string', '' );
if( !empty( $linkdata ) )
{	// Get link data from file browser
	$linkparams = explode( '-', $linkdata );

	$db_ID = $linkparams[0];
	$db_column = $linkparams[1];
	$action = $linkparams[2];
	$file_ID = param( 'file_ID', 'integer', '' );
}

if( !empty( $db_ID ) )
{	// Load data from cache:
	$DataCache = & get_DataCache();
	if( ($edited_Data = & $DataCache->get_by_ID( $db_ID, false )) === false )
	{	unset( $edited_Data );
		forget_param( 'db_ID' );
		$Messages->add( sprintf( T_('Requested &laquo;%s&raquo; object does not exist any longer.'), T_('Data') ), 'error' );
		$action = 'nil';
	}
}

switch( $action )
{
	case 'new':

		if( ! isset( $edited_Data ) )
		{	// We don't have a model to use, start with blank object:
			$edited_Data = new Data();
		}
		else
		{	// Duplicate object in order no to mess with the cache:
			//$edited_Data = duplicate( $edited_Data ); // PHP4/5 abstraction
			$edited_Data = clone $edited_Data;
			$edited_Data->ID = 0;
		}

		break;

	case 'edit':

		// Make sure we got an db_ID:
		param( 'db_ID', 'integer', true );

		break;

	case 'create': // Record new data
	case 'create_new': // Record data and create new
	case 'create_copy': // Record data and create similar
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbdata' );

		$edited_Data = new Data();

		if( $edited_Data->load_from_Request() )
		{
			// Insert in DB:
			$DB->begin();
			$q = $edited_Data->dbexists();

			if($q)
			{	// We have a duplicate entry:

				param_error( $current_DbTable->prefix.'name',
					sprintf( T_('This entry already exists. Do you want to <a %s>edit the existing entry</a>?'),
						'href="?ctrl=dbdata&amp;action=edit&amp;db_ID='.$q.'&amp;dbt_ID='.$current_DbTable->ID.'"' ) );
			}
			else
			{
				$edited_Data->dbinsert();
				$Messages->add( T_('New entry created.'), 'success' );
			}
			$DB->commit();

			if( empty($q) )
			{	// What next?
				switch( $action )
				{
					case 'create_copy':
						// Redirect so that a reload doesn't write to the DB twice:
						header_redirect( '?ctrl=dbdata&action=new&db_ID='.$edited_Data->ID.'&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
						// We have EXITed already at this point!!
						break;
					case 'create_new':
						// Redirect so that a reload doesn't write to the DB twice:
						header_redirect( '?ctrl=dbdata&action=new&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
						// We have EXITed already at this point!!
						break;
					case 'create':
						// Redirect so that a reload doesn't write to the DB twice:
						header_redirect( '?ctrl=dbdata&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
						// We have EXITed already at this point!!
						break;
				}
			}
		}
		break;

	case 'update':
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbdata' );

		// Make sure we got an db_ID:
		param( 'db_ID', 'integer', true );

		if( $edited_Data->load_from_Request() )
		{
			$edited_Data->dbupdate();
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbdata&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
			// We have EXITed already at this point!!
		}
		else
		{
			$action = 'edit';
		}
		break;

	case 'delete':
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbdata' );

		// Make sure we got an db_ID:
		param( 'db_ID', 'integer', true );

		if( param( 'confirm', 'integer', 0 ) )
		{ // confirmed, Delete from DB:
			$msg = sprintf( T_('Data &laquo;%s&raquo; deleted.'), $edited_Data->dget('name') );
			$edited_Data->dbdelete();
			unset( $edited_Data );
			forget_param( 'db_ID' );
			$Messages->add( $msg, 'success' );
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbdata&dbt_ID='.$current_DbTable->ID, 303 ); // Will EXIT
			// We have EXITed already at this point!!
		}
		else
		{	// not confirmed, Check for restrictions:
			if( ! $edited_Data->check_delete( sprintf( T_('Cannot delete entry &laquo;%s&raquo;'), $edited_Data->dget('name') ) ) )
			{	// There are restrictions:
				$action = 'view';
			}
		}
		break;
}

// Set file or image

if( isset( $file_ID ) )
{
	$ColumnMeta = & $TableMeta->ColumnMetas[$db_column];
	$Type = & $ColumnMeta->Type;

	$db_column = $current_DbTable->prefix.$db_column;

	if( $file_ID != 0 )
	{
		if( $Type->name == 'file' )
		{
			$edited_Data->values[$db_column] = $file_ID;
		}
		elseif( $Type->name == 'image' )
		{
			$FileCache = & get_FileCache( );
			$File = & $FileCache->get_by_ID( $file_ID, false, false );
			if( $File )
			{
				$edited_Data->values[$db_column] = $File->_rdfp_rel_path;
			}
		}
	}
	else
	{
		$edited_Data->values[$db_column] = '';
	}
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
		$edited_Data->confirm_delete(
				sprintf( T_('Delete entry &laquo;%s&raquo;?'), $edited_Data->dget('name') ), 'dbdata',
				$action, get_memorized( 'action' ), array() );
	case 'new':
	case 'create':
	case 'create_new':
	case 'create_copy':
	case 'edit':
	case 'update':
		// Display form
		$AdminUI->disp_view( 'dbase/views/_data.form.php' );
		break;


	default:
		// Display contacts:
		$AdminUI->disp_view( 'dbase/views/_data_list.view.php' );
		break;
}

$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();

?>