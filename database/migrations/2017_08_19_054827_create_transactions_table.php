<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {

            $table->id(); // Auto-incrementing primary key `id`
            $table->unsignedBigInteger('business_id')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->boolean('is_kitchen_order')->default(0);
            $table->unsignedBigInteger('res_table_id')->nullable()->comment('fields to restaurant module');
            $table->integer('res_waiter_id')->nullable()->comment('fields to restaurant module');
            $table->enum('res_order_status', ['received', 'cooked', 'served'])->nullable();
            $table->enum('type', ['purchase', 'sell']);
            $table->enum('status', ['received', 'pending', 'ordered', 'draft', 'final']);
            $table->string('sub_type', 20)->nullable();
            $table->string('sub_status', 191)->nullable();
            $table->boolean('is_quotation')->default(0);
            $table->enum('payment_status', ['paid', 'due', 'partial'])->nullable();
            $table->enum('adjustment_type', ['normal', 'abnormal'])->nullable();
            $table->enum('adjustment_sign', ['Minus', 'Plus'])->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('add_doctor_name', 255)->nullable();
            $table->integer('address_id')->default(0);
            $table->unsignedBigInteger('customer_group_id')->nullable()->comment('used to add customer group while selling');
            $table->string('invoice_no', 191)->nullable();
            $table->string('ref_no', 191)->nullable();
            $table->string('source', 191)->nullable();
            $table->string('subscription_no', 191)->nullable();
            $table->string('subscription_repeat_on', 191)->nullable();
            $table->dateTime('transaction_date');
            $table->decimal('total_before_tax', 22, 4)->default(0.0000)->comment('Total before the purchase/invoice tax, this includes the individual product tax');
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->decimal('tax_amount', 22, 4)->default(0.0000);
            $table->enum('discount_type', ['fixed', 'percentage'])->nullable();
            $table->decimal('discount_amount', 22, 4)->default(0.0000);
            $table->integer('rp_redeemed')->default(0)->comment('rp is the short form of reward points');
            $table->decimal('rp_redeemed_amount', 22, 4)->default(0.0000)->comment('rp is the short form of reward points');
            $table->string('shipping_details', 191)->nullable();
            $table->text('shipping_address')->nullable();
            $table->dateTime('delivery_date')->nullable();
            $table->string('shipping_status', 191)->nullable();
            $table->text('tracking')->nullable();
            $table->string('delivered_to', 191)->nullable();
            $table->unsignedBigInteger('delivery_person')->nullable();
            $table->decimal('shipping_charges', 22, 4)->default(0.0000);
            $table->string('shipping_custom_field_1', 191)->nullable();
            $table->string('shipping_custom_field_2', 191)->nullable();
            $table->string('shipping_custom_field_3', 191)->nullable();
            $table->string('shipping_custom_field_4', 191)->nullable();
            $table->string('shipping_custom_field_5', 191)->nullable();
            $table->text('additional_notes')->nullable();
            $table->text('staff_note')->nullable();
            $table->boolean('is_export')->default(0);
            $table->longText('export_custom_fields_info')->nullable();
            $table->decimal('round_off_amount', 22, 4)->default(0.0000)->comment('Difference of rounded total and actual total');
            $table->string('additional_expense_key_1', 191)->nullable();
            $table->decimal('additional_expense_value_1', 22, 4)->default(0.0000);
            $table->string('additional_expense_key_2', 191)->nullable();
            $table->decimal('additional_expense_value_2', 22, 4)->default(0.0000);
            $table->string('additional_expense_key_3', 191)->nullable();
            $table->decimal('additional_expense_value_3', 22, 4)->default(0.0000);
            $table->string('additional_expense_key_4', 191)->nullable();
            $table->decimal('additional_expense_value_4', 22, 4)->default(0.0000);
            $table->decimal('final_total', 22, 4)->default(0.0000);
            $table->double('change_return_amount', 8, 2)->default(0.00);
            $table->unsignedBigInteger('expense_category_id')->nullable();
            $table->integer('expense_sub_category_id')->nullable();
            $table->unsignedBigInteger('expense_for')->nullable();
            $table->integer('commission_agent')->nullable();
            $table->string('document', 191)->nullable();
            $table->boolean('is_direct_sale')->default(0);
            $table->boolean('is_suspend')->default(0);
            $table->decimal('exchange_rate', 20, 3)->default(1.000);
            $table->decimal('total_amount_recovered', 22, 4)->nullable()->comment('Used for stock adjustment.');
            $table->integer('transfer_parent_id')->nullable();
            $table->integer('return_parent_id')->nullable();
            $table->integer('opening_stock_product_id')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->integer('mfg_parent_production_purchase_id')->nullable();
            $table->decimal('mfg_wasted_units', 22, 4)->nullable();
            $table->decimal('mfg_production_cost', 22, 4)->default(0.0000);
            $table->string('mfg_production_cost_type', 191)->default('percentage');
            $table->boolean('mfg_is_final')->default(0);
            $table->boolean('crm_is_order_request')->default(0);
            $table->integer('woocommerce_order_id')->nullable();
            $table->decimal('essentials_duration', 8, 2);
            $table->string('essentials_duration_unit', 20)->nullable();
            $table->decimal('essentials_amount_per_unit_duration', 22, 4)->default(0.0000);
            $table->text('essentials_allowances')->nullable();
            $table->text('essentials_deductions')->nullable();
            $table->text('purchase_requisition_ids')->nullable();
            $table->string('prefer_payment_method', 191)->nullable();
            $table->integer('prefer_payment_account')->nullable();
            $table->text('sales_order_ids')->nullable();
            $table->text('purchase_order_ids')->nullable();
            $table->string('custom_field_1', 191)->nullable();
            $table->string('custom_field_2', 191)->nullable();
            $table->string('custom_field_3', 191)->nullable();
            $table->string('custom_field_4', 191)->nullable();
            $table->integer('import_batch')->nullable();
            $table->dateTime('import_time')->nullable();
            $table->integer('types_of_service_id')->nullable();
            $table->decimal('packing_charge', 22, 4)->nullable();
            $table->enum('packing_charge_type', ['fixed', 'percent'])->nullable();
            $table->text('service_custom_field_1')->nullable();
            $table->text('service_custom_field_2')->nullable();
            $table->text('service_custom_field_3')->nullable();
            $table->text('service_custom_field_4')->nullable();
            $table->text('service_custom_field_5')->nullable();
            $table->text('service_custom_field_6')->nullable();
            $table->boolean('is_created_from_api')->default(0);
            $table->integer('rp_earned')->default(0)->comment('rp is the short form of reward points');
            $table->text('order_addresses')->nullable();
            $table->boolean('is_recurring')->default(0);
            $table->decimal('recur_interval', 22, 4)->nullable();
            $table->enum('recur_interval_type', ['days', 'months', 'years'])->nullable();
            $table->integer('recur_repetitions')->nullable();
            $table->dateTime('recur_stopped_on')->nullable();
            $table->integer('recur_parent_id')->nullable();
            $table->string('invoice_token', 191)->nullable();
            $table->integer('pay_term_number')->nullable();
            $table->enum('pay_term_type', ['days', 'months'])->nullable();
            $table->unsignedBigInteger('pjt_project_id')->nullable();
            $table->string('pjt_title', 191)->nullable();
            $table->integer('selling_price_group_id')->nullable();
            $table->dateTime('hms_booking_arrival_date_time')->nullable();
            $table->dateTime('hms_booking_departure_date_time')->nullable();
            $table->integer('hms_coupon_id')->nullable();
            $table->dateTime('check_in')->nullable();
            $table->dateTime('check_out')->nullable();
            $table->timestamps(); // Creates `created_at` and `updated_at` columns
            $table->softDeletes(); //

            $table->foreign('business_id')->references('id')->on('business')->onDelete('cascade');           
            $table->foreign('location_id')->references('id')->on('business_locations')->onDelete('cascade');           
            $table->foreign('res_table_id')->references('id')->on('res_tables')->onDelete('cascade');           
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');           
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
