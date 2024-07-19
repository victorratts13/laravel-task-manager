import axios from 'axios';
import Alpine from 'alpinejs'
import TomSelect from "tom-select";

 
window.Alpine = Alpine;
window.axios = axios;
window.TomSelect = TomSelect;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
Alpine.start()
