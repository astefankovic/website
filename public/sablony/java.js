
         
      jQuery(document).ready(function($) 
      {
        $(".clickableRow").click(function() 
        {
          window.document.location = $(this).attr("href");
        });
      }); 


      $('.multi-field-wrapper').each(function() {
          var $wrapper = $('.multi-fields', this);
          $(".add-field", $(this)).click(function(e) {
              $('.multi-field:first-child', $wrapper).clone(true).appendTo($wrapper).find('input').val('').focus();
          });
          $('.multi-field .remove-field', $wrapper).click(function() {
              if ($('.multi-field', $wrapper).length > 1)
                  $(this).parents('.multi-field').remove();
          });
      });
