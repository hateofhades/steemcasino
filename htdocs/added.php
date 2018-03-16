<script>
	window.onunload = refreshParent;
    function refreshParent() {
        window.opener.location.reload();
    }
	window.close();
</script>