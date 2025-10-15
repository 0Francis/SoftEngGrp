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
    <title>Edu Bridge - Welcome</title>
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

        .auth-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            min-width: 100px;
        }

        .login-btn {
            background: transparent;
            color: #4a90e2;
            border: 2px solid #4a90e2;
        }

        .signup-btn {
            background: linear-gradient(135deg, #4a90e2, #357abd);
            color: white;
            box-shadow: 0 3px 10px rgba(74, 144, 226, 0.3);
        }

        .hero {
            text-align: center;
            padding: 100px 20px;
            max-width: 800px;
            margin: 0 auto;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        .youths-section {
            background: rgba(255, 255, 255, 0.95);
            margin: 80px 20px;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .youths-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .youth-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .youth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .youth-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .youth-card p {
            padding: 15px;
            font-size: 0.95em;
            color: #444;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 12px;
            width: 90%;
            max-width: 400px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 1.5em;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            header { padding: 10px 20px; }
        }
    </style>
</head>
<body>
    <?php if ($error = getSessionError()): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success = getSessionSuccess()): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    
    <header>
        <a href="landing.php" class="logo">Edu Bridge</a>
        <div>
            <a href="#" class="auth-btn signup-btn" onclick="showSignupModal(); return false;">Sign Up</a>
            <a href="#" class="auth-btn login-btn" onclick="showLoginModal(); return false;">Login</a>
        </div>
    </header>

    <section class="hero">
        <h1>Welcome to Edu Bridge</h1>
        <p>Edu Bridge is your ultimate platform connecting youths with exciting job opportunities, internships, and volunteer roles from top companies and NGOs. Build your future, one connection at a time!</p>
    </section>

    <section class="youths-section">
        <h2>Join a Vibrant Community</h2>
        <p>Meet the young talents like you who are bridging gaps between education and employment. From urban innovators to rural leaders, Edu Bridge empowers everyone.</p>
        <div class="youths-gallery">
            <div class="youth-card">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?w=400&h=200&fit=crop&crop=entropy" alt="Youths in urban community collaborating on a project">
                <p>Urban Innovators: Tech enthusiasts building apps for change.</p>
            </div>
            <div class="youth-card">
                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&h=200&fit=crop&crop=entropy" alt="Youths in rural community volunteering with NGO">
                <p>Rural Leaders: Empowering villages through sustainable projects.</p>
            </div>
            <div class="youth-card">
                <img src="https://images.unsplash.com/photo-1521737604893-d14cc237f11d?w=400&h=200&fit=crop&crop=entropy" alt="Diverse youths in training session">
                <p>Creative Minds: Marketing and design interns at leading firms.</p>
            </div>
            <div class="youth-card">
                <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=400&h=200&fit=crop&crop=entropy" alt="Youths networking in community event">
                <p>Network Builders: Connecting with companies for entry-level roles.</p>
            </div>
        </div>
    </section>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeLoginModal()">&times;</span>
            <form action="youth-login-handler.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <p>You already have an account? Please select...</p>
                <button type="button" onclick="redirectToYouthLogin();">Youth</button>
                <button type="button" onclick="redirectToOrgLogin();">Organization</button>
            </form>
        </div>
    </div>

    <!-- Signup Modal -->
    <div id="signupModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeSignupModal()">&times;</span>
            <form action="youth-signup-handler.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                <p>You don't have an account? Please select...</p>
                <button type="button" onclick="redirectToYouthSignup();">Youth</button>
                <button type="button" onclick="redirectToOrgSignup();">Organization</button>
            </form>
        </div>
    </div>

    <script>
        // Login Modal Functions
        function showLoginModal() {
            document.getElementById('loginModal').style.display = 'block';
        }

        function closeLoginModal() {
            document.getElementById('loginModal').style.display = 'none';
        }

        function redirectToYouthLogin() {
            window.location.href = 'youth-login.php';
        }

        function redirectToOrgLogin() {
            window.location.href = 'org-login.php';
        }

        // Signup Modal Functions
        function showSignupModal() {
            document.getElementById('signupModal').style.display = 'block';
        }

        function closeSignupModal() {
            document.getElementById('signupModal').style.display = 'none';
        }

        function redirectToYouthSignup() {
            window.location.href = 'youth-signup.php';
        }

        function redirectToOrgSignup() {
            window.location.href = 'org-signup.php';
        }
    </script>
</body>
</html>
