document.addEventListener("DOMContentLoaded", function() {
    const podcastUrlInput = document.getElementById(sr42Settings.podcastInputId);

    function adjustInputWidth() {
        if (podcastUrlInput) {
            podcastUrlInput.style.width = ((podcastUrlInput.value.length + 1) * 0.8) + "em"; // Ajuster la multiplication selon la police utilisée
        }
    }

    if (podcastUrlInput) {
        podcastUrlInput.addEventListener('input', adjustInputWidth);
        adjustInputWidth(); // Appel initial pour ajuster la largeur
    }
});
