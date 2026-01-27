<h1>All Events</h1>

<div class="event-list">

</div>

<span class="page-buttons">
    <button type="button" id="previousButton">Previous</button>
    <button type="button" id="nextButton">Next</button>
</span>

<script>
    const allEvents = <?php echo $this->templateData['events_json'] ?>;
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script type="module" src="{= BASE_URL }/js/events/EventModel.js"></script>
<script type="module" src="{= BASE_URL }/js/events/EventView.js"></script>
<script type="module" src="{= BASE_URL }/js/events/EventPresenter.js"></script>
<script type="module" src="{= BASE_URL }/js/pagination.js"></script>