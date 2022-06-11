<?php
$page_title = "Loans";

require_once "dashboard-header.php";

$loans = Loan::get_loans($database_connection);

$pending_loans = Loan::filter_loans_by_status($loans, "Pending");
$approved_loans = Loan::filter_loans_by_status($loans, "Approved");
$rejected_loans = Loan::filter_loans_by_status($loans, "Rejected");
$paid_loans = Loan::filter_loans_by_status($loans, "Paid");
?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
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

                <div class="col-md-9 mx-auto">
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

                <div class="col-md-7 mx-auto">
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

                <div class="col-md-6 mx-auto">
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

            </div>
        </div>
    </section>

<?php
$database_connection->close();
require_once "footer.php";
?>