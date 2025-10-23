cat > /tmp/low_fixed.php <<'PHP'
<?php
if (isset($_REQUEST['Submit'])) {
    $id_raw = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
    if ($id_raw === '' || !is_numeric($id_raw)) {
        $html .= "<pre>Invalid ID supplied.</pre>";
    } else {
        $id = (int) $id_raw;
        switch ($_DVWA['SQLI_DB']) {
            case 'MYSQL':
            default:
                $conn = $GLOBALS['___mysqli_ston'];
                if (!$conn) {
                    $html .= "<pre>Database connection error.</pre>";
                    break;
                }
                $sql = "SELECT first_name, last_name FROM users WHERE user_id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, 'i', $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $first, $last);
                    $found = false;
                    while (mysqli_stmt_fetch($stmt)) {
                        $found = true;
                        $safe_id    = htmlspecialchars((string)$id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safe_first = htmlspecialchars($first, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $safe_last  = htmlspecialchars($last, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                        $html .= "<pre>ID: {$safe_id}<br />First name: {$safe_first}<br />Last name: {$safe_last}</pre>";
                    }
                    if (! $found) {
                        $html .= "<pre>No user found for ID: " . htmlspecialchars((string)$id, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</pre>";
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $html .= "<pre>Query prepare failed.</pre>";
                }
                break;
        }
    }
}
?>
PHP

sudo mv /tmp/low_fixed.php vulnerabilities/sqli/source/low.php
sudo chown www-data:www-data vulnerabilities/sqli/source/low.php
sudo chmod 644 vulnerabilities/sqli/source/low.php
