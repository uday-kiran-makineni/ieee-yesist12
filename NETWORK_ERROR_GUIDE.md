# Network Error Troubleshooting Guide

## Quick Fix Steps

### Step 1: Run the Debug Tool
1. Double-click `debug_network.bat`
2. This will start the server and open debugging tools
3. Use the "Signup Debug Test" page first

### Step 2: Check Common Issues

#### A. Server Not Running
- Make sure the PHP server is running on localhost:8000
- You should see server output in the command window

#### B. Wrong File Paths
- The signup form is trying to call `auth_handler.php`
- Make sure you're accessing the page from `http://localhost:8000/login-system/index.html`
- NOT from file:// URLs

#### C. Browser Security
- Modern browsers block requests from file:// to http://
- Always use the server URL: `http://localhost:8000/login-system/`

### Step 3: Test the Process

1. **Open**: http://localhost:8000/login-system/signup_debug.html
2. **Fill the form** with the pre-filled test data
3. **Click "Test Signup"**
4. **Check the debug information** that appears

### Step 4: Common Error Solutions

#### "Network Error" Message
- **Cause**: JavaScript fetch() failed
- **Solutions**:
  - Restart the PHP server
  - Check if you're using http://localhost:8000 (not file://)
  - Disable browser ad blockers temporarily
  - Try a different browser (Chrome, Firefox, Edge)

#### "Connection Refused"
- **Cause**: Server not running
- **Solution**: Run `debug_network.bat` or `start_server.bat`

#### "CORS Error"
- **Cause**: Cross-origin request blocked
- **Solution**: Access via server URL, not file system

#### "500 Internal Server Error"
- **Cause**: PHP error in auth_handler.php
- **Solution**: Check if database is connected properly

### Step 5: Manual Test

If automated tests fail, try this manual test:

1. Open browser developer tools (F12)
2. Go to Console tab
3. Navigate to: http://localhost:8000/login-system/index.html
4. Try to sign up
5. Check console for specific error messages

### Step 6: Log File Check

The auth_handler.php now logs debug information. Check for:
- PHP error logs
- Server console output
- Any specific error messages

## Files Created for Debugging

1. `signup_debug.html` - Simplified signup test
2. `network_debug.html` - Network connectivity tests  
3. `test_endpoint.php` - Simple test endpoint
4. `debug_network.bat` - Automated server start with debugging

## If All Else Fails

1. Try using the test credentials in the simplified debug form
2. Check if the database connection is working
3. Verify PHP extensions are loaded
4. Restart the computer and try again

The issue is most likely that you're either:
- Not using the correct server URL
- The PHP server isn't running
- There's a browser security restriction