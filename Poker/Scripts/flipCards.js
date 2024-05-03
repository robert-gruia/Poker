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
    document.querySelectorAll('.handValue').forEach(hand => hand.style.display = 'block');
}



$(document).ready(function() {
    $('#submitBet').click(function() {
        var betValue = $('#betValue').val();
        if (betValue == '') {
            document.querySelector('.errorBet').textContent = 'Please enter a bet';
            return;
        }
        else{
            document.querySelector('.errorBet').textContent = '';
        }
        if(bets == 4){
            document.querySelector('.errorBet').textContent = 'Game is over, please refresh the page to play again.';
            return;
        }
        $.ajax({
            url: 'Requests/bet.php', 
            type: 'POST',
            data: { bet: betValue },
            success: function(response) {
                $('#balance').text('Balance: ' + response);
                $('#betValue').val('');
                bets++;
                var dealer = document.querySelectorAll('.flippable-table');
                if (bets == 1) {
                    for (var i = 0; i < 3; i++) {
                        flipCard(dealer[i], dealer[i].dataset.img);

                    }
                }
                else if (bets == 2) {
                    flipCard(dealer[3], dealer[3].dataset.img);
                }
                else if (bets == 3) {
                    flipCard(dealer[4], dealer[4].dataset.img);
                }
                else if (bets == 4) {
                    flipPlayers();
                }
            },
            error: function(error) {
                console.log(error);
            }
        });

    });
});
