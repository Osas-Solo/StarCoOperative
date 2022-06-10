<?php
date_default_timezone_set("Africa/Lagos");
require_once("database-configuration.php");

class Member {
    public $user_id;
    public $username;
    public $first_name;
    public $last_name;
    public $gender;
    public $phone_number;
    public $email_address;
    public $password;
    public $bank_account_number;
    public Bank $bank;
    public InvestmentPlan $investment_plan;

    function __construct(mysqli $database_connection = null, string $username = "", string $password = "") {
        if (isset($database_connection)) {
            $username = cleanse_data($username, $database_connection);
            $password = cleanse_data($password, $database_connection);

            $query = "SELECT * FROM members m ";
            $query .= "INNER JOIN banks b ON m.bank_id = b.bank_id ";
            $query .= "WHERE username = '$username'";
            $query .= ($password != "") ? " AND password = SHA('$password')" : "";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->user_id = $row["user_id"];
                $this->username = $row["username"];
                $this->first_name = $row["first_name"];
                $this->last_name = $row["last_name"];
                $this->phone_number = $row["phone_number"];
                $this->email_address = $row["email_address"];
                $this->password = $row["password"];
                $this->bank_account_number = $row["bank_account_number"];
                $this->bank = new Bank($database_connection, $row["bank_code"]);
                $this->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);

                switch ($row["gender"]) {
                    case 'M':
                        $this->gender = "Male";
                        break;
                    case 'F':
                        $this->gender = "Female";
                        break;
                }
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    function is_found(): bool {
        return $this->username != null;
    }

    public function get_full_name() {
        return $this->first_name . " " . $this->last_name;
    }

    function is_male(): bool {
        return $this->gender == "Male";
    }

    function is_female(): bool {
        return $this->gender == "Female";
    }

    function has_investment(): bool {
        return $this->investment_plan->plan_id != null;
    }

    /**
     * @return Member[]
    */
    public static function get_members(mysqli $database_connection): iterable {
        $members = array();

        $query = "SELECT * FROM members m ";
        $query .= "INNER JOIN banks b ON m.bank_id = b.bank_id ";
        $query .= " ORDER BY username";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $member = new Member();

                $member->user_id = $row["user_id"];
                $member->username = $row["username"];
                $member->first_name = $row["first_name"];
                $member->last_name = $row["last_name"];
                $member->phone_number = $row["phone_number"];
                $member->email_address = $row["email_address"];
                $member->bank_account_number = $row["bank_account_number"];
                $member->bank = new Bank($database_connection, $row["bank_code"]);
                $member->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);


                switch ($row["gender"]) {
                    case 'M':
                        $member->gender = "Male";
                        break;
                    case 'F':
                        $member->gender = "Female";
                        break;
                }

                array_push($members, $member);
            }
        }   //  end of if number of rows > 0

        return $members;
    }   //  end of get_members()
}   //  end of Member class

class Admin {
    public $username;
    public $password;

    function __construct(mysqli $database_connection = null, string $username = "", string $password = "") {
        if (isset($database_connection)) {
            $username = cleanse_data($username, $database_connection);
            $password = cleanse_data($password, $database_connection);

            $query = "SELECT * FROM admins WHERE username = '$username'";
            $query .= ($password != "") ? " AND password = SHA('$password')" : "";


            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->username = $row["username"];
                $this->password = $row["password"];
            }   //  end of if number of rows > 0
        }
    }   //  end of constructor

    function is_found(): bool {
        return $this->username != null;
    }
}   //  end of Admin class

class Bank {
    public $bank_id;
    public $bank_code;
    public $bank_name;

    function __construct(mysqli $database_connection = null, string $bank_code = "") {
        if (isset($database_connection)) {
            $query = "SELECT * FROM banks WHERE bank_code = '$bank_code'";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->bank_id = $row["bank_id"];
                $this->bank_code = $row["bank_code"];
                $this->bank_name = $row["bank_name"];
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    /**
     * @return Bank[]
     */
    public static function get_banks(mysqli $database_connection) : iterable {
        $banks = array();

        $query = "SELECT * FROM banks ORDER BY bank_name";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $bank = new Bank();

                $bank->bank_id = $row["bank_id"];
                $bank->bank_code = $row["bank_code"];
                $bank->bank_name = $row["bank_name"];

                array_push($banks, $bank);
            }
        }   //  end of if number of rows > 0

        return $banks;
    }   //  end of get_banks()
}   //  end of Bank class

