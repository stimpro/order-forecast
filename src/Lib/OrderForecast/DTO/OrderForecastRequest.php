<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\DTO;

class OrderForecastRequest
{
    public const ORDER_TYPE_LONG = 'long';
    public const ORDER_TYPE_SHORT = 'short';

    private string $orderType; // Enum(short, long) - тип сделки, short - продаем акции, long - покупаем акции
    private \DateTime $startDate; // дата выставления заказа
    private int $daysForOrder; // кол-во дней, в течении которых сделка может совершиться, 0 - кол-во не ограничено
    private float $entrypoint; // цена при которой совершаем сделку
    private float $takeProfit; // цена при которой срабатывает Take Profit
    private float $stopLoss; // цена при которой срабатывает Stop Loss
    private bool $checkDayOpen;

    public function __construct(
        string $orderType,
        \DateTime $startDate,
        int $daysForOrder,
        float $entrypoint,
        float $takeProfit,
        float $stopLoss,
        bool $checkDayOpen,
    ) {
        $this->orderType = $orderType;
        $this->startDate = $startDate;
        $this->daysForOrder = $daysForOrder;
        $this->entrypoint = $entrypoint;
        $this->takeProfit = $takeProfit;
        $this->stopLoss = $stopLoss;
        $this->checkDayOpen = $checkDayOpen;
    }

    public function getOrderType(): string
    {
        return $this->orderType;
    }

    public function getStartDate(): \DateTime
    {
        return $this->startDate;
    }

    public function getDaysForOrder(): int
    {
        return $this->daysForOrder;
    }

    public function getEntrypoint(): float
    {
        return $this->entrypoint;
    }

    public function getStopLoss(): float
    {
        return $this->stopLoss;
    }

    public function getTakeProfit(): float
    {
        return $this->takeProfit;
    }

    public function isCheckDayOpen(): bool
    {
        return $this->checkDayOpen;
    }
}
