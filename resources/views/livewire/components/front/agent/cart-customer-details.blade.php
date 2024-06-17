<div id="customerDetails" class="min-w-0 flex-1 space-y-8">
    <div class="grid lg:grid-cols-2 gap-6">
        <div class="space-y-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Payment</h3>

            <div class="grid grid-cols-1 gap-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start">
                    <div class="ms-4 text-sm">
                        <label for="dhl" class="font-medium leading-none text-gray-900 dark:text-white">ToyyibPay</label>
                        <p id="dhl-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">FPX Malaysian Online Banking</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Delivery Methods</h3>

            <div class="grid grid-cols-1 gap-4">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 ps-4 dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start">
                    <div class="ms-4 text-sm">
                        <label for="dhl" class="font-medium leading-none text-gray-900 dark:text-white"> RM <span x-text="cart.deliveryPrice"></span> - Own Delivery </label>
                        <p id="dhl-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">Flat rate delivery</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- payment & shipping grid -->
    
    <div class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Delivery Details</h2>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label for="your_name" :class="summary.errorMsg.name ? 'input-label-error' : 'input-label'"> Your name </label>
                <input x-model="summary.form.name" type="text" id="your_name" class="text-input" placeholder="Ahmad Albab" required />
                <p x-show="summary.errorMsg.name" class="mt-2 text-sm text-red-600 dark:text-red-500" x-text="summary.errorMsg.name"></p>
            </div>

            <div>
                <label for="phone-input-3" :class="summary.errorMsg.phone ? 'input-label-error' : 'input-label'"> Phone Number</label>
                <div class="flex items-center">
                    <span class="z-10 inline-flex shrink-0 items-center rounded-s-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-center text-sm font-medium text-gray-900 hover:bg-gray-200 focus:outline-none focus:ring-4 focus:ring-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-700">+6</span>
                    
                    <div class="relative w-full">
                        <input x-model="summary.form.phone" type="text" id="phone-input" class="z-20 block w-full rounded-e-lg border border-s-0 border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:border-s-gray-700  dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500" placeholder="01XXXXXXXX" required />
                    </div>
                </div>
                <p x-show="summary.errorMsg.phone" class="mt-2 text-sm text-red-600 dark:text-red-500" x-text="summary.errorMsg.phone"></p>
            </div>

            <div>
                <label for="your_email" :class="summary.errorMsg.email ? 'input-label-error' : 'input-label'"> Your email</label>
                <input x-model="summary.form.email" type="email" id="your_email" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" placeholder="name@flowbite.com" required />
                <p x-show="summary.errorMsg.email" class="mt-2 text-sm text-red-600 dark:text-red-500" x-text="summary.errorMsg.email"></p>
            </div>

            <div>
                <label for="address" :class="summary.errorMsg.address ? 'input-label-error' : 'input-label'">Address</label>
                <textarea x-model="summary.form.address" id="address" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Your full address"></textarea>
                <p x-show="summary.errorMsg.address" class="mt-2 text-sm text-red-600 dark:text-red-500" x-text="summary.errorMsg.address"></p>
            </div>

        </div>
    </div>

    {{-- <div>
        <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Enter a gift card, voucher or promotional code </label>
        <div class="flex max-w-md items-center gap-4">
        <input type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" placeholder="" required />
        <button type="button" class="flex items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Apply</button>
        </div>
    </div> --}}
</div>