class InvestmentPlan {
    public $plan_id;
    public $plan_name;
    public $minimum_monthly_investment_amount;
    public $maximum_monthly_investment_amount;
    public $minimum_loan_entitled;
    public $maximum_loan_entitled;
    public $loan_interest_rate;

    function __construct(mysqli $database_connection = null, int $plan_id = null) {
        if (isset($database_connection)) {
            $plan_id = cleanse_data($plan_id, $database_connection);

            $query = "SELECT * FROM investment_plans WHERE plan_id = '$plan_id'";

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->plan_id = $row["plan_id"];
                $this->plan_name = $row["plan_name"];
                $this->minimum_monthly_investment_amount = $row["minimum_monthly_investment_amount"];
                $this->maximum_monthly_investment_amount = $row["maximum_monthly_investment_amount"];
                $this->minimum_loan_entitled = $row["minimum_loan_entitled"];
                $this->maximum_loan_entitled = $row["maximum_loan_entitled"];
                $this->loan_interest_rate = $row["loan_interest_rate"];
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public function get_minimum_monthly_investment_amount() {
        return "&#8358;" . number_format($this->minimum_monthly_investment_amount, 2);
    }

    public function get_maximum_monthly_investment_amount() {
        return "&#8358;" . number_format($this->maximum_monthly_investment_amount, 2);
    }

    public function get_minimum_loan_entitled() {
        return "&#8358;" . number_format($this->minimum_loan_entitled, 2);
    }


    public function get_maximum_loan_entitled() {
        return "&#8358;" . number_format($this->maximum_loan_entitled, 2);
    }

    /**
     * @return InvestmentPlan[]
     */
    public static function get_investment_plans(mysqli $database_connection) : iterable {
        $investment_plans = array();

        $query = "SELECT * FROM investment_plans ORDER BY plan_id";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $investment_plan = new InvestmentPlan();

                $investment_plan->plan_id = $row["plan_id"];
                $investment_plan->plan_name = $row["plan_name"];
                $investment_plan->minimum_monthly_investment_amount = $row["minimum_monthly_investment_amount"];
                $investment_plan->maximum_monthly_investment_amount = $row["maximum_monthly_investment_amount"];
                $investment_plan->minimum_loan_entitled = $row["minimum_loan_entitled"];
                $investment_plan->maximum_loan_entitled = $row["maximum_loan_entitled"];
                $investment_plan->loan_interest_rate = $row["loan_interest_rate"];

                array_push($investment_plans, $investment_plan);
            }
        }   //  end of if number of rows > 0

        return $investment_plans;
    }   //  end of get_investment_plans()
}   //  end of InvestmentPlan class

class Investment {
    public $investment_id;
    public $transaction_reference;
    public InvestmentPlan $investment_plan;
    public $investment_amount;
    public $payment_date;
    public $transaction_date;
    public Member $member;

