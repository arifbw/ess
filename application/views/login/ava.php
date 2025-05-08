<!DOCTYPE html>
<html lang="en" class="no-focus">

<head>
	<link rel="stylesheet" id="css-main" href="<?php echo base_url() . "asset/css/codebase.min.css" ?>">

	<style>
		#overlay {
			margin-top: 40px;
			left: 0;
			top: 0;
			width: 100%;
			z-index: 1;
			color: rgba(209, 206, 209, 0.1);
			line-height: 10px;
			box-shadow: 0 10px 10px rgba(0, 0, 0, 0.7);
			background: rgba(255, 255, 255, 0.6);
			border-radius: 15px;
		}

		#boxlogin {
			margin-right: 120px;
		}

		@media only screen and (max-width: 600px) {
			#boxlogin {
				margin-right: 0px;
			}

			#btn-submit {
				width: 100% !important;
				margin-bottom: 10px;
			}
		}

		body {
			background: url("<?php echo base_url() . "asset/images/img_peruri.jpg" ?>") no-repeat center center fixed;
			-webkit-background-size: cover;
			-moz-background-size: cover;
			-o-background-size: cover;
			background-size: cover;
		}

		.bodys {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			background-color: #ccc0;
		}
	</style>
</head>

<body>
	<div class="row mx-0 justify-content-end">
		<div id="boxlogin" class="hero-static col-lg-6 col-xl-4">
			<div id="overlay">
				<div class="content content-full overflow-hidden">
					<div class="py-30 text-center">
						<img style="width: 60%;" src="<?php echo base_url() . "asset/images/logo-peruri-3.png" ?>">
						<h1 class="h3 font-w700 mt-10 mb-10">ESS</h1>
						<h2 class="h5 font-w400 text-muted mb-0"><b><i>(Employee Self Service)</i></b></h2>
					</div>
					<form class="js-validation-signin" action="<?= base_url('ava/login') ?>" method="post" id="forms" style="box-shadow: 4px 4px 10px grey;">
						<div class="block block-themed block-rounded block-shadow">
							<div class="block-header bg-gd-sea">
								<h3 class="block-title"></h3>
							</div>
							<div class="block-content">

								<input type="hidden" name="ci_csrf_token" value="" />
								<div class="form-group row">
									<div class="col-12">
										<label for="login-username" style="color:#6c757d!important;">Username</label>
										<input type="text" class="form-control" id="username" name="username" autofocus>
									</div>
								</div>
								<div class="form-group row">
									<div class="col-12">
										<label for="login-password" style="color:#6c757d!important;">Password</label>
										<input type="password" class="form-control" id="password" name="password">
									</div>
								</div>
								<!-- Tampilkan angka CAPTCHA di sini -->
								<div class="form-group row">
									<div class="col-12">
										<div class="input-group">
											<input type="text" class="form-control" id="captcha" name="captcha" required style="flex: 5; -webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;">
											<div class="input-group-append" style="flex: 1; display: flex;">
												<span class="input-group-text" id="captcha-addon" style="flex: 1; -webkit-touch-callout: none; -webkit-user-select: none; -khtml-user-select: none; -moz-user-select: none; -ms-user-select: none; user-select: none;">
													<?php echo $this->session->userdata('captcha'); ?>
												</span>
											</div>
										</div>
									</div>
								</div>
								<!-- Display the CAPTCHA error message if set -->
								<?php if ($this->session->flashdata('failed')) : ?>
									<div class="alert alert-danger">
										<?php echo $this->session->flashdata('failed'); ?>
									</div>
								<?php endif ?>
								<div class="form-group row mb-0">
									<div class="col-sm-6 text-sm-right push">
										<button type="submit" class="btn btn-alt-primary" id="btn-submit">
											Login
										</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
