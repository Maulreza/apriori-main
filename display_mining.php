<?php

function display_process_hasil_mining($db_object, $id_process) {
//    ?>
<!--    <strong>Itemset 1:</strong>
    <table class = 'table table-bordered table-striped  table-hover'>
    <tr>
    <th>No</th>
    <th>Item</th>
    <th>Jumlah</th>
    <th>Support</th>
    <th></th>
    </tr>-->
    <?php
//    $sql1 = "SELECT * FROM itemset1 "
//            . " WHERE id_process = ".$id_process
//            . " ORDER BY lolos DESC";
//    $query1 = $db_object->db_query($sql1);
//    $no = 1;
//    while ($row1 = $db_object->db_fetch_array($query1)) {
//        echo "<tr>";
//        echo "<td>" . $no . "</td>";
//        echo "<td>" . $row1['atribut'] . "</td>";
//        echo "<td>" . $row1['jumlah'] . "</td>";
//        echo "<td>" . $row1['support'] . "</td>";
//        echo "<td>" . ($row1['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
//        echo "</tr>";
//        $no++;
//    }
//    ?>
    <!--</table>-->


<!--    <strong>Itemset 2:</strong>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
            <th>No</th>
            <th>Item 1</th>
            <th>Item 2</th>
            <th>Jumlah</th>
            <th>Support</th>
            <th></th>
        </tr>-->
        <?php
//        $sql1 = "SELECT * FROM itemset2 "
//                . " WHERE id_process = ".$id_process
//                . " ORDER BY lolos DESC";
//        $query1 = $db_object->db_query($sql1);
//        $no = 1;
//        while ($row1 = $db_object->db_fetch_array($query1)) {
//            echo "<tr>";
//            echo "<td>" . $no . "</td>";
//            echo "<td>" . $row1['atribut1'] . "</td>";
//            echo "<td>" . $row1['atribut2'] . "</td>";
//            echo "<td>" . $row1['jumlah'] . "</td>";
//            echo "<td>" . $row1['support'] . "</td>";
//            echo "<td>" . ($row1['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
//            echo "</tr>";
//            $no++;
//        }
//        ?>
<!--    </table>

    <strong>Itemset 3:</strong>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
            <th>No</th>
            <th>Item 1</th>
            <th>Item 2</th>
            <th>Item 3</th>
            <th>Jumlah</th>
            <th>Support</th>
            <th></th>
        </tr>-->
        <?php
//        $sql1 = "SELECT * FROM itemset3 "
//                . " WHERE id_process = ".$id_process
//                . " ORDER BY lolos DESC";
//        $query1 = $db_object->db_query($sql1);
//        $no = 1;
//        while ($row1 = $db_object->db_fetch_array($query1)) {
//            echo "<tr>";
//            echo "<td>" . $no . "</td>";
//            echo "<td>" . $row1['atribut1'] . "</td>";
//            echo "<td>" . $row1['atribut2'] . "</td>";
//            echo "<td>" . $row1['atribut3'] . "</td>";
//            echo "<td>" . $row1['jumlah'] . "</td>";
//            echo "<td>" . $row1['support'] . "</td>";
//            echo "<td>" . ($row1['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
//            echo "</tr>";
//            $no++;
//        }
//        ?>
    <!--</table>-->

    <!-- Itemset 4 -->
    <?php
    $sql1 = "SELECT * FROM confidence "
                . " WHERE id_process = ".$id_process
                . " AND from_itemset=4 "
                ;//. " ORDER BY lolos DESC";
    $query1 = $db_object->db_query($sql1);
    ?>
    <h3>Confidence dari itemset 4</h3>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
        <th>No</th>
        <th>X => Y</th>
        <th>Support X U Y</th>
        <th>Support X </th>
        <th>Confidence</th>
        <th>Keterangan</th>
        </tr>
        <?php
            $no=1;
            $data_confidence = array();
            while($row=$db_object->db_fetch_array($query1)){
                    echo "<tr>";
                    echo "<td>".$no."</td>";
                    echo "<td>".$row['kombinasi1']." => ".$row['kombinasi2']."</td>";
                    echo "<td>".price_format($row['support_xUy'])."</td>";
                    echo "<td>".price_format($row['support_x'])."</td>";
                    echo "<td>".price_format($row['confidence'])."</td>";
                    $keterangan = ($row['confidence'] <= $row['min_confidence'])?"Tidak Lolos":"Lolos";
                    echo "<td>".$keterangan."</td>";
                echo "</tr>";
                $no++;
                if($row['lolos']==1){
                $data_confidence[] = $row;
                }
            }
            ?>
    </table>
    

    <?php
    $sql1 = "SELECT * FROM confidence "
                . " WHERE id_process = ".$id_process
                . " AND from_itemset=3 "
                ;//. " ORDER BY lolos DESC";
    $query1 = $db_object->db_query($sql1);
    ?>
        <h3>Confidence dari itemset 3</h3>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
        <th>No</th>
        <th>X => Y</th>
        <th>Support X U Y</th>
        <th>Support X </th>
        <th>Confidence</th>
        <th>Keterangan</th>
        </tr>
        <?php
            $no=1;
            // $data_confidence = array();
            while($row=$db_object->db_fetch_array($query1)){
                    echo "<tr>";
                    echo "<td>".$no."</td>";
                    echo "<td>".$row['kombinasi1']." => ".$row['kombinasi2']."</td>";
                    echo "<td>".price_format($row['support_xUy'])."</td>";
                    echo "<td>".price_format($row['support_x'])."</td>";
                    echo "<td>".price_format($row['confidence'])."</td>";
                    $keterangan = ($row['confidence'] <= $row['min_confidence'])?"Tidak Lolos":"Lolos";
                    echo "<td>".$keterangan."</td>";
                echo "</tr>";
                $no++;
                if($row['lolos']==1){
                $data_confidence[] = $row;
                }
            }
            ?>
    </table>
    
    
    <?php
    $sql1 = "SELECT * FROM confidence "
                . " WHERE id_process = ".$id_process
                . " AND from_itemset=2 "
                ;//. " ORDER BY lolos DESC";
    $query1 = $db_object->db_query($sql1);
    ?>
    <h3>Confidence dari itemset 2</h3>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
        <th>No</th>
        <th>X => Y</th>
        <th>Support X U Y</th>
        <th>Support X </th>
        <th>Confidence</th>
        <th>Keterangan</th>
        </tr>
        <?php
            $no=1;
            //$data_confidence = array();
            while($row=$db_object->db_fetch_array($query1)){
                    echo "<tr>";
                    echo "<td>".$no."</td>";
                    echo "<td>".$row['kombinasi1']." => ".$row['kombinasi2']."</td>";
                    echo "<td>".price_format($row['support_xUy'])."</td>";
                    echo "<td>".price_format($row['support_x'])."</td>";
                    echo "<td>".price_format($row['confidence'])."</td>";
                    $keterangan = ($row['confidence'] <= $row['min_confidence'])?"Tidak Lolos":"Lolos";
                    echo "<td>".$keterangan."</td>";
                echo "</tr>";
                $no++;
                if($row['lolos']==1){
                $data_confidence[] = $row;
                }
            }
            ?>
    </table>

    <!-- <?php echo "TOTAL LOLOS : ".count($data_confidence); ?> -->

    <h3>Rule Asosiasi yang terbentuk:</h3>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
            <th>No</th>
            <th>X => Y</th>
            <th>Confidence</th>
            <th>Nilai Uji lift</th>
            <th>Korelasi rule</th>
            <!-- <th></th> -->
        </tr>
        <?php

        // confidence is string make the confidence float
        foreach($data_confidence as $key => $val){
            $data_confidence[$key]['confidence'] = floatval($val['confidence']);
        }

        // sort the confidence desc
        usort($data_confidence, function($a, $b) {
            return $b['confidence'] <=> $a['confidence'];
        });
        
        $no = 1;
        //while ($row1 = $db_object->db_fetch_array($query1)) {
        foreach($data_confidence as $key => $val){
//            $kom1 = explode(" , ", $row1['kombinasi1']);
//            $jika = implode(" Dan ", $kom1);
//            $kom2 = explode(" , ", $row1['kombinasi2']);
//            $maka = implode(" Dan ", $kom2);
            echo "<tr>";
            echo "<td>" . $no . "</td>";
            echo "<td>" . $val['kombinasi1']." => ".$val['kombinasi2'] . "</td>";
            echo "<td>" . price_format($val['confidence']) . "</td>";
            echo "<td>" . price_format($val['nilai_uji_lift']) . "</td>";
            echo "<td>" . ($val['korelasi_rule']) . "</td>";
            //echo "<td>" . ($val['lolos'] == 1 ? "Lolos" : "Tidak Lolos") . "</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </table>

    <h3>Nilai Uji Lift:</h3>
    <table class='table table-bordered table-striped  table-hover'>
        <tr>
            <th>No</th>
            <th>X => Y</th>
            <th>Nilai Uji lift</th>
            <th>Keterangan</th>
        </tr>
        <?php
        $sql_que = "SELECT conf.*, log.start_date, log.end_date
            FROM confidence conf, process_log `log`
            WHERE conf.id_process = '$id_process'
            AND conf.id_process = log.id
            ORDER BY conf.nilai_uji_lift DESC";

        ($db_query = $db_object->db_query($sql_que)) or die("Query failed");
        function removeSpaces($inputArray)
        {
            if (!is_array($inputArray)) {
                return [];
            }
            return array_map("trim", $inputArray);
        }

        // Function to reformat the sentence
        function ReformatSentence($arr)
        {
            $categories = [
                "produk" => ["smallkebab", "syawarma", "burger", "kebab", "hotdog", "kebabsosis", "blackkebab"],
                "umur" => ["agelow", "ageold", "agemature"],
                "jenis_kelamin" => ["male", "female"],
                "harga" => ["priceabove20000", "pricebelow20000"],
            ];

            $prefixes = [
                "produk" => "membeli ",
                "umur" => "berumur ",
                "jenis_kelamin" => "berjenis kelamin ",
                "harga" => "memiliki total ",
            ];

            foreach ($arr as &$item) {
                foreach ($categories as $category => $values) {
                    if (in_array($item, $values)) {
                        $item = $prefixes[$category] . $item;
                        break;
                    }
                }
            }
            return implode(" dan ", $arr);
        }

        // Initialize
        $no = 0;
        $cell = [];

        while ($data = $db_object->db_fetch_array($db_query)) {
            if ($data["nilai_uji_lift"] < 1.0) continue;

            $kombinasi1 = removeSpaces(explode(",", $data["kombinasi1"]));
            $kombinasi1 = !empty($kombinasi1) ? ReformatSentence($kombinasi1) : "";

            $kombinasi2 = removeSpaces(explode(",", $data["kombinasi2"]));
            $kombinasi2 = !empty($kombinasi2) ? ReformatSentence($kombinasi2) : "";

            $cell[$no][0] = price_format($data["nilai_uji_lift"]);
            $cell[$no][1] = "Jika konsumen " . $kombinasi1 . " maka konsumen akan " . $kombinasi2;

            echo "<tr>";
            echo "<td>" . ($no + 1) . "</td>";
            echo "<td>" . $cell[$no][1] . "</td>";
            echo "<td>" . $cell[$no][0] . "</td>";
            echo "<td>" . ($data['nilai_uji_lift'] >= 1.0 ? "Lolos" : "Tidak Lolos") . "</td>";
            echo "</tr>";
            $no++;
        }
        ?>
    </table>
    <?php
}
?>