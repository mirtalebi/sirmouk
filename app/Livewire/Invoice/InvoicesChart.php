<?php

namespace App\Livewire\Invoice;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Invoice;

class InvoicesChart extends Component
{



    public function render()
    {

        $startDate = Carbon::now()->subDays(6)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartData = [];

        foreach (range(0, 6) as $i) {
            $day = Carbon::now()->subDays(6 - $i)->toDateString();
            $chartData['labels'][] = jdate($day)->format('l');
            $chartData['data'][] = $invoices->firstWhere('date', $day)->total ?? 0;
        }

        return view('livewire.invoice.invoices-chart', compact('chartData'));
    }
}
