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
$dataSource = "read_csv_auto('../../dataset/variant_all.csv', header=true, delim=',')";

$request = json_decode(file_get_contents('php://input'), true) ?: $_GET;

// DataTables server-side params
$draw        = (int)($request['draw'] ?? 1);
$start       = (int)($request['start'] ?? 0);
$length      = (int)($request['length'] ?? 10);
$searchValue = trim($request['search']['value'] ?? '');
$order       = $request['order'] ?? [];
$columns     = $request['columns'] ?? [];
//CHROM,POS,REF,ALT,Consequence,Gene,Feature,Gene_symbol,SWISSPROT,Protein_position,Amino_acids,canonical,clin_sig,SIFT_type,SIFT_score,Polyphen_type,Polyphen_score,3Dmapper_result,PPI
$columns_db = array(
	array( 'db' => 'CHROM', 'dt'=>1),
	array( 'db' => 'POS', 'dt'=>2),
	array( 'db' => 'REF', 'dt'=>3),
	array( 'db' => 'ALT', 'dt'=>4),
    array( 'db' => 'Consequence', 'dt'=>5),
    array( 'db' => 'Gene', 'dt'=>6),
    array( 'db' => 'Feature', 'dt'=>7),
    array( 'db' => 'Gene_symbol', 'dt'=>8),
    array( 'db' => 'SWISSPROT', 'dt'=>9),
    array( 'db' => 'Protein_position', 'dt'=>10),
    array( 'db' => 'Amino_acids', 'dt'=>11),
    array( 'db' => 'canonical', 'dt'=>12),
    array( 'db' => 'clin_sig', 'dt'=>13),
    array( 'db' => 'SIFT_type', 'dt'=>14),
    array( 'db' => 'SIFT_score', 'dt'=>15),
    array( 'db' => 'Polyphen_type', 'dt'=>16),
    array( 'db' => 'Polyphen_score', 'dt'=>17),
    array( 'db' => '3Dmapper_result', 'dt'=>18),
    array( 'db' => 'PPI', 'dt'=>19),
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
    $searchLike = '' . $searchValue . '%';
    // Adjust columns to your actual schema
    $searchableCols = ['uniprot_id', 
    //'protein_name', 'gene_name', 'organism', 
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