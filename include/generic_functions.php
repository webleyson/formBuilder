<?php

function dd($vars, $exit = false){
    //dump and die
    echo "<pre>";
    print_r($vars);
    echo "</pre>";
        exit();
}

function json_response($status, $message, $data = null) {
    header('Content-type: application/json');
    $response = new stdClass();
    $response->status = $status;
    $response->message = $message;
    if ($data !== null) {
        $response->data = $data;
    }
    echo json_encode($response);
}