    function __construct(mysqli $database_connection = null, string $transaction_reference = "", string $username = "") {
        if (isset($database_connection)) {
            $transaction_reference = cleanse_data($transaction_reference, $database_connection);
            $username = cleanse_data($username, $database_connection);

            $query = "SELECT * FROM investments i
                        INNER JOIN members m ON i.user_id = m.user_id
                        WHERE transaction_reference = $transaction_reference";

            if ($username != "") {
                $query .= " AND m.username = '$username'";
            }

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->investment_id = $row["investment_id"];
                $this->transaction_reference = $row["transaction_reference"];
                $this->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);
                $this->investment_amount = $row["investment_amount"];
                $this->payment_date = $row["payment_date"];
                $this->transaction_date = $row["transaction_date"];
                $this->member = new Member($database_connection, $row["username"]);
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public function is_found() {
        return $this->investment_id != null;
    }

    public function get_investment_amount() {
        return "&#8358;" . number_format($this->investment_amount, 2);
    }

    public function get_readable_payment_date() {
        return convert_date_to_month_period($this->payment_date);
    }

    public function get_readable_transaction_date() {
        return convert_date_to_readable_form($this->transaction_date);
    }

    /**
     * @return Investment[]
     */
    public static function get_investments(mysqli $database_connection, string $year = "", string $username = "") : iterable {
        $investments = array();

        $query = "SELECT * FROM investments i
                        INNER JOIN members m ON i.user_id = m.user_id";

        if ($year != "") {
            $query .= " WHERE payment_date LIKE '$year%'";

            if ($username != "") {
                $query .= " AND m.username = '$username'";
            }
        } else if ($username != "") {
            $query .= " WHERE m.username = '$username'";
        }

        $query .= " ORDER BY payment_date DESC";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $investment = new Investment();

                $investment->investment_id = $row["investment_id"];
                $investment->transaction_reference = $row["transaction_reference"];
                $investment->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);
                $investment->investment_amount = $row["investment_amount"];
                $investment->payment_date = $row["payment_date"];
                $investment->transaction_date = $row["transaction_date"];
                $investment->member = new Member($database_connection, $row["user_id"]);

                array_push($investments, $investment);
            }
        }   //  end of if number of rows > 0

        return $investments;
    }   //  end of get_investments()

    /**
     * @param Investment[] $investments
     * @return InvestmentPlan
     */
    public static function getMostMadeInvestmentPlan(mysqli $database_connection, array $investments): InvestmentPlan {
        $investment_plans_count = ["bronze" => 0, "silver" => 0, "gold" => 0];

        foreach ($investments as $current_investment) {
            switch ($current_investment->investment_plan->plan_id) {
                case 1:
                    $investment_plans_count["bronze"]++;
                    break;
                case 2:
                    $investment_plans_count["silver"]++;
                    break;
                case 3:
                    $investment_plans_count["gold"]++;
                    break;
            }
        }

        if (($investment_plans_count["gold"] >= $investment_plans_count["silver"]) &&
            ($investment_plans_count["gold"] >= $investment_plans_count["bronze"])) {
            return new InvestmentPlan($database_connection, 3);
        } else if (($investment_plans_count["silver"] >= $investment_plans_count["gold"]) &&
            ($investment_plans_count["silver"] >= $investment_plans_count["bronze"])) {
            return new InvestmentPlan($database_connection, 2);
        } else if (($investment_plans_count["bronze"] >= $investment_plans_count["gold"]) &&
            ($investment_plans_count["bronze"] >= $investment_plans_count["silver"])) {
            return new InvestmentPlan($database_connection, 1);
        }

        return new InvestmentPlan();
    }

    public static function get_total_investment_amount(array $investments) {
        return "&#8358;" . number_format(self::calculate_total_investment_amount($investments), 2);
    }

    public static function calculate_total_investment_amount(array $investments) {
        return array_reduce($investments, function ($total, $current_investment) {
            return $total + $current_investment->investment_amount;
        });
    }

    public static function get_recent_investment_month(array $investments): int {
        if (count($investments) == 0) {
            return 12;
        } else {
            $investment_date = $investments[0]->payment_date;

            $date_regex = "/(\d{4})-(\d{2})-(\d{2})/";

            preg_match($date_regex, $investment_date, $match_groups);

            $month = $match_groups[2];

            return intval($month);
        }
    }
}   //  end of Investment class

class Loan {
    public $loan_id;
    public Member $member;
    public InvestmentPlan $investment_plan;
    public $amount_requested;
    public $date_requested;
    public $date_approved;
    public $expiry_date;
    public $amount_paid;
    public $status;
    public $monthly_payment_amount;
    public $repayment_amount;

