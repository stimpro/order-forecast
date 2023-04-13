<?php

declare(strict_types=1);

namespace App\Lib\OrderForecast\OrderFlow;

use App\Lib\OrderForecast\DTO\OrderForecastRequest;

class OrderFlowFactory
{
    private OrderFlowLong $longOrderFlow;
    private OrderFlowShort $shortOrderFlow;

    public function __construct(
        OrderFlowLong $longOrderFlow,
        OrderFlowShort $shortOrderFlow,
    ) {
        $this->longOrderFlow = $longOrderFlow;
        $this->shortOrderFlow = $shortOrderFlow;
    }

    public function create(string $orderType): OrderFlowInterface
    {
        return match ($orderType) {
            OrderForecastRequest::ORDER_TYPE_LONG => $this->longOrderFlow,
            OrderForecastRequest::ORDER_TYPE_SHORT => $this->shortOrderFlow,
            default => new \Exception('Unexpected orderType - ' . $orderType),
        };
    }
}
