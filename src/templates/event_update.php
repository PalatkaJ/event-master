<h1>Update Event: {= $event['name'] }</h1>

<form action="{= BASE_URL }/events/{= $event['id'] }/edit" method="POST" enctype="multipart/form-data">
    <label>Event Name:</label>
    <input type="text" name="name" value="{= $event['name'] }" maxlength="64">
    <br>

    <label>Description:</label>
    <textarea name="description" maxlength="1024" required>{= $event['description'] }</textarea>
    <br>

    <label>Start Date:</label>
    {= $event['start_date'] }
    <input type="date" name="start_date" value="{= $event['start_date'] }">
    <br>

    <label>End Date:</label>
    <input type="date" name="end_date" value="{= $event['end_date'] }">
    <br>

    <label>Change Image:</label>
    <input type="file" name="hero_image" accept="image/*">
    <br>

    <h3>Workshops</h3>
    <div id="workshops-container">
        {foreach $event['workshops'] as $workshop}
        <div>
            <input type="text" name="workshops[]" value="{= $workshop['name'] }" maxlength="64">
        </div>
        {/foreach}
        <button type="button" onclick="addWorkshop()">+ Add another workshop</button>
        TODO remove workshop
    </div>

    <button type="submit">Save Changes</button>
</form>

<form action="{=BASE_URL}/events/{=$event['id']}/delete" method="POST" enctype="multipart/form-data">
    <button type="submit">Delete Event</button>
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