<h1>Log In</h1>
<form class="entry-form" action="{= BASE_URL }/login" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" maxlength="255" required>
    </div>

    <button type="submit">Log In</button>
</form>
