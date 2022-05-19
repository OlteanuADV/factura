<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeapAnnouncesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seap_announces', function (Blueprint $table) {
            $table->id();
            $table->string('advNoticeId');
            $table->string('AuthorityName');
            $table->string('cpvCodeText')->nullable();
            $table->decimal('Pret', $precision = 8, $scale = 2);
            $table->longText('contractObject')->nullable();
            $table->longText('contractDescription')->nullable();
            $table->longText('contractRelatedConditions')->nullable();
            $table->string('documentUrl')->nullable();
            $table->string('documentName')->nullable();
            $table->longText('participationConditions')->nullable();
            $table->longText('additionalInformation')->nullable();
            $table->string('AuthorityEmail')->nullable();
            $table->date('Deadline')->nullable();
            $table->string('Region')->nullable();
            $table->json('JsonSEAP')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seap_announces');
    }
}
