<?php
$stats = psp_get_project_quick_stats();

if( psp_project_has_stats($stats) ): ?>
    <div id="psp-project-summary">

        <script>
    		var summaryChartOptions = {
    			responsive: true,
    			percentageInnerCutout : <?php echo esc_js( apply_filters( 'psp_graph_percent_inner_cutout', 75 ) ); ?>,
    			maintainAspectRatio: true,
               segmentShowStroke: false,
               showTooltips: false,
               animation: false,
    		}
            var allSummaryCharts = {};
    	</script>
     <div class="psp-project-summary__stats">
       <?php
       $i = 0;
       foreach( $stats as $key => $stat ):

            if( $stat['total'] == 0 ) {
                 continue;
            }?>
           <div id="<?php echo esc_attr( 'psp-stat-' . $key ); ?>" class="psp-summary-stat">
               <div class="summary-chart-wrap">
                   <canvas class="summary-chart" data-chard-id="psp-<?php echo esc_attr($key); ?>-chart" id="psp-<?php echo esc_attr($key); ?>-chart" width="100"></canvas>
               </div>
               <script>
                   jQuery(document).ready(function() {

                       var data = [
                           {
                               value: <?php echo esc_js( $stat['value'] ); ?>,
                               color: "<?php echo esc_js( $stat['color'] ); ?>",
                               label: "<?php echo esc_js( $stat['label'] ); ?>",
                           },
                           {
                               value: <?php echo esc_js( $stat['remaining'] ); ?>,
                               color: "#ccc",
                               label: "<?php esc_html_e( 'Remaining', 'psp_projects' ); ?>"
                           }
                       ];

                       var psp_<?php echo esc_js($key); ?>_chart = document.getElementById('psp-<?php echo esc_js($key); ?>-chart').getContext('2d');
                       allSummaryCharts['<?php echo esc_js($key); ?>'] = new Chart(psp_<?php echo esc_js($key); ?>_chart).Doughnut(data,summaryChartOptions);

                   });
               </script>
               <div class="psp-h3">
                    <span><?php echo esc_html( $stat['value'] ); ?></span>/<?php echo esc_html( $stat['total'] ); ?>
               </div>
               <div class="psp-h5">
                    <?php echo esc_html( $stat['label'] ); ?>
               </div>
           </div>
           <?php $i++; endforeach; ?>
       </div>
    </div>
<?php endif; ?>
