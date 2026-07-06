<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('partials.head')
</head>

<body class="bg-gray-100 text-gray-800 p-6">

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8" id="captureDiv">
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <img src="{{ asset('assets/logo/main.png') }}" alt="Restaurant Logo" class="h-20">
            </div>
            <div class="text-right max-w-xs">
                <h2 class="text-xl font-semibold mb-2">تجمیعی فاکتورها</h2>

                <div class="text-sm mb-2">
                    <span class="font-medium text-gray-600">شناسه‌ها:</span>
                    <div class="flex flex-wrap gap-1 mt-1 justify-start">
                        @foreach ($totals['ids'] as $id)
                            <span
                                class="bg-gray-100 text-gray-800 text-xs px-1.5 py-0.5 rounded border border-gray-200 font-mono">
                                {{ $id }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="text-sm">
                    <span class="font-medium text-gray-600">تاریخ:</span>
                    <div class="text-xs text-gray-700 mt-0.5 leading-relaxed">
                        {{ implode(' ، ', $totals['dates']) }}
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-6">
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th class="p-2 text-right">آیتم</th>
                        <th class="p-2 text-left">تعداد کل</th>
                        <th class="p-2 text-left">قیمت واحد</th>
                        <th class="p-2 text-left">قیمت کل</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumPrice = 0; @endphp
                    @foreach ($mergedProducts as $product)
                        <tr class="border-b">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2 text-left">{{ $product->pivot->quantity }}</td>
                            <td class="p-2 text-left">{{ number_format($product->pivot->unit_price) }}</td>
                            <td class="p-2 text-left">
                                {{ number_format($product->pivot->unit_price * $product->pivot->quantity) }}
                                <span class="text-xs">تومان</span>
                            </td>
                        </tr>
                        @php $sumPrice += $product->pivot->unit_price * $product->pivot->quantity; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="grid w-full">
            <div class="flex justify-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between py-2">
                        <span>مجموع اقلام</span>
                        <span>{{ number_format($sumPrice) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2">
                        <span>مجموع هزینه بسته بندی</span>
                        <span>{{ number_format($totals['packaging_price']) }} تومان</span>
                    </div>
                    @if ($totals['discount_price'] > 0)
                        <div class="flex justify-between py-2 text-green-800 font-bold">
                            <span>مجموع تخفیف</span>
                            <span>{{ number_format(-$totals['discount_price']) }} تومان</span>
                        </div>
                    @endif
                    @if ($totals['tax_price'] > 0)
                        <div class="flex justify-between py-2">
                            <span>مجموع مالیات بر ارزش افزوده</span>
                            <span>{{ number_format($totals['tax_price']) }} تومان</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-2">
                        <span>مجموع هزینه پیک</span>
                        <span>{{ number_format($totals['courier_price']) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2 font-bold text-lg border-t mt-2">
                        <span>مبلغ قابل پرداخت کل</span>
                        <span>{{ number_format($totals['total_price']) }} تومان</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 text-center text-sm text-gray-500">
            <p>قاطی پلو، جادوی برنج و عشق</p>
        </div>
    </div>

    <div class="flex gap-5 w-full justify-center mt-8">
        <button id="captureButton"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            دریافت تصویر فاکتور تجمیعی
        </button>
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">بازگشت</a>
    </div>

    <script src="{{ url('assets/js/html2canvas-pro.min.js') }}"></script>
    <script>
        window.onload = function() {
            const captureButton = document.getElementById('captureButton');
            const captureDiv = document.getElementById('captureDiv');

            captureButton.addEventListener('click', () => {
                html2canvas(captureDiv, {
                    useCORS: true,
                    scale: 2
                }).then(canvas => {
                    const imageDataURL = canvas.toDataURL('image/png');
                    const downloadLink = document.createElement('a');
                    downloadLink.href = imageDataURL;
                    downloadLink.download = 'ghatipolo-aggregate.png';

                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }).catch(error => {
                    console.error('Error capturing the div:', error);
                    alert('Failed to capture the area. Please try again.');
                });
            });
        };
    </script>
</body>

</html>
