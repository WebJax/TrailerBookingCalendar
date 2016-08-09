<?php
/**
* Don't run this php without Wordpress
*
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/**
* Activating the plugin:
*   - Create the bookingtable
*   - Define versionvariable
*/
function trailerbookingcalendar_activating () 
{
	global $wpdb;
  
  $table_name = $wpdb->prefix . "trailerbookingcalendar";
  
	$sql = "
    CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      userid mediumint(9) NOT NULL,
      bookingstart datetime NOT NULL,
      bookingend datetime NOT NULL,
      infotext tinytext NOT NULL,
      UNIQUE KEY id (id)
    );
  ";
    
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

  global $trailerbookingcalendar_db_version;
  $trailerbookingcalendar_db_version = "1.0";

	add_option( 'trailerbookingcalendar_db_version', $trailerbookingcalendar_db_version);  
  
  add_action( 'wp_enqueue_scripts', 'trailerbookingcalendar_register' );
	
	add_action( 'init', 'trailerbooking_saveevent' );
}

function trailerbooking_saveevent() {
			$current_user = wp_get_current_user();		 
			$pass_validation = true; 
			if (!isset($_POST['info'])) {
				$itxt = "Der er ikke oplyst et telefonnummer";			
			} else {
				$itxt = $_POST['info'];
			}
			$data = array(
				'userid' => $current_user->ID,
				'bookingstart' => $_POST['starthidden'],
				'bookingend' => $_POST['sluthidden'],
				'infotext' => $itxt
			);
			global $wpdb;
			$table_name = $wpdb->prefix . 'trailerbookingcalendar';
			$wpdb->insert($table_name, $data, '%s'); 
		}

