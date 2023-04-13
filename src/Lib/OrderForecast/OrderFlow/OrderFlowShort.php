<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\OrderFlow;

use App\Lib\OrderForecast\CandleService;
use App\Lib\OrderForecast\DTO\Candle;
use App\Lib\OrderForecast\HistoryInterface;

class OrderFlowShort implements OrderFlowInterface
{
    private CandleService $candleService;

    public function __construct(
        CandleService $candleService,
    ) {
        $this->candleService = $candleService;
    }

    public function processOrder(
        HistoryInterface $history,
        float $price,
        \DateTime $startDate,
        ?\DateTime $endDate,
        ?float $open = null,
    ): ?Candle {
        return $this->candleService->getByBetween($history, $price, $startDate, $endDate, null, $open);
    }

    public function takeProfit(
        HistoryInterface $history, float $takeProfit, \DateTime $openDate, float $orderPrice): ?Candle
    {
        $tpCandle = $this->candleService->getByLow($history, $takeProfit, $openDate);
        if (null !== $tpCandle && $tpCandle->getDateTime()->getTimestamp() === $openDate->getTimestamp()) {
            // open > order || close < tp  => TP
            if ($tpCandle->getOpen() > $orderPrice || $tpCandle->getClose() < $takeProfit) {
                return $tpCandle;
            }
            // moved to the next candle
            $shiftOpenDate = clone $openDate;
            $shiftOpenDate->modify('+1 second');
            $tpCandle = $this->candleService->getByLow($history, $takeProfit, $shiftOpenDate);
        }
        return $tpCandle;
    }

    public function stopLoss(HistoryInterface $history, float $stopLoss, \DateTime $openDate): ?Candle
    {
        return $this->candleService->getByHigh($history, $stopLoss, $openDate);
    }
}
