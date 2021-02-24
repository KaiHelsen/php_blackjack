<?php
declare(strict_types=1);

class Suit
{
    private const TYPE_SPADE = 'Spade';
    private const TYPE_HEART = 'Heart';
    private const TYPE_DIAMOND = 'Diamond';
    private const TYPE_CLUB = 'Club';

    private const CHAR_SPADE = 127136;
    private const CHAR_HEART = 127152;
    private const CHAR_DIAMOND = 127168;
    private const CHAR_CLUB = 127184;

    /** @var string */
    private string $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function SPADE(): Suit
    {
        return new Suit(self::TYPE_SPADE);
    }

    public static function HEART(): Suit
    {
        return new Suit(self::TYPE_HEART);
    }

    public static function DIAMOND(): Suit
    {
        return new Suit(self::TYPE_DIAMOND);
    }

    public static function CLUB(): Suit
    {
        return new Suit(self::TYPE_CLUB);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColor(): string
    {
        return in_array($this->name, [self::TYPE_HEART, self::TYPE_DIAMOND]) ? 'red' : 'black';
    }

    public function getStartValue(): int
    {
        switch ($this->name)
        {
            case self::TYPE_SPADE;
                return self::CHAR_SPADE;
            case self::TYPE_CLUB;
                return self::CHAR_CLUB;
            case self::TYPE_DIAMOND;
                return self::CHAR_DIAMOND;
            case self::TYPE_HEART;
                return self::CHAR_HEART;
            default:
                throw new DomainException('Invalid suit type');
        }
    }
}