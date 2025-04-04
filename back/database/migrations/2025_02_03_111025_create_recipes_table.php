<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecipesTable extends Migration
{
    public function up()
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('cuisine_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->json('ingredients');
            $table->json('steps');
            $table->string('image')->default(''); 
            $table->string('video')->default(''); 
            $table->integer('prep_time');
            $table->integer('cook_time');
            $table->integer('servings');
            $table->json('nutrition')->nullable();
            $table->integer('likes_count')->default(0); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recipes');
    }
}
