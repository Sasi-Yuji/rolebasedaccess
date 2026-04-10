<?php
require_once 'app/Config/Database.php';
$db = \Config\Database::connect();
$fields = $db->getFieldNames('chat_attachments');
print_r($fields);
