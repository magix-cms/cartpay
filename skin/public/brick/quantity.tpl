<div class="modal fade" id="quantity-cart" tabindex="-1" role="dialog" aria-labelledby="modalQuantity" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalQuantity">{#edit_quantity#|ucfirst}</h4>
            </div>
            <form action="" id="form_edit_qty" method="post" class="form-horizontal">
                <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label" for="quantity_qty">{#quantity#|ucfirst} :</label>
                            </div>
                            <div class="col-sm-6">
                                <input class="form-control" type="number" id="quantity_qty" name="quantity_qty" value=""  />
                                <input type="hidden" id="item_qty" name="item_qty" value=""  />
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" id="update_qty" class="btn btn-primary" value="{#edit#|ucfirst}" />
                </div>
            </form>
        </div>
    </div>
</div>











