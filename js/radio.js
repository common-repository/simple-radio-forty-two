jQuery(document).ready(function($) {
    let audio = $('audio')[0];
    let latency = 0;
    let isPaused = true;

    function setVolume() {
	    //vol = document.getElementById("volumeslider").value / 100;
	    //$("audio").animate({volume: vol}, 1000); // renvoie une valeur infini. Convient a audio volume mais pas a animate
        let volume = $('#volumeslider').val() / 100;
		//$(audio).animate({volume: volume}, 1000);
        audio.volume = volume;
    }
	
	function setVolume2() {
		vol = document.getElementById("volumeslider").value / 100;
		$(audio).animate({volume: vol}, 1000);
	}

	$('#volumeslider').on("input", setVolume);/*click(function() {
		setVolume();
	});*/
	
    function startAnimation() {
        $("#round").css("animation-play-state", "running");
    }

    function stopAnimation() {
        $("#round").css("animation-play-state", "paused");
    }

    $('#play_button').click(function() {
        play();
    });

    $('#pause_button').click(function() {
        pause();
    });

    function play() {
        $('#play_button').addClass('hidden');
        $('#pause_button').removeClass('hidden');
        audio.volume = 0; // Start with volume 0
        audio.play();
        //$(audio).animate({ volume: 1 }, 1000); // Fade in volume
		setVolume2();
        isPaused = false;
        startAnimation();
    }

    function pause() {
        $('#pause_button').addClass('hidden');
        $('#play_button').removeClass('hidden');
        $(audio).animate({ volume: 0 }, 1000, function() {
            audio.pause();
            stopAnimation();
            isPaused = true;
        });
    }

    function buffer() {
        audio.play();
        setTimeout(function() {
            audio.pause();
        }, 10);
        setTimeout(function() {
            $("#wait").fadeOut(500);
            $("#track").fadeIn(500);
            $("#round").addClass('click');
        }, 5000);
    }

    function copyToClipboard() {
        const link = $(audio).attr("src");
        window.prompt("Copy link and paste it in VLC", link);
    }
/*
    function updateTrackInfo() {
        $.get('/wp-json/radio/v1/gimme_song', function(data) {
            if (data.is_running) {
                $('#title').text(data.title);
                $('#artist').text(data.artist);
            } else {
                $('#title').text('No track playing');
                $('#artist').text('');
            }
        }, 'json');
    }

    function updateProgramInfo() {
        $.get('/wp-json/radio/v1/gimme_program', function(data) {
            if (data.jobs_num > 0 && data.jobs && data.jobs.length > 0) {
                $('#program_name').text(data.jobs[0].title);
                $('#date').text(new Date(data.jobs[0].timestamp * 1000).toLocaleString());
            } else {
                $('#program_name').text('Aucun programme à venir');
                $('#date').text('');
            }
        }, 'json').fail(function() {
            $('#program_name').text('Erreur lors de la récupération des informations sur le programme');
            $('#date').text('');
        });
    }

    function updateListeners() {
        $.get('/wp-json/radio/v1/gimme_listeners', function(data) {
            $('#num').text(data.listeners);
            $('#str').text(data.status);
        }, 'json');
    }
*/
    // Start buffering on ready
    buffer();

    // Initial calls
    //updateTrackInfo();
    //updateProgramInfo();
    //updateListeners();

    // Update every 10 seconds
    //setInterval(updateTrackInfo, 10000);
    //setInterval(updateProgramInfo, 10000);
    //setInterval(updateListeners, 10000);
    
    // Update latency
    setInterval(function() {
        if (isPaused) latency++;
    }, 10000);

    // Event handler for the footer to copy VLC link
    $("#footer_cause").click(function() {
        copyToClipboard();
    });
});

