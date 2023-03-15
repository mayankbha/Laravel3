<script>
    function fbShare(url) {
        var popup = window.open('http://www.facebook.com/sharer.php?p[url]=' + url, '_self','toolbar=0,status=0');
        var pollTimer = window.setInterval(function() {
	        if (popup.closed !== false) { // !== is required for compatibility with Opera
	            window.clearInterval(pollTimer);
	            getShare();
	        }
	    }, 200);
    }
    fbShare('{{$url}}');

</script>
