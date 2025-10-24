# PHP Favicon Generator

A lightweight, device-compatible favicon generator with Bootstrap-styled interface. Generate multiple favicon sizes from a single image upload.

## Features

- **Device Compatible**: Generates favicons for all major devices and platforms
- **Bootstrap Styled**: Modern, responsive UI using Bootstrap 5
- **Low File Structure**: Minimal files for easy deployment
- **Multiple Formats**: Creates 7 different favicon sizes including ICO format
- **ZIP Download**: Download all generated favicons at once
- **Ready-to-use HTML**: Provides copy-paste HTML code for your website

## File Structure

```
favicon-icon/
├── index.php          # Main application file
├── .htaccess          # Apache configuration
├── README.md          # Documentation
├── uploads/           # Temporary upload directory (auto-created)
└── favicons/          # Generated favicon output (auto-created)
```

## Requirements

- PHP 7.4 or higher
- GD Library (for image processing)
- Apache with mod_rewrite (optional, for clean URLs)
- ZipArchive extension (for ZIP downloads)

## Installation

1. **Clone or download** this repository to your web server:
   ```bash
   git clone <repository-url> favicon-icon
   cd favicon-icon
   ```

2. **Set proper permissions**:
   ```bash
   chmod 755 index.php
   chmod 644 .htaccess
   ```

3. **Ensure directories are writable** (they will be auto-created):
   ```bash
   # The script will auto-create these, but you can create them manually:
   mkdir uploads favicons
   chmod 755 uploads favicons
   ```

4. **Configure your web server** to point to the project directory.

5. **Access the application** via your web browser:
   ```
   http://your-domain.com/
   ```

## Usage

1. **Upload an Image**:
   - Click "Choose an Image" button
   - Select a PNG, JPG, or GIF file (max 5MB)
   - Recommended: Use a square image (512x512 or larger) for best results

2. **Generate Favicons**:
   - Click "Generate Favicons" button
   - Wait for processing to complete

3. **Download Files**:
   - Download individual favicon files
   - Or click "Download All (ZIP)" to get all files at once

4. **Implement on Your Website**:
   - Copy the generated HTML code
   - Paste it in the `<head>` section of your website
   - Upload the favicon files to your server

## Generated Favicon Sizes

| Filename | Size | Purpose |
|----------|------|---------|
| `favicon-16x16.png` | 16x16 | Standard browser favicon |
| `favicon-32x32.png` | 32x32 | Taskbar shortcut icon |
| `favicon-48x48.png` | 48x48 | Windows site icons |
| `apple-touch-icon.png` | 180x180 | iOS/macOS Safari |
| `android-chrome-192x192.png` | 192x192 | Android Chrome |
| `android-chrome-512x512.png` | 512x512 | Android Chrome (HD) |
| `favicon.ico` | 16x16 | Legacy browser support |

## HTML Implementation

After generating favicons, add this code to your website's `<head>` section:

```html
<link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
<link rel="icon" type="image/png" sizes="192x192" href="/favicons/android-chrome-192x192.png">
<link rel="icon" type="image/png" sizes="512x512" href="/favicons/android-chrome-512x512.png">
```

## Configuration

You can modify these settings in `index.php`:

```php
$upload_dir = 'uploads/';              // Upload directory
$output_dir = 'favicons/';             // Output directory
$max_file_size = 5 * 1024 * 1024;     // Max file size (5MB)
```

## Security Features

- File type validation (PNG, JPG, GIF only)
- File size limits (5MB default)
- Directory listing disabled via .htaccess
- XSS and clickjacking protection headers
- MIME type sniffing prevention

## Browser Compatibility

- Chrome/Edge (Desktop & Mobile)
- Firefox (Desktop & Mobile)
- Safari (Desktop & Mobile)
- Internet Explorer 11+
- Opera

## Troubleshooting

### "Upload failed" error
- Check PHP upload settings in php.ini
- Ensure `upload_max_filesize` and `post_max_size` are at least 5M
- Verify directory permissions

### GD Library not installed
```bash
# Ubuntu/Debian
sudo apt-get install php-gd

# CentOS/RHEL
sudo yum install php-gd

# Restart Apache
sudo service apache2 restart
```

### Favicons not generating
- Verify GD library is enabled: `php -m | grep gd`
- Check error logs: `tail -f /var/log/apache2/error.log`
- Ensure write permissions on uploads/ and favicons/ directories

### ZIP download not working
- Install ZipArchive extension
- Ubuntu/Debian: `sudo apt-get install php-zip`
- Restart web server

## License

This project is open source and available under the MIT License.

## Support

For issues, questions, or contributions, please create an issue on the repository.

## Credits

- Bootstrap 5 - UI Framework
- Bootstrap Icons - Icon set
- PHP GD Library - Image processing

---

**Made with ❤️ for web developers**
