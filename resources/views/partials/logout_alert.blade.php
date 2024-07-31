<script>
    var message = "{{ $message }}";
    var redirectUri = "{{ $redirect_uri }}";
    document.addEventListener('DOMContentLoaded', function() {
        alert(message);
        window.location.href = redirectUri;
    });
</script>
