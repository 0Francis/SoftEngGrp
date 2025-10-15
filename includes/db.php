<?php
// db.php: Database utility functions (no output or feedback)
require_once __DIR__ . '/config.php';

function getDBConnection($dbname = DB_NAME) {
    $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . $dbname;
    return new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
}

function createDatabaseIfNotExists() {
    try {
        getDBConnection();  // Try connecting to main DB
        return true;  // Success if no exception
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'does not exist') !== false) {
            $defaultPdo = getDBConnection('postgres');  // Connect to default DB
            $stmt = $defaultPdo->prepare("SELECT 1 FROM pg_database WHERE datname = :dbname");
            $stmt->execute([':dbname' => DB_NAME]);
            
            if (!$stmt->fetch()) {
                $defaultPdo->exec('CREATE DATABASE "' . DB_NAME . '" OWNER "' . DB_USER . '";');
            }
            return true;  // Database created or already exists
        }
        throw $e;  // Re-throw for higher-level handling
    }
}

function initDatabase() {
    if (!createDatabaseIfNotExists()) {
        throw new Exception("Failed to create database.");
    }
    
    $pdo = getDBConnection();
    
    // ENUM Types
    $enumStatements = [
        "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'org_type') THEN CREATE TYPE org_type AS ENUM ('Company', 'NGO', 'Training Center', 'Other'); END IF; END$$;",
        "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'opportunity_category') THEN CREATE TYPE opportunity_category AS ENUM ('Internship', 'Volunteer', 'Training', 'Other'); END IF; END$$;",
        "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'opportunity_status') THEN CREATE TYPE opportunity_status AS ENUM ('Open', 'Closed'); END IF; END$$;",
        "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'application_status') THEN CREATE TYPE application_status AS ENUM ('Pending', 'Approved', 'Rejected', 'Completed'); END IF; END$$;",
        "DO $$ BEGIN IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'admin_role') THEN CREATE TYPE admin_role AS ENUM ('Super Admin', 'Verifier', 'Reporter'); END IF; END$$;"
    ];
    
    foreach ($enumStatements as $sql) {
        $pdo->exec($sql);
    }
    
    // Table Creation
    $tables = [
        "youth" => "CREATE TABLE IF NOT EXISTS youth (
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
        
        "organizations" => "CREATE TABLE IF NOT EXISTS organizations (
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
        
        "opportunities" => "CREATE TABLE IF NOT EXISTS opportunities (
            opportunity_id SERIAL PRIMARY KEY,
            org_id INTEGER NOT NULL REFERENCES organizations(org_id) ON DELETE CASCADE,
            title VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            skills_required TEXT,
            duration VARCHAR(100),
            deadline DATE,
            location VARCHAR(255),
            category opportunity_category DEFAULT 'Other',
            status opportunity_status DEFAULT 'Open',
            date_posted TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        
        "applications" => "CREATE TABLE IF NOT EXISTS applications (
            application_id SERIAL PRIMARY KEY,
            youth_id INTEGER NOT NULL REFERENCES youth(youth_id) ON DELETE CASCADE,
            opportunity_id INTEGER NOT NULL REFERENCES opportunities(opportunity_id) ON DELETE CASCADE,
            date_applied TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status application_status DEFAULT 'Pending',
            remarks TEXT
        );",
        
        "admins" => "CREATE TABLE IF NOT EXISTS admins (
            admin_id SERIAL PRIMARY KEY,
            full_name VARCHAR(150) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            role admin_role DEFAULT 'Verifier',
            date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );",
        
        "reports" => "CREATE TABLE IF NOT EXISTS reports (
            report_id SERIAL PRIMARY KEY,
            generated_by INTEGER REFERENCES admins(admin_id) ON DELETE SET NULL,
            report_type VARCHAR(100),
            content TEXT,
            date_generated TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );"
    ];
    
    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
    
    // Sample Data Insertion
    $stmt = $pdo->query("SELECT COUNT(*) FROM organizations");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $sampleOrgs = [
            ["Tech Innovators Ltd", "hr@techinnovators.com", password_hash("123456", PASSWORD_DEFAULT), "Company"],
            ["Youth Empower NGO", "info@youthngo.org", password_hash("123456", PASSWORD_DEFAULT), "NGO"]
        ];
        $insert = $pdo->prepare("INSERT INTO organizations (org_name, email, password, org_type) VALUES (?, ?, ?, ?::org_type)");
        foreach ($sampleOrgs as $org) {
            $insert->execute($org);
        }
    }
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM opportunities");
    $count = $stmt->fetchColumn();
    if ($count == 0) {
        $sampleOpps = [
            [1, "Junior Web Developer Internship", "Assist in building websites using HTML, CSS, and JS.", "HTML, CSS, JS", "3 months", "2025-12-01", "Nairobi", "Internship"],
            [2, "Community Volunteer Program", "Engage youth in community service and leadership training.", "Communication, Teamwork", "2 months", "2025-11-20", "Kisumu", "Volunteer"]
        ];
        $insert = $pdo->prepare("INSERT INTO opportunities (org_id, title, description, skills_required, duration, deadline, location, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?::opportunity_category)");
        foreach ($sampleOpps as $opp) {
            $insert->execute($opp);
        }
    }
    
    return true;  // Initialization complete
}
?>