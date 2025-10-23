cat > /tmp/upload_fixed.php <<'PHP'
<?php
$upload_dir = '/var/www/uploads_safe/';
$max_size   = 5 * 1024 * 1024;
$allowed_ext = ['jpg','jpeg','png','gif','pdf'];
$allowed_mime = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/gif'  => 'gif',
    'application/pdf' => 'pdf'
];
$html = '';
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['file'];
    if ($file['size'] > $max_size) {
        $html .= '<pre>File too large.</pre>';
    } else {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime  = $finfo->file($file['tmp_name']);
        if (! isset($allowed_mime[$mime])) {
            $html .= '<pre>Disallowed file type.</pre>';
        } else {
            $ext = $allowed_mime[$mime];
            $orig_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (! in_array($orig_ext, $allowed_ext, true) || ($orig_ext === 'jpeg' ? $ext !== 'jpg' : $ext !== $orig_ext)) {
                $html .= '<pre>Extension mismatch or not allowed.</pre>';
            } else {
                $safe_name = bin2hex(random_bytes(16)) . '.' . $ext;
                $destination = $upload_dir . $safe_name;
                if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
                    $html .= '<pre>Server error: cannot create upload directory.</pre>';
                } elseif (!move_uploaded_file($file['tmp_name'], $destination)) {
                    $html .= '<pre>Upload failed.</pre>';
                } else {
                    chmod($destination, 0644);
                    $html .= '<pre>File uploaded successfully.</pre>';
                }
            }
        }
    }
}
echo $html;
?>
PHP

sudo mv /tmp/upload_fixed.php vulnerabilities/file_upload/source/low.php
sudo chown www-data:www-data vulnerabilities/file_upload/source/low.php
sudo chmod 644 vulnerabilities/file_upload/source/low.php
