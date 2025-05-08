<link href="<?= base_url('asset/css/') ?>bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

<div class="container">
    <div class="row justify-content-center align-items-center" style="margin-top: 75px;">
        <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
            <div class="card">
                <div class="card-body">
                    <form method="post" action="" autocomplete="off">
                        <div class="form-group">
                            <input type="text" class="form-control" name="username" placeholder="Username" autofocus>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <button type="submit" id="sendlogin" class="btn btn-primary">LOGIN</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>