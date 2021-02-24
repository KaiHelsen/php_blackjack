<?php
declare(strict_types=1);

//require_once('Deck.php');
class Player
{
    public const BLACKJACK = 21;

    private bool $lost = false;
    private bool $hasBlackjack;
    private array $cards;

    public function __construct(Deck $deck)
    {
        $this->cards = [];

        for ($i = 0; $i < 2; $i++)
        {
            self::hit($deck);
        }

        $this->hasBlackjack = ($this->getScore() === self::BLACKJACK);
    }

    /**
     * draws a new card and adds it to the player's hand
     * @param Deck $deck
     */
    public function hit(Deck $deck): void
    {
        array_push($this->cards, $deck->drawCard());

        if ($this->getScore() > self::BLACKJACK)
        {
            $this->lost = true;
        }
    }

    /**
     * give up and lose the game
     */
    public function surrender(): void
    {
        $this->lost = true;
    }

    /**
     * return total score of the cards in the player's hand
     * @return int
     */
    public function getScore(): int
    {
        $totalValue = 0;
        foreach ($this->cards as $card)
        {
            if ($card instanceof Card)
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

    /**
     * returns whether the player has won or lost
     * @return bool
     */
    public function hasLost(): bool
    {
        return $this->lost;
    }

    /**
     * TODO: figure this one out in a bit I guess
     */
    public function stand(): void
    {

    }

    public function getCardFromHand(int $key): ?Card
    {
        return $this->cards[$key];
    }

    public function getHandCount(): int
    {
        return count($this->cards);
    }

    public function getBlackjack() : bool
    {
        return $this->hasBlackjack;
    }
}