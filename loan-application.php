<?php
require_once "entities.php";

$page_title = "Loan Application";

require_once "header.php";

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $member = new Member($database_connection, $username);
} else {
    $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
    header("Location: " . $login_url);
}

$member = new Member($database_connection, $_SESSION["username"]);

if (isset($_POST["apply"])) {
    apply_for_loan($member, $database_connection);
}

if ($member->has_investment()) {
    $investments = Investment::get_investments($database_connection, "", $member->username);
    $investment_plan = Investment::getMostMadeInvestmentPlan($database_connection, $investments);
}
?>

    <section class="account">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center"><?php echo $page_title?></h2>
                        <?php
                        if ($member->has_investment()) {
                        ?>
                        <form class="text-left clearfix mt-50 was-validated" action="loan-application.php" method="post">
                            <div class="form-group mb-4">
                                <input class="d-none" id="investment-plan-id" name="investment-plan-id" type="text" class="form-control"
                                       placeholder="Investment Plan ID" value="<?php echo $investment_plan->plan_id?>">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="amount-requested">Amount Requested (&#8358;)</label>
                                <input id="amount-requested" name="amount-requested" type="number" class="form-control"
                                       placeholder="Amount Requested" required step="0.01"
                                       min="<?php echo $investment_plan->minimum_loan_entitled?>"
                                       max="<?php echo $investment_plan-> maximum_loan_entitled?>">
                                <div class="mt-3">
                                    Note that you can only make a request in the range of
                                    <?php echo $investment_plan->get_minimum_loan_entitled()?> -
                                    <?php echo $investment_plan->get_maximum_loan_entitled()?> based on the amount range you
                                    have invested the most which is the <?php echo $investment_plan->plan_name?>
                                </div>
                            </div>

                            <button type="submit" name="apply" class="btn btn-main text-center">
                                Apply
                            </button>
                        </form>
                        <?php
                        } else {
                        ?>
                        <p class="text-center mt-5 mb-5 p-5">
                            Sorry, you haven't made any investments yet. You can only apply for a loan when you become a
                            full fledged member and you can only become one be making investments.
                        </p>
                            <a href="invest.php" class="btn btn-main">Make Investment</a>
                        <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
function apply_for_loan(Member $member, mysqli $database_connection) {
    $investment_plan_id = cleanse_data($_POST["investment-plan-id"], $database_connection);
    $investment_plan = new InvestmentPlan($database_connection, $investment_plan_id);
    $amount_requested = cleanse_data($_POST["amount-requested"], $database_connection);
    $date_requested = date("Y-m-d");
    $status = "Pending";
    $repayment_amount = Loan::calculate_repayment($amount_requested, $investment_plan);
    $monthly_payment_amount = Loan::calculate_monthly_payment_amount($repayment_amount);

    $insert_query = "INSERT INTO loans (user_id, plan_id, amount_requested, date_requested, status, monthly_payment_amount, 
                        repayment_amount) VALUE 
                         ($member->user_id, $investment_plan_id, $amount_requested, '$date_requested', '$status', 
                          $monthly_payment_amount, $repayment_amount);";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->query($insert_query)) {
        $loan = Loan::get_loans($database_connection, $member->username)[0];

        $alert = "<script>
                    if (confirm('Loan application successful. Please check back for its approval.')) {";
        $loan_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/view-loan.php?loan-id=" . $loan->loan_id;
        $alert .=           "window.location.replace('$loan_url');
                    } else {";
        $alert .=           "window.location.replace('$loan_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}

$database_connection->close();
require_once "footer.php";
?>