# PHP Xdebug Setup Instructions

## Prerequisites
- ✅ PHP 8.3.7 installed at `C:\Program Files\php-8.3.7-Win32-vs16-x64`
- ✅ VS Code with PHP Debug extension installed
- ✅ Configuration files created in your project

## Manual Setup Required (Administrator Privileges Needed)

### Step 1: Install Xdebug Extension

1. **Open PowerShell as Administrator** (Right-click PowerShell → "Run as administrator")

2. **Copy the Xdebug DLL to PHP extensions directory:**
   ```powershell
   Copy-Item "temp_xdebug\php_xdebug.dll" "C:\Program Files\php-8.3.7-Win32-vs16-x64\ext\"
   ```

3. **Verify the copy was successful:**
   ```powershell
   dir "C:\Program Files\php-8.3.7-Win32-vs16-x64\ext\php_xdebug.dll"
   ```

### Step 2: Alternative Method (If admin access is not available)

If you can't get administrator access, you can run PHP with a custom configuration:

1. **Start PHP server with custom ini file:**
   ```powershell
   php -c php.ini -S localhost:8080
   ```

2. **Or set environment variable:**
   ```powershell
   $env:PHPRC = "C:\Users\Uday Kiran\Desktop\ieee yesist 12"
   php -S localhost:8080
   ```

## Testing Xdebug Installation

### Method 1: Check if Xdebug is loaded
```powershell
php -c php.ini -m | findstr xdebug
```

### Method 2: Create a PHP info file
```php
<?php phpinfo(); ?>
```
Look for "xdebug" section in the output.

## How to Debug in VS Code

### 1. Set Breakpoints
- Click in the left margin (gutter) of your PHP file where you want to pause execution
- Red dots will appear indicating breakpoints

### 2. Start Debugging Session
- Press `F5` or go to Debug → Start Debugging
- Choose "Listen for Xdebug" from the configuration dropdown

### 3. Trigger PHP Script
- Start your PHP server: `php -c php.ini -S localhost:8080`
- Visit your PHP script in browser: `http://localhost:8080/debug_test.php`
- Or run directly: `php -c php.ini debug_test.php`

### 4. Debug Features Available
- **Step Over** (F10) - Execute current line
- **Step Into** (F11) - Enter function calls
- **Step Out** (Shift+F11) - Exit current function
- **Continue** (F5) - Resume execution
- **Variable Inspection** - Hover over variables or check Variables panel
- **Call Stack** - See function call hierarchy
- **Watch Expressions** - Monitor specific variables/expressions

## Debug Configurations Available

1. **Listen for Xdebug** - Wait for incoming debug connections
2. **Launch currently open script** - Debug the currently open PHP file
3. **Debug PHP Server** - Start PHP server and debug web requests

## Troubleshooting

### If Xdebug doesn't work:

1. **Check Xdebug is loaded:**
   ```powershell
   php -c php.ini -m | findstr xdebug
   ```

2. **Check Xdebug configuration:**
   ```powershell
   php -c php.ini -i | findstr xdebug
   ```

3. **Check the log file:**
   Look for `xdebug.log` in your project directory

4. **Verify port 9003 is not blocked:**
   ```powershell
   netstat -an | findstr 9003
   ```

5. **Test with simple script:**
   ```php
   <?php
   var_dump("Debug test");
   echo "If you see this, PHP is working!";
   ?>
   ```

## Files Created

- ✅ `php.ini` - PHP configuration with Xdebug settings
- ✅ `.vscode/launch.json` - Debug configurations
- ✅ `.vscode/settings.json` - VS Code PHP settings
- ✅ `debug_test.php` - Test file with debugging examples
- ✅ `temp_xdebug/php_xdebug.dll` - Xdebug extension file

## Next Steps

1. Run the administrator command to copy Xdebug DLL
2. Test with `debug_test.php`
3. Set breakpoints in your `test_form_api.php` file
4. Debug your actual application code!

Happy Debugging! 🐛🔍