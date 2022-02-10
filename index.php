<?php
$update = json_decode(file_get_contents("php://input"), TRUE);
echo $update;