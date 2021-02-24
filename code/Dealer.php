<?php


class Dealer extends Player
{
    private const MIN_SCORE = 15;

    public function hit(Deck $deck): void
    {
        while($this->getScore() <= self::MIN_SCORE)
        {
            parent::hit($deck);
        }
//        echo("done!");
    }



}