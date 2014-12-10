<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<html xmlns='http://www.w3.org/1999/xhtml'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
<title>Grid Design</title>
<link rel='stylesheet' type='text/css' href='dhtmlx.css' />
<!--<script src='dhtmlxdataprocessor_debug.js'></script>-->
<script src='dhtmlx.js'></script>
<style>
@charset "UTF-8";
html, body {
   width: 100%;
   height: 100%;
   margin: 0px;
   background-color:white;
}
</style>
<script type='text/javascript'>
var mySqlGrid;
var myFieldsGrid;
var mySelectedRowID;
dhtmlx.image_path = 'imgs/';
dhtmlxEvent(window,'load',function() {
	myMainLayout = new dhtmlXLayoutObject(document.body,'2E');
	myMainLayout.cells('a').setText('SQL Statement Used To Generate Grid');
	myMainLayout.cells("b").setText("Grid Column Properties");
	mySqlToolbar = myMainLayout.cells('a').attachToolbar();
	mySqlToolbar.addButton("new", 0, "New", "imgs/plus_48.png");
	mySqlToolbar.addButton("delete", 1, "Delete", "imgs/minus_48.png");
	mySqlToolbar.addButton("generate", 2, "Generate Grid Fields Basesd On SQL Statement", "imgs/checkmark_64.png");
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
					dhtmlx.alert( { title:"Notice", type:"alert", text:"Please enter a grid name before proceeding." } ); 
					return;
				}
				if ( !mySqlID ) {
					dhtmlx.alert( { title:"Notice", type:"alert", text:"Please select an SQL statement before proceeeding." } ); 
				} else {
					dhtmlx.confirm( { 
						title:"Caution",
						type:"confirm-warning",
						text:"Previous grid columns will be deleted.",
						ok:"Continue",
						callback: function(result) {
							if (result==true) {
								var mySqlStatement = mySqlGrid.cellById(mySqlID, 1).getValue()
								myParameters = "sqlID=" + mySqlID + "&sqlStatement=" + mySqlStatement + " LIMIT 1,1";
								myURL = "griddesignfields.php?" + encodeURI(myParameters);
								window.dhx4.ajax.get( myURL, function( loader ) {
									if ( loader.xmlDoc.responseText == "Success" ) {
										dhtmlx.message("Records generated.");
										myFieldsLoadStatement = "codebase/xml/myConnFields.php?connector=true&dhx_filter[1]=" + mySqlID;
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
					window.open("griddesigncode.php?" + encodeURI(myParameters));
				}
			break;
		}
	});

	mySqlGrid = myMainLayout.cells('a').attachGrid();
    mySqlGrid.setHeader("ID,SQL Statement, Grid Name, Layout/Window");
    mySqlGrid.setInitWidths("60,1000,200,100");
    mySqlGrid.setColAlign("left,left,left,left");
    mySqlGrid.setColSorting("int,str,str,str");
	mySqlGrid.setColTypes("ron,ed,ed,combo");
	mySqlGrid.setColumnsVisibility("false,false,false,false");
	mySqlGrid._in_header_stat_rowcount=function(tag,index,data){//'stat_rowcount'-counter name
		var calc=function(){                       // function used for calculations
			return this.getRowsNum()+" rows";
		}
		this._stat_in_header(tag,calc,index,data); // default statistics handler processor
	}
	mySqlGrid.attachFooter("#stat_rowcount");
	mySqlGrid.enableEditEvents(true,false,false);
	mySqlGrid.setDateFormat('%Y-%m-%d');
	mySqlGrid.init();
	mySqlGridDP = new dataProcessor ('codebase/xml/myConnSql.php');
	mySqlGridDP.attachEvent('onAfterUpdate', function(sid, action, tid, tag) {
		switch( action ) {
			case 'updated':
				dhtmlx.message('Record updated.');
				break;
			case 'inserted':
				mySqlGrid.showRow(tid);
				mySqlGrid.cells(tid,0).setValue(tid);
				dhtmlx.message('Record added.');
				break;
			case 'deleted':
				dhtmlx.message('Record deleted.');
				break;
			default:
				alert( 'Add,Update or Delete Failed: ' + action );
		}
	}) // end of onAfterUpdate event
	mySqlGrid.attachEvent('onRowSelect', function(id,ind) {
		myFieldsLoadStatement = "codebase/xml/myConnFields.php?connector=true&dhx_filter[1]="+id;
		myFieldsGrid.clearAndLoad( myFieldsLoadStatement );
	})  // end of onRowSelect event
	mySqlGridDP.init( mySqlGrid );
	mySqlLoadStatement = 'codebase/xml/myConnSql.php';
	mySqlGrid.load(mySqlLoadStatement, function() {
	}) //  end of load

	myFieldsGrid = myMainLayout.cells('b').attachGrid();
	myFieldsGrid.setHeader("ID,SQL ID,Table,Field,Seq,Label,Type,Width,Sorting,Align,Filter,Ignore,Update,Visible,DB Type")
	myFieldsGrid.setInitWidths("0,0,100,150,60,100,150,60,60,80,150,70,70,70,100")
	myFieldsGrid.setColAlign("right,right,left,left,left,left,left,left,left,left,left,center,center,center,left")
	myFieldsGrid.setColSorting("int,int,str,str,str,str,str,str,str,str,str,int,int,int,str")
	myFieldsGrid.setColTypes("ron,ron,ro,ro,edn,edtxt,combo,edn,combo,combo,combo,ch,ch,ch,combo")
	myFieldsGrid.setColumnsVisibility("false,false,false,false,false,false,false,false,false,false,false,false,false,false,false")
	myFieldsGrid.enableDragAndDrop(true);
	myFieldsGrid._in_header_stat_rowcount=function(tag,index,data){//'stat_rowcount'-counter name
		var calc=function(){                       // function used for calculations
			return this.getRowsNum()+" rows";
		}
		this._stat_in_header(tag,calc,index,data); // default statistics handler processor
	}
	myFieldsGrid.attachFooter(",,#stat_rowcount");
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
	myFieldsGrid.setDateFormat('%Y-%m-%d');
	myFieldsGrid.init();
	myFieldsGridDP = new dataProcessor ('codebase/xml/myConnFields.php');
	myFieldsGridDP.attachEvent('onAfterUpdate', function(sid, action, tid, tag) {
		switch( action ) {
			case 'updated':
				dhtmlx.message('Record updated.');
				break;
			case 'inserted':
				myFieldsGrid.showRow(tid);
				myFieldsGrid.cells(tid,0).setValue(tid);
				dhtmlx.message('Record added.');
				break;
			case 'deleted':
				dhtmlx.message('Record deleted.');
				break;
			default:
				alert( 'Add,Update or Delete Failed: ' + action );
		}
	}) // end of onAfterUpdate event
	myFieldsGrid.attachEvent('onRowSelect', function(id,ind) {
		mySelectedRowID = id;
	})  // end of onRowSelect event
	myFieldsGridDP.init( myFieldsGrid );
//	myFieldsLoadStatement = 'codebase/xml/myConnFields.php';
//	myFieldsGrid.load(myFieldsLoadStatement, function() {
//	}) //  end of load

})  // end of load event
</script>
</head>
<body>
</body>
</html>

