<h1> User Detail </h1>
<div>
    <p>
        <strong>Full Name:</strong> {= $user['full_name']}
    </p>
    <p>
        <strong>Email Address:</strong> {= $user['email']}
    </p>
</div>

<form action="/settings" method="POST" enctype="multipart/form-data">
    <div>
        <label for="full_name">New name</label>
        <input type="text" id="full_name" name="full_name" maxlength="255" required>
    </div>
    <button type="submit">Submit changes</button>
</form>

TODO delete acc