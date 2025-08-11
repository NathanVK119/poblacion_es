<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';//database connection path

if (isset($_GET['id'])) {
    $lrn = $_GET['id'];
    $sql = "SELECT * FROM poblacion WHERE lrn = '$lrn'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $lrn = $_POST['lrn'];
    $name = $_POST['name'];
    $sex = $_POST['sex'];
    $birthday = $_POST['birthday'];
    $age = $_POST['age'];
    $mother_tongue = $_POST['mother_tongue'];
    $ip = $_POST['ip'];
    $religion = $_POST['religion'];
    $house_number = $_POST['house_number'];
    $barangay = $_POST['barangay'];
    $municipality = $_POST['municipality'];
    $province = $_POST['province'];
    $region = $_POST['region'];
    $father = $_POST['father'];
    $mother = $_POST['mother'];
    $guardian_name = $_POST['guardian_name'];
    $relationship = $_POST['relationship'];
    $contact = $_POST['contact'];
    $learning_modality = $_POST['learning_modality'];
    $sy = $_POST['sy'];
    $grade = $_POST['grade'];
    $section = $_POST['section'];
    $adviser = $_POST['adviser'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    $update_sql = "UPDATE poblacion SET name='$name', sex='$sex', birthday='$birthday', age='$age', 
        mother_tongue='$mother_tongue', ip='$ip', religion='$religion', house_number='$house_number', 
        barangay='$barangay', municipality='$municipality', province='$province', region='$region', 
        father='$father', mother='$mother', guardian_name='$guardian_name', relationship='$relationship', 
        contact='$contact', learning_modality='$learning_modality', sy='$sy', grade='$grade', 
        section='$section', adviser='$adviser', status='$status', remarks='$remarks' 
        WHERE lrn='$lrn'";

    if (mysqli_query($con, $update_sql)) {
        echo "<script>alert('Record updated successfully!'); window.location='view.php';</script>";
    } else {
        echo "<script>alert('Error updating record: " . mysqli_error($con) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="css/edit-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="philippine.js"></script>
    <script src="sy.js"></script>
    <script src="main.js"></script>
</head>
<body>
<header>
    <h1><i class="fas fa-user-graduate"></i> Edit Student Record</h1>
    <a href="view.php"><button class="back-btn"><i class="fas fa-arrow-left"></i> Back</button></a>
</header>

<div class="con-form">
<form method="POST">
    <label><i class="fas fa-id-card"></i> LRN:</label>
    <input type="text" name="lrn" value="<?= $row['lrn'] ?>" readonly>

    <label><i class="fas fa-user"></i> Name:</label>
    <input type="text" name="name" value="<?= $row['name'] ?>" required>
    
    <div class="radio-group">
    <label><i class="fas fa-venus-mars"></i> Sex:</label>
    <label for="male">Male</label>
    <input type="radio" id="male" class="rbtn" name="sex" value="MALE" <?= $row['sex'] == 'MALE' ? 'checked' : '' ?> required>
    <label for="female">Female</label>
    <input type="radio" id="female" class="rbtn" name="sex" value="FEMALE" <?= $row['sex'] == 'FEMALE' ? 'checked' : '' ?> required>
    </div>

    <label><i class="fas fa-calendar"></i> Birthday:</label>
    <input type="date" name="birthday" value="<?= $row['birthday'] ?>" required>

    <label><i class="fas fa-birthday-cake"></i> Age:</label>
    <input type="number" name="age" value="<?= $row['age'] ?>" required>
    
    <label><i class="fas fa-language"></i> Mother Tongue:</label>
    <select name="mother_tongue" required>
    <option value="Cebuano" <?= $row['mother_tongue'] == 'Cebuano' ? 'selected' : '' ?>>Cebuano</option>
    <option value="English" <?= $row['mother_tongue'] == 'English' ? 'selected' : '' ?>>English</option>
    <option value="Ilocano" <?= $row['mother_tongue'] == 'Ilocano' ? 'selected' : '' ?>>Ilocano</option>
    <option value="Tagalog" <?= $row['mother_tongue'] == 'Tagalog' ? 'selected' : '' ?>>Tagalog</option>
    </select>
    
    <label><i class="fas fa-users"></i> IP:</label>
    <input type="text" name="ip" value="<?= $row['ip'] ?>">
    
    <label><i class="fas fa-pray"></i> Religion:</label>
    <select name="religion" required>
    <option value="Adventist" <?= $row['religion'] == 'Adventist' ? 'selected' : '' ?>>Adventist</option>
    <option value="Baptism" <?= $row['religion'] == 'Baptism' ? 'selected' : '' ?>>Baptism</option>
    <option value="Christianity" <?= $row['religion'] == 'Christianity' ? 'selected' : '' ?>>Christianity</option>
    <option value="INC" <?= $row['religion'] == 'INC' ? 'selected' : '' ?>>INC</option>
    <option value="Islam" <?= $row['religion'] == 'Islam' ? 'selected' : '' ?>>Islam</option>
    <option value="Others" <?= $row['religion'] == 'Others' ? 'selected' : '' ?>>Others</option>
    </select>

    <label><i class="fas fa-home"></i> House Number:(Street Sitio/Purok)</label>
    <input type="text" name="house_number" value="<?= $row['house_number'] ?>">
    
    <label><i class="fas fa-globe-asia"></i> Region:</label>
    <select id="region" name="region" required>
    <option value="<?= $row['region'] ?>" selected><?= $row['region'] ?></option>
    </select>

    <label><i class="fas fa-map"></i> Province:</label>
    <select id="province" name="province" required>
    <option value="<?= $row['province'] ?>" selected><?= $row['province'] ?></option>
    </select>

    <label><i class="fas fa-city"></i> Municipality:</label>
    <select id="municipality" name="municipality" required>
    <option value="<?= $row['municipality'] ?>" selected><?= $row['municipality'] ?></option>
    </select>

    <label><i class="fas fa-map-marker-alt"></i> Barangay:</label>
    <select id="barangay" name="barangay" required>
    <option value="<?= $row['barangay'] ?>" selected><?= $row['barangay'] ?></option>
    </select>
    
    <label><i class="fas fa-male"></i> Father:</label>
    <input type="text" name="father" value="<?= $row['father'] ?>">
    
    <label><i class="fas fa-female"></i> Mother:</label>
    <input type="text" name="mother" value="<?= $row['mother'] ?>">
    
    <label><i class="fas fa-user-shield"></i> Guardian:</label>
    <input type="text" name="guardian_name" value="<?= $row['guardian_name'] ?>">
    
    <label><i class="fas fa-link"></i> Relationship:</label>
    <input type="text" name="relationship" value="<?= $row['relationship'] ?>">
    
    <label><i class="fas fa-phone"></i> Contact:</label>
    <input type="text" name="contact" value="<?= $row['contact'] ?>">
    
    <label><i class="fas fa-laptop"></i> Learning Modality:</label>
    <select name="learning_modality" required>
    <option value="Blended" <?= $row['learning_modality'] == 'Blended' ? 'selected' : '' ?>>Blended</option>
    <option value="Face-to-Face" <?= $row['learning_modality'] == 'Face-to-Face' ? 'selected' : '' ?>>Face-to-Face</option>
    <option value="Homeschooling" <?= $row['learning_modality'] == 'Homeschooling' ? 'selected' : '' ?>>Homeschooling</option>
    <option value="Modular" <?= $row['learning_modality'] == 'Modular' ? 'selected' : '' ?>>Modular</option>
    <option value="Online" <?= $row['learning_modality'] == 'Online' ? 'selected' : '' ?>>Online</option>
    </select>
    
    <label><i class="fas fa-calendar-alt"></i> School Year:</label>
    <select name="sy" id="sy" data-selected="<?= $row['sy'] ?>" required></select>

    <label><i class="fas fa-graduation-cap"></i> Grade Level:</label>
    <select name="grade" required>
    <option value="KINDER" <?= $row['grade'] == 'KINDER' ? 'selected' : '' ?>>KINDER</option>
    <option value="GRADE 1" <?= $row['grade'] == 'GRADE 1' ? 'selected' : '' ?>>GRADE 1</option>
    <option value="GRADE 2" <?= $row['grade'] == 'GRADE 2' ? 'selected' : '' ?>>GRADE 2</option>
    <option value="GRADE 3" <?= $row['grade'] == 'GRADE 3' ? 'selected' : '' ?>>GRADE 3</option>
    <option value="GRADE 4" <?= $row['grade'] == 'GRADE 4' ? 'selected' : '' ?>>GRADE 4</option>
    <option value="GRADE 5" <?= $row['grade'] == 'GRADE 5' ? 'selected' : '' ?>>GRADE 5</option>
    <option value="GRADE 6" <?= $row['grade'] == 'GRADE 6' ? 'selected' : '' ?>>GRADE 6</option>
    <option value="GRADE 7" <?= $row['grade'] == 'GRADE 7' ? 'selected' : '' ?>>GRADE 7</option>
    <option value="GRADE 8" <?= $row['grade'] == 'GRADE 8' ? 'selected' : '' ?>>GRADE 8</option>
    <option value="GRADE 9" <?= $row['grade'] == 'GRADE 9' ? 'selected' : '' ?>>GRADE 9</option>
    <option value="GRADE 10" <?= $row['grade'] == 'GRADE 10' ? 'selected' : '' ?>>GRADE 10</option>
    </select>

    <label><i class="fas fa-chalkboard"></i> Section:</label>
    <input type="text" name="section" value="<?= $row['section'] ?>">

    <label><i class="fas fa-chalkboard-teacher"></i> Adviser:</label>
    <input type="text" name="adviser" value="<?= $row['adviser'] ?>">
    
    <label><i class="fas fa-info-circle"></i> Status:</label>
    <select name="status" required>
    <option value="New Student" <?= $row['status'] == 'New Student' ? 'selected' : '' ?>>New Student</option>
    <option value="Old Student" <?= $row['status'] == 'Old Student' ? 'selected' : '' ?>>Old Student</option>
    <option value="Transfer In" <?= $row['status'] == 'Transfer In' ? 'selected' : '' ?>>Transfer In</option>
    <option value="Transfer Out" <?= $row['status'] == 'Transfer Out' ? 'selected' : '' ?>>Transfer Out</option>
    <option value="Returnee" <?= $row['status'] == 'Returnee' ? 'selected' : '' ?>>Returnee</option>
    <option value="Dropped Out" <?= $row['status'] == 'Dropped Out' ? 'selected' : '' ?>>Dropped Out</option>
    <option value="Graduated" <?= $row['status'] == 'Graduated' ? 'selected' : '' ?>>Graduated</option>
    </select>
    
    <label><i class="fas fa-comment"></i> Remarks:</label>
    <textarea name="remarks" rows="5"><?= $row['remarks'] ?></textarea>
    
    <button type="submit"><i class="fas fa-save"></i> Update</button>
</form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pre-select values from PHP
    var selectedRegion = "<?= isset($row['region']) ? addslashes($row['region']) : '' ?>";
    var selectedProvince = "<?= isset($row['province']) ? addslashes($row['province']) : '' ?>";
    var selectedMunicipality = "<?= isset($row['municipality']) ? addslashes($row['municipality']) : '' ?>";
    var selectedBarangay = "<?= isset($row['barangay']) ? addslashes($row['barangay']) : '' ?>";

    loadRegions();
    if(selectedRegion) {
        document.getElementById('region').value = selectedRegion;
        loadProvinces(selectedRegion);
    }
    if(selectedProvince) {
        document.getElementById('province').value = selectedProvince;
        loadMunicipalities(selectedProvince, selectedRegion);
    }
    if(selectedMunicipality) {
        document.getElementById('municipality').value = selectedMunicipality;
        loadBarangays(selectedMunicipality, selectedProvince, selectedRegion);
    }
    if(selectedBarangay) {
        document.getElementById('barangay').value = selectedBarangay;
    }

    document.getElementById('region').addEventListener('change', function() {
        loadProvinces(this.value);
        document.getElementById('municipality').innerHTML = "<option value=''>Select Municipality</option>";
        document.getElementById('barangay').innerHTML = "<option value=''>Select Barangay</option>";
    });
    document.getElementById('province').addEventListener('change', function() {
        loadMunicipalities(this.value, document.getElementById('region').value);
        document.getElementById('barangay').innerHTML = "<option value=''>Select Barangay</option>";
    });
    document.getElementById('municipality').addEventListener('change', function() {
        loadBarangays(this.value, document.getElementById('province').value, document.getElementById('region').value);
    });
});
</script>
</body>
</html>
