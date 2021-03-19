/**
 * Mourning Banner js.
 * Injects a customisable banner.
 */
jQuery(document).ready(function ($) {

    // Build styles.
    var style = '<style>';
    style += '.mourning_banner_container {';
    style += 'padding: 15px 0px 15px 0px; width: 100%; position: relative; z-index: 10000; border: none; display: block; background-color: #000000;';
    style += 'min-height: 30px';
    style += '}';
    style += '.mourning_banner_wrap {';
    style += 'padding: 0px;';
    if ('yes' === mourning_banner_vars.fixed) {
        style += 'position: fixed;';
    }
    style += 'z-index: 10000; border: none; display: block;';
    if (mourning_banner_vars.background_colour) {
        style += 'background-color: ' + mourning_banner_vars.background_colour + ';';
    }
    style += 'text-align: left; font-size: 1em;min-height: 30px';
    style += '}';
    style += '.mourning_banner {';
    style += 'padding: 5px 20px; ';
    if (mourning_banner_vars.text_colour) {
        style += 'color: ' + mourning_banner_vars.text_colour + ';';
    }
    style += '}';
    style += '.mourning_banner a {';
    if (mourning_banner_vars.link_colour) {
        style += 'color: ' + mourning_banner_vars.link_colour + ';';
    }
    style += '}';
    style += '</style>';

    // Build html.
    var banner = '<div class="mourning_banner_container"><div class="mourning_banner_wrap container"><div class="mourning_banner">' + mourning_banner_vars.banner_message + '</div></div></div>';

    // Output.
    if ('prepend' === mourning_banner_vars.position) {
        $(mourning_banner_vars.element_to_attach_to).prepend(style + banner);
    }
    if ('append' === mourning_banner_vars.position) {
        $(mourning_banner_vars.element_to_attach_to).append(style + banner);
    }

});
