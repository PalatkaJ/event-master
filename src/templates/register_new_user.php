<h1>Register</h1>
<form class="entry-form" action="{= BASE_URL }/register" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required>
    </div>

    <div class="form-group">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" maxlength="255" required>
    </div>

    <button type="submit">Create Account</button>
</form>