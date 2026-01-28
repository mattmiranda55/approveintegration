<?php

namespace App\Enums;
enum UrlTypes: string
{
    case Product = 'product';
    case Cart = 'cart';
    case Gallery = 'gallery';
}