    function __construct(mysqli $database_connection = null, int $loan_id = 0, string $username = "") {
        if (isset($database_connection)) {
            $loan_id = cleanse_data($loan_id, $database_connection);
            
            $query = "SELECT * FROM loans l
                        INNER JOIN members m ON l.user_id = m.user_id
                        INNER JOIN investment_plans p ON l.plan_id = p.plan_id
                        WHERE loan_id = $loan_id";

            if ($username != "") {
                $query .= " AND m.username = '$username'";
            }

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->loan_id = $row["loan_id"];
                $this->member = new Member($database_connection, $row["username"]);
                $this->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);
                $this->amount_requested = $row["amount_requested"];
                $this->date_requested = $row["date_requested"];
                $this->date_approved = $row["date_approved"];
                $this->expiry_date = $row["expiry_date"];
                $this->amount_paid = $row["amount_paid"];
                $this->status = $row["status"];
                $this->monthly_payment_amount = $row["monthly_payment_amount"];
                $this->repayment_amount = $row["repayment_amount"];
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public function is_found() {
        return $this->loan_id != null;
    }

    public function get_amount_requested() {
        return "&#8358;" . number_format($this->amount_requested, 2);
    }

    public function get_amount_paid() {
        return "&#8358;" . number_format($this->amount_paid, 2);
    }

    public function get_monthly_payment_amount() {
        return "&#8358;" . number_format($this->monthly_payment_amount, 2);
    }

    public function get_repayment_amount() {
        return "&#8358;" . number_format($this->repayment_amount, 2);
    }

    public function calculate_payment_left() {
        return $this->repayment_amount - $this->amount_paid;
    }

    public function get_payment_left() {
        return "&#8358;" . number_format($this->calculate_payment_left(), 2);
    }

    public function get_readable_date_requested() {
        return convert_date_to_readable_form($this->date_requested);
    }

    public function get_readable_date_approved() {
        return convert_date_to_readable_form($this->date_approved);
    }

    public function get_readable_expiry_date() {
        return convert_date_to_readable_form($this->expiry_date);
    }

    public static function calculate_repayment(float $amount_requested, InvestmentPlan $investment_plan): float {
        $repayment = (($amount_requested * $investment_plan->loan_interest_rate) / 100) + $amount_requested;

        return $repayment;
    }

    public static function calculate_monthly_payment_amount(float $repayment_amount): float {
        return $repayment_amount / 12;
    }

    /**
     * @return Loan[]
     */
    public static function get_loans(mysqli $database_connection, string $username = "") : iterable {
        $loans = array();

        $query = "SELECT * FROM loans l
                        INNER JOIN members m ON l.user_id = m.user_id
                        INNER JOIN investment_plans p ON l.plan_id = p.plan_id";

        if ($username != "") {
            $query .= " WHERE m.username = '$username'";
        }

        $query .= " ORDER BY status DESC, date_requested DESC";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $loan = new Loan();

                $loan->loan_id = $row["loan_id"];
                $loan->member = new Member($database_connection, $row["username"]);
                $loan->investment_plan = new InvestmentPlan($database_connection, $row["plan_id"]);
                $loan->amount_requested = $row["amount_requested"];
                $loan->date_requested = $row["date_requested"];
                $loan->date_approved = $row["date_approved"];
                $loan->expiry_date = $row["expiry_date"];
                $loan->amount_paid = $row["amount_paid"];
                $loan->status = $row["status"];
                $loan->monthly_payment_amount = $row["monthly_payment_amount"];
                $loan->repayment_amount = $row["repayment_amount"];

                array_push($loans, $loan);
            }
        }   //  end of if number of rows > 0

        return $loans;
    }   //  end of get_loans()

    /**
     * @return Loan[]
     */
    public static function filter_loans_by_status(array $loans, string $status) : iterable {
        $filtered_loans = array();

        foreach ($loans as $current_loan) {
            if ($current_loan->status == $status) {
                array_push($filtered_loans, $current_loan);
            }
        }   //  end of foreach

        return $filtered_loans;
    }   //  end of filter_loans_by_status()
}   //  end of Loan class

class LoanPayment {
    public $loan_payment_id;
    public Loan $loan;
    public $amount_paid;
    public $date_paid;
    public $transaction_reference;
    public $transaction_date;

    function __construct(mysqli $database_connection = null, string $transaction_reference = "", string $username = "") {
        if (isset($database_connection)) {
            $transaction_reference = cleanse_data($transaction_reference, $database_connection);
            $username = cleanse_data($username, $database_connection);

            $query = "SELECT * FROM loan_payments p
                        INNER JOIN loans l ON p.loan_id = l.loan_id
                        INNER JOIN members m ON l.user_id = m.user_id
                        WHERE transaction_reference = '$transaction_reference'";

            if ($username != "") {
                $query .= " AND m.username = '$username'";
            }

            if ($database_connection->connect_error) {
                die("Connection failed: " . $database_connection->connect_error);
            }

            $query_result = $database_connection->query($query);

            if ($query_result->num_rows > 0) {
                $row = $query_result->fetch_assoc();

                $this->loan_payment_id = $row["loan_payment_id"];
                $this->loan = new Loan($database_connection, $row["loan_id"]);
                $this->amount_paid = $row["amount_paid"];
                $this->date_paid = $row["date_paid"];
                $this->transaction_reference = $row["transaction_reference"];
                $this->transaction_date = $row["transaction_date"];
            }   //  end of if number of rows > 0
        }   //  end of if $database_connection is set
    }   //  end of constructor

    public function is_found() {
        return $this->loan_payment_id != null;
    }

    public function get_amount_paid() {
        return "&#8358;" . number_format($this->amount_paid, 2);
    }

