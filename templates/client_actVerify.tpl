<div class="table-container clearfix">
    <form class="form-horizontal" method="get">
        <input type="hidden" value="LegalEntities" name="m">
        <input type="hidden" value="verify" name="acts">
        <div class='row'>
            <div class='col-md-8 col-md-offset-2'>
                <div class="form-group">
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
                            &nbsp;&nbsp;Сформировать
                        </button>
                    </div>
                </div>
                <div id="demo-pdf"></div>
            </div>
        </div>
    </form>
</div>