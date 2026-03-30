# Cơ Sở Dữ Liệu Flyway - Hệ Thống Quản Lý Kho

## Tổng Quan

Cơ sở dữ liệu Flyway được thiết kế để quản lý toàn bộ quy trình inventory (kho hàng) của một hệ thống thương mại điện tử. Hệ thống bao gồm **31 bảng** được chia thành **9 nhóm chức năng** chính, với các mối quan hệ phức tạp và ràng buộc khóa ngoại.

- **Engine**: MySQL/InnoDB
- **Charset**: utf8mb4
- **Collation**: utf8mb4_unicode_ci
- **Phiên bản Laravel**: 11.x

---

## 📊 Cấu Trúc Tổng Quan

```
┌─────────────────────────────────────────────────────────────────┐
│           FLYWAY INVENTORY MANAGEMENT DATABASE                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  1. Authentication & Authorization (4 bảng)                     │
│     └─ roles, permissions, role_permission, users               │
│                                                                   │
│  2. Organization & Configuration (5 bảng)                       │
│     └─ company_settings, units_of_measure, warehouses,          │
│        warehouse_sections, user_warehouses                       │
│                                                                   │
│  3. Product Catalog (4 bảng)                                    │
│     └─ categories, products, product_batches,                   │
│        customers, suppliers                                      │
│                                                                   │
│  4. Orders & Quotations (4 bảng)                                │
│     └─ quotations, quotation_items, orders, order_items         │
│                                                                   │
│  5. Inventory Management (5 bảng)                               │
│     └─ inventory, inventory_transactions,                        │
│        inventory_adjustments, stock_movements, reserved_stock    │
│                                                                   │
│  6. Stock Vouchers (4 bảng - MỚI)                               │
│     └─ stock_input_vouchers, stock_input_voucher_items,         │
│        stock_output_vouchers, stock_output_voucher_items         │
│                                                                   │
│  7. Voucher Status Management (2 bảng - MỚI)                    │
│     └─ voucher_statuses, voucher_status_histories               │
│        (Quản lý trạng thái và lịch sử thay đổi phiếu)           │
│                                                                   │
│  8. Batch Tracking (1 bảng)                                     │
│     └─ product_batches (Quản lý lô nhập hàng)                   │
│                                                                   │
│  9. Audit & Logging (1 bảng)                                    │
│     └─ audit_logs                                                │
│                                                                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 1️⃣ Nhóm Authentication & Authorization

### roles
Định nghĩa các vai trò (role) trong hệ thống
```
- id (PK)
- name: Tên vai trò (admin, manager, staff, etc.)
- description: Mô tả chi tiết
- is_active: Trạng thái hoạt động
- created_at, updated_at
```
**Mối quan hệ**: 1 role → N permissions (qua role_permission)

### permissions
Danh sách các quyền hạn trong hệ thống
```
- id (PK)
- name: Tên quyền
- module: Module/nhóm chức năng
- description: Mô tả quyền
- created_at, updated_at
```
**Mối quan hệ**: N permissions → M roles (qua role_permission)

### role_permission
Bảng kết nối many-to-many giữa roles và permissions
```
- id (PK)
- role_id (FK → roles)
- permission_id (FK → permissions)
- created_at, updated_at
- UNIQUE(role_id, permission_id)
```

### users (mở rộng)
**Lưu ý**: Bảng users được tạo sẵn bởi Laravel, nhưng thêm cột role_id
```
- ...các cột mặc định...
- role_id (FK → roles, nullable)
```

---

## 2️⃣ Nhóm Organization & Configuration

### company_settings
Cấu hình thông tin công ty chính
```
- id (PK)
- company_name
- company_code
- address, city, country, zip_code
- tax_code, registration_number
- phone, email, website
- currency, timezone
- logo_url
- created_at, updated_at
```
**Mục đích**: Lưu trữ thông tin tổng công ty cho tất cả các module

### units_of_measure
Đơn vị tính trong hệ thống (Kg, Thùng, Cái, etc.)
```
- id (PK)
- code: Mã đơn vị (PCS, BOX, KG, etc.)
- unit_name: Tên đơn vị
- description
- conversion_factor: Hệ số chuyển đổi
- is_active
- created_at, updated_at
```

### warehouses
Kho hàng chính
```
- id (PK)
- warehouse_name
- warehouse_code (UNIQUE)
- address, city, district, country
- phone, email
- manager_id (FK → users, nullable)
- warehouse_type: ENUM(general, cold_storage, hazmat, distribution)
- capacity: Dung tích tối đa (M³)
- current_occupancy: Dung tích hiện tại
- notes
- is_active
- created_at, updated_at
```
**Chỉ mục**: warehouse_code, manager_id, warehouse_type

### warehouse_sections
Khu vực/vị trí trong kho (rack, shelf, cage, bin, zone)
```
- id (PK)
- warehouse_id (FK → warehouses)
- section_name
- section_code
- section_type: ENUM(rack, shelf, cage, bin, zone)
- capacity: Dung tích khu vực
- current_occupancy
- shelves_count, racks_count
- notes
- is_active
- created_at, updated_at
- UNIQUE(warehouse_id, section_code)
```

### user_warehouses
Gán người dùng vào kho (quản lý/có quyền truy cập)
```
- id (PK)
- user_id (FK → users)
- warehouse_id (FK → warehouses)
- assignment_type: ENUM(primary, secondary)
- assigned_date
- is_active
- created_at, updated_at
- UNIQUE(user_id, warehouse_id)
```

---

## 3️⃣ Nhóm Product Catalog

### categories
Danh mục sản phẩm (có phân cấp)
```
- id (PK)
- name (UNIQUE)
- slug (UNIQUE)
- parent_id (FK → categories, nullable) - Danh mục cha
- description
- icon, thumbnail_url
- display_order
- is_active
- created_at, updated_at
```
**Tính năng**: Hỗ trợ danh mục đa cấp (parent_id tự liên kết)

### products
Sản phẩm chính
```
- id (PK)
- product_name
- product_code (UNIQUE)
- sku: Mã hàng
- category_id (FK → categories, nullable)
- unit_id (FK → units_of_measure)
- supplier_id (FK → suppliers, nullable)
- description
- price: Giá bán mặc định
- cost: Giá vốn mặc định
- reorder_level: Ngưỡng đặt lại hàng
- quantity: Số lượng tồn kho tổng
- is_active
- created_at, updated_at
```
**Chỉ mục**: product_code, sku, category_id

**Ghi chú**: 
- Từ v2.0 trở đi, sản phẩm được quản lý theo **batch nhập hàng** thay vì variants
- Mỗi lô nhập (từ bảng `product_batches`) có thể có giá riêng (_batch_cost_)
- Hỗ trợ theo dõi giá vốn khác nhau cho các lô hàng khác nhau

### customers
Danh sách khách hàng
```
- id (PK)
- customer_name
- customer_code (UNIQUE)
- email, phone
- address, city, country
- customer_type: ENUM(retail, wholesale, distributor)
- credit_limit: Hạn tín dụng
- current_debt: Nợ hiện tại
- tax_code, bank_account
- payment_terms_days
- is_active
- created_at, updated_at
```

### suppliers
Danh sách nhà cung cấp
```
- id (PK)
- supplier_name
- supplier_code (UNIQUE)
- contact_person
- email, phone
- address, city, country
- tax_code, bank_account
- payment_terms_days
- discount_percent: Chiếc khấu
- rating: Đánh giá
- is_active
- created_at, updated_at
```

---

## 4️⃣ Nhóm Quotations & Orders

### quotations
Báo giá/đơn chào (hỗ trợ báo giá cho cả khách hàng và nhà cung cấp)
```
- id (PK)
- quotation_code (UNIQUE)
- quotation_type: ENUM(customer_quotation, supplier_quotation) 
  * customer_quotation: Báo giá cho khách hàng (giá bán)
  * supplier_quotation: Báo giá từ nhà cung cấp (giá mua)
