<?php

namespace App;

class Authenticate
{   
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

    public function emailExists($email)
    {
        $myDBObject = new Database();
        $selectStatement = 'SELECT email FROM `users` WHERE email = ?';
        $queryStmtObject = $myDBObject->conn->prepare($selectStatement);
        $queryStmtObject->bind_param('s', $email);
        $queryStmtObject->execute();
        $result = $queryStmtObject->get_result();
        return $result->num_rows > 0;
    }

    public function usernameExists($username)
    {
        $myDBObject = new Database();
        $selectStatement = 'SELECT username FROM `users` WHERE username = ?';
        $queryStmtObject = $myDBObject->conn->prepare($selectStatement);
        $queryStmtObject->bind_param('s', $username);
        $queryStmtObject->execute();
        $result = $queryStmtObject->get_result();
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
        if (isset($_POST['signUpBtn']))
        {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $confirmEmail  = $_POST['confirm_email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password']; 
            
            // Store the values in session variables
            // to retain them in case of validation errors
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;   
            $_SESSION['confirm_email'] = $confirmEmail;
            $_SESSION['password'] = $password;
            $_SESSION['confirm_password'] = $confirmPassword;

            if ($this->usernameExists($username))
            {
                \App\Alert::PrintMessage("Username already exists", 'Danger');
                return;
            }
            
            if ($email != $confirmEmail)
            {
 
                \App\Alert::PrintMessage("Email doesn't match", 'Danger');
                return;
            
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                \App\Alert::PrintMessage("Please enter a valid email", 'Danger');
                return;
            }
            // Then check if email exists
            if ($this->emailExists($email))
            {
                \App\Alert::PrintMessage("Email already exists", 'Danger');
                return;
            }
            
            // Add password validation
            if (!$this->validatePassword($password)) {
                return;
            }
            
            if ($password != $confirmPassword) {
                \App\Alert::PrintMessage("Password doesn't match", 'Danger');
                return;
            }
            
            $myDatabaseObj = new \App\Database();
            // Modified SQL to include signup_date column
            $insertStatement = "INSERT INTO `users` VALUES(NULL,?,?,?,NOW())";
            $queryObj = $myDatabaseObj->conn->prepare($insertStatement);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // Modified bind_param to include username, email, and hashed password
            $queryObj->bind_param('sss', $username, $email, $hashedPassword);
            $queryStatus = $queryObj->execute();
            
            if ($queryStatus) {
                header('location: SignIn.php?doneSignUp=1');
                \App\Alert::PrintMessage("Done creating your account", 'Success');
                exit();
            } else {
                \App\Alert::PrintMessage("Failed to create your account", 'Danger');
            }
        }
    }
    public function signIn()
    {
        if (isset($_POST['signInBtn'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];

            // Store the values in session variables
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password ; 

            if (empty($password) || empty($email)) {
                \App\Alert::PrintMessage("Email or Password is required.", 'Danger');
                return;
            }

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
            {
                    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            }    
            else 
            {  
                \App\Alert::PrintMessage("Please enter a valid email", 'Danger');
                return;
            }
         
            $myDatabaseObj = new \App\Database();
            $query = "SELECT * FROM `users` WHERE email = ?";
            $queryObj = $myDatabaseObj->conn->prepare($query);
            $queryObj->bind_param('s', $email);  
            $queryStatus = $queryObj->execute();
            
            if (!$queryStatus) {
                \App\Alert::PrintMessage('Something went wrong', 'Danger');
            } else {
                $resultObject = $queryObj->get_result();
                if ($resultObject->num_rows == 1) {
                    $rowArr = $resultObject->fetch_assoc();
                    if (password_verify($password, $rowArr["password"])) {
                        // Authenticated - Fix the session variable names
                        $_SESSION['userID'] = $rowArr["id"]; 
                        $_SESSION['userName'] = $rowArr["username"]; // Changed from name to username
                        \App\Alert::PrintMessage("Welcome Back, " . $rowArr['username'], 'Normal');
                        header('location: index.php');
                        exit();
                    } else {
                        \App\Alert::PrintMessage('Wrong password', 'Danger');
                    }
                } else {
                    \App\Alert::PrintMessage('Email is not valid', 'Danger');
                }
            }
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