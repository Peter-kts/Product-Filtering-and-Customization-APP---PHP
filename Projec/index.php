<?php get_header(); ?>

//Jquery AJAX calls, they get the data values inside the html constructed with the html constructor function
<script type="text/javascript">
jQuery(function($){
  $('#catFilter input[type=checkbox]').on('click', function(e){
    var value = $(this).attr('data-slug');
    var category = $(this).attr('data-cat');
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

    $.ajax({
      url: ajaxurl,
      method: 'post',
      data: {value: value, category: category, action: 'filter'},
      success: function(data, status) { 
        console.log(data);
        document.getElementById("filtered-suits").innerHTML = data;
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    }); // end ajax call
  });
});
</script>

<?php
$url = "https://blacklapel.com/api/product/category/suits";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = json_decode(curl_exec ($ch));
curl_close ($ch);

session_start();
$needleArray = array('color' => array(),
					 'occasion' => array(),
					 'season' => array(),
					 'pattern' => array());
$_SESSION['arraytrans'] = $needleArray;

//This uses the session PHP vairable, replaceable by a MYSQL database or so, in order to store the values from the filter selectors.
//They are each divided by category in order to allow cross-category filtration

$colorArray = array();
$occasionArray = array();
$patternArray = array();
$seasonArray = array();

function add_url_params() {
	global $needleArray;

	if (isset($_GET['color'])) {
		array_push($_SESSION['arraytrans']['color'], $_GET['color']);
	}
	if (isset($_GET['occasion'])) {
		array_push($_SESSION['arraytrans']['occasion'], $_GET['occasion']);
	}
	if (isset($_GET['season'])) {
		array_push($_SESSION['arraytrans']['season'], $_GET['season']);
	}
	if (isset($_GET['pattern'])) {
		array_push($_SESSION['arraytrans']['pattern'], $_GET['pattern']);
	}
	arraycompare($_SESSION['arraytrans']);
}

add_url_params();

function in_array_all($needles, $haystack) {
   return !array_diff($needles, $haystack);
}

echo "<div id='catFilter'>";
	for ($i = 0; $i < count($result[2]->data); $i++) {
		$filterObj = $result[2]->data[$i]->row;

			if ($filterObj[1] === "Color") {
				array_push($colorArray, $filterObj[0]);
			}

			if ($filterObj[1] === "Occasion") {
				array_push($occasionArray, $filterObj[0]);
			}

			if ($filterObj[1] === "Season") {
				array_push($seasonArray, $filterObj[0]);
			}

			if ($filterObj[1] === "Patterns") {
				array_push($patternArray, $filterObj[0]);
			}
	}

	echo "<ul>";
		foreach ($colorArray as $key) {
			echo "<li><input id='checkbox' type='checkbox' data-cat='color' data-slug='" . $key->slug . "'> <label>" . $key->name . "</label></li>";
		}
	echo "</ul>";

	echo "<ul>";
		foreach ($occasionArray as $key) {
			echo "<li><input id='checkbox' type='checkbox' data-cat='occasion' data-slug='" . $key->slug . "'> <label>" . $key->name . "</label></li>";
		}
	echo "</ul>";

	echo "<ul>";
		foreach ($patternArray as $key) {
			echo "<li><input id='checkbox' type='checkbox' data-cat='pattern' data-slug='" . $key->slug . "'> <label>" . $key->name . "</label></li>";
		}
	echo "</ul>";

	echo "<ul>";
		foreach ($seasonArray as $key) {
			echo "<li><input id='checkbox' type='checkbox' data-cat='season' data-slug='" . $key->slug . "'> <label>" . $key->name . "</label></li>";
		}
	echo "</ul>";

echo "</div>";

echo "<ul id='filtered-suits'>";
echo "</ul>";
?>

<?php get_footer(); ?>

