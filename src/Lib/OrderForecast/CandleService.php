<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast;

use App\Lib\OrderForecast\DTO\Candle;

class CandleService
{
    public function getByBetween(
        HistoryInterface $history,
        float $price,
        \DateTime $startDate,
        ?\DateTime $endDate,
        ?float $openMin = null,
        ?float $openMax = null,
    ): ?Candle {
        return $this->find($history, $price, $price, $startDate, $endDate, $openMin, $openMax);
    }

    public function getByHigh(HistoryInterface $history, float $price, \DateTime $startDate): ?Candle
    {
        return $this->find($history, null, $price, $startDate);
    }

    public function getByLow(HistoryInterface $history, float $price, \DateTime $startDate): ?Candle
    {
        return $this->find($history, $price, null, $startDate);
    }

    private function find(
        HistoryInterface $history,
        ?float $low,
        ?float $high,
        \DateTime $startDate,
        ?\DateTime $endDate=null,
        ?float $openMin = null,
        ?float $openMax = null,
    ): ?Candle {
        $resultCandle = null;
        $startDayDate = clone $startDate;
        $startDayDate->setTime(0, 0);
        $dayCandle = $history->findDayCandle($low, $high, $startDayDate, $endDate, $openMin, $openMax);
        if (null !== $dayCandle) {
            $dayDateTime = $dayCandle->getDateTime();
            // if day candle started earlier than startDate
            if ($dayDateTime->getTimestamp() < $startDate->getTimestamp()) {
                // check if minutes data exists for Order only ($low === $high)
                if ($low === $high && false === $this->isMinDataExists($history, $dayDateTime)) {
                    throw new \Exception('There are no 5 minutes data on ' . $dayDateTime->format('Y-m-d'));
                }

                $minsCandle = $this->getMins($history, $low, $high, $startDate);
                if (null === $minsCandle) {
                    $startDayDate->modify('+1 day');
                    $dayCandle = $history->findDayCandle($low, $high, $startDayDate, $endDate, $openMin, $openMax);
                } else {
                    $resultCandle = $minsCandle;
                }
            }

            if (null === $resultCandle && null !== $dayCandle) {
                $minsCandle = $this->getMins($history, $low, $high, $dayCandle->getDateTime());
                // if there are no minutes' data - use day time
                $resultCandle = $minsCandle ?? $dayCandle;
            }
        }

        return $resultCandle;
    }

    private function getMins(HistoryInterface $history, ?float $low, ?float $high, \DateTime $startDate): ?Candle
    {
        $endDate = clone $startDate;
        $endDate->setTime(23, 59, 59);
        return $history->find5MinCandle($low, $high, $startDate, $endDate);
    }

    private function isMinDataExists(HistoryInterface $history, \DateTime $fromDateTime): bool
    {
        $from = $fromDateTime->getTimestamp();
        $toDateTime = clone $fromDateTime;
        $toDateTime->setTime(23, 59);
        $to = $toDateTime->getTimestamp();
        $mins = $history->get5MinCandleTimes($from, $to, 1);

        return count($mins) > 0;
    }
}
