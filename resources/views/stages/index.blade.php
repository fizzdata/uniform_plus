@extends('app')

@section('content')
<div class="Polaris-Page">
    <div class="Polaris-Page__Header">
        <div class="Polaris-Page__Title">
            <h1 class="Polaris-Header-Title">Order Stages</h1>
        </div>
        <div>
            <button class="Polaris-Button Polaris-Button--primary" onclick="openAddStageModal()">
                <span class="Polaris-Button__Content">Add Stage</span>
            </button>
        </div>
    </div>
    
    <div class="Polaris-Page__Content">
        <div class="Polaris-Card">
            <div class="Polaris-Card__Section">
                <div class="Polaris-DataTable">
                    <div class="Polaris-DataTable__ScrollContainer">
                        <table class="Polaris-DataTable__Table">
                            <thead>
                                <tr>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Position</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Name</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Color</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Description</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stages as $stage)
                                <tr class="Polaris-DataTable__TableRow">
                                    <td class="Polaris-DataTable__Cell">{{ $stage->position }}</td>
                                    <td class="Polaris-DataTable__Cell">{{ $stage->name }}</td>
                                    <td class="Polaris-DataTable__Cell">
                                        <div style="background-color: {{ $stage->color }}; width: 24px; height: 24px; border-radius: 4px;"></div>
                                    </td>
                                    <td class="Polaris-DataTable__Cell">{{ $stage->description }}</td>
                                    <td class="Polaris-DataTable__Cell">
                                        <div class="Polaris-ButtonGroup">
                                            <div class="Polaris-ButtonGroup__Item">
                                                <button class="Polaris-Button" onclick="openEditStageModal('{{ $stage->id }}', '{{ $stage->name }}', '{{ $stage->color }}', '{{ $stage->description }}', {{ $stage->position }})">
                                                    <span class="Polaris-Button__Content">Edit</span>
                                                </button>
                                            </div>
                                            <div class="Polaris-ButtonGroup__Item">
                                                <button class="Polaris-Button Polaris-Button--destructive" onclick="deleteStage('{{ $stage->id }}')">
                                                    <span class="Polaris-Button__Content">Delete</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Stage Modal -->
<div id="stage-form-modal" class="Polaris-Modal-Dialog__Container" style="display: none;">
    <!-- Modal content similar to the update stage modal, with form fields for stage properties -->
</div>

<script>
    // JavaScript functions to handle the stage management modal
    // Similar to the order stage update functions
</script>
@endsection