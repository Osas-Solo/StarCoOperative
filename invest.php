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
$last_investment_month_number = Investment::get_recent_investment_month($investments);

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
                                        placeholder="Investment Plan" required onchange="updateInvestmentRange()">
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
                                <label class="form-label font-weight-bold" for="investment-amount">Investment Amount (&#8358;)</label>
                                <input id="investment-amount" name="investment-amount" type="number" class="form-control"
                                       placeholder="Investment Amount" required step="0.01"
                                       value="<?php
                                       if (isset($_POST["investment-amount"])) {
                                           echo $_POST["investment-amount"];
                                       }
                                       ?>">
                                <div id="investment-amount-message"></div>
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="payment-month">Payment Month</label>
                                <select id="payment-month" name="payment-month" class="form-select p-1 d-block"
                                        required>
                                    <?php
                                    if (!$member->has_investment()) {
                                        $available_investment_month_number = date("m");
                                        $available_investment_month = get_month($available_investment_month_number);
                                    } else {
                                        $available_investment_month_number = ($last_investment_month_number == 12) ? 1 :
                                            $last_investment_month_number + 1;
                                        $available_investment_month = get_month($available_investment_month_number);
                                    }
                                    ?>
                                        <option value="<?php echo $available_investment_month?>"
                                            <?php
                                            if (isset($_POST["payment-month"])) {
                                                if ($available_investment_month == $_POST["payment-month"]) {
                                                    echo "selected";
                                                }
                                            }?>>
                                            <?php echo $available_investment_month?>
                                        </option>
                                </select>
                                <div id="investment-month-message">
                                    You can only make a monthly investment for a month following the last month you
                                    invested at unless you are a first investor where you would have to place an
                                    investment on this current month
                                </div>
                            </div>

                            <button type="submit" name="proceed" class="btn btn-main text-center">Make Payment</button>
                        </form>

                        <script src="js/invest-page-update.js"></script>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
$database_connection->close();
require_once "footer.php";
?>