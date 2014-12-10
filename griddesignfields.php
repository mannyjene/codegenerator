<?php
require_once 'codebase/db_connectmysqli.php';
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
$mySqlID = $_GET['sqlID'];
if(!isset($_GET['sqlStatement'])) {
	die("sqlStatement value not set");
}
if(!isset($mysqli)) {
	die("Connection not available");
}
$mySqlStatement = $_GET['sqlStatement'];
$myResults=mysqli_query($mysqli, $mySqlStatement) or die(mysqli_error($mysqli));
if (!$myResults) {
	echo ("No fields for $mySqlStatement");
}
$myDeleteSQL = "DELETE FROM tblDesignGrid WHERE designGridSqlID = $mySqlID;"; 
$myDeleteResult = mysqli_query($mysqli, $myDeleteSQL) or die(mysqli_error($mysqli));
$myFields = mysqli_fetch_fields( $myResults );
$myColumnOrder = 1;
foreach ($myFields as $myValue) {
	$myDataType = myFunctionFieldType($myValue->type);
	switch($myDataType) {
		case "tiny":
			$myType = "ch";
			$mySorting = "int";
			$myAlign = "center";
			$myWidth = "70";
			$myFilter = "#select_filter";
			break;
		case "double":
			$myType = "edn";
			$mySorting = "int";
			$myAlign = "right";
			$myWidth = "80";
			$myFilter = "#numeric_filter";
			break;
		case "decimal":
			$myType = "price";
			$mySorting = "int";
			$myAlign = "right";
			$myWidth = "80";
			$myFilter = "#numeric_filter";
			break;
		case "long":
			$myType = "edn";
			$mySorting = "int";
			$myAlign = "right";
			$myWidth = "70";
			$myFilter = "#numeric_filter";
			break;
		case "date":
			$myType = "dhxCalendarA";
			$mySorting = "str";
			$myAlign = "left";
			$myWidth = "100";
			$myFilter = "#text_filter";
			break;
		case "datetime":
			$myType = "dhxCalendarA";
			$mySorting = "str";
			$myAlign = "left";
			$myWidth = "100";
			$myFilter = "#text_filter";
			break;
		default:
			$myType = "ed";
			$mySorting = "str";
			$myAlign = "left";
			$myWidth = "150";
			$myFilter = "#text_filter";
	} // end of switch
	$myFields = "designGridSqlID,";  //  tblDesignGrid.designGridSqlID col#1
	$myFields.= "designGridTable,";  //  tblDesignGrid.designGridTable col#2
	$myFields.= "designGridField,";  //  tblDesignGrid.designGridField col#3
	$myFields.= "designGridColumnOrder,";  //  tblDesignGrid.designGridColumnOrder col#4
	$myFields.= "designGridColumnLabel,";  //  tblDesignGrid.designGridColumnLabel col#5
	$myFields.= "designGridColumnType,";  //  tblDesignGrid.designGridColumnType col#6
	$myFields.= "designGridColumnWidth,";  //  tblDesignGrid.designGridColumnWidth col#7
	$myFields.= "designGridColumnSorting,";  //  tblDesignGrid.designGridColumnSorting col#8
	$myFields.= "designGridColumnAlign,";  //  tblDesignGrid.designGridColumnAlign col#9
	$myFields.= "designGridColumnFilter,";  //  tblDesignGrid.designGridColumnFilter col#10
	$myFields.= "designGridColumnIgnore,";  //  tblDesignGrid.designGridColumnIgnore col#11
	$myFields.= "designGridIncludeInUpdate,";  //  tblDesignGrid.designGridIncludeInUpdate col#12
	$myFields.= "designGridColumnVisible,";  //  tblDesignGrid.designGridColumnVisible col#13
	$myFields.= "designFormFieldTypeDB";  //  tblDesignGrid.designFormFieldTypeDB col#14
	$myInsertSQL = "INSERT INTO tblDesignGrid ( $myFields)
			VALUES ('".$mySqlID."',
					'".$myValue->table."',
					'".$myValue->name."',
					'".$myColumnOrder."',
					'".$myValue->name."',
					'".$myType."',
					'".$myWidth."',
					'".$mySorting."',
					'".$myAlign."',
					'".$myFilter."',
					'"."0"."',
					'"."1"."',
					'"."1"."',
					'"."$myDataType"."')";
	$myInsertResults = mysqli_query($mysqli, $myInsertSQL) or die(mysqli_error($mysqli));
	$myColumnOrder++;
} // end of foreach
print "Success";
mysqli_free_result( $myResults );
mysqli_close( $mysqli );
?>