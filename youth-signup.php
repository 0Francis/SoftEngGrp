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
    <title>Edu Bridge - Youth Sign Up</title>
    <style>
        /* Consistent futuristic styling with animations. Neutral colors, animated ombre background for dynamic, inviting feel. */
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
            0%, 100% { box-shadow: 0 5px 15px rgba(74, 144, 226, 0.4); }
            50% { box-shadow: 0 5px 25px rgba(74, 144, 226, 0.6); }
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(-45deg, #f5f7fa 0%, #c3cfe2 25%, #a8b8d8 50%, #c3cfe2 75%, #f5f7fa 100%);
            background-size: 400% 400%;
            animation: gradientShift 15s ease infinite;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden;
        }

        /* Header: Logo on left, Back to Home on right */
        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .logo:hover {
            color: #357abd;
        }

        .back-btn {
            padding: 10px 20px;
            border: 2px solid #4a90e2;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            background: transparent;
            color: #4a90e2;
        }

        .back-btn:hover {
            background: #4a90e2;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
        }

        /* Main Sign Up Form Section: Centered, animated */
        .signup-section {
            text-align: center;
            padding: 100px 20px;
            max-width: 400px;
            width: 90%;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(15px);
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .signup-section h1 {
            color: #4a90e2;
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            letter-spacing: 2px;
        }

        .signup-section p {
            color: #666;
            margin-bottom: 30px;
            font-size: 1.1em;
            animation: fadeInUp 1s ease-out 0.4s both;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
            animation: slideInLeft 0.8s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.3s; }
        .form-group:nth-child(2) { animation-delay: 0.5s; }
        .form-group:nth-child(3) { animation-delay: 0.7s; }
        .form-group:nth-child(4) { animation-delay: 0.9s; }

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
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1em;
            box-sizing: border-box;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-group input:focus {
            border-color: #4a90e2;
            box-shadow: 0 0 15px rgba(74, 144, 226, 0.4);
            transform: scale(1.02);
            outline: none;
        }

        .signup-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            animation: fadeInUp 1s ease-out 1.1s both, pulseGlow 2s ease-in-out infinite 1.2s;
        }

        .signup-btn:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 8px 25px rgba(74, 144, 226, 0.5);
        }

        .signup-btn:active {
            transform: translateY(-1px) scale(1);
        }

        .error {
            color: #e74c3c;
            font-size: 0.9em;
            margin-top: 5px;
            display: none;
            animation: fadeInUp 0.5s ease-out;
        }

        .success {
            color: #27ae60;
            font-size: 0.9em;
            margin-top: 10px;
            display: none;
            animation: fadeInUp 0.5s ease-out;
        }

        .switch-link {
            margin-top: 20px;
            color: #666;
            font-size: 0.95em;
        }

        .switch-link a {
            color: #4a90e2;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .switch-link a:hover {
            color: #357abd;
        }

        /* Responsive for mobile (youth-friendly) */
        @media (max-width: 480px) {
            .signup-section {
                padding: 60px 20px;
                margin: 20px;
            }
            .signup-section h1 {
                font-size: 2em;
            }
            body {
                animation-duration: 20s; /* Slower animation on mobile */
            }
        }
    </style>
</head>
<body>
    <header>
        <a href="landing.php" class="logo">Edu Bridge</a>
        <a href="landing.php" class="back-btn">Back to Home</a>
    </header>

    <section class="signup-section">
        <?php if ($error = getSessionError()): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success = getSessionSuccess()): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form id="youthSignupForm" action="youth-signup-handler.php" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="full_name" placeholder="Enter your full name" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Confirm your password" required>
            </div>
            <button type="submit" class="signup-btn">Sign Up</button>
        </form>
    </section>
</body>
</html>