<?php

namespace App;

class FileUpload {
    private $file;
    private $allowedTypes = [];
    private $maxSize = 5242880; // 5MB by default
    
    public function __construct($file) {
        $this->file = $file;
    }
    
    public function setAllowedTypes($types) {
        $this->allowedTypes = $types;
    }
    
    public function setMaxSize($size) {
        $this->maxSize = $size;
    }
    
    public function save($directory) {
        // Check for upload errors
        if ($this->file['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception('Upload failed with error code ' . $this->file['error']);
        }
        
        // Check file size
        if ($this->file['size'] > $this->maxSize) {
            throw new \Exception('File size exceeds limit');
        }
        
        // Check file type
        if (!empty($this->allowedTypes) && !in_array($this->file['type'], $this->allowedTypes)) {
            throw new \Exception('File type not allowed');
        }
        
        // Create upload directory if it doesn't exist
        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/Socicuos/' . $directory;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($this->file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($this->file['tmp_name'], $filepath)) {
            throw new \Exception('Failed to move uploaded file');
        }
        
        return $directory . $filename;
    }
}