<?php
$page_title = "Update Profile";

require_once "dashboard-header.php";
require_once "NUBAN.php";

if (isset($_POST["update"])) {
    update_member_profile($database_connection);
}

$banks = Bank::get_banks($database_connection);
?>

    <section class="signin-page account">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center"><?php echo $page_title?></h2>
                        <form class="text-left clearfix mt-50 was-validated" action="update-profile.php" method="post">
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="first-name">First Name</label>
                                <input id="first-name" name="first-name" type="text" class="form-control"  placeholder="First Name" required
                                       value="<?php
                                       if (isset($_POST["first-name"])) {
                                           echo $_POST["first-name"];
                                       } else {
                                           echo $member->first_name;
                                       }
                                       ?>">
                                <div class="invalid-feedback">Please enter your first name</div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="last-name">Last Name</label>
                                <input id="last-name" name="last-name" type="text" class="form-control"  placeholder="Last Name" required
                                       value="<?php
                                       if (isset($_POST["last-name"])) {
                                           echo $_POST["last-name"];
                                       } else {
                                           echo $member->last_name;
                                       }
                                       ?>">
                                <div class="invalid-feedback">Please enter your last name</div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="bank">Bank</label>
                                <select id="bank" name="bank" type="text" class="form-select p-1 d-block" placeholder="Bank" required>
                                    <?php
                                    foreach ($banks as $current_bank) {
                                        ?>
                                        <option value="<?php echo $current_bank->bank_code?>"
                                            <?php
                                            if (isset($_POST["bank"])) {
                                                if ($current_bank->bank_code == $_POST["bank"]) {
                                                    echo "selected";
                                                }
                                            } else if ($current_bank->bank_code == $member->bank->bank_code) {
                                                echo "selected";
                                            }?>>
                                            <?php echo $current_bank->bank_name?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="bank-account-number">Bank Account Number</label>
                                <input id="bank-account-number" name="bank-account-number" type="text" class="form-control"
                                       placeholder="Bank Account Number" minlength="10" maxlength="10" pattern="\d{10}" required value="<?php
                                    if (isset($_POST["bank-account-number"])) {
                                        echo $_POST["bank-account-number"];
                                    } else {
                                        echo $member->bank_account_number;
                                    }
                                    ?>" onfocus="hideBankAccountNumberErrorMessage()">
                                <div class="text-danger" id="bank-account-number-error-message">
                                    <?php
                                    if (isset($_POST["bank-account-number"])) {
                                        $nuban = new NUBAN($_POST["bank"], $_POST["bank-account-number"]);

                                        if (!$nuban->validate()) {
                                            echo "Please enter a valid bank account number";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="phone-number">Phone Number</label>
                                <input id="phone-number" name="phone-number" type="text" class="form-control"  placeholder="Phone Number"
                                       minlength="11" maxlength="11" pattern="0[7-9][0-1]\d{8}" required value="<?php
                                    if (isset($_POST["phone-number"])) {
                                        echo $_POST["phone-number"];
                                    } else {
                                        echo $member->phone_number;
                                    }
                                ?>" onfocus="hidePhoneNumberErrorMessage()">
                                <div class="text-danger" id="phone-number-error-message">
                                    <?php
                                    if (isset($_POST["phone-number"])) {
                                        if (!is_phone_number_valid($_POST["phone-number"])) {
                                            echo "Please enter a valid phone number";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="email-address">Email Address</label>
                                <input id="email-address" name="email-address" type="email" class="form-control"  placeholder="Email"
                                       required value="<?php
                                    if (isset($_POST["email-address"])) {
                                        echo $_POST["email-address"];
                                    } else {
                                        echo $member->email_address;
                                    }
                                    ?>" onfocus="hideEmailAddressErrorMessage()">
                                <div class="text-danger" id="email-address-error-message">
                                    <?php
                                    if (isset($_POST["email-address"])) {
                                        if (!is_email_address_valid($_POST["email-address"])) {
                                            echo "Please enter a valid email address";
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-check-label font-weight-bold" for="gender">Gender</label><br>
                                <label class="mr-3"><input type="radio" name="gender" class="" value="M"
                                    <?php
                                    if ($member->is_male()) {
                                        echo 'checked';
                                    }?>> Male</label>
                                <label><input type="radio" name="gender" class="" value="F"
                                    <?php
                                    if ($member->is_female()) {
                                        echo 'checked';
                                    }?>> Female</label>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="password">Current Password</label>
                                <input id="password" name="password" type="password" class="form-control"  placeholder="Password" required
                                       oninput="checkPasswordValidity()">
                                <div id="password-error-message">
                                    <?php
                                    display_password_error_message($database_connection);
                                    ?>
                                </div>
                            </div>

                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold d-block" for="investment-plan">Investment Plan</label>
                                <input id="investment-plan" name="investment-plan" type="text" class="form-control"  placeholder="Investment Plan" readonly
                                       value="<?php echo $member->investment_plan->plan_name?>">
                                <div class="mt-2">
                                    <h3 class="btn btn-main">
                                        <a href="investment-plans.php" target="_blank">
                                            Change Investment Plan
                                        </a>
                                    </h3>
                                </div>
                            </div>

                            <button type="submit" name="update" class="btn btn-main text-center">Update</button>
                        </form>

                        <script src="js/signup-validation.js"></script>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php
/**
 * @param mysqli $database_connection
 */
function update_member_profile(mysqli $database_connection) {
    $first_name = cleanse_data($_POST["first-name"], $database_connection);
    $last_name = cleanse_data($_POST["last-name"], $database_connection);
    $username = $_SESSION["username"];
    $email_address = cleanse_data($_POST["email-address"], $database_connection);
    $phone_number = cleanse_data($_POST["phone-number"], $database_connection);
    $gender = cleanse_data($_POST["gender"], $database_connection);
    $password = cleanse_data($_POST["password"], $database_connection);
    $bank_code = cleanse_data($_POST["bank"], $database_connection);
    $bank = new Bank($database_connection, $bank_code);
    $bank_account_number = cleanse_data($_POST["bank-account-number"], $database_connection);

    $member = new Member($database_connection, $username, $password);

    $nuban = new NUBAN($bank_code, $bank_account_number);


    if ($member->is_found() && $nuban->validate()) {
        $update_query = "UPDATE members SET first_name = '$first_name', last_name = '$last_name', 
            email_address = '$email_address', phone_number = '$phone_number', gender = '$gender', 
            bank_id = $bank->bank_id, bank_account_number = '$bank_account_number' WHERE user_id = $member->user_id";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        if ($database_connection->query($update_query)) {
            $alert = "<script>
                    if (confirm('Profile updated successfully.')) {";
            $dashboard_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/dashboard.php";
            $alert .= "window.location.replace('$dashboard_url');
                    } else {";
            $alert .= "window.location.replace('$dashboard_url');
                    }";
            $alert .= "</script>";

            echo $alert;
        }
    }   //  end of if details are valid
}

/**
 * @param mysqli $database_connection
 */
function display_password_error_message(mysqli $database_connection) {
    if (isset($_POST["password"])) {
        $member = new Member($database_connection, $_SESSION["username"], $_POST["password"]);

        if ($member->password == null) {
            echo "Sorry, the password you entered is incorrect";
        }   //  end of if password is null
    }   //  end of if password is set
}

$database_connection->close();
require_once "footer.php";
?>