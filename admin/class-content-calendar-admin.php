<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://nikhil.wisdmlabs.net
 * @since      1.0.0
 *
 * @package    Content_Calendar
 * @subpackage Content_Calendar/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Content_Calendar
 * @subpackage Content_Calendar/admin
 * @author     Nikhil Mhaske <nikhil.mhaske@wisdmlabs.com>
 */
class Content_Calendar_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Content_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Content_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/content-calendar-admin.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Content_Calendar_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Content_Calendar_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/content-calendar-admin.js', array('jquery'), $this->version, false);
	}

	//Add Custom Menu Page
	function cc_add_menu_pages()
	{
		add_menu_page(
			__('Content Calendar', 'content-calendar'),
			'Content Calendar',
			'manage_options',
			'content-calendar',
			array($this, 'content_calendar_callback'),
			'dashicons-calendar-alt',
			6
		);
		add_submenu_page(
			'content-calendar',
			__('Schedule Content', 'content-calendar'),
			__('Schedule Content', 'content-calendar'),
			'manage_options',
			'schedule-content',
			array($this, 'schedule_content_callback')
		);
		add_submenu_page(
			'content-calendar',
			__('View Schedule', 'content-calendar'),
			__('View Schedule', 'content-calendar'),
			'manage_options',
			'view-schedule',
			array($this, 'view_schedule_callback')
		);
	}


	// Handle the form submission
	public function cc_handle_form()
	{
		global $wpdb;

		if (isset($_POST['date']) && isset($_POST['occasion']) && isset($_POST['post_title']) && isset($_POST['author']) && isset($_POST['reviewer'])) {
			$table_name = $wpdb->prefix . 'cc_data';
			$date = sanitize_text_field($_POST['date']);
			$occasion = sanitize_text_field($_POST['occasion']);
			$post_title = sanitize_text_field($_POST['post_title']);
			$author = sanitize_text_field($_POST['author']);
			$reviewer = sanitize_text_field($_POST['reviewer']);
			$wpdb->insert(
				$table_name,
				array(
					'date' => $date,
					'occasion' => $occasion,
					'post_title' => $post_title,
					'author' => $author,
					'reviewer' => $reviewer
				)
			);
		}
	}


	function my_form_submission_handler()
	{
		if (isset($_POST['submit'])) {
			$this->cc_handle_form();
		}
	}

	//Callback from Menu

	public function schedule_content_callback()
	{
?>

		<h1 class="cc-title">Schedule Content</h1>
		<!--Add Input fields on Schedule Content Page-->
		<div class="wrap">


			<form method="post">
				<input type="hidden" name="action" value="cc_form">

				<label for="date">Date:</label>
				<input type="date" name="date" id="date" required /><br />

				<label for="occasion">Occasion:</label>
				<input type="text" name="occasion" id="occasion" required /><br />

				<label for="post_title">Post Title:</label>
				<input type="text" name="post_title" id="post_title" required /><br />

				<label for="author">Author:</label>
				<select name="author" id="author" required>
					<?php
					$users = get_users(array(
						'fields' => array('ID', 'display_name')
					));
					foreach ($users as $user) {
						echo '<option value="' . $user->ID . '">' . $user->display_name . '</option>';
					}
					?>
				</select><br>

				<label for="reviewer">Reviewer:</label>
				<select name="reviewer" id="reviewer" required>
					<?php
					$admins = get_users(array(
						'role' => 'administrator',
						'fields' => array('ID', 'display_name')
					));
					foreach ($admins as $admin) {
						echo '<option value="' . $admin->ID . '">' . $admin->display_name . '</option>';
					}
					?>
				</select><br>

				<?php submit_button('Schedule Post'); ?>

			</form>
		</div>

	<?php
	}



	public function view_schedule_callback()
	{
	?>
		<div class="wrap">
			<h1 class="cc-title">Upcoming Scheduled Content</h1>

			<?php

			global $wpdb;
			$table_name = $wpdb->prefix . 'cc_data';

			$data = $wpdb->get_results("SELECT * FROM $table_name WHERE date >= DATE(NOW()) ORDER BY date");

			echo '<table class="wp-list-table widefat fixed striped table-view-list">';
			echo '<thead><tr><th>ID</th><th>Date</th><th>Occasion</th><th>Post Title</th><th>Author</th><th>Reviewer</th></tr></thead>';
			foreach ($data as $row) {
				echo '<tr>';
				echo '<td>' . $row->id . '</td>';
				echo '<td>' . $row->date . '</td>';
				echo '<td>' . $row->occasion . '</td>';
				echo '<td>' . $row->post_title . '</td>';
				echo '<td>' . get_userdata($row->author)->user_login . '</td>';
				echo '<td>' . get_userdata($row->reviewer)->user_login . '</td>';
				echo '</tr>';
			}
			echo '</table>';


			?>
			<h1 class="cc-title">Deadline Closed Content</h1>

			<?php

			global $wpdb;
			$table_name = $wpdb->prefix . 'cc_data';

			$data = $wpdb->get_results("SELECT * FROM $table_name WHERE date < DATE(NOW()) ORDER BY date DESC");

			echo '<table class="wp-list-table widefat fixed striped table-view-list">';
			echo '<thead><tr><th>ID</th><th>Date</th><th>Occasion</th><th>Post Title</th><th>Author</th><th>Reviewer</th></tr></thead>';
			foreach ($data as $row) {
				echo '<tr>';
				echo '<td>' . $row->id . '</td>';
				echo '<td>' . $row->date . '</td>';
				echo '<td>' . $row->occasion . '</td>';
				echo '<td>' . $row->post_title . '</td>';
				echo '<td>' . get_userdata($row->author)->user_login . '</td>';
				echo '<td>' . get_userdata($row->reviewer)->user_login . '</td>';
				echo '</tr>';
			}
			echo '</table>';
			echo '</div>';
		}

		public function content_calendar_callback()
		{
			?>
			<h1><?php esc_html_e(get_admin_page_title()); ?></h1>
	<?php
			$this->schedule_content_callback();
			$this->view_schedule_callback();
		}
	}
