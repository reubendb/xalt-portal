/*!ZZ
 * Column chart for Most Used Modules.
 * History
 * 2015-Aug-10
 * List of Parameters passed to generateTable function
 */

google.load('visualization', '1', {packages: ['corechart','table']});
google.setOnLoadCallback(mostUsedModules);

function mostUsedModules(param) {
    var syshost = param.value;

    var jsonChartData = $.ajax(
            {url: "include/mostUsedModules.php",
            data: "sys="+syshost,
            dataType:"json", async: false
            }).responseText;

    // Create our data table out of JSON data loaded from server.
    var ChartData = new google.visualization.DataTable(jsonChartData);

    // Define Chart Options .
    var options = {
                  title: 'Most Used Modules (Run Time)',
                  pieHole: 0.4,
                  sliceVisibilityThreshold: 0.02,
    };

    // Instantiate and draw chart.
    var chart = new google.visualization.PieChart(
            document.getElementById('module_div'));

    chart.draw(ChartData, options);
    
    // Create our datatable out of Json Data loaded from php call.
    var div_id = 'module_list_div';
    var table = makeTable(ChartData, div_id);


    // Add our selection handler.
    google.visualization.events.addListener(chart, 'select', selectHandler);

    function selectHandler() {
        // grab a few details before redirecting
        var selection = chart.getSelection();
        var row = selection[0].row;
        var col = selection[0].column;
        var module = [ChartData.getValue(row,0)];

        if (module) {
            gT1(module); 
        }
    }
}                                                           /* mostUsedModules ends */

function gT1(module) {                                      /* GenerateTable (gT)   */

    var jsonTableData = $.ajax(
            {
            url:"include/userList.php", 
            data: "module=" + module,
            datatype: "json", async: false
            }).responseText;

    var div_id = 'user_div';

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

        if(user) {
            gT2(module, user);
        }
    }
}   

function gT2(module,user) {

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
