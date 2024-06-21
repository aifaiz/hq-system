<section x-data="{cart: $store.cart}" x-init="cart.items = []" class="bg-white py-8 antialiased dark:bg-gray-900 md:py-16">
    <div class="mx-auto max-w-2xl px-4 2xl:px-0 pb-16">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-2">Thanks for your order!</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-6 md:mb-8">Your order <a href="#" class="font-medium text-gray-900 dark:text-white hover:underline">#{{$order->fpx_ref}}</a> will be processed within 24 hours during working days.</p>

        <div class="space-y-4 sm:space-y-2 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 mb-6 md:mb-8">
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Payment Status</dt>
                <dd class="sm:text-end text-xl font-bold tracking-tighter">
                    @if($order->pay_status == 'PAID')
                    <span class="text-green-500">{{$order->pay_status}}</span>
                    @else
                    <span class="text-red-600">{{$order->pay_status}}</span>
                    @endif
                </dd>
            </dl>
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Date</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{$order->created_at->format('d M Y')}}</dd>
            </dl>
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Name</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{$order->customer_name}}</dd>
            </dl>
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Phone</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">{{$order->customer_phone}}</dd>
            </dl>
            <dl class="sm:flex items-start justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Address</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end whitespace-pre">{{$order->address}}</dd>
            </dl>
        </div>

        <div class="space-y-4 sm:space-y-2 rounded-lg border border-gray-100 bg-gray-50 p-6 dark:border-gray-700 dark:bg-gray-800 mb-6 md:mb-8">
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Sub Total</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">RM {{number_format($order->sub_total, 2,'.',',')}}</dd>
            </dl>
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Shipping</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">RM {{number_format($order->delivery_price, 2,'.',',')}}</dd>
            </dl>
            <dl class="sm:flex items-center justify-between gap-4">
                <dt class="font-normal mb-1 sm:mb-0 text-gray-500 dark:text-gray-400">Total</dt>
                <dd class="font-medium text-gray-900 dark:text-white sm:text-end">RM {{number_format($order->grand_total, 2,'.',',')}}</dd>
            </dl>
        </div>

        <h2 class="text-lg font-semibold text-gray-900 dark:text-white sm:text-xl mb-2">Your Items</h2>

        <div>
            @foreach($items as $item)
            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-4">
                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                    <div class="shrink-0 md:order-1">
                        <img class="rounded-lg w-20 object-cover" src="/{{$item->product->cover_image}}" alt="{{$item->product->name}}" />
                    </div>
                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                        <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white">{{$item->product->name}}</a>
                    </div>
                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                        <div class="flex items-center">
                            <div class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white">Qty: {{$item->qty}}</div>
                        </div>
                        <div class="text-end md:order-4 md:w-32">
                            <p class="text-base font-bold text-gray-900 dark:text-white">RM <span>{{$item->amount}}</span></p>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="w-full flex flex-col lg:flex-row gap-4 items-center text-center">
            @if($order->pay_status == 'PAID')
            <a href="#" class="w-full text-white bg-primary-700 hover:bg-primary-800 focus:ring-4 focus:ring-primary-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-primary-600 dark:hover:bg-primary-700 focus:outline-none dark:focus:ring-primary-800">Download Receipt</a>
            @endif

            <a href="{{$shopUrl}}" class="w-full py-2.5 px-5 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Return to shopping</a>
        </div>
    </div>
</section>