import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || "9d0fb6df9f1d15dbc9bd",
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || "ap2",
    encrypted: true,
    forceTLS: true,
    authEndpoint: "/pusher/auth",
})
