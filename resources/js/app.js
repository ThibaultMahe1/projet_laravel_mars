import './bootstrap';

import Alpine from 'alpinejs';

import { initHologram } from './hologram';

window.Alpine = Alpine;
window.initHologram = initHologram; // Rendre accessible globalement pour Blade

Alpine.start();
