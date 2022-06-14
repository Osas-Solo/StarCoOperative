<?php
$page_title = "Investment Plans";

require_once "dashboard-header.php";

$investment_plans = InvestmentPlan::get_investment_plans($database_connection);

if (isset($_POST["update"])) {
    update_cart($customer, $database_connection);
}
?>

    <section class="mt-2">
        <div class="container">
            <div class="row">
                <div class="col-12 mx-auto">
                    <div class="block text-center">
                        <h2 class="text-center">Investment Plans</h2>
                    </div>

                    <div class="col-12">
                        <form action="investment-plans.php" method="post" class="was-validated">
                            <table class="table table-striped table-hover table-sm table-responsive text-center mb-5">
                            <thead>
                            <tr>
                                <th>Investment Plan</th>
                                <th>Minimum Monthly Investment Amount (&#8358;)</th>
                                <th>Maximum Monthly Investment Amount (&#8358;)</th>
                                <th>Minimum Loan Entitled (&#8358;)</th>
                                <th>Maximum Loan Entitled (&#8358;)</th>
                                <th>Loan Interest Rate %</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($investment_plans as $current_investment_plan) {
                                ?>
                                <tr>
                                    <td class="p-2">
                                        <input class="form-control d-none" type="number" name="investment-plans-ids[]"
                                               value="<?php echo $current_investment_plan->plan_id?>">
                                        <input class="form-control" type="text" name="investment-plans[]" readonly
                                               value="<?php echo $current_investment_plan->plan_name?>">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="minimum-monthly-investment-amounts[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-minimum-monthly-investment-amount"
                                               value="<?php echo $current_investment_plan->minimum_monthly_investment_amount?>"
                                                step="0.01" min="10000" oninput="setInputLimits()">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="maximum-monthly-investment-amounts[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-maximum-monthly-investment-amount"
                                               value="<?php echo $current_investment_plan->maximum_monthly_investment_amount?>"
                                                step="0.01">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="minimum-loans-entitled[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-minimum-loan-entitled"
                                               value="<?php echo $current_investment_plan->minimum_loan_entitled?>"
                                                step="0.01" min="200000">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="maximum-loans-entitled[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-maximum-loan-entitled"
                                               value="<?php echo $current_investment_plan->maximum_loan_entitled?>"
                                                step="0.01" min="200000" max="2000000" oninput="setInputLimits()">
                                    </td>
                                    <td class="p-2">
                                        <input class="form-control" type="number" name="loan-interest-rates[]"
                                               id="<?php echo $current_investment_plan->plan_id?>-loan-interest-rate"
                                               value="<?php echo $current_investment_plan->loan_interest_rate?>"
                                                step="0.01" min="5" max="20" oninput="setInputLimits()">
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>

                            <div class="col-12 mb-5">
                                <button type="submit" class="btn btn-main mx-auto d-block">Update</button>
                            </div>
                        </form>

                        <script src="../js/investment-plans-form-updater.js"></script>
                    </div>
                </div>

            </div>
        </div>
    </section>

<?php
function update_investment_plans(mysqli $database_connection) {
    $order_insert_query = "";
    $update_products_query = "";
    $transaction_reference = $_POST["transaction-reference"];
    $order_date = date("Y-m-d");

    $cart_products = CartProduct::get_cart_products($database_connection, $customer->username);

    foreach ($cart_products as $current_cart_product) {
        $product_id = $current_cart_product->product->product_id;
        $quantity = $current_cart_product->quantity;
        $price = $current_cart_product->product->price;

        $order_insert_query .= "INSERT INTO orders (transaction_reference, product_id, amount_paid, quantity, order_date,
                                user_id, is_delivered) VALUE 
                                ('$transaction_reference', '$product_id', $price, $quantity, 
                                 '$order_date', $customer->user_id, 0);";

        $update_products_query .= "UPDATE products SET quantity_in_stock = quantity_in_stock - $quantity 
                                    WHERE product_id = '$product_id';";
    }

    $cart_removal_query = "DELETE FROM cart_products WHERE user_id = $customer->user_id";

    if ($database_connection->connect_error) {
        die("Connection failed: " . $database_connection->connect_error);
    }

    if ($database_connection->multi_query($order_insert_query)) {
        $database_connection->multi_query($update_products_query);
        $database_connection->query($cart_removal_query);

        $alert = "<script>
                    if (confirm('Order made successfully')) {";
        $order_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/order.php?transaction-reference=" . $transaction_reference;
        $alert .=           "window.location.replace('$order_url');
                    } else {";
        $alert .=           "window.location.replace('$order_url');
                    }";
        $alert .= "</script>";

        echo $alert;
    }
}


$database_connection->close();
require_once "footer.php";
?>