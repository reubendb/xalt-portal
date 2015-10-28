/*!ZZ
 * Column chart for 
 * History
 * 2015-Aug-10
 * List of Parameters passed to generateTable function
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function uspUUidDetail(sysHost, startDate, endDate, userId) {

    var query = 1;              // top level query
    var jsonChartData = $.ajax(
            {url: "include/uspUUidDetail.php",
            data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId,
            dataType:"json", async: false
            }).responseText;

    // Create our data table out of JSON data loaded from server.
    var ChartData = new google.visualization.DataTable(jsonChartData);
    
    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp1_div';
    var table = makeTable(ChartData, div_id);


    // Add our selection handler.
    google.visualization.events.addListener(table, 'select', selectTable);

    function selectTable() {
        // grab a few details before redirecting
        var selection = table.getSelection();
        var row = selection[0].row;
        var col = selection[0].column;
        var uuid = [ChartData.getValue(row,0)];

        console.log(uuid);
        gTu0(sysHost, startDate, endDate, userId, uuid);
        gTu1(sysHost, uuid);
    }
}                                                           /* UUID Run Details  ends */

function gTu0(syshost, startDate, endDate, userId, uuid) {         /* GenerateTable LIST UUID Job Details  */

    var jsonTableData = $.ajax(
            {url: "include/uspJobDetail.php",
            data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId + "&uuid=" + uuid,
            dataType:"json", async: false
            }).responseText;

    var div_id = 'usp2_div';

    // Create our datatable out of Json Data loaded from php call.
    var TableData = new google.visualization.DataTable(jsonTableData);
    var table = makeTable(TableData, div_id);

    // Add our Actions handler.
    google.visualization.events.addListener(table, 'select', selectHandler);

    function selectHandler() {

        // grab a few details before redirecting
        var selection = table.getSelection()
        var row = selection[0].row;
        var col = selection[0].column;
        var jobId = TableData.getValue(row,0);
        
        console.log(jobId);
       // gT1(sysHost, startDate, endDate, module, version);
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


//alert(ChartData.getValue(row,0) + ChartData.getValue(row,1));
//location.href = 'http://www.google.com?row=' + row + '&col=' + col;
