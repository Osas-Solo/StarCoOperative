<?php
$page_title = "Dashboard";

require_once "dashboard-header.php";

$members = Member::get_members($database_connection);
$investments = Investment::get_investments($database_connection);
$loans = Loan::get_loans($database_connection);
?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Members</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Investment Plan</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($members as $current_member) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-member.php?username=<?php echo $current_member->username?>">
                                            <?php echo $current_member->get_full_name()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php
                                        if ($current_member->has_investment()) {
                                            echo $current_member->investment_plan->plan_name;
                                        } else {
                                            echo "No investments made yet";
                                        }
                                        ?>
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
                            $number_of_investments = min(5, count($investments));
                            for ($i = 0; $i < $number_of_investments; $i++) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-investment.php?transaction-reference=<?php echo $investments[$i]->transaction_reference?>">
                                            <?php echo $investments[$i]->transaction_reference?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $investments[$i]->get_readable_payment_date()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $investments[$i]->get_investment_amount()?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3"><a href="investments.php"><b>View all investments</b></a></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div class="col-md-9 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Loans</h2>
                    </div>

                    <div class="col-12">
                        <table class="table table-striped table-hover table-sm text-center mb-5">
                            <thead>
                            <tr>
                                <th>Loan Amount</th>
                                <th>Requested Date</th>
                                <th>Status</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $number_of_loans = min(5, count($loans));
                            for ($i = 0; $i < $number_of_loans; $i++) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <a href="view-loan.php?loan-id=<?php echo $loans[$i]->loan_id?>">
                                            <?php echo $loans[$i]->get_amount_requested()?>
                                        </a>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $loans[$i]->get_readable_date_requested()?>
                                    </td>
                                    <td class="p-2">
                                        <?php echo $loans[$i]->status?>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="3"><a href="loans.php"><b>View all loans</b></a></td>
                            </tr>
                            </tfoot>
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