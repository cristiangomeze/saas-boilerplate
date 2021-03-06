let dropin = require('braintree-web-drop-in');

let updatePaymentMethod = document.querySelector('#submit-button');

dropin.create({
    authorization: document.getElementById("client-token").value,
    container: '#payment-container',
    card: { cardholderName: { required: true} }
  }, function (createErr, instance) {
    updatePaymentMethod.addEventListener('click', function () {
      instance.requestPaymentMethod(function (requestPaymentMethodErr, payload) {
        axios.post('/payment-method', { payload:payload })
          .then(() =>  Livewire.emit('addedPaymentMethod'))
      });
    });
  });