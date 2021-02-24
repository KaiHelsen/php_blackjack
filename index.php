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

//var_DUMP($_SESSION[GAME_SESSION]->getPlayer()->getHand());

$playerInput = "";
$roundOver = false;

//get player action

if (isset($_POST["action"]))
{
    $playerInput = htmlspecialchars($_POST["action"], ENT_NOQUOTES, 'UTF-8');
    $currentBet = min($_SESSION[PLAYER_CHIPS],max(MIN_CHIP_BET,(int)$_POST["playerBet"]));
//    echo $currentBet;
}

if ($player->getBlackjack() && $dealer->getBlackjack())
{
    //tie
    $lossMessage = "TWO BLACKJACKS! It's a tie!";
    $roundOver = true;
    $_SESSION[PLAYER_CHIPS] += $currentBet;
    unset($_SESSION[GAME_SESSION]);
}
elseif ($player->getBlackjack())
{
    //player wins
    $lossMessage = "BLACKJACK! Player has won this round!";
    $_SESSION[PLAYER_CHIPS] += ($currentBet * 2) + 10;
    $roundOver = true;
    unset($_SESSION[GAME_SESSION]);
}
elseif ($dealer->getBlackjack())
{
    //dealer wins
    $lossMessage = "BLACKJACK! Player has lost this round";
    $_SESSION[PLAYER_CHIPS] -= (5 + $currentBet);
    $roundOver = true;
    unset($_SESSION[GAME_SESSION]);
}
else
{
    switch ($playerInput)
    {
        case(HIT_ME):
            //hit player with a fresh card
            $player->hit($deck);
            if ($player->hasLost())
            {
                $lossMessage = "player has lost this round";
                $_SESSION[PLAYER_CHIPS] -= $currentBet;
                $roundOver = true;
                unset($_SESSION[GAME_SESSION]);
            }
            break;
        case(STAND):
            //dealer takes all the cards they want
            $dealer->hit($deck);

            if (!$dealer->hasLost() && $dealer->getScore() >= $player->getScore())
            {
                $lossMessage = "player has lost this round";
                $_SESSION[PLAYER_CHIPS] -= $currentBet;
                $roundOver = true;
                unset($_SESSION[GAME_SESSION]);
            }
            else
            {
                $lossMessage = "player has won this round!";
                $_SESSION[PLAYER_CHIPS] += $currentBet * 2;
                $roundOver = true;
                unset($_SESSION[GAME_SESSION]);
            }
            break;
        case (SURRENDER):
            //player gives up
            $player->surrender();
            unset($_SESSION[GAME_SESSION]);
//
            $game = $initSession();
            $deck = $game->getDeck();
            $player = $game->getPlayer();
            $dealer = $game->getDealer();

            header("index.php");
            break;
        default:
    }
}

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
        <label>How much do you want to bet? You have <b><?php echo $_SESSION[PLAYER_CHIPS]; ?></b> chips to bet with</label>
    </div>
    <input type="number" name="playerBet" min="<?php echo MIN_CHIP_BET; ?>" max="$_SESSION[PLAYER_CHIPS]" value="<?php echo $currentBet; ?>">

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
