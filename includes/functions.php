<?php
// includes/functions.php

/**
 * Loads all settings from the database into an associative array.
 *
 * @param PDO $pdo The PDO database connection object.
 * @return array An associative array of settings.
 */
function load_settings(PDO $pdo): array
{
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        // PDO::FETCH_KEY_PAIR allows fetching data into a key-value array directly
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        // In case of an error, return an empty array to prevent site crash
        return [];
    }
}
