<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\OrderFlow;

use App\Lib\OrderForecast\DTO\Candle;
use App\Lib\OrderForecast\HistoryInterface;

interface OrderFlowInterface
{
    public function processOrder(
        HistoryInterface $history,
        float $price,
        \DateTime $startDate,
        ?\DateTime $endDate,
        ?float $open = null,
    ): ?Candle;

    public function takeProfit(
        HistoryInterface $history, float $takeProfit, \DateTime $openDate, float $orderPrice): ?Candle;

    public function stopLoss(HistoryInterface $history, float $stopLoss, \DateTime $openDate): ?Candle;
}
