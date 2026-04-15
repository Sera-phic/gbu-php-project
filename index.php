<?php
$page_title = "Home";
require_once 'includes/header.php';
?>

<section class="hero-section">
    <div class="container">
        <h1 class="hero-title">Academic Registration Portal 2024-2025</h1>
        <p class="hero-subtitle">Applications are Open for the Upcoming Academic Year. Read the instructions carefully before applying.</p>
        <div class="hero-btns">
            <a href="register.php" class="btn btn-primary btn-lg">Register Now</a>
            <a href="#instructions" class="btn btn-outline-light btn-lg">View Instructions</a>
        </div>
    </div>
</section>

<section id="instructions" class="section-card">
    <h2 class="section-title">Registration Instructions</h2>
    
    <div class="instruction-grid">
        <div class="instruction-step">
            <div class="step-num">1</div>
            <h3>Fill Form</h3>
            <p>Complete the registration form with your personal, academic, and fee details.</p>
        </div>
        <div class="instruction-step">
            <div class="step-num">2</div>
            <h3>Preview & Submit</h3>
            <p>Review your information and submit the form to generate your registration record.</p>
        </div>
        <div class="instruction-step">
            <div class="step-num">3</div>
            <h3>Download Slip</h3>
            <p>Login with your Roll Number and Mobile Number to download your registration slip.</p>
        </div>
    </div>
    
    <div class="important-notes">
        <h3><i class="fas fa-exclamation-triangle"></i> Important Notes:</h3>
        <ul>
            <li>Ensure all details are accurate to avoid delays.</li>
            <li>Keep a copy of the registration slip for physical verification.</li>
            <li>For any technical issues, contact the ICT Office.</li>
        </ul>
    </div>
</section>

<section class="section-card notices-section">
    <h2 class="section-title">Recent Announcements</h2>
    <div class="notices-list">
        <div class="notice-item high-priority">
            <div class="notice-meta">
                <span class="notice-date">Jan 04, 2025</span>
                <span class="notice-tag">High Priority</span>
            </div>
            <h4>Registration Open for 2025</h4>
            <p>Online registration for the upcoming academic year has commenced. Please complete your registration before January 15, 2025.</p>
        </div>
        <div class="notice-item">
            <div class="notice-meta">
                <span class="notice-date">Jan 05, 2025</span>
            </div>
            <h4>Fee Receipt Submission</h4>
            <p>All students must submit their fee payment receipts to their respective course coordinators.</p>
        </div>
    </div>
</section>

<style>
.hero-btns {
    display: flex;
    justify-content: center;
    gap: 1.5rem;
}

.btn-outline-light {
    border: 2px solid var(--white);
    color: var(--white);
}

.btn-outline-light:hover {
    background-color: var(--white);
    color: var(--dark-color);
}

.instruction-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.instruction-step {
    padding: 1.5rem;
    background: var(--gray-100);
    border-radius: var(--border-radius);
    text-align: center;
    position: relative;
}

.step-num {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: var(--white);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    margin: 0 auto 1rem;
}

.important-notes {
    background: #fff3cd;
    border-left: 5px solid #ffc107;
    padding: 1.5rem;
    border-radius: var(--border-radius);
}

.important-notes h3 {
    color: #856404;
    margin-bottom: 1rem;
}

.important-notes ul {
    list-style-position: inside;
}

.notice-item {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
}

.notice-item:last-child {
    border-bottom: none;
}

.notice-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.5rem;
}

.notice-date {
    font-size: 0.85rem;
    color: var(--gray-600);
}

.notice-tag {
    background: var(--danger-color);
    color: var(--white);
    font-size: 0.75rem;
    padding: 0.2rem 0.5rem;
    border-radius: 3px;
}

.high-priority {
    border-left: 4px solid var(--danger-color);
}
</style>

<?php require_once 'includes/footer.php'; ?>
