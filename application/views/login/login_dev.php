
<link href="<?= base_url('asset/css/') ?>bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background: #f6fefe;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        height: 100vh;
        margin: 0;
    }

    .login-wrapper {
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
        background: linear-gradient(to right, #A7D1E5, #f6fefe);
    }

    .login-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        display: flex;
        max-width: 900px;
        width: 100%;
        overflow: hidden;
    }

    .login-left {
        background-color: #A7D1E5;
        color: white;
        padding: 40px;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .login-left h1 {
        transform: rotate(-90deg);
        white-space: nowrap;
        font-size: 36px;
        opacity: 0.2;
    }

    .login-left img {
        width: 100%;
        max-width: 350px;
        margin-top: 20px;
    }

    .login-right {
        flex: 1;
        padding: 60px 40px;
    }

    .login-right h2 {
        color: #A7D1E5;
        margin-bottom: 30px;
        font-weight: bold;
    }

    .form-control {
        border-radius: 30px;
        padding: 10px 20px;
    }

    .btn-login {
        background-color: #A7D1E5;
        border: none;
        border-radius: 30px;
        padding: 10px 20px;
        width: 100%;
        font-weight: bold;
        color: white;
    }

    .login-links {
        text-align: right;
        margin-top: 15px;
    }

    .login-links a {
        color: #A7D1E5;
        font-size: 0.9rem;
        text-decoration: none;
        margin-left: 15px;
    }

    @media (max-width: 768px) {
        .login-card {
            flex-direction: column;
        }

        .login-left h1 {
            transform: none;
            text-align: center;
        }
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <div class="login-left text-center">
            <img src="https://i.pinimg.com/originals/42/36/d0/4236d00b6df31c5c1dab3566fa61ff3c.gif" alt="Analytics Illustration">
        </div>
        <div class="login-right">
            <h2>Employee Self Service</h2>
            <form method="post" action="" autocomplete="off">
                <div class="form-group mb-3">
                    <input type="text" class="form-control" name="username" placeholder="Username" autofocus>
                </div>
                <div class="form-group mb-4">
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>
                <button type="submit" id="sendlogin" class="btn btn-login">Login</button>
                <div class="login-links">
                    <a href="#">Forgot</a>
                    <a href="#">Help</a>
                </div>
            </form>
        </div>
    </div>
</div>
