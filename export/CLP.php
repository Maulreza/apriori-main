<?php
include_once "../database.php";
include_once "../fungsi.php";

$id_process = $_REQUEST["id_process"];
$db_object = new database();

// Query to fetch data
$sql_que = "SELECT conf.*, log.start_date, log.end_date
FROM confidence conf, process_log `log`
WHERE conf.id_process = '$id_process'
AND conf.id_process = log.id
ORDER BY conf.nilai_uji_lift DESC";

($db_query = $db_object->db_query($sql_que)) or die("Query failed");

// Function to remove spaces from array elements
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
$i = 0;
$cell = [];

while ($data = $db_object->db_fetch_array($db_query)) {
    if ($data["nilai_uji_lift"] < 1.0) continue;

    $kombinasi1 = removeSpaces(explode(",", $data["kombinasi1"]));
    $kombinasi1 = !empty($kombinasi1) ? ReformatSentence($kombinasi1) : "";

    $kombinasi2 = removeSpaces(explode(",", $data["kombinasi2"]));
    $kombinasi2 = !empty($kombinasi2) ? ReformatSentence($kombinasi2) : "";

    $cell[$i][0] = price_format($data["nilai_uji_lift"]);
    $cell[$i][1] = "Jika konsumen " . $kombinasi1 . " maka konsumen akan " . $kombinasi2;
    $i++;
}

require('./fpdf186/fpdf.php');

class PDF extends FPDF
{
    private $currentRow = 0;
    private $maxRowsPerPage = 30;

    function Header()
    {
        $this->SetFont("Courier", "B", 14);
        $this->Cell(28, 1, "Laporan Hasil Analisa", "LRTB", 0, "C");
        $this->Ln(1.5);

        // Table header
        $this->SetFont("Courier", "B", 10);
        $this->Cell(1, 1, "No", 1, 0, "C");
        $this->Cell(24, 1, "Rule", 1, 0, "C");
        $this->Cell(3, 1, "Nilai Lift Ratio", 1, 0, "C");
        $this->Ln();
    }

    function Footer()
    {
        $this->SetY(-1);
        $this->SetFont("Courier", "I", 8);
        $this->Cell(0, 0.5, "Halaman " . $this->PageNo() . "/{nb}", 0, 0, "C");
    }

    function checkPageBreak($height)
    {
        if ($this->GetY() + $height > $this->PageBreakTrigger) {
            $this->AddPage();
            return true;
        }
        return false;
    }
}

// Create PDF
$pdf = new PDF("L", "cm", "A4");
$pdf->AliasNbPages();
$pdf->AddPage();

// Table settings
$lineHeight = 0.7;
$colNoWidth = 1;
$colRuleWidth = 24;
$colLiftWidth = 3;

for ($j = 0; $j < $i; $j++) {
    $rule = $cell[$j][1] ?? "";
    $liftValue = $cell[$j][0] ?? "";

    // Calculate needed height for this row
    $ruleLines = ceil($pdf->GetStringWidth($rule) / ($colRuleWidth * 10)); // Approximate lines needed
    $rowHeight = $lineHeight * max(1, $ruleLines);

    // Check if we need a new page
    if ($pdf->checkPageBreak($rowHeight)) {
        $pdf->SetY($pdf->GetY() + 1);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    // Save current position
    $startY = $y;

    // No column - fixed height
    $pdf->MultiCell($colNoWidth, $rowHeight, $j + 1, 1, 'C', false);
    $xRight = $pdf->GetX();
    $yAfter = $pdf->GetY();

    // Rule column - multi-line
    $pdf->SetXY($x + $colNoWidth, $y);
    $pdf->MultiCell($colRuleWidth, $lineHeight, $rule, 1, 'L', false);
    $maxY = $pdf->GetY();

    // Lift Value column - same height as Rule column
    $pdf->SetXY($x + $colNoWidth + $colRuleWidth, $y);
    $pdf->MultiCell($colLiftWidth, $rowHeight, $liftValue, 1, 'C', false);

    // Update position to the maximum Y used
    $pdf->SetXY($x, $maxY);
}

$pdf->Output("Laporan_Analisa.pdf", "I");
?>
