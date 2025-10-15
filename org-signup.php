<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
startSecureSession();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edu Bridge - Organization Login</title>
    <style>
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 5px 15px rgba(74,144,226,0.4); }
            50% { box-shadow: 0 5px 25px rgba(74,144,226,0.6); }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, #f5f7fa, #c3cfe2, #a8b8d8, #c3cfe2, #f5f7fa);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden;
        }

        header {
            background: rgba(255,255,255,0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.8s ease-out;
        }

        .logo {
            font-size: 1.8em;
            color: #4a90e2;
            font-weight: bold;
            letter-spacing: 2px;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .logo:hover { color: #357abd; }

        .back-btn {
            padding: 10px 20px;
            border: 2px solid #4a90e2;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            background: transparent;
            color: #4a90e2;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .back-btn:hover {
            background: #4a90e2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74,144,226,0.3);
        }

        .login-section {
            text-align: center;
            padding: 100px 20px;
            max-width: 500px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out 0.2s both;
        }
        .login-section h1 {
            color: #4a90e2;
            font-size: 2.5em;
            margin-bottom: 15px;
        }
        .login-section p {
            color: #666;
            font-size: 1.1em;
            margin-bottom: 40px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
        }
        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }

        .form-group label {
            display: block;
            color: #4a90e2;
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 1em;
        }
        .form-group input {
            width: 100%;
            padding: 15px;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
        }
        .form-group input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 10px rgba(74,144,226,0.3);
            transform: translateY(-2px);
            outline: none;
        }

        .login-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 10px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            color: white;
            background: linear-gradient(135deg, #4a90e2, #357abd);
            box-shadow: 0 5px 15px rgba(74,144,226,0.4);
            animation: pulseGlow 2s ease-in-out infinite;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .login-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(74,144,226,0.5);
        }

        .error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
        }

        .switch-link {
            margin-top: 30px;
            color: #666;
            font-size: 1em;
        }
        .switch-link a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: bold;
        }
        .switch-link a:hover { text-decoration: underline; }

        @media (max-width: 768px) {
            header { padding: 10px 20px; flex-wrap: wrap; }
            .login-section { padding: 60px 20px; }
            .login-section h1 { font-size: 2em; }
            .form-group input { padding: 12px; }
        }
    </style>
</head>
<body>
    <header>
        <a href="landing.php" class="logo">Edu Bridge</a>
        <a href="landing.php" class="back-btn">Back to Home</a>
    </header>

    <section class="login-section">
        <h1>Organization Login</h1>
        <p>Welcome back! Please sign in to manage your opportunities and applicants.</p>

        <?php if ($error = getSessionError()): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form id="orgLoginForm" action="org-login-handler.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <div class="form-group">
                <label for="orgEmail">Organization Email</label>
                <input type="email" id="orgEmail" name="orgEmail" placeholder="Enter your organization's email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <button type="submit" class="login-btn">Login</button>
        </form>

        <div class="switch-link">
            Don’t have an account? <a href="org-signup.php">Sign up here</a>
        </div>
    </section>

    <script>
        // Optional: Subtle animation fix on load
        window.addEventListener('load', () => {
            document.querySelectorAll('.form-group').forEach(el => el.style.opacity = '1');
        });
    </script>
</body>
</html>
