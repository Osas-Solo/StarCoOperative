<?php
$page_title = "Investment Plans";

require_once "dashboard-header.php";

$investment_plans = InvestmentPlan::get_investment_plans($database_connection);

if (isset($_POST["update"])) {
    update_investment_plans($database_connection);
}
?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Investment Plans</h2>
                    </div>

                    <div class="col-12">
                        <form action="investment-plans.php" method="post" class="was-validated">
                            <table class="table table-striped table-hover table-sm table-responsive text-center mb-5">
                            <thead>
                            <tr>
                                <th>Investment Plan</th>
                                <th>Minimum Monthly Investment Amount (&#8358;)</th>
                                <th>Maximum Monthly Investment Amount (&#8358;)</th>
                                <th>Minimum Loan Entitled (&#8358;)</th>
                                <th>Maximum Loan Entitled (&#8358;)</th>
                                <th>Loan Interest Rate %</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($investment_plans as $current_investment_plan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <input class="form-control d-none" type="number" name="investment-plan-ids[]"
                                               value="<?php echo $current_investment_plan->plan_id?>">
                                        <input class="form-control" type="text" name="investment-plans[]" readonly
                                               value="<?php echo $current_investment_plan->plan_name?>">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="minimum-monthly-investment-amounts[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-minimum-monthly-investment-amount"
                                               value="<?php echo $current_investment_plan->minimum_monthly_investment_amount?>"
                                                step="0.01" min="10000" oninput="setInputLimits()">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="maximum-monthly-investment-amounts[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-maximum-monthly-investment-amount"
                                               value="<?php echo $current_investment_plan->maximum_monthly_investment_amount?>"
                                                step="0.01">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="minimum-loans-entitled[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-minimum-loan-entitled"
                                               value="<?php echo $current_investment_plan->minimum_loan_entitled?>"
                                                step="0.01" min="200000">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="maximum-loans-entitled[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-maximum-loan-entitled"
                                               value="<?php echo $current_investment_plan->maximum_loan_entitled?>"
                                                step="0.01" min="200000" max="2000000" oninput="setInputLimits()">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="loan-interest-rates[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-loan-interest-rate"
                                               value="<?php echo $current_investment_plan->loan_interest_rate?>"
                                                step="0.01" min="5" max="20" oninput="setInputLimits()">
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>

                            <div class="col-12 mb-5">
                                <button type="submit" class="btn btn-main mx-auto d-block" name="update">Update</button>
                            </div>
                        </form>

                        <script src="../js/investment-plans-form-updater.js"></script>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php
function update_investment_plans(mysqli $database_connection) {
    $plan_ids = array();
    $minimum_monthly_investments = array();
    $maximum_monthly_investments = array();
    $minimum_loans_entitled = array();
    $maximum_loans_entitled = array();
    $loan_interest_rates = array();

    foreach ($_POST["investment-plan-ids"] as $current_investment_plan_id) {
        array_push($plan_ids, $current_investment_plan_id);
    }

    foreach ($_POST["minimum-monthly-investment-amounts"] as $current_minimum_monthly_investment_amount) {
        array_push($minimum_monthly_investments, $current_minimum_monthly_investment_amount);
    }

    foreach ($_POST["maximum-monthly-investment-amounts"] as $current_maximum_monthly_investment_amount) {
        array_push($maximum_monthly_investments, $current_maximum_monthly_investment_amount);
    }

    foreach ($_POST["minimum-loans-entitled"] as $current_minimum_loan_entitled) {
        array_push($minimum_loans_entitled, $current_minimum_loan_entitled);
    }

    foreach ($_POST["maximum-loans-entitled"] as $current_maximum_loan_entitled) {
        array_push($maximum_loans_entitled, $current_maximum_loan_entitled);
    }

    foreach ($_POST["loan-interest-rates"] as $current_loan_interest_rate) {
        array_push($loan_interest_rates, $current_loan_interest_rate);
    }

    $update_investment_plans_query = "";
    $number_of_investment_plans = 3;

    for ($i = 0; $i < $number_of_investment_plans; $i++) {
        $update_investment_plans_query .= "UPDATE investment_plans 
                                            SET minimum_monthly_investment_amount = $minimum_monthly_investments[$i],
                                            maximum_monthly_investment_amount = $maximum_monthly_investments[$i],
                                            minimum_loan_entitled = $minimum_loans_entitled[$i],
                                            maximum_loan_entitled = $maximum_loans_entitled[$i],
                                            loan_interest_rate = $loan_interest_rates[$i]
                                            WHERE plan_id = $plan_ids[$i];";
    }

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->multi_query($update_investment_plans_query)) {
        $alert = "<script>
                    if (confirm('Investment plans updated successfully')) {";
        $investment_plans_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/investment-plans.php";
        $alert .=           "window.location.replace('$investment_plans_url');
                    } else {";
        $alert .=           "window.location.replace('$investment_plans_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}

$database_connection->close();
require_once "footer.php";
?>