- customer_id (FK → customers, nullable) - Khách hàng (nếu customer_quotation)
- supplier_id (FK → suppliers, nullable) - Nhà cung cấp (nếu supplier_quotation)
- user_id (FK → users) - Người tạo
- quotation_date: Ngày báo giá
- valid_until: Hạn hiệu lực báo giá
- status: ENUM(draft, sent, accepted, rejected, expired, cancelled)
- subtotal: Tiền hàng
- discount_amount: Chiết khấu
- tax_amount: Tiền thuế
- total_amount: Tổng tiền
- notes: Ghi chú
- created_at, updated_at

**Tính năng**: 
- Báo giá cho customer: quotation_type = 'customer_quotation', customer_id có giá trị
- Báo giá từ supplier: quotation_type = 'supplier_quotation', supplier_id có giá trị
- Ít nhất một trong customer_id hoặc supplier_id phải có giá trị
```

### quotation_items
Chi tiết dòng báo giá
```
- id (PK)
- quotation_id (FK → quotations)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable) - Lô hàng báo giá (nếu có)
- quantity: Số lượng
- unit_price: Giá đơn vị
- discount_percent
- line_total: Tổng dòng
- created_at, updated_at
```

### orders
Đơn hàng thống nhất (hỗ trợ cả đơn bán và mua)
```
- id (PK)
- order_code (UNIQUE)
- order_type: ENUM(sales_order, purchase_order)
  * sales_order: Đơn hàng bán cho khách hàng
  * purchase_order: Đơn hàng mua từ nhà cung cấp
- customer_id (FK → customers, nullable) - Khách hàng (nếu sales_order)
- supplier_id (FK → suppliers, nullable) - Nhà cung cấp (nếu purchase_order)
- user_id (FK → users) - Người tạo
- warehouse_id (FK → warehouses, nullable) - Kho gửi/nhận
- quotation_id (FK → quotations, nullable) - Báo giá liên quan
- order_date
- expected_delivery_date
- actual_delivery_date (nullable)
- status: ENUM(draft, pending, sent, confirmed, processing, ready, partial_received, shipped, delivered, received, returned, cancelled)
  * Dành cho sales_order: pending, processing, ready, shipped, delivered, returned, cancelled
  * Dành cho purchase_order: draft, sent, confirmed, partial_received, received, cancelled
