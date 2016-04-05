/*!
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart', 'bar']});

function lookUp(query) {

    console.log("LookUp: " + query);
    var jsonChartData = $.ajax
        ({url: "include/lookUp.php",
         data: "query=" + query,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonChartData);

    if (count != 0) {

        document.getElementById("lookup_div").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var TableData = new google.visualization.DataTable(jsonChartData);
        var table = drawTable(TableData, "lookup_div", count);

    }
    if (count == 0){
        document.getElementById("lookup_div").style.visibility = 'hidden';
    }
}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData); 
    return (o.rows.length);
}  

function drawTable(TableData, div_id, count) {

    var tab_options;
    if (count > 10){
        tab_options = {title: 'Table View',
            height: 260,
            width: '100%',
            allowHtml: true,
            alternatingRowStyle: true
        }
    } else {
        tab_options = {title: 'Table View',
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
    return(table);
}


