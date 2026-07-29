<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ParaBite</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: drift 60s linear infinite;
        }
        @keyframes drift {
            from { transform: translate(0, 0); }
            to { transform: translate(50px, 50px); }
        }
        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 900px;
            margin: auto;
            display: flex;
            background: white;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 30px 90px rgba(0,0,0,0.3);
        }
        .login-left {
            flex: 1;
            padding: 60px 50px;
            background: linear-gradient(180deg, #1a1d29 0%, #2d1b69 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        .login-left .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 40px;
        }
        .login-left .brand-icon {
            width: 54px;
            height: 54px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        .login-left .brand-text {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .login-left h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 16px;
            line-height: 1.2;
        }
        .login-left p {
            font-size: 16px;
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 30px;
        }
        .login-left .features {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }
        .login-left .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .login-left .feature-item i {
            width: 32px;
            height: 32px;
            background: rgba(255,255,255,0.12);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }
        .login-right {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .login-right h2 {
            font-size: 28px;
            font-weight: 800;
            color: #1a1d29;
            margin-bottom: 8px;
        }
        .login-right .subtitle {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            display: block;
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102,126,234,0.1);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102,126,234,0.4);
        }
        .alert {
            border: none;
            border-radius: 12px;
            padding: 14px 16px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        .alert ul {
            margin: 0;
            padding-left: 20px;
        }
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                margin: 20px;
            }
            .login-left {
                padding: 40px 30px;
            }
            .login-right {
                padding: 40px 30px;
            }
            .login-left h1 {
                font-size: 28px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="brand">
                <div class="brand-icon">
                    <i class="bi bi-shop"></i>
                </div>
                <div class="brand-text">ParaBite</div>
            </div>
            <h1>Manage your food delivery platform</h1>
            <p>Access powerful admin tools to manage users, merchants, orders and more.</p>
            <div class="features">
                <div class="feature-item">
                    <i class="bi bi-speedometer2"></i>
                    <span>Real-time analytics dashboard</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-people"></i>
                    <span>User & merchant management</span>
                </div>
                <div class="feature-item">
                    <i class="bi bi-shield-check"></i>
                    <span>Secure authentication system</span>
                </div>
            </div>
        </div>
        <div class="login-right">
            <h2>Welcome back</h2>
            <p class="subtitle">Enter your credentials to access admin panel</p>

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/login">
                @csrf
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="admin@parabite.com" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-login">
                    Sign In to Admin Panel
                </button>
            </form>
        </div>
    </div>
</body>
</html>
