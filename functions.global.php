<?php

function get_gallery_url() {
    if (!empty($_SERVER['REQUEST_SCHEME'])) {
        $g_protocol = $_SERVER['REQUEST_SCHEME'];
    } elseif (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $g_protocol = 'https';
    } else {
        $g_protocol = 'http';
    }
    $g_host = $_SERVER['HTTP_HOST'];
    $g_port = $_SERVER['SERVER_PORT'];
    $g_path = dirname($_SERVER['REQUEST_URI']);

// remove default ports
    if (($g_protocol == 'https' && $g_port == '443') ||
        ($g_protocol == 'http' && $g_port == '80')) {
        $g_port = '';
    } else {
        $g_port = ':' . $g_port;
    }

    if ($g_path[0] != '/') {
        $g_path = '/' . $g_path;
    }

    return $g_protocol . '://' . $g_host . $g_port . $g_path;
}