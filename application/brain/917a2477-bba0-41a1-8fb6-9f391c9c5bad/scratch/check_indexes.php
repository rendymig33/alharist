<?php
$db = new PDO('sqlite:database.db');
$result = $db->query("PRAGMA index_list('items')");
echo "Indexes:\n";
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    print_r($row);
    $idxName = $row['name'];
    $info = $db->query("PRAGMA index_info('$idxName')");
    while ($infoRow = $info->fetch(PDO::FETCH_ASSOC)) {
        print_r($infoRow);
    }
}
