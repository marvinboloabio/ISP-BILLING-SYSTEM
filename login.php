<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login - ISP Billing</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #1f1c2c, #928dab);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-container {
      display: flex;
      background: rgba(255, 255, 255, 0.05);
      backdrop-filter: blur(12px);
      -webkit-backdrop-filter: blur(12px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
      width: 100%;
      max-width: 900px;
    }

    .left-panel {
      background-color: rgba(255, 255, 255, 0.05);
      color: #fff;
      padding: 40px;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      text-align: center;
    }

    .left-panel img {
      max-width: 80%;
      margin-bottom: 20px;
    }

    .right-panel {
      background-color: rgba(0, 0, 0, 0.2);
      padding: 40px;
      flex: 1;
      color: #fff;
    }

    .right-panel h4 {
      font-weight: 600;
      margin-bottom: 30px;
    }

    .form-control {
      background: rgba(255, 255, 255, 0.1);
      border: 1px solid rgba(255, 255, 255, 0.2);
      color: #fff;
      border-radius: 8px;
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
      box-shadow: none;
    }

    .form-group label {
      font-size: 0.9rem;
      opacity: 0.85;
    }

    .btn-login {
      background: #0d6efd;
      border: none;
      border-radius: 8px;
      padding: 10px;
      font-weight: 500;
    }

    .btn-login:hover {
      background: #0b5ed7;
    }

    #alert-box .alert {
      margin-bottom: 15px;
    }

    .footer-text {
      margin-top: 20px;
      text-align: center;
      font-size: 0.85rem;
      opacity: 0.6;
    }

.circular-img {
  border-radius: 50%;
  object-fit: contain;  /* show full image */
  width: 150px;
  height: 150px;
  display: block;
  margin: 0 auto;
  background-color: white; /* optional: if you want a background behind */
}

    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
      }

      .left-panel, .right-panel {
        width: 100%;
        padding: 30px;
      }

      .left-panel img {
        max-width: 200px;
      }
    }
  </style>
</head>
<body>

<div class="login-container">
  <div class="left-panel">
    <img src="images/undraw_secure-login_m11a.png" alt="Login Illustration" class="circular-img" />
    <h5>Welcome Back, Admin!</h5>
    <p style="opacity: 0.7;">Secure ISP Billing Portal</p>
  </div>

  <div class="right-panel">
    <h4 class="text-center">Admin Login</h4>
    <div id="alert-box"></div>
    <form id="loginForm">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" class="form-control" placeholder="admin" required autocomplete="off">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" class="form-control" placeholder="••••••••" required>
      </div>
      <button class="btn btn-login btn-block mt-3" type="submit">Login</button>
    </form>
    <div class="footer-text">
      &copy; <?php echo date('Y'); ?> ISP Billing System
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
  $('#loginForm').submit(function(e){
      e.preventDefault();
      $.ajax({
          url: 'ajax/login_process.php',
          method: 'POST',
          data: $(this).serialize(),
          success: function(response){
              if(response.trim() === 'success'){
                  window.location.href = 'modules/dashboard.php';
              } else {
                  $('#alert-box').html('<div class="alert alert-danger">'+response+'</div>');
              }
          }
      });
  });
</script>

</body>
</html>
