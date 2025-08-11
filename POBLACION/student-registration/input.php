<?php
include __DIR__ . '/../shared-source/database-connection/connect.php';//database connection path
if(isset($_POST['submit'])){
    $lrn = $_POST['lrn'];
    
    // Check if LRN already exists
    $check_sql = "SELECT * FROM poblacion WHERE lrn = '$lrn'";
    $check_result = mysqli_query($con, $check_sql);
    
    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Error: LRN already exists in the database!'); window.location.href='input.php';</script>";
        exit();
    }
    
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

    $sql = "INSERT INTO `poblacion` (lrn, name, sex, birthday, age, mother_tongue, ip, religion, house_number, barangay, municipality, province,region, father, mother, guardian_name, relationship, contact, learning_modality, sy, grade, section, adviser, status, remarks, created_at) 
            VALUES ('$lrn','$name','$sex','$birthday','$age','$mother_tongue','$ip','$religion','$house_number','$barangay','$municipality','$province','$region','$father','$mother','$guardian_name','$relationship','$contact','$learning_modality','$sy','$grade','$section','$adviser','$status','$remarks', CURRENT_TIMESTAMP)";
    $result = mysqli_query($con, $sql);
    if($result){
        header('location:view.php');
    }else{
        die(mysqli_error($con));
    }
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/input-style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <title>Poblacion School Form</title>
</head>
<body>
    <header>
        <h1><i class="fas fa-user-graduate"></i> Student Registration</h1>
        <a href="home.php"><button class="back-btn"><i class="fas fa-arrow-left"></i> Back</button></a>
    </header>
    
    <div class="con-form">
        <form method="post">
            <label><i class="fas fa-id-card"></i> LRN:</label>
            <input type="text" name="lrn" required>

            <label><i class="fas fa-user"></i> Name:(Last Name, First Name, Middle Name)</label>
            <input type="text" name="name" required>
            
            <div class="radio-group">
            <label><i class="fas fa-venus-mars"></i> Sex:</label>
            <label for="male">Male</label> 
            <input type="radio" id="male" class="rbtn" name="sex" value="MALE" required>
            <label for="female">Female</label> 
            <input type="radio" id="female" class="rbtn" name="sex" value="FEMALE" required> 
            </div>

            <label><i class="fas fa-calendar"></i> Birthday:</label>
            <input type="date" name="birthday" required>

            <label><i class="fas fa-birthday-cake"></i> Age:</label>
            <input type="number" name="age" required>

            <label><i class="fas fa-language"></i> Mother Tongue:</label>
            <select name="mother_tongue" required>
                <option value="Cebuano">Cebuano</option>
                <option value="English">English</option>
                <option value="Ilocano">Ilocano</option>
                <option value="Tagalog" selected>Tagalog</option>
            </select>

            <div class="form-group">
                <label><i class="fas fa-users"></i> IP:(Ethnic Group)</label>
                <input type="text" class="form-control" name="ip" autocomplete="off">
            </div>

            <label><i class="fas fa-pray"></i> Religion:</label>
            <select name="religion" required>
                <option>Adventist</option>
                <option>Baptism</option>
                <option selected>Christianity</option>
                <option>INC</option>
                <option>Islam</option>
                <option>Others</option>
            </select>

            <label><i class="fas fa-home"></i> House Number:(Street Sitio/Purok)</label>
            <input type="text" name="house_number">

            <label><i class="fas fa-globe-asia"></i> Region</label>
            <select id="region" name="region" required>
                <option value="">Select Region</option>
            </select>

            <label><i class="fas fa-map"></i> Province:</label>
            <select id="province" name="province" required>
                <option value="">Select Province</option>
            </select>

            <label><i class="fas fa-city"></i> Municipality/City:</label>
            <select id="municipality" name="municipality" required>
                <option value="">Select Municipality/City</option>
            </select>

            <label><i class="fas fa-map-marker-alt"></i> Barangay:</label>
            <select id="barangay" name="barangay" required>
                <option value="">Select Barangay</option>
            </select>

            <div class="form-group">
                <label><i class="fas fa-male"></i> Father:(Last Name, First Name, Middle Name)</label>
                <input type="text" class="form-control" name="father" autocomplete="off">
            </div>

            <div class="form-group">
                <label><i class="fas fa-female"></i> Mother:(Last Name, First Name, Middle Name)</label>
                <input type="text" class="form-control" name="mother" autocomplete="off">
            </div>

            <div class="form-group">
                <label><i class="fas fa-user-shield"></i> Guardian Name:</label>
                <input type="text" class="form-control" name="guardian_name" autocomplete="off">
            </div>

            <div class="form-group">
                <label><i class="fas fa-link"></i> Relationship:</label>
                <input type="text" class="form-control" name="relationship" autocomplete="off">
            </div>

            <div class="form-group">
                <label><i class="fas fa-phone"></i> Contact Number:(Parent or Guardian)</label>
                <input type="text" class="form-control" name="contact" autocomplete="off">
            </div>

            <label><i class="fas fa-laptop"></i> Learning Modality:</label>
            <select name="learning_modality" required>
                <option>Blended</option>
                <option selected>Face-to-Face</option>
                <option>Homeschooling</option>
                <option>Modular</option>
                <option>Online</option>
            </select>

            <label><i class="fas fa-calendar-alt"></i> School Year:</label>
            <select name="sy" id="sy" required>
                <option value="">Select School Year</option>
            </select>

            <label><i class="fas fa-graduation-cap"></i> Grade Level:</label>
            <select name="grade" required>
                <option value="KINDER">KINDER</option>
                <option value="GRADE 1">GRADE 1</option>
                <option value="GRADE 2">GRADE 2</option>
                <option value="GRADE 3">GRADE 3</option>
                <option value="GRADE 4">GRADE 4</option>
                <option value="GRADE 5">GRADE 5</option>
                <option value="GRADE 6">GRADE 6</option>
                <option value="GRADE 7">GRADE 7</option>
                <option value="GRADE 8">GRADE 8</option>
                <option value="GRADE 9">GRADE 9</option>
                <option value="GRADE 10">GRADE 10</option>
            </select>
    
            <label><i class="fas fa-chalkboard"></i> Section:</label>
            <input type="text" name="section" required>

            <label><i class="fas fa-chalkboard-teacher"></i> Adviser:</label>
            <input type="text" name="adviser" required>

            <label><i class="fas fa-info-circle"></i> Status:</label>
            <select name="status" required>
            <option>New Student</option>
            <option selected>Old Student</option>
            <option>Transfer In</option>
            <option>Transfer Out</option>
            <option>Returnee</option>
            <option>Dropped Out</option>
            <option>Graduated</option>
            </select>

            <label><i class="fas fa-comment"></i> Remarks:</label>
            <textarea name="remarks" rows="5"></textarea>

            <button type="submit" name="submit"><i class="fas fa-save"></i> Submit</button>
        </form>
    </div>
    <script src="philippine.js"></script>
    <script src="sy.js"></script>
    <script src="main.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        loadRegions();
        document.getElementById('region').addEventListener('change', function() {
            loadProvinces(this.value);
            document.getElementById('municipality').innerHTML = "<option value=''>Select Municipality/City</option>";
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