class TrailerBookingCalendar
{  
  /**
	 * Static property to hold our singleton instance
	 *
	 */
	static $instance = false;
	/**
	 * This is our constructor
	 *
	 * @return void
	 */
	private function __construct() {
    add_action ('plugins_loaded', array( $this, 'trailerbookingcalendar_textdomain'));
    /* back end
		add_action		( 'admin_enqueue_scripts',				array( $this, 'admin_scripts'			)			);
		add_action		( 'do_meta_boxes',						array( $this, 'create_metaboxes'		),	10,	2	);
		add_action		( 'save_post',							array( $this, 'save_custom_meta'		),	1		); */
		// front end
    add_action ( 'wp_enqueue_scripts', array( $this, 'trailerbookingcalendar_register' ) );
		add_shortcode	( 'trailerbooking',	array( $this, 'trailerbookingcalendar_show_calendar' ) );
	}
	/**
	 * If an instance exists, this returns it.  If not, it creates one and
	 * retuns it.
	 *
	 * @return WP_Comment_Notes
	 */
	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}
  
  /**
	 * load textdomain
	 *
	 * @return void
	 */
	public function trailerbookingcalendar_textdomain() {
		load_plugin_textdomain( 'trailerbookingcalendar', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

  /**
   * Register styles and scripts
   *
   */

  public function trailerbookingcalendar_register() {
    wp_enqueue_style ('trailerbookingcalendar_modal_styles', plugins_url( '../css/trailerbookingstyles.css', __FILE__ ));
    wp_enqueue_style ('trailerbookingcalendar_styles', plugins_url( '../css/fullcalendar.min.css', __FILE__ ));
    wp_enqueue_script ('trailerbookingcalendar_fullcalendar_moment', plugins_url( '../js/moment.min.js', __FILE__ ));
    wp_enqueue_script ('trailerbookingcalendar_fullcalendar', plugins_url( '../js/fullcalendar.min.js', __FILE__ ), array('jquery'));
    wp_enqueue_script ('trailerbookingcalendar_fullcalendar_danish', plugins_url( '../js/da.js', __FILE__ ), array('jquery'));
    wp_register_script ('trailerbookingcalendar_fullcalendar_script', plugins_url( '../js/trailerbookingcalendar_script.js', __FILE__ ), array('jquery')); 
		$tbc_pluginurl = plugins_url( 'includes/bookinger.php', dirname(__FILE__) );
		wp_localize_script ('trailerbookingcalendar_fullcalendar_script', 'bookingURL', array( 'pluginurl' => $tbc_pluginurl ));
		wp_enqueue_script ('trailerbookingcalendar_fullcalendar_script');
	}

  /**
   * Show the calendar with a shortcode
   *
   * @return $tbcsc (TrailerBookingCalendarShowCalendar)
   */
  
  public function trailerbookingcalendar_show_calendar() {    
    /**
     * First check if theres been a form request
		 * Then Write the html for the frontend
     *
     */
		
		/* Call the form request check */
		if ( isset($_POST['user_info_nonce']) ) {
			trailerbooking_saveevent();
		}
		
    $tbcsc = "<div id='calendar'></div>";
    $tbcsc .= '<div class="trailercalendar-modal-background">
			<div class="trailercalendar-modal">
				<form method="post" action="">'.wp_nonce_field('user_info', 'user_info_nonce', true, true).'
					<input id="starthidden" type="hidden" name="starthidden" value="Thu Jul 14 2017 00:00:00 GMT+0000"/>
					<input id="sluthidden" type="hidden" name="sluthidden" value="Thu Jul 14 2016 18:15:00 GMT+0000"/>
					<table class="tbctable">
						<tr class="tbctable">
							<th colspan="2" class="tbctable">
								<h3>Opret booking</h3>
							</th>
						</tr>
						<tr class="tbctable">
							<td class="infotxt tbctable">
								<label for="start">Starttidspunkt:</label>
							</td>
							<td class="tbctable">
								<select id="startdato" name="startdatoen">
									<option value="01">1</option>
									<option value="02">2</option>
									<option value="03">3</option>
									<option value="04">4</option>
									<option value="05">5</option>
									<option value="06">6</option>
									<option value="07">7</option>
									<option value="08">8</option>
									<option value="09">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
								</select>
								<select id="startmaaned" name="startmaaneden"> 
									<option value="1">Januar</option>
									<option value="2">Februar</option>
									<option value="3">Marts</option>
									<option value="4">April</option>
									<option value="5">Maj</option>
									<option value="6">Juni</option>
									<option value="7">Juli</option>
									<option value="8">August</option>
									<option value="9">September</option>
									<option value="10">Oktober</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>
								<select id="startaar" name="startaaret">
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
								</select>
								<select id="starttime" name="starttimer">
									<option value="00">00</option>
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
								</select>
								<select id="startminut" name="startminutter">
									<option value="00">00</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
							</td>
						</tr>
						<tr class="tbctable">
							<td class="infotxt tbctable">
								<label for="slut">Sluttidspunkt:</label>
							</td>
							<td class="tbctable">
								<select id="slutdato" name="slutdatoen">
									<option value="01">1</option>
									<option value="02">2</option>
									<option value="03">3</option>
									<option value="04">4</option>
									<option value="05">5</option>
									<option value="06">6</option>
									<option value="07">7</option>
									<option value="08">8</option>
									<option value="09">9</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
									<option value="24">24</option>
									<option value="25">25</option>
									<option value="26">26</option>
									<option value="27">27</option>
									<option value="28">28</option>
									<option value="29">29</option>
									<option value="30">30</option>
									<option value="31">31</option>
								</select>
								<select id="slutmaaned" name="slutmaaneden"> 
									<option value="1">Januar</option>
									<option value="2">Februar</option>
									<option value="3">Marts</option>
									<option value="4">April</option>
									<option value="5">Maj</option>
									<option value="6">Juni</option>
									<option value="7">Juli</option>
									<option value="8">August</option>
									<option value="9">September</option>
									<option value="10">Oktober</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>
								<select id="slutaar" name="slutaaret">
									<option value="2016">2016</option>
									<option value="2017">2017</option>
									<option value="2018">2018</option>
									<option value="2019">2019</option>
								</select>
								<select id="sluttime" name="sluttimer">
									<option value="00">00</option>
									<option value="01">01</option>
									<option value="02">02</option>
									<option value="03">03</option>
									<option value="04">04</option>
									<option value="05">05</option>
									<option value="06">06</option>
									<option value="07">07</option>
									<option value="08">08</option>
									<option value="09">09</option>
									<option value="10">10</option>
									<option value="11">11</option>
									<option value="12">12</option>
									<option value="13">13</option>
									<option value="14">14</option>
									<option value="15">15</option>
									<option value="16">16</option>
									<option value="17">17</option>
									<option value="18">18</option>
									<option value="19">19</option>
									<option value="20">20</option>
									<option value="21">21</option>
									<option value="22">22</option>
									<option value="23">23</option>
								</select>
								<select id="slutminut" name="slutminutter">
									<option value="00">00</option>
									<option value="15">15</option>
									<option value="30">30</option>
									<option value="45">45</option>
								</select>
							</td>
						</tr>
						<tr class="tbctable">
							<td class="infotxt tbctable info">
								<label for="tekst">Info:</label>
							</td>
							<td class="tbctable">
								<textarea id="tekst" name="tekst" cols="30" rows="3" placeholder="Her bør du skrive dit telefonnummer"></textarea>
							</td>
						</tr>
						<tr class="tbctable">
							<td colspan="2" class="tbctable knapperne">
								<input type="button" id="annuller" class="tbc" value="Annuller" />
								<input type="submit" id="submit" class="tbc submit" value="Opret" />
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>';
				
		/**
     * Don't forget to return something, when you're making a shortcode
     *
     */
		
    return $tbcsc;    
  }

		
	
// End of Class
}
/* EOF */
