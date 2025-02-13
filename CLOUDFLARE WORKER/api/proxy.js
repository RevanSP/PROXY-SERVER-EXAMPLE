addEventListener('fetch', event => {
    event.respondWith(handleRequest(event.request));
});

async function handleRequest(request) {
    const url = new URL(request.url);

    if (url.pathname === '/') {
        return Response.redirect(`${url.origin}/api/proxy`, 301);
    }

    if (url.pathname === '/api/proxy') {
        const urlParams = url.searchParams;
        const targetUrl = urlParams.get('url');

        if (!targetUrl) {
            return new Response(JSON.stringify({ error: 'URL parameter is required' }), { status: 400 });
        }

        try {
            const response = await fetch(decodeURIComponent(targetUrl));
            const contentType = response.headers.get('content-type');
            const body = await response.arrayBuffer();

            return new Response(body, {
                headers: {
                    'Content-Type': contentType || 'application/octet-stream',
                    'Access-Control-Allow-Origin': '*',
                    'Access-Control-Allow-Methods': 'GET, OPTIONS',
                    'Access-Control-Allow-Headers': 'Content-Type',
                },
            });
        } catch (error) {
            console.error('Error fetching the URL:', error);
            return new Response(JSON.stringify({ error: 'Failed to fetch the resource' }), { status: 500 });
        }
    }

    return new Response('Not Found', { status: 404 });
}