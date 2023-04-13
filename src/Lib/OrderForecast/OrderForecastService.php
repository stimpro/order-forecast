<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast;

use App\Lib\OrderForecast\DTO\Candle;
use App\Lib\OrderForecast\DTO\OrderForecastRequest;
use App\Lib\OrderForecast\DTO\OrderForecastResult;
use App\Lib\OrderForecast\OrderFlow\OrderFlowFactory;

class OrderForecastService
{
    private OrderFlowFactory $orderFlowFactory;

    public function __construct(
        OrderFlowFactory $orderFlowFactory,
    ) {
        $this->orderFlowFactory = $orderFlowFactory;
    }

    public function getForecast(HistoryInterface $history, OrderForecastRequest $orderRequest): OrderForecastResult
    {
        $orderResult = new OrderForecastResult();

        $maxOrderDate = $this->getMaxOrderDate($history, $orderRequest);

        $orderFlow = $this->orderFlowFactory->create($orderRequest->getOrderType());

        // when order can be opened
        $orderOpenCandle = $orderFlow->processOrder(
            $history,
            $orderRequest->getEntrypoint(),
            $orderRequest->getStartDate(),
            $maxOrderDate,
            $orderRequest->isCheckDayOpen() ? $orderRequest->getStopLoss() : null,
        );

        if (null !== $orderOpenCandle) {
            $openDate = $orderOpenCandle->getDateTime();

            // check TakeProfit
            $takeProfitCandle = $orderFlow->takeProfit(
                $history, $orderRequest->getTakeProfit(), $openDate, $orderRequest->getEntrypoint());

            // check StopLoss
            $stopLossCandle = $orderFlow->stopLoss($history, $orderRequest->getStopLoss(), $openDate);

            // compare TP vs SL - which first and create result
            [$orderStatus, $closeDate] = $this->getOrderResult($orderOpenCandle, $takeProfitCandle, $stopLossCandle);

            // get qty of days between start and open order
            $requestTime = $orderRequest->getStartDate()->getTimestamp();
            $openOrderDayNumber = $this->countDayQty($history, $requestTime, $openDate->getTimestamp());

            $orderResult->setStatus($orderStatus);
            $orderResult->setOpenDate($openDate);
            $orderResult->setOpenOrderDayNumber($openOrderDayNumber);
            $orderResult->setCloseDate($closeDate);
            if (null !== $closeDate) {
                // get qty of days between start and close order
                $closeOrderDayNumber = $this->countDayQty($history, $requestTime, $closeDate->getTimestamp());
                $orderResult->setCloseOrderDayNumber($closeOrderDayNumber);
            }
        }

        return $orderResult;
    }

    private function getMaxOrderDate(HistoryInterface $history, OrderForecastRequest $orderRequest): ?\DateTime
    {
        $maxOrderDate = null;
        $daysForOrder = $orderRequest->getDaysForOrder();
        if ($daysForOrder > 0) {
            $startDate = $orderRequest->getStartDate();
            $times = $history->getDayCandleTimes($startDate->getTimestamp(), 0, $daysForOrder);
            $maxTime = $startDate->getTimestamp();
            foreach ($times as $time) {
                $maxTime = max($time, $maxTime);
            }
            $maxOrderDate = new \DateTime();
            $maxOrderDate->setTimestamp($maxTime);
            $maxOrderDate->setTime(23, 59, 59);
        }

        return $maxOrderDate;
    }

    private function getOrderResult(Candle $orderOpenCandle, ?Candle $takeProfitCandle, ?Candle $stopLossCandle): array
    {
        $orderStatus = OrderForecastResult::ORDER_STATUS_WAIT;
        $closeDateTime = null;

        if (null !== $takeProfitCandle || null !== $stopLossCandle) {
            if ($takeProfitCandle?->getDateTime()->getTimestamp() === $stopLossCandle?->getDateTime()->getTimestamp()
                || $orderOpenCandle->getDateTime()->getTimestamp() === $stopLossCandle?->getDateTime()->getTimestamp()
            ) {
                $orderStatus = OrderForecastResult::ORDER_STATUS_STOP_LOSS;
                $closeDateTime = $stopLossCandle->getDateTime();
            } else {
                $statusDateTime = [];
                if (null !== $takeProfitCandle) {
                    $statusDateTime[OrderForecastResult::ORDER_STATUS_TAKE_PROFIT] = $takeProfitCandle->getDateTime();
                }

                if (null !== $stopLossCandle) {
                    $statusDateTime[OrderForecastResult::ORDER_STATUS_STOP_LOSS] = $stopLossCandle->getDateTime();
                }

                foreach ($statusDateTime as $status => $dateTime) {
                    if (null === $closeDateTime || $dateTime->getTimestamp() < $closeDateTime->getTimestamp()) {
                        $closeDateTime = $dateTime;
                        $orderStatus = $status;
                    }
                }
            }
        }

        return [$orderStatus, $closeDateTime];
    }

    private function countDayQty(HistoryInterface $history, int $startTime, int $endTime): int
    {
        $times = $history->get5MinCandleTimes($startTime, $endTime);
        $days = [];
        foreach ($times as $time) {
            $days[date('Y-m-d', $time)] = 1;
        }

        return count($days);
    }
}
