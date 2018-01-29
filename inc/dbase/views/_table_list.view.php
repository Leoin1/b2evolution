<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $dispatcher;

//Create query
$SQL = new SQL();
$SQL->SELECT( '*' );
$SQL->FROM( 'T_dbase__table' );

// Create result set:
$Results = new Results( $SQL->get(), 'dbt_' );

$Results->Cache = & get_DbTableCache();

$Results->title = T_( 'Tables list' );

if( $current_User->check_perm( 'options', 'edit', false ) )
{ // We have permission to modify:
	$Results->cols[] = array(
							'th' => T_( 'DB Name' ),
							'order' => 'dbt_table',
							'td' => '<strong><a href="'.$dispatcher.'?ctrl=dbtable&amp;dbt_ID=$dbt_ID$&amp;action=edit" title="'.
											T_( 'Edit this table...' ).'">$dbt_table$</a></strong>',
						);
}
else
{	// View only:
	$Results->cols[] = array(
							'th' => T_( 'DB Name' ),
							'order' => 'dbt_table',
							'td' => '$dbt_table$',
						);
}

$Results->cols[] = array(
						'th' => T_( 'DB Prefix' ),
						'td' => '$dbt_prefix$',
					);

$Results->cols[] = array(
						'th' => T_( 'UI Label' ),
						'order' => 'dbt_name',
						'td' => '<strong>$dbt_name$</strong>',
					);

$Results->cols[] = array(
						'th' => T_( 'Description' ),
						'td' => '$dbt_description$',
					);

$Results->cols[] = array(
						'th' => T_( 'Tab order' ),
						'order' => 'dbt_order',
						'td' => '$dbt_order$',
					);


if( $current_User->check_perm( 'options', 'edit', false ) )
{ // We have permission to modify:
	$Results->cols[] = array(
							'th' => T_( 'Actions' ),
							'th_class' => 'shrinkwrap',
							'td_class' => 'shrinkwrap',
							'td' => action_icon( T_( 'Edit this table...' ), 'edit',
	                        '%regenerate_url( \'action\', \'dbt_ID=$dbt_ID$&amp;action=edit\')%' )
	                    .action_icon( T_( 'Delete this table!' ), 'delete',
	                        '%regenerate_url( \'action\', \'dbt_ID=$dbt_ID$&amp;action=delete&amp;\'.url_crumb(\'dbtable\'))%' ));

  $Results->global_icon( T_( 'Create a new table ...' ), 'new', regenerate_url( 'action', 'action=new' ), T_( 'New table' ).' &raquo;', 3, 4  );
}

$Results->display();

?>