<?php

return [
    // Maksimal jumlah transaksi yang boleh dilakukan oleh satu kasir dalam satu hari.
    'daily_transaction_limit' => env('KASIR_DAILY_TRANSACTION_LIMIT', 20),
];
