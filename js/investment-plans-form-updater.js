let minimumMonthlyInvestmentAmountInputs = [];
let maximumMonthlyInvestmentAmountInputs = [];
let minimumLoanEntitledInputs = [];
let maximumLoanEntitledInputs = [];
let loanInterestRateInputs = [];

setInputs();

console.log(minimumMonthlyInvestmentAmountInputs);
console.log(maximumMonthlyInvestmentAmountInputs);
console.log(minimumLoanEntitledInputs);
console.log(maximumLoanEntitledInputs);
console.log(loanInterestRateInputs);

function setInputs() {
    const numberOfInvestmentPlans = 3;

    for (let i = 0; i < numberOfInvestmentPlans; i++) {
        minimumMonthlyInvestmentAmountInputs[i] = document.getElementById((i + 1) + "-minimum-monthly-investment-amount");
        maximumMonthlyInvestmentAmountInputs[i] = document.getElementById((i + 1) + "-maximum-monthly-investment-amount");
        minimumLoanEntitledInputs[i] = document.getElementById((i + 1) + "-minimum-loan-entitled");
        maximumLoanEntitledInputs[i] = document.getElementById((i + 1) + "-maximum-loan-entitled");
        loanInterestRateInputs[i] = document.getElementById((i + 1) + "-loan-interest-rate");
    }
}
