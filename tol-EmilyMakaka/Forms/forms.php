<?php
class forms{
    public function signup(){
      ?>
<form action="mail.php" method="POST">
  <div class="mb-3">
    <label for="exampleInputName1" class="form-label">Name</label>
    <input type="text" class="form-control" id="exampleInputName1" name="name" required>
  </div>
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" required>
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
  </div>
  <?php $this->submit_button('Sign Up', 'signup'); ?>
  <a href='signin.php'>Already have an account? Login</a>
</form>
<?php
}

    

    public function signin(){
?>
<form action="login.php" method="POST">
  <div class="mb-3">
    <label for="exampleInputEmail1" class="form-label">Email address</label>
    <input type="email" class="form-control" id="exampleInputEmail1" name="email" required>
  </div>
  <div class="mb-3">
    <label for="exampleInputPassword1" class="form-label">Password</label>
    <input type="password" class="form-control" id="exampleInputPassword1" name="password" required>
  </div>
  <?php $this->submit_button('Sign In', 'signin'); ?>
  <a href='signup.php'>Don't have an account? Sign Up</a>
</form>
<?php
    }

    private function submit_button($text, $name){
      echo '<button type="submit" class="btn btn-primary" name="'.$name.'">'.$text.'</button>';
    }
}