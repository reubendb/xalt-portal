/*
 * Column charts to Identify Users 
 * History 
 * Kapil Agrawal May-2015 - Initial Changes
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function identifyUser(sysHost, startDate, endDate, objPath) {

    console.log(objPath);

    var jsonTableData = $.ajax
        ({url: "include/identifyUser.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&objPath=" + objPath, 
         dataType:"json",async: false
         }).responseText;

    var div_id = 'identify_user_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblIdentifyUser0', 'identify_user_div', 'lblIdentifyUser1', 
        'lblIdentifyExec0', 'identify_exec_div', 'lblIdentifyExec1', 
        'lblIdenExecDetail0', 'identify_exDeatil_div', 
        'lblIdenRun0', 'identify_run_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyUser0").style.visibility = 'visible';
        document.getElementById("identify_user_div").style.visibility = 'visible';
        document.getElementById("lblIdentifyUser1").style.visibility = 'visible';
        // Create our data table out of JSON data loaded from server.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        // Add event handler 
        google.visualization.events.addListener(table, 'select', selectHandler);

        function selectHandler() {

            // grab details 
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var user = TableData.getValue(row,0);

            gTi1(sysHost, startDate, endDate, objPath, user);
        }
    }
}

function gTi1(sysHost, startDate, endDate,objPath, user) {        /* Get Exec List */

    console.log(objPath + user);

    var jsonTableData = $.ajax
        ({url:"include/identifyExecList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&objPath=" + objPath + "&user=" + user,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_exec_div';

    // Hide all tables which are not required.
    var idsToHide = [ 'lblIdentifyExec0', 'identify_exec_div', 'lblIdentifyExec1',
        'lblIdenExecDetail0', 'identify_exDeatil_div', 
        'lblIdenRun0', 'identify_run_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyExec0").style.visibility = 'visible';
        document.getElementById("identify_exec_div").style.visibility = 'visible';
        document.getElementById("lblIdentifyExec1").style.visibility = 'visible';

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

            gTi2(sysHost, startDate, endDate, objPath, user, exec);
        }
    }
}

function gTi2(sysHost, startDate, endDate,objPath, user, exec) {        /* Get Exec Detail*/

    console.log(user + exec);
    var jsonTableData = $.ajax
        ({url:"include/identifyExecDetail.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&objPath=" + objPath + "&user=" + user + "&exec=" + exec,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_exDetail_div';

    // Hide all tables which are not required.
    var idsToHide = ['lblIdenExecDetail0', 'identify_exDetail_div', 
        'lblIdenRun0', 'identify_run_div']; 
    hideAllDivs(idsToHide);

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdenExecDetail0").style.visibility = 'visible';
        document.getElementById("identify_exDetail_div").style.visibility = 'visible';

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
            var uuid = TableData.getValue(row,6);

            gTi2(sysHost, startDate, endDate, objPath, user, exec);
        }
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

