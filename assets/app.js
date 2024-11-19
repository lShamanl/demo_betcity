/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import jquery from 'jquery';

import './bootstrap';

import '../node_modules/semantic-ui-css/semantic.min.css'
import '../vendor/sylius/ui-bundle/Resources/private/sass/main.scss'
import './styles/app.css';

import 'semantic-ui-css'
import '../node_modules/semantic-ui-calendar/dist/calendar.min'
import '../vendor/sylius/ui-bundle/Resources/private/js/app.js'
import '../vendor/sylius/ui-bundle/Resources/private/js/sylius-auto-complete.js'
import '../node_modules/inputmask/dist/jquery.inputmask.js'
import './js/sidebar'

global.$ = global.jQuery = jquery;
