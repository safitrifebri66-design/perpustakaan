<?php
namespace App\Helpers;

function sanitize($data){
    return htmlspecialchars(trim($data));
}
?>