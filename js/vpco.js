VimpPageComponent = {

	max_width: 1000,

	slider: null,

	overwriteResetButton: function(name, url) {
		$('input[name="cmd[resetFilter]"]').replaceWith('<a class="btn btn-default" href="' + url + '">' + name + '</a>');
	},

	initForm: function() {
		VimpPageComponent.slider = $("#vpco_slider").data("ionRangeSlider");
		VimpPageComponent.updateSlider();

		$('input#width').change(function() {
			let new_width = $(this).val();
			if (VimpPageComponent.keepAspectRatio()) {
				let current_width = $('#vpco_thumbnail').width();
				let current_height = $('#vpco_thumbnail').height();
				let ratio = (current_width / current_height);
				let new_height = new_width / ratio;
				$('#vpco_thumbnail').height(new_height);
				$('input#height').val(new_height);
			}
			$('#vpco_thumbnail').width(new_width);
			VimpPageComponent.updateSlider();
		});

		$('input#height').change(function() {
			let new_height = $(this).val();
			if (VimpPageComponent.keepAspectRatio()) {
				let current_width = $('#vpco_thumbnail').width();
				let current_height = $('#vpco_thumbnail').height();
				let ratio = (current_width / current_height);
				let new_width = new_height * ratio;
				$('#vpco_thumbnail').width(new_width);
				$('input#width').val(new_width);
				VimpPageComponent.updateSlider();
			}
			$('#vpco_thumbnail').height(new_height);
		});
	},

	updateSlider: function() {
		let width = $('input#width').val();
		let percentage = (width / VimpPageComponent.max_width) * 100;
		VimpPageComponent.slider.update({from: percentage});
	},

	sliderCallback: function(data) {
		let current_width = $('#vpco_thumbnail').width();
		let current_height = $('#vpco_thumbnail').height();
		let ratio = (current_width / current_height);
		let percentage = data.from;

		let new_width = VimpPageComponent.max_width * (percentage / 100);
		let new_height = (new_width / ratio);

		$('#vpco_thumbnail').width(new_width);
		$('input#width').val(new_width);
		$('#vpco_thumbnail').height(new_height);
		$('input#height').val(new_height);
	},

	keepAspectRatio: function() {
		return $('input#keep_aspect_ratio').is(":checked");
	},
}