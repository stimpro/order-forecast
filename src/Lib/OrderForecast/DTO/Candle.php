<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\DTO;

class Candle
{
    private float $open;
    private float $close;
    private float $low;
    private float $high;
    private \DateTime $dateTime;

    public function __construct(
        float $open,
        float $close,
        float $low,
        float $high,
        \DateTime $dateTime,
    ) {
        $this->open = $open;
        $this->close = $close;
        $this->low = $low;
        $this->high = $high;
        $this->dateTime = $dateTime;
    }

    public function getOpen(): float
    {
        return $this->open;
    }

    public function getClose(): float
    {
        return $this->close;
    }

    public function getLow(): float
    {
        return $this->low;
    }

    public function getHigh(): float
    {
        return $this->high;
    }

    public function getDateTime(): \DateTime
    {
        return $this->dateTime;
    }
}
