

let app = new Vue({
    el: '#productPage',
    data: {
        products: [],
        product: {
            title: '',
            category_id: '',
            sku: '',
            description: '',
            status: 1,
            feature_image: '',
        },
        file: '',
        files: [],
    },
    methods: {
        getProductList() {
            axios.get(ProductListRoute).then((response) => {
                if (response.status === 200) {
                    this.products = response.data.data;
                } else {
                    toastr.error(response.data.message);
                }
            })
                .catch(error => {
                    toastr.error(error.message)
                })
        },
        StoreProduct(route) {

            let formData = new FormData();
            formData.append('category_id', this.product.category_id);
            formData.append('title', this.product.title);
            formData.append('status', this.product.status);
            formData.append('sku', this.product.sku);
            formData.append('description', this.product.description);
            formData.append('feature_image', this.product.feature_image);

            axios.post(route, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then((response) => {
                if (response.status === 201) {
                    this.resetProduct();
                    toastr.success(response.data.message);
                } else {
                    toastr.error(response.data.message);
                }
            })
                .catch(e => {
                    console.log(e.message);
                });
        },

        resetProduct() {
            this.product = {
                title: '',
                category_id: '',
                sku: '',
                description: '',
                status: 1,
            }
        },
        handleFeatureImage() {

            this.file = this.$refs.featureImage.files[0];
        }
    },
    computed: {

    },
    watch: {

    },
    created() {
        axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    },
    mounted() {

    }
}); 