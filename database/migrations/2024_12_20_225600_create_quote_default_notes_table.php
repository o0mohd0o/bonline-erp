<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quote_default_notes', function (Blueprint $table) {
            $table->id();
            $table->text('note_ar');
            $table->text('note_en')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // Insert default notes
        DB::table('quote_default_notes')->insert([
            [
                'note_ar' => 'يتم إتمام الخدمة خلال 2-3 أيام عمل بعد استلام البيانات المطلوبة',
                'display_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'note_ar' => 'يشمل العرض دعم فني مجاني لمدة أسبوع لضمان استقرار الخدمات بعد تنفيذها',
                'display_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'note_ar' => 'إذا كنت بحاجة إلى أي تعديلات أو خدمات إضافية، يرجى التواصل لتخصيص العرض',
                'display_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('quote_default_notes');
    }
};
