<h1>Event Creation</h1>
<form class="entry-form" action="{= BASE_URL }/events/new" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Event Name:</label>
        <input type="text" id="name" name="name" maxlength="64" required>
    </div>

    <div class="form-group">
        <label for="description">Description:</label>
        <textarea id="description" name="description" maxlength="1024" rows="4" required></textarea>
    </div>

    <div class="form-group">
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" min="{= date('Y-m-d')}" required>
    </div>

    <div class="form-group">
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
    </div>

    <div class="form-group">
        <label for="hero_image">Hero Image:</label>
        <input type="file" id="hero_image" name="hero_image" accept="image/*" required>
    </div>

    <fieldset>
        <legend>Workshops</legend>
        <div id="workshops-list">
            <div class="workshop-entry">
                <input type="text" name="workshops[]" placeholder="Workshop name" aria-label="Workshop name" maxlength="64" required>
            </div>
        </div>

        <button type="button" id="add-workshop-btn">+</button>
    </fieldset>

    <button type="submit">Create Event</button>
</form>

<script type="module" src="{= BASE_URL }/js/workshops.js"></script>
<script type="module" src="{= BASE_URL }/js/eventValidation.js"></script>
