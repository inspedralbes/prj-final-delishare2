<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLiveTable extends Migration
{
    public function up()
    {
        Schema::create('live', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade');
            $table->time('hora');
            $table->date('dia');
            $table->timestamps();
            
            // Restricción única compuesta
            $table->unique(['user_id', 'recipe_id', 'dia', 'hora']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('live'); // Debe coincidir con el nombre en up()
    }
}