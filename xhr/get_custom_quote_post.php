<?php 
// Created By A.Y
if ($f == 'get_custom_quote_post') {
    $data['status'] = 400;
    if (!empty($_GET['post_id'])) {
        $wo['current_post'] = $wo['story'] = Wo_PostData(Wo_Secure($_GET['post_id']));
        if (!empty($wo['story'])) {
            $data['html'] = Wo_LoadPage('lightbox/custom_quote_post');
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}


