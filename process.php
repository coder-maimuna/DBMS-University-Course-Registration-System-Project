<?php
$conn = oci_connect("sys", "sys", "//localhost/XE", null, OCI_SYSDBA);

if (!$conn) {
    $m = oci_error();
    echo "<p>Connection failed: " . $m['message'] . "</p>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = $_POST["sqlInput"];

    $stmt = oci_parse($conn, $sql);
    $success = @oci_execute($stmt);

    if (!$success) {
        $e = oci_error($stmt);
        echo "<p><strong>Error:</strong> " . htmlentities($e['message']) . "</p>";
    } else {
        echo "<h2>SQL Output:</h2>";
        echo "<table border='1' cellpadding='5'>";

        $ncols = oci_num_fields($stmt);
        echo "<tr>";
        for ($i = 1; $i <= $ncols; ++$i) {
            $colname = oci_field_name($stmt, $i);
            echo "<th>" . htmlentities($colname) . "</th>";
        }
        echo "</tr>";

        while (($row = oci_fetch_array($stmt, OCI_ASSOC + OCI_RETURN_NULLS)) != false) {
            echo "<tr>";
            foreach ($row as $item) {
                echo "<td>" . ($item !== null ? htmlentities($item) : "&nbsp;") . "</td>";
            }
            echo "</tr>";
        }

        echo "</table>";
    }

    oci_free_statement($stmt);
    oci_close($conn);
}
?>
