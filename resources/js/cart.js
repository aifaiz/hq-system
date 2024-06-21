document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        added: false,
        noStock: false,
        removed: false,
        deliveryPrice: 10,
        checkoutUrl: Alpine.$persist('#').as('checkoutUrl'),
        items: Alpine.$persist([]).as('cartItem'), // Array to store cart items
        
        addItem(product) {
            // console.log('added ', product)
            let maxQty = parseInt(product.max)
            let addQty = product.qty
            const existingItem = this.items.find(item => item.id === product.id)

            if (existingItem) {
                addQty = existingItem.qty + product.qty

                if(maxQty >= addQty){
                    existingItem.qty += product.qty; // Update quantity if product already exists
                    existingItem.total = existingItem.qty * parseFloat(product.price)
                    Alpine.store('toastManager').addToast('Item added to cart')
                }else{
                    // console.log('product '+ product.name+' not enough stock')
                    Alpine.store('toastManager').addToast('Not enough stock', 'toast-danger')
                }
                // console.log('existing qty', existingItem.qty)
            } else if(maxQty >= addQty) {
                product.total = parseFloat(product.price)
                this.items.push(product); // Add new product
                Alpine.store('toastManager').addToast('Item added to cart')
            }else{
                // this.noStock = true
                Alpine.store('toastManager').addToast('Not enough stock', 'toast-danger')
            }

            // console.log('stock', {maxQty,addQty})


            // setTimeout(()=>{
            //     this.added = false
            //     this.noStock = false
            // }, 2000)

            // console.log('cart', this.items)
        },

        removeItem(productId) {
            this.items = this.items.filter(item => item.id !== productId);
            // console.log('product removed', productId)
            this.removed = true
        },

        increaseQty(productId) {
            const item = this.items.find(item => item.id === productId);
            if (item) {
                let maxQty = parseInt(item.max)
                let addedQty = item.qty + 1
                if(maxQty >= addedQty){
                    item.qty++;
                    item.total = item.qty * parseFloat(item.price)
                }else{
                    Alpine.store('toastManager').addToast('Not enough stock', 'toast-danger')
                }
                // console.log('+ rm', item.total)

                setTimeout(()=>{
                    // this.noStock = false
                }, 2000)
            }
        },

        decreaseQty(productId) {
            const item = this.items.find(item => item.id === productId);
            if (item && item.qty > 1) {
                item.qty--;
                item.total = item.qty * parseFloat(item.price)
            } else {
                this.removeItem(productId); // Optionally remove item if qty is 0
            }
        },
        
        get totalItems() {
            return this.items.length;
        },

        get subTotal() {
            return this.items.reduce((sum, item) => sum + parseFloat(item.price) * item.qty, 0);
        },
        
        get totalPrice() {
            let subTotal = this.subTotal
            let totalPrice = 0

            if(subTotal > 0) {
                totalPrice = this.deliveryPrice + subTotal
            }
            // console.log('subtotal', subTotal)

            return totalPrice
        },

        goCheckout(url) {
            if(this.totalItems <= 0){
                Alpine.store('toastManager').addToast('Cart is empty', 'toast-danger')
                return false
            }

            // console.log('url', url)
            window.location = url
        }
    });

    
});