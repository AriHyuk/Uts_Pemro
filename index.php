<?php
require __DIR__ . '/koneksi.php';
require __DIR__ . '/functions.php';
$menu = get_menu($mysqli);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Resto Family — Order</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>body{background:#f6f7f9} .box{background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.05)}</style>
</head>
<body>
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="m-0">RESTO FAMILY</h3>
    <div>
      <a class="btn btn-outline-secondary" href="menu.php">Kelola Menu</a>
    </div>
  </div>
  <div class="box p-4">

    <div class="row g-4">
      <!-- NASI -->
      <div class="col-lg-4">
        <div class="border rounded p-3 h-100">
          <h6 class="mb-3">Nasi</h6>
          <label class="form-label">Pilih Nasi</label>
          <select id="nasiSelect" class="form-select">
            <option value="">-- pilih --</option>
            <?php foreach ($menu['nasi'] as $m): ?>
              <option value="<?= (int)$m['id'] ?>" data-price="<?= (int)$m['price'] ?>">
                <?= e($m['name']) ?> (<?= rupiah((int)$m['price']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <label class="form-label mt-2">Jumlah</label>
          <input type="number" min="0" value="0" id="nasiQty" class="form-control">
        </div>
      </div>

      <!-- LAUK -->
      <div class="col-lg-4">
        <div class="border rounded p-3 h-100">
          <h6 class="mb-3">Lauk</h6>
          <div class="mb-2" id="laukList">
            <?php foreach ($menu['lauk'] as $m): ?>
              <div class="row g-2 align-items-center mb-2">
                <div class="col-1">
                  <input class="form-check-input laukCheck" type="checkbox"
                        value="<?= (int)$m['id'] ?>" id="lauk-<?= (int)$m['id'] ?>"
                        data-price="<?= (int)$m['price'] ?>" data-name="<?= e($m['name']) ?>">
                </div>
                <div class="col-7">
                  <label class="form-check-label" for="lauk-<?= (int)$m['id'] ?>">
                    <?= e($m['name']) ?> (<?= rupiah((int)$m['price']) ?>)
                  </label>
                </div>
                <div class="col-4">
                  <input type="number" min="0" value="0" class="form-control form-control-sm laukQty" 
                        data-id="<?= (int)$m['id'] ?>" disabled>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- MINUM -->
      <div class="col-lg-4">
        <div class="border rounded p-3 h-100">
          <h6 class="mb-3">Minum</h6>
          <div class="mb-2" id="minumList">
            <?php foreach ($menu['minum'] as $m): ?>
              <div class="row g-2 align-items-center mb-2">
                <div class="col-1">
                  <input class="form-check-input minumCheck" type="checkbox"
                        value="<?= (int)$m['id'] ?>" id="minum-<?= (int)$m['id'] ?>"
                        data-price="<?= (int)$m['price'] ?>" data-name="<?= e($m['name']) ?>">
                </div>
                <div class="col-7">
                  <label class="form-check-label" for="minum-<?= (int)$m['id'] ?>">
                    <?= e($m['name']) ?> (<?= rupiah((int)$m['price']) ?>)
                  </label>
                </div>
                <div class="col-4">
                  <input type="number" min="0" value="0" class="form-control form-control-sm minumQty" 
                        data-id="<?= (int)$m['id'] ?>" disabled>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="text-end mt-3">
      <button id="addBtn" class="btn btn-primary">Tambah ke Pesanan</button>
      <button id="resetFormBtn" class="btn btn-outline-secondary">Reset Pilihan</button>
    </div>

    <hr class="my-4">

    <!-- TABEL KERANJANG -->
    <div class="table-responsive">
      <table class="table table-sm align-middle">
        <thead class="table-light">
          <tr>
            <th>Nasi</th><th>Jml</th>
            <th>Lauk</th><th>Jml</th>
            <th>Minum</th><th>Jml</th>
            <th class="text-end">Sub Total</th><th></th>
          </tr>
        </thead>
        <tbody id="cartBody">
          <tr class="text-center text-muted" id="emptyRow"><td colspan="8">Belum ada pesanan.</td></tr>
        </tbody>
      </table>
    </div>

    <!-- PEMBAYARAN -->
    <div class="row g-4">
      <div class="col-md-6">
        <div class="border rounded p-3">
          <h6 class="mb-3">Pembayaran</h6>
          <div class="mb-3">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payMethod" id="payCash" value="cash" checked>
              <label class="form-check-label" for="payCash">Cash</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payMethod" id="payCard" value="card">
              <label class="form-check-label" for="payCard">Visa/Debit</label>
            </div>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <label class="form-label">Uang Diterima (Cash)</label>
              <input type="number" min="0" value="0" id="cashInput" class="form-control">
            </div>
            <div class="col-6">
              <label class="form-label">Catatan</label>
              <input type="text" id="note" class="form-control" placeholder="opsional">
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="border rounded p-3">
          <h6 class="mb-3">Ringkasan</h6>
          <div class="d-flex justify-content-between"><span>Total Pesanan</span><strong id="totalLabel">Rp0</strong></div>
          <div class="d-flex justify-content-between"><span>PPN (10%)</span><strong id="ppnLabel">Rp0</strong></div>
          <hr>
          <div class="d-flex justify-content-between"><span>Total Bayar</span><strong id="grandLabel">Rp0</strong></div>
          <div class="d-flex justify-content-between"><span>Uang Kembali</span><strong id="changeLabel">Rp0</strong></div>
          <div class="mt-3 text-end">
            <button id="saveBtn" class="btn btn-success">Simpan Order</button>
            <button id="clearBtn" class="btn btn-outline-danger">Bersihkan</button>
          </div>
        </div>
      </div>
    </div>

    <form id="saveForm" action="order_save.php" method="post" class="d-none">
      <input type="hidden" name="cart" id="cartInput">
      <input type="hidden" name="pay_method" id="payMethodInput">
      <input type="hidden" name="cash" id="cashInputHidden">
      <input type="hidden" name="note" id="noteInput">
    </form>

  </div>
</div>

<script>
  let cart = []; // tiap baris: { nasiId, nasiName, nasiQty, laukIds[], laukNames[], laukQty, minumIds[], minumNames[], minumQty, subtotal }

  const rupiah = (n) => 'Rp' + (n||0).toLocaleString('id-ID');

  function readSel() {
    const nasiSel = document.getElementById('nasiSelect');
    const nasiId  = nasiSel.value ? parseInt(nasiSel.value,10) : null;
    const nasiName= nasiSel.options[nasiSel.selectedIndex]?.text?.split(' (')[0] || '';
    const nasiQty = Math.max(0, parseInt(document.getElementById('nasiQty').value||'0',10));
    const laukItems = [];
    document.querySelectorAll('.laukCheck:checked').forEach(check => {
      const id = parseInt(check.value,10);
      const qtyInput = document.querySelector(`.laukQty[data-id="${id}"]`);
      const qty = Math.max(0, parseInt(qtyInput.value||'0',10));
      if (qty > 0) {
        laukItems.push({
          id: id,
          name: check.getAttribute('data-name'),
          qty: qty
        });
      }
    });
    const minumItems = [];
    document.querySelectorAll('.minumCheck:checked').forEach(check => {
      const id = parseInt(check.value,10);
      const qtyInput = document.querySelector(`.minumQty[data-id="${id}"]`);
      const qty = Math.max(0, parseInt(qtyInput.value||'0',10));
      if (qty > 0) {
        minumItems.push({
          id: id,
          name: check.getAttribute('data-name'),
          qty: qty
        });
      }
    });
    return {nasiId,nasiName,nasiQty,laukItems,minumItems};
  }

  function calcSubtotal(sel) {
    let sub = 0;
    if (sel.nasiId && sel.nasiQty>0) {
      const price = parseInt(document.querySelector(`#nasiSelect option[value="${sel.nasiId}"]`).dataset.price,10);
      sub += price * sel.nasiQty;
    }
    sel.laukItems.forEach(item => {
      const price = parseInt(document.querySelector(`#lauk-${item.id}`).dataset.price,10);
      sub += price * item.qty;
    });
    sel.minumItems.forEach(item => {
      const price = parseInt(document.querySelector(`#minum-${item.id}`).dataset.price,10);
      sub += price * item.qty;
    });
    return sub;
  }

  function addToCart() {
    const sel = readSel();
    const hasAny = (sel.nasiId && sel.nasiQty>0) || sel.laukItems.length > 0 || sel.minumItems.length > 0;
    if (!hasAny) { alert('Pilih minimal satu item dengan jumlah > 0'); return; }
    sel.subtotal = calcSubtotal(sel);
    cart.push(sel);
    renderCart();
    resetFormOnly();
  }

  function removeRow(i){ cart.splice(i,1); renderCart(); }

  function renderCart(){
    const body = document.getElementById('cartBody');
    body.innerHTML = '';
    if (cart.length === 0) {
      body.innerHTML = '<tr class="text-center text-muted" id="emptyRow"><td colspan="8">Belum ada pesanan.</td></tr>';
    } else {
      cart.forEach((r,i)=>{
        const tr = document.createElement('tr');
        const laukDisplay = r.laukItems.map(item => item.name).join(', ') || '-';
        const laukTotalQty = r.laukItems.reduce((sum, item) => sum + item.qty, 0);
        const minumDisplay = r.minumItems.map(item => item.name).join(', ') || '-';
        const minumTotalQty = r.minumItems.reduce((sum, item) => sum + item.qty, 0);
        tr.innerHTML = `
          <td>${r.nasiName||'-'}</td><td>${r.nasiQty||0}</td>
          <td>${laukDisplay}</td><td>${r.laukTotalQty||0}</td>
          <td>${minumDisplay}</td><td>${r.minumTotalQty||0}</td>
          <td class="text-end">${rupiah(r.subtotal)}</td>
          <td class="text-end"><button class="btn btn-sm btn-outline-danger" data-i="${i}">Hapus</button></td>`;
        body.appendChild(tr);
      });
      body.querySelectorAll('button[data-i]').forEach(b=>b.onclick=()=>removeRow(parseInt(b.dataset.i,10)));
    }
    recalcSummary();
  }

  function recalcSummary(){
    const total = cart.reduce((s,r)=>s+r.subtotal,0);
    const ppn = Math.round(total*0.10);
    const grand = total+ppn;
    const payMethod = document.querySelector('input[name="payMethod"]:checked').value;
    let cash = Math.max(0, parseInt(document.getElementById('cashInput').value||'0',10));
    if (payMethod==='card'){ document.getElementById('cashInput').disabled=true; cash = grand; }
    else { document.getElementById('cashInput').disabled=false; }
    const change = Math.max(0, cash - grand);
    document.getElementById('totalLabel').textContent = rupiah(total);
    document.getElementById('ppnLabel').textContent = rupiah(ppn);
    document.getElementById('grandLabel').textContent = rupiah(grand);
    document.getElementById('changeLabel').textContent = rupiah(change);
  }

  function resetFormOnly(){
    document.getElementById('nasiSelect').value='';
    document.getElementById('nasiQty').value=0;
    document.querySelectorAll('.laukCheck, .minumCheck').forEach(check => {
      check.checked = false;
      const id = check.value;
      const qtyInput = check.classList.contains('laukCheck') 
        ? document.querySelector(`.laukQty[data-id="${id}"]`)
        : document.querySelector(`.minumQty[data-id="${id}"]`);
      qtyInput.value = 0;
      qtyInput.disabled = true;
    });  
  }
  function clearAll(){ cart=[]; renderCart(); 
    document.getElementById('cashInput').value=0; 
    document.getElementById('note').value=''; 
    document.getElementById('payCash').checked=true; 
    recalcSummary(); 
  }

  // Enable/disable qty input ketika checkbox dicentang
  document.querySelectorAll('.laukCheck, .minumCheck').forEach(check => {
    check.addEventListener('change', function() {
      const id = this.value;
      const qtyInput = this.classList.contains('laukCheck') 
        ? document.querySelector(`.laukQty[data-id="${id}"]`)
        : document.querySelector(`.minumQty[data-id="${id}"]`);
      qtyInput.disabled = !this.checked;
      if (!this.checked) qtyInput.value = 0;
    });
  });

  // Events
  document.getElementById('addBtn').onclick=addToCart;
  document.getElementById('resetFormBtn').onclick=resetFormOnly;
  document.getElementById('clearBtn').onclick=clearAll;
  document.getElementsByName('payMethod').forEach?.(r=>r.addEventListener('change',recalcSummary));
  document.getElementById('cashInput').addEventListener('input',recalcSummary);

  document.getElementById('saveBtn').onclick=()=>{
    if (cart.length===0){ alert('Keranjang kosong'); return; }
    // kirim ke server sebagai JSON
    document.getElementById('cartInput').value = JSON.stringify(cart);
    document.getElementById('payMethodInput').value = document.querySelector('input[name="payMethod"]:checked').value;
    document.getElementById('cashInputHidden').value = document.getElementById('cashInput').value||'0';
    document.getElementById('noteInput').value = document.getElementById('note').value||'';
    document.getElementById('saveForm').submit();
  };

  renderCart();
</script>
</body>
</html>