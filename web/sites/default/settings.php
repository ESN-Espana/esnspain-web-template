<?php


/**
 * Database configuration from environment variables.
 */
$databases['default']['default'] = [
    'database' => getenv('DB_NAME'),
    'username' => getenv('DB_USER'),
    'password' => getenv('DB_PASSWORD'),
    'host' => getenv('DB_HOST'),
    'port' => getenv('DB_PORT') ?: 3306,
    'driver' => 'mysql',
    'prefix' => '',
    'collation' => 'utf8mb4_general_ci',
];

/**
 * Drupal hash salt.
 */
 $hashSalt = getenv('HASH_SALT');

 if (!$hashSalt) {
     throw new RuntimeException('HASH_SALT environment variable is required.');
 }

 $settings['hash_salt'] = $hashSalt;

 /**
  + Configuration synchronization directory.
  */
$settings['config_sync_directory'] = '../config/sync';

/**
 * Trusted host patterns.
 *
 * Examples:
 * TRUSTED_HOST_PATTERNS=^localhost$,^127\.0\.0\.1$
 * TRUSTED_HOST_PATTERNS=^esnmalaga\.org$,^www\.esnmalaga\.org$
 */
if ($trustedHosts = getenv('TRUSTED_HOST_PATTERNS')) {
    $settings['trusted_host_patterns'] = array_map(
        'trim',
        explode(',', $trustedHosts)
    );
)
