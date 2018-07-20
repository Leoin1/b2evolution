<?php

if( !defined('EVO_CONFIG_LOADED') ) die( 'Please, do not access this page directly.' );


/**
 * Aliases for table names:
 *
 * (You should not need to change them.
 *  If you want to have multiple b2evo installations in a single database you should
 *  change {@link $tableprefix} in _basic_config.php)
 */
$db_config['aliases']['T_dbase__table'] = $tableprefix.'dbase__table';


/**
 * Controller mappings.
 *
 * For each controller name, we associate a controller file to be found in /inc/ .
 * The advantage of this indirection is that it is easy to reorganize the controllers into
 * subdirectories by modules. It is also easy to deactivate some controllers if you don't
 * want to provide this functionality on a given installation.
 *
 * Note: while the controller mappings might more or less follow the menu structure, we do not merge
 * the two tables since we could, at any time, decide to make a skin with a different menu structure.
 * The controllers however would most likely remain the same.
 *
 * @global array
 */
$ctrl_mappings['dbtable'] = 'dbase/table.ctrl.php';
$ctrl_mappings['dbdata'] = 'dbase/data.ctrl.php';
$ctrl_mappings['dbsettings'] = 'dbase/settings.ctrl.php';


/**
 * dBase module definition
 */
class dbase_Module extends Module
{
	/**
	 * Do the initializations. Called from in _main.inc.php.
	 * This is typically where classes matching DB tables for this module are registered/loaded.
	 *
	 * Note: this should only load/register things that are going to be needed application wide,
	 * for example: for constructing menus.
	 * Anything that is needed only in a specific controller should be loaded only there.
	 * Anything that is needed only in a specific view should be loaded only there.
	 */
	function init()
	{
		global $tableprefix, $db_table_prefix;
		global $db_data_table_suffix, $db_meta_table_suffix;

		// Construct prefix and suffix for data and meta tables

		$db_table_prefix = $tableprefix.'dbase__';
		$db_data_table_suffix = '';
		$db_meta_table_suffix = '__meta';

		load_class( 'dbase/model/_table.class.php', 'DbTable' );
		load_funcs( 'dbase/model/_dbase.funcs.php' );
	}


	/**
	 * Build the evobar menu
	 */
	function build_evobar_menu()
	{
		global $admin_url;
		global $current_User;
		global $topleft_Menu;

		if( $current_User->check_perm( 'options', 'edit' ) )
		{
			$entries = array();

			$entries['dbtable'] = array(
				'text' => T_( 'DBase' ),
				'href' => $admin_url.'?ctrl=dbtable',
				'style' => 'padding: 3px 1ex;',
			);

			$topleft_Menu->add_menu_entries( NULL, $entries );
		}
	}


	/**
	 * Builds the 3rd half of the menu. This is the one with the configuration features
	 *
	 * At some point this might be displayed differently than the 1st half.
	 */
	function build_menu_3()
	{
		global $dispatcher, $ctrl;
		global $current_User, $current_DbTable;
		global $AdminUI;

		if( $current_User->check_perm( 'options', 'edit' ) )
		{
			// Permission to view messaging:

			$DbTableCache = & get_DbTableCache();
			$DbTableCache->load_all();

			$first_dbt_ID = '';

			$entries = array();

			foreach( $DbTableCache->get_ID_array() as $dbt_ID )
			{	// Add tab for each table
				if( empty( $first_dbt_ID ) )
				{
					$first_dbt_ID = $dbt_ID;
				}

				$DbTable = & $DbTableCache->get_by_ID( $dbt_ID, false );

				$entries[$dbt_ID] = array( 	'text'    => $DbTable->name,
											'title'   => $DbTable->name,
											'href'    => $dispatcher.'?ctrl=dbdata&amp;dbt_ID='.$dbt_ID,
											'entries' => array(
																'dbdata' => array(
																	'text' => T_( 'Entries' ),
																	'href' => '?ctrl=dbdata&amp;dbt_ID='.$dbt_ID ),
																'dbsettings' => array(
																	'text' => T_( 'Settings' ),
																	'href' => '?ctrl=dbsettings&amp;dbt_ID='.$dbt_ID )
															));
			}

			$entries['dbtable'] = array(
								'text' => T_( 'Settings' ),
								'title' => T_( 'Settings' ),
								'href' => $dispatcher.'?ctrl=dbtable' );

			$dbtable_item = array(	'text'  => T_( 'DBase' ),
									'title' => T_( 'DBase' ) );

			if( !empty( $first_dbt_ID ) )
			{
				$dbtable_item['href'] = $dispatcher.'?ctrl=dbdata&amp;dbt_ID='.$first_dbt_ID;
			}
			else
			{
				$dbtable_item['href'] = $dispatcher.'?ctrl=dbtable';
			}

			$dbtable_item['entries'] = $entries;

			$AdminUI->add_menu_entries( NULL, array( 'dbase' => $dbtable_item ) );
		}
	}
}

$dbase_Module = new dbase_Module();

?>