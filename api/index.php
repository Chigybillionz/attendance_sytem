<?php
/**
 * Vercel API Handler for Laravel
 * 
 * This file routes API requests to the Laravel application
 * on Vercel's serverless platform.
 */

// Set up base path
$basePath = dirname(dirname(__FILE__));
chdir($basePath . '/backend');

// Set environment for production
putenv('APP_ENV=' . (getenv('APP_ENV') ?: 'production'));

// Load Laravel
require $basePath . '/backend/public/index.php';
