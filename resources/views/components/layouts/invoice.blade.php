<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    @include('partials.head')

</head>

<body class="bg-gray-100 text-gray-800 p-6">

    <div class="max-w-3xl mx-auto bg-white shadow-lg rounded-2xl p-8" id="captureDiv">
        <!-- Header -->
        <div class="flex justify-between items-center border-b pb-4 mb-6">
            <div>
                <img src="{{ asset('assets/logo/main.png') }}" alt="Restaurant Logo" class="h-20">
            </div>
            <div class="text-right">
                <h2 class="text-xl font-semibold">فاکتور</h2>
                <p class="text-sm">شناسه: #{{ $invoice->id }}</p>
                <p class="text-sm">تاریخ: {{ $invoice->getCreatedAtDate() }}</p>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="mb-6 flex">
            <div class="grow">
                <h3 class="text-lg font-semibold">مشتری: @if ($invoice->is_snap)
                        <span class="text-gray-500 font-bold mr-2 text-sm">(اسنپ)</span>
                    @endif
                </h3>
                @if ($invoice->is_snap)
                    <p class="text-sm">{{ json_decode($invoice->snap_user_credentials, true)['username'] }} </p>
                    <p class="text-sm">{{ json_decode($invoice->snap_user_credentials, true)['mobile'] }}</p>
                @else
                    <p class="text-sm">{{ $invoice->user->name }}</p>
                    <p class="text-sm">{{ $invoice->user->mobile }}</p>
                @endif
            </div>

            <div class="text-center">
                <h3 class="text-lg font-semibold">شماره کارت جهت پرداخت</h3>
                <p dir="ltr" class="font-bold">6063 - 7310 - 4039 - 0230</p>
                <p class="text-sm">سعید آشوری</p>
            </div>
        </div>

        <!-- Order Details -->
        <div class="mb-6">
            <table class="w-full table-auto text-sm">
                <thead class="bg-gray-200 text-gray-600">
                    <tr>
                        <th class="p-2 text-right">آیتم</th>
                        <th class="p-2 text-left">تعداد</th>
                        <th class="p-2 text-left">قیمت واحد</th>
                        <th class="p-2 text-left">قیمت کل</th>
                    </tr>
                </thead>
                <tbody>
                    @php $sumPrice = 0; @endphp
                    @foreach ($invoice->products as $product)
                        <tr class="border-b">
                            <td class="p-2">{{ $product->name }}</td>
                            <td class="p-2 text-left">{{ $product->pivot->quantity }}</td>
                            <td class="p-2 text-left">{{ number_format($product->pivot->unit_price) }}</td>
                            <td class="p-2 text-left">
                                {{ number_format($product->pivot->unit_price * $product->pivot->quantity) }} <span
                                    class="text-xs">تومان</span></td>
                        </tr>
                        @php $sumPrice += $product->pivot->unit_price * $product->pivot->quantity; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <div class="grid w-full">
            <div class="text-sm">
                @if(!empty($invoice->address_id))
                    آدرس: <span class="font-bold">{{ $invoice->address->address }}</span>
                @endif
            </div>
            <div class="flex justify-end">
                <div class="w-full max-w-xs">
                    <div class="flex justify-between py-2">
                        <span>مجموع</span>
                        <span>{{ number_format($sumPrice) }} تومان</span>
                    </div>
                    @if ($invoice->discount_price > 0)
                        <div class="flex justify-between py-2 text-green-800 font-bold">
                            <span>تخفیف</span>
                            <span class="">{{ number_format(-$invoice->discount_price) }} تومان</span>
                        </div>
                        @php $sumPrice -=  $invoice->discount_price; @endphp
                    @endif
                    @if ($invoice->calcTaxPrice() > 0)
                        <div class="flex justify-between py-2">
                            <span>مالیات بر ارزش افزوده</span>
                            <span>{{ number_format($invoice->calcTaxPrice()) }} تومان</span>
                        </div>
                    @endif
                    <div class="flex justify-between py-2">
                        <span>هزینه پیک</span>
                        <span>{{ number_format($invoice->courier_price) }} تومان</span>
                    </div>
                    <div class="flex justify-between py-2 font-bold text-lg border-t mt-2">
                        <span>مبلغ قابل پرداخت</span>
                        <span>{{ number_format($invoice->calcFinalPrice()) }} تومان</span>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>قاطی پلو، جادوی برنج و عشق</p>
            {{-- <p>درصورت مغایرت صورتحسا</p> --}}
        </div>
    </div>

    <div class="flex gap-5 w-full justify-center mt-8">
        <button id="captureButton"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
            دریافت تصویر فاکتور
        </button>
        <a href="{{ url()->previous() }}" class="text-blue-600 hover:underline">بازگشت</a>
    </div>


    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
        integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script> --}}
    <script src="{{ url('assets/js/html2canvas-pro.min.js') }}"></script>
    <script>
        // Ensure the DOM is fully loaded before running the script
        window.onload = function() {
            const captureButton = document.getElementById('captureButton');
            const captureDiv = document.getElementById('captureDiv');

            captureButton.addEventListener('click', () => {
                html2canvas(captureDiv, {
                    useCORS: true,
                    scale: 2
                }).then(canvas => {
                    // Convert the canvas content to a data URL (PNG format)
                    const imageDataURL = canvas.toDataURL('image/png');

                    // Create a temporary anchor (<a>) element
                    const downloadLink = document.createElement('a');
                    // Set the href attribute to the image data URL
                    downloadLink.href = imageDataURL;
                    // Set the download attribute to specify the filename
                    downloadLink.download = 'ghatipolo-invoice-{{ $invoice->id }}.png';

                    // Programmatically click the link to trigger the download
                    document.body.appendChild(downloadLink); // Append to body to ensure it's in the DOM
                    downloadLink.click();
                    document.body.removeChild(downloadLink); // Remove the link after clicking
                }).catch(error => {
                    // Log any errors that occur during the capture process
                    console.error('Error capturing the div:', error);
                    // You could also display a user-friendly message here
                    alert('Failed to capture the area. Please try again.');
                });
            });
        };
    </script>

</body>

</html>
