<?php
$page_title = "Admin Login";

require_once "dashboard-header.php";

if (isset($_POST["login"])) {
    login_admin($database_connection);
}
?>

    <section class="signin-page account p-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block">
                        <h2 class="text-center"><?php echo $page_title?></h2>

                        <form class="text-left clearfix mt-50" action="index.php" method="post">
                            <div class="form-group">
                                <label class="form-label font-weight-bold" for="username">Username</label>
                                <input id="username" name="username" type="text" class="form-control"  placeholder="Username"
                                       required value="<?php
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
                            <div class="form-group">
                                <label class="form-label font-weight-bold" for="password">Password</label>
                                <input type="password" id="password" name="password" class="form-control" placeholder="Password"
                                       required onfocus="hidePasswordErrorMessage()">
                                <div class="text-danger" id="password-error-message">
                                    <?php
                                    display_password_error_message($database_connection);
                                    ?>
                                </div>
                            </div>

                            <button type="submit" name="login" class="btn btn-main" >Login</button>
                        </form>

                        <script src="../js/login-validation.js"></script>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
/**
 * @param mysqli $database_connection
 */
function display_username_error_message(mysqli $database_connection) {
    if(isset($_POST["username"])) {
        if (!is_name_valid($_POST["username"])) {
            echo "Please enter a username";
        } else {
            $is_username_in_use = is_admin_username_in_use($database_connection);

            if (!$is_username_in_use) {
                echo $_POST["username"] . " not found";
            }
        }   //  end of else
    }
}

/**
 * @param mysqli $database_connection
 */
function display_password_error_message(mysqli $database_connection) {
    if (isset($_POST["password"])) {
        $admin = new Admin($database_connection, $_POST["username"], $_POST["password"]);

        if ($admin->password == null) {
            echo "Sorry, the password you entered is incorrect";
        }   //  end of if password is null
    }   //  end of if password is set
}

/**
 * @param mysqli $database_connection
 */
function login_admin(mysqli $database_connection) {
    $username = cleanse_data($_POST["username"], $database_connection);
    $password = cleanse_data($_POST["password"], $database_connection);

    $admin = new Admin($database_connection, $username, $password);

    if ($admin->is_found()) {
        session_start();
        $_SESSION["admin"] = $admin->username;

        $alert = "<script>
                    if (confirm('Login successful. You may proceed to your dashboard.')) {";
        $dashboard_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/dashboard.php";
        $alert .=           "window.location.replace('$dashboard_url');
                    } else {";
        $alert .=           "window.location.replace('$dashboard_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}

/**
 * @param mysqli $database_connection
 * @return int
 */
function is_admin_username_in_use(mysqli $database_connection): int {
    $is_username_in_use = 0;

    $admin = new Admin($database_connection, $_POST["username"]);

    if ($admin->is_found()) {
        $is_username_in_use = 1;
    }

    return $is_username_in_use;   //  end of if username is null
}

$database_connection->close();
require_once "footer.php";
?>