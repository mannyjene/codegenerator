<?php
require_once 'session.php';
require_once 'codebase/db_connectmysqli.php';
if(!isset($_GET['sqlID'])) {
	die("sqlID value not set");
}
$mySqlID = $_GET['sqlID'];
$esliSelectSQL = "SELECT tblSqlGrid.* FROM tblSqlGrid WHERE SqlID = $mySqlID;";
//	print $esliSelectSQL."\n";
$tblSqlGridResults = mysqli_query($mysqli, $esliSelectSQL) or die(mysqli_error());
if (!$tblSqlGridResults) {
	echo "No tblSqlGrid records found for SQLID = $mySqlID";
	exit;
}
$esliRecords = mysqli_fetch_array($tblSqlGridResults);
$esliSqlStatement = $esliRecords["sqlStatement"];
if (!$esliSqlStatement) {
	echo "No SQL Statement found for SQLID = $mySqlID";
	exit;
}
$esliFormOrGridName = $esliRecords["sqlGridName"];
if (!$esliFormOrGridName) {
	echo "No Grid Name found for SQLID = $mySqlID";
	exit;
}
$esliWindowOrLayout = $esliRecords["sqlWindowOrLayout"];
if (!$esliWindowOrLayout) {
	echo "No Grid Window or Layout found for SQLID = $mySqlID";
	exit;
}
$esliGroupSelectSQL = "SELECT designGridTable FROM tblDesignGrid WHERE designGridSqlID = $mySqlID AND designGridColumnIgnore = 0 GROUP BY designGridTable;";
//	print $esliSelectSQL."\n";
$esliGroupSelectResults = mysqli_query($mysqli, $esliGroupSelectSQL) or die(mysqli_error());
if (!$esliGroupSelectResults) {
	echo "No tblDesignGrid records found for SQLID = $mySqlID";
	exit;
}
$esliTotalGroupRecords = mysqli_num_rows($esliGroupSelectResults);
$esliRowsPerPage = 30;
$esliRecordNumber = 1;
$esliTable1 ="No Table";
$esliTable2 ="No Table";
$esliTable3 ="No Table";
while ($esliRecords = mysqli_fetch_array($esliGroupSelectResults)) {
	if ($esliRecordNumber == 1) {
		$esliTable1 = $esliRecords["designGridTable"];
	}
	if ($esliRecordNumber == 2) {
		$esliTable2 = $esliRecords["designGridTable"];
	}
	if ($esliRecordNumber == 3) {
		$esliTable3 = $esliRecords["designGridTable"];
	}
	$esliRecordNumber++;
}
$esliSelectSQL = "SELECT tblDesignGrid.* FROM tblDesignGrid WHERE designGridSqlID = $mySqlID AND designGridColumnIgnore = 0 ORDER BY designGridColumnOrder ASC;";
//	print $esliSelectSQL."\n";
$esliSelectResults = mysqli_query($mysqli, $esliSelectSQL) or die(mysqli_error());
if (!$esliSelectResults) {
	echo "No tblDesignGrid records found for SQLID = $mySqlID";
	exit;
}
$esliRecordNumber = 1;
$esliTotalRecords = mysqli_num_rows($esliSelectResults);
$esliAttachHeader = "esliGrid_$esliFormOrGridName.attachHeader(\"";
$esliConnectorAttachHeader = "esliGrid_$esliFormOrGridName.attachHeader(\"";
$esliSetHeader = "esliGrid_$esliFormOrGridName.setHeader(\"";
$esliSetInitWidths = "esliGrid_$esliFormOrGridName.setInitWidths(\"";
$esliSetColAlign = "esliGrid_$esliFormOrGridName.setColAlign(\"";
$esliSetColSorting = "esliGrid_$esliFormOrGridName.setColSorting(\"";
$esliConnectorColumnSorting = "esliGrid_$esliFormOrGridName.setColSorting(\"";
$esliSetColTypes = "esliGrid_$esliFormOrGridName.setColTypes(\"";
$esliSetColumnsVisibility = "esliGrid_$esliFormOrGridName.setColumnsVisibility(\"";
$eslisetColumnIds = "esliGrid_$esliFormOrGridName.setColumnIds(\"";
$esliColumn = 0;
$esliFields = "";
$esliCells = "";
$esliUpdateFields = "";
$esliUpdateFieldsTable1 = "";
$esliUpdateFieldsTable2 = "";
$esliUpdateFieldsTable3 = "";
$esliInsertFieldsTable1 = "";
$esliInsertFieldsTable2 = "";
$esliInsertFieldsTable3 = "";
$esliInsertValuesTable1 = "";
$esliInsertValuesTable2 = "";
$esliInsertValuesTable3 = "";
$esliAttachHeaderByField = "var esliAttachHeader = \"\";\n";
$esliSetHeaderByField = "var esliSetHeader = \"\";\n";
$esliSetInitWidthsByField = "var esliSetInitWidths = \"\";\n";
$esliSetColAlignByField = "var esliSetColAlign = \"\";\n";
$esliSetColTypesByField = "var esliSetColTypes = \"\";\n";
$esliSetColumnsVisibilityByField = "var esliSetColumnsVisibility = \"\";\n";
$esliSetColumnIdsByField = "var esliSetColumnIds = \"\";\n";
while ($esliRecords = mysqli_fetch_array($esliSelectResults)) {
	if ($esliColumn != 0) {
			if ($esliRecords["designGridIncludeInUpdate"] == "1") {
				if ($esliUpdateFields == "" ) {
					$esliUpdateFields.= " SET ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				} else {
					$esliUpdateFields.= ",\n	 ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				}
			}
		if ($esliTable1 == $esliRecords["designGridTable"]) {
			if ($esliUpdateFieldsTable1 == "" ) {
				$esliUpdateFieldsTable1.= "UPDATE $esliTable1 SET ".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable1.= "INSERT INTO $esliTable1 ( ".$esliRecords["designGridField"];
				$esliInsertValuesTable1.= " Values ( '\".\$_GET[\"c$esliColumn\"].\"'";
			} else {
				$esliUpdateFieldsTable1.= ",\n	 ".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable1.= ", ".$esliRecords["designGridField"];
				$esliInsertValuesTable1.= ", '\".\$_GET[\"c$esliColumn\"].\"'";
			}
		}
		if ($esliTable2 == $esliRecords["designGridTable"]) {
			if ($esliUpdateFieldsTable2 == "" ) {
				$esliUpdateFieldsTable2.= "UPDATE $esliTable2 SET ".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable2.= "INSERT INTO $esliTable2 ( ".$esliRecords["designGridField"];
				$esliInsertValuesTable2.= " Values ( '\".\$_GET[\"c$esliColumn\"].\"'";
			} else {
				$esliUpdateFieldsTable2.= ",\n 	".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable2.= ", ".$esliRecords["designGridField"];
				$esliInsertValuesTable2.= ", '\".\$_GET[\"c$esliColumn\"].\"'";
			}
		}
		if ($esliTable3 == $esliRecords["designGridTable"]) {
			if ($esliUpdateFieldsTable3 == "" ) {
				$esliUpdateFieldsTable3.= "UPDATE $esliTable3 SET ".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable3.= "INSERT INTO $esliTable3 ( ".$esliRecords["designGridField"];
				$esliInsertValuesTable3.= " Values ( '\".\$_GET[\"c$esliColumn\"].\"'";
			} else {
				$esliUpdateFieldsTable3.= ",\n	 ".$esliRecords["designGridField"]." = '\".\$_GET[\"c$esliColumn\"].\"'";
				$esliInsertFieldsTable3.= ", ".$esliRecords["designGridField"];
				$esliInsertValuesTable3.= ", '\".\$_GET[\"c$esliColumn\"].\"'";
			}
		}
	}
	if ( $esliRecords["designGridColumnVisible"] == 0 ) {
		$esliVisibility = "true";
	} else {
		$esliVisibility = "false";
	}
	$esliColumnType = $esliRecords["designGridColumnType"];
	if ( $esliRecords["designGridIncludeInUpdate"] == 0 ) {
		switch ($esliRecords["designGridColumnType"]) {
			case "date":
				$esliColumnType = "rotxt";;
			break;
			case "timestamp":
				$esliColumnType = "rotxt";
			break;
			case "var_string":
				$esliColumnType = "rotxt";
			break;
			default:
			$esliColumnType = "ro";		}
		}
		
	if ($esliRecordNumber == 1) {
		$esliConnectorColumnSorting.="connector";
		$esliConnectorAttachHeader.="#connector_text_filter";
		$esliAttachHeaderByField.="	esliAttachHeader = esliAttachHeader + \"#connector_text_filter\"; //".$esliRecords["designGridField"]."\n";
		$esliSetHeaderByField.="	esliSetHeader = esliSetHeader + \"".$esliRecords["designGridColumnLabel"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetInitWidthsByField.="	esliSetInitWidths = esliSetInitWidths + \"".$esliRecords["designGridColumnWidth"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColAlignByField.="	esliSetColAlign = esliSetColAlign + \"".$esliRecords["designGridColumnAlign"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColTypesByField.="	esliSetColTypes = esliSetColTypes + \"".$esliColumnType."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColumnsVisibilityByField.="	esliSetColumnsVisibility = esliSetColumnsVisibility + \"".$esliVisibility."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColumnIdsByField.="	esliSetColumnIds = esliSetColumnIds + \"".$esliRecords["designGridField"]."\"; //".$esliRecords["designGridField"]."\n";
	} else {
		$esliConnectorColumnSorting.= ",connector";
//		$esliConnectorAttachHeader.=",#connector_select_filter";
		$esliConnectorAttachHeader.=",#connector_text_filter";
		$esliAttachHeaderByField.="	esliAttachHeader = esliAttachHeader + \",#connector_text_filter\"; //".$esliRecords["designGridField"]."\n";
		$esliSetHeaderByField.="	esliSetHeader = esliSetHeader + \",".$esliRecords["designGridColumnLabel"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetInitWidthsByField.="	esliSetInitWidths = esliSetInitWidths + \",".$esliRecords["designGridColumnWidth"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColAlignByField.="	esliSetColAlign = esliSetColAlign + \",".$esliRecords["designGridColumnAlign"]."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColTypesByField.="	esliSetColTypes = esliSetColTypes + \",".$esliColumnType."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColumnsVisibilityByField.="	esliSetColumnsVisibility = esliSetColumnsVisibility + \",".$esliVisibility."\"; //".$esliRecords["designGridField"]."\n";
		$esliSetColumnIdsByField.="	esliSetColumnIds = esliSetColumnIds + \",".$esliRecords["designGridField"]."\"; //".$esliRecords["designGridField"]."\n";
	}
	$esliCells.= "	print(\"<cell>\");\n";
	$esliCells.= "		print(\$row['".$esliRecords["designGridField"]."']);  //  ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." col#".$esliColumn."\n";
	$esliCells.= "	print(\"</cell>\");\n";
	if ($esliRecordNumber == $esliTotalRecords ) {
		$esliFields.= "\$esliFields.= \"".$esliRecords["designGridField"]."\";  //  ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." col#".$esliColumn."\n";
	} else {
		if ($esliRecordNumber == 1) {
			$esliKeyField = $esliRecords["designGridField"];
			$esliFields.= "\$esliFields = \"".$esliRecords["designGridField"].",\";  //  ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." col#".$esliColumn."\n";
		} else {
			$esliFields.= "\$esliFields.= \"".$esliRecords["designGridField"].",\";  //  ".$esliRecords["designGridTable"].".".$esliRecords["designGridField"]." col#".$esliColumn."\n";
//							designGridTable=			'".$_GET["c2"]."',

		}
	}
	if ($esliRecordNumber == 1) {
		$esliAttachHeader.= $esliRecords["designGridColumnFilter"];
	} else {
		$esliAttachHeader.= ",".$esliRecords["designGridColumnFilter"];
	}
	if ($esliRecordNumber == 1) {
		$esliSetHeader.= $esliRecords["designGridColumnLabel"];
	} else {
		$esliSetHeader.= ",".$esliRecords["designGridColumnLabel"];
	}
	if ($esliRecordNumber == 1) {
		$eslisetColumnIds.= $esliRecords["designGridField"];
	} else {
		$eslisetColumnIds.= ",".$esliRecords["designGridField"];
	}
	if ($esliRecordNumber == 1) {
		$esliSetInitWidths.= $esliRecords["designGridColumnWidth"];
	} else {
		$esliSetInitWidths.= ",".$esliRecords["designGridColumnWidth"];
	}
	if ($esliRecordNumber == 1) {
		$esliSetColAlign.= $esliRecords["designGridColumnAlign"];
	} else {
		$esliSetColAlign.= ",".$esliRecords["designGridColumnAlign"];
	}
	if ($esliRecordNumber == 1) {
		$esliSetColSorting.= $esliRecords["designGridColumnSorting"];
	} else {
		$esliSetColSorting.= ",".$esliRecords["designGridColumnSorting"];
	}
	if ($esliRecordNumber == 1) {
		$esliSetColTypes.= $esliRecords["designGridColumnType"];
	} else {
		$esliSetColTypes.= ",".$esliRecords["designGridColumnType"];
	}
	if ($esliRecordNumber == 1) {
		if ( $esliRecords["designGridColumnVisible"] == 0 ) {
			$esliSetColumnsVisibility.= "true"; //$esliRecords["designGridColumnType"];
		} else {
			$esliSetColumnsVisibility.= "false"; //$esliRecords["designGridColumnType"];
		}
	} else {
		if ( $esliRecords["designGridColumnVisible"] == 0 ) {
			$esliSetColumnsVisibility.= ",true"; //.$esliRecords["designGridColumnType"];
		} else {
			$esliSetColumnsVisibility.= ",false"; //.$esliRecords["designGridColumnType"];
		}
	}
	$esliRecordNumber++;
	$esliColumn++;
}
$esliAttachHeader.= "\")";
$esliConnectorAttachHeader.= "\")";
$esliSetHeader.= "\")";
$esliSetInitWidths.= "\")";
$esliSetColAlign.= "\")";
$esliSetColSorting.= "\")";
$esliConnectorColumnSorting.= "\")";
$esliSetColTypes.= "\")";
$esliSetColumnsVisibility.= "\")";
$eslisetColumnIds.= "\")";

$documentroot = dirname( __FILE__ );
$esliLogFile = "connector.log";
$myConnectorcode ="<?php\n";
$myConnectorcode.= "require(\"db_ConnectorGrid.php\");\n\n";
$myConnectorcode.= "//function gridBeforeUpdate(\$action) {\n";
$myConnectorcode.= "// \$action->set_value(\"UpDatedTime\", date(\"Y-m-d H:i:s\"));\n";
$myConnectorcode.= "// global \$esliCookieUser;\n";
$myConnectorcode.= "// \$action->set_value(\"UpdatedByUser\", \$esliCookieUser );\n";
$myConnectorcode.= "// \$action->set_value(\"UpDatedByUser\", \$_SESSION['staff']);\n";
$myConnectorcode.= "//}\n";
$myConnectorcode.= "$esliFields\n";
$myConnectorcode.= "\$myConnector->dynamic_loading($esliRowsPerPage);\n";
$myConnectorcode.= "\$mySQL = \"$esliSqlStatement\";\n";
$myConnectorcode.= "\$myConnector->access->deny(\"delete\"); //blocks Delete action\n";
$myConnectorcode.= "//\$myConnector->event->attach(\"beforeUpdate\", \"gridBeforeUpdate\");\n";
$myConnectorcode.= "//\$myConnector->enable_log(\"$esliLogFile\",true);\n";
$myConnectorcode.= "\$myConnector->set_encoding(\"iso-8859-1\");\n";
$myConnectorcode.= "\$myConnector->set_options(\" field name \",array()); // only if using client side combo add option\n";
$myConnectorcode.= "\$myConnector->render_sql(\$mySQL,\"$esliKeyField\",\$esliFields);\n";
$myConnectorcode.= "?>\n";
$documentfolder = $documentroot."/codebase/xml/";
$file = fopen($documentfolder."myConn_".$esliFormOrGridName.".php","w+");
fwrite($file,$myConnectorcode);
fclose($file);

mysqli_free_result($esliSelectResults);
mysqli_close($mysqli);

//===============
$outputPhpCode = "";
$outputPhpCode.= "<?php\n";
$outputPhpCode.= "require_once 'session.php';\n";
$outputPhpCode.= "include 'codebase/db_connectmysqli.php';\n";
$outputPhpCode.= "\$esliPassID = \$_GET['esliPassID'];\n";
$outputPhpCode.= "\$esliPassID = stripslashes(\$esliPassID);\n";
$outputPhpCode.= "\$esliPassID = mysqli_real_escape_string(\$mysqli, \$esliPassID);\n";
$outputPhpCode.= "?>\n";
$outputPhpCode.= "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n";
$outputPhpCode.= "<html xmlns='http://www.w3.org/1999/xhtml'>\n";
$outputPhpCode.= "<head>\n";
$outputPhpCode.= "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />\n";
$outputPhpCode.= "<title>ESLI ag_$esliFormOrGridName</title>\n";
$outputPhpCode.= "<link rel='stylesheet' type='text/css' href='dhtmlx.css' />\n";
$outputPhpCode.= "<link rel='stylesheet' type='text/css' href='esli.css' />\n";
$outputPhpCode.= "<script type='text/javascript' src='dhtmlx.js'></script>\n";
$outputPhpCode.= "<style>\n";
$outputPhpCode.= "</style>\n";
$outputPhpCode.= "<script type='text/javascript'>\n";
$outputPhpCode.= "var esliGrid_$esliFormOrGridName;\n";
$outputPhpCode.= "var esliSelectedRowID;\n";
$outputPhpCode.= "function esliIsNumeric(esliCheckValue) {\n";
$outputPhpCode.= "	return !isNaN(parseFloat(esliCheckValue)) && isFinite(esliCheckValue);\n";
$outputPhpCode.= "}\n";
$outputPhpCode.= "function esliOnClickAddDelete ( esliID ) {\n";
$outputPhpCode.= "	switch (esliID.id) {\n";
$outputPhpCode.= "		case 'esliAddRow':\n";
$outputPhpCode.= "			esliGrid_$esliFormOrGridName.addRow(esliGrid_$esliFormOrGridName.uid(), ['0'], 0);\n";
$outputPhpCode.= "		break;\n";
$outputPhpCode.= "		case 'esliDeleteRow':\n";
$outputPhpCode.= "			if ( esliGrid_$esliFormOrGridName.getSelectedRowId() ) {\n";
$outputPhpCode.= "				dhtmlx.confirm( { title:'Confirm', ok:'Yes', cancel:'No', text:'Deleting row ' + esliGrid_$esliFormOrGridName.getSelectedRowId() ,\n";
$outputPhpCode.= "					callback:function(result) {\n";
$outputPhpCode.= "						if (result==true) {\n";
$outputPhpCode.= "							esliGrid_$esliFormOrGridName.deleteRow(esliGrid_$esliFormOrGridName.getSelectedRowId());\n";
$outputPhpCode.= "						}\n";
$outputPhpCode.= "					}\n";
$outputPhpCode.= "				});\n";
$outputPhpCode.= "			}\n";
$outputPhpCode.= "		break;\n";
$outputPhpCode.= "	}\n";
$outputPhpCode.= "}\n";
$outputPhpCode.= "dhtmlx.image_path = 'imgs/';\n";
$outputPhpCode.= "dhtmlxEvent(window,'load',function() {\n";
if ( $esliWindowOrLayout == 'Window' ) {
	$outputPhpCode.=  "	var esliWindow = new dhtmlXWindows();\n";
	$outputPhpCode.=  "	var esliWindow_$esliFormOrGridName = esliWindow.createWindow(\"esliDivWindow\", 90, 30, 900, 650);\n";
	$outputPhpCode.=  "	esliWindow_$esliFormOrGridName.setText(\"$esliFormOrGridName\");\n";
	$outputPhpCode.=  "	var esliLayout = esliWindow_$esliFormOrGridName.attachLayout('2E', 'dhx_skyblue');\n";
} else {
	$outputPhpCode.=  "	esliLayout = new dhtmlXLayoutObject(document.body,'2E','dhx_skyblue');\n";
}
$outputPhpCode.= "	var esliStatusBar = esliLayout.attachStatusBar();\n";
$outputPhpCode.= "	esliStatusBar.setText(\"<div class='esliCssDivStatusBar'><?php echo \"Logged In: \".\$sessionStaff; ?></div>\");\n";
$outputPhpCode.= "	esliLayout.cells('a').hideHeader();\n";
$outputPhpCode.= "	esliLayout.cells('a').setHeight(36);\n";
$outputPhpCode.= "	esliLayout.cells('a').attachObject('esliDivTop');\n";
$outputPhpCode.= "	esliLayout.cells('b').setText('$esliFormOrGridName');\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName = new dhtmlXGridObject('esliDivObject');\n";
$outputPhpCode.= "	esliLayout.cells('b').attachObject('esliDivGrid');\n";
$outputPhpCode.= "	".$esliAttachHeaderByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.attachHeader( esliAttachHeader );\n";
$outputPhpCode.= "	".$esliSetHeaderByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setHeader( esliSetHeader );\n";
$outputPhpCode.= "// Hide the id field by zeroing out width instead of setting it to hidden;\n";
$outputPhpCode.= "	".$esliSetInitWidthsByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setInitWidths( esliSetInitWidths );\n";
$outputPhpCode.= "	".$esliSetColAlignByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setColAlign( esliSetColAlign );\n";
$outputPhpCode.= "	".$esliSetColTypesByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setColTypes( esliSetColTypes );\n";
$outputPhpCode.= "// Hide the id field by zeroing out width instead of setting it to hidden;\n";
$outputPhpCode.= "	".$esliSetColumnsVisibilityByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setColumnsVisibility( esliSetColumnsVisibility );\n";
$outputPhpCode.= "	".$esliSetColumnIdsByField;
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setColumnIds( esliSetColumnIds );\n";
//$outputPhpCode.= "	".$esliConnectorAttachHeader.=";\n";
//$outputPhpCode.= "	".$esliSetHeader.=";\n";
//$outputPhpCode.= "	".$esliSetInitWidths.=";\n";
//$outputPhpCode.= "	".$esliSetColAlign.=";\n";
$outputPhpCode.= "	".$esliConnectorColumnSorting.=";\n";
$outputPhpCode.= "// ".$esliSetColSorting.=";\n";
//$outputPhpCode.= "	".$esliSetColTypes.=";\n";
//$outputPhpCode.= "	".$esliSetColumnsVisibility.=";\n";
//$outputPhpCode.= "	".$eslisetColumnIds.=";\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.enableEditEvents(true,false,false);\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setDateFormat('%Y-%m-%d');\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.attachFooter('Rows,#cspan,#stat_count');\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.enablePaging(true, 30, null, 'pagingArea', true);\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.setPagingSkin('bricks');\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.init();\n";
$outputPhpCode.= "	esliGridDP_$esliFormOrGridName = new dataProcessor ('codebase/xml/myConn_$esliFormOrGridName.php');\n";
$outputPhpCode.= "	esliGridDP_$esliFormOrGridName.attachEvent('onAfterUpdate', function(sid, action, tid, tag) {\n";
$outputPhpCode.= "		switch( action ) {\n";
$outputPhpCode.= "			case 'updated':\n";
$outputPhpCode.= "				dhtmlx.message('Record updated.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "			case 'inserted':\n";
$outputPhpCode.= "				esliGrid_$esliFormOrGridName.showRow(tid);\n";
$outputPhpCode.= "				esliGrid_$esliFormOrGridName.cells(tid,0).setValue(tid);\n";
$outputPhpCode.= "				dhtmlx.message('Record added.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "			case 'deleted':\n";
$outputPhpCode.= "				dhtmlx.message('Record deleted.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "			default:\n";
$outputPhpCode.= "				alert( 'Add,Update or Delete Failed: ' + action );\n";
$outputPhpCode.= "		}\n";
$outputPhpCode.= "	}) // end of onAfterUpdate event\n";
$outputPhpCode.= "//esliGrid_$esliFormOrGridName.attachEvent('onRowDblClicked', function(id,ind) {\n";
$outputPhpCode.= "//	var esliParameters = \"esliPassID=\" + id;\n";
$outputPhpCode.= "//	window.open(\"???????.php?\"+encodeURI(esliParameters));\n";
$outputPhpCode.= "//}) // end of onRowDblClicked event\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.attachEvent('onRowSelect', function(id,ind) {\n";
$outputPhpCode.= "		esliSelectedRowID = id;\n";
$outputPhpCode.= "	})  // end of onRowSelect event\n";
$outputPhpCode.= "	esliGridDP_$esliFormOrGridName.init( esliGrid_$esliFormOrGridName );\n";
$outputPhpCode.= "	esliLoadStatement = 'codebase/xml/myConn_$esliFormOrGridName.php';\n";
$outputPhpCode.= "// for filtering	esliLoadStatement = 'codebase/xml/myConn_$esliFormOrGridName.php?connector=true&dhx_filter[1]=<?php echo \$esliPassID; ?>';\n";
$outputPhpCode.= "	esliGrid_$esliFormOrGridName.load(esliLoadStatement, function() {\n";
$outputPhpCode.= "	}) //  end of load\n";
$outputPhpCode.= "\n";
$outputPhpCode.= "})  // end of load event\n";
$outputPhpCode.= "</script>\n";
$outputPhpCode.= "</head>\n";
$outputPhpCode.= "<body>\n";
$outputPhpCode.= "<div class='esliCssDivTop' id='esliDivTop'>\n";
$outputPhpCode.= "	<table border='0' cellpadding='0' cellspacing='0'>\n";
$outputPhpCode.= "		<tr>\n";
$outputPhpCode.= "			<td width='49'><a href='mainmenu.php'><img src='imgs/esli.png' width='49' height='36' alt='ESLI Logo' /></a></td>\n";
$outputPhpCode.= "			<td align='left' width='250'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$esliFormOrGridName</td>\n";
$outputPhpCode.= "		</tr>\n";
$outputPhpCode.= "	</table>\n";
$outputPhpCode.= "</div>\n";
$outputPhpCode.= "<div id='esliDivGrid'>\n";
$outputPhpCode.= "	<table width='100%'>\n";
$outputPhpCode.= "		<tr>\n";
$outputPhpCode.= "			<td>\n";
$outputPhpCode.= "			<div id='esliDivObject' style='width:100%; height:575px; background-color:white;overflow:hidden'></div>\n";
$outputPhpCode.= "			</td>\n";
$outputPhpCode.= "		</tr>\n";
$outputPhpCode.= "		<tr>\n";
$outputPhpCode.= "			<td id='pagingArea'></td>\n";
$outputPhpCode.= "		</tr>\n";
$outputPhpCode.= "<!--	<tr>\n";
$outputPhpCode.= "			<td><input id='esliAddRow' type='button' value='Add Row' onClick='esliOnClickAddDelete(this)' />&nbsp;&nbsp;&nbsp;<input id='esliDeleteRow' type='button' value='Delete Row' onClick='esliOnClickAddDelete(this)' /></td>\n";
$outputPhpCode.= "		</tr> -->\n";
$outputPhpCode.= "		<tr>\n";
$outputPhpCode.= "			<td></td>\n";
$outputPhpCode.= "		</tr>\n";
$outputPhpCode.= "	</table>\n";
$outputPhpCode.= "</div>\n";
if ( $esliWindowOrLayout == 'Window' ) {
	$outputPhpCode.= "<div id='esliDivWindow' style='width:100%;height:100%;overflow:auto;'></div>\n";
}
$outputPhpCode.= "</body>\n";
$outputPhpCode.= "</html>\n";
$outputPhpCode.= "\n";
$documentfolder = $documentroot."/";
$file = fopen($documentfolder."ag_".$esliFormOrGridName.".php","w+");
fwrite($file,$outputPhpCode);
fclose($file);
//===============
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?php echo "Generated Grid - $esliFormOrGridName : $esliSqlStatement"; ?></title>
<link rel="stylesheet" type="text/css" href="dhtmlx.css" />
<link rel="stylesheet" type="text/css" href="esli.css" />
<script src="dhtmlx.js"></script>
<script src="numeral.js"></script>
<style>
</style>
<script type="text/javascript">
<?php 
print "var esliGrid_$esliFormOrGridName;\n";
print "function esliIsNumeric(esliCheckValue) {\n";
print "	return !isNaN(parseFloat(esliCheckValue)) && isFinite(esliCheckValue);\n";
print "}\n";
print "function esliOnClickAddDelete ( esliID ) {\n";
print "	switch (esliID.id) {\n";
print "		case \"esliAddRow\":\n";
print "			esliGrid_$esliFormOrGridName.addRow(esliGrid_$esliFormOrGridName.uid(), [\"0\"], 0);\n";
print "		break;\n";
print "		case \"esliDeleteRow\":\n";
print "			if ( esliGrid_$esliFormOrGridName.getSelectedRowId() ) {\n";
print "		        dhtmlx.confirm( { title:\"Confirm\", ok:\"Yes\", cancel:\"No\", text:\"Deleting row \" + esliGrid_$esliFormOrGridName.getSelectedRowId() ,\n"; 
print "            		callback:function(result) {\n";
print "						if (result==true) {\n";
print "							esliGrid_$esliFormOrGridName.deleteRow(esliGrid_$esliFormOrGridName.getSelectedRowId());\n";
print "						}\n";
print "					}\n";
print "				});\n";
print "			}\n";
print "		break;\n";
print "	}\n";
print "}\n";
?>
dhtmlx.image_path = "imgs/";
dhtmlxEvent(window,"load",function() {
<?php
if ( $esliWindowOrLayout == 'Window' ) {
	print "	var esliWindow = new dhtmlXWindows();\n";
	print "	var esliWindow_$esliFormOrGridName = esliWindow.createWindow(\"esliDivWindow\", 90, 30, 900, 650);\n";
	print "	esliWindow_$esliFormOrGridName.setText(\"$esliFormOrGridName\");\n";
	print "	var esliLayout = esliWindow_$esliFormOrGridName.attachLayout('2E', 'dhx_skyblue');\n";
} else {
	print "	esliLayout = new dhtmlXLayoutObject(document.body,'2E','dhx_skyblue');\n";
}
?>
	var esliStatusBar = esliLayout.attachStatusBar();
	esliStatusBar.setText("<div class='esliCssDivStatusBar'><?php echo "Logged In: ".$sessionStaff; ?></div>");
	esliLayout.cells("a").hideHeader();
	esliLayout.cells("a").setHeight(36);
	esliLayout.cells("a").attachObject("esliDivTop");
	esliLayout.cells("b").setText("<?php echo "Grid - $esliFormOrGridName : $esliSqlStatement"; ?>");
<?php
print "	esliGrid_$esliFormOrGridName = new dhtmlXGridObject('esliDivObject');\n";
print "	esliLayout.cells(\"b\").attachObject(\"esliDivGrid\");\n";
print "	".$esliAttachHeaderByField;
print "	esliGrid_$esliFormOrGridName.attachHeader( esliAttachHeader );\n";
print "	".$esliSetHeaderByField;
print "	esliGrid_$esliFormOrGridName.setHeader( esliSetHeader );\n";
print "	".$esliSetInitWidthsByField;
print "	esliGrid_$esliFormOrGridName.setInitWidths( esliSetInitWidths );\n";
print "	".$esliSetColAlignByField;
print "	esliGrid_$esliFormOrGridName.setColAlign( esliSetColAlign );\n";
print "	".$esliSetColTypesByField;
print "	esliGrid_$esliFormOrGridName.setColTypes( esliSetColTypes );\n";
print "	".$esliSetColumnsVisibilityByField;
print "	esliGrid_$esliFormOrGridName.setColumnsVisibility( esliSetColumnsVisibility );\n";
print "	".$esliSetColumnIdsByField;
print "	esliGrid_$esliFormOrGridName.setColumnIds( esliSetColumnIds );\n";
//print "	".$esliConnectorAttachHeader.=";\n";
//print "	".$esliSetHeader.=";\n";
//print "	".$esliSetInitWidths.=";\n";
//print "	".$esliSetColAlign.=";\n";
print "	".$esliConnectorColumnSorting.=";\n";
print "// ".$esliSetColSorting.=";\n";
//print "	".$esliSetColTypes.=";\n";
//print "	".$esliSetColumnsVisibility.=";\n";
//print "	".$eslisetColumnIds.=";\n";
//print "// ".$esliAttachHeader.="\n";
print "	esliGrid_$esliFormOrGridName.enableEditEvents(true,false,false);\n";
print "	esliGrid_$esliFormOrGridName.setDateFormat(\"%Y-%m-%d\");\n";
print "//esliGrid_$esliFormOrGridName.setColumnExcellType(9,\"ron\");;\n";
print "//esliGrid_$esliFormOrGridName.setNumberFormat(\"$0,000.00\",9,\".\",\",\"); //(US English);\n";
print "	esliGrid_$esliFormOrGridName.attachFooter(\"Rows,#cspan,#stat_count\");\n";
print "	esliGrid_$esliFormOrGridName.enablePaging(true, $esliRowsPerPage, null, \"pagingArea\", true);\n";
print "	esliGrid_$esliFormOrGridName.setPagingSkin(\"bricks\");\n";
print "	esliGrid_$esliFormOrGridName.init();\n";
print "//esliGrid_$esliFormOrGridName.enableSmartRendering(true, 50);\n";
print "	esliGridDP_$esliFormOrGridName = new dataProcessor (\"codebase/xml/myConn_$esliFormOrGridName.php\");\n";
print "	esliGridDP_$esliFormOrGridName.attachEvent(\"onAfterUpdate\", function(sid, action, tid, tag) {\n";
print "		switch( action ) {\n";
print "			case \"updated\":\n";
print "				dhtmlx.message(\"Record updated.\");\n";
print "				break;\n";
print "			case \"inserted\":\n";
print "				esliGrid_$esliFormOrGridName.showRow(tid);\n";
print "				esliGrid_$esliFormOrGridName.cells(tid,0).setValue(tid);\n";
print "				dhtmlx.message(\"Record added.\");\n";
print "				break;\n";
print "			case \"deleted\":\n";
print "				dhtmlx.message(\"Record deleted.\");\n";
print "				break;\n";
print "		default:\n";
print "			alert( \"Add,Update or Delete Failed: \" + action );\n";
print "	}\n";
print "}) // end of onAfterUpdate event\n";
print "//esliGrid_$esliFormOrGridName.attachEvent('onRowDblClicked', function(id,ind) {\n";
print "//	var esliParameters = \"esliPassID=\" + id;\n";
print "//	window.open(\"???????.php?\"+encodeURI(esliParameters));\n";
print "//}) // end of onRowDblClicked event\n";
print "esliGridDP_$esliFormOrGridName.attachEvent(\"onRowSelect\", function(id,ind) {\n";
print "})  // end of onRowSelect event\n";
print "esliGridDP_$esliFormOrGridName.init( esliGrid_$esliFormOrGridName );\n";
print "esliLoadStatement = \"codebase/xml/myConn_$esliFormOrGridName.php\";\n";
print "esliGrid_$esliFormOrGridName.load(esliLoadStatement, function() {\n";
print "}) //  end of load \n";

?>

})  // end of load event
</script>
</head>
<body>
<div class="esliCssDivTop" id="esliDivTop">
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="49"><a href="mainmenu.php"><img src="imgs/esli.png" width="49" height="36" alt="ESLI Logo" /></a></td>
        <td align="left" width="250">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Automatic Grid Generation</td>
    </tr>
</table>
</div>
<div id="esliDivGrid">
<table width="100%">
    <tr>
        <td>
            <div id="esliDivObject" style="width:100%; height:575px; background-color:white;overflow:hidden"></div>
        </td>
    </tr>
    <tr>
        <td id="pagingArea"></td>
    </tr>
<!--<tr>
        <td><input id="esliAddRow" type="button" value="Add Row" onClick="esliOnClickAddDelete(this)" />&nbsp;&nbsp;&nbsp;<input id="esliDeleteRow" type="button" value="Delete Row" onClick="esliOnClickAddDelete(this)" /></td>
    </tr> -->
    <tr>
        <td></td>
    </tr>
</table>
</div>
<?php
if ( $esliWindowOrLayout == 'Window' ) {
	print "<div id='esliDivWindow' style='width:100%;height:100%;overflow:auto;'></div>\n";
}
?>
</body>
</html>
