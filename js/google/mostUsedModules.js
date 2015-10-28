/*!ZZ
 * Column chart for Most Used Modules.
 * History
 * 2015-Aug-10
 * List of Parameters passed to generateTable function
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function mostUsedModules(sysHost, startDate, endDate) {

    console.log('query = ' + query);

    if (query == 0) {                 /* Call from xalt_usp.html page */
        var jsonChartData = $.ajax
            ({url: "include/mostUsedModules.php",
             data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&query=" + query + "&userId=" + userId ,
             dataType:"json", async: false
             }).responseText;
    }
    if (query == 1) {                 /* Call from xalt_usage.html page */
        var jsonChartData = $.ajax
            ({url: "include/mostUsedModules.php",
             data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&query=" + query,
             dataType:"json", async: false
             }).responseText;
    }

    var count = checkJsonData(jsonChartData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        // Create our data table out of JSON data loaded from server.
        var ChartData = new google.visualization.DataTable(jsonChartData);

        // Define Chart Options .
        var options =
        {title: 'Modules Usage',chartArea:
            {width: '80%'},hAxis:
            {title: 'Modules'},vAxis:
            {title: 'Count', format: 'short', minValue: 0}
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.ColumnChart(document.getElementById('mod1_div'));
        chart.draw(ChartData, options);
/*
        // Define Chart Options .
        var options = {title: 'Most Used Modules',
            pieHole: 0.4,
            sliceVisibilityThreshold: 0.01,
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.PieChart(document.getElementById('mod1_div'));

        chart.draw(ChartData, options);
*/
        // Create our datatable out of Json Data loaded from php call.
        var div_id = 'mod2_div';
        var table = makeTable(ChartData, div_id);
        document.getElementById("lblMod").style.visibility = 'visible';

        // Add listener (Get Version Details).
        google.visualization.events.addListener(chart, 'select', selectChart);
        google.visualization.events.addListener(table, 'select', selectTable);

        function selectChart() {
            // grab a few details before redirecting
            var selection = chart.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var module = [ChartData.getValue(row,0)];

            if (query == 0) { query = 3 } else if (query == 1) { query = 2 }
            console.log('query = ' + query);
            gT0(sysHost, startDate, endDate, module);

        }

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var module = [ChartData.getValue(row,0)];

            if (query == 0) { query = 3 } else if (query == 1) { query = 2 }
            console.log('query = ' + query);
            gT0(sysHost, startDate, endDate, module);
        }
    }
}                                                           /* mostUsedModules ends */

function gT0(syshost, startDate, endDate, module) {         /* List version of given modules   */

    console.log('query = ' + query + "module= " + module);

    if (query == 2) {         /* Call from xalt_usage.html page */
        var jsonTableData = $.ajax
            ({url: "include/mostUsedModules.php",
             data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&query=" + query + "&module=" + module,
             dataType:"json", async: false
             }).responseText;

    } else if (query == 3) {       /* Call from xalt_usp.html page */

        console.log("userid = " + userId);
        var jsonTableData = $.ajax
            ({url: "include/mostUsedModules.php",
             data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&query=" + query + "&module=" + module + "&userId=" + userId,
             dataType:"json", async: false
             }).responseText;

    }
    var div_id = 'mod3_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblModVer0").style.visibility = 'visible';
        document.getElementById("lblModVer1").style.visibility = 'visible';
        document.getElementById("mod3_div").style.visibility = 'visible';

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
            var module = TableData.getValue(row,0);
            var version = TableData.getValue(row,1);

            if (query == 3){
                gT2(sysHost, startDate, endDate, module, version, userId);
            }
            else {
                gT1(sysHost, startDate, endDate, module, version);
            }
        }
    }
}   

function gT1(sysHost, startDate, endDate, module, version) {      /* List of Users  */

    console.log("&module=" + module + "&version=" + version);

    var jsonTableData = $.ajax
        ({url:"include/userList.php", 
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&module=" + module + "&version=" + version,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'mod4_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblUserList0").style.visibility = 'visible';
        document.getElementById("lblUserList1").style.visibility = 'visible';
        document.getElementById("mod4_div").style.visibility = 'visible';

        console.log("sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&module=" + module + "&version=" + version);
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

            gT2(sysHost, startDate, endDate, module, version, user);
        }
    }
}   

