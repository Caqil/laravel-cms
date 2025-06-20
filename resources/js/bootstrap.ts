import axios from 'axios';
import { route } from './Lib/route';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Make route function available globally
window.route = route;

declare global {
    interface Window {
        axios: typeof axios;
        route: typeof route;
    }
}
