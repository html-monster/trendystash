'use strict';

var G_CURRENTPAGE = '';
var G_VARS = {
  isrentProduct: 0,
  rentRanges: []
};

$j(document).ready(function () {

  var windowWidth = $j(window).width();

  $j(window).resize(function () {

    windowWidth = $j(this).width();
  });

  $j('.btn-search').click(function () {

    if ($j('.btn-search') && $j('.btn-search').hasClass('active')) {

      $j(this).removeClass('active');

      $j(this).next().removeClass('active');
    } else if ($j('.btn-search')) {

      $j('#header-cart').removeClass('skip-active');

      $j('#header-account').removeClass('skip-active');

      $j(this).addClass('active');

      $j(this).next().addClass('active');
    }
  });

  if ($j('.btn-search')) {
    var closeSearch = function closeSearch() {

      $j('.btn-search').removeClass('active');

      $j('form#search_mini_form').removeClass('active');
    };

    $j(document).click(function (event) {

      if ($j(event.target).closest(".btn-search").length || $j(event.target).closest("#search_mini_form").length) return;

      if ($j(event.target).closest(".skip-account").length || $j(event.target).closest("#header-cart").length) {

        closeSearch();

        return;
      }

      if ($j(event.target).closest(".skip-cart").length) {

        closeSearch();

        return;
      }

      $j('#header-cart').removeClass('skip-active');

      $j('#header-account').removeClass('skip-active');

      closeSearch();

      event.stopPropagation();
    });
  }

  $j(window).resize(function () {

    resizeMenuItems();
  });

  resizeMenuItems();

  function resizeMenuItems() {

    if (windowWidth > 770) {

      $j('ul.level0').each(function () {

        if ($j(this).find('li.parent').length != 0) $j(this).css('width', '500px');
      });
    } else {

      $j('ul.level0').each(function () {

        if ($j(this).find('li.parent').length != 0) $j(this).css('width', 'auto');
      });
    }
  }

  $j('.level0').each(function () {

    var link = $j(this).children('a');

    if (/\/home/i.test(link.attr('href'))) link.attr('href', '/how-it-works-rent-a-bag');
  });

  $j('.level1.view-all').each(function () {

    var link = $j(this).children('a');

    if (/\/home/i.test(link.attr('href'))) $j(this).hide();
  });

  $j('.sidebar').on('click', '.fme-filter .block-subtitle--filter', function () {

    $j(this).toggleClass('active');

    $j(this).parents('.block-content').toggleClass('active');
  });

  function setEqualHeight(columns) {

    var tallestcolumn = 0;

    columns.each(function () {

      var currentHeight = $j(this).height();

      if (currentHeight > tallestcolumn) {

        tallestcolumn = currentHeight;
      }
    });

    columns.height(tallestcolumn);
  }

  if ($j('body').hasClass('cms-home')) {
    var carousel = function carousel() {

      var owl = $j(".slider0");

      owl.owlCarousel({

        items: 3,

        loop: true,

        navigation: true,

        autoHeight: true,

        dots: true,

        singleItem: false,

        slideSpeed: 600,

        paginationSpeed: 600,

        rewindSpeed: 600,

        scrollPerPage: false,

        margin: 50,

        stopOnHover: true

      });

      $j(".next_button").click(function () {

        owl.trigger("owl.next");
      });

      $j(".prev_button").click(function () {

        owl.trigger("owl.prev");
      });

      owl.on("resized.owl.carousel", function (event) {

        var $jthis = $j(this);

        $jthis.find(".owl-height").css("height", $jthis.find(".owl-item.active").height());
      });

      setTimeout(function () {

        owl.find(".owl-height").css("height", owl.find(".owl-item.active").height());
      }, 5000);
    };

    ;

    carousel();
  }

  var today = moment(+moment().format('x') + 86400000 * 2).format('DD/MM/YYYY');

  var today_unix = +moment(moment(today, 'DD/MM/YYYY')).format('x');

  var endDate = today_unix;

  var some_date_range = G_VARS.rentRanges;

  var input_date = $j('input[name="daterange"]');

  var jj = 1;

  input_date.daterangepicker({

    locale: {

      format: 'DD/MM/YYYY'

    },

    "showWeekNumbers": true,

    "autoApply": true,

    "opens": "center",

    "minDate": today,

    "endDate": moment(endDate).format('DD/MM/YYYY'),

    "dateLimit": { days: 13 },

    "isInvalidDate": function isInvalidDate(date) {

      for (var ii = 0; ii < some_date_range.length; ii++) {

        if (some_date_range[ii][0] * 1000 - 122800000 <= +moment(date).format('x') && +moment(date).format('x') <= some_date_range[ii][1] * 1000 + 122800000 || +moment(date).format('x') == today_unix && today_unix + 209200000 >= some_date_range[ii][0] * 1000) {

          if (jj < some_date_range.length - 1) {

            jj++;
          }

          return true;
        }
      }
    }

  });

  input_date.on('apply.daterangepicker', function (ev, picker) {

    var some_date_min = 0;

    for (var ii = 0; ii < some_date_range.length; ii++) {

      var some_date = some_date_range[ii][0] * 1000;

      var startDate = +picker.startDate.format('x') - 122800000;

      var endDate = +picker.endDate.format('x') + 122800000;

      if (some_date > startDate && some_date < endDate) {

        if (some_date_min > some_date || some_date_min === 0) {

          some_date_min = some_date;
        }

        var min_date = moment(some_date_min - 122800000, "x");

        min_date = moment(min_date).format('DD-MM-YYYY');

        input_date.data('daterangepicker').setEndDate(min_date);
      }
    }
  });

  $j(input_date).change(function () {

    var val = $j(this).val().split(' ');

    if (val[0] == val[2]) {

      input_date.val('');

      $j('#error').text('Rent is possible for at least two days');
    } else {

      $j('.options_from_date').val(val[0]);

      $j('.options_to_date').val(val[2]);

      $j('#error').text('');
    }
  });

  $j('.add-to-cart-buttons .btn-cart').click(function () {

    var val = input_date.val();

    if (val == '') {

      $j('#error').text('The field must not be empty');
    } else {

      productAddToCartForm.submit(this);
    }
  });

  $j('.question').click(function () {

    $j(this).next().slideToggle(400);
  });
});
"use strict";

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var __DEBUG__ = true;

