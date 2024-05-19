<?php

function FormatOrderId($id)
{
    return str_pad($id, 4, '0', STR_PAD_LEFT);
}

function FormatOrderStatus($status)
{
    switch ($status) {
        case 1:
            return "Processing";
            break;
        case 2:
            return "Shipping";
            break;
        case 3:
            return "Delivered";
            break;
    }
}
