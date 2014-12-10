<?php
require_once 'codebase/db_connectmysqli.php';
if(!isset($_GET['sqlID'])) {
	die("sqlID value not set");
}
$sqlID = $_GET['sqlID'];
$mySelectSQL = "SELECT tblSqlForm.* FROM tblSqlForm WHERE SqlID = $sqlID;";
//	print $mySelectSQL."<br />";
$mySqlResults = mysqli_query($mysqli, $mySelectSQL) or die(mysqli_error());
if (!$mySqlResults) {
	echo "No tblSqlForm records found for SQLID = $sqlID";
	exit;
}
$myRecords = mysqli_fetch_array($mySqlResults);
$mySqlStatement = $myRecords["sqlStatement"];
if (!$mySqlStatement) {
	echo "No SQL Statement found for SQLID = $sqlID";
	exit;
}
$myFormOrGridName = $myRecords["sqlFormName"];
if (!$myFormOrGridName) {
	echo "No Form Name found for SQLID = $sqlID";
	exit;
}
$myWindowOrLayout = $myRecords["sqlWindowOrLayout"];
if (!$myWindowOrLayout) {
	echo "No Grid Window or Layout found for SQLID = $sqlID";
	exit;
}
$myGroupSelectSQL = "SELECT designFormTable FROM tblDesignForm WHERE designFormSqlID = $sqlID AND designFormFieldIgnore = 0 GROUP BY designFormTable;";
//	print $myGroupSelectSQL."<br />";
$myGroupSelectResults = mysqli_query($mysqli, $myGroupSelectSQL) or die(mysqli_error());
if (!$myGroupSelectResults) {
	echo "No tblDesignForm records found for SQLID = $sqlID";
	exit;
}
$myTotalGroupRecords = mysqli_num_rows($myGroupSelectResults);
$myRecordNumber = 1;
$myTable1 ="No Table";
$myTable2 ="No Table";
$myTable3 ="No Table";
while ($myRecords = mysqli_fetch_array($myGroupSelectResults)) {
	if ($myRecordNumber == 1) {
		$myTable1 = $myRecords["designFormTable"];
	}
	if ($myRecordNumber == 2) {
		$myTable2 = $myRecords["designFormTable"];
	}
	if ($myRecordNumber == 3) {
		$myTable3 = $myRecords["designFormTable"];
	}
	$myRecordNumber++;
}
$mySelectSQL = "SELECT tblDesignForm.* FROM tblDesignForm WHERE designFormSqlID = $sqlID AND designFormFieldIgnore = 0 ORDER BY designFormFieldOrder ASC;";
//	print $mySelectSQL."<br />";
$mySelectResults = mysqli_query($mysqli, $mySelectSQL) or die(mysqli_error());
if (!$mySelectResults) {
	echo "No tblDesignForm records found for SQLID = $sqlID";
	exit;
}
$myRecordNumber = 1;
$myTotalRecords = mysqli_num_rows($mySelectResults);
$myField = 0;
$myFields = "";
$myOutput = "";
$myOutput.= "<div class='esliCssDivResult'>"; 
$myTab = "&nbsp;&nbsp;&nbsp;&nbsp;";
$myOutputFormConnectorFormatting = "<pre>";
$myFormConnectorCode = "";
$myOutputFormConnector = "<pre>";
$myOutputFormData = "<pre>";
$myOutputFormData.="var esliFormJson_$myFormOrGridName = [\n";
$myOutputFormData.="    {type:\"block\", name: \"data\", list:[\n";
$myOutputFormData.="    {type:\"settings\", position:\"label-left\", labelWidth:\"130\", offsetLeft:\"10\", inputWidth:\"250\"},\n";
$myOutputFormData.="    {type:\"label\", label:\"$myFormOrGridName\", offsetLeft:\"5\"},\n";
while ($myRecords = mysqli_fetch_array($mySelectResults)) {
	$myFieldType = $myRecords["designFormFieldTypeDB"];
	if ($myRecordNumber == 1) {
		$myKeyField = $myRecords["designFormFieldBind"];
		$myOutputFormConnector.="\$myFields = \"".$myRecords["designFormFieldBind"].",\";\n";
		$myOutputFormConnectorFormatting.= "&lt;?php<br />";
		$myOutputFormConnectorFormatting.= "require(\"db_connectorForm.php\");\n<br />";
		$myOutputFormConnectorFormatting.= "function esliFormatting(\$esliRow){<br />";
		$myOutputFormConnectorFormatting.= "}<br /><br />";
		$myOutputFormConnectorFormatting.= "//function esliBeforeUpdate(\$action) {\n";
		$myOutputFormConnectorFormatting.= "// \$action->set_value(\"UpDatedTime\", date(\"Y-m-d H:i:s\"));\n";
		$myOutputFormConnectorFormatting.= "// \$action->set_value(\"UpDatedByUser\", \$_SESSION['staff']);\n";
		$myOutputFormConnectorFormatting.= "//}\n";
		$myOutputFormConnectorFormatting.= "function esliCustomFilter(\$filter_by){<br />";
		$myOutputFormConnectorFormatting.= "    \$index = \$filter_by->index(\"$myKeyField\");<br />";
		$myOutputFormConnectorFormatting.= "        if (\$index!==false) {<br />";
		$myOutputFormConnectorFormatting.= "            \$filter_by->rules[\$index][\"operation\"]= \" = \";<br />";
		$myOutputFormConnectorFormatting.= "        }<br />";
		$myOutputFormConnectorFormatting.= "}";
	} else {
		if ( $myRecordNumber == $myTotalRecords ) { 
			$myOutputFormConnector.="\$myFields.= \"".$myRecords["designFormFieldBind"]."\";\n";
		} else {
			$myOutputFormConnector.="\$myFields.= \"".$myRecords["designFormFieldBind"].",\";\n";
		}
	}
	if ($myRecordNumber == $myTotalRecords ) {
		$myFields.= "\$myFields.= \"".$myRecords["designFormFieldBind"]."\";  //  ".$myRecords["designFormTable"].".".$myRecords["designFormFieldBind"]."\n";
	} else {
		if ($myRecordNumber == 1) {
			$myKeyField = $myRecords["designFormFieldBind"];
			$myFields.= "\$myFields = \"".$myRecords["designFormFieldBind"].",\";  //  ".$myRecords["designFormTable"].".".$myRecords["designFormFieldBind"]."\n";
		} else {
			$myFields.= "\$myFields.= \"".$myRecords["designFormFieldBind"].",\";  //  ".$myRecords["designFormTable"].".".$myRecords["designFormFieldBind"]."\n";
		}
	}
//	$myCountMinus1 = $myCount - 1;
	$myFieldParameters = "name:\"".$myRecords["designFormFieldName"]."\", ";
	$myFieldParameters.= "bind:\"".$myRecords["designFormFieldBind"]."\", ";
	$myFieldParameters.= "label:\"".$myRecords["designFormFieldLabel"]."\"";
	if ($myRecords["designFormFieldRequired"] == true ) {
		$myFieldParameters.= ", required: true";
	}
	if ( $myFieldType != "tiny" ) {
		$myFieldParameters.= ", inputWidth:\"".$myRecords["designFormFieldWidth"]."\"";
		$myFieldParameters.= ", style:\"text-align:".$myRecords["designFormFieldAlign"]."\"";
		if ( $myRecords["designFormReadOnly"] == true ) {
			$myFieldParameters.= ", readonly:\"true\", style:\"background:#FFC; color:#00F;\"";
		}
	}
	if ( !empty ( $myRecords["designFormFieldNote"] ) ) {
		$myFieldParameters.= ", note: { text: \"".$myRecords["designFormFieldNote"]."\", width:\"".$myRecords["designFormFieldWidth"]."\"}";
	}
	switch ( $myFieldType ) {
		case "tiny":
			$myOutputFormData.="    {type:\"checkbox\", $myFieldParameters, checked:false},\n";
			break;
		case "decimal":
			$myOutputFormData.="    {type:\"input\", numberFormat: \"0,000.00\", $myFieldParameters},\n";
			break;
		case "double":
			$myOutputFormData.="    {type:\"input\", numberFormat: \"0,000.00\", $myFieldParameters},\n";
			break;
		case "long":
			$myOutputFormData.="    {type:\"input\", numberFormat: \"000\", $myFieldParameters},\n";
			break;
		case "short":
			$myOutputFormData.="    {type:\"input\", numberFormat: \"000\", $myFieldParameters},\n";
			break;
		case "var_string":
			$myOutputFormData.="    {type:\"input\", $myFieldParameters},\n";
			break;
		case "date":
			$myOutputFormData.="    {type:\"calendar\",  dateFormat:\"%Y-%m-%d\", $myFieldParameters},\n";
		  break;
		case "timestamp":
			$myOutputFormData.="    {type:\"calendar\", dateFormat:\"%Y-%m-%d %h:%i:%s\", $myFieldParameters},\n";
			break;
		default:
			$myOutputFormData.="    {type:\"input\", $myFieldParameters},\n";
	} // end of switch
	if ($myRecords["designFormFieldColumns"] == true ) {
		$myOutputFormData.="    {type:\"newcolumn\", offset:\"10\"},\n";
	}
	$myRecordNumber++;
	$myClass = "esliCssInstructions";
	if ($myRecordNumber % 2 == 0) {
		$myClass = "esliCssTrEven";
	}
}
//$myOutputFormData.="    {type:\"block\", name:\"data\", blockOffset:\"0\", list:[\n";
//$myOutputFormData.="    {type:\"button\", xcommand:\"save\", name:\"save\", value:\"Save\", width:\"50\", offsetTop:\"10\", offsetLeft:\"0\"},\n";
//$myOutputFormData.="    {type: \"newcolumn\", offset:\"10\"},\n";
//$myOutputFormData.="    {type:\"button\", xcommand:\"add\", name:\"add\", value:\"Add\", width:\"50\", offsetTop:\"10\", offsetLeft:\"0\"}]},\n";
//$myOutputFormData.="    {type: \"newcolumn\", offset:\"10\"},\n";
//$myOutputFormData.="    {type:\"button\", xcommand:\"delete\", name:\"delete\", value:\"Delete\", width:\"50\", offsetTop:\"10\", offsetLeft:\"0\"},\n";
//$myOutputFormData.="    {type:\"label\", label:\"\"}\n";
$myOutputFormData.="    ]}\n";
$myOutputFormData.="]\n";
$documentroot = dirname( __FILE__ );
$myLogFile = "connector.log";
$myFormConnectorCode ="<?php\n";
$myFormConnectorCode.= "require(\"db_ConnectorForm.php\");\n\n";
$myFormConnectorCode.= "if( !isset(\$_COOKIE['user'])) {\n";
$myFormConnectorCode.= "	\$esliCookieUser = \"No Coookie User\";\n";
$myFormConnectorCode.= "} else {\n";
$myFormConnectorCode.= "	\$esliCookieUser = \$_COOKIE['user'];\n";
$myFormConnectorCode.= "}\n";
$myFormConnectorCode.= "function esliFormatting(\$esliRow) {\n";
$myFormConnectorCode.= "}\n";
$myFormConnectorCode.= "//function esliBeforeUpdate(\$action) {\n";
$myFormConnectorCode.= "// \$action->set_value(\"UpDatedTime\", date(\"Y-m-d H:i:s\"));\n";
$myFormConnectorCode.= "// global \$esliCookieUser;\n";
$myFormConnectorCode.= "// \$action->set_value(\"UpdatedByUser\", \$esliCookieUser );\n";
$myFormConnectorCode.= "// \$action->set_value(\"UpDatedByUser\", \$_SESSION['staff']);\n";
$myFormConnectorCode.= "//}\n";
$myFormConnectorCode.= "function esliCustomFilter(\$filter_by){\n";
$myFormConnectorCode.= "	\$index = \$filter_by->index(\"$myKeyField\");\n";
$myFormConnectorCode.= "		if (\$index!==false) {\n";
$myFormConnectorCode.= "			\$filter_by->rules[\$index][\"operation\"]= \" = \";\n";
$myFormConnectorCode.= "		}\n";
$myFormConnectorCode.= "}\n\n";
$myFormConnectorCode.= "$myFields\n";
$myFormConnectorCode.= "\$esliSqlForm = \"$mySqlStatement\";\n";
$myFormConnectorCode.= "\$formConnector->access->deny(\"delete\"); //blocks Delete action\n";
$myFormConnectorCode.= "//\$formConnector->event->attach(\"beforeUpdate\", \"esliBeforeUpdate\");\n";
$myFormConnectorCode.= "\$formConnector->event->attach(\"beforeRender\",\"esliFormatting\");\n";
$myFormConnectorCode.= "\$formConnector->event->attach(\"beforeFilter\",\"esliCustomFilter\");\n";
$myFormConnectorCode.= "\$formConnector->enable_log(\"$myLogFile\",false);\n";
$myFormConnectorCode.= "\$formConnector->set_encoding(\"iso-8859-1\");\n";
$myFormConnectorCode.= "\$formConnector->render_sql(\$esliSqlForm,\"$myKeyField\",\$myFields);\n";
$myFormConnectorCode.= "?>\n";
$documentfolder = $documentroot."/codebase/xml/";
//print "<span class=\"esliCssLabel\">The following user ".exec('whoami')." is attempting to write to the file ".$documentfolder."</span>\n\n";
$file = fopen($documentfolder."formConn_".$myFormOrGridName.".php","w+");
fwrite($file,$myFormConnectorCode);
fclose($file);
$mySelectResults = mysqli_query($mysqli, $mySqlStatement) or die(mysqli_error());
$myRecords = mysqli_fetch_array($mySelectResults);
$myKeyFieldID = 0;
if ($myRecords) {
	$myKeyFieldID = $myRecords["$myKeyField"];
}

