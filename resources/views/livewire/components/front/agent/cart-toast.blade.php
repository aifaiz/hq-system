<div x-data="{tm: $store.toastManager}" class="space-y-4 fixed top-0 right-6 max-w-sm w-3/4 lg:w-full">
    <template x-for="(toast, index) in tm.toasts" :key="toast.id">
        <div 
            x-show="toast.visible" 
            x-transition:leave="transition ease-in duration-1000"
            x-transition:leave-start="opacity-100" 
            x-transition:leave-end="opacity-0" 
            :class="toast.type"
            @click="tm.removeToast(toast.id)"
        >
            <template x-if="toast.type == 'toast-success'">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
            </template>
            <template x-if="toast.type == 'toast-danger'">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
                </svg>
            </template>
            <div class="ps-4 text-sm font-normal" x-text="toast.message"></div>
        </div>
    </template>
</div>

{{-- <div x-data="{cart: $store.cart}" class="space-y-6 fixed top-0 right-6 max-w-sm w-3/4 lg:w-full">
    <template x-if="cart.added">
        <div id="toast-simple" class="flex items-center w-full max-w-md p-4 space-x-4 rtl:space-x-reverse text-green-600 bg-green-200 divide-x rtl:divide-x-reverse divide-green-400 rounded-lg shadow dark:text-green-400 dark:divide-green-700 space-x dark:bg-green-800" role="alert">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
            <span class="sr-only">Check icon</span>
            <div class="ps-4 text-sm font-normal">Item added to cart.</div>
        </div>
    </template>
    <template x-if="cart.noStock">
        <div  id="toast-simple" class="flex items-center w-full max-w-md p-4 space-x-4 rtl:space-x-reverse text-white bg-red-600 divide-x rtl:divide-x-reverse divide-red-200 rounded-lg shadow dark:text-red-400 dark:divide-red-700 space-x dark:bg-red-800" role="alert">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM10 15a1 1 0 1 1 0-2 1 1 0 0 1 0 2Zm1-4a1 1 0 0 1-2 0V6a1 1 0 0 1 2 0v5Z"/>
            </svg>
            <span class="sr-only">Warning icon</span>
            <div class="ps-4 text-sm font-normal">Not enough stock.</div>
        </div>
    </template>
</div> --}}