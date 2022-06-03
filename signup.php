<?php
require_once "entities.php";
require_once "NUBAN.php";

$page_title = "Become a Member";

require_once "header.php";

if (isset($_SESSION["username"])) {
    session_destroy();
}

if (isset($_POST["signup"])) {
    signup_member($database_connection);
}

$banks = Bank::get_banks($database_connection);
?>

<section class="signin-page account">
  <div class="container">
    <div class="row">
      <div class="col-md-6 mx-auto">
        <div class="block text-center">
          <h2 class="text-center"><?php echo $page_title?></h2>
          <form class="text-left clearfix mt-50 was-validated" action="signup.php" method="post">
            <div class="form-group mb-4">
              <label class="form-label font-weight-bold" for="first-name">First Name</label>
              <input id="first-name" name="first-name" type="text" class="form-control"  placeholder="First Name" required
                     value="<?php
                     if (isset($_POST["first-name"])) {
                         echo $_POST["first-name"];
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
                     }
                     ?>">
                <div class="invalid-feedback">Please enter your last name</div>
            </div>
            <div class="form-group mb-4">
              <label class="form-label font-weight-bold" for="username">Username</label>
              <input id="username" name="username" type="text" class="form-control"  placeholder="Username" required value="<?php
                  if (isset($_POST["username"])) {
                      echo $_POST["username"];
                  }
                  ?>" onfocus="hideUserNameErrorMessage()">
                <div class="text-danger" id="username-error-message">
                    <?php
                    display_username_error_message($database_connection);
                    ?>
                </div>
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
              <label class="mr-3"><input type="radio" name="gender" class="" value="M" checked> Male</label>
              <label><input type="radio" name="gender" class="" value="F"> Female</label>
            </div>
            <div class="form-group mb-4">
              <label class="form-label font-weight-bold" for="password">Password</label>
              <input id="password" name="password" type="password" class="form-control"  placeholder="Password" required
                     oninput="checkPasswordValidity()">
                <div>
                    Password length should be at least 8 characters.
                    Password must contain a lowercase character, uppercase character and a digit

                    <br><br>
                    <span class="text-danger" id = "password-error-message"
                    <?php
                    if(isset($_POST["password"])) {
                        if (!is_password_valid($_POST["password"])) {
                            echo "";
                        } else {
                            echo "style = 'display: none'";
                        }
                    } else {
                        echo "style = 'display: none'";
                    }
                    ?>
                    >Please enter a valid password</span>
                </div>
            </div>
            <div class="form-group mb-4">
              <label class="form-label font-weight-bold" for="confirm-password">Confirm Password</label>
              <input id="confirm-password" name="confirm-password" type="password" class="form-control"
                     placeholder="Confirm Password" required oninput="checkPasswordConfirmation()">
                <div class="text-danger" id = "confirm-password-error-message"
                    <?php
                    if (isset($_POST["confirm-password"])) {
                        if (!is_password_confirmed($_POST["password"], $_POST["confirm-password"])) {
                            echo "";
                        } else {
                            echo "style = 'display: none'";
                        }
                    } else {
                        echo "style = 'display: none'";
                    }
                    ?>>
                    Passwords do not match
                </div>
            </div>

            <button type="submit" name="signup" class="btn btn-main text-center">Sign Up</button>
          </form>

            <script src="js/signup-validation.js"></script>

            <p class="mt-20">Already have an account?<a href="login.php"> Login instead</a></p>

        </div>
      </div>
    </div>
  </div>
</section>

<?php
/**
 * @param mysqli $database_connection
 */
function signup_member(mysqli $database_connection) {
    $first_name = cleanse_data($_POST["first-name"], $database_connection);
    $last_name = cleanse_data($_POST["last-name"], $database_connection);
    $username = cleanse_data($_POST["username"], $database_connection);
    $email_address = cleanse_data($_POST["email-address"], $database_connection);
    $phone_number = cleanse_data($_POST["phone-number"], $database_connection);
    $gender = cleanse_data($_POST["gender"], $database_connection);
    $password = cleanse_data($_POST["password"], $database_connection);
    $password_confirmer = cleanse_data($_POST["confirm-password"], $database_connection);
    $bank_code = cleanse_data($_POST["bank"], $database_connection);
    $bank = new Bank($database_connection, $bank_code);
    $bank_account_number = cleanse_data($_POST["bank-account-number"], $database_connection);

    $nuban = new NUBAN($bank_code, $bank_account_number);

    $is_username_in_use = is_username_in_use($database_connection);

    if (!$is_username_in_use) {
        if (is_name_valid($first_name) && is_name_valid($last_name) && is_name_valid($username)
            && is_email_address_valid($email_address) && is_password_valid($password)
            && is_password_confirmed($password, $password_confirmer) && is_phone_number_valid($phone_number)
            && $nuban->validate()) {

            $insert_query = "INSERT INTO members (username, first_name, last_name, bank_account_number, bank_id, gender, 
                            email_address, phone_number, password) VALUES 
                            ('$username', '$first_name', '$last_name', '$bank_account_number', $bank->bank_id, '$gender',
                             '$email_address', '$phone_number', SHA('$password'))";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            if ($database_connection->query($insert_query)) {
                $alert = "<script>
                        if (confirm('You\'ve successfully completed your registration. You may now proceed to login.')) {";
                $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
                $alert .= "window.location.replace('$login_url');
                        } else {";
                $alert .=           "window.location.replace('$login_url');
                    }";
                $alert .= "</script>";

                echo $alert;
            }
        }   //  end of if details are valid
    }
}

/**
 * @param mysqli $database_connection
 */
function display_username_error_message(mysqli $database_connection) {
    if (isset($_POST["username"])) {
        if (!is_name_valid($_POST["username"])) {
            echo "Please enter a username";
        } else {
            $is_username_in_use = is_username_in_use($database_connection);

            if ($is_username_in_use) {
                echo $_POST["username"] . " is already in use";
            }
        }   //  end of else
    }   //  if username is set
}

$database_connection->close();
require_once "footer.php";
?>