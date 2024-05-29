const WebSocket = require('ws');
const mysql = require('mysql');

// Create MySQL connection
const connection = mysql.createConnection({
    host: 'localhost',
    user: 'root', // Change to your MySQL username
    password: '', // Change to your MySQL password
    database: 'webhook' // Change to your MySQL database name
});

connection.connect(err => {
    if (err) {
        console.error('Error connecting to MySQL database:', err);
        return;
    }
    console.log('Connected to MySQL database');
});

const wss = new WebSocket.Server({ port: 8080 });

wss.on('connection', ws => {
    console.log('Client connected');

    ws.on('message', message => {
        console.log(`Received message => ${message}`);

        // Parse JSON message
        const data = JSON.parse(message);

        // Insert data into MySQL database
        const sql = 'INSERT INTO form_data (firstname, lastname, email, message) VALUES (?, ?, ?, ?)';
        connection.query(sql, [data.firstName, data.lastName, data.email, data.message], (err, result) => {
            if (err) {
                console.error('Error inserting data into MySQL:', err);
                return;
            }
            console.log('Data inserted into MySQL');

            // Broadcast the data to all connected clients
            const jsonString = JSON.stringify(data);
            wss.clients.forEach(client => {
                if (client !== ws && client.readyState === WebSocket.OPEN) {
                    client.send(jsonString);
                }
            });
        });
    });

    ws.on('close', () => {
        console.log('Client disconnected');
    });
});

console.log('WebSocket server is running on ws://localhost:8080');
