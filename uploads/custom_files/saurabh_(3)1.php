<!--  <?php
$email = "<h1>saurabhpp7082@gmail.com</h1>";
echo $email;
$cleanemail = filter_var($email, FILTER_SANITIZE_EMAIL);
echo $cleanemail;
echo"<br>";
$ip = "192.168.1.1";
echo filter_var($ip, FILTER_VALIDATE_IP) ?"Valid" : "Invalid"; 
echo "<br>";
echo "<br>";

$amt = "Rs. 55,690.78 INR";
echo filter_var($amt, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);




echo "<h3> filter validate boolean </h3>";

echo "<br>";


var_dump(filter_var("true", FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));  

echo "<br>";
echo "<h3> filter validate domain </h3>";
var_dump(filter_var("google.com", FILTER_VALIDATE_DOMAIN, ));   

echo "<br>";
echo "<h3> filter validate email </h3>";
$email = "saurabh@gmail.com";
var_dump(filter_var($email, FILTER_VALIDATE_EMAIL));
echo "<br>";
echo "<h3> filter validate float </h3>";
var_dump(filter_var("69.78", FILTER_VALIDATE_FLOAT));
echo "<br>";
echo "<h3> filter validate int </h3>";
var_dump(filter_var("5690", FILTER_VALIDATE_INT));
echo "<br>";
echo "<h3> filter validate IP address </h3>";
var_dump(filter_var("192016801.1", FILTER_VALIDATE_IP));
echo "<br>";
echo "<h3> filter validate MAC </h3>";
var_dump(filter_var("00:1B:44:11:3A:B7", FILTER_VALIDATE_MAC));
echo "<br>";
echo "<h3> filter validate URL </h3>";
var_dump(filter_var("https://www.google.com", FILTER_VALIDATE_URL));
echo "<br>";
echo "<h3> FILTER VALIDATE REGEXP </h3>";
$data = "hello123";
var_dump(filter_var($data, FILTER_VALIDATE_REGEXP,["options"=>["regexp"=>"/^[a-z]+[0-9]+$/"]]));
echo "<br>";
echo "<br> <br>";
echo "<h1> FILTER SANITIZE STRING  </h1>";


echo "<br>";
echo "<h3> filter sanitize email </h3>";
echo filter_var("te*st@gm<>ail.com", FILTER_SANITIZE_EMAIL);
echo "<br>";
echo "<h3> filter sanitize ENCODED </h3>";
echo filter_var("hello world!", FILTER_SANITIZE_ENCODED);
echo "<br>";
echo "<h3> filter sanitize full special chars </h3>";
echo filter_var("<h1>hello@123</h1>", FILTER_SANITIZE_FULL_SPECIAL_CHARS);
echo "<br>";

?> -->

<!-- <?php

$errors = [];


$name = trim($_POST['name'] ?? '');
$age = trim($_POST['age'] ?? '');
$mobile = trim($_POST['mobile'] ?? '');


// name validation 


if ($name == '') {
    $error['name'] = "Name is required";
} elseif (!preg_match("/^[a-zA-Z ]+$/", $name)) {
    $error['name'] = "Only letters and white space allowed";
}

// age validation

$ageoptions = [
    'options' => [
        'min_range' => 1,
        'max_range' => 120
    ]
    ];
    $agevalidated = filter_var($age, FILTER_VALIDATE_INT, $ageoptions);
    if ($age == '') {
        $error['age'] = "Age is required";
    } elseif ($agevalidated === false) {
        $error['age'] = "Age must be a valid number between 1 and 120";
    }

// mobile validation

$mobilvalidated = filter_var($mobile, FILTER_SANITIZE_NUMBER_INT);
if ($mobile == '') {
    $errors['mobile'] = "Mobile number is required";
} elseif (strlen($mobilvalidated) < 10 || strlen($mobilvalidated) > 15) {
    $error['mobile'] = "Mobile number must be between 10 to 15 digits";
}


// chack errors 

if (!empty($errors)) {
    echo "<h3 style='color:red;'>Form has errors:</h3>";
} else {
    foreach ($error as $key => $val) {
        echo "<p>$val: </p>";
    }
} 
//    else {
    echo "<h3 style='color:green;'>Form Submitted Successfully</h3>";
    echo "Name: $name <br>";
    echo "Age: $ageValidated <br>";
    echo "Mobile: $mobile <br>";
// }

?> --> /



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
