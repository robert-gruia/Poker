function flipCard(card, newImgSrc) {
    card.classList.toggle('is-flipped');
    setTimeout(function() {
        card.querySelector('.back img').src = newImgSrc;
    }, 350);
}


function flipPlayers(){
    var players = document.querySelector('.players').querySelectorAll('.flippable');
    for (var i = 0; i < players.length; i++) {
        flipCard(players[i], players[i].dataset.img);
    }
}
