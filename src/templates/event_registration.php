<h1>Register for: {= $event['name'] }</h1>

<form action="{= BASE_URL }/events/{= $event['id'] }/register" method="POST">

    <h3>Available Workshops</h3>
    <p>Select the workshops you wish to attend:</p>

    {foreach $event['workshops'] as $workshop}
    <div>
        <label>
            <input type="checkbox"
                   name="workshops[]"
                   value="{= $workshop['id'] }"
                   {if in_array($workshop['id'], $registeredIds)} checked {/if}>
            {= $workshop['name'] }
        </label>
    </div>
    {/foreach}

    <br>
    <button type="submit">
        Confirm
    </button>
</form>

<p>
    <a href="{= BASE_URL }/events/{= $event['id'] }">Back to Event Detail</a>
</p>