mysqli_free_result($mySelectResults);
mysqli_close($mysqli);
$myFormData = str_replace("<pre>","",$myOutputFormData);
$myFormData = str_replace("</pre>","",$myFormData);
//====================
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
$outputPhpCode.= "<title>ESLI Admin - Form Design</title>\n";
$outputPhpCode.= "<link rel='stylesheet' type='text/css' href='dhtmlx.css' />\n";
$outputPhpCode.= "<link rel='stylesheet' type='text/css' href='esli.css' />\n";
$outputPhpCode.= "<script src='dhtmlx.js'></script>\n";
$outputPhpCode.= "<script src='numeral.js'></script>\n";
$outputPhpCode.= "<style>\n";
$outputPhpCode.= "</style>\n";
$outputPhpCode.= "\n";
$outputPhpCode.= "<script type='text/javascript'>\n";
$outputPhpCode.= "\n";
$outputPhpCode.= "function esliOnClickButton ( esliID ) {\n";
$outputPhpCode.= "	switch (esliID.id) {\n";
$outputPhpCode.= "		case 'esliSave':\n";
$outputPhpCode.= "			//esliForm_$myFormOrGridName.setItemValue('UpdatedTime', new Date().toISOString().slice(0, 19).replace('T', ' ') );\n";
$outputPhpCode.= "			esliForm_$myFormOrGridName.save();\n";
$outputPhpCode.= "			break;\n";
$outputPhpCode.= "	}\n";
$outputPhpCode.= "}\n";
$outputPhpCode.= "\n";
$outputPhpCode.= $myFormData;
$outputPhpCode.= "\n";
$outputPhpCode.= "dhtmlx.image_path = 'imgs/';\n";
$outputPhpCode.= "dhtmlxEvent(window,'load',function() {\n";
if ( $myWindowOrLayout == 'Window' ) {
	$outputPhpCode.=  "	var esliWindow = new dhtmlXWindows();\n";
	$outputPhpCode.=  "	var esliWindow_$myFormOrGridName = esliWindow.createWindow(\"esliDivWindow\", 90, 30, 900, 650);\n";
	$outputPhpCode.=  "	esliWindow_$myFormOrGridName.setText(\"$myFormOrGridName\");\n";
	$outputPhpCode.=  "	var esliLayout = esliWindow_$myFormOrGridName.attachLayout('2E', 'dhx_skyblue');\n";
} else {
	$outputPhpCode.=  "	esliLayout = new dhtmlXLayoutObject(document.body,'2E','dhx_skyblue');\n";
}
$outputPhpCode.= "	var esliStatusBar = esliLayout.attachStatusBar();\n";
$outputPhpCode.= "	esliStatusBar.setText(\"<div class='esliCssDivStatusBar'><?php echo \"Logged In: \".\$sessionStaff; ?></div>\");\n";
$outputPhpCode.= "	esliLayout.cells('a').hideHeader();\n";
$outputPhpCode.= "	esliLayout.cells('a').setHeight(36);\n";
$outputPhpCode.= "	esliLayout.cells('a').attachObject('esliDivTop');\n";
$outputPhpCode.= "	esliLayout.cells('b').setText('$myFormOrGridName');\n";
//$outputPhpCode.= "	esliLayout.cells('b').attachObject('esliDivObject');\n";
$outputPhpCode.= "	esliForm_$myFormOrGridName = esliLayout.cells('b').attachForm(esliFormJson_$myFormOrGridName);\n";
$outputPhpCode.= "	esliForm_$myFormOrGridName.setSkin('dhx_skyblue');\n";
$outputPhpCode.= "	var esliFormDP_$myFormOrGridName = new dataProcessor('codebase/xml/formConn_$myFormOrGridName.php');\n";
$outputPhpCode.= "	esliFormDP_$myFormOrGridName.init(esliForm_$myFormOrGridName);\n";
$outputPhpCode.= "	esliFormDP_$myFormOrGridName.attachEvent('onAfterUpdate',function(sid,action,tid,xml_node) {\n";
$outputPhpCode.= "		switch( action ) {\n";
$outputPhpCode.= "			case 'updated':\n";
$outputPhpCode.= "				dhtmlx.message('Record updated.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "			case 'inserted':\n";
$outputPhpCode.= "				esliForm_$myFormOrGridName.setItemValue('$myKeyField', tid );\n";
$outputPhpCode.= "				dhtmlx.message('Record added.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "			case 'deleted':\n";
$outputPhpCode.= "				dhtmlx.message('Record deleted.');\n";
$outputPhpCode.= "				break;\n";
$outputPhpCode.= "		} // end of switch\n";
$outputPhpCode.= "	}); // end of onAfterUpdate event\n";
$outputPhpCode.= "	esliForm_$myFormOrGridName.attachEvent(\"onInfo\", function (name) {\n";
$outputPhpCode.= "		dhtmlx.alert( { title:\"Help Sample\", type:\"alert\", text:\"Example of adding help to an input\" } );\n";
$outputPhpCode.= "	});\n";
$outputPhpCode.= "	var esliEvent = esliForm_$myFormOrGridName.attachEvent('onEnter', function() {\n";
$outputPhpCode.= "	//esliForm_$myFormOrGridName.setItemValue('UpdatedTime', new Date().toISOString().slice(0, 19).replace('T', ' ') );\n";
$outputPhpCode.= "		esliForm_$myFormOrGridName.save();\n";
$outputPhpCode.= "	});// end of onEnter event\n";
$outputPhpCode.= "	esliForm_$myFormOrGridName.load('codebase/xml/formConn_$myFormOrGridName.php?id=<?php echo \$esliPassID; ?>');\n";
$outputPhpCode.= "}) //end of dhtmlxEvent(window,'load',function()\n";
$outputPhpCode.= "</script>\n";
$outputPhpCode.= "</head>\n";
$outputPhpCode.= "\n";
$outputPhpCode.= "<body>\n";
$outputPhpCode.= "<div class='esliCssDivTop' id='esliDivTop'>\n";
$outputPhpCode.= "	<table border='0' cellpadding='0' cellspacing='0'>\n";
$outputPhpCode.= "		<tr>\n";
$outputPhpCode.= "			<td width='49'><a href='mainmenu.php'><img src='imgs/esli.png' width='49' height='36' alt='ESLI Logo' /></a></td>\n";
$outputPhpCode.= "			<td align='left' width='250'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$myFormOrGridName</td>\n";
$outputPhpCode.= "		</tr>\n";
$outputPhpCode.= "	</table>\n";
$outputPhpCode.= "</div>\n";
if ( $myWindowOrLayout == 'Window' ) {
	$outputPhpCode.= "<div id='esliDivWindow' style='width:100%;height:100%;overflow:auto;'></div>\n";
}
$outputPhpCode.= "</body>\n";
$outputPhpCode.= "</html>\n";
$outputPhpCode.= "\n";
$documentfolder = $documentroot."/";
$file = fopen($documentfolder."af_".$myFormOrGridName.".php","w+");
fwrite($file,$outputPhpCode);
fclose($file);
//====================
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>ESLI Admin - Form Design</title>
<link rel="stylesheet" type="text/css" href="dhtmlx.css" />
<link rel="stylesheet" type="text/css" href="esli.css" />
<script src="dhtmlx.js"></script>
<script src="numeral.js"></script>
<style>
</style>
 
