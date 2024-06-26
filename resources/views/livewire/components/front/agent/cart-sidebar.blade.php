<div x-data="{cart: $store.cart}" class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
    <p class="text-xl font-semibold text-gray-900 dark:text-white">Order summary</p>

    <div class="space-y-4">
    <div class="space-y-2">
        {{-- <dl x-show="cart.items.length > 0" class="flex items-center justify-between gap-4">
            <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Delivery</dt>
            <dd class="text-base font-medium text-gray-900 dark:text-white">RM 10</dd>
        </dl> --}}

        <dl class="flex items-center justify-between gap-4">
        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Sub Total</dt>
        <dd class="text-base font-medium text-gray-900 dark:text-white">RM <span x-text="cart.subTotal"></span></dd>
        </dl>

        {{-- <dl class="flex items-center justify-between gap-4">
        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Savings</dt>
        <dd class="text-base font-medium text-green-600">-$299.00</dd>
        </dl> --}}

        {{-- <dl class="flex items-center justify-between gap-4">
        <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Tax</dt>
        <dd class="text-base font-medium text-gray-900 dark:text-white">$799</dd>
        </dl> --}}
    </div>

    <dl class="flex items-center justify-between gap-4 border-t border-gray-200 pt-2 dark:border-gray-700">
        <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
        <dd class="text-base font-bold text-gray-900 dark:text-white">RM <span x-text="cart.subTotal"></span></dd>
    </dl>
    </div>

    <button x-on:click="cart.goCheckout('{{$checkoutUrl}}')" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Proceed to Checkout</button>

    <div class="flex items-center justify-center gap-2">
    {{-- <span class="text-sm font-normal text-gray-500 dark:text-gray-400"> or </span>
    <a href="#" title="" class="inline-flex items-center gap-2 text-sm font-medium text-primary-700 underline hover:no-underline dark:text-primary-500">
        Continue Shopping
        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5m14 0-4 4m4-4-4-4" />
        </svg>
    </a> --}}
    </div>
</div>
