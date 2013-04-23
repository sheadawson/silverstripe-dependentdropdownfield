jQuery.entwine("dependentdropdown", function($) {
	
	$(":input.dependent-dropdown").entwine({
		onmatch: function() {
			var drop = this;
			var depends = ($(":input[name=" + drop.data('depends').replace(/[#;&,.+*~':"!^$[\]()=>|\/]/g, "\\$&") + "]"));

			this.parents('.field:first').addClass('dropdown');

			depends.change(function () {
				if(!this.value) {
					drop.disable(drop.data('unselected'));
				} else {
					drop.disable("Loading...")

					$.get(drop.data('link'), { val: this.value }, function(data) {
						drop.enable();

						if(drop.data('empty') || drop.data('empty') === "") {
							drop.append($("<option />").val("").text(drop.data('empty')))
						}

						$.each(data, function(k, v) {
							drop.append($("<option />").val(k).text(v))
						});
						drop.trigger("liszt:updated");
					});
				}
			});

			if(!depends.val()) {
				drop.disable(drop.data('unselected'));
			}
		},
		disable: function(text){
			this.empty().append($("<option />").val("").text(text)).attr("disabled", "disabled").trigger("liszt:updated");
		},
		enable: function(){
			this.empty().removeAttr("disabled").next().removeClass('chzn-disabled');
		}
	});

});
