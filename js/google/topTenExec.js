/*!
 * Top Ten Executables 
 * History
 * 2015-Aug-10
 */

google.load('visualization', '1', {packages: ['corechart', 'bar']});

function topTenExec(sysHost, startDate, endDate) {

    console.log("TopTenExec: " + sysHost + startDate + endDate);
    var jsonChartData = $.ajax
        ({url: "include/topTenExec.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" +endDate,
         dataType:"json", async: false
         }).responseText;

    var count = checkJsonData(jsonChartData);
    if (count != 0) {

        document.getElementById("ten_exec_div").style.visibility = 'visible';

        // Create our data table out of JSON data loaded from server.
        var chartData = new google.visualization.DataTable(jsonChartData);

        // Define Chart Options .
        var options = {title: 'Top Ten Executables',
            height: '50%',
            width: '60%',
            hAxis: {title: 'Number of Jobs (log)',logScale: 'True', maxValue: '1000000'},
            vAxis: {title: 'Core Hours (log)', logScale: 'True', maxValue: '10000000'},
            bubble: {textstyle: {fontSize: 5}}
        };

        // Instantiate and draw chart.
        var chart = new google.visualization.BubbleChart(document.getElementById('ten_exec_div'));
        chart.draw(chartData, options);
    }
    if (count == 0){
        document.getElementById("ten_exec_div").style.visibility = 'hidden';
    }

}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData); 
    return (o.rows.length);
}  
