<?php

if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

global $action, $dbase_types;

global $TableMeta, $ColumnMeta, $current_DbTable;

$creating = is_create_action( $action );

$Form = new Form( NULL, 'dbase_settings', 'post' );

if( !$creating )
{
	$Form->global_icon( T_( 'Delete this field!' ), 'delete', regenerate_url( 'action', 'action=delete&dbt_ID='.$current_DbTable->ID.'&dbm_name='.$ColumnMeta->name ) );
}

$Form->global_icon( T_( 'Cancel editing!' ), 'close', regenerate_url( 'action' ) );

if( $creating )
{
	$Form->begin_form( 'fform', T_( 'New field' ) );
}
else
{
	$Form->begin_form( 'fform', T_( 'Edit field' ) );
}

$Form->hiddens_by_key( get_memorized( 'action'.( $creating ? ','.$current_DbTable->meta_prefix.'name' : '' ) ) );

$Form->add_crumb( 'dbsettings' );

// Display description field set

$Form->begin_fieldset( T_( 'Visualization' ), array( 'class'=>'fieldset clear' ) );

if( $creating )
{
	$Form->text_input( $current_DbTable->meta_prefix.'name', $ColumnMeta->name, 25, T_( 'DB Name' ), '', array( 'maxlength'=> 25, 'required'=>true ) );
}
else
{
	$tmp_name = empty( $ColumnMeta->tmp_name ) ? $ColumnMeta->name : $ColumnMeta->tmp_name;

	$Form->text_input( $current_DbTable->meta_prefix.'tmp_name', $tmp_name, 25, T_( 'DB Name' ), '', array( 'maxlength'=> 25, 'required'=>true ) );
	$Form->hidden( $current_DbTable->meta_prefix.'name', $ColumnMeta->name );
}

$Form->text_input( $current_DbTable->meta_prefix.'field_label', $ColumnMeta->field_label, 40, T_( 'UI Label' ), '', array( 'maxlength'=> 40, 'required'=>true ) );

$Form->text_input( $current_DbTable->meta_prefix.'table_order', $ColumnMeta->table_order, 5, T_( 'Table order' ), '', array( 'maxlength'=> 5 ) );

$Form->text_input( $current_DbTable->meta_prefix.'form_order', $ColumnMeta->form_order, 5, T_( 'Form order' ), '', array( 'maxlength'=> 5 ) );

$Form->end_fieldset();

$Form->begin_fieldset( T_( 'Type' ), array( 'class'=>'fieldset clear' ) );

$types_option = array();
foreach($dbase_types as $name => $Type)
{
	$label = $Type->label;
	if( $Type->is_user_type() )
	{
		$label = '&nbsp;&nbsp;'.$label;
	}
	$types_option[$name] = $label;
}

$types_option = Form::get_select_options_string($types_option, $ColumnMeta->Type->name );

$Form->select_input_options( $current_DbTable->meta_prefix.'type', $types_option, T_( 'Type' ), T_( 'Select field type' ), $field_params = array( 'allow_none' => false, 'required' => true, 'onChange' => 'onDbaseTypeChange()' ) );

$Form->text_input( $current_DbTable->meta_prefix.'length', $ColumnMeta->lenght, 3, T_( 'Length' ), T_( 'Lenght of numeric, char and varchar types' ), array( 'maxlength'=> 3 ) );

$Form->text_input( $current_DbTable->meta_prefix.'default', $ColumnMeta->default, 20, T_( 'Default value' ), '', array( 'maxlength'=> 20 ) );

form_country_list( $Form, $current_DbTable->meta_prefix.'default_country', T_( 'Default country' ), $ColumnMeta->default );

form_table_list( $Form, $current_DbTable->meta_prefix.'table', T_( 'Table' ), $ColumnMeta->fk_table.'|'.$ColumnMeta->fk_prefix.'|'.$ColumnMeta->fk_pri_name.'|'.$ColumnMeta->fk_name );

form_fileroot_list( $Form, $current_DbTable->meta_prefix.'fileroot', T_( 'Fileroot' ), $ColumnMeta->fileroot );

$Form->checkbox( $current_DbTable->meta_prefix.'unsigned', $ColumnMeta->unsigned, T_( 'Unsigned' ), T_( 'Uncheck if numeric value must be signed' ) );

$Form->checkbox( $current_DbTable->meta_prefix.'null', $ColumnMeta->null, T_( 'Null' ), T_( 'Uncheck if values must be NOT NULL' ) );

?>

