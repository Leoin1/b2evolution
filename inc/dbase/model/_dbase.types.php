<?php

global $dbase_types;

/**
 * DBase module data types
 *
 * @global array
 */
$dbase_types = array();

load_class( 'dbase/model/type/_type.class.php', 'Type' );

// Numeric types

load_class( 'dbase/model/type/_numeric.type.class.php', 'NumericType' );
load_class( 'dbase/model/type/_string.type.class.php', 'StringType' );
load_class( 'dbase/model/type/_text.type.class.php', 'TextType' );
load_class( 'dbase/model/type/_date.type.class.php', 'DateType' );
load_class( 'dbase/model/type/_time.type.class.php', 'TimeType' );
load_class( 'dbase/model/type/_datetime.type.class.php', 'DateTimeType' );
load_class( 'dbase/model/type/_timestamp.type.class.php', 'TimestampType' );
load_class( 'dbase/model/type/_email.type.class.php', 'EmailType' );
load_class( 'dbase/model/type/_phone.type.class.php', 'PhoneType' );
load_class( 'dbase/model/type/_url.type.class.php', 'UrlType' );
load_class( 'dbase/model/type/_word.type.class.php', 'WordType' );
load_class( 'dbase/model/type/_file.type.class.php', 'LinkedFileType' );
load_class( 'dbase/model/type/_image.type.class.php', 'LinkedImageType' );
load_class( 'dbase/model/type/_country.type.class.php', 'CountryType' );
load_class( 'dbase/model/type/_foreignkey.type.class.php', 'ForeignKeyType' );
load_class( 'dbase/model/type/_checkbox.type.class.php', 'CheckboxType' );

$dbase_types['tinyint'] = new NumericType( 'tinyint', 'TINYINT', 3, 3, 0, 255, -128, 127 );
$dbase_types['checkbox'] = new CheckboxType( 'checkbox', 'CHECKBOX' );
$dbase_types['smallint'] = new NumericType( 'smallint', 'SMALLINT', 5, 5, 0, 65535, -32768, 32767 );
$dbase_types['mediumint'] = new NumericType( 'mediumint', 'MEDIUMINT', 8, 7, 0, 16777215, -8388608, 8388607 );
$dbase_types['int'] = new NumericType( 'int', 'INT', 10, 10, 0, 4294967295, -2147483648, 2147483647 );
$dbase_types['file'] = new LinkedFileType( 'file', 'FILE' );
$dbase_types['foreign'] = new ForeignKeyType( 'foreign', 'FOREIGN KEY' );
$dbase_types['bigint'] = new NumericType( 'bigint', 'BIGINT', 20, 19, 0, 18446744073709551615, -9223372036854775808, 9223372036854775807 );

// String and Text types

$dbase_types['varchar'] = new StringType( 'varchar', 'VARCHAR', 255 );
$dbase_types['email'] = new EmailType( 'email', 'EMAIL' );
$dbase_types['phone'] = new PhoneType( 'phone', 'PHONE' );
$dbase_types['url'] = new UrlType( 'url', 'URL' );
$dbase_types['word'] = new WordType( 'word', 'WORD' );
$dbase_types['image'] = new LinkedImageType( 'image', 'IMAGE' );
$dbase_types['char'] = new StringType( 'char', 'CHAR', 255 );
$dbase_types['country'] = new CountryType( 'country', 'COUNTRY' );
$dbase_types['text'] = new TextType( 'text', 'TEXT' );

// Datetime types

$dbase_types['date'] = new DateType( 'date', 'DATE' );
$dbase_types['time'] = new TimeType( 'time', 'TIME' );
$dbase_types['datetime'] = new DateTimeType( 'datetime', 'DATETIME' );
$dbase_types['timestamp'] = new TimestampType( 'timestamp', 'TIMESTAMP' );

?>