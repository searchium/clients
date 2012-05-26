<?php

date_default_timezone_set('UTC');
include 'searchium.php';

$s = new SearchiumClient('public', 'YmZlODc3YmIyZWUzNWQ3NGZmNDIyZmQzNjJkMjMwYTBkMGUwMTgxOQ');
$doc = array('author' => 'John Doe',
           'title' => 'PHP client example',
           'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
           'url' => 'http://domain.com/'.rand(0,1000),
           'date' => date('Y-m-d\TH:i:s\Z'));
$docid = $s->save($doc);

if ($docid) {
    echo 'Document saved with ID: ' . $docid . PHP_EOL;
    echo 'Fetching back...' . PHP_EOL;
    $newdoc = $s->get($docid);
    print_r($newdoc);
    echo 'Deleting...';
    if ($s->delete($docid)) 
        echo ' done' . PHP_EOL;
    else 
       echo 'error : ' . $s->error;
} else {
    echo 'error : ' . $s->error;
}