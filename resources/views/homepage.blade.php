@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .container {
        max-width: 1830px !important;
    }
</style>
 @section('content')

 <div class="container mt-5">
  <div class="row mb-3 d-flex justify-content-end">
      <a href="{{URL('/burial_application_form')}}"><button class="btn btn-danger mr-2">Apply for Burial Permit</button></a>
      <a href=""><button class="btn btn-info">See Database</button></a>
  </div>
  <div class="row">
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
                <h5 class="card-title text-uppercase text-muted mb-0">Total</h5>
                <span class="h2 font-weight-bold mb-0">350,897</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                <i class="ni ni-active-40"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
                <h5 class="card-title text-uppercase text-muted mb-0">New users</h5>
                <span class="h2 font-weight-bold mb-0">2,356</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                <i class="ni ni-chart-pie-35"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
                <h5 class="card-title text-uppercase text-muted mb-0">For Renewal</h5>
                <span class="h2 font-weight-bold mb-0">924</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-green text-white rounded-circle shadow">
                <i class="ni ni-money-coins"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-xl-3 col-md-6">
      <div class="card card-stats">
        <div class="card-body">
          <div class="row">
            <div class="col">
                <h5 class="card-title text-uppercase text-muted mb-0">For Exhume</h5>
                <span class="h2 font-weight-bold mb-0">49,65%</span>
            </div>
            <div class="col-auto">
              <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                <i class="ni ni-chart-bar-32"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-xl-4">
      <div class="card">
        <div class="card-header">
            <h5 class="h3 mb-0">Slots on Restos per Level</h5>
        </div>
        <div class="card-body">
          <ul class="list-group list-group-flush list my--3">
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 1</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-orange" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <a href="{{URL('/Level_1')}}"><button type="button" class="btn btn-sm btn-success">View</button></a>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 2</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                   <a href="{{URL('/Level_2')}}"><button type="button" class="btn btn-sm btn-success">View</button></a>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 3</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-primar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <button type="button" class="btn btn-sm btn-success">View</button>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 4</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-yellow" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <button type="button" class="btn btn-sm btn-success">View</button>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 5</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-default" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <button type="button" class="btn btn-sm btn-success">View</button>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 6</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <button type="button" class="btn btn-sm btn-success">View</button>
                </div>
              </div>
            </li> 
            <li class="list-group-item px-0">
              <div class="row align-items-center">
                <div class="col">
                  <h5>Level 7</h5>
                </div>
                <div class="col-8">
                  <div class="progress progress-xs mb-0">
                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;"></div>
                  </div>
                </div>
                <div class="col">
                  <button type="button" class="btn btn-sm btn-success">View</button>
                </div>
              </div>
            </li> 
          </ul>
        </div>
      </div>
    </div>
    <div class="col-xl-8">
      <div class="card">
        <div class="card-header border-0">
          <div class="row align-items-center">
            <div class="col">
              <h3 class="mb-0">For Renewal</h3>
            </div>
            <div class="col text-right">
              <a href="#!" class="btn btn-sm btn-primary">See all</a>
              <a href="#!" class="btn btn-sm btn-default">Request for Renewal</a>
            </div>
          </div>
        </div>
        <div class="table-responsive">
          <!-- Projects table -->
          <table class="table align-items-center table-flush">
            <thead class="thead-light">
              <tr>
                <th scope="col">Applicant's Name</th>
                <th scope="col">Relationship to the Deceased</th>
                <th scope="col">Name of the Deceased</th>
                <th scope="col">Burried at</th>
                <th scope="col">Renewal Period</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">Virginia F. Villarmino</th>
                <td>Sister</td>
                <td>Antonio A. Flores</td>
                <td>Level 2, 3rd Floor</td>
                <td>2025-2030</td>
              </tr>
              <tr>
                <th scope="row">Virginia F. Villarmino</th>
                <td>Sister</td>
                <td>Antonio A. Flores</td>
                <td>Level 2, 3rd Floor</td>
                <td>2025-2030</td>
              </tr>
              <tr>
                <th scope="row">Virginia F. Villarmino</th>
                <td>Sister</td>
                <td>Antonio A. Flores</td>
                <td>Level 2, 3rd Floor</td>
                <td>2025-2030</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

 @endsection