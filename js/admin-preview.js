document.addEventListener('DOMContentLoaded', function() {
    const DEFAULT_BACKGROUND_URL = pluginData.pluginUrl + 'style/img/background.gif';
    const DEFAULT_PLAY_BUTTON_URL = pluginData.pluginUrl + 'style/img/play.png'; // Assurez-vous d'avoir cette image ou ajustez le nom selon votre fichier.
    const DEFAULT_PAUSE_BUTTON_URL = pluginData.pluginUrl + 'style/img/pause.png';
    const DEFAULT_ROUND_URL = pluginData.pluginUrl + 'style/img/round.png';
	

    const updatePreview = function() {
        let backgroundUrl = document.getElementById('sr42_background_url').value || DEFAULT_BACKGROUND_URL;
        let playUrl = document.getElementById('sr42_play_url').value || DEFAULT_PLAY_BUTTON_URL;
        let pauseUrl = document.getElementById('sr42_pause_url').value || DEFAULT_PAUSE_BUTTON_URL;
        let roundUrl = document.getElementById('sr42_round_url').value || DEFAULT_ROUND_URL;
        
        document.querySelector('.radio_container').style.backgroundImage = `url(${backgroundUrl})`;
        document.getElementById('preview-play-button').style.backgroundImage = `url(${playUrl})`;
        document.getElementById('preview-pause-button').style.backgroundImage = `url(${pauseUrl})`;
        document.getElementById('preview-round').style.backgroundImage = `url(${roundUrl})`;
    };

    // Ajout des écouteurs d'événements
    document.querySelectorAll('#sr42_background_url, #sr42_play_url','#sr42_pause_url','#sr42_round_url').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    updatePreview(); // Pour l'affichage initial
});