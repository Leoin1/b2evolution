<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $dispatcher;

global $DB, $TableMeta, $current_DbTable;

$TableMeta->set_col_sort_values();

$Table = & $TableMeta;


$Table->title = T_( 'Table fields' );

$Table->global_icon( T_('Create a new field ...'), 'new', regenerate_url( 'action', 'action=new'), T_('New field').' &raquo;', 3, 4  );

$Table->cols[] = array(
						'th' => T_( 'DB Name' ),
						'order' => 'name',
					);

$Table->cols[] = array(
						'th' => T_( 'UI Label' ),
						'order' => 'field_label',
					);

$Table->cols[] = array(
						'th' => T_( 'Type' ),
						'order' => 'Type->name',
					);

$Table->cols[] = array(
						'th' => T_( 'Null' ),
						'order' => 'null',
					);

$Table->cols[] = array(
						'th' => T_( 'Default' ),
						'order' => 'default',
					);

$Table->cols[] = array(
						'th' => T_( 'Table order' ),
						'order' => 'table_order',
						'th_class' => 'shrinkwrap',
						'td_class' => 'shrinkwrap',
					);

$Table->cols[] = array(
						'th' => T_( 'Form order' ),
						'order' => 'form_order',
						'th_class' => 'shrinkwrap',
						'td_class' => 'shrinkwrap',
					);

$Table->cols[] = array(
						'th' => T_( 'Actions' ),
						'th_class' => 'shrinkwrap',
						'td_class' => 'shrinkwrap',
					);

$Table->display_init( );
echo $Table->params['before'];
$Table->display_list_start();

$Table->display_head();
$Table->display_col_headers();

$Table->display_body_start();

// Show 'Name' line.
$name_meta = $TableMeta->name_meta;

$Table->display_line_start( true, false );

$Table->display_col_start();
echo '<strong>name</strong>';
$Table->display_col_end();

$Table->display_col_start();
echo 'Name';
$Table->display_col_end();

$Table->display_col_start();
echo '<strong>'.$name_meta['type'].'</strong>';

if( array_key_exists( 'length', $name_meta ) )
{
	echo ' ('.$name_meta['length'].')';
}

if( !empty( $name_meta['unsigned'] ) )
{
	echo ' unsigned';
}
$Table->display_col_end();

$Table->display_col_start();
if( empty( $name_meta['null'] ) )
{
	echo '<strong>NOT NULL</strong>';
}
else
{
	echo '<strong>NULL</strong>';
}
$Table->display_col_end();

// Field Default
$Table->display_col_start();
echo $name_meta['default'];
$Table->display_col_end();

// Fields table order, form order and actions
$Table->display_col_start();
echo '-';
$Table->display_col_end();
$Table->display_col_start();
echo '-';
$Table->display_col_end();
$Table->display_col_start();
echo '-';
$Table->display_col_end();

$Table->display_line_end();

if( count( $TableMeta->ColumnMetas ) > 0 )
{
	$field_count = 0;
	$ColumnMetas = $TableMeta->get_sorted_column_metas();
	foreach( $ColumnMetas as $ColumnMeta )
	{
		$field_count++;

		$Type = & $ColumnMeta->Type;

		$Table->display_line_start( true, false );

		// Field Name
		$Table->display_col_start();
		echo '<strong>'.$ColumnMeta->name.'</strong>';
		$Table->display_col_end();

		// Field Label
		$Table->display_col_start();
		echo $ColumnMeta->field_label;
		$Table->display_col_end();


		// Field Type
		$Table->display_col_start();
		echo '<strong>'.$Type->name.'</strong>';

		if( $Type->is_variable() )
		{
			echo ' ('.$ColumnMeta->length.')';
		}

		if( $Type->is_signed() && $ColumnMeta->is_unsigned() )
		{
			echo ' unsigned';
		}
		$Table->display_col_end();

		// Field Null
		$Table->display_col_start();
		if( $ColumnMeta->is_required() )
		{
			echo '<strong>NOT NULL</strong>';
		}
		else
		{
			echo '<strong>NULL</strong>';
		}
		$Table->display_col_end();

		// Field Default
		$Table->display_col_start();
		if( $Type->is_default() )
		{
			echo $ColumnMeta->default;
		}
		else
		{
			echo '&nbsp;';
		}
		$Table->display_col_end();

		// Table Order
		$Table->display_col_start();
		echo $ColumnMeta->table_order;
		$Table->display_col_end();

		// Form Order
		$Table->display_col_start();
		echo $ColumnMeta->form_order;
		$Table->display_col_end();

		// Field Actions
		$Table->display_col_start();
		echo action_icon( T_('Edit this field!'), 'edit', regenerate_url( 'dbsettings', 'action=edit&amp;'.$current_DbTable->meta_prefix.'name='.$ColumnMeta->name ) );
		echo action_icon( T_('Delete this field!'), 'delete', regenerate_url( 'dbsettings', 'action=delete&amp;'.$current_DbTable->meta_prefix.'name='.$ColumnMeta->name.'&amp;'.url_crumb( 'dbsettings' ) ) );
		$Table->display_col_end();

		$Table->display_line_end();
	}
}
else
{	// Settings has no any rows
	echo '<tr><td>'.T_( 'No results.' ).'</td></tr>';
}

$Table->display_body_end();

$Table->display_list_end();
echo $Table->params['after'];
?>