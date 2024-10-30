var iframes = iFrameResize({
        log:false,
        checkOrigin: false,
        maxWidth: screen.width,
        minWidth: 400,
        sizeWidth: true,
        autoResize: true,
        bodyMargin: 'none',
        heightCalculationMethod: 'max',
        resizedCallback: function(message) {
                var height = document.getElementById('caag-iframe').clientHeight;
                var newheight = height * 1.1;
                document.getElementById("caag-iframe").style.height = newheight + "px";
        }
}, '#caag-iframe');
