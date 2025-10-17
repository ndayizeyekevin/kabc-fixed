<?php

require_once '../../config/database.php';
require_once '../../models/User.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch($method){
    case 'GET':
        if(isset($_GET['id'])){
            $user = USER::getById($_GET['id']);
            echo json_encode($users);
        } else {
            $users = USER::getAll();
            echo json_encode($users);
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message'=> 'Method not allowed']);
};