- subtotal, discount_amount, shipping_cost, tax_amount
- total_amount: Tổng tiền
- paid_amount: Tiền đã thanh toán
- payment_status: ENUM(unpaid, partial, paid, refund)
- notes
- shipping_address (nullable)
- created_at, updated_at

**Tính năng**:
- Sales order: order_type = 'sales_order', customer_id có giá trị
- Purchase order: order_type = 'purchase_order', supplier_id có giá trị
- Status gộp chung cho cả 2 loại với ý nghĩa khác nhau
```

### order_items
Chi tiết dòng đơn hàng
```
- id (PK)
- order_id (FK → orders)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable) - Lô hàng được xuất
- order_quantity
- delivered_quantity
- unit_price
- discount_percent
- line_total
- created_at, updated_at
```

---

## 5️⃣ Nhóm Inventory Management

### inventory
Tồn kho theo sản phẩm (theo lô) và kho
```
- id (PK)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable) - Lô nhập lô hàng từ product_batches
- warehouse_id (FK → warehouses)
- section_id (FK → warehouse_sections, nullable)
- quantity_on_hand: Số lượng trong tay
- quantity_reserved: Số lượng dự trữ/đặt trước
- quantity_available: Sẵn sàng bán = on_hand - reserved
- reorder_level: Ngưỡng đặt lại
- last_counted_at: Thời gian kiểm kho cuối
- last_counted_by (FK → users, nullable)
- notes
- created_at, updated_at
- UNIQUE(product_id, batch_id, warehouse_id, section_id)
```

### inventory_transactions
Lịch sử tất cả giao dịch kho (audit trail)
```
- id (PK)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable)
- warehouse_id (FK → warehouses)
- section_id (FK → warehouse_sections, nullable)
- user_id (FK → users)
- transaction_type: ENUM(import, export, adjustment, damage, loss, movement, return)
- quantity_change: Thay đổi số lượng (âm=xuất)
- reference_type: Loại tài liệu (order, quotation, adjustment, etc.)
- reference_id: ID tài liệu
- notes
- transaction_date
- created_at, updated_at
- INDEX(transaction_date, warehouse_id, product_id)
```

### inventory_adjustments
Chứng từ điều chỉnh kho
```
- id (PK)
- adjustment_code (UNIQUE)
- warehouse_id (FK → warehouses)
- user_id (FK → users) - Người tạo
- adjustment_date
- reason
- total_adjusted_quantity
- is_approved
- approved_by (FK → users, nullable)
- approval_date
- approval_notes
- created_at, updated_at
```

### stock_movements
Chuyển kho/chuyển khu vực
```
- id (PK)
- movement_code (UNIQUE)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable)
- from_warehouse_id (FK → warehouses)
- from_section_id (FK → warehouse_sections, nullable)
- to_warehouse_id (FK → warehouses)
- to_section_id (FK → warehouse_sections, nullable)
- user_id (FK → users)
- quantity
- movement_date
- status: ENUM(pending, in_transit, received, cancelled)
- notes
- created_at, updated_at
```

### reserved_stock
Tồn kho dự trữ theo đơn hàng/điều chỉnh
```
- id (PK)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable)
- warehouse_id (FK → warehouses)
- user_id (FK → users)
- order_id (FK → orders, nullable) - Đơn hàng nếu dự trữ từ order
- quantity_reserved
- reserved_date
- status: ENUM(active, released, completed)
- release_date
- created_at, updated_at
```

---

## 6️⃣ Nhóm Stock Vouchers (Phiếu Xuất/Nhập Kho) - MỚI

Hệ thống quản lí phiếu xuất kho và phiếu nhập kho chính thức với workflow phê duyệt và tracking chi tiết.

### stock_input_vouchers
Phiếu nhập kho (từ mua hàng, trả hàng, chuyển kho, điều chỉnh, mẫu)
```
- id (PK)
- voucher_code (UNIQUE): Mã phiếu (VD: NHK-2026-001)
- input_type: ENUM(purchase_order, return_from_customer, transfer_in, adjustment, sample)
- supplier_id (FK → suppliers, nullable): Nhà cung cấp (khi mua)
- warehouse_id (FK → warehouses): Kho nhận
- section_id (FK → warehouse_sections, nullable): Khu vực cụ thể
- order_id (FK → orders, nullable): Liên kết purchase_order
- input_date: Ngày nhập
- invoice_number: Số hóa đơn/chứng từ
- created_by (FK → users): Người tạo
- approved_by (FK → users, nullable): Người duyệt
- received_by (FK → users, nullable): Người nhận hàng
- status: ENUM(draft, pending_approval, approved, received, cancelled)
- total_items, total_quantity, total_cost
- notes, rejection_reason
- created_at, updated_at
```

### stock_input_voucher_items
Chi tiết sản phẩm trong phiếu nhập kho
```
- id (PK)
- voucher_id (FK → stock_input_vouchers)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable)
- quantity_ordered, quantity_received, quantity_rejected
- unit_cost, line_total
- notes, rejection_notes
- created_at, updated_at
```

### stock_output_vouchers
Phiếu xuất kho (bán, chuyển kho, mẫu, hỏng, trả supplier, điều chỉnh)
```
- id (PK)
- voucher_code (UNIQUE): Mã phiếu (VD: XK-2026-001)
- output_type: ENUM(sales_order, transfer_out, sample, damage, loss, return_to_supplier, adjustment)
- customer_id (FK → customers, nullable): Khách hàng
- warehouse_id (FK → warehouses): Kho xuất
- section_id (FK → warehouse_sections, nullable): Khu vực xuất cụ thể
- order_id (FK → orders, nullable): Liên kết sales_order
- output_date: Ngày xuất
- created_by (FK → users): Người tạo
- approved_by (FK → users, nullable): Người duyệt
- output_by (FK → users, nullable): Người thực hiện xuất
- status: ENUM(draft, pending_approval, approved, completed, cancelled)
- total_items, total_quantity, total_cost
- notes, rejection_reason
- created_at, updated_at
```

### stock_output_voucher_items
Chi tiết sản phẩm trong phiếu xuất kho
```
- id (PK)
- voucher_id (FK → stock_output_vouchers)
- product_id (FK → products)
- batch_id (FK → product_batches, nullable): Batch cụ thể (FIFO)
- quantity_ordered, quantity_output
- unit_cost, line_total
- notes
- created_at, updated_at
```

**Workflow Phiếu Nhập**: Draft → Pending Approval → Approved → Received  
**Workflow Phiếu Xuất**: Draft → Pending Approval → Approved → Completed  

**Tính Năng**:
✅ Phê duyệt quy trình (draft, duyệt, nhận/xuất)  
✅ Tracking số lượng thực tế vs dự kiến  
✅ Hỗ trợ FIFO/LIFO qua batch_id  
✅ Ghi nhận người tạo, duyệt, thực hiện  
✅ Chi tiết hàng từ chối/hỏng  

---

## 7️⃣ Nhóm Voucher Status Management (Quản Lý Trạng Thái Phiếu)

### voucher_statuses
Định nghĩa các trạng thái có sẵn cho phiếu xuất/nhập kho. Cho phép quản lý trạng thái linh hoạt mà không cần thay đổi code.

```
- id (PK)
- code: Mã trạng thái UNIQUE (draft, pending_approval, approved, received, completed, cancelled)
- name: Tên trạng thái tiếng Việt (Bản nháp, Chờ duyệt, Đã duyệt, Đã nhận, Hoàn thành, Đã hủy)
- description: Mô tả chi tiết trạng thái
- category: Phân loại (input, output, both) - xác định trạng thái áp dụng cho loại phiếu nào
- order: Thứ tự sắp xếp (dùng để hiển thị timeline)
- color: Mã hex color hoặc tên color (gray, yellow, blue, green, red) - dùng cho UI styling
- is_active: Trạng thái có sử dụng không (cho phép soft-delete logic)
- created_at, updated_at
```

**Dữ liệu Mặc Định**:
```
1. draft (Bản nháp) - order: 1, color: gray, category: both
2. pending_approval (Chờ duyệt) - order: 2, color: yellow, category: both
3. approved (Đã duyệt) - order: 3, color: blue, category: both
4. received (Đã nhận) - order: 4, color: green, category: input
5. completed (Hoàn thành) - order: 4, color: green, category: output
6. cancelled (Đã hủy) - order: 99, color: red, category: both
```

**Mối Quan Hệ**:
- 1 VoucherStatus → N VoucherStatusHistory (từ_status_id, to_status_id)

---

### voucher_status_histories
Ghi nhận lịch sử thay đổi trạng thái của từng phiếu. Cung cấp audit trail đầy đủ: ai, khi nào, từ trạng thái nào sang trạng thái nào, lý do gì.

```
- id (PK)
- voucherable_type: Loại voucher (App\Models\StockInputVoucher hoặc App\Models\StockOutputVoucher) - Polymorphic
- voucherable_id: ID của voucher - Polymorphic
- from_status_id (FK → voucher_statuses, nullable): Trạng thái trước đó (NULL nếu trạng thái đầu tiên)
- to_status_id (FK → voucher_statuses): Trạng thái mới
- changed_by (FK → users): Người thay đổi trạng thái
- reason: Lý do thay đổi (nullable)
- notes: Ghi chú thêm (nullable)
- ip_address: IP address của người thực hiện thay đổi (nullable)
- changed_at: Thời gian thực hiện thay đổi (mặc định là CURRENT_TIMESTAMP)
- created_at, updated_at
```

**Mối Quan Hệ**:
- Polymorphic: voucherable() → StockInputVoucher hoặc StockOutputVoucher
- from_status: FK → voucher_statuses.id
- to_status: FK → voucher_statuses.id
- changed_by: FK → users.id

**Scopes Có Sẵn**:
```php
->byVoucherType('StockInputVoucher')  // Lọc theo loại phiếu
->byVoucherId(1)                      // Lọc theo ID voucher
->byStatus('approved')                // Lọc theo status code
->byUser(5)                           // Lọc theo người thay đổi
->dateRange($fromDate, $toDate)       // Lọc theo khoảng thời gian
->latest()                            // Sắp xếp theo thời gian mới nhất trước
```

**Ví Dụ Sử Dụng**:
```php
// Lấy lịch sử thay đổi của phiếu nhập kho
$inputVoucher = StockInputVoucher::find(1);
$history = $inputVoucher->statusHistories()->latest()->get();