    public function get_readable_date_paid() {
        return convert_date_to_readable_form($this->date_paid);
    }

    public function get_readable_transaction_date() {
        return convert_date_to_readable_form($this->transaction_date);
    }

    /**
     * @return LoanPayment[]
     */
    public static function get_loan_payments(mysqli $database_connection, string $username = "") : iterable {
        $loan_payments = array();

        $query = "SELECT * FROM loan_payments p
                        INNER JOIN loans l ON p.loan_id = l.loan_id
                        INNER JOIN members m ON l.user_id = m.user_id";

        if ($username != "") {
            $query .= " WHERE m.username = '$username'";
        }

        $query .= " ORDER BY date_paid";

        if ($database_connection->connect_error) {
            die("Connection failed: " . $database_connection->connect_error);
        }

        $query_result = $database_connection->query($query);

        if ($query_result->num_rows > 0) {
            while ($row = $query_result->fetch_assoc()) {
                $loan_payment = new LoanPayment();

                $loan_payment->loan_payment_id = $row["loan_payment_id"];
                $loan_payment->loan = new Loan($database_connection, $row["loan_id"]);
                $loan_payment->amount_paid = $row["amount_paid"];
                $loan_payment->date_paid = $row["date_paid"];
                $loan_payment->transaction_reference = $row["transaction_reference"];
                $loan_payment->transaction_date = $row["transaction_date"];

                array_push($loan_payments, $loan_payment);
            }
        }   //  end of if number of rows > 0

        return $loan_payments;
    }   //  end of get_loan_payments()
}   //  end of LoanPayment class

function cleanse_data($data, $database_connection): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = mysqli_escape_string($database_connection, $data);

    return $data;
}

function is_name_valid(string $name) {
    return strlen($name) > 0;
}

function is_email_address_valid(string $email_address) {
    $email_regex = "/^[A-Za-z0-9+_.-]+@(.+\..+)$/";

    return preg_match($email_regex, $email_address);
}

function is_phone_number_valid(string $phone_number) {
    $phone_number_regex = "/0[7-9][0-1]\d{8}/";

    return preg_match($phone_number_regex, $phone_number);
}

function is_password_valid(string $password) {
    $lowercase_regex = "/[a-z]/";
    $uppercase_regex = "/[A-Z]/";
    $digit_regex = "/[0-9]/";

    return preg_match($lowercase_regex, $password) && preg_match($uppercase_regex, $password)
        && preg_match($digit_regex, $password) && strlen($password) >= 8;
}

function is_password_confirmed(string $password, string $password_confirmer) {
    return $password == $password_confirmer;
}

function is_textarea_filled(string $text_area_text) {
    $text_area_regex = "/[a-zA-Z0-9]+/";

    return preg_match($text_area_regex, $text_area_text);
}

function convert_date_to_readable_form(string $reverse_date) {
    $reverse_date_regex = "/(\d{4})-(\d{2})-(\d{2})/";

    preg_match($reverse_date_regex, $reverse_date, $match_groups);

    $year = $match_groups[1];
    $month = $match_groups[2];
    $day = $match_groups[3];

    $month = get_month($month - 1);

    return $month . " " . $day . ", " . $year;
}

function convert_date_to_month_period(string $reverse_date) {
    $reverse_date_regex = "/(\d{4})-(\d{2})-(\d{2})/";

    preg_match($reverse_date_regex, $reverse_date, $match_groups);

    $year = $match_groups[1];
    $month = $match_groups[2];

    $month = get_month($month - 1);

    return "$month $year";
}

/**
 * @param mysqli $database_connection
 * @return bool
 */
function is_username_in_use(mysqli $database_connection): bool {
    $is_username_in_use = false;

    $member = new Member($database_connection, $_POST["username"]);

    if ($member->is_found()) {
        $is_username_in_use = true;
    }

    return $is_username_in_use;   //  end of if username is null
}

function get_available_investment_months(int $last_investment_month): array {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
        "November", "December"];

    $available_investment_months = array();

    if ($last_investment_month >= 12) {
        return $months;
    } else {
        for ($i = $last_investment_month; $i < 12; $i++) {
            array_push($available_investment_months, $months[$i]);
        }
    }

    return $available_investment_months;
}

function get_month_number(string $month): int {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
        "November", "December"];

    return array_search($month, $months) + 1;
}

function get_month(int $month_number): string {
    $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October",
        "November", "December"];

    return $months[$month_number - 1];
}
?>