<?php

//phpinfo();
//require all classes
require('./code/Deck.php');
require('./code/Suit.php');
require('./code/Card.php');
require('./code/Player.php');
require('./code/Dealer.php');
require('./code/Blackjack.php');

//declare CONST values
const HIT_ME = 'hit';
const STAND = 'stand';
const SURRENDER = 'surrender';
const GAME_SESSION = "myGame";

//initialize classes
session_start();

//if there isn't a blackjack game yet, create one
if (!isset($_SESSION[GAME_SESSION]))
{
    $_SESSION[GAME_SESSION] = new Blackjack();
}
$game = $_SESSION[GAME_SESSION];
$deck = $game->getDeck();
$player = $game->getPlayer();
$dealer = $game->getDealer();

$lossMessage = "";
//var_DUMP($_SESSION[GAME_SESSION]->getPlayer()->getHand());


//get player action
$playerInput = "";
if (isset($_POST["action"]))
{
    $playerInput = htmlspecialchars($_POST["action"], ENT_NOQUOTES, 'UTF-8');
}

switch ($playerInput)
{
    case(HIT_ME):
        //hit player with a fresh card
        $player->hit($deck);
        if ($player->hasLost())
        {
            $lossMessage = "player has lost this round";
            unset($_SESSION[GAME_SESSION]);
        }

        break;
    case(STAND):
        //dealer takes all the cards they want
        $dealer->hit($deck);

        if (!$dealer->hasLost() && $dealer->getScore() >= $player->getScore())
        {
            $lossMessage = "player has lost this round";
            unset($_SESSION[GAME_SESSION]);
        }
        else
        {
            $lossMessage = "player has won this round!";
            unset($_SESSION[GAME_SESSION]);
        }
        break;
    case (SURRENDER):
        //player gives up
        $player->surrender();
        $lossMessage = "player has lost this round";
        unset($_SESSION[GAME_SESSION]);
        break;
    default:
        $lossMessage = "hey, that's not a proper input, you cheater!";
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blackjack!</title>
</head>
<body>
<form action="index.php" method="post">
    <button type="submit" name="action" value="<?php echo HIT_ME; ?>">Hit</button>
    <button type="submit" name="action" value="<?php echo STAND; ?>">Stand</button>
    <button type="submit" name="action" value="<?php echo SURRENDER; ?>">Surrender</button>

</form>

<div>
    <div>Player hand: <?php echo $player->getScore(); ?></div>

    <?php for ($i = 0; $i < $player->getHandCount(); $i++):
        echo $player->getCardFromHand($i)->getUnicodeCharacter(true);
    endfor; ?>
</div>

<div>
    <div>Dealer hand: <?php echo $dealer->getScore(); ?></div>
    <?php for ($i = 0; $i < $dealer->getHandCount(); $i++):
        echo $dealer->getCardFromHand($i)->getUnicodeCharacter(true);
    endfor; ?>
    <br>
</div>

<div>
    <?php if (!empty($lossMessage)): ?>
        <b><?php echo $lossMessage; ?></b>
    <?php endif; ?>
</div>

</body>
</html>
