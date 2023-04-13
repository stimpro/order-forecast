<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast;

use App\Lib\OrderForecast\DTO\Candle;

interface HistoryInterface
{
    public function getDayCandleTimes(int $from, int $to, int $limit=0): array;

    public function findDayCandle(
        ?float $low,
        ?float $high,
        ?\DateTime $startDate,
        ?\DateTime $endDate,
        ?float $openMin = null,
        ?float $openMax = null,
    ): ?Candle;

    public function get5MinCandleTimes(int $from, int $to, int $limit=0): array;

    public function find5MinCandle(?float $low, ?float $high, ?\DateTime $startDate, ?\DateTime $endDate): ?Candle;
}
