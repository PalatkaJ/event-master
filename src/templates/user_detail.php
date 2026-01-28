<h1> User Detail </h1>

<div>
<form class="entry-form" action="{= BASE_URL }/settings" method="POST" enctype="multipart/form-data">
    <div class="form-group">
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" value="{= $user['full_name'] }" maxlength="64">
    </div>

    <div class="form-group">
    <label for="email">Email Address:</label>
    <input type="email" id="email" name="email" value="{= $user['email'] }" maxlength="64" readonly>
    </div>

    <button type="submit">Submit changes</button>
</form>

<form action="{= BASE_URL }/delete" method="POST" enctype="multipart/form-data">
    <button type="submit" class="deleteBtn">Delete Account</button>
</form>
</div>