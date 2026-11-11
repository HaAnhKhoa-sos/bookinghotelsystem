<?php

function base_url($path = '')
{
    $directory = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    if (basename($directory) === 'admin') {
        $directory = dirname($directory);
    }

    $base = trim($directory, '/.');
    $prefix = $base === '' ? '/' : '/' . $base . '/';
    return $prefix . ltrim($path, '/');
}

function url($page = 'home', $params = array())
{
    return base_url('index.php?' . http_build_query(array_merge(array('page' => $page), $params)));
}

function admin_url($page = 'dashboard', $params = array())
{
    return base_url('admin/index.php?' . http_build_query(array_merge(array('page' => $page), $params)));
}

function asset($path)
{
    return base_url('assets/' . ltrim($path, '/'));
}
