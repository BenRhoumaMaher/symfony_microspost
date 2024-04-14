/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

const btn = document.getElementById("hamburger-icon");
const nav = document.getElementById("mobile-menu");
btn.addEventListener("click", () => {
    nav.classList.toggle("flex");
    nav.classList.toggle("hidden");
});

import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()
