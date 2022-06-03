<?php
require_once "entities.php";

$page_title = "Investment Plans";

require_once "header.php";

$investment_plans = InvestmentPlan::get_investment_plans($database_connection);
?>

<section class="pricing-table section" id="pricing">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="title text-center">
                    <h2><?php echo $page_title?>.</h2>
                    <span class="border"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <div class="row">
                            <?php
                            foreach ($investment_plans as $current_investment_plan) {
                            ?>
                            <div class="col-md-4 col-sm-6 col-xs-12">
                                <div class="pricing-item">
                                    <h3><?php echo $current_investment_plan->plan_name?></h3>
                                    <div class="pricing-body">
                                        <div class="price">
                                            <h4>Monthly Investment Range:</h4>
                                            <h2>
                                                <?php echo $current_investment_plan->get_minimum_monthly_investment_amount()?>
                                                -
                                                <?php echo $current_investment_plan->get_maximum_monthly_investment_amount()?>
                                            </h2>
                                        </div>
                                        <div class="progress" data-percent="45%">
                                            <div class="progress-bar"></div>
                                        </div>
                                        <p>
                                            You'd be entitled to loans in the following range at an interest rate of
                                            <?php echo $current_investment_plan->loan_interest_rate?>%:
                                        </p>
                                        <h2>
                                            <?php echo $current_investment_plan->get_minimum_loan_entitled()?>
                                            -
                                            <?php echo $current_investment_plan->get_maximum_loan_entitled()?>
                                        </h2>
                                        <a class="btn btn-main" href="invest.php?investment-plan=<?php echo $current_investment_plan->plan_id?>">
                                            Invest
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End container -->
</section> <!-- End section -->

<?php
$database_connection->close();
require_once "footer.php";
?>