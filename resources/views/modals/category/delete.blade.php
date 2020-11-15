<div class="modal fade" id="deleteCategory" tabindex="-1" role="dialog" aria-labelledby="deleteCategoryLabel" arial-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteCategoryLabel">Are you sure !</h5>
                <button type="button" class="close" data-dismiss="modal" arial-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
               <p>If you delete category it will be removed permanently</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button data-dismiss="modal" type="button" class="btn btn-info" @click="deleteCategory('{{route('categories.delete',':id')}}'.replace(':id',category.id))">Delete</button>
            </div>
        </div>
    </div>
</div>
