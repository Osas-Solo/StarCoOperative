<?php
require_once "entities.php";

$page_title = "Complete Investment";

require_once "header.php";

if (!isset($_POST["investment-amount"])) {
    $invest_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/invest.php";
    header("Location: " . $invest_url);
}

$member = new Member($database_connection, $_SESSION["username"]);

if (isset($_POST["make-payment"])) {
    complete_investment($member, $database_connection);
}

$investment_plan = new InvestmentPlan($database_connection, $_POST["investment-plan"]);
$investment_amount = $_POST["investment-amount"];
$payment_date = date("Y") . "-" . get_month_number($_POST["payment-month"]) . "-01";
?>

    <section class="account">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center"><?php echo $page_title?></h2>
                        <form id="payment-form" class="text-left clearfix mt-50 was-validated">
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="investment-plan">Investment Plan</label>
                                <input id="investment-plan" name="investment-plan" type="text" class="form-control"
                                       placeholder="Investment Plan" required readonly
                                       value="<?php echo $investment_plan->plan_name?>">
                                <input class="d-none" id="investment-plan-id" name="investment-plan-id" type="text" class="form-control"
                                       placeholder="Investment Plan ID" value="<?php echo $investment_plan->plan_id?>">
                                <input class="d-none" id="transaction-reference" name="transaction-reference" type="text" class="form-control"
                                       placeholder="Transaction Reference">
                                <input class="d-none" id="email-address" name="email-address" type="email" class="form-control"
                                       placeholder="Email Address" value="<?php echo $member->email_address?>">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="transaction-amount">Investment Amount (&#8358;)</label>
                                <input id="transaction-amount" name="investment-amount" type="number" class="form-control"
                                       placeholder="Investment Amount" required readonly step="0.01"
                                       value="<?php echo $investment_amount?>">
                            </div>
                            <div class="form-group mb-4">
                                <label class="form-label font-weight-bold" for="payment-month">Payment Month</label>
                                <input id="payment-month" name="payment-month" type="text" class="form-control"
                                       placeholder="Payment Month" required readonly
                                       value="<?php echo $_POST["payment-month"]?>">
                                <input class="d-none" id="payment-date" name="payment-date" type="text" class="form-control"
                                       placeholder="Payment Date"
                                       value="<?php echo $payment_date?>">
                            </div>

                            <button id="payment-button" type="submit" name="make-payment" class="btn btn-main text-center"
                                    onclick="payWithPaystack()">
                                Make Payment
                            </button>
                        </form>

                        <script src = "https://js.paystack.co/v1/inline.js"></script>
                        <script src="js/paystack.js"></script>

                    </div>
                </div>
            </div>
        </div>
    </section>

<?php
function complete_investment(Member $member, mysqli $database_connection) {
    $transaction_reference = $_POST["transaction-reference"];
    $investment_plan_id = $_POST["investment-plan-id"];
    $investment_amount = $_POST["investment-amount"];
    $transaction_date = date("Y-m-d");
    $payment_date = $_POST["payment-date"];

    $insert_query = "INSERT INTO investments (transaction_reference, plan_id, investment_amount, payment_date, 
                         transaction_date, user_id) VALUE 
                         ('$transaction_reference', $investment_plan_id, $investment_amount, '$payment_date', 
                          '$transaction_date', $member->user_id)";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->query($insert_query)) {
        $alert = "<script>
                    if (confirm('Investment made successfully')) {";
        $investment_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/view-investment.php?transaction-reference=" . $transaction_reference;
        $alert .=           "window.location.replace('$investment_url');
                    } else {";
        $alert .=           "window.location.replace('$investment_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}

$database_connection->close();
require_once "footer.php";
?>