@extends('layouts.masterlayout')
@inject('carbon', 'Carbon\Carbon')
<style>
    .parent {
        display: grid;
        grid-template-columns: repeat(36, 1fr);
        grid-template-rows: repeat(7, 1fr);
        grid-column-gap: 0px;
        grid-row-gap: 10px;
    }
    .container{
        max-width: 1890px !important;
    }
    button.taken {
        border: 2px solid #e90000;
        background: #ffc1c1;
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    button.available {
        border: 2px solid #057f05;
        background: #76ff76;
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    button.no_lapida {
        border: 2px solid rgb(0, 0, 0);
        background:rgb(143, 143, 143);
        width: 50px;
        font-size: 11px;
        height: 100px;
    }
    .modal-title, .form-control{
        color:black !important;
    }
    .a#tabs-icons-text-2-tab, .a#tabs-icons-text-3-tab{
        color: black !important;
    }
    .nav-link {
        color: black !important;
    }
    .nav-link.active {
        color: white !important;
    }
</style>
@section('content')
<div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-10 d-flex justify-content-center">
            <h3 style="margin-left:20%;">LEVEL 2 APARTMENT</h3>
        </div>
        <div class="col">
        <span class="badge badge-secondary">LEGEND: </span>
        <span class="badge badge-success">Available</span>
        <span class="badge badge-danger">Taken</span>
        <span class="badge badge-dark">No Lapida</span>
        </div>

    </div>
    <div class="parent">
        
        <!-- ROW 7 -->
        <button class="available"><div>R7_1</div></button>
        <button class="available"><div>R7_2</div></button>
        <button class="available"><div>R7_3</div></button>
        <button class="available"><div>R7_4</div></button>
        <button class="available"><div>R7_5</div></button>
        <button class="available"><div>R7_6</div></button>
        <button class="available"><div>R7_7</div></button>
        <button class="available"><div>R7_8</div></button>
        <button class="available"><div>R7_9</div></button>
        <button class="available"><div>R7_10</div></button>
        <button class="available"><div>R7_11</div></button>
        <button class="available"><div>R7_12</div></button>
        <button class="available"><div>R7_13</div></button>
        <button class="available"><div>R7_14</div></button>
        <button class="available"><div>R7_15</div></button>
        <button class="available"><div>R7_16</div></button>
        <button class="available"><div>R7_17</div></button>
        <button class="available"><div>R7_18</div></button>
        <button class="available"><div>R7_19</div></button>
        <button class="available"><div>R7_20</div></button>
        <button class="available"><div>R7_21</div></button>
        <button class="available"><div>R7_22</div></button>
        <button class="available"><div>R7_23</div></button>
        <button class="available"><div>R7_24</div></button>
        <button class="available"><div>R7_25</div></button>
        <button class="available"><div>R7_26</div></button>
        <button class="available"><div>R7_27</div></button>
        <button class="available"><div>R7_28</div></button>
        <button class="available"><div>R7_29</div></button>
        <button class="available"><div>R7_30</div></button>
        <button class="available"><div>R7_31</div></button>
        <button class="available"><div>R7_32</div></button>
        <button class="available"><div>R7_33</div></button>
        <button class="available"><div>R7_34</div></button>
        <button class="available"><div>R7_35</div></button>
        <button class="available"><div>R7_36</div></button>

        <!-- ROW 6 -->
        <button class="available"><div>R6_1</div></button>
        <button class="available"><div>R6_2</div></button>
        <button class="available"><div>R6_3</div></button>
        <button class="available"><div>R6_4</div></button>
        <button class="available"><div>R6_5</div></button>
        <button class="available"><div>R6_6</div></button>
        <button class="available"><div>R6_7</div></button>
        <button class="available"><div>R6_8</div></button>
        <button class="available"><div>R6_9</div></button>
        <button class="available"><div>R6_10</div></button>
        <button class="available"><div>R6_11</div></button>
        <button class="available"><div>R6_12</div></button>
        <button class="available"><div>R6_13</div></button>
        <button class="available"><div>R6_14</div></button>
        <button class="available"><div>R6_15</div></button>
        <button class="available"><div>R6_16</div></button>
        <button class="available"><div>R6_17</div></button>
        <button class="available"><div>R6_18</div></button>
        <button class="available"><div>R6_19</div></button>
        <button class="available"><div>R6_20</div></button>
        <button class="available"><div>R6_21</div></button>
        <button class="available"><div>R6_22</div></button>
        <button class="available"><div>R6_23</div></button>
        <button class="available"><div>R6_24</div></button>
        <button class="available"><div>R6_25</div></button>
        <button class="available"><div>R6_26</div></button>
        <button class="available"><div>R6_27</div></button>
        <button class="available"><div>R6_28</div></button>
        <button class="available"><div>R6_29</div></button>
        <button class="available"><div>R6_30</div></button>
        <button class="available"><div>R6_31</div></button>
        <button class="available"><div>R6_32</div></button>
        <button class="available"><div>R6_33</div></button>
        <button class="available"><div>R6_34</div></button>
        <button class="available"><div>R6_35</div></button>
        <button class="available"><div>R6_36</div></button>

        <!-- ROW 5 -->
        <button class="available"><div>R5_1</div></button>
        <button class="available"><div>R5_2</div></button>
        <button class="available"><div>R5_3</div></button>
        <button class="available"><div>R5_4</div></button>
        <button class="available"><div>R5_5</div></button>
        <button class="available"><div>R5_6</div></button>
        <button class="available"><div>R5_7</div></button>
        <button class="available"><div>R5_8</div></button>
        <button class="available"><div>R5_9</div></button>
        <button class="available"><div>R5_10</div></button>
        <button class="available"><div>R5_11</div></button>
        <button class="available"><div>R5_12</div></button>
        <button class="available"><div>R5_13</div></button>
        <button class="available"><div>R5_14</div></button>
        <button class="available"><div>R5_15</div></button>
        <button class="available"><div>R5_16</div></button>
        <button class="available"><div>R5_17</div></button>
        <button class="available"><div>R5_18</div></button>
        <button class="available"><div>R5_19</div></button>
        <button class="available"><div>R5_20</div></button>
        <button class="available"><div>R5_21</div></button>
        <button class="available"><div>R5_22</div></button>
        <button class="available"><div>R5_23</div></button>
        <button class="available"><div>R5_24</div></button>
        <button class="available"><div>R5_25</div></button>
        <button class="available"><div>R5_26</div></button>
        <button class="available"><div>R5_27</div></button>
        <button class="available"><div>R5_28</div></button>
        <button class="available"><div>R5_29</div></button>
        <button class="available"><div>R5_30</div></button>
        <button class="available"><div>R5_31</div></button>
        <button class="available"><div>R5_32</div></button>
        <button class="available"><div>R5_33</div></button>
        <button class="available"><div>R5_34</div></button>
        <button class="available"><div>R5_35</div></button>
        <button class="available"><div>R5_36</div></button>

        <!-- ROW 4 -->
        <button class="available"><div>R4_1</div></button>
        <button class="available"><div>R4_2</div></button>
        <button class="available"><div>R4_3</div></button>
        <button class="available"><div>R4_4</div></button>
        <button class="available"><div>R4_5</div></button>
        <button class="available"><div>R4_6</div></button>
        <button class="available"><div>R4_7</div></button>
        <button class="available"><div>R4_8</div></button>
        <button class="available"><div>R4_9</div></button>
        <button class="available"><div>R4_10</div></button>
        <button class="available"><div>R4_11</div></button>
        <button class="available"><div>R4_12</div></button>
        <button class="available"><div>R4_13</div></button>
        <button class="available"><div>R4_14</div></button>
        <button class="available"><div>R4_15</div></button>
        <button class="available"><div>R4_16</div></button>
        <button class="available"><div>R4_17</div></button>
        <button class="available"><div>R4_18</div></button>
        <button class="available"><div>R4_19</div></button>
        <button class="available"><div>R4_20</div></button>
        <button class="available"><div>R4_21</div></button>
        <button class="available"><div>R4_22</div></button>
        <button class="available"><div>R4_23</div></button>
        <button class="available"><div>R4_24</div></button>
        <button class="available"><div>R4_25</div></button>
        <button class="available"><div>R4_26</div></button>
        <button class="available"><div>R4_27</div></button>
        <button class="available"><div>R4_28</div></button>
        <button class="available"><div>R4_29</div></button>
        <button class="available"><div>R4_30</div></button>
        <button class="available"><div>R4_31</div></button>
        <button class="available"><div>R4_32</div></button>
        <button class="available"><div>R4_33</div></button>
        <button class="available"><div>R4_34</div></button>
        <button class="available"><div>R4_35</div></button>
        <button class="available"><div>R4_36</div></button>

        <!-- ROW 3 -->
        <button class="available"><div>R3_1</div></button>
        <button class="available"><div>R3_2</div></button>
        <button class="available"><div>R3_3</div></button>
        <button class="available"><div>R3_4</div></button>
        <button class="available"><div>R3_5</div></button>
        <button class="available"><div>R3_6</div></button>
        <button class="available"><div>R3_7</div></button>
        <button class="available"><div>R3_8</div></button>
        <button class="available"><div>R3_9</div></button>
        <button class="available"><div>R3_10</div></button>
        <button class="available"><div>R3_11</div></button>
        <button class="available"><div>R3_12</div></button>
        <button class="available"><div>R3_13</div></button>
        <button class="available"><div>R3_14</div></button>
        <button class="available"><div>R3_15</div></button>
        <button class="available"><div>R3_16</div></button>
        <button class="available"><div>R3_17</div></button>
        <button class="available"><div>R3_18</div></button>
        <button class="available"><div>R3_19</div></button>
        <button class="available"><div>R3_20</div></button>
        <button class="available"><div>R3_21</div></button>
        <button class="available"><div>R3_22</div></button>
        <button class="available"><div>R3_23</div></button>
        <button class="available"><div>R3_24</div></button>
        <button class="available"><div>R3_25</div></button>
        <button class="available"><div>R3_26</div></button>
        <button class="available"><div>R3_27</div></button>
        <button class="available"><div>R3_28</div></button>
        <button class="available"><div>R3_29</div></button>
        <button class="available"><div>R3_30</div></button>
        <button class="available"><div>R3_31</div></button>
        <button class="available"><div>R3_32</div></button>
        <button class="available"><div>R3_33</div></button>
        <button class="available"><div>R3_34</div></button>
        <button class="available"><div>R3_35</div></button>
        <button class="available"><div>R3_36</div></button>

        <!-- ROW 2 -->
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_1</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_2</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_3</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_4</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_5</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_6</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_7</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_8</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_9</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_10</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_11</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_12</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_13</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_14</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_15</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_16</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_17</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_18</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_19</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_20</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_21</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_22</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_23</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_24</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_25</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_26</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_27</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_28</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_29</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_30</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_31</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_32</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_33</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_34</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_35</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R2_36</div></button>

        <!-- ROW 1 -->
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_1</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_2</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_3</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_4</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_5</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_6</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_7</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_8</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_9</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_10</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_11</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_12</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_13</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_14</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_15</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_16</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_17</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_18</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_19</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_20</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_21</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_22</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_23</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_24</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_25</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_26</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_27</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_28</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_29</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_30</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_31</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_32</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_33</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_34</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_35</div></button>
        <button class="available" data-toggle="modal" data-target="#detailModal"><div>R1_36</div></button>
    </div>
</div>

<!-- DETAILS Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">[Column Number] Information</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="nav-wrapper">
                    <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true"><i class="ni ni-cloud-upload-96 mr-2"></i>1st Slot</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-bell-55 mr-2"></i>2nd Slot</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-3-tab" data-toggle="tab" href="#tabs-icons-text-3" role="tab" aria-controls="tabs-icons-text-3" aria-selected="false"><i class="ni ni-calendar-grid-58 mr-2"></i>3rd Slot</a>
                        </li>
                    </ul>
                </div>
                <div class="card shadow">
                    <div class="card-body">
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="tabs-icons-text-3" role="tabpanel" aria-labelledby="tabs-icons-text-3-tab">
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Location</label>
                                            <input class="form-control" type="text" placeholder="location" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Name of Deceased</label>
                                            <input class="form-control" type="text" placeholder="name of deceased" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Sex</label>
                                            <input class="form-control" type="text" placeholder="sex" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Birth</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Date of Death</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Contact Person</label>
                                            <input class="form-control" type="text" placeholder="contact person" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-group">
                                            <label for="example-text-input" class="form-control-label">Year of Renewal</label>
                                            <input class="form-control" type="text" placeholder="" id="example-text-input" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection