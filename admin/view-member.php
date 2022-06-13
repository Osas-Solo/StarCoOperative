<?php
$page_title = "View Member";

require_once "dashboard-header.php";

$username = "";
$member = new Member();

if (isset($_GET["username"])) {
    $username = $_GET["username"];

    $member = new Member($database_connection, $username);
    $investments = Investment::get_investments($database_connection, "", $username);
    $loans = Loan::get_loans($database_connection, $username);

    $pending_loans = Loan::filter_loans_by_status($loans, "Pending");
    $approved_loans = Loan::filter_loans_by_status($loans, "Approved");
    $rejected_loans = Loan::filter_loans_by_status($loans, "Rejected");
    $paid_loans = Loan::filter_loans_by_status($loans, "Paid");
}


?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto mb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Member Profile</h2>
                    </div>

                    <?php
                    if ($member->is_found()) {
                        ?>
                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <tbody>
                            <tr>
                                <th class="p-2">Name</th>
                                <td class="p-2"><?php echo $member->get_full_name()?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Gender</th>
                                <td class="p-2"><?php echo $member->gender?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Email Address</th>
                                <td class="p-2"><?php echo $member->email_address?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Phone Number</th>
                                <td class="p-2"><?php echo $member->phone_number?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Bank</th>
                                <td class="p-2"><?php echo $member->bank->bank_name?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Bank Account Number</th>
                                <td class="p-2"><?php echo $member->bank_account_number?></td>
                            </tr>
                            <tr>
                                <th class="p-2">Investment Plan</th>
                                <td class="p-2">
                                    <?php
                                    if ($member->has_investment()) {
                                        echo $member->investment_plan->plan_name;
                                    } else {
                                        echo "This member hasn't made any investment yet";
                                    }
                                    ?>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="col-md-9 mx-auto mb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Investments</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Transaction Reference</th>
                                <th>Investment Period</th>
                                <th>Investment Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($investments as $current_investment) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-investment.php?transaction-reference=<?php echo $current_investment->transaction_reference?>">
                                            <?php echo $current_investment->transaction_reference?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_investment->get_readable_payment_date()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_investment->get_investment_amount()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 mx-auto mb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Pending Loans</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Requested Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($pending_loans as $current_pending_loan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-loan.php?loan-id=<?php echo $current_pending_loan->loan_id?>">
                                            <?php echo $current_pending_loan->get_amount_requested()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_pending_loan->get_readable_date_requested()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-9 mx-auto mb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Approved Loans</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Expiry Date</th>
                                <th>Payment Left</th>
                                <th>Monthly Payment Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($approved_loans as $current_approved_loan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-loan.php?loan-id=<?php echo $current_approved_loan->loan_id?>">
                                            <?php echo $current_approved_loan->get_amount_requested()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_approved_loan->get_readable_expiry_date()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_approved_loan->get_payment_left()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_approved_loan->get_monthly_payment_amount()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-7 mx-auto nb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Rejected Loans</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Requested Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($rejected_loans as $current_rejected_loan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-loan.php?loan-id=<?php echo $current_rejected_loan->loan_id?>">
                                            <?php echo $current_rejected_loan->get_amount_requested()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_rejected_loan->get_readable_date_requested()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-md-6 mx-auto mb-5">
                    <div class="block text-center">
                        <h2 class="text-center">Paid Loans</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Repayment Amount</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($paid_loans as $current_paid_loan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-loan.php?loan-id=<?php echo $current_paid_loan->loan_id?>">
                                            <?php echo $current_paid_loan->get_amount_requested()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $current_paid_loan->get_repayment_amount()?>
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
                } else {
                    ?>
                    <p class="text-center mt-5 mb-5 p-5">
                        Sorry, no member with the username <?php echo $username?> could be found
                    </p>
                    <?php
                }
                ?>
            </div>
        </div>
    </section>

<?php
$database_connection->close();
require_once "footer.php";
?>