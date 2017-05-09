<?php
session_start();

set_time_limit(0);
ini_set('memory_limit','2048M');

include 'job.php';

echo '<pre>';
$repository = new Repository();

$aListSearch = $repository->getAllSearch();

while ($search = $aListSearch->fetch_object()) {
    if ($search->last_check < date('Ymd')) {
        $params                      = unserialize($search->params);
        $_SESSION['idCurrentSearch'] = $search->id;
        getForData($params);
        $repository->updateDateSearch($search->id);
    }
}
$_SESSION['idCurrentSearch'] = null;
