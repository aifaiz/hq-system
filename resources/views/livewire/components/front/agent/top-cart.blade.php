<div x-data="{cart: $store.cart}" id="myCartDropdown1" class="hidden z-10 mx-auto max-w-sm space-y-4 overflow-hidden rounded-lg bg-white p-4 antialiased shadow-lg dark:bg-gray-800">
    <template x-for="item in cart.items" :key="item.id">
    <div class="grid grid-cols-2">
        <div>
            <a href="#" class="truncate text-sm font-semibold leading-none text-gray-900 dark:text-white hover:underline" x-text="item.name"></a>
            <p class="mt-0.5 truncate text-sm font-normal text-gray-500 dark:text-gray-400">RM <span x-text="item.total"></span></p>
        </div>

        <div class="flex items-center justify-end gap-6">
            <p class="text-sm font-normal leading-none text-gray-500 dark:text-gray-400">Qty: <span x-text="item.qty"></span></p>

            <button x-on:click="cart.removeItem(item.id)" data-tooltip-target="tooltipRemoveItem1a" type="button" class="text-red-600 hover:text-red-700 dark:text-red-500 dark:hover:text-red-600">
                <span class="sr-only"> Remove </span>
                <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                    <path fill-rule="evenodd" d="M2 12a10 10 0 1 1 20 0 10 10 0 0 1-20 0Zm7.7-3.7a1 1 0 0 0-1.4 1.4l2.3 2.3-2.3 2.3a1 1 0 1 0 1.4 1.4l2.3-2.3 2.3 2.3a1 1 0 0 0 1.4-1.4L13.4 12l2.3-2.3a1 1 0 0 0-1.4-1.4L12 10.6 9.7 8.3Z" clip-rule="evenodd" />
                </svg>
            </button>
            <div id="tooltipRemoveItem1a" role="tooltip" class="tooltip invisible absolute z-10 inline-block rounded-lg bg-gray-900 px-3 py-2 text-sm font-medium text-white opacity-0 shadow-sm transition-opacity duration-300 dark:bg-gray-700">
                Remove item
                <div class="tooltip-arrow" data-popper-arrow></div>
            </div>
        </div>
    </div>
    </template>

    <div x-show="cart.items.length === 0">
        <p>Cart is empty.</p>
    </div>

    @if ($enableOrder == 'YES')
        <a x-show="cart.items.length > 0" href="#" title="" class="mb-2 me-2 inline-flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800" role="button"> Proceed to Checkout </a>
    @endif
</div>