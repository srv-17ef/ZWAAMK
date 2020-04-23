<?php
$newdata = [];
$headers = true;
$csv = array_map('str_getcsv', file('data.csv'));
$data = mb_convert_encoding($csv, "UTF-8", "UCS-2");
foreach ($data as $str) {
    $parts = preg_split('/\t/', $str[0]);
    if ($headers) {
        $headers = false;
        $test = 'zwaamk';
        $i = 0;
        foreach ($parts as $header) {
            $name = strtolower($header);
            $name = str_replace(' ', '_', $name);
            $name = preg_replace("/\([^)]+\)/", "", $name);
            $name = str_replace('.', '', $name);
            $name = str_replace('ä', 'a', $name);
            $name = str_replace('å', 'a', $name);
            $name = str_replace('ö', 'o', $name);
            $parts[$i] = $name;
            $i++;
        }

        $sql = "create table if not exists $test (
                $parts[0] int primary key,
                $parts[1] datetime, 
                $parts[2] int, 
                $parts[3] float, 
                $parts[4] int, 
                $parts[5] float, 
                $parts[6] int,
                $parts[7] float,
                $parts[8] float,
                $parts[9] float,
                $parts[10] float,
                $parts[11] varchar(255),
                $parts[12] float,
                $parts[13] float,
                $parts[14] float,
                $parts[15] float,
                $parts[16] float,
                $parts[17] float,
                $parts[18] float
                )";
    } else {
        $parts = preg_replace('/^\s+/', '', $parts);
        $newdata[] = $parts;
    }
}
//runQuery("drop table zwaamk");
runQuery($sql);
//seed2($newdata);

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

function getData(string $query)
{
    return runQuery($query)->fetchAll();
}

//
//function seed($data)
//{
//    foreach ($data as $datum) {
//        $query = "insert into zwaamk values(";
//        foreach ($datum as $value) {
//            $query .= "'$value'" . ",";
//        }
//        $query = rtrim($query, ",");
//        $query .= ");";
//        runQuery($query);
//    }
//}

function seed2($data)
{
    foreach ($data as $datum) {
        if (!$datum[0] ?? null) continue;
        $id = intval($datum[0]);
        foreach ($datum as $index => $value) {
            if ($value === "--.-" || $value === "--" || $value === "---") {
                $datum[$index] = "null";
            }
        }
        $query = "insert into zwaamk values($id, '$datum[1]', $datum[2], $datum[3], $datum[4], $datum[5], $datum[6], $datum[7], $datum[8], $datum[9], $datum[10], '$datum[11]', $datum[12], $datum[13], $datum[14], $datum[15], $datum[16], $datum[17], $datum[18])";
        runQuery($query);
    }
}
