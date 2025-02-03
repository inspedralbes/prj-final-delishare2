<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipeUserTable extends Migration
{
    public function up()
    {
        Schema::create('recipe_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipe_id')->constrained()->onDelete('cascade'); // Esta línea es clave
            $table->boolean('saved')->default(false);
            $table->boolean('liked')->default(false); // Indica si el usuario ha dado like
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipe_user');
    }
}
