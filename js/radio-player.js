document.addEventListener("DOMContentLoaded", function() {
    const audio = document.querySelector('audio');
    const waitMessage = document.getElementById('wait');
    const offlineMessage = document.getElementById('offline');
    const playButton = document.getElementById('play_button');
    const pauseButton = document.getElementById('pause_button');

    // Vos fonctions existantes ici
    function hideWaitMessage() {
        waitMessage.classList.add('hidden');
    }

    function showErrorMessage(message) {
        hideWaitMessage();
        offlineMessage.querySelector('p').textContent = message;
        offlineMessage.classList.remove('hidden');
        playButton.classList.add('hidden');
        pauseButton.classList.add('hidden');
    }

    function checkURL(url) {
        return fetch(url, { method: 'HEAD' })
            .then(response => response.ok)
            .catch(() => false);
    }

    function startCountdown() {
        let countdown = 7;
        const countdownInterval = setInterval(() => {
            console.log('Time left: ' + countdown + ' seconds');
            countdown--;

            if (countdown < 0) {
                clearInterval(countdownInterval);
                if (!waitMessage.classList.contains('hidden')) {
                    showErrorMessage('<?php esc_html_e("Buffering failed", "simple-radio-forty-two"); ?>');
                }
            }
        }, 1000);
    }

    function getPodcastUrl() {
        return fetch(PodcastData.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=get_latest_podcast_url&security=${PodcastData.nonce}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        });
    }

    function updatePodcastIfNeeded() {
        getPodcastUrl().then(newUrl => {
            if (newUrl && newUrl !== PodcastData.url) {
                PodcastData.url = newUrl;
                checkURL(newUrl).then(isAccessible => {
                    if (isAccessible) {
                        audio.src = newUrl;
                        audio.load();
                        startCountdown();
                        // Optionnel: auto-play après un changement d'URL
                        // audio.play().catch(showErrorMessage);
                    } else {
                        showErrorMessage('<?php esc_html_e("New URL is offline", "simple-radio-forty-two"); ?>');
                    }
                }).catch(() => showErrorMessage('<?php esc_html_e("Error checking new URL", "simple-radio-forty-two"); ?>'));
            }
        }).catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
    }

    // Vérification initiale
    updatePodcastIfNeeded();

    // Configuration de l'intervalle pour vérifier les mises à jour de l'URL
    setInterval(updatePodcastIfNeeded, 5 * 60 * 1000); // Toutes les 5 minutes

    // Play/Pause functionality
    playButton.onclick = function() {
        audio.play().catch(() => showErrorMessage('<?php esc_html_e("Playback failed", "simple-radio-forty-two"); ?>'));
        this.classList.add('hidden');
        pauseButton.classList.remove('hidden');
    };

    pauseButton.onclick = function() {
        audio.pause();
        this.classList.add('hidden');
        playButton.classList.remove('hidden');
    };

    // Volume control
    document.getElementById('volumeslider').oninput = function() {
        audio.volume = this.value / 100;
    };

    // Setup event for when audio starts playing to hide wait message
    audio.addEventListener('playing', hideWaitMessage);
});