function gT2(sysHost, startDate, endDate, module, version, user) {       /* List of executables */

    console.log("&module=" + module + "&version=" + version + "&user= " + user);

    var jsonTableData = $.ajax
        ({url:"include/execList.php", 
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&module=" + module + "&version=" + version + "&user=" + user,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'exec_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblExecList0").style.visibility = 'visible';
        document.getElementById("lblExecList1").style.visibility = 'visible';
        document.getElementById("exec_div").style.visibility = 'visible';

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

            gT3(sysHost, startDate, endDate, module, version, user, exec);
        }
    }
}

function gT3(sysHost, startDate, endDate, module, version, user, exec) { /* Executable detail */

    console.log("&user= " + user + "&exec=" + exec + query);

    var jsonTableData = $.ajax
        ({url:"include/execDetailList.php", 
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + 
         "&module=" + module + "&version=" + version + "&user=" + user + "&exec=" + exec,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'exec_detail_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblExecDetailList").style.visibility = 'visible';
        document.getElementById("exec_detail_div").style.visibility = 'visible';

        if (query == 3) {       /* Call from xalt_usp.html page */
            document.getElementById("lblExecDetailList1").style.visibility = 'visible'; 
        }
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

            gT4(uuid);
            if (query == 3) {         /* Call from xalt_usp.html page */
                gT5(uuid);            /* get objects at linktime */ 
            }
        }
    }
}

function gT4(uuid) {               /* get job run details */

    console.log("&uuid=" + uuid);

    if (query == 3) {             /* Call from xalt_usp.html page */
        var jsonTableData = $.ajax
            ({url:"include/uspRunDetail.php", 
             data: "uuid=" + uuid,
             datatype: "json", async: false
             }).responseText;

        document.getElementById("lblRunDetail1").style.visibility = 'visible';
    } else {
        var jsonTableData = $.ajax
            ({url:"include/runDetail.php",
             data: "uuid=" + uuid,
             datatype: "json", async: false
             }).responseText;
    }

    var div_id = 'run_detail_div';
    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblRunDetail").style.visibility = 'visible';
        document.getElementById("run_detail_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        if (query == 3) {           /* Call from xalt_usp.html page */
            // Add our Actions handler.
            google.visualization.events.addListener(table, 'select', selectHandler);

            function selectHandler() {

                // grab a few details before redirecting
                var selection = table.getSelection();
                var row = selection[0].row;
                var col = selection[0].column;
                var runId = TableData.getValue(row,0);

                console.log(runId); 
                 gT6(runId);            /* get runtime env info */ 
            }
        }
    }
}


function gT5(uuid) {               /* get object information*/

    console.log("&uuid=" + uuid);

    var jsonTableData = $.ajax
        ({url:"include/uspObjDetail.php", 
         data: "uuid=" + uuid,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'obj_div';
    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblObj").style.visibility = 'visible';
        document.getElementById("obj_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);
    }
}

function gT6(runId) {               /* get runtime env information*/

    console.log("&runId=" + runId);

    var jsonTableData = $.ajax
        ({url:"include/uspRunEnv.php", 
         data: "runId=" + runId,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'run_env_div';
    var count = checkJsonData(jsonTableData);         /* if no data is returned do Nothing!! */
    if (count != 0) {
        document.getElementById("lblRunEnv").style.visibility = 'visible';
        document.getElementById("run_env_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);
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

    visualization.setSelection([]);
    table.clearChart();

    table.draw(TableData, tab_options);

    return (TableData,table);
}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData);
    return (o.rows.length);

}

function hideAllDivs () {

    var divsToHide = document.getElementsByClassName("row-module");
    for(var i = 0; i < divsToHide.length; i++)
    {
        console.log(divsToHide[i]);
        divsToHide[i].style.visibility="hidden";
    }
}


//alert(ChartData.getValue(row,0) + ChartData.getValue(row,1));
//location.href = 'http://www.google.com?row=' + row + '&col=' + col;
