const express = require('express');
const axios = require('axios');
const cors = require('cors');
const path = require('path');
const app = express();

app.use(cors({
    origin: '*',
    methods: ['GET', 'OPTIONS'],
    allowedHeaders: ['Content-Type']
}));

app.get('/api/proxy', async (req, res) => {
    const targetUrl = req.query.url;

    if (!targetUrl) {
        return res.status(400).json({ error: 'URL parameter is required' });
    }

    try {
        const response = await axios({
            method: 'GET',
            url: targetUrl,
            responseType: 'arraybuffer'
        });

        const filename = path.basename(new URL(targetUrl).pathname);

        res.set({
            'Content-Type': 'application/octet-stream',
            'Content-Disposition': `attachment; filename="${filename}"`,
            'Content-Length': response.data.length,
            'Cache-Control': 'no-cache'
        });

        res.send(response.data);

    } catch (error) {
        console.error('Error fetching the resource:', error.message);
        res.status(500).json({
            error: 'Failed to fetch the resource: ' + error.message
        });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Proxy server running on port ${PORT}`);
});