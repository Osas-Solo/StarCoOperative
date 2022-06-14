const numberOfInvestmentPlans = 3;

let minimumMonthlyInvestmentAmountInputs = [];
let maximumMonthlyInvestmentAmountInputs = [];
let minimumLoanEntitledInputs = [];
let maximumLoanEntitledInputs = [];
let loanInterestRateInputs = [];

setInputs();
makeInputsReadOnly();

function setInputs() {
    for (let i = 0; i < numberOfInvestmentPlans; i++) {
        minimumMonthlyInvestmentAmountInputs[i] = document.getElementById((i + 1) + "-minimum-monthly-investment-amount");
        maximumMonthlyInvestmentAmountInputs[i] = document.getElementById((i + 1) + "-maximum-monthly-investment-amount");
        minimumLoanEntitledInputs[i] = document.getElementById((i + 1) + "-minimum-loan-entitled");
        maximumLoanEntitledInputs[i] = document.getElementById((i + 1) + "-maximum-loan-entitled");
        loanInterestRateInputs[i] = document.getElementById((i + 1) + "-loan-interest-rate");
    }
}

function makeInputsReadOnly() {
    for (let i = 0; i < numberOfInvestmentPlans; i++) {
        if (i > 0) {
            minimumMonthlyInvestmentAmountInputs[i].setAttribute("readonly", "");
            maximumLoanEntitledInputs[i].setAttribute("readonly", "");
        }

        maximumMonthlyInvestmentAmountInputs[i].setAttribute("readonly", "");
        minimumLoanEntitledInputs[i].setAttribute("readonly", "");
    }
}

function setInputLimits() {
    let minimumMonthlyAmounts = [0, 0, 0];
    let maximumMonthlyAmounts = [0, 0, 0];
    let minimumLoansEntitled = [0, 0, 0];
    let maximumLoansEntitled = [0, 0, 0];
    let minimumLoanInterestRates = [0, 0, 0];
    let maximumLoanInterestRates = [0, 0, 0];

    /*minimumLoanInterestRates[2] = 10;
    maximumLoanInterestRates[2] = 20;
    minimumLoanInterestRates[1] = 8;
    maximumLoanInterestRates[1] = minimumLoanInterestRates[2] - 0.01;
    minimumLoanInterestRates[0] = 5;
    maximumLoanInterestRates[0] = minimumLoanInterestRates[1] - 0.01;*/


    for (let i = 0; i < minimumLoansEntitled.length; i++) {
        minimumLoansEntitled[0] = 200_000;
    }
    
    minimumMonthlyAmounts[0] = 10_000;
    maximumMonthlyAmounts[0] = Number(minimumMonthlyInvestmentAmountInputs[0].value) + 15_000;

    minimumMonthlyAmounts[1] = maximumMonthlyAmounts[0];
    maximumMonthlyAmounts[1] = minimumMonthlyAmounts[1] + 25_000;
    maximumLoansEntitled[1] = Number(maximumLoanEntitledInputs[0].value) + 300_000;

    minimumMonthlyAmounts[2] = maximumMonthlyAmounts[1];
    maximumMonthlyAmounts[2] = minimumMonthlyAmounts[1] + 25_000;
    maximumLoansEntitled[2] = Number(maximumLoanEntitledInputs[1].value) + 300_000;

    for (let i = 0; i < numberOfInvestmentPlans; i++) {
        if (i > 0) {
            minimumMonthlyInvestmentAmountInputs[i].value = maximumMonthlyAmounts[i - 1];
            maximumLoanEntitledInputs[i].value = maximumLoansEntitled[i - 1];
        }

        maximumMonthlyInvestmentAmountInputs[i].value = maximumMonthlyAmounts[i];

/*
        minimumMonthlyInvestmentAmountInputs[i].setAttribute("min", minimumMonthlyAmounts[i]);
        minimumMonthlyInvestmentAmountInputs[i].setAttribute("max", maximumMonthlyAmounts[i]);
        maximumMonthlyInvestmentAmountInputs[i].setAttribute("min", minimumMonthlyAmounts[i]);
        maximumMonthlyInvestmentAmountInputs[i].setAttribute("max", maximumMonthlyAmounts[i]);
        minimumLoanEntitledInputs[i].setAttribute("min", minimumMonthlyAmounts[i]);
        minimumLoanEntitledInputs[i].setAttribute("max", maximumMonthlyAmounts[i]);
        maximumLoanEntitledInputs[i].setAttribute("min", minimumMonthlyAmounts[i]);
        maximumLoanEntitledInputs[i].setAttribute("max", maximumMonthlyAmounts[i]);

        loanInterestRateInputs[i].setAttribute("min", minimumLoanInterestRates[i]);
        loanInterestRateInputs[i].setAttribute("max", maximumLoanInterestRates[i]);
*/
    }
}