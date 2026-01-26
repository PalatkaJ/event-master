<h1>Event Creation</h1>
<form action="/events/new" method="POST" enctype="multipart/form-data">
    <div>
        <label for="name">Event Name (max 64 chars):</label>
        <input type="text" id="name" name="name" maxlength="64" required>
    </div>

    <div>
        <label for="description">Description (max 1024 chars):</label>
        <textarea id="description" name="description" maxlength="1024" rows="4" required></textarea>
    </div>

    <div>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
    </div>

    <div>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
    </div>

    <div>
        <label for="hero_image">Hero Image:</label>
        <input type="file" id="hero_image" name="hero_image" accept="image/*" required>
    </div>

    <fieldset id="workshops-container">
        <legend>Workshops</legend>
        <div class="workshop-entry">
            <input type="text" name="workshops[]" placeholder="Workshop name" required>
        </div>
        <button type="button" onclick="addWorkshop()">+ Add another workshop</button>
    </fieldset>

    <button type="submit">Create Event</button>
</form>

<script>
    function addWorkshop() {
        const container = document.getElementById('workshops-container');
        const div = document.createElement('div');
        div.className = 'workshop-entry';
        div.innerHTML = '<input type="text" name="workshops[]" placeholder="Workshop name" required>';
        container.insertBefore(div, container.lastElementChild);
    }
</script>
