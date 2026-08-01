

<?php

$errors = [];  // errors array

// SAFE INPUT
$name   = trim($_POST['name'] ?? '');
$age    = trim($_POST['age'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');

/* ---------------- NAME VALIDATION ------------------ */

if ($name === '') {
    $errors['name'] = "Name is required.";
} elseif (!preg_match("/^[a-zA-Z ]{2,50}$/", $name)) {
    $errors['name'] = "Only letters allowed (2–50 characters).";
}


/* ---------------- AGE VALIDATION ------------------ */

$ageOptions = [
    'options' => [
        'min_range' => 1,
        'max_range' => 120
    ]
];

$ageValidated = filter_var($age, FILTER_VALIDATE_INT, $ageOptions);

if ($age === '') {
    $errors['age'] = "Age is required.";
} elseif ($ageValidated === false) {
    $errors['age'] = "Age must be a number between 1 and 120.";
}


/* ---------------- MOBILE NO VALIDATION ------------------ */

$mobileValidated = filter_var(
    $mobile,
    FILTER_VALIDATE_INT
);

if ($mobile === '') {
    $errors['mobile'] = "Mobile number is required.";
} elseif ($mobileValidated === false || strlen($mobile) != 10) {
    $errors['mobile'] = "Mobile must be a 10-digit number.";
}


/* ---------------- CHECK ERRORS ------------------ */

if (!empty($errors)) {
    echo "<h3 style='color:red;'>Form has errors:</h3>";
    foreach ($errors as $key => $val) {
        echo "<p>$val</p>";
    }
} else {
    echo "<h3 style='color:green;'>Form Submitted Successfully</h3>";
    echo "Name: $name <br>";
    echo "Age: $ageValidated <br>";
    echo "Mobile: $mobile <br>";
}

?>
