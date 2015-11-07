/*!ZZ
 * User Software Provenance JS 
 * History
 * 2015-Oct
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function usp(sysHost, startDate, endDate, userId) {        /* get exec list */

    var jsonTableData = $.ajax(
            {url: "include/uspExecList.php",
            data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId,
            dataType:"json", async: false
            }).responseText;


    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_exec_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblExec0', 'usp_exec_div', 'lblExec1',
        'lblExecDetail0', 'usp_exDetail_div', 'lblUspRun0', 'usp_run_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count!=0) {

        document.getElementById("lblExec0").style.visibility = 'visible';
        document.getElementById("usp_exec_div").style.visibility = 'visible';
        document.getElementById("lblExec1").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);


        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var exec = [TableData.getValue(row,0)];

            gTu0(sysHost, startDate, endDate, userId, exec);
        }
    }
}

function gTu0(syshost, startDate, endDate, userId, exec) {         /* get exec detail list  */

    var jsonTableData = $.ajax
        ({url: "include/uspExecDetail.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId + "&exec=" + exec,
         dataType:"json", async: false
         }).responseText;

    var div_id = 'usp_exDetail_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblExecDetail0', 'usp_exDetail_div', 'lblUspRun0', 'usp_run_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count!=0) {

        document.getElementById("lblExecDetail0").style.visibility = 'visible';
        document.getElementById("usp_exDetail_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,0);

            console.log(uuid);
            // gT1(sysHost, startDate, endDate, module, version);
        }
    }
}   

/* gTu1:
 * userId is not needed as User A might be using exec compiled by user B   
 * Date Range is also not required as we don't care if it was comipled by User xyz at xyz time
 */
function gTu1(sysHost, uuid) {  

    var jsonTableData = $.ajax(
            {
url:"include/uspObjDetail.php", 
data: "sysHost=" + sysHost + "&uuid=" + uuid,
datatype: "json", async: false
}).responseText;

var div_id = 'usp3_div';

console.log(jsonTableData);
// Create our datatable out of Json Data loaded from php call.
var TableData = new google.visualization.DataTable(jsonTableData);
var table = makeTable(TableData, div_id);

// Add our Actions handler.
google.visualization.events.addListener(table, 'select', selectHandler);

function selectHandler() {

    // grab a few details before redirecting
    var selection = table.getSelection();
    var row = selection[0].row;
    var col = selection[0].column;
    var user = TableData.getValue(row,0);

    console.log("sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate);
    //        gT2(module, user);
}
}   

function gT2(module,user) {                              /* GenerateTable LIST EXECUTABLES   */

    var jsonTableData = $.ajax(
            {url:"include/execList.php", 
data: "user=" +user + "&module=" + module,
datatype: "json", async: false
}).responseText;

var div_id = 'exec_div';

// Create our datatable out of Json Data loaded from php call.
var TableData = new google.visualization.DataTable(jsonTableData);
var table = makeTable(TableData, div_id);

// Add our Actions handler.
google.visualization.events.addListener(table, 'select', selectHandler);

function selectHandler() {

    // grab a few details before redirecting
    var selection = table.getSelection();
    var row = selection[0].row;
    var col = selection[0].column;
    var exec = TableData.getValue(row,0);

    if(exec) {
        gT3(module, user, exec);
    }
}
}

function gT3(module,user,exec) {

    var jsonTableData = $.ajax(
            {url:"include/execDetailList.php", 
data: "user=" +user + "&exec=" + exec,
datatype: "json", async: false
}).responseText;

var div_id = 'exec_detail_div';

// Create our datatable out of Json Data loaded from php call.
var TableData = new google.visualization.DataTable(jsonTableData);
var table = makeTable(TableData, div_id);

// Add our Actions handler.
google.visualization.events.addListener(table, 'select', selectHandler);

function selectHandler() {

    // grab a few details before redirecting
    var selection = table.getSelection();
    var row = selection[0].row;
    var col = selection[0].column;
    var uuid = TableData.getValue(row,0);

    // alert("gt3: UUID Selected >> " + uuid);
    if (uuid){
        gT4(uuid, user);
    }
}
}

function gT4(uuid,user) {

    var jsonTableData = $.ajax(
            {url:"include/runDetail.php", 
data: "uuid=" + uuid + "&user=" + user,
datatype: "json", async: false
}).responseText;

var div_id = 'run_detail_div';

// Create our datatable out of Json Data loaded from php call.
var TableData = new google.visualization.DataTable(jsonTableData);
var table = makeTable(TableData, div_id);

// Add our Actions handler.
google.visualization.events.addListener(table, 'select', selectHandler);

function selectHandler() {

    // grab a few details before redirecting
    var selection = table.getSelection();
    var row = selection[0].row;
    var col = selection[0].column;
    var runid = TableData.getValue(row,0);

}
}
function makeTable(TableData, div_id) {

    var tab_options = {title: 'Table View',
        showRowNumber: true,
        height: 200,
        width: '100%',
        allowHtml: true,
        alternatingRowStyle: true,
        height: 200}

    // Instantiate and Draw our Table
    var table = new google.visualization.Table(document.getElementById(div_id));

    table.draw(TableData, tab_options);

    return (TableData,table);
}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData);
    return (o.rows.length);
}

function hideAllDivs (idsToHide) {

    var attrToHide = document.querySelectorAll("*[style]");

    for(var i=0; i< attrToHide.length; i++) {
        if ($.inArray(attrToHide[i].id, idsToHide) != -1){     // if ID is present in the list Hide it
            attrToHide[i].style.visibility = "hidden";
        }
    }
}

