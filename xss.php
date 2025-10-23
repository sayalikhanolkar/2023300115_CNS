cat > /tmp/xss_fixed.php <<'PHP'
<?php
session_start();
$html = '';
if (isset($_POST['submit_comment'])) {
    $raw_comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    if ($raw_comment === '' || mb_strlen($raw_comment, 'UTF-8') > 2000) {
        $html .= "<pre>Invalid comment.</pre>";
    } else {
        $clean_comment = preg_replace('/[[:cntrl:]]/u', '', $raw_comment);
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, comment) VALUES (:uid, :c)");
        $stmt->execute(['uid' => $userId, 'c' => $clean_comment]);
        $html .= "<pre>Comment saved.</pre>";
    }
}
$comment_from_db = isset($comment_from_db) ? $comment_from_db : '';
if ($comment_from_db !== '') {
    echo '<div class="comment">' . htmlspecialchars($comment_from_db, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</div>';
}
?>
PHP

sudo mv /tmp/xss_fixed.php vulnerabilities/xss/source/low.php
sudo chown www-data:www-data vulnerabilities/xss/source/low.php
sudo chmod 644 vulnerabilities/xss/source/low.php
