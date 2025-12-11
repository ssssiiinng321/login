<?php
// Vercel Serverless environments are read-only except for /tmp.
// We must store sessions there for them to persist (briefly).
$savePath = '/tmp';
if (!file_exists($savePath)) {
    mkdir($savePath, 0777, true);
}
session_save_path($savePath);
session_start();
?>
