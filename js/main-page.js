document.addEventListener("DOMContentLoaded", function() {
    const copyButton = document.getElementById('copy_shortcode');
    const shortcodeInput = document.getElementById('shortcode');
    const successMessage = document.getElementById('success_message');

    if (copyButton && shortcodeInput && successMessage) {
        copyButton.onclick = function() {
            shortcodeInput.select();
            document.execCommand('copy');
            
            successMessage.style.display = 'block';
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 3000); // Masquer le message après 3 secondes
        };
    }
});
