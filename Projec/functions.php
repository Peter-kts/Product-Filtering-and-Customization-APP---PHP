<?php
if ( ! function_exists( 'blank_setup' ) ) :
	function blank_setup() {
		load_theme_textdomain( 'intentionally-blank' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );

		// This theme allows users to set a custom background.
		add_theme_support( 'custom-background', apply_filters( 'intentionally_blank_custom_background_args', array(
			'default-color' => 'f5f5f5',
		) ) );

		add_theme_support( 'custom-logo' );
		add_theme_support( 'custom-logo', array(
			'height'      => 256,
			'width'       => 256,
			'flex-height' => true,
			'flex-width'  => true,
			'header-text' => array( 'site-title', 'site-description' ),
		) );

		function blank_custom_logo() {
			if ( function_exists( 'the_custom_logo' ) ) {
				return get_custom_logo();
			}else{
				return '';
			}
		}
	}
endif; // blank_setup
add_action( 'after_setup_theme', 'blank_setup' );

add_theme_support( 'post-thumbnails' ); 

function register_my_session(){
  if( !session_id() ){
    session_start();
    $_SESSION['arraytrans'];
    $_SESSION['customization_options'];
    $_SESSION['selected_options'];
  }
}

add_action('init', 'register_my_session');

function blank_enqueue_style() {
    wp_enqueue_style( 'blank-style', get_stylesheet_uri() ); 
}
add_action( 'wp_enqueue_scripts', 'blank_enqueue_style' );

function create_needle_stack(){
	$x = $_POST['value'];
	$y = $_POST['category'];

	if (($key = array_search($x, $_SESSION['arraytrans'][$y])) !== false) {
    	unset($_SESSION['arraytrans'][$y][$key]);
	} else {
		array_push($_SESSION['arraytrans'][$y], $x);
		arraycompare($_SESSION['arraytrans']);
	}
	die();
}
add_action('wp_ajax_filter', 'create_needle_stack');
add_action('wp_ajax_nopriv_filter', 'create_needle_stack');

function arraycompare($arrayParam){
	$url = "https://blacklapel.com/api/product/category/suits";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = json_decode(curl_exec ($ch));
	curl_close ($ch);

	for ($i = 0; $i < count($result[0]->data); $i++) {
		$baseArray = $result[0]->data[$i]->row[4];
		$count = 0;
		foreach ($arrayParam as $subArray) {
			foreach ($subArray as $value) {
				if ( in_array($value, $baseArray) ) {
					$count ++;
					if ($count == count(array_filter($arrayParam))) {
						construct_html($result[0]->data[$i]->row[0]);
					}
				}
			}
		}
	}
}

function construct_html($json_object) {
	echo "<a href='product/?slug=" . $json_object->slug . "'><li class='suit-object'>";
	echo "<h1>" . $json_object->name . "</h1>";
	echo "<img src='" . "http://cdn.blacklapel.com/" . get_string_between($json_object->images, '"src":"assets/', '"}, {"') . "'>";
	echo "<p>" . $json_object->story . "</p>";
	echo "</li></a>";
}

function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function init_customization() {
	if (isset($_GET['slug'])) {
		$url = "https://blacklapel.com/api/product/option/" . $_GET['slug'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$result = json_decode(curl_exec ($ch), true);
		curl_close ($ch);

		$filter_groups = array("Vest", "Monogramming");
		$_SESSION['customization_options'] = array();

		foreach ($result as $property) {
			if (!in_array($property["group"], $filter_groups)) {
				array_push($_SESSION['customization_options'], $property);
			}
		}

		echo "YOU ARE CUSTOMIZING " . strtoupper(str_replace('-', ' ', $_GET['slug']));
	
		echo "<div class='selector' id='prev' data='-1'>Previous</div>";
		echo "<div class='selector' id='next' data='+1'>Next</div>";
		echo "<div id='options-selector'>";
		customization_options();
		echo "</div>";
	}
}

function customization_options() {
foreach ($_SESSION['customization_options'] as $customization_Object) {
	echo "<div class='option-div'>";
	echo $customization_Object["name"];
	foreach ($customization_Object["options"] as $option) {
		if ($option["price"] != 0) {
			echo "<p id='extra-price'> EXTRA $" . $option["price"] . "</p>";
			echo "<input type='radio' class='custom-option' name='" . $customization_Object["name"] . "' slug='" . $customization_Object["slug"] . "' value='" . $option["value"] . "' price='" . $option["price"] . "'>";
			echo $option["value"];
		} else {
		echo "<input type='radio' class='custom-option' name='" . $customization_Object["name"] . "' slug='" . $customization_Object["slug"] . "' value='" . $option["value"] . "' price='" . 0 . "'>";
		echo $option["value"];
		}
	}
	echo "</div>";
}
}

add_action('wp_ajax_options', 'finalize-custom');
add_action('wp_ajax_nopriv_options', 'finalize-custom');

function finalizeCustom() {
$formValue = $_POST['formValue'];
echo "REVIEW YOUR SELECTIONS";
var_dump($formValue);
die();
}

add_action('wp_ajax_finalizeCustom', 'finalizeCustom');
add_action('wp_ajax_nopriv_finalizeCustom', 'finalizeCustom');

require get_template_directory() . '/ajax.php';
