<?php
/**
 * PHP Favicon Generator
 * Device-compatible favicon generator with Bootstrap UI
 */

// Configuration
$upload_dir = 'uploads/';
$output_dir = 'favicons/';
$max_file_size = 5 * 1024 * 1024; // 5MB

// Create directories if they don't exist
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}
if (!file_exists($output_dir)) {
    mkdir($output_dir, 0755, true);
}

// Favicon sizes for different devices
$favicon_sizes = [
    'favicon-16x16.png' => 16,
    'favicon-32x32.png' => 32,
    'favicon-48x48.png' => 48,
    'apple-touch-icon.png' => 180,
    'android-chrome-192x192.png' => 192,
    'android-chrome-512x512.png' => 512,
];

$message = '';
$error = '';
$generated_files = [];

// Process upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['favicon_image'])) {
    $file = $_FILES['favicon_image'];

    // Validate upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'Upload failed. Please try again.';
    } elseif ($file['size'] > $max_file_size) {
        $error = 'File size exceeds 5MB limit.';
    } elseif (!in_array($file['type'], ['image/png', 'image/jpeg', 'image/jpg', 'image/gif'])) {
        $error = 'Invalid file type. Please upload PNG, JPG, or GIF.';
    } else {
        // Process image
        $source_image = null;

        switch ($file['type']) {
            case 'image/png':
                $source_image = imagecreatefrompng($file['tmp_name']);
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $source_image = imagecreatefromjpeg($file['tmp_name']);
                break;
            case 'image/gif':
                $source_image = imagecreatefromgif($file['tmp_name']);
                break;
        }

        if ($source_image) {
            $source_width = imagesx($source_image);
            $source_height = imagesy($source_image);

            // Generate favicons in different sizes
            foreach ($favicon_sizes as $filename => $size) {
                $favicon = imagecreatetruecolor($size, $size);

                // Preserve transparency
                imagealphablending($favicon, false);
                imagesavealpha($favicon, true);
                $transparent = imagecolorallocatealpha($favicon, 255, 255, 255, 127);
                imagefilledrectangle($favicon, 0, 0, $size, $size, $transparent);
                imagealphablending($favicon, true);

                // Resize image
                imagecopyresampled(
                    $favicon, $source_image,
                    0, 0, 0, 0,
                    $size, $size, $source_width, $source_height
                );

                // Save PNG
                $output_path = $output_dir . $filename;
                imagepng($favicon, $output_path, 9);
                imagedestroy($favicon);

                $generated_files[] = $filename;
            }

            // Generate favicon.ico (multi-resolution ICO file)
            $ico_path = $output_dir . 'favicon.ico';
            $favicon_16 = imagecreatetruecolor(16, 16);
            imagealphablending($favicon_16, false);
            imagesavealpha($favicon_16, true);
            $transparent = imagecolorallocatealpha($favicon_16, 255, 255, 255, 127);
            imagefilledrectangle($favicon_16, 0, 0, 16, 16, $transparent);
            imagealphablending($favicon_16, true);
            imagecopyresampled($favicon_16, $source_image, 0, 0, 0, 0, 16, 16, $source_width, $source_height);

            // Convert to ICO (simplified - just save as PNG for now, proper ICO would need a library)
            imagepng($favicon_16, $ico_path);
            imagedestroy($favicon_16);
            $generated_files[] = 'favicon.ico';

            imagedestroy($source_image);

            $message = 'Favicons generated successfully!';
        } else {
            $error = 'Failed to process image.';
        }
    }
}

