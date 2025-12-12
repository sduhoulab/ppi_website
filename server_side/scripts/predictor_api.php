<?php

$config = include( 'config.php' );

$uniprotId = $_POST['uniprotId'] ?? '';
$sequence = $_POST['proteinSeq'] ?? '';
$cutoff = $_POST['cutoff'] ?? 0.5;

// Try to match a string after ">"
if (preg_match('/^>\s*([A-Za-z0-9_]+)/', $sequence, $matches)) {
    $uniprotId_ = $matches[1];
} else {
    $uniprotId_ = "";  // or null if no header found
}

// Remove FASTA header line (anything starting with ">" up to the first newline)
$sequence = preg_replace('/^>.*[\s\n]/', '', $sequence);

// Remove non-word characters (equivalent to Python's [\W])
$sequence = preg_replace('/[\W]/', '', $sequence);

if (empty($uniprotId) && !empty($uniprotId_)) {
    $uniprotId = $uniprotId_;
}

$db = new PDO(
    "mysql:host={$config->dbHost};dbname={$config->dbName};charset={$config->dbCharset};port={$config->dbPort}",
    $config->dbUser,
    $config->dbPass,
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
);

$sql = "select * from proteinSeqPredict where (uniprotId = :uniprotId or proteinSeq = :sequence)  order by createdAt desc limit 1";

$stmt = $db->prepare($sql);
$stmt->bindParam(':uniprotId', $uniprotId);
$stmt->bindParam(':sequence', $sequence);
$stmt->execute();
$result = $stmt->fetch();
if ($result) {
    $result['predictResult'] = json_decode($result['predictResult'], true);
    echo json_encode([
        'status' => 'success',
        'data' => $result
    ]);
} else {
    if (empty($sequence)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Protein sequence is required'
        ]);
        exit;
    }

    $url = "http://api.protatlas.org/predictor";

    // Prepare data
    $data = [
        "sequence" => $sequence,
    ];

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Execute request
    $response = curl_exec($ch);

    // Close cURL
    curl_close($ch);

    // Check for errors
    if (curl_errno($ch)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Request Error: ' . curl_error($ch)
        ]);
    } else {
        
        // Decode response JSON
        $result = json_decode($response, true);

        // Extract values
        $predictResult = $result['prediction'] ?? null;
        
        if($predictResult) {
            $predictResultJSON = json_encode($predictResult);
            // Store in database
            $insertSql = "INSERT INTO proteinSeqPredict (uniprotId, proteinSeq, predictResult) VALUES (:uniprotId, :proteinSeq, :predictResult)";
            $insertStmt = $db->prepare($insertSql);
            $insertStmt->bindParam(':uniprotId', $uniprotId);
            $insertStmt->bindParam(':proteinSeq', $sequence);
            $insertStmt->bindParam(':predictResult', $predictResultJSON);
            $insertStmt->execute();

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'uniprotId' => $uniprotId,
                    'proteinSeq' => $sequence,
                    'predictResult' => $predictResult,
                    'cutoff' => $cutoff
                ]
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Prediction failed'
            ]);
        }
    }
}