var AjaxSend = function () {
    function AjaxSend() {
        _classCallCheck(this, AjaxSend);

        Object.defineProperty(this, "options", {
            enumerable: true,
            writable: true,
            value: {
                formData: null,
                message: "",
                url: "",
                respCodeName: 'Error',
                respCodes: [],
                beforeChkResponse: null
            }
        });
    }

    _createClass(AjaxSend, [{
        key: "send",
        value: function send(inProps) {
            var self = this;
            var props = _extends({}, this.options, inProps);
            var message = props.message;

            var promise = new Promise(function (resolve, reject) {
                jQuery.ajax({
                    url: props.url,

                    type: 'POST',
                    success: function success(data) {
                        var error = -1001;
                        try {
                            __DEBUG__ && console.log('data AJAX', data);

                            data = JSON.parse(data);

                            if (props.beforeChkResponse) data = props.beforeChkResponse(data);

                            if (data[props.respCodeName] > 100 && data[props.respCodeName] < 200) {
                                error = -data[props.respCodeName];
                                throw new Error(message);
                            } else if (data[props.respCodeName] == 100) {
                                error = -100;
                                throw new Error(message);
                            } else if (data[props.respCodeName] == 200) {
                                error = 100;
                                throw new Error("");
                            } else {
                                error = -1000;
                                throw new Error(message);
                            }
                        } catch (e) {
                            error < 0 && console.warn('E', error);
                            switch (error) {
                                case -100:
                                    ;
                                case -1000:
                                    ;break;
                                default:
                                    var _iteratorNormalCompletion = true;
                                    var _didIteratorError = false;
                                    var _iteratorError = undefined;

                                    try {
                                        for (var _iterator = props.respCodes[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
                                            var val = _step.value;

                                            if (error == val.code) {
                                                message = val.message || val.callback && val.callback(data);
                                                break;
                                            }
                                        }
                                    } catch (err) {
                                        _didIteratorError = true;
                                        _iteratorError = err;
                                    } finally {
                                        try {
                                            if (!_iteratorNormalCompletion && _iterator.return) {
                                                _iterator.return();
                                            }
                                        } finally {
                                            if (_didIteratorError) {
                                                throw _iteratorError;
                                            }
                                        }
                                    }

                            }
                        }

                        error < 0 ? reject({ code: error, message: message, data: data }) : resolve({ code: error, message: message, data: data });
                    },
                    error: function error() {
                        reject({ code: -1002, message: message });
                    },

                    data: props.formData || new FormData(),

                    cache: false
                });
            });

            return promise;
        }
    }]);

    return AjaxSend;
}();
"use strict";

var _createClass = function () { function defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } } return function (Constructor, protoProps, staticProps) { if (protoProps) defineProperties(Constructor.prototype, protoProps); if (staticProps) defineProperties(Constructor, staticProps); return Constructor; }; }();

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

var Loading = function () {
    function Loading() {
        _classCallCheck(this, Loading);
    }

    _createClass(Loading, null, [{
        key: "show",
        value: function show() {
            $j(this.loaderSelector).fadeIn(200);
        }
    }, {
        key: "hide",
        value: function hide() {
            $j(this.loaderSelector).fadeOut(200);
        }
    }]);

    return Loading;
}();

