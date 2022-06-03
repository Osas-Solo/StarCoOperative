<?php
require_once "entities.php";

$investment_plan = new InvestmentPlan($database_connection, $_POST["investment-plan-id"]);

$investment_range = array("minimumInvestmentAmount"=> $investment_plan->minimum_monthly_investment_amount,
    "maximumInvestmentAmount"=> $investment_plan->maximum_monthly_investment_amount);

echo json_encode($investment_range);
?>
