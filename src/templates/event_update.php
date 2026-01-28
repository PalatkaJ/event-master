<h1>Update Event: {= $event['name'] }</h1>

<form class="entry-form" action="{= BASE_URL }/events/{= $event['id'] }/edit" method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Event Name:</label>
        <input type="text" id="name" name="name" value="{= $event['name'] }" maxlength="64">
    </div>

    <div class="form-group">
    <label for="description">Description:</label>
    <textarea id="description" name="description" maxlength="1024" required>{= $event['description'] }</textarea>
    </div>

    <div class="form-group">
    <label for="start_date">Start Date:</label>
    <input type="date" id="start_date" name="start_date" min="{= date('Y-m-d')}" value="{= $event['start_date'] }">
    </div>

    <div class="form-group">
    <label for="end_date">End Date:</label>
    <input type="date" id="end_date" name="end_date" value="{= $event['end_date'] }">
    </div>

    <div class="form-group">
    <label for="hero_image">Change Image:</label>
    <input type="file" id="hero_image" name="hero_image" accept="image/*">
    </div>

    <fieldset>
        <legend>Workshops</legend>
        <div id="workshops-list">
            {foreach $event['workshops'] as $workshop}
            <span class="workshop-entry">
                <input type="text" name="workshops[]" aria-label="Workshop name" value="{= $workshop['name'] }" maxlength="64" readonly>
                <button type="button" class="del-workshop-btn deleteBtn">remove</button>
            </span>
            {/foreach}
        </div>
        <button type="button" id="add-workshop-w-del-btn" class="add-btn">+</button>
    </fieldset>
    <button type="submit">Save Changes</button>
</form>

<form action="{=BASE_URL}/events/{=$event['id']}/delete" method="POST">
    <button type="submit" class="deleteBtn">Delete Event</button>
</form>

<script type="module" src="{= BASE_URL }/js/workshops.js"></script>
<script type="module" src="{= BASE_URL }/js/eventValidation.js"></script>