// Thay đổi trạng thái với ghi nhận
$inputVoucher->submitForApproval(userId: 1, notes: 'Gửi duyệt');
$inputVoucher->approve(approvedBy: 2, reason: 'Đủ đầy đủ hồ sơ');
$inputVoucher->completeReceiving(receivedBy: 3, reason: 'Đã xác nhận kho');

// Truy vấn lịch sử chi tiết
$changes = VoucherStatusHistory::byVoucherType('StockInputVoucher')
    ->byVoucherId(1)
    ->dateRange(now()->subDays(30), now())
    ->with('fromStatus', 'toStatus', 'changedBy')
    ->get();
```

---

## 8️⃣ Nhóm Batch Tracking

### product_batches
Quản lý lô nhập hàng từ nhà cung cấp
```
- id (PK)
- batch_code (UNIQUE): Mã lô hàng (ví dụ: BATCH-2026-001-SUPPLIER)
- product_id (FK → products): Sản phẩm
- supplier_id (FK → suppliers, nullable): Nhà cung cấp của lô hàng
- quantity_imported: Số lượng nhập
- quantity_available: Số lượng còn sẵn (tracking usage)
- unit_cost: Giá vốn/đơn vị của lô này (có thể khác giá sản phẩm chính)
- unit_price: Giá bán đề xuất của lô (nullable - nếu khác giá bình thường)
- import_date: Ngày nhập vào kho
- expiry_date: Hạn sử dụng/hạn bảo hành (nullable)
- notes: Ghi chú về lô hàng
- status: ENUM(active, selling_out, expired, discontinued)
- created_at, updated_at