<script type="text/javascript">	

function esliOnClickButton ( esliID ) {
	switch (esliID.id) {
	case "esliSave":
			<?php print "//esliForm_$myFormOrGridName.setItemValue(\"UpdatedTime\", new Date().toISOString().slice(0, 19).replace('T', ' ') );\n"; ?>
        	<?php print("esliForm_$myFormOrGridName"); ?>.save();
			break;
	}
}

<?php
echo $myFormData;
?>

dhtmlx.image_path = "imgs/";
dhtmlxEvent(window,"load",function() {
<?php
if ( $myWindowOrLayout == 'Window' ) {
	print "var esliWindow = new dhtmlXWindows();\n";
	print "var esliWindow_$myFormOrGridName = esliWindow.createWindow(\"esliDivWindow\", 90, 30, 900, 650);\n";
	print "esliWindow_$myFormOrGridName.setText(\"$myFormOrGridName\");\n";
	print "var esliLayout = esliWindow_$myFormOrGridName.attachLayout('2E', 'dhx_skyblue');\n";
} else {
	print "esliLayout = new dhtmlXLayoutObject(document.body,'2E','dhx_skyblue');\n";
}
?>
	var esliStatusBar = esliLayout.attachStatusBar();
	esliStatusBar.setText("<div class='esliCssDivStatusBar'><?php echo "Logged In: ".$sessionStaff; ?></div>");
	esliLayout.cells("a").hideHeader();
	esliLayout.cells("a").setHeight(36);
	esliLayout.cells("a").attachObject("esliDivTop");
	esliLayout.cells("b").setText("<?php echo "Form - $myFormOrGridName : $mySqlStatement"; ?>");
<?php
print "	esliForm_$myFormOrGridName = esliLayout.cells(\"b\").attachForm(esliFormJson_$myFormOrGridName);\n";
print "	esliForm_$myFormOrGridName.setSkin(\"dhx_skyblue\");\n";
print "	var esliFormDP_$myFormOrGridName = new dataProcessor(\"codebase/xml/formConn_$myFormOrGridName.php\");\n";
print "	esliFormDP_$myFormOrGridName.init(esliForm_$myFormOrGridName);\n";

print "	esliFormDP_$myFormOrGridName.attachEvent(\"onAfterUpdate\",function(sid,action,tid,xml_node) {\n";
print "		switch( action ) {\n";
print "			case \"updated\":\n";
print "				dhtmlx.message(\"Record updated.\");\n";
print "			break;\n";
print "		case \"inserted\":\n";
print "			esliForm_$myFormOrGridName.setItemValue(\"$myKeyField\", tid );\n";
print "			dhtmlx.message(\"Record added.\");\n";
print "			break;\n";
print "		case \"deleted\":\n";
print "			dhtmlx.message(\"Record deleted.\");\n";
print "			break;\n";
print "	 	}\n";
print "    }); // end of onAfterUpdate event\n";
print "    var esliEvent = esliForm_$myFormOrGridName.attachEvent(\"onEnter\", function(){\n";
print "//        esliForm_$myFormOrGridName.setItemValue(\"UpdatedTime\", new Date().toISOString().slice(0, 19).replace('T', ' ') );\n";
print "        esliForm_$myFormOrGridName.save();\n";
print "    });// end of onEnter event\n";
print "	esliForm_$myFormOrGridName.load(\"codebase/xml/formConn_$myFormOrGridName.php?id=\<?php echo \$esliPassID; ?>\");\n";
?>
}) //end of dhtmlxEvent(window,"load",function()
</script>
</head>
 
<body>
<div class="esliCssDivTop" id="esliDivTop">
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="49"><a href="mainmenu.php"><img src="imgs/esli.png" width="49" height="36" alt="ESLI Logo" /></a></td>
        <td align="left" width="250">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Automatic Form Generation</td>
    </tr>
</table>
</div>
<?php
if ( $myWindowOrLayout == 'Window' ) {
	print "<div id='esliDivWindow' style='width:100%;height:100%;overflow:auto;'></div>\n";
}
?>
</body>
</html>