<script type="text/javascript">
	<!--

	var prefix = '<?php echo $current_DbTable->meta_prefix; ?>';

	var fields = new Array();
	fields[0] = 'ffield_'+ prefix + 'length';
	fields[1] = 'ffield_'+ prefix + 'unsigned';
	fields[2] = 'ffield_'+ prefix + 'default';
	fields[3] = 'ffield_'+ prefix + 'fileroot';
	fields[4] = 'ffield_'+ prefix + 'table';
	fields[5] = 'ffield_'+ prefix + 'default_country';
	fields[6] = 'ffield_'+ prefix + 'null';

	onDbaseTypeChange();

	// Listener on type change
	function onDbaseTypeChange()
	{
		var type = document.getElementById( prefix + 'type' );

		changeFildsVisibility( [ 0, 1, 2, 3, 4, 5, 6 ], 'none' );
		setDefailtValue( '' );

		switch( type.value )
		{
			// Numeric fields
			case 'tinyint':
				showNumericFields( 3, 1, 1 );
				break;
			case 'smallint':
				showNumericFields( 5, 1, 1 );
				break;
			case 'mediumint':
				showNumericFields( 8, 1, 1 );
				break;
			case 'int':
				showNumericFields( 10, 2, 2 );
				break;
			case 'bigint':
				showNumericFields( 20, 2, 2 );
				break;
			// String fields
			case 'char':
				showStringFields( 3, 3, 3 );
				break;
			case 'varchar':
				showStringFields( 40, 3, 3 );
				break;
			case 'email':
				showStringFields( 50, 3, 3 );
				break;
			case 'phone':
				showStringFields( 30, 3, 3 );
				break;
			case 'url':
				showStringFields( 255, 3, 3 );
				break;
			case 'word':
				showStringFields( 20, 3, 3 );
				break;
			// Date/Time fields
			case 'date':
				showDatetimeFields( 10 )
				break;
			case 'time':
				showDatetimeFields( 8 )
				break;
			case 'datetime':
			case 'timestamp':
				showDatetimeFields( 19 );
				break;
			// File fields
			case 'file':
				changeFildsVisibility( [ 6 ], 'block' );
				setTypeLength( 10, 2, 2);
				setUnsigned( true );
				break;
			// Image field
			case 'image':
				changeFildsVisibility( [ 0, 3, 6 ] , 'block' );
				setTypeLength( 255, 3, 3);
				break;
			// Country field
			case 'country':
				changeFildsVisibility( [ 5, 6 ], 'block' );
				setTypeLength( 2, 3, 3 );
				break;
			// Foreign Key field
			case 'foreign':
				changeFildsVisibility( [ 4, 6 ], 'block' );
				setTypeLength( 10, 2, 2 );
				setUnsigned( true );
				break;
			// Checkbox field
			case 'checkbox':
				setTypeLength( 1, 1, 1 );
				setUnsigned( true );
				setNull( true );
				setDefailtValue( 0 );
				break;
			// Default fields
			case 'text':
				changeFildsVisibility( [ 6 ], 'block' );
			default:
				// Without fields
				break;
		}
	}

	// Show numeric fields
	// @param value
	// @param size
	// @param maxlength
	function showNumericFields( value, size, maxlength )
	{
		changeFildsVisibility( [ 0, 1, 2, 6 ], 'block' );
		setTypeLength( value, size, maxlength );
		setDefailtLength( value );
	}

	// Show string fields
	// @param value
	// @param size
	// @param maxlength
	function showStringFields( value, size, maxlength )
	{
		changeFildsVisibility( [ 0, 2, 6 ], 'block' );
		setTypeLength( value, size, maxlength );
		setDefailtLength( value );
	}

	// Show datetime fields
	// @param maxlength
	function showDatetimeFields( maxlength )
	{
		changeFildsVisibility( [ 2, 6 ], 'block' );
		setDefailtLength( maxlength );
	}

	// Change fields visibility
	// @param field indexes
	// @param visibility, none or block
	function changeFildsVisibility( indexes, visibility )
	{
		for( i = 0; i < indexes.length; i++ )
		{
			var field = document.getElementById( fields[indexes[i]] );
			if( field != null )
			{
				field.style.display = visibility;
			}
		}
	}

	// Set type length field
	// @param value
	// @param size
	// @param maxlength
	function setTypeLength( value, size, maxlength )
	{
		var field = document.getElementById( prefix + 'length' );
		field.setAttribute('value', value );
		field.setAttribute('size', size);
		field.setAttribute('maxlength', maxlength);
	}

	// Set defailt field maxlength
	// @param maxlength
	function setDefailtLength( maxlength )
	{
		var field = document.getElementById( prefix + 'default' );
		field.setAttribute('maxlength', maxlength);
	}

	// Set defailt value
	// @param value
	function setDefailtValue( value )
	{
		var field = document.getElementById( prefix + 'default' );
		field.setAttribute('value', value );
	}

	// Set unsigned/signed field value
	// @param value
	function setUnsigned( value )
	{
		var field = document.getElementById( prefix + 'unsigned' );
		field.checked = value;
	}

	// Set null field value
	// @param value
	function setNull( value )
	{
		var field = document.getElementById( prefix + 'null' );
		field.checked = value;
	}

	-->
</script>
<noscript>Please enable JavaScript to use dBase module</noscript>
<?php

$Form->end_fieldset();

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