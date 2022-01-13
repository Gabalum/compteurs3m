</div><?php /* ! #mainContent */ ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@0.5.7/chartjs-plugin-annotation.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script>
<script src="/dashboard/assets/js/main.js"></script>
<script src="/dashboard/assets/js/charts.js"></script>
<?php if(isset($scripts) && is_array($scripts) && count($scripts) > 0): ?>
    <?php foreach($scripts as $script): ?>
        <?php echo $script ?>
    <?php endforeach ?>
<?php endif ?>
</body>
</html>
