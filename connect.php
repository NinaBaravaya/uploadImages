<?php
try {
    $dbh = new PDO('mysql:host=localhost', 'root', '');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "CREATE DATABASE IF NOT EXISTS creasept";
    $dbh->exec($sql);

    $dbh = new PDO('mysql:host=localhost;dbname=creasept', 'root', '');
    $sql = "CREATE TABLE IF NOT EXISTS `images` (
      `img_id` int(10) UNSIGNED NOT NULL,
      `name` varchar(255) NOT NULL,
      `img` varchar(255) NOT NULL,
      `size` int(11) NOT NULL,
      `date` datetime NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC";
    $dbh->exec($sql);


    $sql = "ALTER TABLE `images`
  ADD PRIMARY KEY (`img_id`)";
    $dbh->exec($sql);

    $sql = "ALTER TABLE `images`
  MODIFY `img_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT";
    $dbh->exec($sql);

    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->exec("set names utf8");
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}

return $dbh;
