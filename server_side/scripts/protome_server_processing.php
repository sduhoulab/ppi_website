<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

 $config = include( 'config.php' );

// DB table to use
$table = 'protome';

// Table's primary key
$primaryKey = 'ID';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes

$columns = array(
	array( 'db' => 'ID', 'dt'=>0),
	array( 'db' => 'Accession', 'dt'=>1),
	array( 'db' => 'Protein_Name', 'dt'=>2),
	array( 'db' => 'Gene', 'dt'=>3),
	array( 'db' => 'Sequence_Length', 'dt'=>4),
	array( 'db' => 'Interaction_Residues_Model', 'dt'=>5),
	array( 'db' => 'Interaction_Residues_PDB', 'dt'=>6),
	array( 'db' => 'Interaction_Residues_Alphafold', 'dt'=>7),
);

// SQL server connection information
$sql_details = array(
	'user' => $config->dbUser,
	'pass' => $config->dbPass,
	'db'   => $config->dbName,
	'host' => $config->dbHost
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require( 'ssp.class.php' );

echo json_encode(
	SSP::simple( $_GET, $sql_details, $table, $primaryKey, $columns )
);


