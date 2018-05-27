(function($) {
  $.fn.selectplus = function() {
    return this.each(function() {
      var fieldname = 'selectplus';
      var addbtn = $('.add-page-button');
      var fieldContainer = $('.selectplus-formfield-wrapper');
      var selectfield = $('.selectplus-content');
      var fields = fieldContainer.find('input').not('input[type="button"]');
      var borderColor = fields.css('border');
      var message = $('.selectplus-message');

      addbtn.unbind('click').on('click', function(e) {
         e.preventDefault();
         fields.val('');
         selectfield.toggle();
         fieldContainer.toggle();
      });


      fieldContainer.on('click', '.save-button', function() {
        message.html('').removeClass('error success');
        fields.css('border', borderColor).next('span').remove();
        var errorMessage = {};
        fields.each(function(index) {
          if($(this).val().length === 0 && $(this).data("required")) {
            var label = $(this).prev().contents().get(0).nodeValue;

            $(this).css('border', '2px solid #b3000a');
            $(this).after('<span class="error">'+$(this).data('message')+'</span>');

            errorMessage[$( this ).attr('name')] = label;
          };
        });

        if(jQuery.isEmptyObject(errorMessage)) {
          $.fn.ajaxSelectplus(fieldname, fields, fieldContainer, selectfield);
        }
        return false;
      });

    });

  };

  // Ajax function
  $.fn.ajaxSelectplus = function(fieldname, fields, fieldContainer, selectfield) {
    item = {};
    fields.each(function() {
      item[$( this ).attr('name')] = $(this).val();
    });
    var blueprintFieldname = $('.selectplus-field').data('fieldname');
    var selectbox = $('.selectplus-field').find('select');
    var baseURL = window.location.href.replace(/(\/edit.*)/g, '/field') + '/' + blueprintFieldname + '/' + fieldname;
    var messageBox = $('.selectplus-message');
    var form = $(form);

    var data = item;
    $.ajax({
      contentType: "application/json; charset=utf-8",
      url: baseURL + '/selectplus',
      type: 'POST',
      data: data,
      dataType: "json",
      success: function(response) {
        console.log(fields);
        messageBox.append(response.message).addClass(response.class);

        if(response.class == 'success') {

          selectbox.append('<option value="'+response.uid+'" selected>'+response.title+'</option>');
          selectbox.trigger('change');
          selectbox.closest(form).trigger('keep');


          setTimeout(function(){
            selectfield.fadeIn(200);
            fieldContainer.fadeOut(200);
            messageBox.fadeOut(200).removeClass('success').html('');
          }, 1000);
          fields.val('');

        }
      }
    });
  };

})(jQuery);
