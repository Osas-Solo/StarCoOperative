<?php
$page_title = "View Loan";

require_once "dashboard-header.php";

$loan_id = 0;
$loan = new Loan();

if (isset($_GET["loan-id"])) {
    $loan_id = $_GET["loan-id"];

    $loan = new Loan($database_connection, $loan_id);
}

if (isset($_POST["approve"])) {
    update_loan_status($database_connection, $loan, true);
} else if (isset($_POST["reject"])) {
    update_loan_status($database_connection, $loan, false);
}

if ($loan->is_approved() || $loan->is_paid()) {
    $loan_repayments = LoanPayment::get_loan_payments($database_connection, $loan->loan_id);
}
?>

    <section class="mt-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Loan Details</h2>
                    </div>

                    <div class="col-12">
                        <?php
                        if ($loan->is_found()) {
                            ?>
                            <table class="table table-striped table-hover table-sm text-center mb-5">
                                <tbody>
                                <tr>
                                    <th class="p-2">Amount Requested</th>
                                    <td class="p-2"><?php echo $loan->get_amount_requested()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Repayment Amount</th>
                                    <td class="p-2"><?php echo $loan->get_repayment_amount()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Request Date</th>
                                    <td class="p-2"><?php echo $loan->get_readable_date_requested()?></td>
                                </tr>
                                <?php
                                if ($loan->is_approved()) {
                                ?>
                                <tr>
                                    <th class="p-2">Approval Date</th>
                                    <td class="p-2"><?php echo $loan->get_readable_date_approved()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Expiry Date</th>
                                    <td class="p-2"><?php echo $loan->get_readable_expiry_date()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Amount Paid</th>
                                    <td class="p-2"><?php echo $loan->get_amount_paid()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Amount Left</th>
                                    <td class="p-2"><?php echo $loan->get_payment_left()?></td>
                                </tr>
                                <?php
                                }
                                ?>
                                <?php
                                if (!$loan->is_paid()) {
                                ?>
                                <tr>
                                    <th class="p-2">Monthly Payment Amount</th>
                                    <td class="p-2"><?php echo $loan->get_monthly_payment_amount()?></td>
                                </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <th class="p-2">Status</th>
                                    <td class="p-2 <?php if ($loan->is_rejected()) {
                                        echo 'text-danger';
                                    } else if ($loan->is_approved()) {
                                        echo 'text-success';
                                    } else if ($loan->is_pending()) {
                                        echo 'text-warning';
                                    } else if ($loan->is_paid()) {
                                        echo 'text-primary';
                                    }?>">
                                        <?php echo $loan->status?>
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <?php
                            if ($loan->is_pending()) {
                                ?>
                                <div class="col-12 text-center">
                                    <form method="post">
                                        <input type="number" name="loan-id" class="d-none" value="<?php echo $loan->loan_id?>">
                                        <button type="submit" name="reject" class="btn btn-danger mr-5">
                                            Reject
                                        </button>
                                        <button type="submit" name="approve" class="btn btn-success ml-5">
                                            Approve
                                        </button>
                                    </form>
                                </div>
                                <?php
                            }
                            ?>
                            <?php
                        } else {
                            ?>
                            <p class="text-center mt-5 mb-5 p-5">
                                Sorry, no loan with ID <?php echo $loan_id?> could be found
                            </p>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <?php
                if ($loan->is_approved() || $loan->is_paid()) {
                ?>
                <div class="col-md-9 mx-auto mt-5">
                    <div class="block text-center">
                        <h2 class="text-center">Loan Repayments</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Transaction Reference</th>
                                <th>Amount Paid</th>
                                <th>Transaction Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($loan_repayments as $current_loan_repayment) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <?php echo $current_loan_repayment->transaction_reference?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_loan_repayment->get_amount_paid()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_loan_repayment->get_readable_transaction_date()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php
                }
                ?>

            </div>
        </div>
    </section>

<?php
function update_loan_status(mysqli $database_connection, Loan $loan, bool $is_approved) {
    $status = $is_approved ? "Approved" : "Rejected";

    $update_query = "UPDATE loans SET status = '$status'";

    if ($is_approved) {
        $date_approved = date("Y-m-d");
        $expiry_date = set_expiry_date($date_approved);
        $amount_paid = 0;

        $update_query .= ", date_approved = '$date_approved', expiry_date = '$expiry_date', amount_paid = $amount_paid";
    }

    $update_query .= " WHERE loan_id = $loan->loan_id";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->query($update_query)) {
        $alert = "<script>
                    if (confirm('Loan $status successfully.')) {";
        $loan_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/view-loan.php?";
        $loan_url .= "loan-id=$loan->loan_id";
        $alert .=           "window.location.replace('$loan_url');
                    } else {";
        $alert .=           "window.location.replace('$loan_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}

function set_expiry_date(string $approval_date): string{
    $reverse_date_regex = "/(\d{4})-(\d{2})-(\d{2})/";

    preg_match($reverse_date_regex, $approval_date, $match_groups);

    $year = $match_groups[1];
    $month = $match_groups[2];

    $year++;

    if ($month == 12) {
        $month = "1";
        $year++;
    } else {
        $month++;
    }

    $day = "1";

    return "$year-$month-$day";
}

$database_connection->close();
require_once "footer.php";
?>