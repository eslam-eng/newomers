<?php

// echo var_dump("ddsd"); 
// if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($f == "post") {

    global $sqlConnect;

    // Update all rows to set the boolean columns to 0
    $updateAllQuery = "UPDATE " . T_ModelWebsite . " SET 
                        is_sticks = 0, 
                        is_games = 0, 
                        is_movies = 0, 
                        is_common_things = 0, 
                        is_memories = 0, 
                        is_events = 0, 
                        is_blogs = 0, 
                        is_jobs = 0, 
                        is_finance = 0, 
                        is_store = 0, 
                        is_user_status = 0, 
                        is_forum = 0, 
                        is_pages = 0, 
                        is_stories = 0,
                        is_groups = 0,
                        is_trending = 0,
                        is_activitie = 0,
                        is_album =0";
    
    $updateAllResult = mysqli_query($sqlConnect, $updateAllQuery);


    if (!$updateAllResult) {
        // Handle the error if the update fails
        echo "Error updating all rows: " . mysqli_error($sqlConnect);
    }

    // Update the settings array with values from the POST request
    foreach ($_POST as $key => $default) {
        if (isset($_POST[$key])) {
            if ( $key != 'hash_id' && $key != 'website_mode') {
                // var_dump($key != 'website_mode');
                // var_dump($key != 'hash_id');

                $query_one = " UPDATE " . T_ModelWebsite . " SET ". $key ." = 1 WHERE `id` = 1";
                $sql       = mysqli_query($sqlConnect, $query_one);
            }
        }
    }
    // echo '<script></script>';
        // Redirect back after the update is finished
        // header("Location: admin-cp/website_mode");
        // exit(); 
}