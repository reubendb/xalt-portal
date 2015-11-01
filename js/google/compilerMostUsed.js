/*!ZZ
 * Column chart for Most Used Compiler.
 * History
 * 2015-Aug-10
 * List of Parameters passed to generateTable function
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function compilerMostUsed(sysHost, startDate, endDate) {

    var jsonChartData = $.ajax
        ({url: "include/compilerMostUsed.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate, 
         dataType:"json", async: false
         }).responseText;


    var count = checkJsonData(jsonChartData);             /* if no data is returned do Nothing!! */
    if (count != 0) {


        // Create our data table out of JSON data loaded from server.
        var ChartData = new google.visualization.DataTable(jsonChartData);

        // Define Chart Options .
        var options = 
        {title: 'Most Used Compiler',chartArea: 
            {width: '80%'},hAxis:
            {title: 'Compilers'},vAxis: 
            {title: 'Count', format: 'short', minValue: 0}
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.ColumnChart(
                document.getElementById('comp1_div'));

        chart.draw(ChartData, options);

        // Create our datatable out of Json Data loaded from php call.
        var div_id = 'comp2_div';
        var table = makeTable(ChartData, div_id);
        document.getElementById("lblComp").style.visibility = 'visible'; 

        // Add our selection handler.
        google.visualization.events.addListener(chart, 'select', selectHandler);
        google.visualization.events.addListener(table, 'select', selectTable);

        // List ids to hide
        var idsToHide = ['lblCompUser0', 'comp3_div', 'lblCompUser1', 'lblCompExec0', 'lblCompExec1', 
           'comp4_div', 'lblCompExecRow', 'lblCompExecDetail0','comp5_div','lblCompRun0','comp6_div']; 
        hideAllDivs(idsToHide);

        function selectHandler() {
            // grab a few details before redirecting
            var selection = chart.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var linkProgram = [ChartData.getValue(row,0)];

            gTc1(sysHost, startDate, endDate, linkProgram); 
        }

        function selectTable() {
            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var linkProgram = [ChartData.getValue(row,0)];

            gTc1(sysHost, startDate, endDate, linkProgram); 
        }
    }
}

function gTc1(sysHost, startDate, endDate, linkProgram) {         /* Get user list  */

    console.log("linkProgram= " + linkProgram);

    if (linkProgram == 'g++'){                                    /* special case for g++ as ajax call consider + as special character */
        linkProgram = 'gpp';
    }
    var jsonTableData = $.ajax
        ({url:"include/compUserList.php",
         data: "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&linkProgram=" + linkProgram,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'comp3_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblCompUser0").style.visibility = 'visible';
        document.getElementById("comp3_div").style.visibility = 'visible';
        document.getElementById("lblCompUser1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        // List ids to hide
        var idsToHide = ['lblCompExec0', 'comp4_div', 'lblCompExec1', 
            'lblCompExecRow', 'lblCompExecDetail0','comp5_div','lblCompRun0','comp6_div']; 
        hideAllDivs(idsToHide);

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var user = TableData.getValue(row,0);

            gTc2(sysHost, startDate, endDate, linkProgram, user);
        }
    }
}   

function gTc2(sysHost, startDate, endDate, linkProgram,user) {

    console.log("User= " + user);

    var jsonTableData = $.ajax
        ({url:"include/compExecList.php", 
         data:  "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&user=" +user + "&linkProgram=" + linkProgram,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'comp4_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblCompExec0").style.visibility = 'visible';
        document.getElementById("comp4_div").style.visibility = 'visible';
        document.getElementById("lblCompExec1").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        // List ids to hide
        var idsToHide = ['lblCompExecRow', 'lblCompExecDetail0','comp5_div','lblCompRun0','comp6_div']; 
        hideAllDivs(idsToHide);

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var exec = TableData.getValue(row,0);

            gTc3(sysHost ,startDate , endDate, linkProgram, user, exec);
        }
    }
}

function gTc3(sysHost, startDate, endDate, linkProgram, user, exec) {

    console.log("Exec= " + exec);

    var jsonTableData = $.ajax
        ({url:"include/compExecDetailList.php", 
         data:  "sysHost=" + sysHost + "&startDate=" + startDate + "&endDate=" + endDate + "&linkProgram=" + linkProgram + "&user=" +user + "&exec=" + exec,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'comp5_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblCompExecRow").style.visibility = 'visible';
        document.getElementById("lblCompExecDetail0").style.visibility = 'visible';
        document.getElementById("comp5_div").style.visibility = 'visible';

        // Create our datatable out of Json Data loaded from php call.
        var TableData = new google.visualization.DataTable(jsonTableData);
        var table = makeTable(TableData, div_id);

        // Add our Actions handler.
        google.visualization.events.addListener(table, 'select', selectHandler);

        // List ids to hide
        var idsToHide = ['lblCompRun0','comp6_div']; 
        hideAllDivs(idsToHide);

        function selectHandler() {

            // grab a few details before redirecting
            var selection = table.getSelection();
            var row = selection[0].row;
            var col = selection[0].column;
            var uuid = TableData.getValue(row,6);

            // Not passing start/end date as user might run even after the date range.
            gTc4(sysHost, uuid, user);
        }
    }
}

function gTc4(sysHost, uuid,user) {

    console.log("UUId= " + uuid);

    var jsonTableData = $.ajax
        ({url:"include/runDetail.php", 
         data:  "sysHost=" + sysHost + "&uuid=" + uuid + "&user=" + user,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'comp6_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblCompRun0").style.visibility = 'visible';
        document.getElementById("comp6_div").style.visibility = 'visible';

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

            alert("gt4: RUNID Selected >> " + runid);
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

