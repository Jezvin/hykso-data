/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
import $ from 'jquery';
$(document).ready(function() {
    $(".title[data-type]").on('click', function () {
        let type = $(this).data("type");
        $("[data-type='" + type + "']").toggleClass("deployed");
    });
})