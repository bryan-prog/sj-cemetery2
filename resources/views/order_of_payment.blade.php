@extends('layouts.masterlayout')
<style>
    .custom-control-label, td, .form-control-label {
        color: black !important;
    }
    th{
        font-size:14px !important;
    }
    #total { background:#f8f9fa; }
</style>
@section('content')

<div class="container mt-4">
  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center px-2">
      <h3 class="mb-0">
        <img src="https://img.icons8.com/bubbles/50/cash-in-hand.png" alt="goodnotes"/>
        Please select the corresponding fees that apply by ticking the appropriate checkboxes below.
      </h3>
      <a href="{{ url('/Homepage') }}" class="btn btn-default btn-sm">Back to Home</a>
    </div>

    <div class="card-body pb-5 pt-2">
      <div class="row" style="align-items: center;">
        <div class="col-12">
            <div class="row">
                <div class="table-responsive pb-4">
                    <table class="table">
                        <thead class="bg-default">
                            <tr>
                                <th class="text-white">Fee</th>
                                <th class="text-white text-center">Account Code</th>
                                <th class="text-white text-center">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck1" data-amount="500">
                                        <label class="custom-control-label" for="customCheck1">Burial Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40201010-03-01</td>
                                <td class="text-center">500.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck2" data-amount="100">
                                        <label class="custom-control-label" for="customCheck2">Review of Death Certificate</label>
                                    </div>
                                </td>
                                <td class="text-center"></td>
                                <td class="text-center">100.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck3" data-amount="200">
                                        <label class="custom-control-label" for="customCheck3">Transfer Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40201040-04-20</td>
                                <td class="text-center">200.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck4" data-amount="300">
                                        <label class="custom-control-label" for="customCheck4">Exhumation Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40201040-04-51</td>
                                <td class="text-center">300.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck5" data-amount="2500">
                                        <label class="custom-control-label" for="customCheck5">Construction Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-04</td>
                                <td class="text-center">2,500.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck6" data-amount="100">
                                        <label class="custom-control-label" for="customCheck6">Certification Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40202140-17-01</td>
                                <td class="text-center">100.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck7" data-amount="100">
                                        <label class="custom-control-label" for="customCheck7">Electrical Fee</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-07</td>
                                <td class="text-center">100.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="customCheck8" data-amount="50">
                                        <label class="custom-control-label" for="customCheck8">Penalty - Cemetery</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-03</td>
                                <td class="text-center">50.00</td>
                            </tr>
                            <tr>
                                <td colspan="3">Note: Select only 1 Renewal:</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio1" name="radio_Renewal" data-amount="300">
                                        <label class="custom-control-label" for="customRadio1">Renewal - Apartment / Lot Type</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-01</td>
                                <td class="text-center">300.00 per Lot/Year</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio2" name="radio_Renewal" data-amount="100">
                                        <label class="custom-control-label" for="customRadio2">Renewal - Restos</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-14</td>
                                <td class="text-center">100.00 per Year</td>
                            </tr>
                            <tr>
                                <td colspan="3">Note: Select only 1 Type of Burial:</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio3" name="radio_Type" data-amount="6000">
                                        <label class="custom-control-label" for="customRadio3">Type of Burial - Old Niche Category A (Top/Side)</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-08</td>
                                <td class="text-center">6,000.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio4" name="radio_Type" data-amount="3000">
                                        <label class="custom-control-label" for="customRadio4">Type of Burial - Type of Burial - Old Niche Category B (Front)</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-09</td>
                                <td class="text-center">3,000.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio5" name="radio_Type" data-amount="3000">
                                        <label class="custom-control-label" for="customRadio5">Type of Burial - Apt/Restos Category A (Front)</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-10</td>
                                <td class="text-center">3,000.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio6" name="radio_Type" data-amount="2000">
                                        <label class="custom-control-label" for="customRadio6">Type of Burial - Apt/Restos Category B</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-11</td>
                                <td class="text-center">2,000.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio7" name="radio_Type" data-amount="8000">
                                        <label class="custom-control-label" for="customRadio7">Type of Burial - New Niche / Lot Category A</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-12</td>
                                <td class="text-center">8,000.00</td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input" id="customRadio8" name="radio_Type" data-amount="5000">
                                        <label class="custom-control-label" for="customRadio8">Type of Burial - New Niche / Lot Category B</label>
                                    </div>
                                </td>
                                <td class="text-center">40202160-01-13</td>
                                <td class="text-center">5,000.00</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="row">
                <div class="col"></div>
                <div class="col"></div>
                <div class="col">
                    <div class="form-group inline">
                        <label for="total" class="form-control-label ">TOTAL : </label>
                        <input class="form-control" type="text" placeholder="0.00" id="total" readonly>
                    </div>
                </div>
            </div>
            <button id="submit" class="btn btn-primary btn-lg btn-block mt-4">
              <i class="fa fa-arrow-right mr-2"></i> Submit
            </button>
        </div>
      </div>

    </div>
  </div>
