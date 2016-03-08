/*!ZZ
 * User Software Provenance JS 
 * History
 * 2015-Oct
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function usp(sysHost, startDate, endDate, userId) {        /* get exec list */

    var jsonTableData = $.ajax
        ({url: "include/uspExecList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&userId=" + userId,
         dataType:"json", async: false
         }).responseText;


    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'usp_exec_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblExec0', 'usp_exec_div', 'lblExec1', 'lblExecDetail0', 
        'usp_exDetail_div', 'lblObj', 'obj_div', 'lblUspRun0', 'usp_run_div', 
        'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div', 'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count!=0) {

        document.getElementById("lblExec0").style.visibility = 'visible';
        document.getElementById("usp_exec_div").style.visibility = 'visible';
        document.getElementById("lblExec1").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        // Add our selection handler.
        google.visualization.events.addListener(table, 'select', selectTable);

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var exec = [TableData.getValue(row,0)];

            gTu0(sysHost, startDate, endDate, userId, exec);      /* get exec detail list  */
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
    var idsToHide = ['lblExecDetail0', 'usp_exDetail_div', 'lblObj', 'obj_div',
        'lblUspRun0', 'usp_run_div', 'lblRunObj', 'runObj_div', 'lblRunEnv', 
        'run_env_div','lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count!=0) {

        document.getElementById("lblExecDetail0").style.visibility = 'visible';
        document.getElementById("usp_exDetail_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);

            gTu1(uuid);       /* get run details */      
            gTu2(uuid);       /* get object information */
            gTu5(uuid);       /* get functions called  */
        }
    }
}   

/* gTu1:
 * userId is not needed as User A might be using exec compiled by user B   
 * Date Range is also not required as we don't care if it was comipled by User xyz at xyz time
 */

function gTu1(uuid) {         /* get run details */

    console.log("UUId= " + uuid);

    var jsonTableData = $.ajax
        ({url:"include/runDetail.php",
         data:  "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'usp_run_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblUspRun0', 'usp_run_div', 'lblObj', 'obj_div','lblRunObj', 
        'runObj_div', 'lblRunEnv', 'run_env_div', 'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblUspRun0").style.visibility = 'visible';
        document.getElementById("usp_run_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        function selectHandler() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var runId = TableData.getValue(row,0);

            // get run details irrespective of who built the code
            gTu3(runId);            /* get runtime env detail */
            gTu4(runId);            /* get objects at runtime */
        }
    }
}

function gTu2(uuid) {               /* get object information */

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecObj.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'obj_div';

    // List ids to hide
    var idsToHide = [ 'lblObj', 'obj_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblObj").style.visibility = 'visible';
        document.getElementById("obj_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}

function gTu3(runId) {               /* get runtime env information */

    console.log("&runId=" + runId);
    var jsonTableData = $.ajax
        ({url:"include/getRunEnv.php",
         data: "runId=" + runId,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'run_env_div';

    // List ids to hide
    var idsToHide = ['lblRunEnv', 'run_env_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblRunEnv").style.visibility = 'visible';
        document.getElementById("run_env_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}

function gTu4(runId) {               /* get objects at runtime */

    console.log("&runId=" + runId);
    var jsonTableData = $.ajax
        ({url:"include/getRunObj.php",
         data: "runId=" + runId,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'runObj_div';

    // List ids to hide
    var idsToHide = ['lblRunObj', 'runObj_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblRunObj").style.visibility = 'visible';
        document.getElementById("runObj_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}

function gTu5(uuid) {               /* get functions called  */

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecFunc.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'func_div';

    // List ids to hide
    var idsToHide = [ 'lblFunc', 'func_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblFunc").style.visibility = 'visible';
        document.getElementById("func_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);
    }
}

function makeTable(TableData, div_id, count) {

    var tab_options;
    if (count > 10){
        tab_options = {title: 'Table View',
            showRowNumber: true,
            height: 260,
            width: '100%',
            allowHtml: true,
            alternatingRowStyle: true
        }
    } else {
        tab_options = {title: 'Table View',
            showRowNumber: true,
            height: '100%',
            width: '100%',
            allowHtml: true,
            alternatingRowStyle: true,
            page: 'enable', pageSize: '10'
        }
    }

    // Instantiate and Draw our Table
    var table = new google.visualization.Table(document.getElementById(div_id));

    table.draw(TableData, tab_options);
    return (table);
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

