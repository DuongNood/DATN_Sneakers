@extends('admin.layouts.master')
@section('content')
    <!-- Start Content-->
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">{{$title}}</h4>
            </div>
        </div>

        <!-- General Form -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <form>
                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Product code</label>
                                        <input type="text" id="simpleinput" class="form-control" name="product_code">
                                    </div>
                                    <div class="mb-3">
                                        <label for="example-email" class="form-label">Product name</label>
                                        <input type="email" id="example-email" name="example-email" class="form-control" name="product_name">
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-password" class="form-label">Password</label>
                                        <input type="password" id="example-password" class="form-control" value="password">
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-palaceholder" class="form-label">Placeholder</label>
                                        <input type="text" id="example-palaceholder" class="form-control"
                                            placeholder="placeholder">
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-textarea" class="form-label">Text area</label>
                                        <textarea class="form-control" id="example-textarea" rows="5"
                                            spellcheck="false"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-disable" class="form-label">Readonly</label>
                                        <input class="form-control" type="text" value="Readonly input here..."
                                            aria-label="readonly input example" readonly>
                                    </div>

                                    <div>
                                        <label for="example-disable" class="form-label">Disabled</label>
                                        <input type="text" class="form-control" id="example-disable" disabled=""
                                            value="Disabled value">
                                    </div>

                                </form>
                            </div>

                            <div class="col-lg-6">
                                <form>
                                    <div class="mb-3">
                                        <label for="example-select" class="form-label">Input Select</label>
                                        <select class="form-select" id="example-select">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="simpleinput" class="form-label">Disabled</label>
                                        <select id="simpleinput" class="form-select" aria-label="Default select example">
                                            <option selected>Select Menu</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="exampleColorInput" class="form-label">Color picker</label>
                                        <input type="color" id="exampleColorInput" value="#563d7c" title="Choose your color"
                                            class="form-control form-control-color">
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-date" class="form-label">Date</label>
                                        <input type="date" id="example-date" class="form-control" name="date">
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-multiselect" class="form-label">Multiple Select</label>
                                        <select id="example-multiselect" multiple class="form-control">
                                            <option>1</option>
                                            <option>2</option>
                                            <option>3</option>
                                            <option>4</option>
                                            <option>5</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="example-custom" class="form-label">Button With Dropdowns</label>
                                        <div class="input-group mb-3">
                                            <input type="text" class="form-control" id="example-custom"
                                                aria-label="Text input with dropdown button">
                                            <button class="btn btn-primary dropdown-toggle" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">Dropdown</button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="#">Action</a></li>
                                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li><a class="dropdown-item" href="#">Separated link</a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="exampleDataList" class="form-label">Datalist example</label>
                                        <input class="form-control" list="datalistOptions" id="exampleDataList"
                                            placeholder="Type to search...">
                                        <datalist id="datalistOptions">
                                            <option value="San Francisco">
                                            <option value="New York">
                                            <option value="Seattle">
                                            <option value="Los Angeles">
                                            <option value="Chicago">
                                        </datalist>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>       
    </div> <!-- container-fluid -->
@endsection