<h1>Test account apis</h1>

<h3>Hash password</h3>
<?php
// stored password.
$password = 'pass';
// this module path.
$testmod_path = \Module::exists('testmod');
?>
<p>
    password: <?=$password; ?><br>
    hashed password: <?php
    $hashed_pass = $account_model->hashPassword('pass');
    echo $hashed_pass;
    ?> 
</p>

<h3>Check password</h3>
stored password: <?=$password; ?><br>
entered password: notpass || result: <?php var_dump($account_model->checkPassword('notpass', $hashed_pass)); ?><br>
entered password: <?=$password; ?> || result: <?php var_dump($account_model->checkPassword($password, $hashed_pass)); ?> 

<h3>Login success log</h3>
<p><?php
if (!is_writable($testmod_path)) {
    echo '<div class="alert alert-danger">Please change permission to allow read/write/delete for this folder:<br>'.$testmod_path.'</div>';
}
if (file_exists($testmod_path.'login-success.txt') && is_file($testmod_path.'login-success.txt')) {
    $login_success_log = \File::read($testmod_path.'login-success.txt', true);
    echo $login_success_log;
} else {
    echo 'no data.';
}
?></p>

<h3>Member edit account</h3>
<p><?php
if (!is_writable($testmod_path)) {
    echo '<div class="alert alert-danger">Please change permission to allow read/write/delete for this folder:<br>'.$testmod_path.'</div>';
}
if (file_exists($testmod_path.'member-edit-account.txt') && is_file($testmod_path.'member-edit-account.txt')) {
    $member_edit_account_log = \File::read($testmod_path.'member-edit-account.txt', true);
    echo $member_edit_account_log;
} else {
    echo 'no data.';
}
?></p>