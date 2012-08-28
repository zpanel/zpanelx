/**
 * Functions used in Setup configuration forms
 */

// show this window in top frame
if (top != self) {
    window.top.location.href = location;
}

// ------------------------------------------------------------------
// Messages
//

// stores hidden message ids
var hiddenMessages = [];

$(function() {
    var hidden = hiddenMessages.length;
    for (var i = 0; i < hidden; i++) {
        $('#'+hiddenMessages[i]).css('display', 'none');
    }
    if (hidden > 0) {
        var link = $('#show_hidden_messages');
        link.click(function(e) {
            e.preventDefault();
            for (var i = 0; i < hidden; i++) {
                $('#'+hiddenMessages[i]).show(500);
            }
            $(this).remove();
        });
        link.html(link.html().replace('#MSG_COUNT', hidden));
        link.css('display', '');
    }
});

//
// END: Messages
// ------------------------------------------------------------------

// ------------------------------------------------------------------
// Form validation and field operations
//

$.extend(true, validators, {
    // field validators
    _field: {
        /**
         * hide_db field
         *
         * @param {boolean} isKeyUp
         */
        hide_db: function(isKeyUp) {
            if (!isKeyUp && this.value != '') {
                var data = {};
                data[this.id] = this.value;
                ajaxValidate(this, 'Servers/1/hide_db', data);
            }
            return true;
        },
        /**
         * TrustedProxies field
         *
         * @param {boolean} isKeyUp
         */
        TrustedProxies: function(isKeyUp) {
            if (!isKeyUp && this.value != '') {
                var data = {};
                data[this.id] = this.value;
                ajaxValidate(this, 'TrustedProxies', data);
            }
            return true;
        }
    },
    // fieldset validators
    _fieldset: {
        /**
         * Validates Server fieldset
         *
         * @param {boolean} isKeyUp
         */
        Server: function(isKeyUp) {
            if (!isKeyUp) {
                ajaxValidate(this, 'Server', getAllValues());
            }
            return true;
        },
        /**
         * Validates Server_login_options fieldset
         *
         * @param {boolean} isKeyUp
         */
        Server_login_options: function(isKeyUp) {
            return validators._fieldset.Server.apply(this, [isKeyUp]);
        },
        /**
         * Validates Server_pmadb fieldset
         *
         * @param {boolean} isKeyUp
         */
        Server_pmadb: function(isKeyUp) {
            if (isKeyUp) {
                return true;
            }

            var prefix = getIdPrefix($(this).find('input'));
            var pmadb_active = $('#' + prefix + 'pmadb').val() != '';
            if (pmadb_active) {
                ajaxValidate(this, 'Server_pmadb', getAllValues());
            }

            return true;
        }
    }
});

/**
 * Calls server-side validation procedures
 *
 * @param {Element} parent  input field in <fieldset> or <fieldset>
 * @param {String}  id      validator id
 * @param {Object}  values  values hash {element1_id: value, ...}
 */
function ajaxValidate(parent, id, values)
{
    parent = $(parent);
    // ensure that parent is a fieldset
    if (parent.attr('tagName') != 'FIELDSET') {
        parent = parent.closest('fieldset');
        if (parent.length == 0) {
            return false;
        }
    }

    if (parent.data('ajax') != null) {
        parent.data('ajax').abort();
    }

    parent.data('ajax', $.ajax({
        url: 'validate.php',
        cache: false,
        type: 'POST',
        data: {
            token: parent.closest('form').find('input[name=token]').val(),
            id: id,
            values: $.toJSON(values)
        },
        success: function(response) {
            if (response == null) {
                return;
            }

            var error = {};
            if (typeof response != 'object') {
                error[parent.id] = [response];
            } else if (typeof response['error'] != 'undefined') {
                error[parent.id] = [response['error']];
            } else {
                for (var key in response) {
                    var value = response[key];
                    error[key] = jQuery.isArray(value) ? value : [value];
                }
            }
            displayErrors(error);
        },
        complete: function() {
            parent.removeData('ajax');
        }
    }));

    return true;
}

//
// END: Form validation and field operations
// ------------------------------------------------------------------

// ------------------------------------------------------------------
// User preferences allow/disallow UI
//

$(function() {
   $('.userprefs-allow').click(function(e) {
       if (this != e.target) {
           return;
       }
       var el = $(this).find('input');
       if (el.attr('disabled')) {
           return;
       }
       el.attr('checked', !el.attr('checked'));
   });
});

//
// END: User preferences allow/disallow UI
// ------------------------------------------------------------------
