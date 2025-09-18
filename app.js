const express = require('express');
const cors = require('cors');
const mysql = require('mysql2/promise');

const app = express();
app.use(cors());
app.use(express.json());

const pool = mysql.createPool({
  host: 'localhost',
  user: 'root', // <-- Change to your MySQL username
  password: 'root', // <-- Change to your MySQL password
  database: 'formbuilder'
});

// Save a new form template

// Create a new form template
app.post('/form-template', async (req, res) => {
  const { title, structure } = req.body;
  const [result] = await pool.execute(
    'INSERT INTO form_templates (title, structure) VALUES (?, ?)',
    [title, JSON.stringify(structure)]
  );
  res.json({ id: result.insertId, title, structure });
});

// Update an existing form template
app.put('/form-template/:id', async (req, res) => {
  const { title, structure } = req.body;
  const { id } = req.params;
  try {
    const [result] = await pool.execute(
      'UPDATE form_templates SET title = ?, structure = ? WHERE id = ?',
      [title, JSON.stringify(structure), id]
    );
    if (result.affectedRows === 0) {
      return res.status(404).json({ error: 'Form not found.' });
    }
    res.json({ id, title, structure });
  } catch (err) {
    res.status(500).json({ error: 'Error updating form.' });
  }
});

// List all form templates
app.get('/form-template', async (req, res) => {
  const [rows] = await pool.execute('SELECT * FROM form_templates');
  res.json(rows);
});

// Save a form submission
app.post('/form-entry', async (req, res) => {
  const { form_id, data } = req.body;
  const [result] = await pool.execute(
    'INSERT INTO form_entries (form_id, data) VALUES (?, ?)',
    [form_id, JSON.stringify(data)]
  );
  res.json({ id: result.insertId, form_id, data });
});

// Get all submissions for a form
app.get('/form-entry/:form_id', async (req, res) => {
  const [rows] = await pool.execute(
    'SELECT * FROM form_entries WHERE form_id = ?',
    [req.params.form_id]
  );
  res.json(rows);
});

app.listen(4000, () => console.log('Server running on port 4000'));
