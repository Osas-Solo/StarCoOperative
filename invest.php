<?php
require_once "entities.php";

$page_title = "Invest";

require_once "header.php";

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $member = new Member($database_connection, $username);
} else {
    $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
    header("Location: " . $login_url);
}

if (isset($_GET["investment-plan"])) {
    $investment_plan_id = $_GET["investment-plan"];
}

$year = date("Y");

$investments = Investment::get_investments($database_connection, $year, $username);
$last_investment_month = Investment::get_recent_investment_month($investments);

$investment_plans = InvestmentPlan::get_investment_plans($database_connection);
?>

    <section class="account">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center"><?php echo $page_title?></h2>
                        <form class="text-left clearfix mt-50 was-validated" action="complete-investment.php" method="post">
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="investment-plan">Investment Plan</label>
                                <select id="investment-plan" name="investment-plan" class="form-select p-1 d-block"
                                        placeholder="Investment Plan" required>
                                    <?php
                                    foreach ($investment_plans as $current_investment_plan) {
                                        ?>
                                        <option value="<?php echo $current_investment_plan->plan_id?>"
                                            <?php
                                            if (isset($investment_plan_id)) {
                                                if ($current_investment_plan->plan_id == $investment_plan_id) {
                                                    echo "selected";
                                                }
                                            } else if (isset($_POST["investment-plan"])) {
                                                if ($current_investment_plan->plan_id == $_POST["investment-plan"]) {
                                                    echo "selected";
                                                }
                                            } else if ($member->has_investment()) {
                                                if ($current_investment_plan->plan_id == $member->investment_plan->plan_id) {
                                                    echo "selected";
                                                }
                                            }
                                            ?>>
                                            <?php echo $current_investment_plan->plan_name?>
                                        </option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="investment-amount">Investment Amount</label>
                                <input id="investment-amount" name="investment-amount" type="number" class="form-control"
                                       placeholder="Investment Amount" required
                                       value="<?php
                                       if (isset($_POST["investment-amount"])) {
                                           echo $_POST["investment-amount"];
                                       }
                                       ?>">
                                <div id="investment-amount-error" class="invalid-feedback">Please enter your last name</div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="payment-month">Payment Month</label>
                                <select id="payment-month" name="payment-month" class="form-select p-1 d-block"
                                        required>
                                    <?php
                                    $available_investment_months = get_available_investment_months($last_investment_month);
                                    $month_number = get_month_number($available_investment_months[0]);

                                    foreach ($available_investment_months as $current_available_investment_month) {
                                     ?>
                                        <option value="<?php echo $month_number?>"
                                            <?php
                                            if (isset($_POST["payment-month"])) {
                                                if ($current_available_investment_month == $_POST["payment-month"]) {
                                                    echo "selected";
                                                }
                                            }?>>
                                            <?php echo $current_available_investment_month?>
                                        </option>
                                    <?php
                                        $month_number++;
                                    }
                                    ?>
                                </select>
                            </div>

                            <button type="submit" name="proceed" class="btn btn-main text-center">Make Payment</button>
                        </form>

                        <script src="js/signup-validation.js"></script>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
function get_available_investment_months(int $last_investment_month): array {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
        "November", "December"];

    $available_investment_months = array();

    if ($last_investment_month >= 12) {
        return $months;
    } else {
        for ($i = $last_investment_month; $i < 12; $i++) {
            array_push($available_investment_months, $months[$i]);
        }
    }

    return $available_investment_months;
}

function get_month_number(string $month): int {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
        "November", "December"];

    return array_search($month, $months) + 1;
}

$database_connection->close();
require_once "footer.php";
?>