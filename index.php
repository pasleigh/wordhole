<?php
$x = 0;
if (array_key_exists('e', $_GET) == false) {
    $_GET['e'] = null;
}
$edit = $_GET['e'];

$show_edit_block = false;
if ($edit === "edit") {
    $show_edit_block = true;
}

$edit_block = "";
if ($show_edit_block) {
    $edit_block .= <<<HTML
    <div class='card mb-3'>
        <div class='card-header'>
            <h5>Enter wordle scores data</h5>
        </div>
        <div id='edit_body' class='card-body m-2'>
            This is the body of the edit card

            <div style="font-size: small">
            <div id='jspreadsheet_wordle_data'></div>
            </div>
            <div class="row">
                <div class="col-4">
                    <button type="button" class="btn btn-primary" id="submit_cwt_editor_data">Submit updates</button>
                </div>
            </div>
        </div>
    </div>
    HTML;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Wordhole Record</title>
    <link rel="icon" type="image/x-icon" href="./wordle_96.jpg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-iYQeCzEYFbKjA/T2uDLTpkwGzCiq6soy8tYaI1GyVh/UjpbCx/TYkiZhlZB6+fzT" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h2>Wordhole Record of Rounds</h2>
    <div class="row>">
        <div class="col-md-3">
            <div class="form-group">
                <label for="round_select">Select a round</label>
                <select id="round_select" class="form-select" aria-label="Select a round to show">
                    <option inactive>Select a round</option>
                </select>
            </div>
        </div>
        <div class="col-md-9"></div>
    </div>
    <div id="par_chart_container" style="height: 600px;"></div>

    <div id="edit_block"><?php echo($edit_block); ?></div>

    <div id="scores"></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-u1OknCvxWvY5kfmNBILK2hRnQC3Pr17a+RTT6rIHI7NnikvbZlHgTPOOmMi466C8"
        crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
<script src="./js/load_xlsx.js"></script>
<script src="./js/mycharts.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/series-label.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script src="./frameworks/jspreadsheets/jspreadsheet.js"></script>
<link rel="stylesheet" href="./frameworks/jspreadsheets/jspreadsheet.css" type="text/css"/>
<script src="./frameworks/jsuites/jsuites.js"></script>
<link rel="stylesheet" href="./frameworks/jsuites/jsuites.css" type="text/css"/>

<script>
    $(document).ready(function () {
        load_wordle_data('par_chart_container');
    });
    $('#round_select').on('change', function () {
        var i = $(this).find(":selected").val();
        var name = $(this).find(":selected").text();
        //alert("Changed " + i + " name");
        let selected_round_data = all_rounds_data[i];

        let chart_container_id = 'par_chart_container';
        draw_par_chart(selected_round_data, chart_container_id);
        write_winners_info(selected_round_data);
        updateTable(selected_round_data)
    });
</script>
</body>

</html>