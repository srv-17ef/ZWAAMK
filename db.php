<?php
function connect()
{
    $dsn = "mysql:dbname=weather2020;host=192.168.250.74;port=3306;charset=utf8";
    try {
        $db = new PDO($dsn, "demo1234", "demo1234");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        print $e;
        exit();
    }
}

function runQuery(string $query)
{
    $db = connect();
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt;
}
function fetchAll(string $query)
{
    $db = connect();
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll();
}
function fetchOne(string $query)
{
    $db = connect();
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetch();
}