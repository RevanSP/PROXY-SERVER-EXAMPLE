import axios from 'axios';

export default async function handler(req, res) {
    try {
        const { url } = req.query;

        if (!url) {
            return res.status(400).json({ error: 'URL parameter is required' });
        }

        const response = await axios.get(decodeURIComponent(url), {
            responseType: 'arraybuffer'
        });

        const contentType = response.headers['content-type'];

        res.setHeader('Access-Control-Allow-Origin', '*');
        res.setHeader('Access-Control-Allow-Methods', 'GET, OPTIONS');
        res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

        res.setHeader('Content-Type', contentType || 'application/octet-stream');
        res.status(200).send(response.data);
    } catch (error) {
        console.error('Error fetching the URL:', error);
        res.status(500).json({ error: 'Failed to fetch the resource' });
    }
}