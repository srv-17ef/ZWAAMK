<!doctype html>
<html lang="en">
<head>
    <meta charset="UCS-2">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>ZWAAMK</title>
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
            $name = strtolower($header);
            $name = str_replace(' ', '_', $name);
//            $name = preg_match('/^[\w]+$/', $name);
            $name = preg_replace("/\([^)]+\)/", "", $name);
            $name = str_replace('.', '', $name);
            $name = str_replace('ä', 'a', $name);
            $name = str_replace('å', 'a', $name);
            $name = str_replace('ö', 'o', $name);
            $sql .= "$name varchar(255),";
        }
        $sql = rtrim($sql, ",");
        $sql .= ');';
        //var_dump($sql);

        runQuery($sql);
    } else {

        $newdata[] = $parts;
    }
}
seed($newdata);

//var_dump($newdata);



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
function seed($data)
{
    $fetchedColumns = fetchAll("SHOW COLUMNS FROM zwaamk");
    $columns = [];
    $primaryKey = null; $primaryMax = null;
    foreach ($fetchedColumns as $fetchedColumn)
    {
        $columns[$fetchedColumn["Field"]] = $fetchedColumn["Type"];
        if( $fetchedColumn["Key"] === "PRI")
        {
            $primaryKey = $fetchedColumn["Field"];
            $primaryMax = fetchOne("SELECT MAX($primaryKey) FROM zwaamk");
            foreach ($primaryMax as $realMax) { $primaryMax = $realMax; }
        }
    }
    $query = "insert into zwaamk values (";
    foreach ($data as $datum) {
        if(!($datum[0] ?? null)) continue;

        $i = 0;
        foreach ($columns as $columnName => $columnData) {
            if($columnName == $primaryKey)
            {
                $datum[$i] = strval(intval($datum[$i]) + $primaryMax);
            }

            $insertThis = $datum[$i];
            if(!in_array(strtolower(explode('(',$columnData)[0]),["int","integer","float"]))
            {
                $insertThis = " '".trim($insertThis," ")."'";
            }

            $query .= $insertThis . ",";

            $i++;
        }
        $query = rtrim($query, ",");
        $query .= "), (";
    }
    $query = rtrim($query, ", (");
    $query .= ");";

    var_dump($query);
//    runQuery($query);
}
?>

</body>
</html>

