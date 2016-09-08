<div class="modal fade" id="attr-cart" tabindex="-1" role="dialog" aria-labelledby="modalQuantity" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="modalAttr">{#edit_attr#|ucfirst}</h4>
            </div>
            <form action="" id="form_edit_attr" method="post" class="form-horizontal">
                <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <label class="control-label" for="quantity_attr">{#attr_cart#|ucfirst} :</label>
                            </div>
                            <div class="col-sm-6">
                                <select name="attr" class="form-control select"></select>
                                <input type="hidden" id="item_attr" name="item_attr" value=""  />
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{#cancel#|ucfirst}</button>
                    <input type="submit" id="update_attr" class="btn btn-box btn-flat btn-dark-theme" value="{#edit#|ucfirst}" />
                </div>
            </form>
        </div>
    </div>
</div>