Index:
- UNIQUE(product_id, batch_code)
- (product_id, import_date)
- (supplier_id)
- (status)
```

**Mục đích**: 
- Theo dõi từng lô nhập hàng (batch) riêng biệt
- Quản lý giá vốn khác nhau cho các lô khác nhau (cùng sản phẩm)
- Theo dõi hạn sử dụng/bảo hành nếu có
- Hỗ trợ FIFO (First In First Out) hoặc LIFO (Last In First Out) inventory method
- Liên kết với order_items, inventory, inventory_transactions để theo dõi tính khả dụng

**Tính năng**:
- Mỗi lô nhập từ nhà cung cấp có mã batch_code riêng
- unit_cost có thể khác giữa các lô (giá mua mỗi lô có thể khác nhau)
- quantity_available giảm khi sản phẩm được xuất/sử dụng
- Dễ dàng tính COGS (Cost of Goods Sold) theo batch
- Hỗ trợ theo dõi số serial hoặc hạn sử dụng nếu cần

---

## 9️⃣ Nhóm Audit & Logging

### audit_logs
Nhật ký hoạt động và thay đổi dữ liệu
```
- id (PK)
- user_id (FK → users, nullable)
- model_type: Loại model thay đổi (Product, Order, etc.)
- model_id: ID của model
- action: ENUM(create, update, delete, view, export, import)
- old_values: JSON - Giá trị cũ
- new_values: JSON - Giá trị mới
- ip_address
- user_agent
- description
- created_at, updated_at
- INDEX(created_at, model_type, action)
```

**Tính năng**: Ghi lại tất cả hoạt động khi người dùng tạo/sửa/xóa dữ liệu

---

## 🔗 Mối Quan Hệ Chính (Foreign Keys)

### Mối quan hệ phân cấp:
```
users ─── role_id ──→ roles
        └─ manager_id ──→ warehouses
        └─ user_id ──→ quotations, orders
        └─ user_id ──→ inventory_transactions, audit_logs

categories ─ parent_id ──→ categories (self-referencing)

products ──→ categories, units_of_measure, suppliers
          └─ product_id ──→ product_batches (NEW)
          └─ product_id ──→ inventory, quotation_items, order_items

product_batches ──→ products, suppliers
                 └─ batch_id ──→ inventory, order_items, quotation_items,
                                 inventory_transactions, stock_movements, reserved_stock

quotations ──→ customers, suppliers, users
            └─ quotation_id ──→ quotation_items, orders

orders ──→ customers (sales_order), suppliers (purchase_order), users, warehouses, quotations
        └─ order_id ──→ order_items

warehouses ──→ users (manager)
            └─ warehouse_id ──→ warehouse_sections, inventory, user_warehouses

