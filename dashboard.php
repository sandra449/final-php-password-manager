<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['encryption_key'])) {
    header("Location: login.php");
    exit();
}

require_once 'config/database.php';
require_once 'classes/User.php';
require_once 'classes/PasswordStorage.php';
require_once 'classes/PasswordGenerator.php';

$database = new Database();
$db = $database->getConnection();

$storage = new PasswordStorage($db, $_SESSION['user_id'], $_SESSION['encryption_key']);
$generator = new PasswordGenerator();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $website_name = $_POST['website_name'];
        
        // Get password preferences
        $length = isset($_POST['length']) ? (int)$_POST['length'] : 12;
        $lowercase = isset($_POST['lowercase']) ? (int)$_POST['lowercase'] : 3;
        $uppercase = isset($_POST['uppercase']) ? (int)$_POST['uppercase'] : 3;
        $numbers = isset($_POST['numbers']) ? (int)$_POST['numbers'] : 3;
        $special = isset($_POST['special']) ? (int)$_POST['special'] : 3;
        
        // Generate password with preferences
        $password = $generator->generate($length, $lowercase, $uppercase, $numbers, $special);
        
        if ($storage->store($website_name, $password)) {
            $message = "Password generated and stored successfully!";
        } else {
            $error = "Failed to store password";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

try {
    $stored_passwords = $storage->getAll();
} catch (Exception $e) {
    $error = $e->getMessage();
    $stored_passwords = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Manager - Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3>Password Manager</h3>
                        <a href="logout.php" class="btn btn-danger">Logout</a>
                    </div>
                    <div class="card-body">
                        <h5>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h5>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($message); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" class="mb-4">
                            <div class="form-group">
                                <label>Website Name</label>
                                <input type="text" name="website_name" class="form-control" required>
                            </div>
                            
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5 class="mb-0">Password Preferences</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Password Length</label>
                                                <input type="number" name="length" class="form-control" value="12" min="8" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Lowercase</label>
                                                <input type="number" name="lowercase" class="form-control" value="3" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Uppercase</label>
                                                <input type="number" name="uppercase" class="form-control" value="3" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Numbers</label>
                                                <input type="number" name="numbers" class="form-control" value="3" min="0" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label>Special</label>
                                                <input type="number" name="special" class="form-control" value="3" min="0" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Generate & Store Password</button>
                        </form>
                        
                        <h5>Stored Passwords</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Website</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stored_passwords as $stored): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($stored['website_name']); ?></td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="password" class="form-control" value="<?php echo htmlspecialchars($stored['password']); ?>" readonly>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary toggle-password" type="button">Show</button>
                                                        <button class="btn btn-outline-secondary copy-password" type="button">Copy</button>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.previousElementSibling;
                if (input.type === 'password') {
                    input.type = 'text';
                    this.textContent = 'Hide';
                } else {
                    input.type = 'password';
                    this.textContent = 'Show';
                }
            });
        });

        // Copy password to clipboard
        document.querySelectorAll('.copy-password').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.previousElementSibling;
                const originalType = input.type;
                input.type = 'text';
                input.select();
                document.execCommand('copy');
                input.type = originalType;
                
                const originalText = this.textContent;
                this.textContent = 'Copied!';
                setTimeout(() => {
                    this.textContent = originalText;
                }, 1000);
            });
        });
    });
    </script>
</body>
</html> 