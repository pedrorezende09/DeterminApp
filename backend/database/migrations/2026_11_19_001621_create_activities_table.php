    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('activities', function (Blueprint $table) {
                $table->id();

                $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

                $table->foreignId('category_id');

                $table->string('title', 150);

                $table->text('description')->nullable();

                $table->string('type', 20);

                $table->string('priority', 15)->nullable();

                $table->date('due_date')->nullable();

                $table->date('ends_at')->nullable();

                $table->string('status', 20);

                $table->timestamp('completed_at')->nullable();

                $table->timestamps();

                $table->foreign(['category_id', 'user_id'])
                    ->references(['id', 'user_id'])
                    ->on('categories')
                    ->restrictOnDelete();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('activities');
        }
    };
