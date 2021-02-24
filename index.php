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
const PLAYER_CHIPS = "chips";
const INIT_CHIP_AMOUNT = 100;
const MIN_CHIP_BET = 5;


//initialize classes
session_start();

//if there isn't a blackjack game yet, create one
$initSession = static function (): Blackjack
{
    if (!isset($_SESSION[GAME_SESSION]))
    {
        $_SESSION[GAME_SESSION] = new Blackjack();
    }
    if (!isset($_SESSION[PLAYER_CHIPS]))
    {
        $_SESSION[PLAYER_CHIPS] = INIT_CHIP_AMOUNT;
    }
    return $_SESSION[GAME_SESSION];
};

$game = $initSession();
$deck = $game->getDeck();
$player = $game->getPlayer();
$dealer = $game->getDealer();
$currentBet = 0;

function endGame(int $betValue, bool $playerWon = true): bool
{
    if ($playerWon)
    {
        $_SESSION[PLAYER_CHIPS] += $betValue;
    }
    else
    {
        $_SESSION[PLAYER_CHIPS] -= $betValue;
    }
    unset($_SESSION[GAME_SESSION]);
    return true;
}

//var_DUMP($_SESSION[GAME_SESSION]->getPlayer()->getHand());

$playerInput = "";
$roundOver = false;

//get player action

if (isset($_POST["action"]))
{
    $playerInput = htmlspecialchars($_POST["action"], ENT_NOQUOTES, 'UTF-8');
    $currentBet = min($_SESSION[PLAYER_CHIPS], max(MIN_CHIP_BET, (int)$_POST["playerBet"]));

    switch ($playerInput)
    {
        case(HIT_ME):
            //hit player with a fresh card
            $player->hit($deck);
            break;
        case(STAND):
            //dealer takes all the cards they want
            $dealer->hit($deck);
            $roundOver = true;
            break;
        case (SURRENDER):
            //player gives up
            $player->surrender();
            unset($_SESSION[GAME_SESSION]);

            //initialize new session
            $game = $initSession();
            $deck = $game->getDeck();
            $player = $game->getPlayer();
            $dealer = $game->getDealer();

            header("index.php");
            break;
        default:
    }
}

if ($player->getBlackjack() && $dealer->getBlackjack())
{
    //IF PLAYER AND DEALER HAVE BLACKJACK
    //tie
    $lossMessage = "TWO BLACKJACKS! It's a tie!";
    $roundOver = endGame($currentBet);
}
elseif ($player->getBlackjack())
{
    //IF PLAYER HAS BLACKJACK
    //player wins
    $lossMessage = "BLACKJACK! Player has won this round!";
    $roundOver = endGame(($currentBet * 2) + 10);
}
elseif ($dealer->getBlackjack())
{
    //IF DEALER HAS BLACKJACK
    //dealer wins
    $lossMessage = "BLACKJACK! Player has lost this round";
    $roundOver = endgame(($currentBet + 5), false);
}
elseif ($player->hasLost() || ($roundOver && !$dealer->hasLost() && $dealer->getScore() >= $player->getScore() ))
{
    //IF PLAYER HAS LOST BECAUSE THEY WENT TOO HIGH
    //OR
    //ROUND IS OVER AND DEALER HAS A HIGHER OR EQUAL SCORE TO PLAYER
    $lossMessage = "player lost this round";
    $roundOver = endgame($currentBet, false);
}
elseif ($dealer->hasLost() || ($roundOver && !$player->hasLost() && $dealer->getScore() < $player->getScore() ))
{
    //IF DEALER LOST BECAUSE THEY WENT TOO HIGH
    //OR
    //ROUND IS OVER AND DEALER HAS A LOWER SCORE TO PLAYER
    $lossMessage = "Player has won this round!";
    $roundOver = endgame($currentBet * 2);
}


//go through win/loss conditions

//reset session
//session_unset();


?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blackjack!</title>
</head>
<body>
<form action="index.php" method="post">
    <button type="submit" name="action" value="<?php echo HIT_ME; ?>" <?php echo $roundOver ? " disabled" : ""; ?>>Hit
    </button>
    <button type="submit" name="action" value="<?php echo STAND; ?>" <?php echo $roundOver ? " disabled" : ""; ?>>
        Stand
    </button>
    <button type="submit" name="action" value="<?php echo SURRENDER; ?>">Restart</button>
    <div>
        <label>How much do you want to bet? You have <b><?php echo $_SESSION[PLAYER_CHIPS]; ?></b> chips to bet
            with</label>
    </div>
    <input type="number" name="playerBet" min="<?php echo MIN_CHIP_BET; ?>" max="$_SESSION[PLAYER_CHIPS]"
           value="<?php echo $currentBet; ?>">

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
