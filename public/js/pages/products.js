let app = new Vue({
    el: '#productPage',
    data: {
        products: {},
        product: {},
        categories: []
    },

    methods: {
        getAllProducts() {
            let route = ProductListRoute;
            axios.get(route)
                .then((response) => {
                    if (response.status === 200) {
                        this.products = response.data.data;
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(error.message);
                })
        },
        selectProduct(product) {
            this.product = product;
        },
        getCategoryList() {
            axios.get(CategoryListRoute)
                .then((response) => {
                    if (response.status === 200) {
                        this.categories = response.data.data;
                    } else {
                        toastr.error(response.data.message);
                    }
                })
                .catch(error => {
                    toastr.error(error.message)
                })
        },
        deleteProduct(route) {
            axios.delete(route)
                .then((response) => {
                    if (response.status === 204) {
                        this.getAllProducts();
                        toastr.success(response.data.message);
                    } else {
                        toastr.error(response.data.message);
                    }
                }).catch(error => {

                    toastr.error(error.message);
                })
        },
        resetProduct() {
            this.product = {

            }
        }

    },
    computed: {

    },
    watch: {

    },
    created() {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        this.getAllProducts();
        this.getCategoryList();
    },
    mounted() {

    }
})