<?php

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function money($value)
{
    return number_format((float) $value, 0, ',', '.') . 'đ';
}

function redirect($location)
{
    header('Location: ' . $location);
    exit;
}
