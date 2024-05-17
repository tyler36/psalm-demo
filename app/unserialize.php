<?php

$command = $_GET["data"];

getObject($command);

function getObject(string $data) : object {
    return unserialize($data);
}
