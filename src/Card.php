<?php

namespace AllDressed;

use AllDressed\Builders\FakeCardBuilder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Fluent;

class Card extends Fluent
{
    /**
     * The attributes that are mass assignable.
     *
     * * @var array<int, string>
     */
    protected $fillable = [
        'number',
        'year',
        'month',
        'cvc',
    ];

    /**
     * Create a new card instance.
     *
     * @param  string  $number
     * @param  int  $month
     * @param  int  $year
     * @param  string  $cvc
     * @param  \AllDressed\Address|null  $address
     */
    public function __construct(string $number, int $month, int $year, string $cvc, Address $address = null)
    {
        $this->number = str_replace(' ', '', $number);
        $this->month = $month;
        $this->year = $year;
        $this->cvc = $cvc;
        $this->address = $address;
    }

    /**
     * Fake a card for tests.
     *
     * @return \AllDressed\Builders\FakeCardBuilder
     */
    public static function fake(): FakeCardBuilder
    {
        return FakeCardBuilder::make();
    }

    /**
     * Retrieve the last four digits of the card number.
     *
     * @return string
     */
    public function getLastFourDigits(): string
    {
        return substr($this->number, -4);
    }

    /**
     * Retrieve the expiration month the card.
     *
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * Retrieve the number of the card.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Retrieve the verification code the card.
     *
     * @return string
     */
    public function getVerificationCode(): string
    {
        return $this->cvc;
    }

    /**
     * Retrieve the expiration year the card.
     *
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * Create a new card instance.
     *
     * @param  string  $number
     * @param  int  $year
     * @param  int  $month
     * @param  string  $cvc
     * @return static
     */
    public static function make(...$args): static
    {
        return new static(...$args);
    }

    /**
     * Create a random card instance.
     *
     * @param  string|null  $number
     * @return static
     */
    public static function random(string $number = null): static
    {
        return static::make(
            $number ?? implode('', array_map(
                static fn () => mt_rand(0, 9),
                array_fill(0, mt_rand(15, 16), null)
            )),
            mt_rand(1, 12),
            Carbon::today()->addYears(mt_rand(1, 10))->year,
            mt_rand(100, 9999),
        );
    }
}
