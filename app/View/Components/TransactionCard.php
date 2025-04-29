<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TransactionCard extends Component
{
    public $transaction;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.transaction-card');
    }

    /**
     * Get the appropriate icon and color based on transaction type and status
     */
    public function iconData()
    {
        if ($this->transaction['status'] === 'failed') {
            return [
                'icon' => 'fas fa-times-circle',
                'bg' => 'bg-danger bg-opacity-10',
                'color' => 'text-danger'
            ];
        }

        if ($this->transaction['type'] === 'credit') {
            return [
                'icon' => 'fas fa-arrow-circle-down',
                'bg' => 'bg-success bg-opacity-10',
                'color' => 'text-success'
            ];
        } else {
            // Debit or other types
            if ($this->transaction['status'] === 'pending') {
                return [
                    'icon' => 'fas fa-arrow-circle-up',
                    'bg' => 'bg-warning bg-opacity-10',
                    'color' => 'text-warning'
                ];
            }
            return [
                'icon' => 'fas fa-arrow-circle-up',
                'bg' => 'bg-danger bg-opacity-10',
                'color' => 'text-danger'
            ];
        }
    }

    public function statusBadge()
    {
        switch (strtolower($this->transaction['status'])) {
            case 'completed':
                return 'bg-success bg-opacity-10 text-success';
            case 'pending':
                return 'bg-warning bg-opacity-10 text-warning';
            case 'failed':
                return 'bg-danger bg-opacity-10 text-danger';
            default:
                return 'bg-secondary bg-opacity-10 text-secondary';
        }
    }

}
