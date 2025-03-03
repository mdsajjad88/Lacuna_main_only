<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        {!! Form::open([
            'url' => action([\App\Http\Controllers\ProductController::class, 'checkDetailUpdate'], $randomCheck->id),
            'method' => 'post',
        ]) !!}
        {!! csrf_field() !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3>Edit Random Check <small><strong>Location: </strong></small></h3>
                <div>
                    <a href="{{ route('random_check.printA4', $randomCheck->id) }}" class="btn btn-info btn-sm">Print(A4)</a>
                    <a href="{{ route('random_check.printPOS', $randomCheck->id) }}" style="margin-right: 15px;" class="btn btn-info btn-sm">Print(POS)</a>
                </div>
            </div>
        </div>
        <div class="modal-body" id="printableArea">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Brand Name</th>
                            <th>Soft. Count</th>
                            <th>Phy. Count Dif.</th>
                            <th>Comment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($randomCheck->randomCheckDetails as $detail)
                            <tr>
                                <td>{{ $detail->product->category->name }}</td>
                                <td>{{ $detail->product->name }}</td>
                                <td>{{ $detail->product->sku }}</td>
                                <td>{{ $detail->product->brand_name }}</td>
                                <td>{{ $detail->current_stock }}</td>
                                <td>
                                    <div class="input-group input-number ">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat quantity-down-int" data-index="{{ $detail->id }}">
                                                <i class="fa fa-minus text-danger"></i>
                                            </button>
                                        </span>
                                        {!! Form::number("details[{$detail->id}][physical_count]", number_format($detail->physical_count), [
                                            'class' => 'form-control input_number',
                                            'required' => true,
                                            'id' => "physical_count_{$detail->id}",
                                            'data-id' => $detail->id
                                        ]) !!}
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default btn-flat quantity-up-int" data-index="{{ $detail->id }}">
                                                <i class="fa fa-plus text-success"></i>
                                            </button>
                                        </span>
                                    </div>
                                    <div style="text-align: center;">
                                        <small id="physical_count_text_{{ $detail->id }}" class="form-text "></small>
                                    </div>
                                </td>
                                <td>
                                    {!! Form::text("details[{$detail->id}][comment]", $detail->comment, ['class' => 'form-control ']) !!}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('comment', 'Comment:') !!}
                        {!! Form::textarea('comment', strip_tags($randomCheck->comment), ['class' => 'form-control', 'rows' => 3, 'placeholder' => 'Overall comments...']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        {!! Form::close() !!}
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(document).ready(function() {
        function updatePhysicalCountText(id, value) {
            const textElement = document.querySelector(#physical_count_text_${id});
            if (value === 0) {
                textElement.textContent = '0 (match)';
            } else if (value < 0) {
                textElement.textContent = ${value} (missing);
            } else if (value > 0) {
                textElement.textContent = +${value} (surplus);
            }
        }

        $('.quantity-down-int').on('click', function() {
            const index = $(this).data('index');
            const input = $(#physical_count_${index});
            let value = parseInt(input.val()) || 0; // Ensure value is an integer
            input.val(value - 1);
            updatePhysicalCountText(index, parseInt(input.val()));
        });

        $('.quantity-up-int').on('click', function() {
            const index = $(this).data('index');
            const input = $(#physical_count_${index});
            let value = parseInt(input.val()) || 0; // Ensure value is an integer
            input.val(value + 1);
            updatePhysicalCountText(index, parseInt(input.val()));
        });

        // Initialize text elements
        $('.input_number').each(function() {
            const id = $(this).data('id');
            const value = parseInt($(this).val()) || 0; // Ensure value is an integer
            updatePhysicalCountText(id, value);
        });
    });
</script>