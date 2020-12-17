<div class="modal fade" id="deleteProduct" tabindex="-1" role="dialog" aria-labelledby="deleteProductLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Are you confirm?</h5>
                <button type="button" class="close" data-dismis="modal" arial-label="close">
                    <span arial-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>If you delete products it will be removed permanently.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="deleteProduct('{{ route('api.product.delete',':id')}}'.replace(':id', product.id))">Remove</button>
            </div>
        </div>
    </div>
</div>