<?php

/**
 * Provides an interface to access PostgreSQL interactions. Code has been
 * adapted from example MySQL connection discussed by Edel Sherratt (eds@aber.ac.uk).
 *
 * @return PDO|null
 */
function get_db_handle() {
    $driver = 'pgsql';
    $host = 'db.dcs.aber.ac.uk';
    $databaseName = 'database_name';
    $dataSourceName = "$driver:host=$host;dbname=$databaseName";

    try {
        return new PDO($dataSourceName, 'username', 'password');

    } catch(PDOException $e) {
        echo "WARNING: Please alert alt28@aber.ac.uk regarding a database connection failing.\n";
        return null;
    }
}
?>