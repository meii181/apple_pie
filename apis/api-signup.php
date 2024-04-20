<?php
//connect to database
require_once("globals.php");
session_start();

if(isset($_POST["submit-signup"])){

if(empty($_POST["name"]) || empty($_POST["last_name"]) || empty($_POST["email"]) || empty($_POST["pwd"]) || empty($_POST["pwd-repeat"])){
    header("Location:../signup.php?error=emptyfields");
    exit();
}

else if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)){
    header("Location:../signup.php?error=invalidemail");
    exit();
}

 else if(strlen ($_POST["name"]) < _NAME_MIN_LEN){
    header("Location:../signup.php?error=mincharacters_2");
    exit();
}

 else if(strlen ($_POST["last_name"]) < 4){
    header("Location:../signup.php?error=mincharacters_4");
    exit();
}

 else if(strlen ($_POST["name"]) > _NAME_MAX_LEN){
    header("Location:../signup.php?error=maxcharacters_10");
    exit();
}

 else if(strlen ($_POST["last_name"]) > 11){
    header("Location:../signup.php?error=maxcharacters_11");
    exit();
}

else if (!isset ($_POST["nickname"])){
    header("Location: ../signup.php?error=requirednickname");
    exit();
}

else if(strlen($_POST["nickname"]) < 5){
    header("Location: ../signup.php?error=mincharacters_5");
}

 else if(strlen($_POST["pwd"]) < _PASSWORD_MIN_LEN){
    header("Location:../signup.php?error=mincharacters_7");
    exit();
}

 else if(strlen($_POST["pwd"]) > _PASSWORD_MAX_LEN){
    header("Location:../singup.php?error=maxcharacters_15");
    exit();
}

 else if($_POST["pwd"] !== $_POST["pwd-repeat"]){
    header("Location:../signup.php?error=passworddoesnotmatch");
    exit();
 }

 else if (!isset($_POST["phone"])){
    header("Location: ../signup.php?error=requiredphonenumber");
    exit();
}

else if (!isset($_POST["confirm-phone"])){
    header("Location: ../signup.php?error=confirmphonenumber");
    exit();
}

else if (strlen($_POST["phone"]) !== 8 ){
    header("Location: ../signup.php?error=mustbedanishnumber");
    exit();
}

else if ($_POST["phone"] !== $_POST["confirm-phone"]){
    header("Location: ../signup.php?error=phonenumberdoesnotmatch");
    exit();
}


}


try{

    $db = _db();

    //password_hash
    $hash = password_hash($_POST["pwd"], PASSWORD_DEFAULT);
    $verification_key = bin2hex(random_bytes(16));
    $forgotten_password_key = bin2hex(random_bytes(16));
    $lang = $_GET["lang"] ?? "en";
    


    //check if email and phone are taken or not
    $q = $db->prepare("SELECT * FROM customers WHERE user_email = :user_email AND phone_number = :phone_number");
    $q->bindValue(":user_email", $_POST["email"]);
    $q->bindValue(":phone_number", $_POST["phone"]);
    $q->execute();
    $row = $q->fetch(); 

    if($row > 0){
        header("Location:../signup.php?error=emailistaken");
        exit();

    } else {

        $q = $db->prepare("SELECT * FROM customers WHERE phone_number = :phone_number");
        $q->bindValue(":phone_number", $_POST["phone"]);
        $q->execute();
        $row = $q->fetch(); 
    
        if($row > 0){
            header("Location:../signup.php?error=phoneistaken");
            exit();

        } else {

        $q = $db->prepare("INSERT INTO customers VALUES(:user_id, :user_name, :user_last_name, :user_email, 
        :user_password, :phone_number, :verification_key, :forgotten_password, :verified_user, :user_nickname)");
        
        $q->bindValue(":user_id", null);
        $q->bindValue(":user_name", $_POST["name"]);
        $q->bindValue(":user_last_name", $_POST["last_name"]);
        $q->bindValue(":user_email", $_POST["email"]);
        $q->bindValue(":user_password", $hash);
        $q->bindValue(":phone_number", $_POST["phone"]);
        $q->bindValue(":verification_key", $verification_key);
        $q->bindValue(":forgotten_password", $forgotten_password_key);
        $q->bindValue(":verified_user", false);
        $q->bindValue(":user_nickname", $_POST["nickname"]);
        $q->execute();

        $id = $db->lastInsertId();

        $to_email = $_POST["email"];
        $subject = "Account confirmation";
        $message = "Welcome to Apple Pie. Please click the link down below to verify your account:
        <a href='http://localhost/reexam/apis/api-validate-user.php?verification_key=$verification_key'>Click here!</a>";

        require_once("../apis/email_verify/send_email.php");

        $to_phone = $_POST["phone"];
        $sms_message = "Welcome, here you can verify your account: <a href='http://localhost/reexam/apis/api-validate-phone.php?verification_key=$verification_key>Tap here!</a>";

        require_once("../apis/email_verify/send_sms.php");

        $_SESSION["user_name"] = $_POST["name"];

        header("Location: ../verify-sent.php");
        exit();
    }
}

} catch(Exception $ex){
    http_response_code(500);
    echo "System not working";
    exit();
}