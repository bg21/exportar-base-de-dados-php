<?php
require("conexao.php");

if (isset($_GET['exportar']) && $_GET['exportar'] == "true") {

    set_time_limit(3000);
    $db = conectar();

    $f = fopen("backup/" . "Base de Dados - " . date('d-m-Y') . '.sql', 'wt');

    $tables = $db->query('SHOW TABLES');
    foreach ($tables as $table) {
        $sql = '-- TABLE: ' . $table[0] . PHP_EOL;
        $create = $db->query('SHOW CREATE TABLE `' . $table[0] . '`')->fetch();
        $sql .= $create['Create Table'] . ';' . PHP_EOL;
        fwrite($f, $sql);

        $rows = $db->query('SELECT * FROM `' . $table[0] . '`');
        $rows->setFetchMode(\PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $row = array_map(array($db, 'quote'), $row);
            $sql = 'INSERT INTO `' . $table[0] . '` (`' . implode('`, `', array_keys($row)) . '`) VALUES (' . implode(', ', $row) . ');' . PHP_EOL;
            fwrite($f, $sql);
        }

        $sql = PHP_EOL;
        $result = fwrite($f, $sql);
    }
    flush();
    fclose($f);

    echo "Base de Dados exportada! Confira na pasta backup.";
}
