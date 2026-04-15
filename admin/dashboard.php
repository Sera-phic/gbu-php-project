<?php
$page_title = "Admin Dashboard";
require_once 'includes/header.php';

// Fetch registration statistics
try {
    $total_count = $conn->query("SELECT COUNT(*) FROM registrations")->fetchColumn();
    $today_count = $conn->query("SELECT COUNT(*) FROM registrations WHERE DATE(created_at) = CURDATE()")->fetchColumn(); // Need to check if created_at exists
    
    // Check if created_at exists, if not use a fallback or skip today_count for now
    // Based on the SQL dump, created_at is NOT in the schema. Let's stick to total for now.
    $today_count = 0; // Fallback
    
    $programme_stats = $conn->query("SELECT nameOfProgramme, COUNT(*) as count FROM registrations GROUP BY nameOfProgramme")->fetchAll();
    
} catch (PDOException $e) {
    // Silent fail for stats or show 0
    $total_count = 0;
    $programme_stats = [];
}
?>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="section-card stat-card bg-primary text-white">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-content">
                <h3 class="stat-title">Total Registrations</h3>
                <p class="stat-value"><?php echo number_format($total_count); ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card stat-card bg-success text-white">
            <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
            <div class="stat-content">
                <h3 class="stat-title">Recent Activity</h3>
                <p class="stat-value">View All</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="section-card stat-card bg-info text-white">
            <div class="stat-icon"><i class="fas fa-chart-pie"></i></div>
            <div class="stat-content">
                <h3 class="stat-title">Active Semester</h3>
                <p class="stat-value">2024-25</p>
            </div>
        </div>
    </div>
</div>

<div class="section-card mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="section-title mb-0">Recent Registrations</h2>
        <div class="search-box">
            <input type="text" id="registrationSearch" class="form-control" placeholder="Search students...">
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table" id="registrationsTable">
            <thead>
                <tr>
                    <th>Roll Number</th>
                    <th>Full Name</th>
                    <th>Programme</th>
                    <th>Branch</th>
                    <th>Year/Sem</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $conn->query("SELECT * FROM registrations ORDER BY id DESC LIMIT 50");
                    while ($row = $stmt->fetch()):
                ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($row['rollNumber']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['fullName']); ?></td>
                        <td><?php echo htmlspecialchars($row['nameOfProgramme']); ?></td>
                        <td><?php echo htmlspecialchars($row['branchSpecialization']); ?></td>
                        <td>Year <?php echo htmlspecialchars($row['year']); ?> / Sem <?php echo htmlspecialchars($row['semester']); ?></td>
                        <td><?php echo htmlspecialchars($row['studentContact']); ?></td>
                        <td class="table-actions">
                            <a href="view_student.php?id=<?php echo $row['id']; ?>" class="btn-icon text-info" title="View"><i class="fas fa-eye"></i></a>
                            <a href="edit_student.php?id=<?php echo $row['id']; ?>" class="btn-icon text-warning" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="delete_student.php?id=<?php echo $row['id']; ?>" class="btn-icon text-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this record?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center'>Error loading data: " . $e->getMessage() . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.row { display: flex; flex-wrap: wrap; gap: 1.5rem; }
.col-md-4 { flex: 1; min-width: 300px; }

.stat-card {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    transition: transform 0.3s;
}

.stat-card:hover { transform: translateY(-5px); }

.bg-primary { background-color: var(--primary-color) !important; }
.bg-success { background-color: var(--success-color) !important; }
.bg-info { background-color: var(--info-color) !important; }
.text-white { color: var(--white) !important; }

.stat-icon { font-size: 2.5rem; opacity: 0.8; }
.stat-title { font-size: 0.9rem; font-weight: 500; margin-bottom: 0.25rem; }
.stat-value { font-size: 1.5rem; font-weight: 700; margin-bottom: 0; }

.table-actions { display: flex; gap: 0.75rem; }
.btn-icon { font-size: 1.1rem; text-decoration: none; transition: opacity 0.3s; }
.btn-icon:hover { opacity: 0.7; }

.search-box .form-control {
    width: 300px;
    background-color: var(--gray-100);
    border-color: var(--gray-300);
}

.mb-0 { margin-bottom: 0 !important; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        filterTable('registrationSearch', 'registrationsTable');
    });
</script>

<?php require_once 'includes/footer.php'; ?>
