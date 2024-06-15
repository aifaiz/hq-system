<section class="bg-white pb-16 antialiased dark:bg-gray-900">
  <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white sm:text-2xl">Ahmad Agent Shop</h2>

    <div x-data="{cart: $store.cart}" class="fixed top-14 right-6">
      <livewire:components.front.agent.cart-toast />
    </div>

    <div class="mt-6 sm:mt-8 md:gap-6 lg:flex lg:items-start xl:gap-8">
      <div class="mx-auto w-full flex-none lg:max-w-2xl xl:max-w-4xl">
        <div class="grid lg:grid-cols-3 gap-4">
          @foreach($products as $p)
            <livewire:components.front.agent.product-card 
              wire:key="product_{{$p->id}}" 
              productid="{{$p->id}}"
              name="{{$p->name}}"
              description="{{$p->description}}"
              price="{{$p->price}}"
              enableorder="{{$enableOrder}}"
              image="{{$p->cover_image}}"
            />
          @endforeach
        </div>
      </div>

      <div class="mx-auto mt-6 max-w-4xl flex-1 space-y-6 lg:mt-0 lg:w-full">
        @if($enableOrder == 'YES')
        <livewire:components.front.agent.cart-sidebar :refcode="$refcode" />
        @endif

        {{-- <div class="space-y-4 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
          <form class="space-y-4">
            <div>
              <label for="voucher" class="mb-2 block text-sm font-medium text-gray-900 dark:text-white"> Do you have a voucher or gift card? </label>
              <input type="text" id="voucher" class="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder:text-gray-400 dark:focus:border-primary-500 dark:focus:ring-primary-500" placeholder="" required />
            </div>
            <button type="submit" class="flex w-full items-center justify-center rounded-lg bg-primary-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4 focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Apply Code</button>
          </form>
        </div> --}}
      </div>
    </div>
  </div>
</section>
