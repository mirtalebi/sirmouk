<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Invoice;
use App\Models\Scopes\DescOrderScope;

class InvoicesChart extends Component
{



    public function render()
    {

        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $invoices = Invoice::withoutGlobalScope(DescOrderScope::class)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [];

        foreach (range(0, 30) as $i) {
            $day = Carbon::now()->subDays(30 - $i)->toDateString();
            $chartData['labels'][] = jdate($day)->format('l');
            $chartData['data'][] = $invoices->firstWhere('date', $day)->total ?? 0;
        }

        return view('livewire.invoice.invoices-chart', compact('chartData'));
    }
}
