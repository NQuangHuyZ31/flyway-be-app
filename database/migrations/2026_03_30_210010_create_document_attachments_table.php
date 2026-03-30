<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tài liệu đính kèm (Document Attachments)
     * = Lưu trữ file, hình ảnh cho các chứng từ
     * = Dùng cho: PO, Invoice, Receipt, Photos, etc
     */
    public function up(): void
    {
        Schema::create('document_attachments', function (Blueprint $table) {
            $table->id()->comment('ID tài liệu');
            
            // Polymorphic relationship - attach to any document
            $table->string('attachable_type')->comment('Loại đối tượng (Order, Invoice, PO, Receipt, etc)');
            $table->unsignedBigInteger('attachable_id')->comment('ID đối tượng');
            
            // File information
            $table->string('file_name', 255)->comment('Tên file gốc');
            $table->string('stored_path', 500)->comment('Đường dẫn lưu trữ (S3, local, etc)');
            $table->string('file_type', 50)->comment('Loại file (pdf, jpg, doc, etc)');
            $table->string('mime_type', 100)->comment('MIME type (application/pdf, image/jpeg, etc)');
            $table->integer('file_size')->default(0)->comment('Kích thước file (bytes)');
            
            // Document classification
            $table->enum('document_type', ['purchase_order', 'invoice', 'receipt', 'proof_of_delivery', 'quality_cert', 'photo', 'contract', 'report', 'other'])->comment('Loại tài liệu');
            $table->string('document_number', 100)->nullable()->comment('Số chứng từ (VD: INV-2026-001)');
            $table->date('document_date')->nullable()->index()->comment('Ngày tài liệu');
            
            // Upload & Access
            $table->unsignedBigInteger('uploaded_by')->comment('Khóa ngoại: Người đăng tải');
            $table->boolean('is_verified')->default(false)->comment('Đã xác minh hay chưa');
            $table->unsignedBigInteger('verified_by')->nullable()->comment('Khóa ngoại: Người xác minh');
            $table->timestamp('verified_at')->nullable()->comment('Thời gian xác minh');
            
            // Versioning & Management
            $table->integer('version')->default(1)->comment('Phiên bản tài liệu');
            $table->boolean('is_latest')->default(true)->comment('Là phiên bản mới nhất');
            $table->unsignedBigInteger('original_attachment_id')->nullable()->comment('FK: File gốc (nếu đây là version cũ)');
            $table->text('change_notes')->nullable()->comment('Ghi chú thay đổi giữa các phiên bản');
            
            // Retention & Compliance
            $table->date('retention_until')->nullable()->comment('Giữ tài liệu đến ngày');
            $table->boolean('is_archived')->default(false)->comment('Đã lưu trữ');
            $table->text('archive_reason')->nullable()->comment('Lý do lưu trữ');
            
            // Metadata & Tags
            $table->string('document_title', 255)->nullable()->comment('Tiêu đề tài liệu');
            $table->text('description')->nullable()->comment('Mô tả/ghi chú về tài liệu');
            $table->string('tags', 500)->nullable()->comment('Tags (invoice, urgent, contract, etc)');
            
            // Security & Access Control
            $table->boolean('is_confidential')->default(false)->comment('Tài liệu bảo mật');
            $table->boolean('is_active')->default(true)->comment('Tài liệu có hoạt động');
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('uploaded_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('verified_by')
                ->references('id')->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('original_attachment_id')
                ->references('id')->on('document_attachments')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Chỉ mục
            $table->index(['attachable_type', 'attachable_id']);  // Tìm file theo đối tượng
            $table->index('uploaded_by');
            $table->index('document_type', 'idx_document_type');
            $table->index('document_date', 'idx_document_date');
            $table->index('is_archived');
            $table->index('retention_until', 'idx_retention_until');                    // Tìm file sắp hết thời gian giữ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_attachments');
    }
};
