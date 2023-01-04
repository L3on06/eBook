<?php 
    include('Components/Header.php'); 
    $_SESSION['page'] = 'Profile';

    include('classes/CRUD.php');
    $crud = new CRUD;

    $error= '';
    $errors = [];

    $user = $crud->read('users', ['column' => 'id', 'value' => $_SESSION['user_id']]);
    $user = (is_array($user) && count($user) > 0) ? $user[0] : null;
  
    

    if(isset($_POST['update_profile_btn'])) {
        //validation
         if(strlen($_POST['username']) < 3)
            $errors[] = 'username is empty or too short!';

        if(strlen($_POST['email']) < 4)
            $errors[] = 'email is not valid!';

        if(strlen($_POST['address']) < 4)
            $errors[] = 'address is not valid!';
            
        if(strlen($_POST['phone']) < 6)
            $errors[] = 'phone is not valid!';

       if(count($errors) === 0) {
            if(!empty($_FILES['image']['name'])) {
                $filename = time().$_FILES['image']['name'];

                if($crud->update('users', [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'address' => $_POST['address'],
                    'phone' => $_POST['phone'],
                    // 'avatar' => $filename
                ], ['column' => 'id', 'value' => $user['id']])) {
                    // upload
                     move_uploaded_file($_FILES['image']['tmp_name'], '../assets/img/avatars/'.$filename);
                    header('Location: profile.php?status=success');
                } else {
                    header('Location: profile.php?status=error');
                }
            } else { 
                if($crud->update('users', [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'address' => $_POST['address'],
                    'phone' => $_POST['phone']
                ], ['column' => 'id', 'value' => $user['id']])) {
                    header('Location: profile.php?status=success');
                } else {
                    header('Location: profile.php?status=error');
                }
            }
        }
    }
    

    if(isset($_POST['update_password_btn'])) {
        if(strlen($_POST['password1']) < 6)
            $errors[] = 'Password is empty or too short!';
            
        if(strlen($_POST['password2']) < 6)
            $errors[] = 'Repeat password is empty or too short!';

        if($_POST['password1'] !== $_POST['password2'])
            $errors[] = 'Password has to be same as Repeat password!';

        
        if(count($errors) === 0) {
            $password = password_hash($_POST['password1'], PASSWORD_BCRYPT);

            if($crud->update('users', [
                'password' => $password
            ], ['column' => 'id', 'value' => $user['id']])) {
                header('Location: profile.php?status=success');
            } else {
                header('Location: profile.php?status=error');
            }
        }
    }
?>

<div class="profile my-5">
    <div class="container">
        <h3 class="mb-4">Profile l3on06</h3>
        <?php if(isset($error)) echo '<p>'.$error.'</p>'; ?>
        <?php 
            if(count($errors)) {
                echo '<ul>';
                foreach($errors as $error) {
                    echo '<li>'.$error.'</li>';
                }
                echo '</ul>';
            }
        ?>
        <?php 
        if(isset($_GET['status'])) {
            switch($_GET['status']) {
                case 'success':
                    echo 'Action was performed successfully';
                    break;
                case 'error':
                    echo 'Something want wrong!';
                    break;
            }
        }
        ?>
        <div class="card mb-4">
            <div class="card-header">
                <h5>Update</h5>
            </div>
            <div class="card-body">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" class="form-control" id="username"  required <?php if(!is_null($user)):?> value='<?= $user['username'] ?>' <?php endif; ?>/>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" required <?php if(!is_null($user)):?> value='<?= $user['email'] ?>' <?php endif; ?>/>
                <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
            </div>
              <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <input type="text" name="address" class="form-control" id="address"  required <?php if(!is_null($user) && !empty($user['address'])):?> value='<?= $user['address'] ?>' <?php endif; ?>/>
            </div>
              <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="phone" name="phone" class="form-control" id="phone"  required <?php if(!is_null($user) && !empty($user['phone']) ):?> value='<?= $user['phone'] ?>' <?php endif; ?>/>
            </div>
            <button type="submit" class="btn btn-primary" name="update_profile_btn">Update</button>
        </form>
            </div>
        </div>
         <div class="card mb-4">
            <div class="card-header">
                <h5>Change Password</h5>
            </div>
            <div class="card-body">
        <form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
            <div class="mb-3">
                <label for="password1" class="form-label">Password</label>
                <input type="password" name="password1" class="form-control" id="password1"  require/>
            </div>
            <div class="mb-3">
                <label for="password2" class="form-label">Repeat Password</label>
                <input type="password" name="password2" class="form-control" id="password2"  require/>
            </div>
            <button type="submit" class="btn btn-primary" name="update_password_btn">Update</button>
        </form>
            </div>
        </div>
    </div>
</div>

<?php include('Components/Footer.php'); ?>