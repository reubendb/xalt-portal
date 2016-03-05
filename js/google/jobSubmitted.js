/*!
 * Donut Chart for Job Submitted on Darter
 * History
 * 2015-Aug-10
 */

google.load("visualization", "1", {packages:["corechart"]});
function jobSubmitted(sysHost, startDate, endDate) {

    console.log("JobSubmitted: " + sysHost + startDate + endDate);
    var jsonChartData = $.ajax
        ({url: "include/jobSubmitted.php",
         data: "sysHost="+sysHost + "&startDate=" + startDate + "&endDate=" + endDate, 
         datatype: "json", async: false
         }).responseText;

    var count = checkJsonData(jsonChartData);
    if (count != 0) {

        document.getElementById("jobs_sub_div").style.visibility = 'visible';

        // Create data table from json
        var chartData = new google.visualization.DataTable(
                jsonChartData);

        //Define Chart Options
        var options = {title: 'Jobs Submitted',
            pieHole: 0.4
        };

        // Instantiate and draw chart
        var chart = new google.visualization.PieChart(document.getElementById('jobs_sub_div'));
        chart.draw(chartData, options);

    }
    if (count == 0){
        document.getElementById("jobs_sub_div").style.visibility = 'hidden';
    }
}

function checkJsonData (jsonTableData) {
    var o = JSON.parse(jsonTableData); 
    return (o.rows.length);
}  
