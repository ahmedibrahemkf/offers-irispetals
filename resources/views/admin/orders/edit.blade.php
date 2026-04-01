@extends('layouts.admin')
@section('title', 'تعديل الطلب')
@section('page_title', 'تعديل الطلب '.$order->order_number)
@section('content')
  @php
    $statusLabels = [
      'new' => 'جديد',
      'confirmed' => 'مؤكد',
      'in_progress' => 'قيد التنفيذ',
      'ready' => 'جاهز',
      'out_for_delivery' => 'خارج للتوصيل',
      'delivered' => 'تم التسليم',
      'cancelled' => 'ملغي',
      'returned' => 'مرتجع',
    ];
    $paymentLabels = [
      'unpaid' => 'غير مدفوع',
      'partial' => 'مدفوع جزئي',
      'paid' => 'مدفوع',
      'refunded' => 'مسترجع',
    ];
    $savedCollections = old('collector_ids')
      ? collect(old('collector_ids'))->map(function ($collectorId, $i) {
          return [
            'collector_id' => $collectorId,
            'amount' => old('collector_amounts.'.$i),
            'note' => old('collector_notes.'.$i),
          ];
        })->toArray()
      : $order->collections->map(fn ($row) => [
          'collector_id' => $row->collector_id,
          'amount' => $row->amount,
          'note' => $row->note,
        ])->toArray();

    if (count($savedCollections) === 0) {
      $savedCollections[] = ['collector_id' => '', 'amount' => '', 'note' => ''];
    }
  @endphp

  <form class="card grid grid-2" method="post" action="{{ route('admin.orders.update', $order) }}">
    @csrf
    @method('PUT')

    <div>
      <label>الحالة</label>
      <select class="select" name="status">
        @foreach(['new','confirmed','in_progress','ready','out_for_delivery','delivered','cancelled','returned'] as $status)
          <option value="{{ $status }}" @selected(old('status', $order->status) === $status)>{{ $statusLabels[$status] ?? $status }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>حالة الدفع</label>
      <select class="select" name="payment_status">
        @foreach(['unpaid','partial','paid','refunded'] as $payment)
          <option value="{{ $payment }}" @selected(old('payment_status', $order->payment_status) === $payment)>{{ $paymentLabels[$payment] ?? $payment }}</option>
        @endforeach
      </select>
      <div class="muted" style="margin-top:6px">يتم ضبطها تلقائيًا من إجمالي التحصيل إذا سجّلت التحصيلات أدناه.</div>
    </div>

    <div>
      <label>إجمالي المدفوع (يدوي)</label>
      <input class="input" id="amount_paid" type="number" step="0.01" min="0" name="amount_paid" value="{{ old('amount_paid', $order->amount_paid) }}">
      <div class="muted" style="margin-top:6px">إذا أدخلت تقسيم التحصيل بالأسفل سيتم اعتماد مجموع التحصيل تلقائيًا.</div>
    </div>

    <div>
      <label>إجمالي الطلب</label>
      <input class="input" id="order_total" type="number" step="0.01" value="{{ $order->amount_total }}" disabled>
      <div class="muted" style="margin-top:6px">المتبقي الحالي: {{ number_format((float) $order->amount_remaining, 2) }} ج</div>
    </div>

    <div>
      <label>الموظف المسؤول</label>
      <select class="select" name="assigned_staff_id">
        <option value="">—</option>
        @foreach($staff as $user)
          <option value="{{ $user->id }}" @selected((int) old('assigned_staff_id', $order->assigned_staff_id) === (int) $user->id)>{{ $user->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>الصنايعي</label>
      <select class="select" name="assigned_craftsman_id">
        <option value="">—</option>
        @foreach($craftsmen as $user)
          <option value="{{ $user->id }}" @selected((int) old('assigned_craftsman_id', $order->assigned_craftsman_id) === (int) $user->id)>{{ $user->name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label>تاريخ التسليم</label>
      <input class="input" type="date" name="delivery_date" value="{{ old('delivery_date', optional($order->delivery_date)->format('Y-m-d')) }}">
    </div>

    <div>
      <label>فترة التسليم</label>
      <input class="input" name="delivery_time_slot" value="{{ old('delivery_time_slot', $order->delivery_time_slot) }}">
    </div>

    <div style="grid-column:1/-1">
      <label>ملاحظات داخلية</label>
      <textarea name="internal_notes">{{ old('internal_notes', $order->internal_notes) }}</textarea>
    </div>

    <div style="grid-column:1/-1">
      <h3 style="margin:4px 0 8px">تقسيم التحصيل</h3>
      <div class="muted" style="margin-bottom:8px">يمكن توزيع تحصيل الطلب على أكثر من محصل. النظام يمنع تجاوز إجمالي الطلب.</div>

      <div id="collections_rows" class="grid">
        @foreach($savedCollections as $i => $row)
          <div class="card collection-row" style="padding:12px">
            <div class="grid grid-3">
              <div>
                <label>المحصل</label>
                <select class="select collector-id" name="collector_ids[]">
                  <option value="">اختر المحصل</option>
                  @foreach($collectors as $collector)
                    <option value="{{ $collector->id }}" @selected((string) ($row['collector_id'] ?? '') === (string) $collector->id)>{{ $collector->name }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label>المبلغ</label>
                <input class="input collector-amount" type="number" min="0" step="0.01" name="collector_amounts[]" value="{{ $row['amount'] ?? '' }}">
              </div>
              <div>
                <label>ملاحظة</label>
                <input class="input" name="collector_notes[]" value="{{ $row['note'] ?? '' }}" placeholder="اختياري">
              </div>
            </div>
            <div class="actions" style="margin-top:8px">
              <button class="btn btn-danger remove-collector-row" type="button">حذف السطر</button>
            </div>
          </div>
        @endforeach
      </div>

      <div class="actions" style="margin-top:8px">
        <button class="btn btn-soft" type="button" id="add_collector_row">إضافة محصل آخر</button>
      </div>

      <div class="card" style="margin-top:10px;padding:12px">
        <div><b>إجمالي التحصيل المسجل:</b> <span id="collections_total">0.00</span> ج</div>
        <div><b>إجمالي الطلب:</b> {{ number_format((float) $order->amount_total, 2) }} ج</div>
        <div id="collections_error" class="err" style="display:none;margin-top:8px"></div>
      </div>
    </div>

    <div class="actions" style="grid-column:1/-1">
      <button class="btn btn-primary" type="submit">حفظ التعديلات</button>
      <a class="btn btn-soft" href="{{ route('admin.orders.show', $order) }}">رجوع</a>
    </div>
  </form>

  <form style="margin-top:10px" method="post" action="{{ route('admin.orders.destroy', $order) }}" onsubmit="return confirm('تأكيد حذف الطلب؟')">
    @csrf
    @method('DELETE')
    <button class="btn btn-danger" type="submit">حذف الطلب</button>
  </form>

  <template id="collector_row_template">
    <div class="card collection-row" style="padding:12px">
      <div class="grid grid-3">
        <div>
          <label>المحصل</label>
          <select class="select collector-id" name="collector_ids[]">
            <option value="">اختر المحصل</option>
            @foreach($collectors as $collector)
              <option value="{{ $collector->id }}">{{ $collector->name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label>المبلغ</label>
          <input class="input collector-amount" type="number" min="0" step="0.01" name="collector_amounts[]">
        </div>
        <div>
          <label>ملاحظة</label>
          <input class="input" name="collector_notes[]" placeholder="اختياري">
        </div>
      </div>
      <div class="actions" style="margin-top:8px">
        <button class="btn btn-danger remove-collector-row" type="button">حذف السطر</button>
      </div>
    </div>
  </template>

  <script>
    (function () {
      const rowsWrap = document.getElementById('collections_rows');
      const addButton = document.getElementById('add_collector_row');
      const template = document.getElementById('collector_row_template');
      const totalNode = document.getElementById('collections_total');
      const errorNode = document.getElementById('collections_error');
      const amountPaid = document.getElementById('amount_paid');
      const orderTotal = Number(document.getElementById('order_total')?.value || 0);

      function parseAmount(value) {
        const n = Number(value);
        return Number.isFinite(n) ? n : 0;
      }

      function calcCollectionsTotal() {
        const amountInputs = rowsWrap.querySelectorAll('.collector-amount');
        let total = 0;
        amountInputs.forEach(function (input) {
          total += parseAmount(input.value);
        });
        totalNode.textContent = total.toFixed(2);
        amountPaid.value = total > 0 ? total.toFixed(2) : amountPaid.value;

        if (total > orderTotal) {
          errorNode.textContent = 'إجمالي التحصيل أكبر من إجمالي الطلب';
          errorNode.style.display = 'block';
        } else {
          errorNode.style.display = 'none';
          errorNode.textContent = '';
        }
      }

      function bindRowEvents(scope) {
        scope.querySelectorAll('.collector-amount').forEach(function (input) {
          input.addEventListener('input', calcCollectionsTotal);
        });

        scope.querySelectorAll('.remove-collector-row').forEach(function (button) {
          button.addEventListener('click', function () {
            const rows = rowsWrap.querySelectorAll('.collection-row');
            if (rows.length <= 1) {
              rows[0].querySelectorAll('input,select').forEach(function (el) {
                el.value = '';
              });
            } else {
              button.closest('.collection-row')?.remove();
            }
            calcCollectionsTotal();
          });
        });
      }

      addButton?.addEventListener('click', function () {
        const node = template.content.cloneNode(true);
        rowsWrap.appendChild(node);
        bindRowEvents(rowsWrap);
        calcCollectionsTotal();
      });

      bindRowEvents(rowsWrap);
      calcCollectionsTotal();
    })();
  </script>
@endsection

