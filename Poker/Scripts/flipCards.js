var bets = 0;
var potValue = 0;
var isWinner = false;
var yes = 1;
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
    function setWinner(division){
        isWinner = true;
        yes = division;
    }
    function updateBalance(){
        if(isWinner){
            $.ajax({
                url: 'Requests/addMoney.php',
                type: 'POST',
                data: { pot: potValue/yes },
                success: function(response) {
                    $('#balance').text('Balance: ' + response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    }
    
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
                betValue = parseInt(betValue);
                var yes = 0;
                for(var i = 0; i < 3; i++){
                    yes += Math.floor(Math.random() * (betValue + 50 - Math.max(0, betValue - 50) + 1) + Math.max(0, betValue - 50));
                }
                potValue += yes;
                potValue += betValue;
                $('.pot h2').text('Pot: ' + potValue);
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
                    updateBalance();
                }
            },
            error: function(error) {
                console.log(error);
            }
        });

    });
});
