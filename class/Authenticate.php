<?php

namespace App;

class Authenticate
{   
    private $table = 'users';
    protected $conn;

    public function __construct() {
        $myDatabaseObj = new Database();
        $this->conn = $myDatabaseObj->conn;
    }

    private function validatePassword($password) {
        $errors = [];
        
        // Check minimum length
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        // Check for uppercase
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        // Check for lowercase
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        // Check for numbers
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        // Check for special characters
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        // If there are any errors, join them and return as string
        if (!empty($errors)) {
            \App\Alert::PrintMessage(implode(", ", $errors), 'Danger');
            return false;
        }
        
        return true;
    }
     
    public function checkUsernameAjax() {
        if (!isset($_POST['username'])) {
            throw new \Exception('Username is required');
        }

        $username = trim($_POST['username']);
        $exists = $this->usernameExists($username);
        
        header('Content-Type: application/json');
        echo json_encode([
            'exists' => $exists,
            'message' => $exists ? 'Username already taken' : 'Username available'
        ]);
    }

    public function validatePasswordAjax() {
        if (!isset($_POST['password'])) {
            throw new \Exception('Password is required');
        }

        $password = $_POST['password'];
        $errors = [];
        
        // Check minimum length
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        // Check for uppercase
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        // Check for lowercase
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        // Check for numbers
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        // Check for special characters
        if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'isValid' => empty($errors),
            'errors' => $errors
        ]);
    }

    public function emailExists($email)
    {
        $query = "SELECT email FROM {$this->table} WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    public function usernameExists($username)
    {
        $query = "SELECT username FROM {$this->table} WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            throw new \Exception("Prepare failed: " . $this->conn->error);
        }

        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }
     
    public function isAuth()
    {
        return isset($_SESSION['userID']); // bool
    }

    public function redirectIfAuth()
    {
        // Used in page SignIn & SignUp
        if ($this->isAuth())
            header('location: index.php');
    }

    public function redirectIfNotAuth()
    {
        // Used in page index.php
        // and other pages that require authentication

        if (!$this->isAuth())
            header('location: SignIn.php');
    }

    public function signUp()
    {
        if (!isset($_POST['signUpBtn'])) {
            return;
        }

        try {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $confirmEmail = $_POST['confirm_email'] ?? '';
            
            $_SESSION['form_data'] = [
                'username' => $username,
                'email' => $email,
                'confirm_email' => $confirmEmail
            ];

            // Never store passwords in session
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if ($this->usernameExists($username))
            {
                throw new \Exception("Username already exists");
            }
            
            if ($email !== $confirmEmail)
            {
                throw new \Exception("Email doesn't match");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                throw new \Exception("Please enter a valid email");
            }
            // Then check if email exists
            if ($this->emailExists($email))
            {
                throw new \Exception("Email already exists");
            }
            
            // Add password validation
            if (!$this->validatePassword($password)) {
                return;
            }
            
            if ($password !== $confirmPassword) {
                throw new \Exception("Password doesn't match");
            }
            $query = "INSERT INTO {$this->table} (username, email, password, profile_pic, bio) VALUES (?, ?, ?, 'default.jpg', NULL)";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Prepare failed: " . $this->conn->error);
            }
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param('sss', $username, $email, $hashedPassword);
            $queryStatus = $stmt->execute();
            
            if ($queryStatus) {
                header('location: SignIn.php?doneSignUp=1');
                \App\Alert::PrintMessage("Done creating your account", 'Success');
                // Clean up session form data after use
                unset($_SESSION['form_data']);
                exit();
            } else {
                throw new \Exception("Failed to create your account");
            }
        } catch (\Exception $e) {
            \App\Alert::PrintMessage($e->getMessage(), 'Danger');
        }
    }
    public function signIn()
    {
        if (!isset($_POST['signInBtn'])) {
            return;
        }        try {
            $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
            $password = isset($_POST['password']) ? $_POST['password'] : '';
            
            $_SESSION['form_data'] = ['email' => $email];

            if (empty($password) || empty($email)) {
                throw new \Exception("Email or Password is required.");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception("Please enter a valid email");
            }
         
            $query = "SELECT * FROM {$this->table} WHERE email = ?";
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                throw new \Exception("Something went wrong");
            }

            $stmt->bind_param('s', $email);  
            $queryStatus = $stmt->execute();
            
            if (!$queryStatus) {
                throw new \Exception('Something went wrong');
            }

            $resultObject = $stmt->get_result();
            if ($resultObject->num_rows == 1) {
                $rowArr = $resultObject->fetch_assoc();                
                if (password_verify($password, $rowArr["password"])) {
                    // Store user data in session
                    $_SESSION['userID'] = $rowArr["id"]; 
                    $_SESSION['userName'] = $rowArr["username"];
                    $_SESSION['profilePic'] = $rowArr["profile_pic"];
                    $_SESSION['bio'] = $rowArr["bio"];
                    $_SESSION['createdAt'] = $rowArr["created_at"];
                    
                    \App\Alert::PrintMessage("Welcome Back, " . $rowArr['username'], 'Normal');
                    header('location: index.php');
                    exit();
                } else {
                    throw new \Exception('Wrong password');
                }
            } else {
                throw new \Exception('Email is not valid');
            }
        } catch (\Exception $e) {
            \App\Alert::PrintMessage($e->getMessage(), 'Danger');
        }
    }
    public function logOut() {
        if(isset($_GET['logout'])) {
            session_unset();
            session_destroy();
            header("location: SignIn.php");
            exit();
        }
    }
  
}