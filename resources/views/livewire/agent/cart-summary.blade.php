<div x-data="{summary: $store.cartSummary, cart: $store.cart}" class="relative">
    <template x-if="summary.isLoading">
        <div class="mx-auto max-w-screen-md px-6">
            <livewire:components.skeleton/>
        </div>
    </template>

    <livewire:components.front.agent.cart-toast />

    <template x-if="!summary.isLoading">
        <section x-show="!summary.isLoading" class="bg-white pt-2 antialiased dark:bg-gray-900 md:pt-4 pb-16">
            <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl mb-6">Checkout Items</h2>

                <livewire:components.front.agent.cart-alert />

                <div class="grid gap-6 lg:grid-cols-3">
                    <div class="lg:col-span-2">
                        <template x-for="(item, idx) in cart.items" :key="idx">
                            <div class="mb-6 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 md:p-6">
                                <div class="space-y-4 md:flex md:items-center md:justify-between md:gap-6 md:space-y-0">
                                    <a href="#" class="shrink-0 md:order-1">
                                        <img x-show="item.image" class="rounded-lg w-20 object-cover" :src="item.image" :alt="item.name" />
                                    </a>

                                    <label for="counter-input" class="sr-only">Choose quantity:</label>
                                    <div class="flex items-center justify-between md:order-3 md:justify-end">
                                        <div class="flex items-center">
                                            <button x-on:click="cart.decreaseQty(item.id)" type="button" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 2">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 1h16" />
                                                </svg>
                                            </button>

                                            <div class="w-10 shrink-0 border-0 bg-transparent text-center text-sm font-medium text-gray-900 focus:outline-none focus:ring-0 dark:text-white" x-text="item.qty"></div>

                                            <button x-on:click="cart.increaseQty(item.id)" type="button" class="inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-md border border-gray-300 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 dark:focus:ring-gray-700">
                                                <svg class="h-2.5 w-2.5 text-gray-900 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16" />
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="text-end md:order-4 md:w-32">
                                            <p class="text-base font-bold text-gray-900 dark:text-white">RM <span x-text="item.total"></span></p>
                                        </div>
                                    </div>

                                    <div class="w-full min-w-0 flex-1 space-y-4 md:order-2 md:max-w-md">
                                        <a href="#" class="text-base font-medium text-gray-900 hover:underline dark:text-white" x-text="item.name"></a>

                                        <div class="flex items-center gap-4">
                                            {{-- <button type="button" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-900 hover:underline dark:text-gray-400 dark:hover:text-white">
                                                <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.01 6.001C6.5 1 1 8 5.782 13.001L12.011 20l6.23-7C23 8 17.5 1 12.01 6.002Z" />
                                                </svg>
                                                Add to Favorites
                                            </button> --}}

                                            <button x-on:click="cart.removeItem(item.id)" type="button" class="inline-flex items-center text-sm font-medium text-red-600 hover:underline dark:text-red-500">
                                                <svg class="me-1.5 h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 17.94 6M18 18 6.06 6" />
                                                </svg>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </template>
                        
                        <livewire:components.front.agent.cart-customer-details />
                    </div><!-- col span 2 -->
                    
                    <livewire:components.front.agent.cart-summary-card :refcode="$refcode" />

                    {{-- <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
                        <form class="space-y-4">
                            <div>
                                <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Do you have a voucher or gift card? </label>
                                <input type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" placeholder="" required />
                            </div>
                            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Apply Code</button>
                        </form>
                    </div> --}}
                </div><!-- grid -->
            </div>
        </section>
    </template>
</div>


@script
<script>
let orderUrl = "{{route('agent.product', ['refcode'=>$refcode])}}"
document.addEventListener('livewire:initialized', async () => {
    let dprice = parseFloat("{{$deliveryPrice}}")
    $store.cart.deliveryPrice = dprice
    //console.log('dprice', dprice)
    //let setItem = await $wire.setItem($store.cart.items)
    //console.log('items', $store.cart.items, setItem)
    //console.log('loading', $store.cartSummary.isLoading)
    //if(setItem){
        setTimeout(()=>{
            $store.cartSummary.isLoading = false
            //console.log('loading', $store.cartSummary.isLoading)
        }, 1000)
    //}

    $watch('$store.cart.removed', (value) =>{
        //console.log('is removed', {value,items:$store.cart.items.length})
        //$wire.setItem($store.cart.items)
        if($store.cart.items.length <= 0){
            //console.log('cart empty', orderUrl)
            Livewire.navigate(orderUrl)
        }

        $store.cart.removed = false
    })
})
</script>
@endscript