<!DOCTYPE html>
<?php get_header(); ?>

<?php
echo ("<form id='customization-form' method='post' name='customization-form' action=''>");
	init_customization();
	echo ("<input type='submit' />");
echo ("</form>");

echo ("<div id='review-section'></div>");
?>

<script type="text/javascript">
jQuery(function($){
  $('#customization-form input[type=submit]').on('click', function(e){
  	e.preventDefault();
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
	var object = {};
	var data = new Array();
	$('input[type="radio"]:checked').each(function(){
		var attrname = $(this).attr("name");
		var extra = $(this).attr("price");
		var val = $(this).val();
		var slug = $(this).attr("slug");

	    object = {
	    	title: attrname,
	    	attributes: { 
	        	selected: val,
	        	slug: slug,
	        	additionalPrice: extra,
	        	group: attrname,
	        }
	    };

	    data.push(object);
	});

	console.log(data);

    $.ajax({
      url: ajaxurl,
      method: 'post',
      data: {formValue: data, action: 'finalizeCustom'},
      success: function(data, status) { 
        console.log(data);
        document.getElementById("review-section").innerHTML = data;
      },
      error: function(xhr, desc, err) {
        console.log(xhr);
        console.log("Details: " + desc + "\nError:" + err);
      }
    }); // end ajax call
  });
});
</script>

<script type="text/javascript">
	var optionDivs = document.getElementsByClassName("option-div");
	var activeNum = 0;
	showNext(activeNum);
	$('#prev').hide();
	console.log(optionDivs);

	document.getElementById('next').onclick = function() {
		activeNum += 1;
		if (activeNum >= optionDivs.length -1) {
			activeNum = optionDivs.length -1;
			$('#next').hide();
		}
		$('#prev').show();
		showNext(activeNum);
	};

	document.getElementById('prev').onclick = function() {
		activeNum -= 1;
		if (activeNum <= 0) {
			activeNum = 0;
			$('#prev').hide();
		}
		$('#next').show();
		showNext(activeNum);
	};

	function showNext (number) {
		$(optionDivs).hide();
		$(optionDivs[number]).show();
	};
</script>

<?php get_footer(); ?>

