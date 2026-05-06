<?php
function get_clients_storage_path(){
    return __DIR__ . '/admin_clients.json';
}

function read_clients(){
    $p = get_clients_storage_path();
    if (!file_exists($p)) return [];
    $json = file_get_contents($p);
    $arr = json_decode($json, true);
    if (!is_array($arr)) return [];
    return $arr;
}

function write_clients(array $clients){
    $p = get_clients_storage_path();
    file_put_contents($p, json_encode(array_values($clients), JSON_PRETTY_PRINT));
}

function find_client(array $clients, $id){
    foreach ($clients as $i => $c) if ((string)$c['id'] === (string)$id) return $i;
    return -1;
}

?>
