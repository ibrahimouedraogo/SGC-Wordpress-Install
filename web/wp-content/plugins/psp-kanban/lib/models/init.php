<?php
$libs = array(
     'lanes'
);

foreach( $libs as $lib ) {
     include_once( $lib . '.php' );
}

function psp_kb_get_progress_options() {

     return apply_filters( 'psp_kb_get_progress_options', array(
          'no'   => 'Don\'t update',
          '5'       => '5%',
          '10'      => '10%',
          '15'       => '15%',
          '20'      => '20%',
          '25'       => '25%',
          '30'      => '30%',
          '35'       => '35%',
          '40'      => '40%',
          '45'       => '45%',
          '50'      => '50%',
          '55'       => '55%',
          '60'      => '60%',
          '65'       => '65%',
          '70'      => '70%',
          '75'       => '75%',
          '80'      => '80%',
          '85'       => '85%',
          '90'      => '90%',
          '95'       => '95%',
          '100'      => '100%',
     ) );

}