/*jQuery(document).ready(function($) {
    let audio = $('audio')[0];
    let latency = 0;
    let isPaused = true;

    function setVolume() {
        let volume = $('#volumeslider').val() / 100;
        audio.volume = volume;
    }

    $('#play_button').click(function() {
        play();
    });

    $('#pause_button').click(function() {
        pause();
    });

    function play() {
        $('#play_button').addClass('hidden');
        $('#pause_button').removeClass('hidden');
        audio.volume = 0; // Start with volume 0
        audio.play();
        $(audio).animate({ volume: 1 }, 1000); // Fade in volume
        isPaused = false;
        startAnimation();
    }

    function pause() {
        $('#pause_button').addClass('hidden');
        $('#play_button').removeClass('hidden');
        $(audio).animate({ volume: 0 }, 1000, function() {
            audio.pause();
            stopAnimation();
            isPaused = true;
        });
    }

    function buffer() {
        audio.play();
        setTimeout(function() {
            audio.pause();
        }, 10);
        setTimeout(function() {
            $("#wait").fadeOut(500);
            $("#track").fadeIn(500);
            $("#round").addClass('click');
        }, 5000);
    }

    function updateTrackInfo() {
        $.get('/wp-json/radio/v1/gimme_song', function(data) {
            if (data.is_running) {
                $('#title').text(data.title);
                $('#artist').text(data.artist);
            } else {
                $('#title').text('No track playing');
                $('#artist').text('');
            }
        }, 'json');
    }

    function updateProgramInfo() {
        $.get('/wp-json/radio/v1/gimme_program', function(data) {
            if (data.jobs_num > 0 && data.jobs && data.jobs.length > 0) {
                $('#program_name').text(data.jobs[0].title);
                $('#date').text(new Date(data.jobs[0].timestamp * 1000).toLocaleString());
            } else {
                $('#program_name').text('Aucun programme à venir');
                $('#date').text('');
            }
        }, 'json').fail(function() {
            $('#program_name').text('Erreur lors de la récupération des informations sur le programme');
            $('#date').text('');
        });
    }

    function updateListeners() {
        $.get('/wp-json/radio/v1/gimme_listeners', function(data) {
            $('#num').text(data.listeners);
            $('#str').text(data.status);
        }, 'json');
    }

    // Start buffering on ready
    buffer();

    // Initial calls
    updateTrackInfo();
    updateProgramInfo();
    updateListeners();

    // Update every 10 seconds
    setInterval(updateTrackInfo, 10000);
    setInterval(updateProgramInfo, 10000);
    setInterval(updateListeners, 10000);
    
    // Update latency
    setInterval(function() {
        if (isPaused) latency++;
    }, 10000);
});
*/

/*jQuery(document).ready(function($) {
    let audio = $('audio')[0];

    function setVolume() {
        let volume = $('#volumeslider').val() / 100;
        audio.volume = volume;
    }

    $('#play_button').click(function() {
        audio.play();
        $('#play_button').addClass('hidden');
        $('#pause_button').removeClass('hidden');
    });

    $('#pause_button').click(function() {
        audio.pause();
        $('#pause_button').addClass('hidden');
        $('#play_button').removeClass('hidden');
    });

    function updateTrackInfo() {
        $.get('/wp-json/radio/v1/gimme_song', function(data) {
            if (data.is_running) {
                $('#title').text(data.title);
                $('#artist').text(data.artist);
            } else {
                $('#title').text('No track playing');
                $('#artist').text('');
            }
        }, 'json');
    }

    function updateProgramInfo() {
    $.get('/wp-json/radio/v1/gimme_program', function(data) {
        // Vérifier si le nombre de programmes est supérieur à 0
        if (data.jobs_num > 0 && data.jobs && data.jobs.length > 0) {
            $('#program_name').text(data.jobs[0].title);
            $('#date').text(new Date(data.jobs[0].timestamp * 1000).toLocaleString());
        } else {
            // Afficher un message indiquant qu'il n'y a pas de programme
            $('#program_name').text('Aucun programme à venir');
            $('#date').text('');
        }
    }, 'json').fail(function() {
        // Gérer les erreurs d'appel API
        $('#program_name').text('Erreur lors de la récupération des informations sur le programme');
        $('#date').text('');
    });
}

    function updateListeners() {
        $.get('/wp-json/radio/v1/gimme_listeners', function(data) {
            $('#num').text(data.listeners);
            $('#str').text(data.status); // Supposons que 'str' soit le statut ici
        }, 'json');
    }

    // Initial calls
    updateTrackInfo();
    updateProgramInfo();
    updateListeners();

    // Update every 10 seconds
    setInterval(updateTrackInfo, 10000);
    setInterval(updateProgramInfo, 10000);
    setInterval(updateListeners, 10000);
});
*/
/*jQuery(document).ready(function($) {
    let audio = $('audio')[0];

    function setVolume() {
        let volume = $('#volumeslider').val() / 100;
        audio.volume = volume;
    }

    $('#play_button').click(function() {
        audio.play();
        $('#play_button').addClass('hidden');
        $('#pause_button').removeClass('hidden');
    });

    $('#pause_button').click(function() {
        audio.pause();
        $('#pause_button').addClass('hidden');
        $('#play_button').removeClass('hidden');
    });

    function updateTrackInfo() {
        $.get('gimme_song.php', function(data) {
            if (data.is_running) {
                $('#title').text(data.title);
                $('#artist').text(data.artist);
            } else {
                $('#title').text('No track playing');
                $('#artist').text('');
            }
        }, 'json');
    }

    function updateProgramInfo() {
        $.get('gimme_program.php', function(data) {
            if (data.jobs_num > 0) {
                $('#program_name').text(data.jobs[0].title);
                $('#date').text(new Date(data.jobs[0].timestamp * 1000).toLocaleString());
            } else {
                $('#program_name').text('No upcoming program');
                $('#date').text('');
            }
        }, 'json');
    }

    function updateListeners() {
        $.get('gimme_listeners.php', function(data) {
            $('#num').text(data.num);
            $('#str').text(data.str);
        }, 'json');
    }

    // Initial calls
    updateTrackInfo();
    updateProgramInfo();
    updateListeners();

    // Update every 10 seconds
    setInterval(updateTrackInfo, 10000);
    setInterval(updateProgramInfo, 10000);
    setInterval(updateListeners, 10000);
});
*/