Object.defineProperty(Loading, "loaderSelector", {
    enumerable: true,
    writable: true,
    value: "#loading-mask"
});
//# sourceMappingURL=data:application/json;charset=utf8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbImNvbW1vbi5qcyIsIkFqYXhTZW5kLmpzIiwiTG9hZGluZy5qcyJdLCJuYW1lcyI6WyJHX0NVUlJFTlRQQUdFIiwiR19WQVJTIiwiaXNyZW50UHJvZHVjdCIsInJlbnRSYW5nZXMiLCIkaiIsImRvY3VtZW50IiwicmVhZHkiLCJ3aW5kb3dXaWR0aCIsIndpbmRvdyIsIndpZHRoIiwicmVzaXplIiwiY2xpY2siLCJoYXNDbGFzcyIsInJlbW92ZUNsYXNzIiwibmV4dCIsImFkZENsYXNzIiwiY2xvc2VTZWFyY2giLCJldmVudCIsInRhcmdldCIsImNsb3Nlc3QiLCJsZW5ndGgiLCJzdG9wUHJvcGFnYXRpb24iLCJyZXNpemVNZW51SXRlbXMiLCJlYWNoIiwiZmluZCIsImNzcyIsImxpbmsiLCJjaGlsZHJlbiIsImF0dHIiLCJoaWRlIiwib24iLCJ0b2dnbGVDbGFzcyIsInBhcmVudHMiLCJzZXRFcXVhbEhlaWdodCIsImNvbHVtbnMiLCJ0YWxsZXN0Y29sdW1uIiwiY3VycmVudEhlaWdodCIsImhlaWdodCIsImNhcm91c2VsIiwib3dsIiwib3dsQ2Fyb3VzZWwiLCJpdGVtcyIsImxvb3AiLCJuYXZpZ2F0aW9uIiwiYXV0b0hlaWdodCIsImRvdHMiLCJzaW5nbGVJdGVtIiwic2xpZGVTcGVlZCIsInBhZ2luYXRpb25TcGVlZCIsInJld2luZFNwZWVkIiwic2Nyb2xsUGVyUGFnZSIsIm1hcmdpbiIsInN0b3BPbkhvdmVyIiwidHJpZ2dlciIsIiRqdGhpcyIsInNldFRpbWVvdXQiLCJ0b2RheSIsIm1vbWVudCIsImZvcm1hdCIsInRvZGF5X3VuaXgiLCJzb21lX2RhdGVfcmFuZ2UiLCJpbnB1dF9kYXRlIiwiamoiLCJkYXRlcmFuZ2VwaWNrZXIiLCJsb2NhbGUiLCJkYXlzIiwiZGF0ZSIsImlpIiwiZXYiLCJwaWNrZXIiLCJzb21lX2RhdGVfbWluIiwic29tZV9kYXRlIiwic3RhcnREYXRlIiwiZW5kRGF0ZSIsIm1pbl9kYXRlIiwiZGF0YSIsInNldEVuZERhdGUiLCJjaGFuZ2UiLCJ2YWwiLCJzcGxpdCIsInRleHQiLCJwcm9kdWN0QWRkVG9DYXJ0Rm9ybSIsInN1Ym1pdCIsInNsaWRlVG9nZ2xlIiwiX19ERUJVR19fIiwiQWpheFNlbmQiLCJmb3JtRGF0YSIsIm1lc3NhZ2UiLCJ1cmwiLCJyZXNwQ29kZU5hbWUiLCJyZXNwQ29kZXMiLCJiZWZvcmVDaGtSZXNwb25zZSIsImluUHJvcHMiLCJzZWxmIiwicHJvcHMiLCJvcHRpb25zIiwicHJvbWlzZSIsIlByb21pc2UiLCJyZXNvbHZlIiwicmVqZWN0IiwialF1ZXJ5IiwiYWpheCIsInR5cGUiLCJzdWNjZXNzIiwiZXJyb3IiLCJjb25zb2xlIiwibG9nIiwiSlNPTiIsInBhcnNlIiwiRXJyb3IiLCJlIiwid2FybiIsImNvZGUiLCJjYWxsYmFjayIsIkZvcm1EYXRhIiwiY2FjaGUiLCJMb2FkaW5nIiwibG9hZGVyU2VsZWN0b3IiLCJmYWRlSW4iLCJmYWRlT3V0Il0sIm1hcHBpbmdzIjoiOztBQUFBLElBQUlBLGdCQUFnQixFQUFwQjtBQUNBLElBQUlDLFNBQVM7QUFDWkMsaUJBQWUsQ0FESDtBQUVaQyxjQUFZO0FBRkEsQ0FBYjs7QUFPQUMsR0FBR0MsUUFBSCxFQUFhQyxLQUFiLENBQW1CLFlBQVk7O0FBRTlCLE1BQUlDLGNBQWNILEdBQUdJLE1BQUgsRUFBV0MsS0FBWCxFQUFsQjs7QUFFQUwsS0FBR0ksTUFBSCxFQUFXRSxNQUFYLENBQWtCLFlBQVU7O0FBRTNCSCxrQkFBZUgsR0FBRyxJQUFILEVBQVNLLEtBQVQsRUFBZjtBQUVBLEdBSkQ7O0FBWUFMLEtBQUcsYUFBSCxFQUFrQk8sS0FBbEIsQ0FBd0IsWUFBWTs7QUFFbkMsUUFBSVAsR0FBRyxhQUFILEtBQXFCQSxHQUFHLGFBQUgsRUFBa0JRLFFBQWxCLENBQTJCLFFBQTNCLENBQXpCLEVBQStEOztBQUU5RFIsU0FBRyxJQUFILEVBQVNTLFdBQVQsQ0FBcUIsUUFBckI7O0FBRUFULFNBQUcsSUFBSCxFQUFTVSxJQUFULEdBQWdCRCxXQUFoQixDQUE0QixR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U0FBdEIsOEhBQ0E7QUFBQSxnREFEU1osR0FDVDs7QUFDSSxnREFBSTBCLFNBQVMxQixJQUFJa0MsSUFBakIsRUFDQTtBQUNJekIsMERBQVVULElBQUlTLE9BQUosSUFBZVQsSUFBSW1DLFFBQUosSUFBY25DLElBQUltQyxRQUFKLENBQWF0QyxJQUFiLENBQXZDO0FBQ0E7QUFDSDtBQUNKO0FBVEw7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTs7QUFKSjtBQWVIOztBQUdENkIsZ0NBQVEsQ0FBUixHQUFZTCxPQUFPLEVBQUNhLE1BQU1SLEtBQVAsRUFBY2pCLGdCQUFkLEVBQXVCWixVQUF2QixFQUFQLENBQVosR0FBbUR1QixRQUFRLEVBQUNjLE1BQU1SLEtBQVAsRUFBY2pCLGdCQUFkLEVBQXVCWixVQUF2QixFQUFSLENBQW5EO0FBQ0gscUJBcEVPO0FBcUVSNkIsMkJBQU8saUJBQVc7QUFDZEwsK0JBQU8sRUFBQ2EsTUFBTSxDQUFDLElBQVIsRUFBY3pCLGdCQUFkLEVBQVA7QUFDSCxxQkF2RU87O0FBeUVSWiwwQkFBTW1CLE1BQU1SLFFBQU4sSUFBa0IsSUFBSTRCLFFBQUosRUF6RWhCOztBQTJFUkMsMkJBQU87QUEzRUMsaUJBQVo7QUFrRkgsYUFwRmEsQ0FBZDs7QUFzRkEsbUJBQU9uQixPQUFQO0FBQ0g7Ozs7Ozs7Ozs7O0lDNUdDb0I7Ozs7Ozs7K0JBTUY7QUFDSWhILGVBQUcsS0FBS2lILGNBQVIsRUFBd0JDLE1BQXhCLENBQStCLEdBQS9CO0FBQ0g7OzsrQkFJRDtBQUNJbEgsZUFBRyxLQUFLaUgsY0FBUixFQUF3QkUsT0FBeEIsQ0FBZ0MsR0FBaEM7QUFDSDs7Ozs7O3NCQWRDSDs7O1dBRW9DIiwiZmlsZSI6ImNvbW1vbi5qcyIsInNvdXJjZXNDb250ZW50IjpbInZhciBHX0NVUlJFTlRQQUdFID0gJyc7XHJ2YXIgR19WQVJTID0ge1xyXHRpc3JlbnRQcm9kdWN0OiAwLFxyXHRyZW50UmFuZ2VzOiBbXSxccn07XHJcclxyXHIkaihkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xyXHJcdHZhciB3aW5kb3dXaWR0aCA9ICRqKHdpbmRvdykud2lkdGgoKTtcclxyXHQkaih3aW5kb3cpLnJlc2l6ZShmdW5jdGlvbigpe1xyXHJcdFx0d2luZG93V2lkdGggPSAoJGoodGhpcykud2lkdGgoKSk7XHJcclx0fSk7XHJcclx0Ly8gaGVhZGVyPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHJcclx0Ly9zZWFyY2ggaGlkZS9zaG93XHJcclx0JGooJy5idG4tc2VhcmNoJykuY2xpY2soZnVuY3Rpb24gKCkge1xyXHJcdFx0aWYgKCRqKCcuYnRuLXNlYXJjaCcpICYmICRqKCcuYnRuLXNlYXJjaCcpLmhhc0NsYXNzKCdhY3RpdmUnKSkge1xyXHJcdFx0XHQkaih0aGlzKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcclx0XHRcdCRqKHRoaXMpLm5leHQoKS5yZW1vdmVDbGFzcygnYWN0aXZlJyk7XHJcclx0XHR9XHJcclx0XHRlbHNlIGlmICgkaignLmJ0bi1zZWFyY2gnKSkge1xyXHJcdFx0XHQkaignI2hlYWRlci1jYXJ0JykucmVtb3ZlQ2xhc3MoJ3NraXAtYWN0aXZlJyk7XHJcclx0XHRcdCRqKCcjaGVhZGVyLWFjY291bnQnKS5yZW1vdmVDbGFzcygnc2tpcC1hY3RpdmUnKTtcclxyXHRcdFx0JGoodGhpcykuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXHJcdFx0XHQkaih0aGlzKS5uZXh0KCkuYWRkQ2xhc3MoJ2FjdGl2ZScpO1xyXHJcdFx0fVxyXHJcdH0pO1xyXHJcdGlmICgkaignLmJ0bi1zZWFyY2gnKSkge1xyXHJcdFx0JGooZG9jdW1lbnQpLmNsaWNrKGZ1bmN0aW9uIChldmVudCkge1xyXHJcdFx0XHRpZiAoJGooZXZlbnQudGFyZ2V0KS5jbG9zZXN0KFwiLmJ0bi1zZWFyY2hcIikubGVuZ3RoIHx8ICRqKGV2ZW50LnRhcmdldCkuY2xvc2VzdChcIiNzZWFyY2hfbWluaV9mb3JtXCIpLmxlbmd0aClcclxyXHRcdFx0XHRyZXR1cm47XHJcclx0XHRcdGlmICgkaihldmVudC50YXJnZXQpLmNsb3Nlc3QoXCIuc2tpcC1hY2NvdW50XCIpLmxlbmd0aCB8fCAkaihldmVudC50YXJnZXQpLmNsb3Nlc3QoXCIjaGVhZGVyLWNhcnRcIikubGVuZ3RoKSB7XHJcclx0XHRcdFx0Y2xvc2VTZWFyY2goKTtcclxyXHRcdFx0XHRyZXR1cm47XHJcclx0XHRcdH1cclxyXHRcdFx0aWYgKCRqKGV2ZW50LnRhcmdldCkuY2xvc2VzdChcIi5za2lwLWNhcnRcIikubGVuZ3RoKXtcclxyXHRcdFx0XHRjbG9zZVNlYXJjaCgpO1xyXHJcdFx0XHRcdHJldHVybjtcclxyXHRcdFx0fVxyXHJcdFx0XHQkaignI2hlYWRlci1jYXJ0JykucmVtb3ZlQ2xhc3MoJ3NraXAtYWN0aXZlJyk7XHJcclx0XHRcdCRqKCcjaGVhZGVyLWFjY291bnQnKS5yZW1vdmVDbGFzcygnc2tpcC1hY3RpdmUnKTtcclxyXHRcdFx0Y2xvc2VTZWFyY2goKTtcclxyXHRcdFx0ZXZlbnQuc3RvcFByb3BhZ2F0aW9uKCk7XHJcclx0XHR9KTtcclxyXHRcdGZ1bmN0aW9uIGNsb3NlU2VhcmNoKCl7XHJcclx0XHRcdCRqKCcuYnRuLXNlYXJjaCcpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxyXHRcdFx0JGooJ2Zvcm0jc2VhcmNoX21pbmlfZm9ybScpLnJlbW92ZUNsYXNzKCdhY3RpdmUnKTtcclxyXHRcdH1cclxyXHR9XHJcclx0JGood2luZG93KS5yZXNpemUoZnVuY3Rpb24oKXtcclxyXHRcdHJlc2l6ZU1lbnVJdGVtcygpO1xyXHJcdH0pO1xyXHJcdHJlc2l6ZU1lbnVJdGVtcygpO1xyXHJcdGZ1bmN0aW9uICByZXNpemVNZW51SXRlbXMoKXtcclxyXHRcdGlmKHdpbmRvd1dpZHRoID4gNzcwKXtcclxyXHRcdFx0JGooJ3VsLmxldmVsMCcpLmVhY2goZnVuY3Rpb24oKXtcclxyXHRcdFx0XHRpZigkaih0aGlzKS5maW5kKCdsaS5wYXJlbnQnKS5sZW5ndGggIT0gMClcclxyXHRcdFx0XHRcdCRqKHRoaXMpLmNzcygnd2lkdGgnLCAnNTAwcHgnKTtcclxyXHRcdFx0fSk7XHJcclx0XHR9XHJcclx0XHRlbHNle1xyXHJcdFx0XHQkaigndWwubGV2ZWwwJykuZWFjaChmdW5jdGlvbigpe1xyXHJcdFx0XHRcdGlmKCRqKHRoaXMpLmZpbmQoJ2xpLnBhcmVudCcpLmxlbmd0aCAhPSAwKVxyXHJcdFx0XHRcdFx0JGoodGhpcykuY3NzKCd3aWR0aCcsICdhdXRvJyk7XHJcclx0XHRcdH0pO1xyXHJcdFx0fVxyXHJcdH1cclxyXHJcclx0Ly9NZW51XHJcclx0JGooJy5sZXZlbDAnKS5lYWNoKGZ1bmN0aW9uICgpIHtcclxyXHRcdHZhclx0bGluayA9ICRqKHRoaXMpLmNoaWxkcmVuKCdhJyk7XHJcclx0XHRpZihsaW5rLmF0dHIoJ2hyZWYnKSA9PT0gJ2h0dHBzOi8vd3d3LnJlbnQtYS1iYWcuY2x1Yi9ob21lJylcclxyXHRcdFx0XHRsaW5rLmF0dHIoJ2hyZWYnLCAnL2hvdy1pdC13b3Jrcy1yZW50LWEtYmFnJyk7XHJcclx0XHRpZihsaW5rLmF0dHIoJ2hyZWYnKSA9PT0gJ2h0dHA6Ly9yZW50YWJhZy5sb2NhL2hvbWUnKVxyXHJcdFx0XHRsaW5rLmF0dHIoJ2hyZWYnLCAnL2hvdy1pdC13b3Jrcy1yZW50LWEtYmFnJyk7XHJcdH0pO1xyXHJcdCRqKCcubGV2ZWwxLnZpZXctYWxsJykuZWFjaChmdW5jdGlvbiAoKSB7XHJcclx0XHR2YXJcdGxpbmsgPSAkaih0aGlzKS5jaGlsZHJlbignYScpO1xyXHJcdFx0aWYobGluay5hdHRyKCdocmVmJykgPT09ICdodHRwczovL3d3dy5yZW50LWEtYmFnLmNsdWIvaG9tZScpXHJcclx0XHRcdCRqKHRoaXMpLmhpZGUoKTtcclxyXHRcdGlmKGxpbmsuYXR0cignaHJlZicpID09PSAnaHR0cDovL3JlbnRhYmFnLmxvY2EvaG9tZScpXHJcclx0XHRcdCRqKHRoaXMpLmhpZGUoKTtcclxyXHR9KTtcclxyXHJcclx0Ly8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHJcclx0Ly8gc2lkZWJhcj09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHQkaignLnNpZGViYXInKS5vbignY2xpY2snLCAnLmZtZS1maWx0ZXIgLmJsb2NrLXN1YnRpdGxlLS1maWx0ZXInLCBmdW5jdGlvbigpe1xyXHJcdFx0JGoodGhpcykudG9nZ2xlQ2xhc3MoJ2FjdGl2ZScpO1xyXHJcdFx0JGoodGhpcykucGFyZW50cygnLmJsb2NrLWNvbnRlbnQnKS50b2dnbGVDbGFzcygnYWN0aXZlJyk7XHJcclx0fSk7XHJcclx0Ly8gPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHJcclxyXHJcdCAvL3Byb2R1Y3QgY29udGVudD09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcclx0IC8vaGVpZ2h0IG9mIGNvbHVtbnNcclxyXHRmdW5jdGlvbiBzZXRFcXVhbEhlaWdodChjb2x1bW5zKVxyXHJcdHtcclxyXHRcdHZhciB0YWxsZXN0Y29sdW1uID0gMDtcclxyXHRcdGNvbHVtbnMuZWFjaChcclxyXHRcdFx0XHRmdW5jdGlvbigpXHJcclx0XHRcdFx0e1xyXHJcdFx0XHRcdFx0dmFyIGN1cnJlbnRIZWlnaHQgPSAkaih0aGlzKS5oZWlnaHQoKTtcclxyXHRcdFx0XHRcdGlmKGN1cnJlbnRIZWlnaHQgPiB0YWxsZXN0Y29sdW1uKVxyXHJcdFx0XHRcdFx0e1xyXHJcdFx0XHRcdFx0XHR0YWxsZXN0Y29sdW1uID0gY3VycmVudEhlaWdodDtcclxyXHRcdFx0XHRcdH1cclxyXHRcdFx0XHR9XHJcclx0XHQpO1xyXHJcdFx0Y29sdW1ucy5oZWlnaHQodGFsbGVzdGNvbHVtbik7XHJcclx0fVxyXHJcclxyXHQvLyAkaignI2ZtZV9sYXllcmVkX2NvbnRhaW5lcicpLmJpbmQoXCJET01TdWJ0cmVlTW9kaWZpZWRcIixmdW5jdGlvbigpe1xyXHQvL1xyXHQvLyBcdHNldEVxdWFsSGVpZ2h0KCRqKFwiLnByb2R1Y3RzLWdyaWQgID4gLml0ZW0gLnByb2R1Y3QtaW5mb1wiKSk7XHJcdC8vXHJcdC8vIH0pO1xyXHJcdCAvLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcclxyXHJcclxyXHQvL2hvbWUgcGFnZSBjb250ZW50PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHRpZigkaignYm9keScpLmhhc0NsYXNzKCdjbXMtaG9tZScpKXtcclxyXHRcdGZ1bmN0aW9uIGNhcm91c2VsKCkge1xyXHJcdFx0XHR2YXIgb3dsID0gJGooXCIuc2xpZGVyMFwiKTtcclxyXHRcdFx0Ly8gdmFyIG93bDIgPSAkaihcIi5zbGlkZXIxXCIpO1xyXHJcdFx0XHQvLyBvd2wyLm93bENhcm91c2VsKHtcclx0XHRcdC8vXHJcdFx0XHQvLyBcdGl0ZW1zIDogMyxcclx0XHRcdC8vXHJcdFx0XHQvLyBcdGxvb3AgOiB0cnVlLFxyXHRcdFx0Ly9cclx0XHRcdC8vIFx0bmF2aWdhdGlvbiA6IHRydWUsXHJcdFx0XHQvL1xyXHRcdFx0Ly8gXHRhdXRvSGVpZ2h0IDogdHJ1ZSxcclx0XHRcdC8vXHJcdFx0XHQvLyBcdGRvdHMgOiB0cnVlLFxyXHRcdFx0Ly9cclx0XHRcdC8vIFx0c2luZ2xlSXRlbSA6IGZhbHNlLFxyXHRcdFx0Ly9cclx0XHRcdC8vIFx0c2xpZGVTcGVlZCA6IDYwMCxcclx0XHRcdC8vXHJcdFx0XHQvLyBcdHBhZ2luYXRpb25TcGVlZCA6IDYwMCxcclx0XHRcdC8vXHJcdFx0XHQvLyBcdC8vIGF1dG9QbGF5IDogMzUwMCxcclx0XHRcdC8vXHJcdFx0XHQvLyBcdHJld2luZFNwZWVkIDogNjAwLFxyXHRcdFx0Ly9cclx0XHRcdC8vIFx0c2Nyb2xsUGVyUGFnZSA6IGZhbHNlLFxyXHRcdFx0Ly9cclx0XHRcdC8vIFx0bWFyZ2luIDogNTAsXHJcdFx0XHQvL1xyXHRcdFx0Ly8gXHRzdG9wT25Ib3ZlciA6IHRydWVcclx0XHRcdC8vXHJcdFx0XHQvLyB9KTtcclxyXHRcdFx0b3dsLm93bENhcm91c2VsKHtcclxyXHRcdFx0XHRpdGVtcyA6IDMsXHJcclx0XHRcdFx0bG9vcCA6IHRydWUsXHJcclx0XHRcdFx0bmF2aWdhdGlvbiA6IHRydWUsXHJcclx0XHRcdFx0YXV0b0hlaWdodCA6IHRydWUsXHJcclx0XHRcdFx0ZG90cyA6IHRydWUsXHJcclx0XHRcdFx0c2luZ2xlSXRlbSA6IGZhbHNlLFxyXHJcdFx0XHRcdHNsaWRlU3BlZWQgOiA2MDAsXHJcclx0XHRcdFx0cGFnaW5hdGlvblNwZWVkIDogNjAwLFxyXHJcdFx0XHRcdC8vIGF1dG9QbGF5IDogMzAwMCxcclxyXHRcdFx0XHRyZXdpbmRTcGVlZCA6IDYwMCxcclxyXHRcdFx0XHRzY3JvbGxQZXJQYWdlIDogZmFsc2UsXHJcclx0XHRcdFx0bWFyZ2luIDogNTAsXHJcclx0XHRcdFx0c3RvcE9uSG92ZXIgOiB0cnVlXHJcclx0XHRcdH0pO1xyXHJcdFx0XHQkaihcIi5uZXh0X2J1dHRvblwiKS5jbGljayhmdW5jdGlvbigpIHtcclxyXHRcdFx0XHRvd2wudHJpZ2dlcihcIm93bC5uZXh0XCIpO1xyXHJcdFx0XHRcdC8vIG93bDIudHJpZ2dlcihcIm93bC5uZXh0XCIpO1xyXHJcdFx0XHR9KTtcclxyXHRcdFx0JGooXCIucHJldl9idXR0b25cIikuY2xpY2soZnVuY3Rpb24oKSB7XHJcclx0XHRcdFx0b3dsLnRyaWdnZXIoXCJvd2wucHJldlwiKTtcclxyXHRcdFx0XHQvLyBvd2wyLnRyaWdnZXIoXCJvd2wucHJldlwiKTtcclxyXHRcdFx0fSk7XHJcclx0XHRcdG93bC5vbihcInJlc2l6ZWQub3dsLmNhcm91c2VsXCIsIGZ1bmN0aW9uKGV2ZW50KSB7XHJcclx0XHRcdFx0dmFyICRqdGhpcyA9ICRqKHRoaXMpO1xyXHJcdFx0XHRcdCRqdGhpcy5maW5kKFwiLm93bC1oZWlnaHRcIikuY3NzKFwiaGVpZ2h0XCIsICRqdGhpcy5maW5kKFwiLm93bC1pdGVtLmFjdGl2ZVwiKS5oZWlnaHQoKSk7XHJcclx0XHRcdH0pO1xyXHJcdFx0XHRzZXRUaW1lb3V0KGZ1bmN0aW9uKCkge1xyXHJcdFx0XHRcdG93bC5maW5kKFwiLm93bC1oZWlnaHRcIikuY3NzKFwiaGVpZ2h0XCIsIG93bC5maW5kKFwiLm93bC1pdGVtLmFjdGl2ZVwiKS5oZWlnaHQoKSk7XHJcclx0XHRcdFx0Ly8gb3dsMi5maW5kKFwiLm93bC1oZWlnaHRcIikuY3NzKFwiaGVpZ2h0XCIsIG93bDIuZmluZChcIi5vd2wtaXRlbS5hY3RpdmVcIikuaGVpZ2h0KCkpO1xyXHJcdFx0XHR9LCA1MDAwKTtcclxyXHRcdH07XHJcclx0XHRjYXJvdXNlbCgpO1xyXHJcdH1cclxyXHQvLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHQvL3NpbmdsZSBwcm9kdWN0IHBhZ2U9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXHJcdHZhciB0b2RheSA9IG1vbWVudCgoK21vbWVudCgpLmZvcm1hdCgneCcpICsgODY0MDAwMDAgKjIpKS5mb3JtYXQoJ0REL01NL1lZWVknKTtcclxyXHR2YXIgdG9kYXlfdW5peCA9ICttb21lbnQobW9tZW50KHRvZGF5LCAnREQvTU0vWVlZWScpKS5mb3JtYXQoJ3gnKTtcclxyXHR2YXIgc29tZV9kYXRlX3JhbmdlID0gR19WQVJTLnJlbnRSYW5nZXM7XHJcclx0dmFyIGlucHV0X2RhdGUgPSAkaignaW5wdXRbbmFtZT1cImRhdGVyYW5nZVwiXScpO1xyXHJcdHZhciBqaiA9IDE7XHJcclx0aW5wdXRfZGF0ZS5kYXRlcmFuZ2VwaWNrZXIoe1xyXHJcdFx0bG9jYWxlOiB7XHJcclx0XHRcdGZvcm1hdDogJ0REL01NL1lZWVknXHJcclx0XHR9LFxyXHJcdFx0XCJzaG93V2Vla051bWJlcnNcIjogdHJ1ZSxcclxyXHRcdFwiYXV0b0FwcGx5XCI6IHRydWUsXHJcclx0XHRcIm9wZW5zXCI6IFwiY2VudGVyXCIsXHJcclx0XHRcIm1pbkRhdGVcIjogdG9kYXksXHJcclx0XHRcImRhdGVMaW1pdFwiOiB7IGRheXM6IDEzIH0sXHJcclx0XHRcImlzSW52YWxpZERhdGVcIiA6IGZ1bmN0aW9uKGRhdGUpe1xyXHJcdFx0XHRmb3IodmFyIGlpID0gMDsgaWkgPCBzb21lX2RhdGVfcmFuZ2UubGVuZ3RoOyBpaSsrICl7XHJcclx0XHRcdFx0aWYoc29tZV9kYXRlX3JhbmdlW2lpXVswXSAqIDEwMDAgLSAxMjI4MDAwMDAgPD0gK21vbWVudChkYXRlKS5mb3JtYXQoJ3gnKSAmJiArbW9tZW50KGRhdGUpLmZvcm1hdCgneCcpIDw9IHNvbWVfZGF0ZV9yYW5nZVtpaV1bMV0gKiAxMDAwICsgMTIyODAwMDAwXHJcclx0XHRcdFx0XHRcdC8vfHwgc29tZV9kYXRlX3JhbmdlW2pqXVswXSAqIDEwMDAgPT0gK21vbWVudChkYXRlKS5mb3JtYXQoJ3gnKSArIDEyMjgwMDAwMCAmJiArbW9tZW50KGRhdGUpLmZvcm1hdCgneCcpIC0gMTIyODAwMDAwID09IHNvbWVfZGF0ZV9yYW5nZVtpaV1bMV0gKiAxMDAwXHJcclx0XHRcdFx0XHRcdHx8ICgrbW9tZW50KGRhdGUpLmZvcm1hdCgneCcpID09IHRvZGF5X3VuaXgpICYmIHRvZGF5X3VuaXggKyAyMDkyMDAwMDAgPj0gc29tZV9kYXRlX3JhbmdlW2lpXVswXSAqIDEwMDApXHJcclx0XHRcdFx0e1xyXHJcdFx0XHRcdFx0aWYoamogPCBzb21lX2RhdGVfcmFuZ2UubGVuZ3RoIC0gMSl7XHJcclx0XHRcdFx0XHRcdGpqKys7XHJcclx0XHRcdFx0XHR9XHJcclx0XHRcdFx0XHRyZXR1cm4gdHJ1ZTtcclxyXHRcdFx0XHR9XHJcclx0XHRcdH1cclxyXHRcdH1cclxyXHR9KTtcclxyXHRpbnB1dF9kYXRlLm9uKCdhcHBseS5kYXRlcmFuZ2VwaWNrZXInLCBmdW5jdGlvbihldiwgcGlja2VyKSB7XHJcclx0XHR2YXIgc29tZV9kYXRlX21pbiA9IDA7XHJcclx0XHRmb3IodmFyIGlpID0gMDsgaWkgPCBzb21lX2RhdGVfcmFuZ2UubGVuZ3RoOyBpaSsrKXtcclxyXHRcdFx0dmFyIHNvbWVfZGF0ZSA9IHNvbWVfZGF0ZV9yYW5nZVtpaV1bMF0gKiAxMDAwO1xyXHJcdFx0XHR2YXIgc3RhcnREYXRlID0gK3BpY2tlci5zdGFydERhdGUuZm9ybWF0KCd4JykgLSAxMjI4MDAwMDA7XHJcclx0XHRcdHZhciBlbmREYXRlID0gK3BpY2tlci5lbmREYXRlLmZvcm1hdCgneCcpICsgMTIyODAwMDAwO1xyXHJcdFx0XHRpZiAoc29tZV9kYXRlID4gc3RhcnREYXRlICYmIHNvbWVfZGF0ZSA8IGVuZERhdGUpe1xyXHJcdFx0XHRcdGlmKHNvbWVfZGF0ZV9taW4gPiBzb21lX2RhdGUgfHwgc29tZV9kYXRlX21pbiA9PT0gMCl7XHJcclx0XHRcdFx0XHRzb21lX2RhdGVfbWluID0gc29tZV9kYXRlO1xyXHJcdFx0XHRcdH1cclxyXHRcdFx0XHR2YXIgbWluX2RhdGUgPSBtb21lbnQoc29tZV9kYXRlX21pbiAtIDEyMjgwMDAwMCwgXCJ4XCIpO1xyXHJcdFx0XHRcdG1pbl9kYXRlID0gbW9tZW50KG1pbl9kYXRlKS5mb3JtYXQoJ0RELU1NLVlZWVknKTtcclxyXHRcdFx0XHRpbnB1dF9kYXRlLmRhdGEoJ2RhdGVyYW5nZXBpY2tlcicpLnNldEVuZERhdGUobWluX2RhdGUpO1xyXHJcdFx0XHR9XHJcclx0XHR9XHJcclx0fSk7XHJcclx0JGooaW5wdXRfZGF0ZSkuY2hhbmdlKGZ1bmN0aW9uKCl7XHJcclx0XHR2YXIgdmFsID0gJGoodGhpcykudmFsKCkuc3BsaXQoJyAnKTtcclxyXHRcdGlmKHZhbFswXSA9PSB2YWxbMl0pXHJcclx0XHR7XHJcclx0XHRcdGlucHV0X2RhdGUudmFsKCcnKTtcclxyXHRcdFx0JGooJyNlcnJvcicpLnRleHQoJ1JlbnQgaXMgcG9zc2libGUgZm9yIGF0IGxlYXN0IHR3byBkYXlzJyk7XHJcclx0XHR9XHJcclx0XHRlbHNlXHJcclx0XHR7XHJcclx0XHRcdCRqKCcub3B0aW9uc19mcm9tX2RhdGUnKS52YWwodmFsWzBdKTtcclxyXHRcdFx0JGooJy5vcHRpb25zX3RvX2RhdGUnKS52YWwodmFsWzJdKTtcclxyXHRcdFx0JGooJyNlcnJvcicpLnRleHQoJycpO1xyXHJcdFx0fVxyXHJcdH0pO1xyXHJcdCRqKCcuYWRkLXRvLWNhcnQtYnV0dG9ucyAuYnRuLWNhcnQnKS5jbGljayhmdW5jdGlvbigpe1xyXHJcdFx0dmFyIHZhbCA9IGlucHV0X2RhdGUudmFsKCk7XHJcclx0XHRpZih2YWwgPT0gJycpXHJcclx0XHR7XHJcclx0XHRcdCRqKCcjZXJyb3InKS50ZXh0KCdUaGUgZmllbGQgbXVzdCBub3QgYmUgZW1wdHknKTtcclxyXHRcdH1cclxyXHRcdGVsc2VcclxyXHRcdHtcclxyXHRcdFx0cHJvZHVjdEFkZFRvQ2FydEZvcm0uc3VibWl0KHRoaXMpO1xyXHJcdFx0fVxyXHJcdH0pO1xyXHJcdC8vaWYodHlwZW9mIHNvbWVfZGF0ZV9yYW5nZSAhPT0gJ3VuZGVmaW5lZCcgJiYgc29tZV9kYXRlX3JhbmdlLmxlbmd0aCA+IDApe1xyXHJcdC8vXHR2YXIgY29udGFpbmVyID0gJGooJy5zaG9ydC1kZXNjcmlwdGlvbiAuc3RkJyk7XHJcclx0Ly9cdGNvbnRhaW5lci5hcHBlbmQoJzxwPlJlbnRlZCBmb3IgdGhvc2UgZGF0ZXM6PC9wPicpO1xyXHJcdC8vXHRmb3IodmFyIGlpID0gMDsgaWkgPCBzb21lX2RhdGVfcmFuZ2UubGVuZ3RoOyBpaSsrKVxyXHJcdC8vXHR7XHJcclx0Ly9cdFx0dmFyIGZyb20gPSBtb21lbnQoc29tZV9kYXRlX3JhbmdlW2lpXVswXSAqIDEwMDAgLSA4NjQwMDAwMCwgXCJ4XCIpLmZvcm1hdCgnREQvTU0vWVlZWScpO1xyXHJcdC8vXHRcdHZhciB0byA9IG1vbWVudChzb21lX2RhdGVfcmFuZ2VbaWldWzFdICogMTAwMCArIDg2NDAwMDAwLCBcInhcIikuZm9ybWF0KCdERC9NTS9ZWVlZJyk7XHJcclx0Ly9cdFx0Y29udGFpbmVyLmFwcGVuZCgnPHNwYW4+JyArIGZyb20gKyAnIC0gJyArIHRvICsgJzwvc3Bhbj48YnI+JylcclxyXHQvL1x0fVxyXHJcdC8vXHR2YXIgYnV0dG9uID0gJGooJy5zaG9ydC1kZXNjcmlwdGlvbiAuc3RkIC5idXR0b24nKS5kZXRhY2goKTtcclxyXHQvL1x0Y29udGFpbmVyLmFwcGVuZChidXR0b24pO1xyXHJcdC8vfVxyXHJcclxyXHQvLz09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyXHQvL2NhcnQgcGFnZSBjb250ZW50PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyLypcclx0JGooJy5wcm9kdWN0LWNhcnQtaW5mbycpLmVhY2goZnVuY3Rpb24oKXtcclxyXHRcdC8vIGNvbnNvbGUubG9nKC9bMC05XXsyLDR9Wy9dWzAtOV17Miw0fVsvXVswLTldezIsNH0vZ2kudGVzdCgkaih0aGlzKS5jaGlsZHJlbignLml0ZW0tb3B0aW9ucycpLmZpbmQoJ2RkJykudGV4dCgpKSk7XHJcclx0XHRpZigvWzAtOV17Miw0fVsvXVswLTldezIsNH1bL11bMC05XXsyLDR9L2dpLnRlc3QoJGoodGhpcykuY2hpbGRyZW4oJy5pdGVtLW9wdGlvbnMnKS5maW5kKCdkZCcpLnRleHQoKSkpXHJcclx0XHR7XHJcclx0XHRcdCRqKHRoaXMpLm5leHQoKS5uZXh0KCkuaHRtbCgnPHA+LSAgPC9wPicpLmNzcygndmlzaWJpbGl0eScsICd2aXNpYmxlJyk7XHJcclx0XHR9XHJcclx0XHRlbHNlXHJcclx0XHR7XHJcclx0XHRcdCRqKHRoaXMpLm5leHQoKS5uZXh0KCkuY3NzKCd2aXNpYmlsaXR5JywgJ3Zpc2libGUnKTtcclxyXHRcdH1cclxyXHR9KTtcciovXHJcclx0Ly89PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09XHJcclxyXHJcdC8vRkFRPT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PVxyXHJcdCRqKCcucXVlc3Rpb24nKS5jbGljayhmdW5jdGlvbiAoKSB7XHJcclx0XHQkaih0aGlzKS5uZXh0KCkuc2xpZGVUb2dnbGUoNDAwKTtcclxyXHR9KTtcclxyXHQvLyA9PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT09PT1cclxyfSk7XHJcciIsIi8qKlxyXG4gKiBDcmVhdGVkIGJ5IFZsYXNha2ggb24gMDguMDMuMjAxNy5cclxuICovXHJcblxyXG5jb25zdCBfX0RFQlVHX18gPSB0cnVlO1xyXG5cclxuXHJcbmNsYXNzIEFqYXhTZW5kXHJcbntcclxuICAgIC8qKkBwcml2YXRlKi8gb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgZm9ybURhdGE6IG51bGwsXHJcbiAgICAgICAgICAgIG1lc3NhZ2U6IFwiXCIsXHJcbiAgICAgICAgICAgIHVybDogXCJcIixcclxuICAgICAgICAgICAgcmVzcENvZGVOYW1lOiAnRXJyb3InLFxyXG4gICAgICAgICAgICByZXNwQ29kZXM6IFtdLFxyXG4gICAgICAgICAgICBiZWZvcmVDaGtSZXNwb25zZTogbnVsbCxcclxuICAgICAgICB9O1xyXG5cclxuXHJcbiAgICAvKipAcHVibGljKi8gc2VuZChpblByb3BzKVxyXG4gICAge1xyXG4gICAgICAgIHZhciBzZWxmID0gdGhpcztcclxuICAgICAgICB2YXIgcHJvcHMgPSB7Li4udGhpcy5vcHRpb25zLCAuLi5pblByb3BzfTtcclxuICAgICAgICB2YXIgbWVzc2FnZSA9IHByb3BzLm1lc3NhZ2U7XHJcblxyXG4gICAgICAgIGxldCBwcm9taXNlID0gbmV3IFByb21pc2UoKHJlc29sdmUsIHJlamVjdCkgPT5cclxuICAgICAgICB7XHJcbiAgICAgICAgICAgIGpRdWVyeS5hamF4KHtcclxuICAgICAgICAgICAgICAgIHVybDogcHJvcHMudXJsLFxyXG4gICAgICAgICAgICAgICAgLy8gdXJsOiBNYWluQ29uZmlnLkJBU0VfVVJMICsgRFMgKyBNYWluQ29uZmlnLkFKQVhfVEVTVCxcclxuICAgICAgICAgICAgICAgIHR5cGU6ICdQT1NUJyxcclxuICAgICAgICAgICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uKGRhdGEpXHJcbiAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgdmFyIGVycm9yID0gLTEwMDE7XHJcbiAgICAgICAgICAgICAgICAgICAgdHJ5XHJcbiAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBfX0RFQlVHX18mJmNvbnNvbGUubG9nKCAnZGF0YSBBSkFYJywgZGF0YSApO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgZGF0YSA9IEpTT04ucGFyc2UoZGF0YSk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBiZWZvcmUgY2hlY2sgcmVzcG9uc2UgY2FsbGJhY2tcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKHByb3BzLmJlZm9yZUNoa1Jlc3BvbnNlKSBkYXRhID0gcHJvcHMuYmVmb3JlQ2hrUmVzcG9uc2UoZGF0YSk7XHJcblxyXG5cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHVzZXIgZGVmaW5lZCBlcnJvclxyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiggZGF0YVtwcm9wcy5yZXNwQ29kZU5hbWVdID4gMTAwICYmIGRhdGFbcHJvcHMucmVzcENvZGVOYW1lXSA8IDIwMCApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yID0gLWRhdGFbcHJvcHMucmVzcENvZGVOYW1lXTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihtZXNzYWdlKTtcclxuXHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBjYXRjaGVkIHNlcnZlciBlcnJvciwgY29tbW9uIGVycm9yXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZGF0YVtwcm9wcy5yZXNwQ29kZU5hbWVdID09IDEwMCApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yID0gLTEwMDtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIHRocm93IG5ldyBFcnJvcihtZXNzYWdlKTtcclxuXHJcblxyXG4gICAgICAgICAgICAgICAgICAgICAgICAvLyBzdWNjZXNzXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiggZGF0YVtwcm9wcy5yZXNwQ29kZU5hbWVdID09IDIwMCApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yID0gMTAwO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgdGhyb3cgbmV3IEVycm9yKFwiXCIpO1xyXG5cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC8vIHVuZGVmaW5kZWQgZXJyb3JcclxuICAgICAgICAgICAgICAgICAgICAgICAgfSBlbHNlXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIGVycm9yID0gLTEwMDA7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICB0aHJvdyBuZXcgRXJyb3IobWVzc2FnZSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH0gLy8gZW5kaWZcclxuXHJcblxyXG4gICAgICAgICAgICAgICAgICAgIH0gY2F0Y2ggKGUpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgZXJyb3IgPCAwICYmIGNvbnNvbGUud2FybiggJ0UnLCBlcnJvciApO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBzd2l0Y2goIGVycm9yIClcclxuICAgICAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAtMTAwOiA7IC8vIHNvbWUgYmFja2VuZCBub3QgY29udHJvbGxlZCBlcnJvclxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgY2FzZSAtMTAwMCA6IDsgYnJlYWs7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICBkZWZhdWx0OlxyXG4vLyAwfHxjb25zb2xlLmxvZyggJ2Vycm9yLCB2YWwuY29kZScsIGVycm9yLCBzZWxmLm9wdGlvbnMucmVzcENvZGVzICk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgZm9yKCBsZXQgdmFsIG9mIHByb3BzLnJlc3BDb2RlcyApXHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBpZiggZXJyb3IgPT0gdmFsLmNvZGUgKVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBtZXNzYWdlID0gdmFsLm1lc3NhZ2UgfHwgdmFsLmNhbGxiYWNrJiZ2YWwuY2FsbGJhY2soZGF0YSk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICBicmVhaztcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgfSAvLyBlbmRpZlxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIH0gLy8gZW5kZm9yXHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICB9XHJcblxyXG5cclxuICAgICAgICAgICAgICAgICAgICBlcnJvciA8IDAgPyByZWplY3Qoe2NvZGU6IGVycm9yLCBtZXNzYWdlLCBkYXRhfSkgOiByZXNvbHZlKHtjb2RlOiBlcnJvciwgbWVzc2FnZSwgZGF0YX0pO1xyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbigpIHtcclxuICAgICAgICAgICAgICAgICAgICByZWplY3Qoe2NvZGU6IC0xMDAyLCBtZXNzYWdlfSk7XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgLy8gRm9ybSBkYXRhXHJcbiAgICAgICAgICAgICAgICBkYXRhOiBwcm9wcy5mb3JtRGF0YSB8fCBuZXcgRm9ybURhdGEoKSxcclxuICAgICAgICAgICAgICAgIC8vIE9wdGlvbnMgdG8gdGVsbCBqUXVlcnkgbm90IHRvIHByb2Nlc3MgZGF0YSBvciB3b3JyeSBhYm91dCB0aGUgY29udGVudC10eXBlXHJcbiAgICAgICAgICAgICAgICBjYWNoZTogZmFsc2UsXHJcbiAgICAgICAgICAgICAgICAvL2NvbnRlbnRUeXBlOiBmYWxzZSxcclxuICAgICAgICAgICAgICAgIC8vcHJvY2Vzc0RhdGE6IGZhbHNlXHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgICAgICAvLyAuYWx3YXlzKGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgLy8gICAgIC8vIGZvcm0uZmluZCgnLmxvYWRpbmctaWNvJykuZmFkZU91dCgyMDApO1xyXG4gICAgICAgICAgICAvLyB9KVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICByZXR1cm4gcHJvbWlzZTtcclxuICAgIH1cclxufSIsIi8qKlxyXG4gKiBDcmVhdGVkIGJ5IHRpYW5uYSBvbiAxMS4wNi4xNy5cclxuICovXHJcblxyXG5jbGFzcyBMb2FkaW5nXHJcbntcclxuICAgIC8qKkBwcml2YXRlKi8gc3RhdGljIGxvYWRlclNlbGVjdG9yID0gXCIjbG9hZGluZy1tYXNrXCI7XHJcblxyXG5cclxuICAgIC8qKkBwdWJsaWMqLyBzdGF0aWMgc2hvdygpXHJcbiAgICB7XHJcbiAgICAgICAgJGoodGhpcy5sb2FkZXJTZWxlY3RvcikuZmFkZUluKDIwMCk7XHJcbiAgICB9XHJcblxyXG5cclxuICAgIC8qKkBwdWJsaWMqLyBzdGF0aWMgaGlkZSgpXHJcbiAgICB7XHJcbiAgICAgICAgJGoodGhpcy5sb2FkZXJTZWxlY3RvcikuZmFkZU91dCgyMDApO1xyXG4gICAgfVxyXG59Il19
