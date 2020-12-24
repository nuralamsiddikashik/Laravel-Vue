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

        updateProduct(route) {
            axios.put(route, {
                title: this.product.title,
                slug: this.product.slug,
                sku: this.product.sku,
                category_id: this.product.category_id,
            }).then((response) => {
                if (response.status === 201) {
                    this.resetProduct();
                    this.getAllProducts();
                    toastr.success(response.data.message);
                } else {
                    this.resetProduct();
                    toastr.error(response.data.message);
                }
            }).catch(error => {
                this.resetProduct();
                toastr.error(error.message);
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
                title: '',
                sku: '',
                category_id: '',
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