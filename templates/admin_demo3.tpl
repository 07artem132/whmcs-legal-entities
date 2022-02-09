<form class="form-horizontal" method="post">
    <div class='row'>
        <div class='col-md-8 col-md-offset-2'>
            <div class="form-group">
                <label for="client_id" class="col-sm-2 control-label">id клиента</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name='client_id' id="client_id" required>
                </div>
                <label for="start_date" class="col-sm-2 control-label">Дата начала</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" name='start_date' id="start_date" required>
                </div>
                <label for="end_date" class="col-sm-2 control-label">Дата окончания</label>
                <div class="col-sm-9">
                    <input type="date" class="form-control" name='end_date' id="end_date" required>
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
