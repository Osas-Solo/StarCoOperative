const investmentAmountInput = document.getElementById("investment-amount");
const investmentAmountMessage = document.getElementById("investment-amount-message");

updateInvestmentRange();

function updateInvestmentRange() {
    const investmentPlanID = document.getElementById("investment-plan").value;

    const investmentRangeRequest = new XMLHttpRequest();

    investmentRangeRequest.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            const investmentRange = JSON.parse(this.responseText);

            investmentAmountInput.setAttribute("min", investmentRange.minimumInvestmentAmount);
            investmentAmountInput.setAttribute("max", investmentRange.maximumInvestmentAmount);

            investmentAmountMessage.innerHTML = "Please enter an amount in the range of &#8358;<b>" +
                investmentRange.minimumInvestmentAmount + "</b> - &#8358;<b>" + investmentRange.maximumInvestmentAmount
                + "</b>";
        }
    };

    investmentRangeRequest.open("POST", "investment-range-updater.php", true);
    investmentRangeRequest.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    investmentRangeRequest.send("investment-plan-id=" + investmentPlanID);
}
