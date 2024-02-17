var bets = 0;

function flipCard(card, newImgSrc) {
    card.classList.toggle('is-flipped');
    setTimeout(function() {
        card.querySelector('img').src = newImgSrc;
    }, 250);
}


function flipPlayers(){
    var players = document.querySelector('.players').querySelectorAll('.flippable');
    for (var i = 0; i < players.length; i++) {
        flipCard(players[i], players[i].dataset.img);
    }
}



$(document).ready(function() {
    $('#submitBet').click(function() {
        var betValue = $('#betValue').val();

        $.ajax({
            url: 'Requests/bet.php', 
            type: 'POST',
            data: { bet: betValue },
            success: function(response) {
                $('#balance').text('Balance: ' + response);
                $('#betValue').val('');
                bets++;
                if (bets == 1) {
                    var dealer = document.querySelectorAll('.flippable-table');
                    console.log(dealer);
                    for (var i = 0; i < 3; i++) {
                        flipCard(dealer[i], dealer[i].dataset.img);

                    }
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    });
});
