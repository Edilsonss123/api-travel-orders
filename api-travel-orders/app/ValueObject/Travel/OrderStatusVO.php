<?php

namespace App\ValueObject\Travel;

enum OrderStatusVO: int
{
    case Requested  = 1;
    case Approved   = 2;
    case Canceled   = 3;
}
