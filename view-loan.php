<?php
$page_title = "View Loan";

require_once "dashboard-header.php";

$loan_id = 0;
$loan = new Loan();

if (isset($_GET["loan-id"])) {
    $loan_id = $_GET["loan-id"];

    $loan = new Loan($database_connection, $loan_id, $member->username);
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
                                    <td class="p-2"><?php echo $loan->status?></td>
                                </tr>
                                </tbody>
                            </table>

                            <?php
                            if ($loan->is_approved()) {
                                ?>
                                <div class="col-12">
                                    <form action="loan-repayment.php" method="post">
                                        <input type="number" name="loan-id" class="d-none" value="<?php echo $loan->loan_id?>">
                                        <button type="submit" class="btn btn-main mx-auto d-block">Repay Loan</button>
                                    </form>
                                </div>
                                <?php
                            } else if ($loan->is_paid()) {
                            ?>
                                <div class="col-12 text-center mb-5">
                                    <h3><a href="loan-application.php">Apply for a Loan</a></h3>
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

            </div>
        </div>
    </section>

<?php
$database_connection->close();
require_once "footer.php";
?>