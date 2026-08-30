<?php

namespace App\Enums;

enum WalletType: string
{
    case BANK = 'BANK';
    case E_WALLET = 'E_WALLET';
    case CASH = 'CASH';
    case SAVINGS = 'SAVINGS';
    case OTHER = 'OTHER';
}
