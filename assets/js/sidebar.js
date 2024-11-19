$(document).ready(function () {

    $('#sidebar1').sidebar('attach events', '#sidebar-toggle', 'toggle');
    $('#sidebar1').sidebar('setting', {
        dimPage: false,
        closable: true,
    });

});