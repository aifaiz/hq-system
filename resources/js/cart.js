document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        added: false,
        noStock: false,
        removed: false,
        deliveryPrice: 10,
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
        }
    });

    Alpine.store('cartSummary', {
        isLoading: true,
        isProcessing: false,
        processingMsg: '',
        hasAlert: false,
        oosProduct: [],
        form: Alpine.$persist({
            name: '',
            phone: '',
            email: '',
            address: ''
        }).as('customerDetails'),
        errorMsg: {
            name: '',
            phone: '',
            email: '',
            address: ''
        },

        // validations. return false if invalid
        validateEmail(email){
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            let isValid = emailRegex.test(email)
            if(!isValid){
                return false
            }

            return true
        },

        validatePhone(phone){
            const regex = /^01\d{8,}$/;
            if (!regex.test(phone)) {
                return false
            }
            return true
        },

        validateEmpty(value){
            if(value == ''){
                return false
            }

            return true
        },

        validateForm(){
            let error = false

            this.errorMsg.name = ''
            this.errorMsg.phone = ''
            this.errorMsg.email = ''
            this.errorMsg.address = ''

            let nameValid = this.validateEmpty(this.form.name)
            if(!nameValid){
                this.errorMsg.name = 'required.'
                error = true
            }

            let phoneValid = this.validatePhone(this.form.phone)
            if(!phoneValid){
                this.errorMsg.phone = 'required. number only. 01XXXXXXXX'
                error = true
            }

            let emailValid = this.validateEmail(this.form.email)
            let emailEmpty = this.validateEmpty(this.form.email)
            if(!emailValid || !emailEmpty){
                this.errorMsg.email = 'invalid email.'
                error = true
            }

            let addressValid = this.validateEmpty(this.form.address)
            if(!addressValid){
                this.errorMsg.address = 'required'
                error = true
            }

            return error
        },

        async processCart(wireID){
            this.isProcessing = true

            let error = this.validateForm()

            if(!error){
                this.processingMsg = 'processing cart...'
                // console.log('wire', wireID)
                let component = Livewire.find(wireID)
                let items = Alpine.store('cart').items
                // console.log('before items', items)
                let setThings = await component.call('setItems', items, this.form)
                // console.log('set', setThings)
                if(setThings.status){
                    // let getItems = await component.call('getItems')
                    // console.log('proc', {setItems, getItems})
                    let url = await component.call('processCart')
                    // console.log('url', url)
                    
                    this.processingMsg = 'redirecting to payment...'

                    //clear cart
                    // Alpine.store('cart').items = []

                    //redirect here
                }else{
                    // this.processingMsg = 'redirecting to payment...'
                    // console.log('some items are out of stock')
                    this.oosProduct = setThings.outOfStock
                    this.hasAlert = true
                    this.isProcessing = false
                    error = true
                    // console.log('after items', setThings.items)
                    Alpine.store('cart').items = setThings.items

                    document.querySelector('#summaryAlert').scrollIntoView({
                        behavior: 'smooth'
                    });   
                }
            }else{
                document.querySelector('#customerDetails').scrollIntoView({
                    behavior: 'smooth'
                });   
                this.isProcessing = false
            }
            
            // console.log('error', error)

        }

        //end validations. return false if invalid
    });
});