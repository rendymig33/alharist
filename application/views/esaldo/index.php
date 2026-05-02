<?php $flashData = flash(); ?>

<style>
    .esaldo-hero {
        background: linear-gradient(135deg, #ffffff 0%, #fff9eb 50%, #fff4d0 100%);
        border: 1px solid var(--line);
        border-radius: 20px;
        padding: 28px 24px;
        margin-top: 18px;
        box-shadow: 0 14px 30px rgba(28, 39, 60, .06);
        position: relative;
        overflow: hidden;
    }

    .esaldo-hero::before {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 160px;
        height: 160px;
        background: radial-gradient(circle, rgba(255, 213, 74, .3) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .esaldo-hero::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 30%;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(215, 25, 32, .06) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .esaldo-hero .hero-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: #98a2b3;
        margin-bottom: 8px;
    }

    .esaldo-hero .hero-title {
        font-size: 15px;
        font-weight: 600;
        color: #344054;
        margin: 0;
    }

    .esaldo-card-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin: 18px 0;
    }

    .esaldo-card {
        background: linear-gradient(135deg, #ffffff, #fffaf0);
        border: 1px solid var(--line);
        border-radius: 18px;
        padding: 16px;
        box-shadow: 0 12px 24px rgba(28, 39, 60, .05);
    }

    .esaldo-card-head {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        align-items: flex-start;
        margin-bottom: 14px;
    }

    .esaldo-card-balance {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.15;
        margin-bottom: 12px;
    }

    .esaldo-card .action-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .esaldo-card .action-row .btn {
        flex: 1;
    }

    .esaldo-modal {
        width: min(520px, 100%);
    }

    .esaldo-form-card {
        background: var(--white);
    }

    .esaldo-input-group {
        position: relative;
        margin-bottom: 16px;
    }

    .esaldo-input-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: #667085;
        margin-bottom: 8px;
    }

    .esaldo-input-group input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid var(--line);
        border-radius: 12px;
        font-size: 14px;
        box-sizing: border-box;
    }

    .esaldo-input-group input:focus {
        outline: none;
        border-color: var(--red);
        box-shadow: 0 0 0 3px rgba(215, 25, 32, .1);
    }

    .esaldo-balance-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .esaldo-currency-prefix {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        font-weight: 700;
        color: #98a2b3;
        pointer-events: none;
        z-index: 2;
    }

    .esaldo-input-group input[name="balance"] {
        padding-left: 42px;
    }

    .esaldo-empty {
        text-align: center;
        padding: 60px 20px;
        color: #98a2b3;
    }

    .esaldo-empty-icon {
        font-size: 40px;
        margin-bottom: 12px;
        opacity: .5;
    }

    .esaldo-empty p {
        margin: 0;
        font-size: 14px;
    }

    @media (max-width: 920px) {
        .esaldo-card-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 640px) {
        .esaldo-hero {
            padding: 20px 16px;
        }

        .esaldo-card-grid {
            grid-template-columns: 1fr;
        }

        .esaldo-card .action-row {
            grid-template-columns: 1fr 1fr;
        }

        .esaldo-modal {
            width: 100%;
        }
    }
</style>

<?php if (!empty($flashData)): ?>
    <div class="flash <?= htmlspecialchars((string) ($flashData['type'] ?? 'success')) ?>">
        <?= htmlspecialchars((string) ($flashData['message'] ?? '')) ?>
    </div>
<?php endif; ?>

<div class="toolbar">
    <div class="small">Kelola saldo E-Saldo Anda.</div>
    <button type="button" class="btn" onclick="toggleEsaldoModal(true)">Add E-Saldo</button>
</div>

<div class="card">
    <h3>Daftar E-Saldo</h3>
    <?php if (!empty($esaldos)): ?>
        <div class="esaldo-card-grid">
            <?php foreach ($esaldos as $esaldo): ?>
                <div class="esaldo-card">
                    <div class="esaldo-card-head">
                        <div>
                            <div class="section-title" style="margin-bottom:6px;"><?= htmlspecialchars((string) $esaldo['name']) ?></div>
                            <div class="small"><?= htmlspecialchars((string) ($esaldo['created_at'] ?? '-')) ?></div>
                        </div>
                    </div>
                    <div class="esaldo-card-balance"><?= rupiah((float) $esaldo['balance']) ?></div>
                    <div class="small" style="margin-bottom:14px;">Saldo aktif pada E-Saldo ini.</div>
                    <div class="action-row">
                        <button type="button" class="btn btn-secondary edit-esaldo-btn" data-id="<?= (int) $esaldo['id'] ?>" data-name="<?= htmlspecialchars((string) $esaldo['name']) ?>" data-balance="<?= htmlspecialchars(number_format((float) $esaldo['balance'], 0, ',', '.')) ?>">Edit</button>
                        <button type="button" class="btn btn-danger delete-esaldo-btn" data-id="<?= (int) $esaldo['id'] ?>">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="esaldo-empty">
            <div class="esaldo-empty-icon">📭</div>
            <p>Belum ada data saldo. Tambahkan saldo pertama Anda!</p>
        </div>
    <?php endif; ?>
