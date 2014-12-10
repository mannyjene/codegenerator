<?php
require_once 'codebase/db_connectmysqli.php';
/*
case MYSQLI_TYPE_DECIMAL
Field is defined as DECIMAL
case MYSQLI_TYPE_NEWDECIMAL
Precision math DECIMAL or NUMERIC field (MySQL 5.0.3 and up)
case MYSQLI_TYPE_BIT
Field is defined as BIT (MySQL 5.0.3 and up)
case MYSQLI_TYPE_TINY
Field is defined as TINYINT
case MYSQLI_TYPE_SHORT
Field is defined as SMALLINT
case MYSQLI_TYPE_LONG
Field is defined as INT
case MYSQLI_TYPE_FLOAT
Field is defined as FLOAT
case MYSQLI_TYPE_DOUBLE
Field is defined as DOUBLE
case MYSQLI_TYPE_NULL
Field is defined as DEFAULT NULL
case MYSQLI_TYPE_TIMESTAMP
Field is defined as TIMESTAMP
case MYSQLI_TYPE_LONGLONG
Field is defined as BIGINT
case MYSQLI_TYPE_INT24
Field is defined as MEDIUMINT
case MYSQLI_TYPE_DATE
Field is defined as DATE
case MYSQLI_TYPE_TIME
Field is defined as TIME
case MYSQLI_TYPE_DATETIME
Field is defined as DATETIME
case MYSQLI_TYPE_YEAR
Field is defined as YEAR
case MYSQLI_TYPE_NEWDATE
Field is defined as DATE
case MYSQLI_TYPE_INTERVAL
Field is defined as INTERVAL
case MYSQLI_TYPE_ENUM
Field is defined as ENUM
case MYSQLI_TYPE_SET
Field is defined as SET
case MYSQLI_TYPE_TINY_BLOB
Field is defined as TINYBLOB
case MYSQLI_TYPE_MEDIUM_BLOB
Field is defined as MEDIUMBLOB
case MYSQLI_TYPE_LONG_BLOB
Field is defined as LONGBLOB
case MYSQLI_TYPE_BLOB
Field is defined as BLOB
case MYSQLI_TYPE_VAR_STRING
Field is defined as VARCHAR
case MYSQLI_TYPE_STRING
Field is defined as CHAR or BINARY
case MYSQLI_TYPE_CHAR
Field is defined as TINYINT. For CHAR, see case MYSQLI_TYPE_STRING
case MYSQLI_TYPE_GEOMETRY
Field is defined as GEOMETRY

*/
function myFunctionFieldType ($myFieldType) {
	switch($myFieldType)
		{
		//  These are considered numeric by IS_NUM() macro
		case MYSQLI_TYPE_DECIMAL:      return "decimal";
		case MYSQLI_TYPE_TINY:         return "tiny";
		case MYSQLI_TYPE_SHORT:        return "short";
		case MYSQLI_TYPE_LONG:         return "long";
		case MYSQLI_TYPE_FLOAT:        return "float";
		case MYSQLI_TYPE_DOUBLE:       return "double";
		case MYSQLI_TYPE_NULL:         return "null";
		case MYSQLI_TYPE_LONGLONG:     return "longlong";
		case MYSQLI_TYPE_INT24:        return "int24";
		case MYSQLI_TYPE_YEAR:         return "year";
		case MYSQLI_TYPE_TIMESTAMP:    return "timestamp";
		case 246:      return "decimal";

		//  These are not considered numeric by IS_NUM()
		case MYSQLI_TYPE_DATE:         return "date";
		case MYSQLI_TYPE_TIME:         return "time";
		case MYSQLI_TYPE_DATETIME:     return "datetime";
		case MYSQLI_TYPE_ENUM:         return "enum";
		case MYSQLI_TYPE_SET:          return "set";
		case MYSQLI_TYPE_TINY_BLOB:    return "tiny_blob";
		case MYSQLI_TYPE_MEDIUM_BLOB:  return "medium_blob";
		case MYSQLI_TYPE_LONG_BLOB:    return "long_blob";
		case MYSQLI_TYPE_BLOB:         return "blob";
		case MYSQLI_TYPE_VAR_STRING:   return "var_string";
		case MYSQLI_TYPE_STRING:       return "string";
		default:                      return "unknown";
		}
}
if(!isset($_GET['sqlID'])) {
	die("sqlID value not set");
}
$sqlID = $_GET['sqlID'];
if(!isset($_GET['sqlStatement'])) {
	die("sqlStatement value not set");
}
$sqlStatement = $_GET['sqlStatement'];
$mySQL = $sqlStatement;
//print $mySQL."<br />"; 
$myResults=mysqli_query($mysqli, $mySQL) or die(mysqli_error());
if (!$myResults) {
	echo ("No fields for $sqlStatement");
}
$myDeleteSQL = "DELETE FROM tblDesignForm WHERE designFormSqlID = $sqlID;"; 
$myDeleteResult = mysqli_query($mysqli, $myDeleteSQL) or die(mysqli_error($mysqli));
//print $myDeleteSQL."<br />";
$myFields = mysqli_fetch_fields( $myResults );
$myColumnOrder = 1;
foreach ($myFields as $myValue) {
	$myDataType = myFunctionFieldType($myValue->type);
	switch($myDataType) {
		case "tiny":
			$myType = "checkbox";
			$myAlign = "center";
			break;
		case "double":
			$myType = "input";
			$myAlign = "right";
			break;
		case "decimal":
			$myType = "input";
			$myAlign = "right";
			break;
		case "long":
			$myType = "input";
			$myAlign = "right";
			break;
		case "date":
			$myType = "calendar";
			$myAlign = "left";
			break;
		case "datetime":
			$myType = "calendar";
			$myAlign = "left";
			break;
		default:
			$myType = "input";
			$myAlign = "left";
	} // end of switch
	$myFields = "designFormSqlID,";  //  tblDesignForm.designFormSqlID col#1
	$myFields.= "designFormTable,";  //  tblDesignForm.designFormTable col#2
	$myFields.= "designFormField,";  //  tblDesignForm.designFormField col#3
	$myFields.= "designFormFieldOrder,";  //  tblDesignForm.designFormFieldOrder col#4
	$myFields.= "designFormFieldType,";  //  tblDesignForm.designFormFieldType col#5
	$myFields.= "designFormFieldName,";  //  tblDesignForm.designFormFieldName col#6
	$myFields.= "designFormFieldBind,";  //  tblDesignForm.designFormFieldBind col#7
	$myFields.= "designFormFieldLabel,";  //  tblDesignForm.designFormFieldLabel col#8
	$myFields.= "designFormFieldRequired,";  //  tblDesignForm.designFormFieldRequired col#9
	$myFields.= "designFormFieldWidth,";  //  tblDesignForm.designFormFieldWidth col#10
	$myFields.= "designFormFieldAlign,";  //  tblDesignForm.designFormFieldAlign col#11
	$myFields.= "designFormFieldIgnore,";  //  tblDesignForm.designFormFieldIgnore col#12
	$myFields.= "designFormFieldNote,";  //  tblDesignForm.designFormFieldNote col#13
	$myFields.= "designFormFieldTypeDB";  //  tblDesignForm.designFormFieldTypeDB col#14
	$myInsertSQL = "INSERT INTO tblDesignForm ( $myFields)
			VALUES ('".$sqlID."',
					'".$myValue->table."',
					'".$myValue->name."',
					'".$myColumnOrder."',
					'".$myType."',
					'".$myValue->name."',
					'".$myValue->name."',
					'".$myValue->name."',
					'"."0"."',
					'"."250"."',
					'".$myAlign."',
					'"."0"."',
					'".""."',
					'"."$myDataType"."')";
	$myInsertResult = mysqli_query($mysqli, $myInsertSQL) or die(mysqli_error($mysqli));
//		print $myInsertSQL."<br />";
//	print "Field added : $myValue->name<br />";
	$myColumnOrder++;
}
//print "List of Fields Added - End<br />";
print "Success";
mysqli_free_result($myResults);
mysqli_close($mysqli);
?>