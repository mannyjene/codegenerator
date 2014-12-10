<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Form Design / DHTMLX</title>
<link rel="stylesheet" type="text/css" href="dhtmlx.css" />
<script src="dhtmlx.js"></script>
<style>
@charset "UTF-8";
html, body {
   width: 100%;
   height: 100%;
   margin: 0px;
   background-color:white;
}
</style>
 
<script type="text/javascript">	
dhtmlx.image_path = "imgs/";
dhtmlxEvent(window,"load",function() {
	myMainLayout = new dhtmlXLayoutObject(document.body,"2E");
	myMainLayout.cells("a").setText("SQL Statement Used To Generate Form");
	myMainLayout.cells("b").setText("Form Fields Properties");
	mySqlToolbar = myMainLayout.cells('a').attachToolbar();
	mySqlToolbar.addButton("new", 0, "New", "imgs/plus_48.png");
	mySqlToolbar.addButton("delete", 1, "Delete", "imgs/minus_48.png");
	mySqlToolbar.addButton("generate", 2, "Generate Form Fields Basesd On SQL Statement", "imgs/checkmark_64.png");
	mySqlToolbar.attachEvent("onClick", function(id) {
		switch( id ) {
			case 'new':
				mySqlGrid.addRow(mySqlGrid.uid(), ["000", "New Row", "New Row"], 0);
				break;
			case "delete":
				if ( mySqlGrid.getSelectedRowId() ) {
					dhtmlx.confirm( { title:"Confirm", ok:"Yes", cancel:"No", text:"Deleting row " + mySqlGrid.getSelectedRowId() , 
						callback:function(result) {
							if (result==true) {
								mySqlGrid.deleteRow(mySqlGrid.getSelectedRowId());
							}
						}
					});
				}
			break;
			case "generate":
				var mySqlID = mySqlGrid.getSelectedRowId();
				if ( !mySqlGrid.cellById(mySqlID, 2).getValue() ) {
					dhtmlx.alert( { title:"Notice", type:"alert", text:"Please enter a form name before proceeding." } ); 
					return;
				}
				if ( !mySqlID ) {
					dhtmlx.alert( { title:"Notice", type:"alert", text:"Please select an SQL statement before proceeeding." } ); 
				} else {
					dhtmlx.confirm( { 
						title:"Caution",
						type:"confirm-warning",
						text:"Previous form fields will be deleted.",
						ok:"Continue",
						callback: function(result) {
							if (result==true) {
								var mySqlStatement = mySqlGrid.cellById(mySqlID, 1).getValue()
								myParameters = "sqlID=" + mySqlID + "&sqlStatement=" + mySqlStatement + " LIMIT 1,1";
								myURL = "formdesignfields.php?" + encodeURI(myParameters);
								window.dhx4.ajax.get( myURL, function( loader ) {
									if ( loader.xmlDoc.responseText == "Success" ) {
										dhtmlx.message("Records generated.");
										myFieldsLoadStatement = "codebase/xml/myConnFieldsForm.php?connector=true&dhx_filter[1]=" + mySqlID;
										myFieldsGrid.clearAndLoad( myFieldsLoadStatement );
									} else {
										myMessage = "Record creation failed:\n\n" + loader.xmlDoc.responseText;
										dhtmlx.alert( { title:"Notice", type:"alert", text:myMessage } ); 
									}
								});
							}
						}
					}); 
				}
			break;
		}
	});
	
	myFieldsToolbar = myMainLayout.cells('b').attachToolbar();
	myFieldsToolbar.addButton("new", 0, "New", "imgs/plus_48.png");
	myFieldsToolbar.addButton("delete", 1, "Delete", "imgs/minus_48.png");
	myFieldsToolbar.addButton("generate", 2, "Generate Code", "imgs/checkmark_64.png");
	myFieldsToolbar.attachEvent("onClick", function(id) {
		switch( id ) {
			case 'new':
				myFieldsGrid.addRow(myFieldsGrid.uid(), ["000", "New Row", "New Row"], 0);
				break;
			case "delete":
				if ( myFieldsGrid.getSelectedRowId() ) {
					dhtmlx.confirm( { title:"Confirm", ok:"Yes", cancel:"No", text:"Deleting row " + myFieldsGrid.getSelectedRowId() , 
						callback:function(result) {
							if (result==true) {
								myFieldsGrid.deleteRow(myFieldsGrid.getSelectedRowId());
							}
						}
					});
				}
			break;
			case "generate":
				var mySqlID = mySqlGrid.getSelectedRowId();
				if ( !mySqlID ) {
					dhtmlx.alert( { title:"Notice", type:"alert", text:"Please select an SQL statement before proceeeding." } ); 
				} else {
					var mySqlStatement = mySqlGrid.cellById(mySqlID, 1).getValue()
					myParameters = "sqlID=" + mySqlID + "&sqlStatement=" + mySqlStatement + " LIMIT 1,1";
					window.open("formdesigncode.php?" + encodeURI(myParameters));
				}
			break;
		}
	});

	mySqlGrid = myMainLayout.cells("a").attachGrid();
	mySqlGrid.setImagePath("imgs/");
	// mySqlGrid.attachHeader("#numeric_filter,#text_filter,#text_filter")
	mySqlGrid.setHeader("ID,SQL Statement,Form Name, Layout/Window")
	mySqlGrid.setInitWidths("60,1000,200,100")
	mySqlGrid.setColAlign("left,left,left,left")
    mySqlGrid.setColSorting("int,str,str,str");
	mySqlGrid.setColTypes("ron,ed,ed,combo")
	mySqlGrid.setColumnsVisibility("false,false,false,false")
	mySqlGrid.enableEditEvents(true,false,false);
	mySqlGrid.setDateFormat("%Y-%m-%d");
	//mySqlGrid.setNumberFormat("$0,000.00",9,".",","); //(US English);
	mySqlGrid._in_header_stat_rowcount=function(tag,index,data){//'stat_rowcount'-counter name
		var calc=function(){                       // function used for calculations
			return this.getRowsNum()+" rows";
		}
		this._stat_in_header(tag,calc,index,data); // default statistics handler processor
	}
	mySqlGrid.attachFooter("#stat_rowcount");
	mySqlGrid.init();
	mySqlGridDP = new dataProcessor ("codebase/xml/myConnSqlForm.php");
	mySqlGridDP.attachEvent("onAfterUpdate", function(sid, action, tid, tag) {
			switch( action ) {
				case "updated":
					dhtmlx.message("Record updated.");
					break;
				case "inserted":
					mySqlGrid.showRow(tid);
					mySqlGrid.cells(tid,0).setValue(tid);
					dhtmlx.message("Record added.");
					break;
				case "deleted":
					dhtmlx.message("Record deleted.");
					break;
			default:
				alert( "Add,Update or Delete Failed: " + action );
		}
	}) // end of onAfterUpdate event
	mySqlGrid.attachEvent("onRowSelect", function(id,ind) {
		myFieldsLoadStatement = "codebase/xml/myConnFieldsForm.php?connector=true&dhx_filter[1]=" + id;
		myFieldsGrid.clearAndLoad( myFieldsLoadStatement );
	})  // end of onRowSelect event
	mySqlGridDP.init( mySqlGrid );
	mySqlLoadStatement = "codebase/xml/myConnSqlForm.php";
	mySqlGrid.loadXML(mySqlLoadStatement, function() {
	}) //  end of load 

    myFieldsGrid = myMainLayout.cells("b").attachGrid();
    myFieldsGrid.setImagePath("imgs/");
	myFieldsGrid.setHeader("ID,SQL ID,Table,Field,Seq,Type,Name,Bind,Label,Required,Width,Align,Ignore,Note,DB Type,Column,Read Only")
	myFieldsGrid.setInitWidths("0,0,100,100,60,70,100,100,100,70,60,70,70,100,100,70,70")
	myFieldsGrid.setColAlign("right,right,left,left,left,left,left,left,left,center,left,left,center,left,left,center,center")
	myFieldsGrid.setColSorting("int,int,str,str,str,str,str,str,str,int,str,str,int,str,str,int,int")
	myFieldsGrid.setColTypes("edn,edn,ed,ed,ed,combo,ed,ed,ed,ch,ed,combo,ch,ed,combo,ch,ch")
	myFieldsGrid.setColumnsVisibility("true,true,false,false,false,false,false,false,false,false,false,false,false,false,false,false,false")
	myFieldsGrid.enableEditEvents(true,false,false);
	myFieldsGrid.enableDragAndDrop(true);
	myFieldsGrid._in_header_stat_rowcount=function(tag,index,data){//'stat_rowcount'-counter name
		var calc=function(){                       // function used for calculations
			return this.getRowsNum()+" rows";
		}
		this._stat_in_header(tag,calc,index,data); // default statistics handler processor
	}
	myFieldsGrid.attachFooter(",,,,#stat_rowcount");
    myFieldsGrid.init();
	mySqlGridDP
    myFieldsGridDP = new dataProcessor ("codebase/xml/myConnFieldsForm.php");
	myFieldsGridDP.attachEvent("onAfterUpdate", function(sid, action, tid, tag) {
		switch( action ) {
			case "updated":
				dhtmlx.message("Record updated.");
				break;
			case "inserted":
				myFieldsGrid.showRow(tid);
				myFieldsGrid.cells(tid,0).setValue(tid);
				dhtmlx.message("Record added.");
				break;
			case "deleted":
				dhtmlx.message("Record deleted.");
				break;
			default:
				alert( "Add,Update or Delete Failed: " + action );
		}
	})
	myFieldsGridDP.init(myFieldsGrid);
    myFieldsGrid.enableEditEvents(true,false,false);
	myFieldsGrid.attachEvent("onDrop", function(sId,tId,dId,sObj,tObj,sCol,tCol) {
		var ids=myFieldsGrid.getAllRowIds(",");
		idsArray = ids.split(",");
		for(var i=0;i<idsArray.length;i++){
			 id = idsArray[i];
			 	myOrder = i+1;
				myFieldsGrid.cells(id,4).setValue(myOrder);
				myFieldsGridDP.setUpdated(id,true,"updated");
		}
	});
    myFieldsLoadStatement = "codebase/xml/myConnFieldsForm.php";

}) //end of dhtmlxEvent(window,"load",function()
</script>
</head>
<body>
</body>
</html>
