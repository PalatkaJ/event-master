<h1>All Events</h1>

<div class="event-list">

</div>

<div class="page-buttons">
    <button type="button" id="previousButton">Previous</button>
    <p id="pageNr"></p>
    <button type="button" id="nextButton">Next</button>
</div>

<script>
    const allEvents = <?php echo $this->templateData['events_json'] ?>;
    const BASE_URL = '<?php echo BASE_URL; ?>';
</script>
<script type="module" src="{= BASE_URL }/js/events/EventModel.js"></script>
<script type="module" src="{= BASE_URL }/js/events/EventView.js"></script>
<script type="module" src="{= BASE_URL }/js/events/EventPresenter.js"></script>
<script type="module" src="{= BASE_URL }/js/pagination.js"></script>