
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CSV Upload</title>
</head>
<body>

    <h2>Upload CSV File</h2>

    <!-- Form to upload CSV file -->
    <form  method="post" enctype="multipart/form-data">
        <label for="csvFile">Choose CSV File:</label>
        <input type="file" name="csvFile" id="csvFile" accept=".csv" required>
        <button type="submit" name="submit">Upload</button>
    </form>

</body>
</html>

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// --- Upload CSV ---
if (isset($_POST['submit'])) {
    if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
        $fileTmpName = $_FILES['csvFile']['tmp_name'];
        $fileName = $_FILES['csvFile']['name'];

        if (strtolower(pathinfo($fileName, PATHINFO_EXTENSION)) == 'csv') {
            if (($handle = fopen($fileTmpName, "r")) !== FALSE) {
                fgetcsv($handle); // Skip header

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // CSV format: [menu_name, price, cat, subcat, desc]
                    $menu    = trim($data[0]);
                    $price   = trim($data[1]);
                    $cat     = trim($data[2]);
                    $subcat  = trim($data[3]);
                    $desc    = trim($data[4]);

                    // Default values
                    $unit         = 'U';
                    $tax_type     = 'B';
                    $product_type = 2;
                    $orgnNatCd    = 'RW';
                    $itemTyCd     = 2;
                    $pkgUnitCd    = 'AM';
                    $qtyUnitCd    = 'U';
                    $dftPrc       = (int)$price;
                    $itemNm       = $menu;

                    // --- Generate next itemCd ---
                    $stmt = $db->prepare("SELECT itemCd FROM menu ORDER BY menu_id DESC LIMIT 1");
                    $stmt->execute();
                    if ($row = $stmt->fetch()) {
                        $curCd = $row['itemCd'];
                        $cut = substr($curCd, 7);
                        $next = str_pad(intval($cut) + 1, 7, '0', STR_PAD_LEFT);
                        $itemCd = $orgnNatCd.$itemTyCd.$pkgUnitCd.$qtyUnitCd.$next;
                    } else {
                        $itemCd = $orgnNatCd.$itemTyCd.$pkgUnitCd.$qtyUnitCd.'0000001';
                    }

                    // --- Prepare data for RRA ---
                    $postData = [
                        'itemNm'     => $itemNm,
                        'cat_id'     => $cat,
                        'subcat_ID'  => $subcat,
                        'menu_desc'  => $desc,
                        'dftPrc'     => $dftPrc,
                        'qtyUnitCd'  => $qtyUnitCd,
                        'taxTyCd'    => $tax_type,
                        'itemTyCd'   => $itemTyCd,
                        'orgnNatCd'  => $orgnNatCd,
                        'pkgUnitCd'  => $pkgUnitCd,
                        'grpPrcL1'   => 0,
                        'grpPrcL2'   => 0,
                        'grpPrcL3'   => 0,
                        'grpPrcL4'   => 0,
                        'grpPrcL5'   => 0,
                    ];
                    $postData['dftPrc'] = (int) $postData['dftPrc'];

                    $ab = json_encode(array_merge($postData, ['itemCd' => $itemCd]));
                    $resp = rra_function($ab, 'items/saveItems');

                    // --- If RRA success, save locally ---
                    if ($resp['resultCd'] == '000') {
                        $save = $db->prepare("INSERT INTO menu
                            (menu_name, cat_id, subcat_ID, unit, menu_desc, menu_price, tax_type, product_type, itemCd, itemTyCd, itemNm, orgnNatCd, pkgUnitCd, qtyUnitCd, dftPrc)
                            VALUES (:menu_name, :cat, :subcat, :unit, :menu_desc, :menu_price, :tax_type, :product_type, :itemCd, :itemTyCd, :itemNm, :orgnNatCd, :pkgUnitCd, :qtyUnitCd, :dftPrc)");
                        $save->execute([
                            ':menu_name'   => $menu,
                            ':cat'         => $cat,
                            ':subcat'      => $subcat,
                            ':unit'        => $unit,
                            ':menu_desc'   => $desc,
                            ':menu_price'  => $dftPrc,
                            ':tax_type'    => $tax_type,
                            ':product_type'=> $product_type,
                            ':itemCd'      => $itemCd,
                            ':itemTyCd'    => $itemTyCd,
                            ':itemNm'      => $itemNm,
                            ':orgnNatCd'   => $orgnNatCd,
                            ':pkgUnitCd'   => $pkgUnitCd,
                            ':qtyUnitCd'   => $qtyUnitCd,
                            ':dftPrc'      => $dftPrc
                        ]);
                        echo "<p>✔️ $menu added successfully.</p>";
                    } else {
                        echo "<p>❌ Failed to save $menu in RRA (Error: {$resp['resultMsg']}).</p>";
                    }
                }

                fclose($handle);
            }
        } else {
            echo "Please upload a valid CSV file.";
        }
    } else {
        echo "Error: " . $_FILES['csvFile']['error'];
    }
}
?>





