<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VoucherStatusSeeder extends Seeder
{
    /**
     * Seed các trạng thái phiếu mặc định
     */
    public function run(): void
    {
        $statuses = [
            // Trạng thái chung
            [
                'code' => 'draft',
                'name' => 'Bản nháp',
                'description' => 'Phiếu mới tạo, chưa submit duyệt',
                'category' => 'both',
                'order' => 1,
                'color' => 'gray',
            ],
            [
                'code' => 'pending_approval',
                'name' => 'Chờ duyệt',
                'description' => 'Phiếu đã submit, chờ quản lý duyệt',
                'category' => 'both',
                'order' => 2,
                'color' => 'yellow',
            ],
            [
                'code' => 'approved',
                'name' => 'Đã duyệt',
                'description' => 'Phiếu đã được quản lý duyệt, chột xác nhận nhập/xuất',
                'category' => 'both',
                'order' => 3,
                'color' => 'blue',
            ],
            // Trạng thái nhập kho
            [
                'code' => 'received',
                'name' => 'Đã nhận',
                'description' => 'Đã nhận xong hàng vào kho',
                'category' => 'input',
                'order' => 4,
                'color' => 'green',
            ],
            // Trạng thái xuất kho
            [
                'code' => 'completed',
                'name' => 'Hoàn thành',
                'description' => 'Đã xuất xong hàng khỏi kho',
                'category' => 'output',
                'order' => 4,
                'color' => 'green',
            ],
            // Trạng thái huỷ (chung)
            [
                'code' => 'cancelled',
                'name' => 'Đã huỷ',
                'description' => 'Phiếu bị huỷ, không còn hiệu lực',
                'category' => 'both',
                'order' => 99,
                'color' => 'red',
            ],
        ];

        foreach ($statuses as $status) {
            DB::table('voucher_statuses')->insert([
                ...$status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
