<?php
(@include_once("./config.php")) OR die("Cannot read config.php file<BR>");
(@include_once("./create_sqlite_tables.php")) OR die("Cannot read create_sqlite_tables.php file<BR>");

/*
  $round_data[] = array(
        'start_date'=>$start_date,
        'start_date_d-m-Y'=>$start_date2,
        'start_wordle'=>$start_wordle,
        'par'=>$par,
        'results'=>$results,
        'name'=>$worksheet_name
    );
$results[] = array(
    'first_name'=>$first_name,
    'family_name'=>$family_name,
    'scores'=>$scores
);
*/
function push_all_round_data_database($db,$round_data){
    $ret_value = true;
    for($i = 0; $i < count($round_data); $i++){
        $this_round = $round_data[$i];
        $sheet_name = $this_round['name'];
        $tmp = explode(" ",$sheet_name);
        if($tmp[0] == 'Round') {
            $round_num = intval($tmp[1]);
            $wordle_start_date = $this_round['start_date'];
            $wordle_start_date_d_m_Y = $this_round['start_date_d-m-Y'];
            $wordle_start_num = $this_round['start_wordle'];
            $par = $this_round['par'];

            $round_id = GetRoundID($db,$round_num,$wordle_start_num, $wordle_start_date, $par);

            $results = $this_round['results'];

            //echo('Results<BR>');
            //var_dump($results);
            for ($j = 0; $j < count($results); $j++) {
                $result = $results[$j];
                $first_name = $result['first_name'];
                $family_name = $result['family_name'];
                $person_id = GetPersonID($db, $first_name, $family_name);
                $scores = $result['scores'];
                $total = 0;
                for ($k = 0; $k < count($scores); $k++) {
                    $score = $scores[$k];
                    if ($score == null) {
                        break;
                    }
                    $total += $score - $par;
                }
                // Do something with the total
            }
        }
    }

    return $ret_value;
}

function GetPersonID($db,$first_name,$family_name){
    $query = "SELECT * FROM w_people WHERE first_name='$first_name' AND family_name='$family_name'";
    $results = $db->query($query);

    $person = $results->fetchArray();

    //echo("Person: <BR>");
    //var_dump($person);
    if($person){
        // return the id
        $id = $person['id'];
    }else{
        $query = "INSERT INTO w_people (first_name, family_name) VALUES ('$first_name', '$family_name')";
        $db->query($query);
        $id = $db->lastInsertRowId();
    }

    return $id;
}

function GetRoundID($db,$round_num,$wordle_start_num, $wordle_start_date, $par){
    $query = "SELECT * FROM w_index WHERE round_num='$round_num' AND wordle_start_num='$wordle_start_num' AND wordle_start_date='$wordle_start_date'";
    $results = $db->query($query);

    $round = $results->fetchArray();

    //echo("Person: <BR>");
    //var_dump($person);
    if($round){
        // return the id
        $id = $round['id'];
    }else{
        $query = "INSERT INTO w_index (round_num, wordle_start_num, wordle_start_date, par) VALUES ('$round_num', '$wordle_start_num', '$wordle_start_date', '$par')";
        $db->query($query);
        $id = $db->lastInsertRowId();
    }

    return $id;
}