inventory_transactions ← import/export/adjustment/movement events
```

---

## ✨ Tính Năng Báo Giá Linh Hoạt

### Báo Giá Cho Khách Hàng (Customer Quotation)
Dùng để gửi giá bán cho khách hàng
```sql
INSERT INTO quotations (
    quotation_code, quotation_type, customer_id, user_id, 
    quotation_date, valid_until, status, total_amount
) VALUES (
    'QT001', 'customer_quotation', 1, 1,
    '2026-03-30', '2026-04-30', 'draft', 5000000
);
```

### Báo Giá Từ Nhà Cung Cấp (Supplier Quotation)
Dùng để nhận giá mua từ nhà cung cấp
```sql
INSERT INTO quotations (
    quotation_code, quotation_type, supplier_id, user_id, 
    quotation_date, valid_until, status, total_amount
) VALUES (
    'QT_SUP001', 'supplier_quotation', 1, 1,
    '2026-03-30', '2026-04-30', 'draft', 3500000
);
```

### So Sánh Báo Giá
```sql
-- Tất cả báo giá cho khách hàng ID 1
SELECT * FROM quotations 
WHERE quotation_type = 'customer_quotation' AND customer_id = 1;

-- Tất cả báo giá từ supplier ID 2
SELECT * FROM quotations 
WHERE quotation_type = 'supplier_quotation' AND supplier_id = 2;

-- Báo giá gần hết hạn
SELECT * FROM quotations 
WHERE valid_until < DATE_ADD(NOW(), INTERVAL 7 DAY) 
AND status IN ('draft', 'sent');
```

---

## � Hệ thống Đơn Hàng Thống Nhất (Unified Orders)

### Đơn Hàng Bán Cho Khách Hàng (Sales Order)
```sql
-- Tạo đơn bán cho khách hàng
INSERT INTO orders (
    order_code, order_type, customer_id, warehouse_id, user_id, 
    order_date, expected_delivery_date, status, total_amount, payment_status
) VALUES (
    'SO001', 'sales_order', 1, 1, 1,
    '2026-03-30', '2026-04-10', 'pending', 5000000, 'unpaid'
);

-- Thêm chi tiết order
INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
VALUES (1, 1, 10, 500000, 5000000);
```

**Status cho sales_order**:
- `pending`: Đợi xử lý
- `processing`: Đang chuẩn bị
- `ready`: Sẵn sàng lấy hàng
- `shipped`: Đã gửi
- `delivered`: Đã giao
- `returned`: Trả hàng
- `cancelled`: Hủy

### Đơn Hàng Mua Từ Nhà Cung Cấp (Purchase Order)
```sql
-- Tạo đơn mua từ supplier
INSERT INTO orders (
    order_code, order_type, supplier_id, warehouse_id, user_id, 
    order_date, expected_delivery_date, status, total_amount, payment_status
) VALUES (
    'PO001', 'purchase_order', 1, 1, 1,
    '2026-03-30', '2026-04-15', 'confirmed', 3500000, 'unpaid'
);

-- Thêm chi tiết order
INSERT INTO order_items (order_id, product_id, quantity, unit_price, line_total)
VALUES (1, 1, 10, 350000, 3500000);
```

**Status cho purchase_order**:
- `draft`: Nháp chưa gửi
- `sent`: Đã gửi cho supplier
- `confirmed`: Supplier xác nhận
- `partial_received`: Nhận một phần
- `received`: Nhận đủ hàng
- `cancelled`: Hủy

### Truy Vấn Đơn Hàng
```sql
-- Tất cả đơn bán chưa giao
SELECT * FROM orders 
WHERE order_type = 'sales_order' AND status != 'delivered' AND status != 'cancelled'
ORDER BY created_at DESC;

-- Tất cả đơn mua chưa nhận đủ
SELECT * FROM orders 
WHERE order_type = 'purchase_order' AND status != 'received' AND status != 'cancelled'
ORDER BY created_at DESC;

