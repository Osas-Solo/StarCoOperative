<?php
require_once "entities.php";

if (!isset($_POST["investment-plan-id"])) {
    $invest_url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/invest.php";
    header("Location: " . $invest_url);
}

$investment_plan = new InvestmentPlan($database_connection, $_POST["investment-plan-id"]);

$investment_range = array("minimumInvestmentAmount"=> $investment_plan->minimum_monthly_investment_amount,
    "maximumInvestmentAmount"=> $investment_plan->maximum_monthly_investment_amount);

echo json_encode($investment_range);
?>
