<?php
/**
 * Loom Video Widget
 *
 * Elementor widget for embedding Loom videos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Loom_Video_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'loom-video';
	}

	/**
	 * Get widget title.
	 *
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Loom Video', 'loom-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-video-camera';
	}

	/**
	 * Get widget categories.
	 *
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'loom-widgets' );
	}

	/**
	 * Get widget keywords.
	 *
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'loom', 'video', 'embed', 'recording' );
	}

	/**
	 * Register widget controls.
	 */
	protected function register_controls() {
		// Content Section
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Video Settings', 'loom-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'loom_url',
			array(
				'label'       => esc_html__( 'Loom Video URL', 'loom-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://www.loom.com/share/...', 'loom-elementor-widgets' ),
				'default'     => array(
					'url' => '',
				),
				'description' => esc_html__( 'Enter the Loom video URL or share link', 'loom-elementor-widgets' ),
			)
		);

		$this->add_control(
			'aspect_ratio',
			array(
				'label'   => esc_html__( 'Aspect Ratio', 'loom-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => array(
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'1-1'  => '1:1',
					'9-16' => '9:16',
				),
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'loom-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'loom-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'loom-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_owner',
			array(
				'label'        => esc_html__( 'Hide Owner', 'loom-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'loom-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'loom-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_control(
			'hide_title',
			array(
				'label'        => esc_html__( 'Hide Title', 'loom-elementor-widgets' ),
				'type'         => \Elementor\Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'loom-elementor-widgets' ),
				'label_off'    => esc_html__( 'No', 'loom-elementor-widgets' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->end_controls_section();

		// Style Section
		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Style', 'loom-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'      => esc_html__( 'Width', 'loom-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px', 'vw' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 100,
						'max' => 1920,
					),
					'vw' => array(
						'min' => 10,
						'max' => 100,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .loom-video-wrapper' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'align',
			array(
				'label'     => esc_html__( 'Alignment', 'loom-elementor-widgets' ),
				'type'      => \Elementor\Controls_Manager::CHOOSE,
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'loom-elementor-widgets' ),
						'icon'  => 'eicon-text-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'loom-elementor-widgets' ),
						'icon'  => 'eicon-text-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'loom-elementor-widgets' ),
						'icon'  => 'eicon-text-align-right',
					),
				),
				'default'   => 'center',
				'selectors' => array(
					'{{WRAPPER}}' => 'text-align: {{VALUE}};',
				),
			)
		);

		$this->end_controls_section();
	}

	/**
	 * Render widget output on the frontend.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$loom_url = $settings['loom_url']['url'];

		if ( empty( $loom_url ) ) {
			echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Please enter a Loom video URL', 'loom-elementor-widgets' ) . '</div>';
			return;
		}

		// Extract video ID from Loom URL
		$video_id = $this->extract_loom_video_id( $loom_url );

		if ( ! $video_id ) {
			echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Invalid Loom video URL', 'loom-elementor-widgets' ) . '</div>';
			return;
		}

		// Build embed URL with parameters
		$embed_url = 'https://www.loom.com/embed/' . $video_id;
		$params    = array();

		if ( 'yes' === $settings['autoplay'] ) {
			$params[] = 'autoplay=1';
		}

		if ( 'yes' === $settings['hide_owner'] ) {
			$params[] = 'hide_owner=true';
		}

		if ( 'yes' === $settings['hide_title'] ) {
			$params[] = 'hide_title=true';
		}

		if ( ! empty( $params ) ) {
			$embed_url .= '?' . implode( '&', $params );
		}

		// Get aspect ratio class
		$aspect_ratio_class = 'aspect-ratio-' . $settings['aspect_ratio'];

		?>
		<div class="loom-video-wrapper">
			<div class="loom-video-container <?php echo esc_attr( $aspect_ratio_class ); ?>">
				<iframe
					src="<?php echo esc_url( $embed_url ); ?>"
					frameborder="0"
					webkitallowfullscreen
					mozallowfullscreen
					allowfullscreen
					style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
				</iframe>
			</div>
		</div>

		<style>
			.loom-video-container {
				position: relative;
				width: 100%;
				overflow: hidden;
			}
			.loom-video-container.aspect-ratio-16-9 {
				padding-bottom: 56.25%;
			}
			.loom-video-container.aspect-ratio-4-3 {
				padding-bottom: 75%;
			}
			.loom-video-container.aspect-ratio-1-1 {
				padding-bottom: 100%;
			}
			.loom-video-container.aspect-ratio-9-16 {
				padding-bottom: 177.78%;
			}
		</style>
		<?php
	}

	/**
	 * Extract video ID from Loom URL
	 *
	 * @param string $url Loom video URL.
	 * @return string|false Video ID or false if not found.
	 */
	private function extract_loom_video_id( $url ) {
		// Pattern to match Loom URLs
		// Examples:
		// https://www.loom.com/share/VIDEO_ID
		// https://loom.com/share/VIDEO_ID
		$pattern = '/loom\.com\/share\/([a-zA-Z0-9]+)/';

		if ( preg_match( $pattern, $url, $matches ) ) {
			return $matches[1];
		}

		return false;
	}
}
