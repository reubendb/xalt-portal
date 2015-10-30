/*
 * Column charts to Identify Users 
 * History 
 * Kapil Agrawal May-2015 - Initial Changes
 */

google.load('visualization', '1', {packages: ['corechart','table']});

function identifyUser(objPath) {

    console.log(objPath);

    var jsonTableData = $.ajax
        ({url: "include/identifyUser.php",
         data: "objPath="+objPath, 
         dataType:"json",async: false
         }).responseText;

    var div_id = 'identify_user_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyUser0").style.visibility = 'visible';
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

            gTi1(objPath, user);


        }
    }
}

function gTi1(objPath, user) {        /* Get Exec List */

    console.log(objPath + user);

    var jsonTableData = $.ajax
        ({url:"include/identifyExecList.php",
         data: "user=" + user + "&objPath=" + objPath,
         datatype: "json", async: false
         }).responseText;

    var div_id = 'identify_exec_div';

    var count = checkJsonData(jsonTableData);             /* if no data is returned do Nothing!! */
    if (count != 0) {

        document.getElementById("lblIdentifyExec0").style.visibility = 'visible';
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
