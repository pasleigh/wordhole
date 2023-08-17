var all_rounds_data;
var the_chart;

function load_wordle_data(chart_container_id){
    $.ajax({
        type: 'post',
        //url: 'test_pwd.php',
        //url: 'externaldev?task=ajax',
        //url: 'index.php?option=com_dotcontent&view=external?task=ajax',
        url: './load_xlsx.php',
        contentType: false,
        processData: false,
        dataType: "json",
        success: function (mydata) {
            //
            //console.log(JSON.stringify(mydata));
            if(mydata.is_valid == 1){
                let html = ""
                for(let i = 0; i < mydata.round_data.length; i++){
                    html += "<option value='" + i + "'>" + mydata.round_data[i].name + "</option>";
                }
                $('#round_select').html(html);

                all_rounds_data = mydata.round_data;

                //alert(mydata.message + "\n" + mydata.file_info);
                let selected_round_data= all_rounds_data[0];
                let my_chart_container_id = "par_chart_container";
                draw_par_chart(selected_round_data,my_chart_container_id,0);
                write_winners_info(selected_round_data);
            }
        },
        error: function (jqXHR, textStatus, errorThrown) {
            alert('An error occurred... Look at the console (F12 or Ctrl+Shift+I, Console tab) for more information!');
            console.log('jqXHR.responseText');
            console.log(jqXHR.responseText);
            console.log('jqXHR:');
            console.log(jqXHR);
            console.log('textStatus:');
            console.log(textStatus);
            console.log('errorThrown:');
            console.log(errorThrown);
        }
    });

}

function draw_par_chart(score_data,container_id){
    let myChart = myline_chart;

    let par = parseInt(score_data.par);
    let start_wordle_num = parseInt(score_data.start_wordle);

    myChart.title = "Worldhole : " + score_data.name + ". First hole (" + score_data.start_wordle + ") " + score_data.start_date;
    myChart.subtitle.text = myChart.title;

    myChart.series = [];

    for(let i = 0 ; i < score_data.results.length; i++) {
        let name = score_data.results[i].first_name + " " + score_data.results[i].family_name;
        let score_array = Array();
        let running_total = 0;
        for(let j = 0; j < score_data.results[i].scores.length; j++) {
            let x = start_wordle_num + j ;
            let y;
            let v;
            let comment = "";
            if(score_data.results[i].scores[j] ){
                v = Math.round(score_data.results[i].scores[j]);
                y = v-par;
                running_total += y;
                if(score_data.results[i].scores[j]>7){
                    comment = "<BR>Did not submit 🙁";// U+2641
                }
                if(v == 7 && score_data.results[i].scores[j]<7){
                    // A real 7 score
                    comment = "😵";//U+1F974️";
                }
                if(v < 3){
                    comment = "🤩";//U+1F929"; // Big smile
                }
            }else{
                y = null;
                running_total = null;
                v = null;
            }
            score_array.push({x:x,y:running_total,v:v, h:j+1, c:comment});
        }
        myChart.series.push(
            {
                name: name,
                data: score_array
            }
        );

    }

    /*
    if(the_chart) {
        while( the_chart.series.length > 0 ) {
            for(let i = 0 ; i < the_chart.series.length; i++){
                the_chart.series[i].remove( false );
            }
        }
    }
    */
    $('#'+container_id).show();
    $('#'+container_id).highcharts(myChart);

}
function find_winners(score_data){
    // find the lowest score for each hole
    // and record the people wi that score
    let min_scorers = Array();

    let num_holes = score_data.results[0].scores.length;
    for(let i = 0; i < num_holes ; i++){

        // loop through people to find lowest score for this hole
        let min_score = 10;
        for(let j = 0 ; j < score_data.results.length ;j++){
            let score = score_data.results[j].scores[i];
            if(score === null){score = 7;}
            if(score < min_score){
                min_score = score;
            }
        }

        // loop through people to find the list of people with this score
        let people = Array();
        for(let j = 0 ; j < score_data.results.length ;j++){
            let score = score_data.results[j].scores[i];
            if(score === min_score){
                // add this name to the array
                let first_name = score_data.results[j].first_name;
                let family_name = score_data.results[j].family_name;
                people.push({first_name: first_name, family_name: family_name});
            }
        }
        min_scorers.push({score: min_score, names: people});
    }

    // loop through the people to find how many won a hole
    let winner_stats = Array();
    for(let j = 0 ; j < score_data.results.length ;j++){
        let first_name = score_data.results[j].first_name;
        let family_name = score_data.results[j].family_name;

        // loop through holes for this person
        let win_count = 0;
        for(let i = 0; i < num_holes ; i++) {
            let min_for_hole = min_scorers[i].score;
            let person_score_for_hole = score_data.results[j].scores[i];
            if(min_for_hole == person_score_for_hole){
                win_count++;
            }
        }
        winner_stats.push({first_name: first_name, family_name: family_name, win_count: win_count})
    }

    return {winners: min_scorers, winner_stats: winner_stats};
}
function write_winners_info(latest_round_data){
    let winners_data = find_winners(latest_round_data);
    let winners = winners_data.winners;
    let winners_count = winners_data.winner_stats;
    //console.log(winners);
    let html = "";

    for(i = 0; i < winners.length; i++){
        let html_row ="";
        html_row += "<div class='row'>";
        html_row += `<strong>Hole: ${i+1}, (${parseInt(latest_round_data.start_wordle)+i})`;
        html_row += ` best score: ${winners[i].score}.</strong>`;
        //html += "</div>";
        //html += "<div class='row'>";
        //html += "<div class='col-2'></div>";
        //html += "<div class='col-2'>";
        let phrase = "";
        if(winners[i].names.length == 1){
            phrase = "person";
        }else{
            phrase = "people";
        }
        html_row += ` ${winners[i].names.length} ${phrase} got this score`;
        //html += "</div>";
        html_row += "</div>";
        html_row += "<div class='row mb-3'>";
        for(j = 0; j < winners[i].names.length; j++){
            //html += "<div class='row'>";
            //html += "<div class='col-2'></div>";
            //html += "<div class='col-2'>";
            html_row += `${winners[i].names[j].first_name}`;
            html_row += ` ${winners[i].names[j].family_name}, `;
            //html += "</div>";
            //html += "</div>";
        }
        html_row += "</div>";
        if(winners[i].names.length>0){
            html += html_row;
        }
    }
    html += "<div class='row mt-5'>";
    html += "<div class='col-6'><h5>How many holes did you get the best score?</h5></div>";
    html += "</div>";
    for(j = 0; j < winners_count.length; j++){
        html += "<div class='row'>";
        html += "<div class='col-2'></div>";
        html += "<div class='col-4'>";
        html += `${winners_count[j].first_name}`;
        html += ` ${winners_count[j].family_name} `;
        html += ` :  ${winners_count[j].win_count} `;
        html += "</div>";
        html += "</div>";
    }
    $('#scores').html(html);

}