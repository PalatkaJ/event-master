<h1>Register for: {= $event['name'] }</h1>

<form class="entry-form" action="{= BASE_URL }/events/{= $event['id'] }/register" method="POST" enctype="multipart/form-data">
    <fieldset>
        <legend>Available Workshops</legend>
        <div id="workshops-list">
            {foreach $event['workshops'] as $workshop}
            <span class="workshop-entry">
                <label>
                    <input type="checkbox" name="workshops[]" value="{= $workshop['id'] }"
                        {if in_array($workshop['id'], $registeredIds)} checked {/if}>{= $workshop['name'] }
                </label>
            </span>
            {/foreach}
        </div>
    </fieldset>

    <button type="submit">
        Register for Event
    </button>
</form>