<?php
$page_title = "Student Registration Form";
require_once 'includes/header.php';

// Program list for the dropdown
$programmes = [
    'B.tech' => 'B.TECH',
    'Integrated B.tech + M.tech' => 'Integrated B.tech + M.tech',
    'BCA' => 'BCA',
    'M.tech' => 'M.tech',
    'MCA' => 'MCA',
    'PHD' => 'PHD'
];

// Branch list
$branches = [
    'CSE' => 'CSE',
    'CSE (AI)' => 'CSE (AI)',
    'CSE (Cyber Security)' => 'CSE (Cyber Security)',
    'CSE (Data Science)' => 'CSE (Data Science)',
    'CSE (Internet of Things)' => 'CSE (IoT)',
    'CSE (Machine Learning)' => 'CSE (ML)',
    'ECE' => 'ECE',
    'ECE (AI/ML)' => 'ECE (AI/ML)',
    'IT' => 'IT',
    'Integrated B.tech + M.tech CSE' => 'Integrated B.tech + M.tech CSE',
    'BCA' => 'BCA',
    'MCA' => 'MCA',
    'PHD (CSE)' => 'PHD (CSE)',
    'PHD (ECE)' => 'PHD (ECE)'
];

// Hostel list
$hostels = [
    'Sant Ravi Das Boys Hostel', 'Guru Ghasi Das Boys Hostel', 'Shri Narayan Guru Boys Hostel', 
    'Birsa Munda Hostel Boys', 'Sant Kabir Das Hostel Boys', 'Shri Chatrapati Sahu Ji Maharaj Boys Hostel', 
    'Malik Mohd. Jaysi Hostel Boys', 'Tulsidas Hostel Boys', 'Raheem Boys Hostel', 'Ram Sharan Das Boys Hostel', 
    'Munshi Prem Chand Boys Hostel (New Hostel)', 'Maharshi Valmiki Boys Hostel', 'Rani Laxmi Bai Girls Hostel', 
    'Mahamaya Girls Hostel', 'Rama Bai Ambedkar Girls Hostel', 'Savitri Bai Phule Girls Hostel', 
    'Maha Devi Verma Girls Hostel', 'Ismat Chughtai Girls Hostel', 'Married Research Scholars Hostel', 'Day Scholar'
];
?>