// Get existing generated files
$existing_files = [];
if (is_dir($output_dir)) {
    $files = scandir($output_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..' && is_file($output_dir . $file)) {
            $existing_files[] = $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Favicon Generator</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .main-card {
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            border: none;
            border-radius: 15px;
        }
        .upload-area {
            border: 3px dashed #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        .upload-area:hover {
            border-color: #764ba2;
            background: #e9ecef;
        }
        .upload-icon {
            font-size: 4rem;
            color: #667eea;
        }
        .file-list {
            max-height: 400px;
            overflow-y: auto;
        }
        .file-item {
            transition: all 0.2s;
        }
        .file-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        .header-icon {
            font-size: 3rem;
            color: #667eea;
        }
        .preview-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid #e9ecef;
        }
        .browser-mockup {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .browser-header {
            background: #e9ecef;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .browser-dots {
            display: flex;
            gap: 5px;
        }
        .browser-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }
        .dot-red { background: #ff5f56; }
        .dot-yellow { background: #ffbd2e; }
        .dot-green { background: #27c93f; }
        .browser-tab {
            background: white;
            padding: 6px 12px;
            border-radius: 6px 6px 0 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            margin-left: 8px;
        }
        .browser-tab img {
            width: 16px;
            height: 16px;
        }
        .browser-content {
            padding: 20px;
            min-height: 60px;
            background: white;
        }
        .mobile-mockup {
            background: #1c1c1e;
            border-radius: 30px;
            padding: 15px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            max-width: 280px;
            margin: 0 auto;
        }
        .mobile-screen {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            padding: 20px;
            min-height: 320px;
        }
        .mobile-time {
            color: white;
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
        }
        .mobile-icons {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .mobile-icon {
            text-align: center;
        }
        .mobile-icon img {
            width: 60px;
            height: 60px;
            border-radius: 13px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.3);
            margin-bottom: 5px;
        }
        .mobile-icon-label {
            color: white;
            font-size: 10px;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }
        .desktop-mockup {
            background: #2c3e50;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .desktop-icon {
            text-align: center;
            display: inline-block;
            margin: 10px 15px;
        }
        .desktop-icon img {
            width: 64px;
            height: 64px;
            border-radius: 4px;
            margin-bottom: 5px;
        }
        .desktop-icon-label {
            color: white;
            font-size: 12px;
            background: rgba(0,0,0,0.5);
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Header -->
                <div class="text-center mb-4">
                    <i class="bi bi-grid-3x3-gap-fill header-icon"></i>
                    <h1 class="text-white mt-3 fw-bold">Favicon Generator</h1>
                    <p class="text-white-50">Generate device-compatible favicons instantly</p>
                </div>

                <!-- Main Card -->
                <div class="card main-card">
                    <div class="card-body p-4">

                        <!-- Alerts -->
                        <?php if ($message): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($message); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Upload Form -->
                        <div class="row">
                            <div class="col-lg-6">
                                <h4 class="mb-3"><i class="bi bi-cloud-upload me-2"></i>Upload Image</h4>
                                <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                    <div class="upload-area mb-3">
                                        <i class="bi bi-image upload-icon"></i>
                                        <h5 class="mt-3">Choose an Image</h5>
                                        <p class="text-muted">PNG, JPG, or GIF (Max 5MB)</p>
                                        <input type="file" name="favicon_image" id="favicon_image"
                                               class="form-control mt-3" accept="image/png,image/jpeg,image/jpg,image/gif" required>
                                    </div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="bi bi-magic me-2"></i>Generate Favicons
                                        </button>
                                    </div>
                                </form>

                                <!-- Info -->
                                <div class="mt-4">
                                    <h6 class="fw-bold">Generated Sizes:</h6>
                                    <ul class="small text-muted">
                                        <li>16x16 - Standard favicon</li>
                                        <li>32x32 - Taskbar shortcut icon</li>
                                        <li>48x48 - Windows site icons</li>
                                        <li>180x180 - Apple touch icon</li>
                                        <li>192x192 - Android Chrome</li>
                                        <li>512x512 - Android Chrome HD</li>
                                        <li>favicon.ico - Legacy browsers</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Generated Files -->
                            <div class="col-lg-6">
                                <h4 class="mb-3"><i class="bi bi-download me-2"></i>Download Files</h4>

                                <?php if (!empty($existing_files)): ?>
                                    <div class="file-list">
                                        <?php foreach ($existing_files as $file): ?>
                                            <div class="file-item d-flex justify-content-between align-items-center p-3 border-bottom">
                                                <div>
                                                    <i class="bi bi-file-earmark-image text-primary me-2"></i>
                                                    <strong><?php echo htmlspecialchars($file); ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php echo number_format(filesize($output_dir . $file) / 1024, 2); ?> KB
                                                    </small>
                                                </div>
                                                <a href="<?php echo $output_dir . $file; ?>" download
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>

                                    <!-- Download All Button -->
                                    <div class="mt-3 d-grid">
                                        <a href="?download_all=1" class="btn btn-success">
                                            <i class="bi bi-folder-zip me-2"></i>Download All (ZIP)
                                        </a>
                                    </div>

                                    <!-- HTML Code -->
                                    <div class="mt-4">
                                        <h6 class="fw-bold">HTML Code:</h6>
                                        <div class="bg-light p-3 rounded">
                                            <code style="font-size: 0.85rem; display: block; white-space: pre-wrap;">
&lt;link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png"&gt;
&lt;link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png"&gt;
&lt;link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png"&gt;
&lt;link rel="icon" type="image/png" sizes="192x192" href="/favicons/android-chrome-192x192.png"&gt;
&lt;link rel="icon" type="image/png" sizes="512x512" href="/favicons/android-chrome-512x512.png"&gt;
                                            </code>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center p-5 text-muted">
                                        <i class="bi bi-folder2-open" style="font-size: 4rem;"></i>
                                        <p class="mt-3">No favicons generated yet.<br>Upload an image to get started!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <?php if (!empty($existing_files)): ?>
                <div class="card main-card mt-4">
                    <div class="card-body p-4">
                        <h4 class="mb-4"><i class="bi bi-eye me-2"></i>Live Preview</h4>
                        <p class="text-muted mb-4">See how your favicon will appear across different devices and platforms</p>

                        <div class="row">
                            <!-- Browser Preview -->
                            <div class="col-lg-4 mb-4">
                                <div class="preview-card">
                                    <h6 class="fw-bold mb-3 text-center">
                                        <i class="bi bi-browser-chrome text-primary me-2"></i>Browser Tab
                                    </h6>
                                    <div class="browser-mockup">
                                        <div class="browser-header">
                                            <div class="browser-dots">
                                                <div class="browser-dot dot-red"></div>
                                                <div class="browser-dot dot-yellow"></div>
                                                <div class="browser-dot dot-green"></div>
                                            </div>
                                            <div class="browser-tab">
                                                <img src="<?php echo $output_dir; ?>favicon-16x16.png" alt="Favicon">
                                                <span>My Website</span>
                                            </div>
                                        </div>
                                        <div class="browser-content">
                                            <small class="text-muted">Your favicon appears in the browser tab</small>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="badge bg-info">16x16 / 32x32 px</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Mobile Preview -->
                            <div class="col-lg-4 mb-4">
                                <div class="preview-card">
                                    <h6 class="fw-bold mb-3 text-center">
                                        <i class="bi bi-phone text-success me-2"></i>Mobile Home Screen
                                    </h6>
                                    <div class="mobile-mockup">
                                        <div class="mobile-screen">
                                            <div class="mobile-time">9:41 AM</div>
                                            <div class="mobile-icons">
                                                <div class="mobile-icon">
                                                    <img src="<?php echo $output_dir; ?>apple-touch-icon.png" alt="App Icon">
                                                    <div class="mobile-icon-label">My Site</div>
                                                </div>
                                                <div class="mobile-icon">
                                                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 13px;"></div>
                                                    <div class="mobile-icon-label">App</div>
                                                </div>
                                                <div class="mobile-icon">
                                                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 13px;"></div>
                                                    <div class="mobile-icon-label">App</div>
                                                </div>
                                                <div class="mobile-icon">
                                                    <div style="width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 13px;"></div>
                                                    <div class="mobile-icon-label">App</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="badge bg-success">180x180 / 192x192 px</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Desktop Preview -->
                            <div class="col-lg-4 mb-4">
                                <div class="preview-card">
                                    <h6 class="fw-bold mb-3 text-center">
                                        <i class="bi bi-display text-warning me-2"></i>Desktop Shortcut
                                    </h6>
                                    <div class="desktop-mockup">
                                        <div class="desktop-icon">
                                            <img src="<?php echo $output_dir; ?>favicon-48x48.png" alt="Desktop Icon">
                                            <div class="desktop-icon-label">My Website</div>
                                        </div>
                                        <div class="desktop-icon">
                                            <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                            <div class="desktop-icon-label">Folder</div>
                                        </div>
                                        <div class="desktop-icon">
                                            <div style="width: 64px; height: 64px; background: rgba(255,255,255,0.1); border-radius: 4px;"></div>
                                            <div class="desktop-icon-label">File</div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="badge bg-warning">48x48 px</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Preview Info -->
                        <div class="alert alert-info mt-3">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Preview Note:</strong> These are simulated previews. Actual appearance may vary slightly depending on the device, browser, and operating system.
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Footer -->
                <div class="text-center mt-4 text-white-50">
                    <small>PHP Favicon Generator &copy; <?php echo date('Y'); ?></small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // File input preview
        document.getElementById('favicon_image').addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const fileName = this.files[0].name;
                const fileSize = (this.files[0].size / 1024).toFixed(2);
                console.log('Selected: ' + fileName + ' (' + fileSize + ' KB)');
            }
        });
    </script>
</body>
</html>

<?php
// Handle download all as ZIP
if (isset($_GET['download_all']) && !empty($existing_files)) {
    $zip_filename = 'favicons_' . date('YmdHis') . '.zip';
    $zip_path = $output_dir . $zip_filename;

    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
        foreach ($existing_files as $file) {
            $zip->addFile($output_dir . $file, $file);
        }
        $zip->close();

        // Download ZIP
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zip_filename . '"');
        header('Content-Length: ' . filesize($zip_path));
        readfile($zip_path);
        unlink($zip_path); // Delete temporary ZIP
        exit;
    }
}
?>
