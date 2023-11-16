<?php
function CheckPasswordHash($password, $hash)
{
    if (password_verify($password, $hash)) {
        return true;
    } else {
        return false;
    }
}