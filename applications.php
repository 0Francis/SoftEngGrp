<?php
include 'db_connect.php'; // connection file

// 🟢 Handle new application submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $opportunityid = $_POST['opportunityid'];
    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    
    // (Optional) handle file upload
    $resume = null;
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
        $targetDir = "uploads/";
        if (!file_exists($targetDir)) mkdir($targetDir, 0777, true);
        $resume = $targetDir . basename($_FILES["resume"]["name"]);
        move_uploaded_file($_FILES["resume"]["tmp_name"], $resume);
    }

    // Insert application into DB
    $stmt = $conn->prepare("INSERT INTO applications (opportunityid, fullname, email, phone, resume) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $opportunityid, $fullname, $email, $phone, $resume);

    if ($stmt->execute()) {
        echo "<p style='color:green;'>✅ Application submitted successfully!</p>";
    } else {
        echo "<p style='color:red;'>❌ Error: " . $stmt->error . "</p>";
    }

    $stmt->close();
}

// 🟣 Fetch all applications for display
$applications = [];
$sql = "SELECT a.application_id, a.fullname, a.email, a.phone, a.resume, a.applied_on, 
               o.title AS opportunity_title
        FROM applications a
        JOIN opportunities o ON a.opportunityid = o.opportunityid
        ORDER BY a.applied_on DESC";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $applications[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Applications</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <h2 class="mb-4 text-center">📋 Youth Applications</h2>

    <?php if (count($applications) > 0): ?>
      <table class="table table-striped table-hover">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Applicant Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Opportunity</th>
            <th>Resume</th>
            <th>Applied On</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($applications as $index => $app): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($app['fullname']) ?></td>
            <td><?= htmlspecialchars($app['email']) ?></td>
            <td><?= htmlspecialchars($app['phone']) ?></td>
            <td><?= htmlspecialchars($app['opportunity_title']) ?></td>
            <td>
              <?php if ($app['resume']): ?>
                <a href="<?= htmlspecialchars($app['resume']) ?>" target="_blank">View</a>
              <?php else: ?>
                <em>No resume</em>
              <?php endif; ?>
            </td>
            <td><?= $app['applied_on'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php else: ?>
      <div class="alert alert-info text-center">No applications received yet.</div>
    <?php endif; ?>
  </div>
</body>
</html>
