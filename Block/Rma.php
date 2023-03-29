<?php

namespace Aize\Rma\Block;

use Magento\Sales\Block\Order\Info\Buttons;

class Rma extends Buttons
{
    /**
     * Check if the RMA Request button should be displayed
     *
     * @return bool
     */
    public function isRmaAvailable(): bool
    {
        // Implement the logic to determine if the RMA button should be displayed.
        // For now, let's assume it should always be displayed.
        return true;
    }
}
