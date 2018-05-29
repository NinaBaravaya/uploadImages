<?php
session_start();

require_once 'config.php';
require_once 'functions.php';

if ($_POST) {
   add_img();
}

// получение массива картинок
$images = images();

// CONTENT
include 'add_img.php';