<section class="section-card">
    <h2 class="section-title">Registration Form (2024-25)</h2>
    <p class="text-muted mb-4">Please fill in all the details accurately. Fields marked with * are mandatory.</p>
    
    <form action="preview.php" method="POST" class="registration-form">
        <!-- Personal Details Section -->
        <div class="form-section">
            <h3><i class="fas fa-user"></i> Personal Details</h3>
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="rollNumber">Roll Number *</label>
                        <input type="text" id="rollNumber" name="rollNumber" class="form-control" placeholder="Enter Roll Number" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="fullName">Student's Full Name *</label>
                        <input type="text" id="fullName" name="fullName" class="form-control" placeholder="Enter Full Name" required>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="fathersName">Father's / Husband's Name *</label>
                        <input type="text" id="fathersName" name="fathersName" class="form-control" placeholder="Enter Name" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="nameOfProgramme">Name of Programme *</label>
                        <select id="nameOfProgramme" name="nameOfProgramme" class="form-control" required>
                            <option value="">Select Programme</option>
                            <?php foreach ($programmes as $val => $label): ?>
                                <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="branchSpecialization">Branch / Specialization *</label>
                        <select id="branchSpecialization" name="branchSpecialization" class="form-control" required>
                            <option value="">Select Branch</option>
                            <?php foreach ($branches as $val => $label): ?>
                                <option value="<?php echo $val; ?>"><?php echo $label; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="year">Year *</label>
                        <select id="year" name="year" class="form-control" required>
                            <option value="">Select Year</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?>st Year</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="semester">Semester *</label>
                        <select id="semester" name="semester" class="form-control" required>
                            <option value="">Select Semester</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?php echo $i; ?>">Semester <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="category">Category *</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <option value="Gen">General</option>
                            <option value="OBC">OBC</option>
                            <option value="SC">SC</option>
                            <option value="ST">ST</option>
                            <option value="PH">PH</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="gender">Gender *</label>
                        <select id="gender" name="gender" class="form-control" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="aadharCard">Aadhar Number *</label>
                        <input type="text" id="aadharCard" name="aadharCard" class="form-control" placeholder="12-digit Aadhar" required>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Contact Details -->
        <div class="form-section mt-4">
            <h3><i class="fas fa-address-book"></i> Contact Details</h3>
            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="studentContact">Student Contact *</label>
                        <input type="text" id="studentContact" name="studentContact" class="form-control" required>
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="studentEmail">Student Email *</label>
                        <input type="email" id="studentEmail" name="studentEmail" class="form-control" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="permanentAddress">Permanent Address *</label>
                <textarea id="permanentAddress" name="permanentAddress" class="form-control" rows="3" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label" for="hostelAddress">Hostel Selection *</label>
                <select id="hostelAddress" name="hostelAddress" class="form-control" required>
                    <option value="">Select Hostel</option>
                    <?php foreach ($hostels as $hostel): ?>
                        <option value="<?php echo $hostel; ?>"><?php echo $hostel; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Fee Details Section -->
        <div class="form-section mt-4">
            <h3><i class="fas fa-file-invoice-dollar"></i> Fee Details</h3>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Particulars</th>
                            <th>Amount Paid</th>
                            <th>Remaining</th>
                            <th>Bank/Txn Details</th>
                            <th>Platform</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Odd Semester -->
                        <tr>
                            <td>Odd Semester</td>
                            <td><input type="number" step="0.01" name="oddSemesterAmount" class="form-control" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="oddSemesterRemaining" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" name="oddSemesterTxnDetails" class="form-control" placeholder="Ref No."></td>
                            <td>
                                <select name="oddSemesterPlatform" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Online">Online</option>
                                    <option value="Challan">Challan</option>
                                    <option value="Loan">Loan</option>
                                </select>
                            </td>
                            <td><input type="date" name="oddSemesterDate" class="form-control"></td>
                        </tr>
                        <!-- Even Semester -->
                        <tr>
                            <td>Even Semester</td>
                            <td><input type="number" step="0.01" name="evenSemesterAmount" class="form-control" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="evenSemesterRemaining" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" name="evenSemesterTxnDetails" class="form-control" placeholder="Ref No."></td>
                            <td>
                                <select name="evenSemesterPlatform" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Online">Online</option>
                                    <option value="Challan">Challan</option>
                                    <option value="Loan">Loan</option>
                                </select>
                            </td>
                            <td><input type="date" name="evenSemesterDate" class="form-control"></td>
                        </tr>
                        <!-- Hostel Fee -->
                        <tr>
                            <td>Hostel Fee</td>
                            <td><input type="number" step="0.01" name="hostelAmount" class="form-control" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="hostelRemaining" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" name="hostelTxnDetails" class="form-control" placeholder="Ref No."></td>
                            <td>
                                <select name="hostelPlatform" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Online">Online</option>
                                    <option value="Challan">Challan</option>
                                </select>
                            </td>
                            <td><input type="date" name="hostelDate" class="form-control"></td>
                        </tr>
                        <!-- Mess Fee -->
                        <tr>
                            <td>Mess Fee</td>
                            <td><input type="number" step="0.01" name="messAmount" class="form-control" placeholder="0.00"></td>
                            <td><input type="number" step="0.01" name="messRemaining" class="form-control" placeholder="0.00"></td>
                            <td><input type="text" name="messTxnDetails" class="form-control" placeholder="Ref No."></td>
                            <td>
                                <select name="messPlatform" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Online">Online</option>
                                    <option value="Challan">Challan</option>
                                </select>
                            </td>
                            <td><input type="date" name="messDate" class="form-control"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Added missing hidden fields for backward compatibility -->
            <input type="hidden" name="oddSemesterDetails" value="">
            <input type="hidden" name="evenSemesterDetails" value="">
            <input type="hidden" name="hostelPaymentMode" value="">
            <input type="hidden" name="messPaymentMode" value="">
            <input type="hidden" name="fatherOccupation" value="N/A">
        </div>

        <!-- Hidden inputs for backward compatibility with current preview page if needed -->
        <!-- In a real refactor, I'd also refactor the fee section but for now I'll focus on the main structure -->
        
        <div class="form-actions mt-5">
            <button type="submit" class="btn btn-success btn-lg btn-block">
                <i class="fas fa-eye"></i> Preview Registration
            </button>
        </div>
    </form>
</section>

<style>
.form-section h3 {
    border-bottom: 2px solid var(--gray-300);
    padding-bottom: 0.5rem;
    margin-bottom: 1.5rem;
    color: var(--primary-color);
    font-size: 1.25rem;
}

.form-section h3 i {
    margin-right: 0.5rem;
}

.btn-block {
    width: 100%;
    display: block;
}

.mt-4 { margin-top: 1.5rem; }
.mt-5 { margin-top: 3rem; }
.mb-4 { margin-bottom: 1.5rem; }
.text-muted { color: var(--gray-600); font-size: 0.9rem; }
</style>

<?php require_once 'includes/footer.php'; ?>
