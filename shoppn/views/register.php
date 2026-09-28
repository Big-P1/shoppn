<?php
include 'layout/header.php';
?>

<h2>Customer Registration</h2>

<?php
if (isset($_GET['error'])) {
    echo "<p>" . $_GET['error'] . "</p>";
}
?>

<form action="../actions/register_action.php" method="POST" onsubmit="return validateForm()">
    <label>Name:</label>
    <input type="text" name="name" required><br><br>

    <label>Email:</label>
    <input type="email" name="email" required><br><br>

    <label>Password:</label>
    <input type="password" name="password" required><br><br>

    <label>Country:</label>
    <input type="text" name="country" required><br><br>

    <label>City:</label>
    <input type="text" name="city" required><br><br>

    <label>Contact:</label>
    <input type="text" name="contact" required><br><br>

    <button type="submit">Register</button>
</form><script>
function validateForm() {
    let name = document.getElementsByName("name")[0].value;
    let email = document.getElementsByName("email")[0].value;
    let password = document.getElementsByName("password")[0].value;
    let country = document.getElementsByName("country")[0].value;
    let city = document.getElementsByName("city")[0].value;
    let contact = document.getElementsByName("contact")[0].value;

    if (name == "" || email == "" || password == "" ||
        country == "" || city == "" || contact == "") {
        alert("Please fill in all required fields.");
        return false;
    }

    if (!email.includes("@")) {
        alert("Please enter a valid email.");
        return false;
    }

    if (password.length < 6) {
        alert("Password must be at least 6 characters.");
        return false;
    }

    return true;
}
</script>

<?php
include 'layout/footer.php';
?>