<!doctype html>
<html lang="en">
<head>
    <meta charset="UCS-2">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<?php
$newdata = [];
$headers = true;
$csv = array_map('str_getcsv', file('data.csv'));
$data = mb_convert_encoding($csv, "UTF-8", "UCS-2");
//var_dump($data);
foreach ($data as $str) {
    $parts = preg_split('/\t/', $str[0]);
    if ($headers) {
//        var_dump($parts);
        $headers = false;

        $test = 'zwaamk';
        $sql = "create table if not exists $test (";
        foreach ($parts as $header) {
//            var_dump($header);
            $name = cleandata($header);
            $sql .= "$name varchar(255),";
        }
        $sql = rtrim($sql, ",");
        $sql .= ');';
        $primarykey = cleandata($parts[0]);
        $sqlAlter = "alter table $test
    modify $primarykey int NOT NULL PRIMARY KEY ;";
        runQuery("drop table if exists $test");
        runQuery($sql);
        runQuery("truncate $test");
        runQuery($sqlAlter);
        Echo 'database created';
    }
    $newdata[] = $parts;
}

//var_dump($newdata);

function cleandata(string $data)
{
    $name = strtolower($data);
    $name = str_replace(' ', '_', $name);
//            $name = preg_match('/^[\w]+$/', $name);
    $name = preg_replace("/\([^)]+\)/", "", $name);
    $name = str_replace('.', '', $name);
    $name = str_replace('ä', 'a', $name);
    $name = str_replace('å', 'a', $name);
    $name = str_replace('ö', 'o', $name);
    return $name;
}


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


?>

</body>
</html>

