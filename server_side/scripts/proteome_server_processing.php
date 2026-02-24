<?php
// api/data.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // adjust for production
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/../../vendor/autoload.php';

use Saturio\DuckDB\DuckDB;

// Initialize DuckDB (in-memory or persistent file)
$duckdb = DuckDB::create(); // or new DuckDB('mydata.duckdb');

// Optional: enable HTTPFS for remote Parquet/CSV on S3/GCS/R2
// $duckdb->query("INSTALL httpfs; LOAD httpfs;");

// Your data source – use Parquet for best performance
// $dataSource = "read_parquet('data/*.parquet')"; // or 'large.csv' / read_csv_auto('large.csv')
$dataSource = "read_csv_auto('../../dataset/protein_info.txt', header=true, delim='\t')";

$request = json_decode(file_get_contents('php://input'), true) ?: $_GET;

// DataTables server-side params
$draw        = (int)($request['draw'] ?? 1);
$start       = (int)($request['start'] ?? 0);
$length      = (int)($request['length'] ?? 10);
$searchValue = trim($request['search']['value'] ?? '');
$order       = $request['order'] ?? [];
$columns     = $request['columns'] ?? [];

$columns_db = array(
	array( 'db' => 'uniprot_id', 'dt'=>0),
	array( 'db' => 'protein_name', 'dt'=>1),
	array( 'db' => 'gene_name', 'dt'=>2),
	array( 'db' => 'organism', 'dt'=>3),
	array( 'db' => 'sequence_length', 'dt'=>4)
);

// Build ORDER BY
$orderBy = '';
if (!empty($order)) {
    $orderClauses = [];
    foreach ($order as $o) {
        $colIdx = (int)$o['column'];
        $dir    = strtoupper($o['dir']) === 'DESC' ? 'DESC' : 'ASC';
        $colName = $columns[$colIdx]['data'] ?? $columns[$colIdx]['name'] ?? '';
        if ($colName) {
            $orderClauses[] = "\"$colName\" $dir";
        }
    }
    if ($orderClauses) {
        $orderBy = 'ORDER BY ' . implode(', ', $orderClauses);
    }
}

// Build WHERE (simple global search – improve with per-column filters if needed)
$where = 'WHERE 1=1';
$params = [];
if ($searchValue !== '') {
    $searchLike = '%' . $searchValue . '%';
    // Adjust columns to your actual schema
    $searchableCols = ['uniprot_id', 'protein_name', 'gene_name', 'organism', 
	// 'sequence_length', 
	// 'function'
	]; // ← customize!
    $conditions = [];
    foreach ($searchableCols as $col) {
        $conditions[] = "\"$col\" LIKE ?";
        $params[] = $searchLike;
    }
    if ($conditions) {
        $where .= ' AND (' . implode(' OR ', $conditions) . ')';
    }
}

// Count total (unfiltered)
$totalSql = "SELECT COUNT(*) FROM $dataSource";
$results = $duckdb->query($totalSql);
$recordsTotal = iterator_to_array($results->rows())[0][0] ?? 0;

// Count filtered
$filteredSql = "SELECT COUNT(*) FROM $dataSource $where";
$filteredStmt = $duckdb->preparedStatement($filteredSql);
foreach ($params as $i => $val) {
    $filteredStmt->bindParam($i + 1, $val);
}
$recordsFiltered = iterator_to_array($filteredStmt->execute()->rows())[0][0] ?? $recordsTotal;

// Main data query
$limitOffset = $length > 0 ? "LIMIT $length OFFSET $start" : '';
$sql = "SELECT ".implode(",", array_filter(array_map(fn($row) => $row['db'] ?? null, $columns_db), fn($v) => !empty($v)))." FROM $dataSource $where $orderBy $limitOffset";

$stmt = $duckdb->preparedStatement($sql);
foreach ($params as $i => $val) {
    $stmt->bindParam($i + 1, $val);
}

$result = $stmt->execute();

$rows = iterator_to_array($result->rows());

// DataTables response format
$response = [
    'draw'            => $draw,
    'recordsTotal'    => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data'            => $rows,
	'sql'			 => $sql,
];

echo json_encode($response);