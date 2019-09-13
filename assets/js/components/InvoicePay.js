class InvoicePay {
  constructor() {
    //  Create validator handler
    this.$form = $("#js-invoice-main-form");
    this.$btn = $("#js-invoice-pay-now");
    this.$form.data("validators", []);
    this.$form.data("validated", false);

    // --------------------------------------------------------------------------

    //  If there is only one driver enabled - hide the radios
    if ($(".js-invoice-driver-select input").length === 1) {
      $("#js-invoice-main-form-payment-drivers").hide();
    }

    //  Bind to driver selection
    $(".js-invoice-driver-select input")
      .on("click", e => {
        let $el = $(e.currentTarget);

        //  Highlight selection
        $(".js-invoice-driver-select.active").removeClass("active");

        $el.closest(".js-invoice-driver-select").addClass("active");

        //  Show payment fields
        $(".js-invoice-panel-payment-details").addClass("hidden");

        $(
          '.js-invoice-panel-payment-details[data-driver="' +
            $el.data("driver") +
            '"]'
        ).removeClass("hidden");

        //  Update button
        let btnString = $el.data("is-redirect") ? "Continue" : "Pay Now";

        this.$btn.removeClass("btn--warning btn--disabled").text(btnString);
      })
      .filter(":checked")
      .trigger("click");

    //  Card input formatting
    $(".js-invoice-cc-num").payment("formatCardNumber");
    $(".js-invoice-cc-exp").payment("formatCardExpiry");
    $(".js-invoice-cc-cvc").payment("formatCardCVC");

    //  CVC Card type formatting
    $(".js-invoice-cc-num")
      .on("keyup", e => {
        let $el = $(e.currentTarget);
        let cardNum = $.trim($el.val());
        let cardType = $.payment.cardType(cardNum);
        let cardCvc = $(".js-invoice-cc-cvc");

        cardCvc.removeClass("amex other");
        $el.closest(".form__group").removeClass("has-error");

        if (cardNum.length > 0) {
          switch (cardType) {
            case "amex":
              cardCvc.addClass("amex");
              break;

            default:
              cardCvc.addClass("other");
              break;
          }
        }
      })
      .trigger("keyup");

    //  Validation
    this.$form.on("submit", e => {
      //  If validated, let the form fly
      if (this.$form.data("validated")) {
        return true;
      } else {
        e.preventDefault();
        this.$btn.addClass("btn--working").prop("disabled", true);
      }

      // --------------------------------------------------------------------------

      let deferred = new $.Deferred();
      let isValid = true;

      // --------------------------------------------------------------------------

      //  Set up the resolution actions
      deferred
        .done(() => {
          this.$form.data("validated", true).submit();
        })
        .fail(message => {
          $("#js-error")
            .html(message)
            .removeClass("hidden");

          $("#js-invoice").addClass("shake");

          setTimeout(() => {
            $("#js-invoice").removeClass("shake");
          }, 500);

          this.$btn.removeClass("btn--working").prop("disabled", false);
        });

      // --------------------------------------------------------------------------

      try {
        //  Hide errors
        $("#js-error").addClass("hidden");

        //  Ensure a driver selected
        let $selectedDriver = $(".js-invoice-driver-select input:checked");
        if ($selectedDriver.length === 0) {
          deferred.reject("Please select a payment option");
          return;
        }

        //  Validate any fields
        $(".js-invoice-panel-payment-details:not(.hidden) :input").each(
          (index, element) => {
            let $el = $(element);
            let $group = $el.closest(".form__group");
            let val = $.trim($el.val());

            $group.removeClass("has-error");

            if ($el.data("is-required") && val.length === 0) {
              isValid = false;
              $group.addClass("has-error");
            }

            if ($el.data("cc-num") && !$.payment.validateCardNumber(val)) {
              isValid = false;
              $group.addClass("has-error");
            }

            if ($el.data("cc-exp")) {
              let expObj = $.payment.cardExpiryVal(val);
              if (!$.payment.validateCardExpiry(expObj.month, expObj.year)) {
                isValid = false;
                $group.addClass("has-error");
              }
            }

            if ($el.data("cc-cvc") && !$.payment.validateCardCVC(val)) {
              isValid = false;
              $group.addClass("has-error");
            }

            if (!isValid) {
              $("#js-error")
                .html("Please check all fields")
                .removeClass("hidden");
            }
          }
        );

        if (!isValid) {
          deferred.reject("Please correct highlighted fields");
          return;
        }

        //  Execute any custom validator
        let validators = this.$form.data("validators");
        let validator = null;
        for (let i = 0; i < validators.length; i++) {
          if (validators[i].slug === $selectedDriver.val()) {
            validator = validators[i].instance;
          }
        }

        if (validator) {
          validator.validate(deferred);
        } else {
          deferred.resolve();
        }
      } catch (error) {
        deferred.reject(error.message);
      }
    });
  }
}

export default InvoicePay;
