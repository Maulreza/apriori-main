<?php

Start session if needed
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (!isset($_SESSION['apriori_parfum_id'])) {
    header("Location: index.php?menu=forbidden");
    exit;
}

include_once "database.php";
include_once "fungsi.php";
include_once "mining.php";
include_once "display_mining.php";

// Initialize database object
$db_object = new database();

// Get error or success messages
$pesan_error = $_GET['pesan_error'] ?? "";
$pesan_success = $_GET['pesan_success'] ?? "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    echo "post";
    $can_process = true;
    $min_support = $_POST['min_support'] ?? null;
    $min_confidence = $_POST['min_confidence'] ?? null;

    // Validate min_support and min_confidence
    if (empty($min_support) || empty($min_confidence)) {
        header("Location: ?menu=proses_apriori&pesan_error=Min Support dan Min Confidence harus diisi");
        exit;
    }

    if (!is_numeric($min_support) || !is_numeric($min_confidence)) {
        header("Location: ?menu=proses_apriori&pesan_error=Min Support dan Min Confidence harus diisi angka");
        exit;
    }

    // Ensure min support and min confidence are between 0 and 100
    if ($min_support < 0 || $min_support > 100 || $min_confidence < 0 || $min_confidence > 100) {
        header("Location: ?menu=proses_apriori&pesan_error=Min Support dan Min Confidence harus diisi diantara 0 - 100");
        exit;
    }

    // Validate and format date range
    if (!empty($_POST['range_tanggal']) && strpos($_POST['range_tanggal'], " - ") !== false) {
        $tgl = explode(" - ", $_POST['range_tanggal']);
        $start = format_date($tgl[0]);
        $end = format_date($tgl[1]);
    } else {
        header("Location: ?menu=proses_apriori&pesan_error=Range tanggal tidak valid");
        exit;
    }

    if ($can_process) {
        if (isset($_POST['id_process'])) {
            $id_process = $_POST['id_process'];
            reset_hitungan($db_object, $id_process);

            $field = [
                "start_date" => $start,
                "end_date" => $end,
                "min_support" => $min_support,
                "min_confidence" => $min_confidence,
            ];
            $where = ["id" => $id_process];
            $db_object->update_record("process_log", $field, $where);
        } else {
            $field_value = [
                "start_date" => $start,
                "end_date" => $end,
                "min_support" => $min_support,
                "min_confidence" => $min_confidence,
            ];
            $db_object->insert_record("process_log", $field_value);
            $id_process = $db_object->db_insert_id();
        }

        // Execute mining process and display results
        $result = mining_process($db_object, $min_support, $min_confidence, $start, $end, $id_process);
        if ($result) {
            display_success("Proses mining selesai");
        } else {
            display_error("Gagal mendapatkan aturan asosiasi");
        }

        display_process_hasil_mining($db_object, $id_process);
    }
} else {
    
    // Fetch transactions based on date range
    $where = "WHERE 1=1";
    if (!empty($_POST['range_tanggal']) && strpos($_POST['range_tanggal'], " - ") !== false) {
        $tgl = explode(" - ", $_POST['range_tanggal']);
        $start = format_date($tgl[0]);
        $end = format_date($tgl[1]);
        $where = " WHERE transaction_date BETWEEN '$start' AND '$end'";
    }

    var_dump($where);

    $sql = "SELECT * FROM transaksi $where";
    $query = $db_object->db_query($sql);
    $jumlah = $db_object->db_num_rows($query);

    if ($jumlah === 0) {
        echo "Data kosong...";
    } else {
        echo "<table class='table table-bordered table-striped table-hover'>";
        echo "<tr><th>No</th><th>Tanggal</th><th>Produk</th></tr>";
        $no = 1;
        while ($row = $db_object->db_fetch_array($query)) {
            echo "<tr><td>{$no}</td><td>{$row['transaction_date']}</td><td>{$row['produk']}</td></tr>";
            $no++;
        }
        echo "</table>";
    }
}
