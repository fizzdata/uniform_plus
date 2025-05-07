@extends('app')

@section('content')
<div class="Polaris-Page">
    <div class="Polaris-Page__Header Polaris-Page__Header--hasPagination Polaris-Page__Header--hasSecondaryActions">
        <div class="Polaris-Page__Title">
            <h1 class="Polaris-Header-Title">Orders</h1>
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
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop Polaris-DataTable__Cell--firstColumn" scope="col">Order #</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Customer</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Date</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Status</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Stage</th>
                                    <th class="Polaris-DataTable__Cell Polaris-DataTable__Cell--verticalAlignTop" scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders['orders'] as $order)
                                <tr class="Polaris-DataTable__TableRow">
                                    <td class="Polaris-DataTable__Cell">#{{ $order['name'] }}</td>
                                    <td class="Polaris-DataTable__Cell">{{ $order['customer']['first_name'] }} {{ $order['customer']['last_name'] }}</td>
                                    <td class="Polaris-DataTable__Cell">{{ \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') }}</td>
                                    <td class="Polaris-DataTable__Cell">{{ $order['financial_status'] }}</td>
                                    <td class="Polaris-DataTable__Cell">
                                        <span class="order-stage" style="background-color: {{ $order['stage']->color ?? '#eeeeee' }}; padding: 4px 8px; border-radius: 4px;">
                                            {{ $order['stage']->name ?? 'No Stage' }}
                                        </span>
                                    </td>
                                    <td class="Polaris-DataTable__Cell">
                                        <button class="Polaris-Button" data-order-id="{{ $order['id'] }}" onclick="openStageModal('{{ $order['id'] }}')">
                                            <span class="Polaris-Button__Content">Update Stage</span>
                                        </button>
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

<!-- Stage Update Modal -->
<div id="stage-modal" class="Polaris-Modal-Dialog__Container" style="display: none;">
    <div class="Polaris-Modal-Dialog">
        <div class="Polaris-Modal-Dialog__Modal">
            <div class="Polaris-Modal-Header">
                <div class="Polaris-Modal-Header__Title">
                    <h2 class="Polaris-Heading">Update Order Stage</h2>
                </div>
                <button class="Polaris-Modal-CloseButton" onclick="closeStageModal()">
                    <span class="Polaris-Icon Polaris-Icon--colorBase Polaris-Icon--applyColor">
                        <!-- Close icon SVG -->
                    </span>
                </button>
            </div>
            <div class="Polaris-Modal__BodyWrapper">
                <div class="Polaris-Modal__Body Polaris-Scrollable Polaris-Scrollable--vertical" data-polaris-scrollable="true">
                    <section class="Polaris-Modal-Section">
                        <div class="Polaris-FormLayout">
                            <div class="Polaris-FormLayout__Item">
                                <div class="Polaris-Select">
                                    <select id="stage-select" class="Polaris-Select__Input">
                                        @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="Polaris-FormLayout__Item">
                                <div class="Polaris-TextField">
                                    <textarea id="stage-notes" class="Polaris-TextField__Input" placeholder="Add notes about this stage change"></textarea>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
            <div class="Polaris-Modal-Footer">
                <div class="Polaris-Modal-Footer__FooterContent">
                    <div class="Polaris-Stack Polaris-Stack--alignmentCenter">
                        <div class="Polaris-Stack__Item">
                            <button class="Polaris-Button Polaris-Button--primary" onclick="saveStageChange()">
                                <span class="Polaris-Button__Content">Save</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentOrderId = null;
    
    function openStageModal(orderId) {
        currentOrderId = orderId;
        document.getElementById('stage-modal').style.display = 'block';
    }
    
    function closeStageModal() {
        document.getElementById('stage-modal').style.display = 'none';
    }
    
    function saveStageChange() {
        const stageId = document.getElementById('stage-select').value;
        const notes = document.getElementById('stage-notes').value;
        
        fetch(`/orders/${currentOrderId}/update-stage`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                stage_id: stageId,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success toast using App Bridge
                const Toast = actions.Toast;
                const toastNotice = Toast.create(app, {
                    message: 'Order stage updated successfully',
                    duration: 5000,
                });
                toastNotice.dispatch(Toast.Action.SHOW);
                
                // Refresh the page to show updated data
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            closeStageModal();
        });
    }
</script>
@endsection