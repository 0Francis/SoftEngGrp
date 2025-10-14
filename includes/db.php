<?php
// database.php - PDO connection and auto-create tables with your SQL

require_once 'config.php';

function getDBConnection() {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}

function createDatabaseIfNotExists() {
    try {
        getDBConnection();  // Test connection
        return true;
    } catch (PDOException $e) {
        // Simple creation if DB missing
        $defaultDsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=postgres";
        $pdoDefault = new PDO($defaultDsn, DB_USER, DB_PASS);
        $pdoDefault->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " OWNER " . DB_USER . ";");
        $pdoDefault->exec("GRANT ALL PRIVILEGES ON DATABASE " . DB_NAME . " TO " . DB_USER . ";");
        return true;
    }
}


function initDatabase() {
    $pdo = getDBConnection();
    
    $enumTypes = [
        "CREATE TYPE IF NOT EXISTS org_type AS ENUM ('Company', 'NGO', 'Training Center', 'Other');",
        "CREATE TYPE IF NOT EXISTS opportunity_category AS ENUM ('Internship', 'Volunteer', 'Training', 'Other');",
        "CREATE TYPE IF NOT EXISTS opportunity_status AS ENUM ('Open', 'Closed');",
        "CREATE TYPE IF NOT EXISTS application_status AS ENUM ('Pending', 'Approved', 'Rejected', 'Completed');",
        "CREATE TYPE IF NOT EXISTS admin_role AS ENUM ('Super Admin', 'Verifier', 'Reporter');",
    ];
    
    foreach ($enumTypes as $query) {
        try {
            $pdo->exec($query);
        } catch (PDOException $e) {
            error_log("ENUM creation failed: " . $e->getMessage());
        }
    }
    
    $sqlQueries = [
        // Youth table
        "CREATE TABLE IF NOT EXISTS youth (
            youth_id SERIAL PRIMARY KEY,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            education_level VARCHAR(100),
            skills TEXT,
            interests TEXT,
            availability VARCHAR(100),
            verified BOOLEAN DEFAULT FALSE,
            date_joined TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        
        // Organizations table
        "CREATE TABLE IF NOT EXISTS organizations (
            org_id SERIAL PRIMARY KEY,
            org_name VARCHAR(200) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address VARCHAR(255),
            org_type org_type DEFAULT 'Other',
            verified BOOLEAN DEFAULT FALSE,
            date_registered TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        
        // Opportunities table
        "CREATE TABLE IF NOT EXISTS opportunities (
            opportunity_id SERIAL PRIMARY KEY,
            org_id INTEGER NOT NULL,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            skills_required TEXT,
            duration VARCHAR(100),
            deadline DATE,
            location VARCHAR(255),
            category opportunity_category DEFAULT 'Other',
            status opportunity_status DEFAULT 'Open',
            date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (org_id) REFERENCES organizations(org_id) ON DELETE CASCADE
        );",
        
        // Applications table
        "CREATE TABLE IF NOT EXISTS applications (
            application_id SERIAL PRIMARY KEY,
            youth_id INTEGER NOT NULL,
            opportunity_id INTEGER NOT NULL,
            date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status application_status DEFAULT 'Pending',
            remarks TEXT,
            FOREIGN KEY (youth_id) REFERENCES youth(youth_id) ON DELETE CASCADE,
            FOREIGN KEY (opportunity_id) REFERENCES opportunities(opportunity_id) ON DELETE CASCADE
        );",
        
        // Admins table
        "CREATE TABLE IF NOT EXISTS admins (
            admin_id SERIAL PRIMARY KEY,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role admin_role DEFAULT 'Verifier',
            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        
        // Reports table
        "CREATE TABLE IF NOT EXISTS reports (
            report_id SERIAL PRIMARY KEY,
            generated_by INTEGER,
            report_type VARCHAR(100),
            content TEXT,
            date_generated TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (generated_by) REFERENCES admins(admin_id) ON DELETE SET NULL
        );",
    ];
    
    foreach ($sqlQueries as $query) {
        try {
            $pdo->exec($query);
        } catch (PDOException $e) {
            error_log("Table creation failed: " . $e->getMessage());
        }
    }
    
    //Insert sample data (only if tables are empty)
    // Check organizations
    $stmt = $pdo->query("SELECT COUNT(*) FROM organizations");
    if ($stmt->fetchColumn() == 0) {
        $sampleOrgs = [
            ["Tech Innovators Ltd", "hr@techinnovators.com", "123456", "Company"],
            ["Youth Empower NGO", "info@youthngo.org", "123456", "NGO"]
        ];
        $insertOrg = $pdo->prepare("INSERT INTO organizations (org_name, email, password, org_type) VALUES (?, ?, ?, ?::org_type)");
        foreach ($sampleOrgs as $org) {
            $insertOrg->execute($org);
        }
    }
    
    // Check opportunities (depends on orgs existing)
    $stmt = $pdo->query("SELECT COUNT(*) FROM opportunities");
    if ($stmt->fetchColumn() == 0) {
        $sampleOpps = [
            [1, "Junior Web Developer Internship", "Assist in building websites using HTML, CSS, and JS.", "HTML, CSS, JS", "3 months", "2025-12-01", "Nairobi", "Internship"],
            [2, "Community Volunteer Program", "Engage youth in community service and leadership training.", "Communication, Teamwork", "2 months", "2025-11-20", "Kisumu", "Volunteer"]
        ];
        $insertOpp = $pdo->prepare("INSERT INTO opportunities (org_id, title, description, skills_required, duration, deadline, location, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?::opportunity_category)");
        foreach ($sampleOpps as $opp) {
            $insertOpp->execute($opp);
        }
    }

    echo "Database initialized with tables and sample data!";
}

initDatabase();
?>
