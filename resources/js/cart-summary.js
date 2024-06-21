document.addEventListener('alpine:init', () => {
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

            let items = Alpine.store('cart').items.length
            if(items <= 0){
                Alpine.store('toastManager').addToast('No items in cart', 'toast-danger')
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
                    if(url != false){
                        this.processingMsg = 'redirecting to payment...'
                        window.location = url
                    }else{
                        this.processingMsg = ''
                        this.isProcessing = false
                        Alpine.store('toastManager').addToast('Could not process cart. Please try again later', 'toast-danger')
                    }

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
})