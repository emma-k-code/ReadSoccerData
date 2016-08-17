var system = require('system');
var webPage = require('webpage');
var page = webPage.create();
var fs = require('fs');

phantom.page.injectJs('jquery.js');

page.open("http://www.228365365.com/sports.php", function() {

    page.switchToFrame(0);
    page.switchToFrame(2);
    page.switchToFrame(1);

    fs.write('web/1.html', page.frameContent, 'w');
    phantom.exit();

});