</div>


<div class="modal fade" id="oopSuccessModal" tabindex="-1" role="dialog" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header border-0">
        <h5 class="modal-title d-flex align-items-center">
          <img class="mr-2" src="https://img.icons8.com/emoji/28/check-mark-emoji.png" alt=""/>
          Order of Payment Submitted
        </h5>
      </div>

      <div class="modal-body pt-0">
        <p class="mb-2" style="color:black !important;">
          Your selections have been recorded.
        </p>
        <p class="mb-0" style="color:black !important;">
          <strong>Total Amount:</strong> <span id="oopSuccessTotal">0.00</span>
        </p>
      </div>

      <div class="modal-footer border-0">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="oopSuccessGoBack">
          Return to Application
        </button>
      </div>

    </div>
  </div>
</div>


<script>
  (function () {

    function getParam(name) {
      const qs = new URLSearchParams(window.location.search);
      return qs.get(name);
    }


    function applyPreselection() {
      const selectedParam = getParam('selected');
      if (!selectedParam) return;
      selectedParam.split(',').map(s => s.trim()).forEach(id => {
        if (!id) return;
        const el = document.getElementById(id);
        if (el && (el.type === 'checkbox' || el.type === 'radio')) {
          el.checked = true;
        }
      });
    }

    function computeTotal() {
      let sum = 0;

      document.querySelectorAll('input[type="checkbox"]:checked').forEach(el => {
        const amt = Number(el.dataset.amount || 0);
        if (!isNaN(amt)) sum += amt;
      });

      const renewal = document.querySelector('input[name="radio_Renewal"]:checked');
      if (renewal) {
        const amt = Number(renewal.dataset.amount || 0);
        if (!isNaN(amt)) sum += amt;
      }

      const burialType = document.querySelector('input[name="radio_Type"]:checked');
      if (burialType) {
        const amt = Number(burialType.dataset.amount || 0);
        if (!isNaN(amt)) sum += amt;
      }

      return sum;
    }

    function updateDisplay() {
      const total = computeTotal();
      document.getElementById('total').value = total.toFixed(2);
    }


    function collectSelectedIds() {
      const ids = [];
      document.querySelectorAll('input[type="checkbox"]:checked, input[type="radio"]:checked').forEach(el => {
        if (el.id) ids.push(el.id);
      });
      return ids.join(',');
    }


    applyPreselection();
    updateDisplay();


    document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(el => {
      el.addEventListener('change', updateDisplay);
    });

    var pendingTotal = 0;
    var returnUrl    = `{{ route('burial_application_form') }}`;


    document.getElementById('submit').addEventListener('click', function (e) {
      e.preventDefault();
      pendingTotal = computeTotal();

      const qs = new URLSearchParams(window.location.search);
      returnUrl = qs.get('return_to') || returnUrl;

      document.getElementById('oopSuccessTotal').textContent = pendingTotal.toFixed(2);
      $('#oopSuccessModal').modal('show');
    });


    document.getElementById('oopSuccessGoBack').addEventListener('click', function () {
      const selectedIds = collectSelectedIds();
      const sep = returnUrl.includes('?') ? '&' : '?';
      let target = returnUrl + sep + 'oop_total=' + encodeURIComponent(pendingTotal.toFixed(2));
      if (selectedIds) {
        target += '&oop_sel=' + encodeURIComponent(selectedIds);
      }
      window.location.href = target;
    });
  })();
</script>
@endsection
