<?php
require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/PasswordGenerator.php';
require_once 'classes/PasswordStorage.php';

echo "Starting Password Manager Application Tests\n\n";

// Test 1: Database Connection
echo "Test 1: Database Connection\n";
try {
    $database = new Database();
    $conn = $database->getConnection();
    if ($conn) {
        echo "✓ Database connection successful\n";
    }
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: User Registration
echo "\nTest 2: User Registration\n";
$user = new User($conn);
$test_username = "testuser_" . time();
$test_password = "TestPassword123!";

try {
    if ($user->create($test_username, $test_password)) {
        echo "✓ User registration successful\n";
    } else {
        echo "✗ User registration failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ User registration failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: User Login
echo "\nTest 3: User Login\n";
try {
    if ($user->login($test_username, $test_password)) {
        echo "✓ User login successful\n";
    } else {
        echo "✗ User login failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ User login failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Password Generation
echo "\nTest 4: Password Generation\n";
$password_generator = new PasswordGenerator();
try {
    $generated_password = $password_generator->generate(12, 3, 3, 3, 3);
    if (strlen($generated_password) === 12) {
        echo "✓ Password generation successful: $generated_password\n";
    } else {
        echo "✗ Password generation failed: Invalid length\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Password generation failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Password Storage
echo "\nTest 5: Password Storage\n";
$password_storage = new PasswordStorage($conn, $user);
try {
    if ($password_storage->store("TestWebsite", $generated_password)) {
        echo "✓ Password storage successful\n";
    } else {
        echo "✗ Password storage failed\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Password storage failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 6: Password Retrieval
echo "\nTest 6: Password Retrieval\n";
try {
    $stored_passwords = $password_storage->getAll();
    if (!empty($stored_passwords)) {
        echo "✓ Password retrieval successful\n";
        echo "Retrieved password for: " . $stored_passwords[0]['website_name'] . "\n";
    } else {
        echo "✗ Password retrieval failed: No passwords found\n";
        exit(1);
    }
} catch (Exception $e) {
    echo "✗ Password retrieval failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nAll tests completed successfully!\n";
?> 