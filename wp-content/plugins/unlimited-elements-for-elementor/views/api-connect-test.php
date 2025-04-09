<?php
/*
 * This is a plain php file, with very simple funcitonality that tests api response.
 * 
 */
ini_set("display_errors","on");

echo "The code run here is: file_get_contents(\"https://api.unlimited-elements.com\"); <br><br> Response: <br><br>";


$response = file_get_contents("https://api.unlimited-elements.com");

$response = htmlspecialchars($response);

echo "<pre>".$response."</pre>";
