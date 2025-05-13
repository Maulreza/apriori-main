<?php
include_once "../database.php";
include_once "../fungsi.php";

$id_process = $_REQUEST["id_process"];
$date = $_REQUEST["date"];
$db_object = new database();

// Query to fetch data
$sql_que = "SELECT conf.*, log.start_date, log.end_date
FROM confidence conf, process_log `log`
WHERE conf.id_process = '$id_process'
AND conf.id_process = log.id
ORDER BY conf.nilai_uji_lift DESC";

($db_query = $db_object->db_query($sql_que)) or die("Query failed");

// Remove spaces from array elements
function removeSpaces($inputArray)
{
    if (!is_array($inputArray)) return [];
    return array_map("trim", $inputArray);
}

// Reformat rules into sentences
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

// Build cell data
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
    private $lineHeight = 0.7;

    function __construct($orientation = 'L', $unit = 'cm', $size = 'A4')
    {
        parent::__construct($orientation, $unit, $size);
        $this->SetMargins(1, 1, 1);
        $this->SetAutoPageBreak(true, 1);
    }

    function Header()
    {
        $this->SetFont("Courier", "B", 14);
        $this->Cell(0, 1, "Laporan Hasil Analisa", 0, 1, "C");
        $this->Ln(0.5);

        $this->SetFillColor(200, 220, 255);
        $this->SetFont("Courier", "B", 10);
        $this->Cell(1, 1, "No", 1, 0, "C", true);
        $this->Cell(24, 1, "Rule", 1, 0, "C", true);
        $this->Cell(3, 1, "Nilai Lift", 1, 1, "C", true);
    }

    function Footer()
    {
        $this->SetY(-1);
        $this->SetFont("Courier", "I", 8);
        $this->Cell(0, 0.5, "Halaman " . $this->PageNo() . "/{nb}", 0, 0, "C");
    }

    function checkPageBreak($h)
    {
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage();
            return true;
        }
        return false;
    }

    function NbLines($w, $txt)
    {
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") $nb--;
        $sep = -1;
        $i = 0; $j = 0; $l = 0; $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++; $sep = -1; $j = $i; $l = 0; $nl++; continue;
            }
            if ($c == ' ') $sep = $i;
            $l += $cw[$c] ?? 0;
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) $i++;
                } else {
                    $i = $sep + 1;
                }
                $sep = -1; $j = $i; $l = 0; $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }
}

// Create PDF
$pdf = new PDF("L", "cm", "A4");
$pdf->AliasNbPages();
$pdf->AddPage();

$colNoWidth = 1;
$colRuleWidth = 24;
$colLiftWidth = 3;
$lineHeight = 0.7;

for ($j = 0; $j < $i; $j++) {
    $rule = $cell[$j][1] ?? "";
    $liftValue = $cell[$j][0] ?? "";
    $fill = $j % 2 == 0;

    $ruleLines = $pdf->NbLines($colRuleWidth, $rule);
    $rowHeight = $lineHeight * max(1, $ruleLines);

    if ($pdf->checkPageBreak($rowHeight)) {
        $pdf->SetY($pdf->GetY() + 1);
    }

    $x = $pdf->GetX();
    $y = $pdf->GetY();

    $pdf->SetFont("Courier", "", 9);
    $pdf->SetFillColor(245, 245, 245);

    $pdf->MultiCell($colNoWidth, $rowHeight, $j + 1, 1, 'C', $fill);
    $pdf->SetXY($x + $colNoWidth, $y);
    $pdf->MultiCell($colRuleWidth, $lineHeight, $rule, 1, 'L', $fill);
    $pdf->SetXY($x + $colNoWidth + $colRuleWidth, $y);
    $pdf->MultiCell($colLiftWidth, $rowHeight, $liftValue, 1, 'C', $fill);

    $pdf->SetXY($x, $y + $rowHeight);
}

$pdf->Output("Laporan_Analisa_".$date.".pdf", "I");
?>
