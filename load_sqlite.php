<?php
(@include_once("./config.php")) OR die("Cannot read config.php file<BR>");
(@include_once("./create_sqlite_tables.php")) OR die("Cannot read create_sqlite_tables.php file<BR>");
(@include_once("./database_functions.php")) OR die("Cannot read database_functions.php file<BR>");

function getDateStrFromCell($worksheet,$row,$col,$date_format = 'd-m-Y'){
    $cellDataType = $worksheet->getCell([$col, $row])->getDataType();
    if ($cellDataType == 'n') {
        // Date format
        $mydate = $worksheet->getCell([$col, $row])->getValue();
        $mydate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mydate);
        $mydate_str = $mydate->format($date_format); // 02-09-1963
        //$intro_week_s2 = $intro_week_sd->format('j/M/Y'); // 2/sep/163
    } elseif ($cellDataType == 'f'){
        // Formula (hopefully a date)
        $mydate = $worksheet->getCell([$col, $row])->getOldCalculatedValue();
        $mydate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($mydate);
        $mydate_str = $mydate->format($date_format); // 02-09-1963
    }else{
        // let just take the value
        $mydate_str = $worksheet->getCell([$col, $row])->getValue();
    }
    return $mydate_str;
}

$query = "SELECT * FROM w_index WHERE 1";
$results = $db->query($query);

while ($round = $results->fetchArray()) {
    $round_id = $round['id'];
    $round_name = "Round " . $round['round_num'];

    // Get the par
    $par = $round['par'];

    // Get the date of the first wordle
    $start_date = $round['wordle_start_date'];
    $start_date2 = $start_date;

    // Get the wordle number of the first one
    $start_wordle = $round['wordle_start_num'];

    $results = array();

    $query = "SELECT * FROM w_people WHERE 1";
    $people_results = $db->query($query);


    while ($people_rec = $people_results->fetchArray()) {
        $people_id = $people_rec['id'];


        $query = "SELECT COUNT(*) as count FROM w_results WHERE round_id=$round_id AND people_id=$people_id";
        $round_results = $db->query($query);
        $round_recs = $round_results->fetchArray();
        $numScores = $round_recs['count'];

        if($numScores > 0) {
            $query = "SELECT * FROM w_results WHERE round_id=$round_id AND people_id=$people_id ORDER BY hole_num ASC";
            $round_results = $db->query($query);
            while ($round_recs = $round_results->fetchArray()) {
                $first_name = $people_rec['first_name'];
                $family_name = $people_rec['family_name'];

                $scores = array();
                for ($i = 1; $i <= $numScores; $i++) {
                    $myString = $worksheet->getCell([$col, $row])->getValue();
                    if ($myString == "") {
                        $scores[] = null;
                    } else {
                        $scores[] = floatval($myString);
                    }
                }


                $results[] = array(
                    'first_name' => $first_name,
                    'family_name' => $family_name,
                    'scores' => $scores
                );
            }
        }
    }

    $round_data[] = array(
        'start_date'=>$start_date,
        'start_date_d-m-Y'=>$start_date2,
        'start_wordle'=>$start_wordle,
        'par'=>$par,
        'results'=>$results,
        'name'=>$round_name
    );

    // only do the first worksheet
    //break;
}

push_all_round_data_database($db,$round_data);

$message = "Success";
$is_valid = 1;


$return_data = array(
    'round_data'=>$round_data,
    'message'=>$message,
    'file_info'=>$file_info,
    'is_valid'=>$is_valid
);

echo json_encode($return_data);
exit;