<?php 
include '../inc/conn.php';

$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

try {
    $db->beginTransaction();

    $order = $_POST['order'];
    $table_id = $_POST['table_id'];

    // 1️⃣ Delete
    $stmt1 = $db->prepare("DELETE FROM room_credits WHERE OrderCode = :order");
    $stmt1->execute([
        ':order' => $order
    ]);

    if ($stmt1->rowCount() === 0) {
        throw new Exception("Delete failed.");
    }

    // 2️⃣ Update
    $stmt2 = $db->prepare("UPDATE tbl_cmd SET room_client = :client WHERE OrderCode = :order");
    $stmt2->execute([
        ':client' => null,
        ':order' => $order
    ]);

    if ($stmt2->rowCount() === 0) {
        throw new Exception("Update failed.");
    }


    $db->commit();

    // echo "Success";
    echo "
        <script>alert('Client successfully removed'); 
        window.location='../reception/index?resto=gstInvce&resrv=" . $table_id . "&c=". $order ."'</script>
        ";

} catch (Exception $e) {
    $db->rollBack();
    echo "Rolled back: " . $e->getMessage();
}
?>
