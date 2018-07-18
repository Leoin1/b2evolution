<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

dbase_ctrl_init();

/**
 * @var User
 */
global $current_User;

// Check minimum permission:
$current_User->check_perm( 'options', 'edit', true );

// Set options path:
$AdminUI->set_path( 'dbase', 'dbtable' );

// Get action parameter from request:
param_action();

if( param( 'dbt_ID', 'integer', '', true ) )
{// Load table from cache:
	$DbTableCache = & get_DbTableCache();
	if( ( $edited_DbTable = & $DbTableCache->get_by_ID( $dbt_ID, false ) ) === false )
	{	unset( $edited_DbTable );
		forget_param( 'dbt_ID' );
		$Messages->add( sprintf( T_( 'Requested &laquo;%s&raquo; object does not exist any longer.' ), T_( 'DbTable' ) ), 'error' );
		$action = 'nil';
	}
}

switch( $action )
{
	case 'new':
		// Check permission:
		$current_User->check_perm( 'options', 'edit', true );

		if( ! isset( $edited_DbTable ) )
		{	// We don't have a model to use, start with blank object:
			$edited_DbTable = new DbTable();
		}
		else
		{	// Duplicate object in order no to mess with the cache:
			$edited_DbTable = duplicate( $edited_DbTable ); // PHP4/5 abstraction
			$edited_DbTable->ID = 0;
		}
		break;

	case 'edit':
		// Check permission:
		$current_User->check_perm( 'options', 'edit', true );

		// Make sure we got an dbt_ID:
		param( 'dbt_ID', 'integer', true );
 		break;

	case 'create': // Record new table
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbtable' );

		// Insert new table:
		$edited_DbTable = new DbTable();

		// Check permission:
		$current_User->check_perm( 'options', 'edit', true );

		// Load data from request
		if( $edited_DbTable->load_from_Request() )
		{	// We could load data from form without errors:

			// Insert in DB:
			$DB->begin();
			$q = $edited_DbTable->dbexists( NULL, NULL );
			if( $q )
			{	// We have a duplicate entry:
				param_error( 'dbt_table', T_( 'This table already exists' ) );
			}
			else
			{
				$edited_DbTable->dbinsert();
				$Messages->add( T_('New table created.'), 'success' );
			}
			$DB->commit();

			if( empty( $q ) )
			{	// What next?

				switch( $action )
				{
					case 'create':
						// Redirect so that a reload doesn't write to the DB twice:
						header_redirect( '?ctrl=dbtable', 303 ); // Will EXIT
						// We have EXITed already at this point!!
						break;
				}
			}
		}
		break;

	case 'update':
		// Edit table form:
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbtable' );

		// Check permission:
		$current_User->check_perm( 'options', 'edit', true );

		// Make sure we got an dbt_ID:
		param( 'dbt_ID', 'integer', true );

		// load data from request
		if( $edited_DbTable->load_from_Request() )
		{	// We could load data from form without errors:

			// Update in DB:
			$DB->begin();
			$q = $edited_DbTable->dbexists( NULL, NULL );
			if( $q )
			{	// We have a duplicate entry:
				param_error( 'dbt_table', T_( 'This table already exists' ) );
			}
			else
			{
				$edited_DbTable->dbupdate();
				$Messages->add( T_('Table updated.'), 'success' );
			}
			$DB->commit();

			if( empty( $q ) )
			{
				// If no error, Redirect so that a reload doesn't write to the DB twice:
				header_redirect( '?ctrl=dbtable', 303 ); // Will EXIT
				// We have EXITed already at this point!!
			}
		}
		break;

	case 'delete':
		// Delete table:
		// Check that this action request is not a CSRF hacked request:
		$Session->assert_received_crumb( 'dbtable' );

		// Check permission:
		$current_User->check_perm( 'options', 'edit', true );

		// Make sure we got an dbt_ID:
		param( 'dbt_ID', 'integer', true );

		if( param( 'confirm', 'integer', 0 ) )
		{ // confirmed, Delete from DB:
			$msg = sprintf( T_('Table &laquo;%s&raquo; deleted.'), $edited_DbTable->dget('name') );
			$edited_DbTable->dbdelete();
			unset( $edited_DbTable );
			forget_param( 'dbt_ID' );
			$Messages->add( $msg, 'success' );
			// Redirect so that a reload doesn't write to the DB twice:
			header_redirect( '?ctrl=dbtable', 303 ); // Will EXIT
			// We have EXITed already at this point!!
		}
		else
		{	// not confirmed, Check for restrictions:
			if( ! $edited_DbTable->check_delete( sprintf( T_('Cannot delete table &laquo;%s&raquo;'), $edited_DbTable->dget('name') ) ) )
			{	// There are restrictions:
				$action = 'view';
			}
		}
		break;

}

// Display <html><head>...</head> section! (Note: should be done early if actions do not redirect)
$AdminUI->disp_html_head();

// Display title, menu, messages, etc. (Note: messages MUST be displayed AFTER the actions)
$AdminUI->disp_body_top();

$AdminUI->disp_payload_begin();

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
		$edited_DbTable->confirm_delete(
				sprintf( T_('Delete table &laquo;%s&raquo;?'), $edited_DbTable->dget('name') ), 'dbtable',
				$action, get_memorized( 'action' ), array() );
	case 'new':
	case 'create':
	case 'edit':
	case 'update':
		$AdminUI->disp_view( 'dbase/views/_table.form.php' );
		break;

	default:
		// No specific request, list all countries:
		// Cleanup context:
		forget_param( 'dbt_ID' );
		// Display table list:
		$AdminUI->disp_view( 'dbase/views/_table_list.view.php' );
		break;
}

$AdminUI->disp_payload_end();

// Display body bottom, debug info and close </html>:
$AdminUI->disp_global_footer();

?>