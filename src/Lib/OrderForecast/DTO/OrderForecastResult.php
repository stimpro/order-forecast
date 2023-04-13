<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\DTO;

class OrderForecastResult
{
    public const ORDER_STATUS_FAIL = 'fail'; // цена не достигла entrypointValue
    public const ORDER_STATUS_WAIT = 'wait'; // цена не достигла ни stopLoss, ни takeProfit
    public const ORDER_STATUS_STOP_LOSS = 'stopLoss';
    public const ORDER_STATUS_TAKE_PROFIT = 'takeProfit';

    private ?\DateTime $openDate = null;
    private ?\DateTime $closeDate = null;
    private string $status = self::ORDER_STATUS_FAIL;
    private ?int $openOrderDayNumber = null;
    private ?int $closeOrderDayNumber = null;

    public function setOpenDate(\DateTime $openDate): void
    {
        $this->openDate = $openDate;
    }

    public function getOpenDate(): ?\DateTime
    {
        return $this->openDate;
    }

    public function setCloseDate(?\DateTime $closeDate): void
    {
        $this->closeDate = $closeDate;
    }

    public function getCloseDate(): ?\DateTime
    {
        return $this->closeDate;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setOpenOrderDayNumber(?int $openOrderDayNumber): void
    {
        $this->openOrderDayNumber = $openOrderDayNumber;
    }

    public function getOpenOrderDayNumber(): ?int
    {
        return $this->openOrderDayNumber;
    }

    public function setCloseOrderDayNumber(?int $closeOrderDayNumber): void
    {
        $this->closeOrderDayNumber = $closeOrderDayNumber;
    }

    public function getCloseOrderDayNumber(): ?int
    {
        return $this->closeOrderDayNumber;
    }
}
