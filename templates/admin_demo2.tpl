<form class="form-horizontal" method="post">
    <div class='row'>
        <div class='col-md-8 col-md-offset-2'>
            <div class="form-group">
                <label for="invoice_id" class="col-sm-2 control-label">id счета</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name='invoice_id' id="invoice_id" required>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-8">
                    <button type="submit" class="btn btn-primary center-block">
                        <i class='fa fa-floppy-o'></i>
                        &nbsp;&nbsp;Обновить
                    </button>
                </div>
            </div>
            <div id="demo-pdf"></div>
        </div>
    </div>
</form>

<script>
    let b64 = "data:application/pdf;base64,{$pdf}";
    PDFObject.embed(b64, "#demo-pdf");
</script>
