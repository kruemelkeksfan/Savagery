<?php

include_once 'MongoDatabase.php';

// Database Connection
$mongo = new MongoDatabase();

echo json_encode($mongo->get_collections());
