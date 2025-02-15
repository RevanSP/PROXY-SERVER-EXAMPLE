import axios from 'axios';

export const config = {
    api: {
        responseLimit: false,
    },
};

export default async function handler(req, res) {
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'GET, HEAD, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    if (req.method === 'OPTIONS') {
        res.status(200).end();
        return;
    }

    if (req.method !== 'GET' && req.method !== 'HEAD') {
        res.status(405).json({ error: 'Method not allowed' });
        return;
    }

    const { url } = req.query;

    if (!url) {
        res.status(400).json({ error: 'URL parameter is required' });
        return;
    }

    try {
        const response = await axios({
            method: req.method,
            url: url,
            responseType: 'arraybuffer'
        });

        const filename = new URL(url).pathname.split('/').pop();

        res.setHeader('Content-Type', 'application/octet-stream');
        res.setHeader('Content-Disposition', `attachment; filename="${filename}"`);
        res.setHeader('Content-Length', response.data.length);
        res.setHeader('Cache-Control', 'no-cache');

        if (req.method === 'HEAD') {
            res.end();
        } else {
            res.send(response.data);
        }
    } catch (error) {
        console.error('Error fetching the resource:', error.message);
        res.status(500).json({
            error: 'Failed to fetch the resource: ' + error.message
        });
    }
}