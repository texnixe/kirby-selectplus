(function($) {
  $.fn.selectplus = function() {
    return this.each(function() {
      var fieldname = 'selectplus';
      var addbtn = $('.add-page-button');
      var fieldContainer = $('.selectplus-formfield-wrapper');
      var selectfield = $('.selectplus-content');
      var fields = fieldContainer.find('input').not('input[type="button"]');
      var borderColor = fields.css('border');

      addbtn.unbind('click').on('click', function(e) {
         e.preventDefault();
         selectfield.toggle();
         fieldContainer.toggle();
      });


      fieldContainer.on('click', '.save-button', function(e) {
        $('.selectplus-message').html('').removeClass('error success');
        fields.css('border', borderColor);
        fields.next('span').remove();
        var message = {};
        fields.each(function(index) {

          if($(this).attr('required') && !$(this).val()) {
            var label = $(this).prev().text();
            $(this).css('border', '1px solid red');
            $(this).after('<span class="error">Bitte ' + label + ' ausfüllen</span>');

            message[$( this ).attr('name')] = label;
          };
        });

        if(jQuery.isEmptyObject(message)) {
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

    var data = item;
    $.ajax({
      contentType: "application/json; charset=utf-8",
      url: baseURL + '/selectplus',
      type: 'POST',
      data: data,
      dataType: "json",
      success: function(response) {
        messageBox.append(response.message).addClass(response.class);

        if(response.class == 'success') {

          selectbox.append('<option value="'+response.uid+'" selected>'+response.title+'</option>');

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
