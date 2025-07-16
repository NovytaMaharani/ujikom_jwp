<?php
// Fungsi untuk membaca data dari file txt
function readData($filename) {
    if (!file_exists($filename)) return [];
    $lines = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];
    foreach ($lines as $line) {
        $data[] = explode("|", $line);
    }
    return $data;
}

// Fungsi untuk menulis data ke file txt
function writeData($filename, $data) {
    $lines = [];
    foreach ($data as $row) {
        $lines[] = implode("|", $row);
    }
    file_put_contents($filename, implode("\n", $lines));
}

// Tambah data
function addData($filename, $row) {
    $file = fopen($filename, "a");
    fwrite($file, implode("|", $row) . "\n");
    fclose($file);
}

// Hapus data berdasarkan index
function deleteData($filename, $index) {
    $data = readData($filename);
    unset($data[$index]);
    writeData($filename, $data);
}

// Update data berdasarkan index
function updateData($filename, $index, $newRow) {
    $data = readData($filename);
    $data[$index] = $newRow;
    writeData($filename, $data);
}
?>
