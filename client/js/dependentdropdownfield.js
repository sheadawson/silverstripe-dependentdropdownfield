
jQuery.entwine("dependentdropdown", function ($) {

  $(":input.dependent-dropdown").entwine({
    onmatch: function () {
      var drop = this;
      var fieldName = drop.data('depends').replace(/[#;&,.+*~':"!^$[\]()=>|\/]/g, "\\$&");
      var depends = ($(":input[name=" + fieldName + "]"));
      var dependsTreedropdownfield = ($(".listbox[id$=" + fieldName + "]"));
      var displayError = function(data, element) {
        element.parent().append($("<div class='mt-1 mb-0 message error'/>").text(data.error));
      };

      this.parents('.field:first').addClass('dropdown');

      if (dependsTreedropdownfield.length) {
        dependsTreedropdownfield.on('change', function (e) {
          var newValue = dependsTreedropdownfield.val();
          var selectedValuesArray = drop.val();

          // if the new value is not set, set it as disabled
          if (!newValue) {
            drop.disable(drop.data('unselected'));
            return;
          }

          drop.disable("Loading...");
          $.get(
            drop.data('link'),
            {
              val: newValue,
              selectedValues: selectedValuesArray,
            },
            function (data) {
              var dependant = $('.dependent-dropdown');
              var hasError = typeof data.error !== 'undefined';

              if (dependant.hasClass('chosen-disabled')) {
                dependant.removeClass('chosen-disabled');
              }

              if (data.length === 0 || hasError) {
                dependant.addClass('chosen-disabled');
              }

              if (hasError) {
                return displayError(data, dependsTreedropdownfield);
              }

              drop.enable();
              if (drop.data('empty') || drop.data('empty') === "") {
                drop.append($("<option />").val("").text(drop.data('empty')));
              }

              $.each(data, function () {
                drop.append($("<option />").val(this.k).text(this.v).prop('selected', this.s));
              });
              drop.trigger("liszt:updated").trigger("chosen:updated").trigger("change");
            }
          );
        });

        return;
      }

      depends.change(function () {
        if (!this.value) {
          drop.disable(drop.data('unselected'));
        } else {
          drop.disable("Loading...");

          $.get(drop.data('link'), {
              val: this.value
            },
            function (data) {
              var hasError = typeof data.error !== 'undefined';

              if (hasError) {
                return displayError(data, depends);
              }

              drop.enable();

              if (drop.data('empty') || drop.data('empty') === "") {
                drop.append($("<option />").val("").text(drop.data('empty')));
              }

              $.each(data, function () {
                drop.append($("<option />").val(this.k).text(this.v));
              });
              drop.trigger("liszt:updated").trigger("chosen:updated").trigger("change");
            });
        }
      });

      if (!depends.val()) {
        drop.disable(drop.data('unselected'));
      }
    },
    disable: function (text) {
      this.empty().append($("<option />").val("").text(text)).attr("disabled", "disabled").trigger("liszt:updated").trigger("chosen:updated");
    },
    enable: function () {
      this.empty().removeAttr("disabled").next().removeClass('chzn-disabled');
    }
  });

});
