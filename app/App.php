<?php

declare(strict_types=1);

function getTransactionFiles(string $dirPath): array // $dirPath is the path to the directory containing the transaction files
{
    $files = [];

    foreach (scandir($dirPath) as $file) { // scandir() returns an array of files and directories

        if (is_dir($file)) {
            continue;
        }
        $files[] = $dirPath . $file; // $files[] is an array of file paths
    }
    return $files;
}

function getTransactions(string $fileName, ?callable $transactionHandler): array // getTransactions() returns an array of transactions
{
    if (!file_exists($fileName)) {
        trigger_error('File "' . $fileName . '" does not exist.', E_USER_ERROR);
    }

    $file = fopen($fileName, 'r'); // open the file for reading

    fgetcsv($file); // skip the first line

    $transactions = [];

    while (($transaction = fgetcsv($file)) !== false) { // fgets() returns a line from the file
        if ($transactionHandler !== null) {
            $transaction = $transactionHandler($transaction);
        }
        $transactions[] = $transaction;
    }
    return $transactions; // $transactions is an array of transactions
}

function extractTransaction(array $transaction): array // extractTransaction() returns an array of transaction fields
{
    [$date, $checkNumber, $description, $amount] = $transaction;

    $amount = str_replace(['$', ','], '', $amount);


    return [
        'date' => $date,
        'check_number' => $checkNumber,
        'description' => $description,
        'amount' => $amount
    ];
}

function calculateTotals(array $transactions): array
{
    $totals = ['netTotal' => 0, 'totalIncome' => 0, 'totalExpense' => 0];

    foreach ($transactions as $transaction) {
        $totals['netTotal'] += $transaction['amount'];

        if ($transaction['amount'] > 0) {
            $totals['totalIncome'] += $transaction['amount'];
        } else {
            $totals['totalExpense'] += $transaction['amount'];
        }
    }

    return $totals;
}
