<?php

include_once 'consolare.php';
include_once 'ConsolareMS.php';
include_once 'ConsolareMysql.php';

function log_data($message, $type = "INFO", $file = __FILE__, $line = __LINE__)
{
    $now = new DateTime();
    $timestamp = ($now)->format('Y:m:d H:i:s') . sprintf('%03d', (int)($now->format('u') / 1000));
    echo "[$type][$timestamp][$file][$line]: $message\n";
}

log_data("ROUTINE STARTED");
$id = '';
try {
    // Select novas guias
    $consolare_ms = new ConsolareMS();
    $consolare_ms->Connection();
    $results = $consolare_ms->GetNew();
    $consolare_ms->Close();

    if ($results) {
        $num_order_arr = [];

        $consolare_data = [];
        $where = [];
        foreach ($results as $result) {
            $consolare = new Consolare($result);
            $consolare_data[] = $consolare;
            $num_order_arr[] = $consolare->dadosos_NumOrdem;
            // SETTING WHERE STRING
            $where[] = $consolare->dadosos_NumOrdem;
        }
        log_data("ORDERS FETCHED - [" . implode(", ", $num_order_arr) . "]");

        $consolare_mysql = new ConsolareMysql();
        $consolare_mysql->connect();
        $consolare_data = $consolare_mysql->GetNotIncluded($where, $consolare_data);
        if (count($consolare_data)) {
            $new_consolare = array_map(function ($consolare) {
                return $consolare->dadosos_NumOrdem;
            }, $consolare_data);
            log_data("NEW ORDERS - [" . implode(", ", $new_consolare) . "]");
            foreach ($consolare_data as $consolare) {
                $id = $consolare->dadosos_NumOrdem;
                $consolare_mysql->insert_guia($consolare);
                log_data("SUCCESSFUL ORDER INSERT - [" . $id . "]");
            }
        } else {
            log_data("NO NEW RESULTS");
        }
    }else{
        log_data("NO RESULTS");
    }
    log_data("END ROUTINE");

} catch (PDOException $e) {
    // Handle any errors
    $file = $e->getFile();
    $line = $e->getLine();
    log_data("[$id]" . $e->getMessage(), "ERROR", $file, $line);
    log_data("END ROUTINE");

}
