<?php
//session_start();
if (!isset($_SESSION['apriori_parfum_id'])) {
    header("location:index.php?menu=forbidden");
}

include_once "database.php";
include_once "fungsi.php";
include_once "import/excel_reader2.php";
?>
<div class="main-content">
    <div class="main-content-inner">
        <div class="page-content">
            <div class="page-header">
                <h1>
                    Data Transaksi
                </h1>
            </div><!-- /.page-header -->
            <?php
            //object database class
            $db_object = new database();

            $pesan_error = $pesan_success = "";
            if (isset($_GET['pesan_error'])) {
                $pesan_error = $_GET['pesan_error'];
            }
            if (isset($_GET['pesan_success'])) {
                $pesan_success = $_GET['pesan_success'];
            }

            if (isset($_POST['submit'])) {
                // if(!$input_error){
                $data = new Spreadsheet_Excel_Reader($_FILES['file_data_transaksi']['tmp_name']);

                $baris = $data->rowcount($sheet_index = 0);
                $column = $data->colcount($sheet_index = 0);
                //import data excel dari baris kedua, karena baris pertama adalah nama kolom
                // $temp_date = $temp_produk = "";
                for ($i = 2; $i <= $baris; $i++) {
                    for ($c = 1; $c <= $column; $c++) {
                        $value[$c] = $data->val($i, $c);
                    }

                    // if($i==2){
                    //     $temp_produk .= $value[3];
                    // }
                    // else{
                    //     if($temp_date == $value[1]){
                    //         $temp_produk .= ",".$value[3];
                    //     }
                    //     else{
                    $table = "transaksi";
                    // $produkIn = get_produk_to_in($temp_produk);
                    $temp_date = format_date($value[1]);
                    $produkIn = $value[2];

                    //mencegah ada jarak spasi
                    $produkIn = str_replace(" ,", ",", $produkIn);
                    $produkIn = str_replace("  ,", ",", $produkIn);
                    $produkIn = str_replace("   ,", ",", $produkIn);
                    $produkIn = str_replace("    ,", ",", $produkIn);
                    $produkIn = str_replace(", ", ",", $produkIn);
                    $produkIn = str_replace(",  ", ",", $produkIn);
                    $produkIn = str_replace(",   ", ",", $produkIn);
                    $produkIn = str_replace(",    ", ",", $produkIn);
                    //$item1 = explode(",", $produkIn);


                    //                    $field_value = array("transaction_date"=>($temp_date),
                    //                        "produk"=>$produkIn);
                    //                    $query = $db_object->insert_record($table, $field_value);
                    //                    INSERT INTO transaksi (transaction_date, produk)
                    //                    VALUES
                    //                    ('2016-06-01', 'nipple pigeon L'),
                    //                    ('2016-06-01', 'nipple ninio'),
                    //                    ('2016-06-01', 'mamamia L36'),
                    //                    ('2016-06-01', 'sweety FP XL34')
                    $sql = "INSERT INTO transaksi (transaction_date, produk) VALUES ";
                    $value_in = array();
                    //foreach ($item1 as $key => $isi) {
                    //  $value_in[] = "('$temp_date' , '$isi' )";
                    //}
                    //$value_to_sql_in = implode(",", $value_in);
                    //$sql .= $value_to_sql_in;
                    $sql .= " ('$temp_date', '$produkIn')";
                    $db_object->db_query($sql);

                    //         $temp_produk = $value[3];
                    //     }
                    // }

                    // $temp_date = $value[1];
                }
            ?>
                <script>
                    location.replace("?menu=data_transaksi&pesan_success=Data berhasil disimpan");
                </script>
            <?php
            }

            if (isset($_POST['delete'])) {
                $sql = "TRUNCATE transaksi";
                $db_object->db_query($sql);
            ?>
                <script>
                    location.replace("?menu=data_transaksi&pesan_success=Data transaksi berhasil dihapus");
                </script>
            <?php
            }

            $sql = "SELECT
        *
        FROM
         transaksi";
            $query = $db_object->db_query($sql);
            $jumlah = $db_object->db_num_rows($query);
            ?>
            <div class="row">
                <div class="col-sm-4">
                    <div class="widget-box">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12">
                    <div class="widget-box">
                        <div class="widget-body">
                            <div class="widget-main">
                                <?php
                                if (!empty($pesan_error)) {
                                    display_error($pesan_error);
                                }
                                if (!empty($pesan_success)) {
                                    display_success($pesan_success);
                                }

                                echo "Jumlah data: " . $jumlah . "<br>";
                                if ($jumlah == 0) {
                                    echo "Data kosong...";
                                } else {
                                ?>
                                    <table class='table table-bordered table-striped  table-hover'>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Produk</th>
                                            <th>Umur</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Harga</th>
                                        </tr>
                                        <?php
                                        // raw data: smallburger,largeburger,kebab, hotdog, largesosis, blackkebab, kebabsosis, agelow, ageold, agemature, male, female, priceabove20000, pricebelow20000
                                        // produk is smallburger,largeburger,kebab, hotdog, largesosis, blackkebab, kebabsosis
                                        // umur is agelow, ageold, agemature
                                        // jenis kelamin is male, female
                                        // harga is priceabove20000, pricebelow20000
                                        $no = 1;
                                        while ($row = $db_object->db_fetch_array($query)) {
                                            echo "<tr>";
                                            echo "<td>" . $no . "</td>";
                                            echo "<td>" . format_date2($row['transaction_date']) . "</td>";
                                            // echo "<td>".$row['produk']."</td>";
                                            // sesuaikan th dengan data yang ada di database
                                            $rawProduk = explode(",", $row['produk']);
                                            $arrProduk = array("smallkebab", "syawarma", "burger", "kebab", "hotdog", "kebabsosis", "blackkebab");
                                            $arrUmur = array("agelow", "ageold", "agemature");
                                            $arrJenisKelamin = array("male", "female");
                                            $arrHarga = array("priceabove20000", "pricebelow20000");

                                            $produk = "";
                                            $umur = "";
                                            $jenisKelamin = "";
                                            $harga = "";
                                            foreach ($rawProduk as $key => $value) {
                                                if (in_array($value, $arrProduk)) {
                                                    $produk .= $value . ", ";
                                                }
                                                if (in_array($value, $arrUmur)) {
                                                    $umur .= $value . ", ";
                                                }
                                                if (in_array($value, $arrJenisKelamin)) {
                                                    $jenisKelamin .= $value . ", ";
                                                }
                                                if (in_array($value, $arrHarga)) {
                                                    $harga .= $value . ", ";
                                                }
                                            }

                                            echo "<td>" . substr($produk, 0, -2) . "</td>";
                                            echo "<td>" . substr($umur, 0, -2) . "</td>";
                                            echo "<td>" . substr($jenisKelamin, 0, -2) . "</td>";
                                            echo "<td>" . substr($harga, 0, -2) . "</td>";

                                            echo "</tr>";
                                            $no++;
                                        }
                                        ?>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
function get_produk_to_in($produk)
{
    $ex = explode(",", $produk);
    //$temp = "";
    for ($i = 0; $i < count($ex); $i++) {

        $jml_key = array_keys($ex, $ex[$i]);
        if (count($jml_key) > 1) {
            unset($ex[$i]);
        }

        //$temp = $ex[$i];
    }
    return implode(",", $ex);
}

?>