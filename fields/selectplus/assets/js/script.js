(function($) {
  $.fn.selectplus = function() {
    return this.each(function() {
      var fieldname = 'selectplus';
      var field = $(this);
      var btn = $('.add-page-button');
      var container = $('.field-selectplus');
      var selectWithAdd = $('.select-with-add').children('.field-content');

      container.hide();

      btn.on('click', function(e) {

        e.preventDefault();

        selectWithAdd.toggle();
        container.toggle();
      });


      container.on('click', '.save-button', function(e) {
        //check if the first field is empty
        var fields = container.find('input');
        var uid = container.find('input').first().val();

        if(uid == '') {
          alert('Das Location-Feld darf nicht leer sein!');
        }
        $.fn.ajaxSelectplus(fieldname, fields);
        return false;
      });


    });

  };

  // Ajax function
  $.fn.ajaxSelectplus = function(fieldname, fields) {
    item = {}
    fields.each(function() {

     item[$( this ).attr('name')] = $(this).val();
    });

    var blueprintFieldname = $('.select-with-add').data('fieldname');
    var container = $('.field-selectplus');
    var baseURL = window.location.href.replace(/(\/edit.*)/g, '/field') + '/' + blueprintFieldname + '/' + fieldname;

    var data = item;
    $.ajax({
      //contentType: "application/json; charset=utf-8",
      url: baseURL + '/selectplus',
      type: 'POST',
      data: data,
      dataType: "json",
      success: function(response) {
        if(response.class == 'error') {

          container.show().html(response.message).addClass(response.class);
        }

        if(response.class == 'success') {

          container.show().html(response.message).addClass(response.class);

          setTimeout(function () {
              window.location.reload();
          }, 500);
        }
      }
    });
  };

})(jQuery);
