<?php

namespace App;

class Authenticate
{   


    public function isValidEmail($email)
    {

        $pattern = '/^[^\s@]+@[^\s@]+\.[^\s@]+$/';
        return preg_match($pattern, $email) === 1;
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
        if (isset($_POST['signUpBtn'])) {
            $username = $_POST['username'];
            $email = $_POST['email'];


            if ($this->usernameExists($username)) {
                \App\Alert::PrintMessage("Username already exists", 'Danger');
                return;
            }
            

            if (!$this->isValidEmail($email)) {
                \App\Alert::PrintMessage("Please enter a valid email address", 'Danger');
                return;
            }

            // Then check if email exists
            if ($this->emailExists($email)) {
                \App\Alert::PrintMessage("Email already exists", 'Danger');
                return;
            }
            
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm_password'];
            if ($password != $confirmPassword)
                \App\Alert::PrintMessage("Confirm Password not matched", 'Danger');
            
            $myDatabaseObj = new \App\Database();
            $insertStatement = "INSERT INTO `users` VALUES(NULL,?,?,?)"; // Sql injection
            $queryObj = $myDatabaseObj->conn->prepare($insertStatement);
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $queryObj->bind_param('sss', $username, $email, $hashedPassword);
            $queryStatus = $queryObj->execute();
            if ($queryStatus) {
                
                header('location: SignIn.php?doneSignUp=1');
                Alert::PrintMessage("Done creating your account", 'Success');
            }

            else {
                Alert::PrintMessage("Failed to create your account", 'Danger');
            }
        }
    }
    public function signIn()
    {
        if (isset($_POST['signInBtn'])) {
            $password = $_POST['password'];
            $email = $_POST['email'];

            if (empty($password) || empty($email)) {
                \App\Alert::PrintMessage("Email or Password is required.", 'Danger');
                return;
            }

            if (!$this->isValidEmail($email)) {
                \App\Alert::PrintMessage("Please enter a valid email address", 'Danger');
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