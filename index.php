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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container main-container py-4">
  <div class="header-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
          <h1 class="brand-title-with-emoji">🍽️ <span class="gradient-text">RESTO FAMILY</span></h1>
          <div>
              <a class="btn btn-outline-secondary" href="menu.php">📋 Kelola Menu</a>
          </div>
      </div>
  </div>
  
  <div class="main-box">
    <div class="row g-4">
      <!-- NASI -->
      <div class="col-lg-4">
        <div class="category-card">
          <h6 class="category-title">🍚 Nasi</h6>
          <div id="nasiList">
            <?php foreach ($menu['nasi'] as $m): ?>
              <div class="menu-item-row">
                <div class="row g-2 align-items-center">
                  <div class="col-1">
                    <input class="form-check-input nasiCheck" type="checkbox"
                          value="<?= (int)$m['id'] ?>" id="nasi-<?= (int)$m['id'] ?>"
                          data-price="<?= (int)$m['price'] ?>" data-name="<?= e($m['name']) ?>">
                  </div>
                  <div class="col-7">
                    <label class="menu-label" for="nasi-<?= (int)$m['id'] ?>">
                      <?= e($m['name']) ?>
                    </label>
                    <div class="price-badge mt-1"><?= rupiah((int)$m['price']) ?></div>
                  </div>
                  <div class="col-4">
                    <input type="number" min="0" value="0" class="form-control form-control-sm nasiQty" 
                          data-id="<?= (int)$m['id'] ?>" disabled>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- LAUK -->
      <div class="col-lg-4">
        <div class="category-card">
          <h6 class="category-title">🍗 Lauk</h6>
          <div id="laukList">
            <?php foreach ($menu['lauk'] as $m): ?>
              <div class="menu-item-row">
                <div class="row g-2 align-items-center">
                  <div class="col-1">
                    <input class="form-check-input laukCheck" type="checkbox"
                          value="<?= (int)$m['id'] ?>" id="lauk-<?= (int)$m['id'] ?>"
                          data-price="<?= (int)$m['price'] ?>" data-name="<?= e($m['name']) ?>">
                  </div>
                  <div class="col-7">
                    <label class="menu-label" for="lauk-<?= (int)$m['id'] ?>">
                      <?= e($m['name']) ?>
                    </label>
                    <div class="price-badge mt-1"><?= rupiah((int)$m['price']) ?></div>
                  </div>
                  <div class="col-4">
                    <input type="number" min="0" value="0" class="form-control form-control-sm laukQty" 
                          data-id="<?= (int)$m['id'] ?>" disabled>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- MINUM -->
      <div class="col-lg-4">
        <div class="category-card">
          <h6 class="category-title">🥤 Minuman</h6>
          <div id="minumList">
            <?php foreach ($menu['minum'] as $m): ?>
              <div class="menu-item-row">
                <div class="row g-2 align-items-center">
                  <div class="col-1">
                    <input class="form-check-input minumCheck" type="checkbox"
                          value="<?= (int)$m['id'] ?>" id="minum-<?= (int)$m['id'] ?>"
                          data-price="<?= (int)$m['price'] ?>" data-name="<?= e($m['name']) ?>">
                  </div>
                  <div class="col-7">
                    <label class="menu-label" for="minum-<?= (int)$m['id'] ?>">
                      <?= e($m['name']) ?>
                    </label>
                    <div class="price-badge mt-1"><?= rupiah((int)$m['price']) ?></div>
                  </div>
                  <div class="col-4">
                    <input type="number" min="0" value="0" class="form-control form-control-sm minumQty" 
                          data-id="<?= (int)$m['id'] ?>" disabled>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="action-buttons">
      <button id="addBtn" class="btn btn-primary">Tambah ke Pesanan</button>
      <button id="resetFormBtn" class="btn btn-outline-secondary">🔄 Reset Pilihan</button>
    </div>

    <hr>

    <!-- TABEL KERANJANG -->
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Item</th>
            <th class="text-end">Harga</th>
            <th class="text-center">Jumlah</th>
            <th class="text-end">Sub Total</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody id="cartBody">
          <tr class="empty-state"><td colspan="5">🛒 Belum ada pesanan</td></tr>
        </tbody>
      </table>
    </div>

    <!-- PEMBAYARAN -->
    <div class="row g-4">
      <div class="col-md-6">
        <div class="payment-card">
          <h6 class="section-title">💳 Pembayaran</h6>
          <div class="mb-3">
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payMethod" id="payCash" value="cash" checked>
              <label class="form-check-label" for="payCash">💵 Cash</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="payMethod" id="payCard" value="card">
              <label class="form-check-label" for="payCard">💳 Visa/Debit</label>
            </div>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label fw-semibold">Uang Diterima</label>
              <input type="number" min="0" value="0" id="cashInput" class="form-control" placeholder="0">
            </div>
            <div class="col-12">
              <label class="form-label fw-semibold">Catatan (opsional)</label>
              <input type="text" id="note" class="form-control" placeholder="Tambahkan catatan...">
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-md-6">
        <div class="summary-card">
          <h6 class="section-title">📊 Ringkasan</h6>
          <div class="summary-row">
            <span>Total Pesanan</span>
            <strong id="totalLabel">Rp 0</strong>
          </div>
          <div class="summary-row">
            <span>PPN (10%)</span>
            <strong id="ppnLabel">Rp 0</strong>
          </div>
          <div class="summary-row total">
            <span>Total Bayar</span>
            <strong id="grandLabel">Rp 0</strong>
          </div>
          <div class="summary-row">
            <span>Uang Kembali</span>
            <strong id="changeLabel" style="color: #38ef7d;">Rp 0</strong>
          </div>
          <div class="mt-4 d-flex gap-2">
            <button id="saveBtn" class="btn btn-success flex-fill">💾 Simpan Order</button>
            <button id="clearBtn" class="btn btn-outline-danger">🗑️ Bersihkan</button>
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
  let cart = [];
  const rupiah = (n) => 'Rp ' + (n||0).toLocaleString('id-ID');

  function readSel() {
    const items = [];
    ['nasi', 'lauk', 'minum'].forEach(cat => {
      document.querySelectorAll(`.${cat}Check:checked`).forEach(check => {
        const id = parseInt(check.value,10);
        const qtyInput = document.querySelector(`.${cat}Qty[data-id="${id}"]`);
        const qty = Math.max(0, parseInt(qtyInput.value||'0',10));
        if (qty > 0) {
          const price = parseInt(check.getAttribute('data-price'),10);
          items.push({
            id, 
            name: check.getAttribute('data-name'), 
            price, 
            qty,
            type: cat, 
            subtotal: price * qty
          });
        }
      });
    });
    return items;
  }

  function addToCart() {
    const sel = readSel();
    if (sel.length === 0) { 
      alert('⚠️ Pilih minimal satu item dengan jumlah > 0'); 
      return; 
    }
    
    sel.forEach(newItem => {
      // Cari apakah item sudah ada di keranjang
      const existingItemIndex = cart.findIndex(item => 
        item.id === newItem.id && item.type === newItem.type
      );
      
      if (existingItemIndex !== -1) {
        // Jika item sudah ada, tambahkan quantity
        cart[existingItemIndex].qty += newItem.qty;
        cart[existingItemIndex].subtotal = cart[existingItemIndex].price * cart[existingItemIndex].qty;
      } else {
        // Jika item belum ada, tambahkan sebagai item baru
        cart.push(newItem);
      }
    });
    
    renderCart();
    resetFormOnly();
  }

  function removeRow(i){ 
    if (confirm('🗑️ Hapus item ini dari keranjang?')) {
      cart.splice(i,1); 
      renderCart(); 
    }
  }

  function renderCart(){
    const body = document.getElementById('cartBody');
    body.innerHTML = '';
    if (cart.length === 0) {
      body.innerHTML = '<tr class="empty-state"><td colspan="5">🛒 Belum ada pesanan</td></tr>';
    } else {
      cart.forEach((r,i)=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td><strong>${r.name}</strong></td>
          <td class="text-end">${rupiah(r.price)}</td>
          <td class="text-center">${r.qty}</td>
          <td class="text-end"><strong>${rupiah(r.subtotal)}</strong></td>
          <td class="text-center"><button class="btn btn-sm btn-outline-danger" data-i="${i}">Hapus</button></td>`;
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
    if (payMethod==='card'){ 
      document.getElementById('cashInput').disabled=true; 
      cash = grand; 
    } else { 
      document.getElementById('cashInput').disabled=false; 
    }
    const change = Math.max(0, cash - grand);
    document.getElementById('totalLabel').textContent = rupiah(total);
    document.getElementById('ppnLabel').textContent = rupiah(ppn);
    document.getElementById('grandLabel').textContent = rupiah(grand);
    document.getElementById('changeLabel').textContent = rupiah(change);
  }

  function resetFormOnly(){
    document.querySelectorAll('.nasiCheck, .laukCheck, .minumCheck').forEach(check => {
      check.checked = false;
      const id = check.value;
      let qtyInput = document.querySelector(`.${check.classList.contains('nasiCheck')?'nasi':check.classList.contains('laukCheck')?'lauk':'minum'}Qty[data-id="${id}"]`);
      qtyInput.value = 0;
      qtyInput.disabled = true;
    });  
  }

  function clearAll(){ 
    cart=[]; 
    renderCart(); 
    document.getElementById('cashInput').value=0; 
    document.getElementById('note').value=''; 
    document.getElementById('payCash').checked=true; 
    recalcSummary(); 
  }

  document.querySelectorAll('.nasiCheck, .laukCheck, .minumCheck').forEach(check => {
    check.addEventListener('change', function() {
      const id = this.value;
      const cat = this.classList.contains('nasiCheck')?'nasi':this.classList.contains('laukCheck')?'lauk':'minum';
      const qtyInput = document.querySelector(`.${cat}Qty[data-id="${id}"]`);
      qtyInput.disabled = !this.checked;
      if (!this.checked) qtyInput.value = 0;
    });
  });

  document.getElementById('addBtn').onclick=addToCart;
  document.getElementById('resetFormBtn').onclick=resetFormOnly;
  document.getElementById('clearBtn').onclick=clearAll;
  document.getElementsByName('payMethod').forEach?.(r=>r.addEventListener('change',recalcSummary));
  document.getElementById('cashInput').addEventListener('input',recalcSummary);

  document.getElementById('saveBtn').onclick=()=>{
    if (cart.length===0){ alert('⚠️ Keranjang kosong'); return; }
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