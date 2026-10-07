import Echo from 'laravel-echo';

import Pusher from 'pusher-js';

const echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER || 'mt1',
    wsHost: process.env.MIX_PUSHER_HOST || `ws-${process.env.MIX_PUSHER_APP_CLUSTER || 'mt1'}.pusher.com`,
    wsPort: process.env.MIX_PUSHER_PORT || 80,
    wssPort: process.env.MIX_PUSHER_PORT || 443,
    forceTLS: (process.env.MIX_PUSHER_SCHEME || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

export default echo;
