<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case UNPAID = 'unpaid';
    case PAID = 'paid';
    case BANK_TRANSFER = 'bank_transfer';
    case PENDING_BANK_TRANSFER = 'pending_bank_transfer';
}