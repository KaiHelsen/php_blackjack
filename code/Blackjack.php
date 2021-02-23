<?php
declare(strict_types = 1);

class Blackjack
{
    private Player $player;
    private Player $dealer;
    private Deck $deck;

    public function __construct()
    {
        $this->deck = new Deck();
        $this->player = new Player($this->deck);
        $this->dealer = new Player($this->deck);
        $this->deck->shuffle();
    }
    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * @return Player
     */
    public function getDealer(): Player
    {
        return $this->dealer;
    }

    /**
     * @return Deck
     */
    public function getDeck(): Deck
    {
        return $this->deck;
    }


}