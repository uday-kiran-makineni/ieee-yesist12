<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Form Builder Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        h1, h2 {
            color: #333;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .test-section {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #fafafa;
        }
        button {
            padding: 10px 20px;
            margin: 5px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        button:hover {
            background: #0056b3;
        }
        button.danger {
            background: #dc3545;
        }
        button.danger:hover {
            background: #c82333;
        }
        button.success {
            background: #28a745;
        }
        button.success:hover {
            background: #218838;
        }
        .response {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin-top: 10px;
            font-family: monospace;
            white-space: pre-wrap;
            max-height: 300px;
            overflow-y: auto;
        }
        .error {
            background: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
        .success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .templates-list {
            max-height: 200px;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 10px;
            margin-top: 10px;
        }
        .template-item {
            padding: 10px;
            border-bottom: 1px solid #eee;
            cursor: pointer;
        }
        .template-item:hover {
            background-color: #f0f0f0;
        }
        .template-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>PHP Form Builder API Test Interface</h1>
        <p>This interface allows you to test the PHP Form Builder API endpoints. Make sure your PHP server is running!</p>
    </div>

    <div class="container">
        <div class="test-section">
            <h2>1. Create Form Template</h2>
            <div class="form-group">
                <label for="template-title">Form Title:</label>
                <input type="text" id="template-title" placeholder="Enter form title" value="Sample Contact Form">
            </div>
            <div class="form-group">
                <label for="template-structure">Form Structure (JSON):</label>
                <textarea id="template-structure" placeholder="Enter form structure as JSON">
[
    {
        "type": "text",
        "label": "Full Name",
        "name": "fullname",
        "required": true,
        "placeholder": "Enter your full name"
    },
    {
        "type": "email",
        "label": "Email Address",
        "name": "email",
        "required": true,
        "placeholder": "Enter your email"
    },
    {
        "type": "textarea",
        "label": "Message",
        "name": "message",
        "required": false,
        "placeholder": "Enter your message"
    },
    {
        "type": "select",
        "label": "Department",
        "name": "department",
        "options": [
            {"value": "sales", "text": "Sales"},
            {"value": "support", "text": "Support"},
            {"value": "hr", "text": "Human Resources"}
        ]
    }
]
                </textarea>
            </div>
            <button onclick="createTemplate()">Create Template</button>
            <div id="create-response" class="response" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>2. Get All Templates</h2>
            <button onclick="getAllTemplates()">Load All Templates</button>
            <div id="templates-response" class="response" style="display: none;"></div>
            <div id="templates-list" class="templates-list" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>3. Get Specific Template</h2>
            <div class="form-group">
                <label for="get-template-id">Template ID:</label>
                <input type="number" id="get-template-id" placeholder="Enter template ID" value="1">
            </div>
            <button onclick="getTemplate()">Get Template</button>
            <div id="get-template-response" class="response" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>4. Update Template</h2>
            <div class="form-group">
                <label for="update-template-id">Template ID:</label>
                <input type="number" id="update-template-id" placeholder="Enter template ID" value="1">
            </div>
            <div class="form-group">
                <label for="update-title">New Title:</label>
                <input type="text" id="update-title" placeholder="Enter new title" value="Updated Contact Form">
            </div>
            <div class="form-group">
                <label for="update-structure">Updated Structure (JSON):</label>
                <textarea id="update-structure" placeholder="Enter updated form structure">
[
    {
        "type": "text",
        "label": "Full Name",
        "name": "fullname",
        "required": true,
        "placeholder": "Enter your full name"
    },
    {
        "type": "email",
        "label": "Email Address",
        "name": "email",
        "required": true,
        "placeholder": "Enter your email"
    },
    {
        "type": "phone",
        "label": "Phone Number",
        "name": "phone",
        "required": false,
        "placeholder": "Enter your phone number"
    },
    {
        "type": "textarea",
        "label": "Message",
        "name": "message",
        "required": true,
        "placeholder": "Enter your message"
    }
]
                </textarea>
            </div>
            <button onclick="updateTemplate()">Update Template</button>
            <div id="update-response" class="response" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>5. Submit Form Entry</h2>
            <div class="form-group">
                <label for="entry-form-id">Form Template ID:</label>
                <input type="number" id="entry-form-id" placeholder="Enter form template ID" value="1">
            </div>
            <div class="form-group">
                <label for="entry-data">Form Data (JSON):</label>
                <textarea id="entry-data" placeholder="Enter form submission data">
{
    "fullname": "John Doe",
    "email": "john.doe@example.com",
    "phone": "+1234567890",
    "message": "This is a test message from the PHP API test interface."
}
                </textarea>
            </div>
            <button class="success" onclick="createEntry()">Submit Form Entry</button>
            <div id="entry-response" class="response" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>6. Get Form Entries</h2>
            <div class="form-group">
                <label for="entries-form-id">Form Template ID:</label>
                <input type="number" id="entries-form-id" placeholder="Enter form template ID" value="1">
            </div>
            <button onclick="getEntries()">Get All Entries</button>
            <div id="entries-response" class="response" style="display: none;"></div>
        </div>

        <div class="test-section">
            <h2>7. Delete Template</h2>
            <div class="form-group">
                <label for="delete-template-id">Template ID:</label>
                <input type="number" id="delete-template-id" placeholder="Enter template ID to delete">
            </div>
            <button class="danger" onclick="deleteTemplate()">Delete Template</button>
            <div id="delete-response" class="response" style="display: none;"></div>
        </div>
    </div>

    <script>
        const API_BASE_URL = 'test_form_api.php';

        function showResponse(elementId, data, isError = false) {
            const element = document.getElementById(elementId);
            element.style.display = 'block';
            element.className = `response ${isError ? 'error' : 'success'}`;
            element.textContent = JSON.stringify(data, null, 2);
        }

        async function makeRequest(url, method = 'GET', data = null) {
            try {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                    }
                };
                
                if (data) {
                    options.body = JSON.stringify(data);
                }

                const response = await fetch(url, options);
                const result = await response.json();
                
                return {
                    success: response.ok,
                    data: result,
                    status: response.status
                };
            } catch (error) {
                return {
                    success: false,
                    data: { error: error.message },
                    status: 0
                };
            }
        }

        async function createTemplate() {
            const title = document.getElementById('template-title').value;
            const structureText = document.getElementById('template-structure').value;
            
            try {
                const structure = JSON.parse(structureText);
                const result = await makeRequest(API_BASE_URL, 'POST', {
                    title: title,
                    structure: structure
                });
                
                showResponse('create-response', result.data, !result.success);
            } catch (error) {
                showResponse('create-response', { error: 'Invalid JSON structure: ' + error.message }, true);
            }
        }

        async function getAllTemplates() {
            const result = await makeRequest(API_BASE_URL, 'GET');
            showResponse('templates-response', result.data, !result.success);
            
            if (result.success && Array.isArray(result.data)) {
                displayTemplatesList(result.data);
            }
        }

        function displayTemplatesList(templates) {
            const listElement = document.getElementById('templates-list');
            listElement.style.display = 'block';
            listElement.innerHTML = '';
            
            if (templates.length === 0) {
                listElement.innerHTML = '<p>No templates found.</p>';
                return;
            }
            
            templates.forEach(template => {
                const item = document.createElement('div');
                item.className = 'template-item';
                item.innerHTML = `
                    <strong>ID: ${template.id}</strong> - ${template.title}
                    <br><small>Created: ${template.created_at || 'Unknown'}</small>
                `;
                item.onclick = () => {
                    document.getElementById('get-template-id').value = template.id;
                    document.getElementById('update-template-id').value = template.id;
                    document.getElementById('entry-form-id').value = template.id;
                    document.getElementById('entries-form-id').value = template.id;
                };
                listElement.appendChild(item);
            });
        }

        async function getTemplate() {
            const id = document.getElementById('get-template-id').value;
            const result = await makeRequest(`${API_BASE_URL}/template/${id}`, 'GET');
            showResponse('get-template-response', result.data, !result.success);
        }

        async function updateTemplate() {
            const id = document.getElementById('update-template-id').value;
            const title = document.getElementById('update-title').value;
            const structureText = document.getElementById('update-structure').value;
            
            try {
                const structure = JSON.parse(structureText);
                const result = await makeRequest(`${API_BASE_URL}/template/${id}`, 'PUT', {
                    title: title,
                    structure: structure
                });
                
                showResponse('update-response', result.data, !result.success);
            } catch (error) {
                showResponse('update-response', { error: 'Invalid JSON structure: ' + error.message }, true);
            }
        }

        async function createEntry() {
            const formId = document.getElementById('entry-form-id').value;
            const dataText = document.getElementById('entry-data').value;
            
            try {
                const data = JSON.parse(dataText);
                const result = await makeRequest(`${API_BASE_URL}/entry`, 'POST', {
                    form_id: formId,
                    data: data
                });
                
                showResponse('entry-response', result.data, !result.success);
            } catch (error) {
                showResponse('entry-response', { error: 'Invalid JSON data: ' + error.message }, true);
            }
        }

        async function getEntries() {
            const formId = document.getElementById('entries-form-id').value;
            const result = await makeRequest(`${API_BASE_URL}/entries/${formId}`, 'GET');
            showResponse('entries-response', result.data, !result.success);
        }

        async function deleteTemplate() {
            const id = document.getElementById('delete-template-id').value;
            if (!confirm(`Are you sure you want to delete template ID ${id}?`)) {
                return;
            }
            
            const result = await makeRequest(`${API_BASE_URL}/template/${id}`, 'DELETE');
            showResponse('delete-response', result.data, !result.success);
        }

        // Load templates on page load
        window.onload = function() {
            getAllTemplates();
        };
    </script>
</body>
</html>