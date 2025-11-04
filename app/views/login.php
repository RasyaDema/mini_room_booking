<?php
// login form
?>
<section class="auth-section">
    <h2>Login</h2>
    <form method="post" action="?action=login" id="loginForm" novalidate>
        <input type="hidden" name="_csrf" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" name="email" type="email" required placeholder="you@example.com">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" name="password" type="password" required minlength="6" placeholder="password">
        </div>
        <button type="submit" class="btn btn--primary">Login</button>
    </form>
</section>
