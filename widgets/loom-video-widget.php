<?php
/**
 * Loom Video Widget
 *
 * Elementor widget for embedding Loom videos.
 *
 * @since 1.0.0
 */
class Loom_Video_Widget extends \Elementor\Widget_Base {

	/**
	 * Get widget name.
	 *
	 * Retrieve Loom Video widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'loom-video';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve Loom Video widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title() {
		return esc_html__( 'Loom Video', 'loom-elementor-widgets' );
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve Loom Video widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon() {
		return 'eicon-video-camera';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the Loom Video widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories() {
		return array( 'loom-widgets' );
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords() {
		return array( 'loom', 'video', 'embed', 'screen recording' );
	}

	/**
	 * Register Loom Video widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls() {

		// Content Tab
		$this->start_controls_section(
			'content_section',
			array(
				'label' => esc_html__( 'Content', 'loom-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
			)
		);

		$this->add_control(
			'loom_url',
			array(
				'label'       => esc_html__( 'Loom Video URL', 'loom-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::URL,
				'input_type'  => 'url',
				'placeholder' => esc_html__( 'https://www.loom.com/share/...', 'loom-elementor-widgets' ),
				'description' => esc_html__( 'Enter the Loom video URL or share link', 'loom-elementor-widgets' ),
				'default'     => array(
					'url' => '',
				),
			)
		);

		$this->add_control(
			'video_id',
			array(
				'label'       => esc_html__( 'Loom Video ID', 'loom-elementor-widgets' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter video ID (optional)', 'loom-elementor-widgets' ),
				'description' => esc_html__( 'If URL is not provided, enter the video ID directly', 'loom-elementor-widgets' ),
			)
		);

		$this->end_controls_section();

		// Style Tab
		$this->start_controls_section(
			'style_section',
			array(
				'label' => esc_html__( 'Video Settings', 'loom-elementor-widgets' ),
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			)
		);

		$this->add_responsive_control(
			'width',
			array(
				'label'      => esc_html__( 'Width', 'loom-elementor-widgets' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( '%', 'px' ),
				'range'      => array(
					'%'  => array(
						'min' => 10,
						'max' => 100,
					),
					'px' => array(
						'min' => 100,
						'max' => 1920,
					),
				),
				'default'    => array(
					'unit' => '%',
					'size' => 100,
				),
				'selectors'  => array(
					'{{WRAPPER}} .loom-video-container' => 'width: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'aspect_ratio',
			array(
				'label'   => esc_html__( 'Aspect Ratio', 'loom-elementor-widgets' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => '16-9',
				'options' => array(
					'16-9' => '16:9',
					'4-3'  => '4:3',
					'1-1'  => '1:1',
					'21-9' => '21:9',
				),
			)
		);

		$this->add_responsive_control(
			'alignment',
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
	 * Render Loom Video widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();

		$video_id = '';

		// Get video ID from URL or direct input
		if ( ! empty( $settings['loom_url']['url'] ) ) {
			$video_id = $this->extract_video_id( $settings['loom_url']['url'] );
		} elseif ( ! empty( $settings['video_id'] ) ) {
			$video_id = $settings['video_id'];
		}

		// If no video ID, show a message
		if ( empty( $video_id ) ) {
			if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="elementor-alert elementor-alert-warning">' . esc_html__( 'Please enter a Loom video URL or ID', 'loom-elementor-widgets' ) . '</div>';
			}
			return;
		}

		$aspect_ratio_class = 'aspect-ratio-' . $settings['aspect_ratio'];
		?>
		<div class="loom-video-container <?php echo esc_attr( $aspect_ratio_class ); ?>">
			<div class="loom-video-wrapper">
				<iframe 
					src="https://www.loom.com/embed/<?php echo esc_attr( $video_id ); ?>" 
					frameborder="0" 
					webkitallowfullscreen 
					mozallowfullscreen 
					allowfullscreen
					class="loom-video-iframe">
				</iframe>
			</div>
		</div>
		<style>
			.loom-video-container {
				position: relative;
				display: inline-block;
			}
			.loom-video-wrapper {
				position: relative;
				width: 100%;
				padding-bottom: 56.25%; /* 16:9 aspect ratio */
			}
			.loom-video-container.aspect-ratio-4-3 .loom-video-wrapper {
				padding-bottom: 75%; /* 4:3 aspect ratio */
			}
			.loom-video-container.aspect-ratio-1-1 .loom-video-wrapper {
				padding-bottom: 100%; /* 1:1 aspect ratio */
			}
			.loom-video-container.aspect-ratio-21-9 .loom-video-wrapper {
				padding-bottom: 42.857%; /* 21:9 aspect ratio */
			}
			.loom-video-iframe {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
			}
		</style>
		<?php
	}

	/**
	 * Extract video ID from Loom URL
	 *
	 * @since 1.0.0
	 * @access private
	 * @param string $url Loom video URL.
	 * @return string Video ID.
	 */
	private function extract_video_id( $url ) {
		// Pattern to match Loom video URLs
		// Examples: 
		// https://www.loom.com/share/abc123
		// https://loom.com/share/abc123
		// https://www.loom.com/embed/abc123
		$pattern = '/loom\.com\/(share|embed)\/([a-zA-Z0-9]+)/';
		
		if ( preg_match( $pattern, $url, $matches ) ) {
			return $matches[2];
		}

		return '';
	}
}
