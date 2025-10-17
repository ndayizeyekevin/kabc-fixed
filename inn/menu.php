<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Initialize message variables

$msg = $msge = null;

// Helper function to sanitize input or return null if empty
function nullable_input($key) {
    return isset($_POST[$key]) && trim($_POST[$key]) !== '' ? trim($_POST[$key]) : null;
}

// Country list (ISO 3166-1 alpha-2 codes)
$countries = [
    'AF' => 'Afghanistan', 'AX' => 'Åland Islands', 'AL' => 'Albania', 'DZ' => 'Algeria', 
    'AS' => 'American Samoa', 'AD' => 'Andorra', 'AO' => 'Angola', 'AI' => 'Anguilla', 
    'AQ' => 'Antarctica', 'AG' => 'Antigua and Barbuda', 'AR' => 'Argentina', 'AM' => 'Armenia', 
    'AW' => 'Aruba', 'AU' => 'Australia', 'AT' => 'Austria', 'AZ' => 'Azerbaijan', 
    'BS' => 'Bahamas', 'BH' => 'Bahrain', 'BD' => 'Bangladesh', 'BB' => 'Barbados', 
    'BY' => 'Belarus', 'BE' => 'Belgium', 'BZ' => 'Belize', 'BJ' => 'Benin', 
    'BM' => 'Bermuda', 'BT' => 'Bhutan', 'BO' => 'Bolivia', 'BQ' => 'Bonaire, Sint Eustatius and Saba', 
    'BA' => 'Bosnia and Herzegovina', 'BW' => 'Botswana', 'BV' => 'Bouvet Island', 'BR' => 'Brazil', 
    'IO' => 'British Indian Ocean Territory', 'BN' => 'Brunei Darussalam', 'BG' => 'Bulgaria', 
    'BF' => 'Burkina Faso', 'BI' => 'Burundi', 'CV' => 'Cabo Verde', 'KH' => 'Cambodia', 
    'CM' => 'Cameroon', 'CA' => 'Canada', 'KY' => 'Cayman Islands', 'CF' => 'Central African Republic', 
    'TD' => 'Chad', 'CL' => 'Chile', 'CN' => 'China', 'CX' => 'Christmas Island', 
    'CC' => 'Cocos (Keeling) Islands', 'CO' => 'Colombia', 'KM' => 'Comoros', 'CG' => 'Congo', 
    'CD' => 'Congo, Democratic Republic of the', 'CK' => 'Cook Islands', 'CR' => 'Costa Rica', 
    'CI' => 'Côte d\'Ivoire', 'HR' => 'Croatia', 'CU' => 'Cuba', 'CW' => 'Curaçao', 
    'CY' => 'Cyprus', 'CZ' => 'Czech Republic', 'DK' => 'Denmark', 'DJ' => 'Djibouti', 
    'DM' => 'Dominica', 'DO' => 'Dominican Republic', 'EC' => 'Ecuador', 'EG' => 'Egypt', 
    'SV' => 'El Salvador', 'GQ' => 'Equatorial Guinea', 'ER' => 'Eritrea', 'EE' => 'Estonia', 
    'SZ' => 'Eswatini', 'ET' => 'Ethiopia', 'FK' => 'Falkland Islands (Malvinas)', 'FO' => 'Faroe Islands', 
    'FJ' => 'Fiji', 'FI' => 'Finland', 'FR' => 'France', 'GF' => 'French Guiana', 
    'PF' => 'French Polynesia', 'TF' => 'French Southern Territories', 'GA' => 'Gabon', 
    'GM' => 'Gambia', 'GE' => 'Georgia', 'DE' => 'Germany', 'GH' => 'Ghana', 'GI' => 'Gibraltar', 
    'GR' => 'Greece', 'GL' => 'Greenland', 'GD' => 'Grenada', 'GP' => 'Guadeloupe', 
    'GU' => 'Guam', 'GT' => 'Guatemala', 'GG' => 'Guernsey', 'GN' => 'Guinea', 
    'GW' => 'Guinea-Bissau', 'GY' => 'Guyana', 'HT' => 'Haiti', 'HM' => 'Heard Island and McDonald Islands', 
    'VA' => 'Holy See', 'HN' => 'Honduras', 'HK' => 'Hong Kong', 'HU' => 'Hungary', 
    'IS' => 'Iceland', 'IN' => 'India', 'ID' => 'Indonesia', 'IR' => 'Iran', 
    'IQ' => 'Iraq', 'IE' => 'Ireland', 'IM' => 'Isle of Man', 'IL' => 'Israel', 
    'IT' => 'Italy', 'JM' => 'Jamaica', 'JP' => 'Japan', 'JE' => 'Jersey', 
    'JO' => 'Jordan', 'KZ' => 'Kazakhstan', 'KE' => 'Kenya', 'KI' => 'Kiribati', 
    'KP' => 'Korea, Democratic People\'s Republic of', 'KR' => 'Korea, Republic of', 
    'KW' => 'Kuwait', 'KG' => 'Kyrgyzstan', 'LA' => 'Lao People\'s Democratic Republic', 
    'LV' => 'Latvia', 'LB' => 'Lebanon', 'LS' => 'Lesotho', 'LR' => 'Liberia', 
    'LY' => 'Libya', 'LI' => 'Liechtenstein', 'LT' => 'Lithuania', 'LU' => 'Luxembourg', 
    'MO' => 'Macao', 'MG' =>'Madagascar', 'MW' => 'Malawi', 'MY' => 'Malaysia', 
    'MV' => 'Maldives', 'ML' => 'Mali', 'MT' => 'Malta', 'MH' => 'Marshall Islands', 
    'MQ' => 'Martinique', 'MR' => 'Mauritania', 'MU' => 'Mauritius', 'YT' => 'Mayotte', 
    'MX' => 'Mexico', 'FM' => 'Micronesia', 'MD' => 'Moldova', 'MC' => 'Monaco', 
    'MN' => 'Mongolia', 'ME' => 'Montenegro', 'MS' => 'Montserrat', 'MA' => 'Morocco', 
    'MZ' => 'Mozambique', 'MM' => 'Myanmar', 'NA' => 'Namibia', 'NR' => 'Nauru', 
    'NP' => 'Nepal', 'NL' => 'Netherlands', 'NC' => 'New Caledonia', 'NZ' => 'New Zealand', 
    'NI' => 'Nicaragua', 'NE' => 'Niger', 'NG' => 'Nigeria', 'NU' => 'Niue', 
    'NF' => 'Norfolk Island', 'MK' => 'North Macedonia', 'MP' => 'Northern Mariana Islands', 
    'NO' => 'Norway', 'OM' => 'Oman', 'PK' => 'Pakistan', 'PW' => 'Palau', 
    'PS' => 'Palestine', 'PA' => 'Panama', 'PG' => 'Papua New Guinea', 'PY' => 'Paraguay', 
    'PE' => 'Peru', 'PH' => 'Philippines', 'PN' => 'Pitcairn', 'PL' => 'Poland', 
    'PT' => 'Portugal', 'PR' => 'Puerto Rico', 'QA' => 'Qatar', 'RE' => 'Réunion', 
    'RO' => 'Romania', 'RU' => 'Russian Federation', 'RW' => 'Rwanda', 'BL' => 'Saint Barthélemy', 
    'SH' => 'Saint Helena', 'KN' => 'Saint Kitts and Nevis', 'LC' => 'Saint Lucia', 
    'MF' => 'Saint Martin', 'PM' => 'Saint Pierre and Miquelon', 'VC' => 'Saint Vincent and the Grenadines', 
    'WS' => 'Samoa', 'SM' => 'San Marino', 'ST' => 'Sao Tome and Principe', 'SA' => 'Saudi Arabia', 
    'SN' => 'Senegal', 'RS' => 'Serbia', 'SC' => 'Seychelles', 'SL' => 'Sierra Leone', 
    'SG' => 'Singapore', 'SX' => 'Sint Maarten', 'SK' => 'Slovakia', 'SI' => 'Slovenia', 
    'SB' => 'Solomon Islands', 'SO' => 'Somalia', 'ZA' => 'South Africa', 
    'GS' => 'South Georgia and the South Sandwich Islands', 'SS' => 'South Sudan', 
    'ES' => 'Spain', 'LK' => 'Sri Lanka', 'SD' => 'Sudan', 'SR' => 'Suriname', 
    'SJ' => 'Svalbard and Jan Mayen', 'SE' => 'Sweden', 'CH' => 'Switzerland', 
    'SY' => 'Syrian Arab Republic', 'TW' => 'Taiwan', 'TJ' => 'Tajikistan', 
    'TZ' => 'Tanzania', 'TH' => 'Thailand', 'TL' => 'Timor-Leste', 'TG' => 'Togo', 
    'TK' => 'Tokelau', 'TO' => 'Tonga', 'TT' => 'Trinidad and Tobago', 'TN' => 'Tunisia', 
    'TR' => 'Turkey', 'TM' => 'Turkmenistan', 'TC' => 'Turks and Caicos Islands', 
    'TV' => 'Tuvalu', 'UG' => 'Uganda', 'UA' => 'Ukraine', 'AE' => 'United Arab Emirates', 
    'GB' => 'United Kingdom', 'US' => 'United States', 'UM' => 'United States Minor Outlying Islands', 
    'UY' => 'Uruguay', 'UZ' => 'Uzbekistan', 'VU' => 'Vanuatu', 'VE' => 'Venezuela', 
    'VN' => 'Viet Nam', 'VG' => 'Virgin Islands, British', 'VI' => 'Virgin Islands, U.S.', 
    'WF' => 'Wallis and Futuna', 'EH' => 'Western Sahara', 'YE' => 'Yemen', 
    'ZM' => 'Zambia', 'ZW' => 'Zimbabwe'
];