-- Đơn chưa thanh toán
SELECT o.*, CASE WHEN o.order_type = 'sales_order' THEN c.customer_name 
       ELSE s.supplier_name END as party_name
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.id
LEFT JOIN suppliers s ON o.supplier_id = s.id
WHERE o.payment_status IN ('unpaid', 'partial')
ORDER BY o.created_at DESC;
```

### Ưu Điểm Của Unified Orders
✅ **Một bảng duy nhất** - Dễ quản lý và truy vấn chung  
✅ **Linh hoạt** - Dễ dàng thêm các loại đơn hàng mới (transfer orders, etc.)  
✅ **Nhất quán** - Cùng một cấu trúc để tính toán, báo cáo  
✅ **Hiệu suất** - Tối ưu index và query planning  
✅ **Backward compatible** - Giữ lại purchase_orders để transition dần

---

## 🎯 Hệ Thống Quản Lý Batch Nhập Hàng (Product Batches) - MỚI V2.0

### Khái Niệm Batch
Mỗi lô hàng (batch) đại diện cho một lần nhập hàng từ nhà cung cấp có:
- Mã lô riêng (batch_code)
- Giá vốn riêng (unit_cost)
- Ngày nhập riêng (import_date)
- Hạn sử dụng (expiry_date) - nếu có
- Trạng thái (status)

### Ưu Điểm So Với Product Variants
| Feature | Product Variants | Product Batches |
|---------|-----------------|-----------------|
| **Mục đích** | Size, màu, loại | Lô nhập hàng |
| **Số lượng** | Cố định trên sản phẩm | Linh hoạt theo nhập |
| **Giá** | Có thể khác | Khác nhau mỗi lô |
| **Tracking** | SKU variant | Batch code |
| **FIFO/LIFO** | Khó | Dễ |
| **Hạn sử dụng** | Không hỗ trợ | Hỗ trợ tốt |
| **COGS** | Khó tính | Dễ, chính xác |

### Ví Dụ Sử Dụng Product Batches

#### 1. Nhập hàng từ nhà cung cấp
```sql
-- Nhập 100 cái sản phẩm ID 1 từ supplier ID 1
INSERT INTO product_batches (
    batch_code, product_id, supplier_id,
    quantity_imported, quantity_available, unit_cost, unit_price,
    import_date, status, notes
) VALUES (
    'BATCH-2026-001-SUP01', 1, 1,
    100, 100, 50000, 75000,
    '2026-03-30', 'active', 'Nhập từ supplier A, giầu Good'
);
```

#### 2. Xem các batch của sản phẩm
```sql
-- Lấy tất cả batch của product ID 1
SELECT * FROM product_batches WHERE product_id = 1 AND status = 'active';

-- Batch gần hết hàng (< 20 cái)
SELECT * FROM product_batches WHERE product_id = 1 AND quantity_available < 20;

-- Batch sắp hết hạn (7 ngày nữa)
SELECT * FROM product_batches 
WHERE product_id = 1 
AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
AND status = 'active';
```

#### 3. Xuất hàng theo batch (FIFO)
```sql
-- Khi xuất hàng, lấy batch sớm nhất
SELECT * FROM product_batches 
WHERE product_id = 1 
AND status = 'active' 
AND quantity_available > 0
ORDER BY import_date ASC
LIMIT 1;

-- Sau đó cập nhật tồn kho batch
UPDATE product_batches 
SET quantity_available = quantity_available - 10
WHERE id = 1;
```

#### 4. Theo dõi COGS (Cost of Goods Sold)
```sql
-- Tính COGS cho đơn hàng dựa trên batch cost
SELECT oi.*, pb.unit_cost as batch_cost, (oi.order_quantity * pb.unit_cost) as inventory_cost
FROM order_items oi
JOIN product_batches pb ON oi.batch_id = pb.id
WHERE oi.order_id = 1;

-- Tính lợi nhuận thực tế
SELECT oi.line_total - (oi.order_quantity * pb.unit_cost) as actual_profit
FROM order_items oi
JOIN product_batches pb ON oi.batch_id = pb.id
WHERE oi.order_id = 1;
```

#### 5. Sử dụng Eloquent Model
```php
// Lấy batch theo product
$batches = Product::find(1)->batches()->active()->get();

// Lấy batch gần hết hàng
$lowStock = ProductBatch::byProduct(1)->where('quantity_available', '<', 10)->get();

// Tính margin của batch
$batch = ProductBatch::find(1);
$margin = $batch->calculateMargin(); // Tính % lợi nhuận

// Kiểm tra batch còn sẵn không
if ($batch->isAvailable()) {
    // Có thể bán
}

