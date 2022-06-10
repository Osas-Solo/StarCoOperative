<?php
$page_title = "View Investment";

require_once "dashboard-header.php";

$transaction_reference = "";
$investment = new Investment();

if (isset($_GET["transaction-reference"])) {
    $transaction_reference = $_GET["transaction-reference"];

    $investment = new Investment($database_connection, $transaction_reference, $member->username);
}
?>

    <section class="mt-5 mb-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Investment <?php echo $transaction_reference?> Details</h2>
                    </div>

                    <div class="col-12">
                        <?php
                        if ($investment->is_found()) {
                            ?>
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <tbody>
                                <tr>
                                    <th class="p-2">Transaction Reference</th>
                                    <td class="p-2"><?php echo $investment->transaction_reference?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Investment Plan</th>
                                    <td class="p-2"><?php echo $investment->investment_plan->plan_name?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Investment Amount</th>
                                    <td class="p-2"><?php echo $investment->get_investment_amount()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Payment Period</th>
                                    <td class="p-2"><?php echo $investment->get_readable_payment_date()?></td>
                                </tr>
                                <tr>
                                    <th class="p-2">Transaction Date</th>
                                    <td class="p-2"><?php echo $investment->get_readable_transaction_date()?></td>
                                </tr>
                                <tr>
                                    <th colspan="2"><a href="invest.php">Make another investment</a></th>
                                </tr>
                            </tbody>
                        </table>
                            <?php
                        } else {
                            ?>
                        <p class="text-center mt-5 mb-5 p-5">
                            Sorry, no investment with transaction reference <?php echo $transaction_reference?> could be found
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