// Package Unit constants
$package_units = [
    'BOX' => 'Box', 'BAG' => 'Bag', 'CTN' => 'Carton', 'PKT' => 'Packet',
    'BTL' => 'Bottle', 'CAN' => 'Can', 'JAR' => 'Jar', 'TRAY' => 'Tray'
];

// Unit constants
$units = [
    'KG' => 'Kilogram', 'G' => 'Gram', 'L' => 'Liter', 'ML' => 'Milliliter',
    'PC' => 'Piece', 'OZ' => 'Ounce', 'LB' => 'Pound', 'GAL' => 'Gallon'
];

// Pagination and sorting
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$items_per_page = 10;
$offset = ($page - 1) * $items_per_page;
// Validate sort column and order
$valid_sort_columns = ['itemCd', 'menu_name', 'cat_name', 'menu_price', 'menu_id'];
$sort_column = isset($_GET['sort']) && in_array($_GET['sort'], $valid_sort_columns) ? $_GET['sort'] : 'menu_id';
$sort_order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? strtoupper($_GET['order']) : 'DESC';

// Add new menu item
if (isset($_POST['saveMenu'])) {
    try {
        // Sanitize and validate inputs
        $menu = trim($_POST['itemNm']);
        $desc = nullable_input('menu_desc');
        $price = (float)$_POST['dftPrc'];
        $unit = nullable_input('qtyUnitCd');
        $tax_type = nullable_input('taxTyCd');
        $product_type = nullable_input('itemTyCd');
        $cat_id = nullable_input('cat_id');
        $subcat_id = nullable_input('subcat_ID');
        $pkg_unit = nullable_input('pkgUnitCd');
        $country_origin = nullable_input('orgnNatCd');
        $pkg_unit = 'AM';

        // Log inputs for debugging
        error_log("Menu: $menu, Cat: $cat_id, Unit: $unit, Product Type: $product_type, Package: $pkg_unit, Country: $country_origin, Tax: $tax_type, Price: $price");

        // Validate required fields
        if (!$menu || !$cat_id || !$unit || !$product_type || !$pkg_unit || !$country_origin || !$tax_type || $price <= 0) {
            $msge = "All required fields must be filled in. Missing: ";
            if (!$menu) $msge .= "Menu Name, ";
            if (!$cat_id) $msge .= "Category, ";
            if (!$unit) $msge .= "Unit, ";
            if (!$product_type) $msge .= "Product Type, ";
            if (!$pkg_unit) $msge .= "Package Unit, ";
            if (!$country_origin) $msge .= "Country Origin, ";
            if (!$tax_type) $msge .= "Tax Type, ";
            if ($price <= 0) $msge .= "Valid Price, ";
            $msge = rtrim($msge, ', ');
        } else {


            // Define missing columns data
            $tin = '103149499';
            $bhfId = '03'; 
            $itemClsCd = '90101500';
            $itemTyCd = $product_type; 
            $itemNm = $menu;
            $qtyUnitCd = 'U'; 
            $taxTyCd = $tax_type;
            $dftPrc = $price;
            // End of missing columns data
            
            // Get tax rate
            $stmt = $db->prepare("SELECT tax_rate FROM tbl_tax_type WHERE code_name = ?");
            $stmt->execute([$tax_type]);
            $rate = $stmt->fetchColumn() ?: 0;

            // Generate new item code
            $stmt = $db->prepare("SELECT itemCd FROM menu ORDER BY CAST(SUBSTRING(itemCd, -7) AS UNSIGNED) DESC LIMIT 1");
            $stmt->execute();
            $row = $stmt->fetch();
            $nextSuffix = $row ? str_pad((int)substr($row['itemCd'], -7) + 1, 7, '0', STR_PAD_LEFT) : '0000001';
            $itemCd = $country_origin . $product_type . $pkg_unit . $unit . $nextSuffix;

            error_log("Generated Item Code: $itemCd");

            // Check if menu exists (case-insensitive)
            $result = $db->prepare("SELECT 1 FROM menu WHERE LOWER(menu_name) = LOWER(?)");
            $result->execute([$menu]);

            if ($result->rowCount() > 0) {
                $msge = "Menu Already Exists!";
            } else {
                // Insert new menu item
                $insert = $db->prepare("
                    INSERT INTO menu 
                    (item_code, company_id, itemCd, menu_name, menu_desc, menu_price, unit, tax_type, product_type, cat_id, subcat_id, pkgUnitCd, orgnNatCd, tin, bhfId, itemClsCd, itemTyCd, itemNm, qtyUnitCd, taxTyCd, dftPrc) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $success = $insert->execute([
                    $itemCd, 1, $itemCd, $menu, $desc, $price, $unit, $tax_type, $product_type, 
                    $cat_id, $subcat_id, $pkg_unit, $country_origin, $tin, $bhfId, $itemClsCd, $itemTyCd, $itemNm, $qtyUnitCd, $taxTyCd, $dftPrc
                ]);

                if ($success) {
                    $msg = "Successfully Added!";
                    error_log("Menu item inserted successfully with ID: " . $db->lastInsertId());
                } else {
                    $msge = "Failed to insert menu item.";
                    error_log("Insert failed: " . print_r($insert->errorInfo(), true));
                }
            }
        }
        echo '<meta http-equiv="refresh" content="1;URL=index?resto=menu">';
    } catch (PDOException $e) {
        $msge = "Database Error: " . $e->getMessage();
        error_log("PDO Error: " . $e->getMessage());
    }
}

// Update menu item
if (isset($_POST['btn-update'])) {
    try {
        $id = (int)$_POST['id'];
        $menu = trim($_POST['menu']);
        $cat = (int)$_POST['cat'];
        $subcat = nullable_input('subcat') ? (int)$_POST['subcat'] : null;
        $desc = nullable_input('desc');
        $price = (float)$_POST['price'];

        if (!$id || !$menu || !$cat || !$price) {
            $msge = "Required fields missing for update.";
        } else {
            $sql = $db->prepare("
                UPDATE menu 
                SET menu_name = ?, cat_id = ?, subcat_id = ?, menu_desc = ?, menu_price = ? 
                WHERE menu_id = ?
            ");
            $success = $sql->execute([$menu, $cat, $subcat, $desc, $price, $id]);
            
            if ($success) {
                $msg = "Updated Successfully";
            } else {
                $msge = "Update failed";
                error_log("Update failed: " . print_r($sql->errorInfo(), true));
            }
        }
        echo '<meta http-equiv="refresh" content="1;URL=?resto=menu">';
    } catch (PDOException $e) {
        $msge = "Database Error: " . $e->getMessage();
        error_log("PDO Error: " . $e->getMessage());
    }
}

// Delete menu item
if (isset($_POST['btn-delete'])) {
    try {
        $delete_id = (int)$_POST['delete_id'];
        if ($delete_id) {
            // Check for pending orders using this item
            $check = $db->prepare("SELECT COUNT(*) FROM cart_sales WHERE item_id = ? AND status = 1");
            $check->execute([$delete_id]);
            $pending_orders = $check->fetchColumn();
            if ($pending_orders > 0) {
                $msge = "Cannot delete: This item is used in a pending order.";
            } else {
                $del = $db->prepare("DELETE FROM menu WHERE menu_id = ?");
                $success = $del->execute([$delete_id]);
                if ($success) {
                    $msg = "Deleted Successfully";
                } else {
                    $msge = "Delete failed";
                    error_log("Delete failed: " . print_r($del->errorInfo(), true));
                }
            }
        } else {
            $msge = "Invalid item for deletion.";
        }
        echo '<meta http-equiv="refresh" content="1;URL=?resto=menu">';
    } catch (PDOException $e) {
        $msge = "Database Error: " . $e->getMessage();
        error_log("PDO Error: " . $e->getMessage());
    }
}

// Get total number of records for pagination
try {
    $total_stmt = $db->prepare("SELECT COUNT(*) FROM menu");
    $total_stmt->execute();
    $total_items = $total_stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);
} catch (PDOException $e) {
    $msge = "Database Error: Failed to fetch total items.";
    error_log("PDO Error: " . $e->getMessage());
    $total_items = 0;
    $total_pages = 1;
}

// Fetch menu items with pagination and sorting
try {
    $sql = $db->prepare("
        SELECT m.*, c.cat_name, s.subcat_name 
        FROM menu m
        INNER JOIN category c ON m.cat_id = c.cat_id
        LEFT JOIN subcategory s ON m.subcat_id = s.subcat_id
        ORDER BY $sort_column $sort_order
        LIMIT :limit OFFSET :offset
    ");
    $sql->bindValue(':limit', (int)$items_per_page, PDO::PARAM_INT);
    $sql->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $sql->execute();
    $menu_items = $sql->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $msge = "Database Error: Failed to fetch menu items.";
    error_log("Query Error: " . $e->getMessage());
    $menu_items = [];
}
?>

<!-- Breadcomb area Start -->
<div class="breadcomb-area">
    <div class="container">
        <?php if ($msg): ?>
            <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Well Done!</strong> <?=htmlentities($msg)?>
            </div>
        <?php elseif ($msge): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <strong>Sorry!</strong> <?=htmlentities($msge)?>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-xs-12 col-md-6">
                <h2>Manage Menu</h2>
                <p>Welcome to <?=htmlentities($Rname)?> <span class="bread-ntd">Panel</span></p>
            </div>
            <div class="col-xs-12 col-md-6 text-right">
                <button class="btn btn-primary" data-toggle="modal" data-target="#addMenuModal"><i class="fa fa-plus-circle"></i> Add Menu</button>
            </div>
        </div>
    </div>
</div>
<!-- Breadcomb area End -->

<!-- Data Table area Start -->
<div class="data-table-area">
    <div class="container">
        <div class="table-responsive">
            <table id="menu-table" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th><a href="?resto=menu&sort=itemCd&order=<?= $sort_column == 'itemCd' && $sort_order == 'ASC' ? 'DESC' : 'ASC' ?>&page=<?= $page ?>">Code <?= $sort_column == 'itemCd' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?resto=menu&sort=menu_name&order=<?= $sort_column == 'menu_name' && $sort_order == 'ASC' ? 'DESC' : 'ASC' ?>&page=<?= $page ?>">Menu Name <?= $sort_column == 'menu_name' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th><a href="?resto=menu&sort=cat_name&order=<?= $sort_column == 'cat_name' && $sort_order == 'ASC' ? 'DESC' : 'ASC' ?>&page=<?= $page ?>">Category <?= $sort_column == 'cat_name' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Subcategory</th>
                        <th>Description</th>
                        <th><a href="?resto=menu&sort=menu_price&order=<?= $sort_column == 'menu_price' && $sort_order == 'ASC' ? 'DESC' : 'ASC' ?>&page=<?= $page ?>">Price <?= $sort_column == 'menu_price' ? ($sort_order == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                        <th>Unit</th>
                        <th>Package Unit</th>
                        <th>Country Origin</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = ($page - 1) * $items_per_page + 1;
                foreach ($menu_items as $fetch):
                    $unit_display = $units[$fetch['unit']] ?? 'Unknown Unit';
                    $pkg_display = $package_units[$fetch['pkgUnitCd']] ?? 'Unknown Package';
                    $country_display = $countries[$fetch['orgnNatCd']] ?? 'Unknown Country';
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= htmlentities($fetch['itemCd']) ?></td>
                        <td><?= htmlentities($fetch['menu_name']) ?></td>
                        <td><?= htmlentities($fetch['cat_name']) ?></td>
                        <td><?= htmlentities($fetch['subcat_name'] ?? 'N/A') ?></td>
                        <td><?= htmlentities($fetch['menu_desc'] ?? '') ?></td>
                        <td><?= number_format($fetch['menu_price'], 2) ?></td>
                        <td><?= htmlentities($fetch['unit']) ?></td>
                        <td><?= htmlentities($fetch['pkgUnitCd']) ?></td>
                        <td><?= htmlentities($country_display) ?></td>
                        <td>
                            <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#updateModal<?= $fetch['menu_id'] ?>">Edit</button>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                <input type="hidden" name="delete_id" value="<?= $fetch['menu_id'] ?>">
                                <button type="submit" name="btn-delete" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Update Modal -->
                    <div id="updateModal<?= $fetch['menu_id'] ?>" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="updateLabel<?= $fetch['menu_id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" class="form-horizontal" action="">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        <h4 class="modal-title" id="updateLabel<?= $fetch['menu_id'] ?>">Update Menu Item</h4>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="id" value="<?= $fetch['menu_id'] ?>">
                                        <div class="form-group">
                                            <label class="control-label col-sm-4">Menu Name</label>
                                            <div class="col-sm-8">
                                                <input type="text" class="form-control" name="menu" value="<?= htmlentities($fetch['menu_name']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4">Category</label>
                                            <div class="col-sm-8">
                                                <select class="form-control select2" name="cat" required>
                                                    <option value="<?= $fetch['cat_id'] ?>"><?= htmlentities($fetch['cat_name']) ?></option>
                                                    <?php
                                                    $cats = $db->query("SELECT cat_id, cat_name FROM category WHERE cat_id IN (1,2) ORDER BY cat_name")->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($cats as $catOpt):
                                                        if ($catOpt['cat_id'] != $fetch['cat_id']):
                                                    ?>
                                                        <option value="<?= $catOpt['cat_id'] ?>"><?= htmlentities($catOpt['cat_name']) ?></option>
                                                    <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4">Subcategory</label>
                                            <div class="col-sm-8">
                                                <select class="form-control select2" name="subcat">
                                                    <option value="<?= $fetch['subcat_ID'] ?>"><?= htmlentities($fetch['subcat_name'] ?? 'N/A') ?></option>
                                                    <?php
                                                    $subcats = $db->query("SELECT subcat_id, subcat_name FROM subcategory ORDER BY subcat_name")->fetchAll(PDO::FETCH_ASSOC);
                                                    foreach ($subcats as $subcatOpt):
                                                        if ($subcatOpt['subcat_id'] != $fetch['subcat_id']):
                                                    ?>
                                                        <option value="<?= $subcatOpt['subcat_id'] ?>"><?= htmlentities($subcatOpt['subcat_name']) ?></option>
                                                    <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4">Description</label>
                                            <div class="col-sm-8">
                                                <textarea name="desc" class="form-control"><?= htmlentities($fetch['menu_desc'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-sm-4">Price</label>
                                            <div class="col-sm-8">
                                                <input type="number" step="0.01" name="price" class="form-control" value="<?= htmlentities($fetch['menu_price']) ?>" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" name="btn-update" class="btn btn-primary">Update</button>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </tbody>
            </table>
            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="<?= $page <= 1 ? 'disabled' : '' ?>">
                        <a href="?resto=menu&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&page=<?= max(1, $page - 1) ?>" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                        <li class="<?= $p == $page ? 'active' : '' ?>">
                            <a href="?resto=menu&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="<?= $page >= $total_pages ? 'disabled' : '' ?>">
                        <a href="?resto=menu&sort=<?= $sort_column ?>&order=<?= $sort_order ?>&page=<?= min($total_pages, $page + 1) ?>" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>
<!-- Data Table area End -->

<!-- Add Menu Modal -->
<div class="modal fade" id="addMenuModal" tabindex="-1" role="dialog" aria-labelledby="addMenuLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" class="form-horizontal" action="" id="addMenuForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title" id="addMenuLabel">Add New Menu</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="control-label col-sm-3">Menu Name</label>
                        <div class="col-sm-9">
                            <input type="text" name="itemNm" class="form-control" placeholder="Menu Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <select name="cat_id" class="form-control select2" required>
                                <option value="" disabled selected>Select Category</option>
                                <?php
                                try {
                                    $cats = $db->query("SELECT cat_id, cat_name FROM category WHERE cat_id IN (1,2) ORDER BY cat_name")->fetchAll(PDO::FETCH_ASSOC);
                                    if (empty($cats)) {
                                        echo '<option value="" disabled>No categories available</option>';
                                        error_log("No categories found in database");
                                    } else {
                                        foreach ($cats as $cat):
                                            echo "<option value='{$cat['cat_id']}'>" . htmlentities($cat['cat_name']) . "</option>";
                                        endforeach;
                                    }
                                } catch (PDOException $e) {
                                    error_log("Category fetch error: " . $e->getMessage());
                                    echo '<option value="" disabled>Error loading categories</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Subcategory</label>
                        <div class="col-sm-9">
                            <select name="subcat_ID" class="form-control select2">
                                <option value="" disabled selected>Select Subcategory</option>
                                <?php
                                try {
                                    $subcats = $db->query("SELECT subcat_id, subcat_name FROM subcategory ORDER BY subcat_name")->fetchAll(PDO::FETCH_ASSOC);
                                    if (empty($subcats)) {
                                        echo '<option value="" disabled>No subcategories available</option>';
                                        error_log("No subcategories found in database");
                                    } else {
                                        foreach ($subcats as $subcat):
                                            echo "<option value='{$subcat['subcat_id']}'>" . htmlentities($subcat['subcat_name']) . "</option>";
                                        endforeach;
                                    }
                                } catch (PDOException $e) {
                                    error_log("Subcategory fetch error: " . $e->getMessage());
                                    echo '<option value="" disabled>Error loading subcategories</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Unit</label>
                        <div class="col-sm-9">
                            <select name="qtyUnitCd" class="form-control select2" required>
                                <option value="" disabled selected>Select Unit</option>
                                <?php
                                asort($units);
                                foreach ($units as $code => $name):
                                    echo "<option value='$code'>" . htmlentities($name) . "</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <select name="itemTyCd" class="form-control select2" required>
                                <option value="" disabled selected>Select Type</option>
                                <?php
                                try {
                                    $types = $db->query("SELECT code, code_name FROM tbl_product_type ORDER BY code_name")->fetchAll(PDO::FETCH_ASSOC);
                                    if (empty($types)) {
                                        echo '<option value="" disabled>No product types available</option>';
                                        error_log("No product types found in database");
                                    } else {
                                        foreach ($types as $type):
                                            echo "<option value='{$type['code']}'>" . htmlentities($type['code_name']) . "</option>";
                                        endforeach;
                                    }
                                } catch (PDOException $e) {
                                    error_log("Product type fetch error: " . $e->getMessage());
                                    echo '<option value="" disabled>Error loading product types</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Tax</label>
                        <div class="col-sm-9">
                            <select name="taxTyCd" class="form-control select2" required>
                                <option value="" disabled selected>Select Tax Type</option>
                                <?php
                                try {
                                    $taxes = $db->query("SELECT code_name, code_description FROM tbl_tax_type ORDER BY code_description")->fetchAll(PDO::FETCH_ASSOC);
                                    if (empty($taxes)) {
                                        echo '<option value="" disabled>No tax types available</option>';
                                        error_log("No tax types found in database");
                                    } else {
                                        foreach ($taxes as $tax):
                                            echo "<option value='{$tax['code_name']}'>" . htmlentities($tax['code_description']) . "</option>";
                                        endforeach;
                                    }
                                } catch (PDOException $e) {
                                    error_log("Tax type fetch error: " . $e->getMessage());
                                    echo '<option value="" disabled>Error loading tax types</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Package Unit</label>
                        <div class="col-sm-9">
                            <select name="pkgUnitCd" class="form-control select2" required>
                                <option value="" disabled selected>Select Package Unit</option>
                                <?php
                                asort($package_units);
                                foreach ($package_units as $code => $name):
                                    echo "<option value='$code'>" . htmlentities($name) . "</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Country Origin</label>
                        <div class="col-sm-9">
                            <select name="orgnNatCd" class="form-control select2" required>
                                <option value="" disabled selected>Select Country</option>
                                <?php
                                asort($countries);
                                foreach ($countries as $code => $name):
                                    echo "<option value='$code'>" . htmlentities($name) . "</option>";
                                endforeach;
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <textarea name="menu_desc" class="form-control" placeholder="Description"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-sm-3">Price</label>
                        <div class="col-sm-9">
                            <input type="number" step="0.01" name="dftPrc" class="form-control" placeholder="Price" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="saveMenu" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for client-side validation -->
<script>
document.getElementById('addMenuForm').addEventListener('submit', function(event) {
    const requiredSelects = ['cat_id', 'qtyUnitCd', 'itemTyCd', 'taxTyCd', 'pkgUnitCd', 'orgnNatCd'];
    let errors = [];

    requiredSelects.forEach(function(name) {
        const select = document.querySelector(`select[name="${name}"]`);
        if (!select.value || select.value === '') {
            errors.push(name.replace(/([A-Z])/g, ' $1').replace(/^./, str => str.toUpperCase()));
        }
    });

    const price = document.querySelector('input[name="dftPrc"]').value;
    if (!price || parseFloat(price) <= 0) {
        errors.push('Price');
    }

    const menuName = document.querySelector('input[name="itemNm"]').value;
    if (!menuName || menuName.trim() === '') {
        errors.push('Menu Name');
    }

    if (errors.length > 0) {
        event.preventDefault();
        alert('Please fill in all required fields: ' + errors.join(', '));
    }
});
</script>
