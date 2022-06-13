<?php
$page_title = "Investments";

require_once "dashboard-header.php";

$investments = Investment::get_investments($database_connection);
?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Investments</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Transaction Reference</th>
                                <th>Member</th>
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
                                        <?php echo $current_investment->member->get_full_name()?>
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

            </div>
        </div>
    </section>

<?php
$database_connection->close();
require_once "footer.php";
?>