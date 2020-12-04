let app = new Vue({
    el: '#productPage',
    data: {
        products: {

        }
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
        }
    },
    computed: {

    },
    watch: {

    },
    created() {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
        this.getAllProducts();
    },
    mounted() {

    }
})