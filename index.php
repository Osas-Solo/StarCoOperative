<?php
$page_title = "Home";

require_once "header.php";
?>

<!--
Start About Section
==================================== -->
<section class="service-2 section bg-gray">
  <div class="container">
    <div class="row">
      <div class="col">
        <div class="title text-center">
          <h2>How We Work</h2>
          <span class="border"></span>
          <p>You too can get the benefits of this co-operative by following these easy steps</p>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4 p-0">
        <div class="service-item text-center">
            <span class="count">1.</span>
            <h4>Sign Up</h4>
            <p>Become a member entering your personal details at the signup page.</p>
        </div>
      </div>
      <div class="col-md-4 p-0">
        <div class="service-item text-center">
          <span class="count">2.</span>
          <h4>Select Investment Plan</h4>
          <p>Check out the overview of our investment plans and select any which suits you.</p>
        </div>
      </div>
      <div class="col-md-4 p-0">
        <div class="service-item text-center">
          <span class="count">3.</span>
          <h4>Pay The Investment Amount For The Month</h4>
          <p>To confirm your investment, pay an amount in the range of the investment plan which you have selected.</p>
        </div>
      </div>
    </div>    <!-- End row -->
  </div>    <!-- End container -->
</section>   <!-- End section -->

<!--
Start Call To Action
==================================== -->
<section class="call-to-action section-sm">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center">
				<h2>Become a member to get the benefits of this co-operative now!</h2>
				<a href="signup.php" class="btn btn-main">Get Started</a>
			</div>
		</div> 		<!-- End row -->
	</div>   	<!-- End container -->
</section>   <!-- End section -->

<?php
require_once "footer.php";
?>