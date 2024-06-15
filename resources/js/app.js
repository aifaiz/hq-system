import './bootstrap';
import 'flowbite';
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

document.addEventListener('alpine:init', () => {
    Alpine.store('cart', {
        added: false,
        removed: false,
        deliveryPrice: 10,
        items: Alpine.$persist([]).as('cartItem'), // Array to store cart items
        
        addItem(product) {
            // console.log('added ', product)
            const existingItem = this.items.find(item => item.id === product.id);
            if (existingItem) {
                existingItem.qty += product.qty; // Update quantity if product already exists
                existingItem.total = existingItem.qty * parseFloat(product.price)
            } else {
                product.total = parseFloat(product.price)
                this.items.push(product); // Add new product
            }

            this.added = true

            setTimeout(()=>{
                this.added = false
            }, 2000)

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
                item.qty++;
                item.total = item.qty * parseFloat(item.price)
                // console.log('+ rm', item.total)
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
        form: Alpine.$persist({
            name: '',
            phone: '',
            email: '',
            address: ''
        }).as('customerDetails'),

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
        }

        //end validations. return false if invalid
    });
});

Livewire.start()