// Lấy giá bán của batch
$sellingPrice = $batch->getSellingPrice(); // unit_price ?? unit_cost
```

### Tính Năng Batch Management
✅ **FIFO/LIFO** - Theo dõi lô hàng theo ngày nhập  
✅ **Giá Vốn Chính Xác** - Mỗi lô có giá khác nhau  
✅ **Hạn Sử Dụng** - Quan trọng cho hàng tươi/hết hạn  
✅ **Báo Cáo COGS** - Tính toán lợi nhuận chính xác  
✅ **Automatic Expiry Alerts** - Thông báo hàng sắp hết hạn  
✅ **Warehouse Tracking** - Biết batch nào ở kho nào  

---


### Tạo cơ sở dữ liệu mới
```bash
php artisan migrate:fresh
```

### Chỉ chạy migration chưa chạy
```bash
php artisan migrate
```

### Rollback
```bash
php artisan migrate:rollback
```

### Xem trạng thái migration
```bash
php artisan migrate:status
```

---

## 📋 Thứ Tự Thực Thi Migration

Các migration được chạy theo thứ tự timetamp (tự động). Thứ tự logic:

1. **Laravel defaults** (0001_01_01_000000 - 0001_01_01_000002)
2. **Auth & Roles** (2026_03_30_055822)
3. **Organization** (2026_03_30_055844 - 055846)
4. **Product Catalog** (2026_03_30_055856 - 055857)
5. **Orders & Quotations** (2026_03_30_055926 - 055928)
6. **Inventory** (2026_03_30_055934 - 055935)
7. **Warehouse Sections** (2026_03_30_060802)
8. **Foreign Keys** (2026_03_30_062210)
9. **Quotations Enhancement** (2026_03_30_163741) ← Hỗ trợ báo giá cho supplier
10. **Orders Refactor** (2026_03_30_173741) ← Unified orders (sales + purchase)
11. **Drop Purchase Orders** (2026_03_30_183741) ← Xóa purchase_orders table

---

## ⚙️ Các Tính Năng Đặc Biệt

### 1. Kiểm soát quyền hạn (RBAC)
- Hệ thống role/permission cho phép kiểm soát granular từng chức năng
- Linh hoạt thêm/sửa quyền mà không cần code

### 2. Theo dõi tồn kho chi tiết
- Cấp độ kho, khu vực (zone/rack/shelf/cage/bin)
- Theo dõi dự trữ vs sẵn sàng
- Audit trail đầy đủ qua inventory_transactions

### 3. Danh mục đa cấp
- Categories hỗ trợ phân cấp (cha-con) tùy ý
- Có thể tạo cây danh mục sâu

### 4. Quản lý biến thể
- Mỗi sản phẩm có thể có nhiều biến thể (size, color, etc.)
- Mỗi biến thể có SKU, giá riêng

### 5. Báo giá -> Đơn hàng
- Báo giá có thể convert sang đơn hàng thực
- Hỗ trợ báo giá cho cả customer (sales_quotation) và supplier (supplier_quotation)
- Quản lý vòng đời từ draft → sent → accepted → order

### 6. Hệ thống Đơn Hàng Thống Nhất
- Một bảng `orders` cho cả đơn bán (sales_order) và đơn mua (purchase_order)
- Dễ dàng mở rộng cho các loại đơn mới (transfer orders, etc.)
- Status gộp hợp lý cho cả 2 loại
- Truy vấn chung, báo cáo thống nhất

### 7. Quản lý chất lượng
- PO items theo dõi QC pass/fail
- Có thể nhập một phần, kiểm tra chất lượng

### 8. Audit & Compliance
- Ghi lại mọi hành động user (view, create, update, delete)
- JSON để lưu old/new values chi tiết
- Theo dõi IP, User Agent

---

## 📊 Ví Dụ Truy Vấn Thường Dùng

### Tồn kho theo sản phẩm
```sql
SELECT p.product_name, pv.variant_name, i.quantity_on_hand, 
       i.quantity_reserved, i.quantity_available, w.warehouse_name
FROM inventory i
JOIN products p ON i.product_id = p.id
LEFT JOIN product_variants pv ON i.variant_id = pv.id
JOIN warehouses w ON i.warehouse_id = w.id
ORDER BY p.product_name;
```

### Lịch sử biến động kho
```sql
SELECT it.transaction_date, it.transaction_type, 
       p.product_name, it.quantity_change, 
       it.reference_type, u.name
FROM inventory_transactions it
JOIN products p ON it.product_id = p.id
JOIN users u ON it.user_id = u.id
WHERE it.warehouse_id = ? 
ORDER BY it.transaction_date DESC;
```

### Đơn hàng chưa thanh toán
```sql
SELECT o.order_code, 
       CASE WHEN o.order_type = 'sales_order' THEN c.customer_name 
            ELSE s.supplier_name END as party_name,
       o.order_type, o.total_amount, 
       o.paid_amount, (o.total_amount - o.paid_amount) as remain
FROM orders o
LEFT JOIN customers c ON o.customer_id = c.id AND o.order_type = 'sales_order'
LEFT JOIN suppliers s ON o.supplier_id = s.id AND o.order_type = 'purchase_order'
WHERE o.payment_status != 'paid'
ORDER BY o.created_at DESC;
```

### Thống kê đơn hàng theo loại
```sql
-- Tổng doanh số bán theo khách hàng
SELECT c.customer_name, COUNT(*) as total_orders, SUM(o.total_amount) as total_revenue
FROM orders o
JOIN customers c ON o.customer_id = c.id
WHERE o.order_type = 'sales_order'
GROUP BY o.customer_id
ORDER BY total_revenue DESC;

-- Tổng chi phí mua theo supplier
SELECT s.supplier_name, COUNT(*) as total_orders, SUM(o.total_amount) as total_spent
FROM orders o
JOIN suppliers s ON o.supplier_id = s.id
WHERE o.order_type = 'purchase_order'
GROUP BY o.supplier_id
ORDER BY total_spent DESC;
```

---

## 🔒 Bảo Mật & Tối Ưu

- **Charset**: utf8mb4 hỗ trợ emoji, ký tự đặc biệt
- **Collation**: utf8mb4_unicode_ci (không phân biệt hoa/thường)
- **Foreign Keys**: ON DELETE CASCADE/RESTRICT/SET NULL theo logic
- **Indexes**: Trên các cột tìm kiếm thường xuyên
- **Transactions**: JSON audit_logs, inventory_transactions cho revision history

---

## 📞 Hỗ Trợ

Để biết thêm chi tiết, tham khảo:
- Các file migration trong `database/migrations/`
- Model Eloquent tương ứng trong `app/Models/`
- API Endpoints trong `routes/api.php`

**Tạo ngày**: 30/03/2026  
**Phiên bản CSDL**: 1.0
