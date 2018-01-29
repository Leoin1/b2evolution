<?php

if( !defined('EVO_CONFIG_LOADED') ) die( 'Please, do not access this page directly.' );


global $db_storage_charset;


/**
 * The b2evo database scheme.
 *
 * This gets updated through {@link db_delta()} which generates the queries needed to get
 * to this scheme.
 *
 * Please see {@link db_delta()} for things to take care of.
 */


$schema_queries['T_dbase__table'] = array(
		'Creating table for database data',
		"CREATE TABLE T_dbase__table (
			dbt_ID int(10) unsigned NOT NULL auto_increment,
			dbt_name varchar(50) NOT NULL,
			dbt_description varchar(255) NULL,
			dbt_table varchar(50) NOT NULL,
			dbt_prefix varchar(5) NOT NULL,
			dbt_order float(4,1) unsigned NULL,
			PRIMARY KEY dbt_ID (dbt_ID),
			UNIQUE (dbt_table)
		) ENGINE = innodb DEFAULT CHARSET = $db_storage_charset" );

?>