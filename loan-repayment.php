<?php
require_once "entities.php";

$page_title = "Loan Repayment";

require_once "header.php";

if (isset($_SESSION["username"])) {
    $username = $_SESSION["username"];
    $member = new Member($database_connection, $username);
} else {
    $login_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/login.php";
    header("Location: " . $login_url);
}

if (isset($_POST["repay-loan"])) {
    repay_loan($member, $database_connection);
}

if (!isset($_POST["loan-id"])) {
    $loans_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/loans.php";
    header("Location: " . $loans_url);
} else {
    $loan = new Loan($database_connection, $_POST["loan-id"]);
}
?>

    <section class="account">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center"><?php echo $page_title?></h2>
                        <form id="payment-form" class="text-left clearfix mt-50 was-validated mb-5">
                            <div class="form-group mb-4">
                                <input class="d-none" id="loan-id" name="loan-id" type="text" class="form-control"
                                       placeholder="Loan ID" value="<?php echo $loan->loan_id?>">
                                <input class="d-none" id="transaction-reference" name="transaction-reference" type="text" class="form-control"
                                       placeholder="Transaction Reference">
                                <input class="d-none" id="email-address" name="email-address" type="email" class="form-control"
                                       placeholder="Email Address" value="<?php echo $member->email_address?>">
                            </div>
                            <div class="form-group mb-4">
                                <?php
                                $minimum_payment_amount = min($loan->calculate_payment_left(), $loan->monthly_payment_amount);
                                $maximum_payment_amount = max($loan->calculate_payment_left(), $minimum_payment_amount);
                                ?>
                                <label class="form-label font-weight-bold" for="transaction-amount">Payment Amount (&#8358;)</label>
                                <input id="transaction-amount" name="transaction-amount" type="number" class="form-control"
                                       placeholder="Payment Amount" required step="0.01"
                                       min="<?php echo $minimum_payment_amount?>"
                                       max="<?php echo $maximum_payment_amount?>">
                                <div>
                                    Note that you can only make a repayment in the range of
                                    <?php echo "&#8358;" . number_format($minimum_payment_amount, 2)?> -
                                    <?php echo "&#8358;" . number_format($maximum_payment_amount, 2)?>
                                </div>
                            </div>

                            <button id="payment-button" type="submit" name="repay-loan" class="btn btn-main text-center"
                                    onclick="payWithPaystack()">
                                Repay Loan
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
function repay_loan(Member $member, mysqli $database_connection) {
    $transaction_reference = cleanse_data($_POST["transaction-reference"], $database_connection);;
    $payment_amount = cleanse_data($_POST["transaction-amount"], $database_connection);
    $loan_id = $_POST["loan-id"];
    $loan = new Loan($database_connection, $loan_id);
    $amount_paid = $loan->amount_paid + $payment_amount;
    $status = "Approved";
    $transaction_date = date("Y-m-d");

    if ($amount_paid >= $loan->repayment_amount) {
        $status = "Paid";
    }

    $insert_query = "INSERT INTO loan_payments (loan_id, amount_paid, transaction_reference, transaction_date) VALUES 
                        ($loan->loan_id, $amount_paid, '$transaction_reference', '$transaction_date'); ";
    $update_query = "UPDATE loans SET amount_paid = $amount_paid, status = '$status' WHERE loan_id = $loan->loan_id;";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->multi_query($insert_query . $update_query)) {
        $alert = "<script>
                    if (confirm('Loan repayment made successfully')) {";
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