let transactionReference = document.getElementById("transaction-reference");
const paymentForm = document.getElementById("payment-form");
const paymentButton = document.getElementById("payment-button");

const emailAddress = document.getElementById("email-address").value;

paymentForm.addEventListener("submit", payWithPaystack, false);

function payWithPaystack(e) {
    e.preventDefault();

    const transactionAmount = document.getElementById("transaction-amount").value;

    let handler = PaystackPop.setup({
        key: "pk_test_7a73f8cd650ec49659c5d3a3368356620be11376",
        email: emailAddress,
        amount: transactionAmount * 100,

        onClose: function() {
            alert("Cancel transaction?");
        },

        callback: function(response) {
            paymentForm.removeEventListener("submit", payWithPaystack, false);
            transactionReference.value = response.reference;
            completeTransaction();
        }   //  end of callback
    });

    handler.openIframe();
}   //  end of payWithPaystack()

function completeTransaction() {
    paymentButton.removeAttribute("onclick");

    paymentForm.setAttribute("action", "");
    paymentForm.setAttribute("method", "POST");
    paymentButton.click();
}