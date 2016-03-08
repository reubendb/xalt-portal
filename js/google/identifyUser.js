/*
 * Column charts to Identify Users 
 * History 
 * Kapil Agrawal May-2015 - Initial Changes
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function identifyUser(sysHost, startDate, endDate, objPath, execName, query) {

    console.log(objPath + ":" + execName + ":" + query );

    var jsonTableData = $.ajax
        ({url: "include/identifyUser.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + 
         "&objPath=" + objPath + "&execName=" + execName + "&query=" + query, 
         dataType:"json",async: false
         }).responseText;

    var div_id = 'identify_user_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblIdentifyUser0', 'identify_user_div', 'lblIdentifyUser1', 
        'lblIdentifyExec0', 'identify_exec_div', 'lblIdentifyExec1', 
        'lblIdenExecDetail0','identify_exDetail_div', 'lblIdenRun0', 
        'identify_run_div','lblObj', 'obj_div', 'lblRunObj', 'runObj_div', 
        'lblRunEnv', 'run_env_div','lblFunc', 'func_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyUser0").style.visibility = 'visible';
        document.getElementById("identify_user_div").style.visibility = 'visible';
        document.getElementById("lblIdentifyUser1").style.visibility = 'visible';
        if (query == 1) { 
            document.getElementById('lblIdentifyUser1').innerHTML = 
                '<small> [Count = Number of times User linked to given Object Path] </small>';     
        } else if (query == 2) {
            document.getElementById('lblIdentifyUser1').innerHTML = 
                '<small> [Count = Number of times User for given Executable Name] </small>';     
        }
        // Create our data table out of JSON data loaded from server.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id, count);

        // Add event handler 
        google.visualization.events.addListener(table, 'select', selectHandler);

        function selectHandler() {

            // grab details 
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var user = TableData.getValue(row,0);

            gTi1(sysHost, startDate, endDate, objPath, execName, user, query);    /* Get Exec List */
        }
    }
}

function gTi1(sysHost, startDate, endDate,objPath, execName, user, query) {        /* Get Exec List */

    console.log(objPath + user);

    var jsonTableData = $.ajax
        ({url:"include/identifyExecList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + 
         "&objPath=" + objPath + "&execName=" + execName + "&user=" + user + "&query=" + query,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_exec_div';

    // Hide all tables which are not required.
    var idsToHide = [ 'lblIdentifyExec0', 'identify_exec_div', 'lblIdentifyExec1',
        'lblIdenExecDetail0', 'identify_exDetail_div', 'lblIdenRun0', 'identify_run_div',
        'lblObj', 'obj_div', 'lblRunObj', 'runObj_div', 'lblRunEnv', 'run_env_div',
        'lblFunc', 'func_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyExec0").style.visibility = 'visible';
        document.getElementById("identify_exec_div").style.visibility = 'visible';
        document.getElementById("lblIdentifyExec1").style.visibility = 'visible';

        if (query == 1) {
            document.getElementById('lblIdentifyExec0').innerHTML = 
                'List of Executable(s) <small> (for given user-objectPath)</small>';     
            document.getElementById('lblIdentifyExec1').innerHTML = 
                '<small>[Count = Number of Executable linked to given user-objPath]</small>';
        }else if (query == 2){
            document.getElementById('lblIdentifyExec0').innerHTML = 
                'List of Executable(s) <small> (for given user-execName)</small>';     
            document.getElementById('lblIdentifyExec1').innerHTML = 
                '<small>[Count = Number of Executable linked to given user-execName]</small>';
        }

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
            var exec = TableData.getValue(row,0);
            var page = 0;                         /* pagination changes */

            gTi2(sysHost, startDate, endDate, objPath, user, exec, query, page); /* Get Exec Detail*/
        }
    }
}

function gTi2(sysHost, startDate, endDate,objPath, user, exec, query, page) {   /* Get Exec Detail*/

    console.log(user + exec + page);

    var jsonTableData = $.ajax
        ({url:"include/identifyExecDetail.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + 
         "&objPath=" + objPath + "&user=" + user + "&exec=" + exec + "&query=" + query + 
         "&page=" + page,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_exDetail_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblIdenExecDetail0', 'identify_exDetail_div', 
        'lblIdenRun0', 'identify_run_div','lblObj', 'obj_div', 'lblRunObj', 
        'runObj_div', 'lblRunEnv', 'run_env_div', 'lblFunc', 'func_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdenExecDetail0").style.visibility = 'visible';
        document.getElementById("identify_exDetail_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeExecDetail(TableData, div_id, page);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        // google.visualization.table exposes a 'page' event.
        google.visualization.events.addListener(table, 'page', myPageEventHandler);

        function myPageEventHandler(e) {
            page = e['page'];
            /* get executable details */
            gTi2(sysHost, startDate, endDate, objPath, user, exec, query, page);
        }

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);

            // get run details irrespective of who built the code
            gTi3(uuid);       /* get run details */
            gTi4(uuid);       /* get object information */
            gTi7(uuid);       /* get functions called */
        }
    }
}

function gTi3(uuid) {         /* get run details */

    console.log("UUId= " + uuid);

    var jsonTableData = $.ajax
        ({url:"include/runDetail.php",
         data:  "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_run_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblIdenRun0', 'identify_run_div','lblRunObj', 'runObj_div', 
        'lblRunEnv', 'run_env_div'];
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);     /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdenRun0").style.visibility = 'visible';
        document.getElementById("identify_run_div").style.visibility = 'visible';

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
            gTi5(runId);          /* get run env detail */
            gTi6(runId);          /* get object at run time */
        }
    }
}

function gTi4(uuid) {               /* get object information */

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecObj.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'obj_div';

    // List ids to hide
    var idsToHide = ['lblObj', 'obj_div'];
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

function gTi5(runId) {               /* get runtime env information*/

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

function gTi6(runId) {               /* get object at runtime */

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

function gTi7(uuid) {               /* get functions called */

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/getExecFunc.php",
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'func_div';

    // List ids to hide
    var idsToHide = ['lblFunc', 'func_div'];
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

function makeExecDetail(TableData, div_id, page) {

    var tab_options = {showRowNumber: true,
        height: '100%', width: '100%',
        page: 'enable', pageSize: '10', startPage: parseInt(page),
        pagingSymbols: {prev: ['< prev'],next: ['next >']},
        allowHtml: true, alternatingRowStyle: true
    }
    // Instantiate and Draw our Table
    var table = new google.visualization.Table(document.getElementById(div_id));

    table.clearChart();
    table.draw(TableData, tab_options);
    return (table);
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

    table.clearChart();
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