</div>

<div class="modal-backdrop" id="esaldo-modal">
    <div class="modal esaldo-modal">
        <div class="modal-head">
            <h3 style="margin:0;" id="esaldo-modal-title">Add E-Saldo</h3>
            <button type="button" class="modal-close" onclick="toggleEsaldoModal(false)">Tutup</button>
        </div>
        <form method="post" class="esaldo-form" id="esaldo-form">
            <input type="hidden" name="action" value="save">
            <input type="hidden" name="id" id="esaldo-id">

            <div class="esaldo-input-group">
                <label for="esaldo-name">Nama / Keterangan</label>
                <input
                    type="text"
                    id="esaldo-name"
                    name="name"
                    placeholder="Contoh: Saldo BCA / Provider XYZ"
                    required
                    autocomplete="off">
            </div>

            <div class="esaldo-input-group">
                <label for="esaldo-balance">Nominal Saldo</label>
                <div class="esaldo-balance-wrap">
                    <span class="esaldo-currency-prefix">Rp</span>
                    <input
                        id="esaldo-balance"
                        name="balance"
                        inputmode="numeric"
                        placeholder="0"
                        required
                        autocomplete="off">
                </div>
            </div>

            <div style="margin-top:12px;"><button type="submit">Simpan Saldo</button></div>
        </form>
    </div>
</div>

<script>
    function toggleEsaldoModal(show) {
        const modal = document.getElementById('esaldo-modal');
        modal.classList.toggle('active', show);

        if (!show) {
            document.getElementById('esaldo-form').reset();
            document.getElementById('esaldo-id').value = '';
            document.getElementById('esaldo-modal-title').textContent = 'Add E-Saldo';
        }
    }

    (function() {
        const balanceInput = document.getElementById('esaldo-balance');
        const nameInput = document.getElementById('esaldo-name');

        if (balanceInput) {
            balanceInput.addEventListener('input', function() {
                const digits = this.value.replace(/[^\d]/g, '');
                this.value = digits === '' ? '' : Number(digits).toLocaleString('id-ID');
            });
        }

        document.getElementById('esaldo-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const button = this.querySelector('button[type="submit"]');
            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Menyimpan...';

            try {
                const response = await fetch('index.php?route=esaldo', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    const successMsg = document.createElement('div');
                    successMsg.className = 'alert alert-success';
                    successMsg.textContent = result.message;
                    document.body.appendChild(successMsg);

                    setTimeout(() => {
                        successMsg.remove();
                        window.location.reload();
                    }, 1500);
                } else {
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'alert alert-danger';
                    errorMsg.textContent = result.message;
                    document.body.appendChild(errorMsg);
                    setTimeout(() => errorMsg.remove(), 3000);
                }
            } catch (error) {
                const errorMsg = document.createElement('div');
                errorMsg.className = 'alert alert-danger';
                errorMsg.textContent = 'Terjadi kesalahan: ' + error.message;
                document.body.appendChild(errorMsg);
                setTimeout(() => errorMsg.remove(), 3000);
            } finally {
                button.disabled = false;
                button.textContent = originalText;
            }
        });

        document.querySelectorAll('.edit-esaldo-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const balance = this.dataset.balance;

                document.getElementById('esaldo-id').value = id;
                document.getElementById('esaldo-name').value = name;
                document.getElementById('esaldo-balance').value = balance;
                document.getElementById('esaldo-modal-title').textContent = 'Edit E-Saldo';
                toggleEsaldoModal(true);
            });
        });

        document.querySelectorAll('.delete-esaldo-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                if (!confirm('Hapus saldo ini?')) return;

                const id = this.dataset.id;
                const button = this;
                const originalText = button.textContent;
                button.disabled = true;
                button.textContent = 'Menghapus...';

                try {
                    const formData = new FormData();
                    formData.append('action', 'delete');
                    formData.append('id', id);

                    const response = await fetch('index.php?route=esaldo', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        const card = button.closest('.esaldo-card');
                        card.style.opacity = '0.5';
                        card.style.pointerEvents = 'none';

                        setTimeout(() => {
                            card.remove();
                            const successMsg = document.createElement('div');
                            successMsg.className = 'alert alert-success';
                            successMsg.textContent = result.message;
                            document.body.appendChild(successMsg);
                            setTimeout(() => successMsg.remove(), 3000);
                        }, 300);
                    } else {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'alert alert-danger';
                        errorMsg.textContent = result.message;
                        document.body.appendChild(errorMsg);
                        setTimeout(() => errorMsg.remove(), 3000);
                        button.disabled = false;
                        button.textContent = originalText;
                    }
                } catch (error) {
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'alert alert-danger';
                    errorMsg.textContent = 'Terjadi kesalahan: ' + error.message;
                    document.body.appendChild(errorMsg);
                    setTimeout(() => errorMsg.remove(), 3000);
                    button.disabled = false;
                    button.textContent = originalText;
                }
            });
        });
    }());
</script>