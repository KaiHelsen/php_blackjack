<?php
declare(strict_types = 1);

use JetBrains\PhpStorm\Pure;

class Player
{

    private CONST BLACKJACK = 21;
    private bool $lost = false;
    private array $cards;

    public function __construct(Deck $deck)
    {
        $this->cards = [];

        require('Deck.php');
        for($i = 0; $i < 2; $i++)
        {
            $this->cards[$i] = $deck->drawCard();
        }
    }

    public function hit(Deck $deck) : void
    {
        array_push($this->cards, $deck->drawCard());

        if($this->getScore() > self::BLACKJACK)
        {
            $this->lost = true;
        }
    }
    public function surrender() : void
    {
        $this->lost = true;
    }
    #[Pure] public function getScore() : int
    {
        $totalValue = 0;
        foreach($this->cards AS $card)
        {
            if($card instanceof Card)
            {
                $totalValue += $card->getValue();
            }
            else
            {
                continue;
            }
        }
        return $totalValue;
    }
    public function hasLost() : bool
    {
        return $this->lost;
    }
    public function stand() : void{

    }
}