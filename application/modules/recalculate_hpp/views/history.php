<?php if (empty($history)): ?>
    <p class="text-muted small text-center py-2 mb-0">Belum ada riwayat sinkronisasi.</p>
<?php else: ?>
    <?php foreach ($history as $h): ?>
        <div class="history-row d-flex align-items-center justify-content-between py-2 border-bottom">
            <div class="d-flex align-items-center gap-2">
                <?php if ($h['status'] === 'done'): ?>
                    <i class="ri-checkbox-circle-fill text-success small"></i>
                <?php else: ?>
                    <i class="ri-close-circle-fill text-danger small"></i>
                <?php endif; ?>
                <div>
                    <div class="small fw-medium">
                        <?= date('d M Y, H:i', strtotime($h['created_at'])) ?>
                    </div>
                    <div class="text-muted" style="font-size:.75rem">
                        <?= htmlspecialchars($h['message'] ?? '-') ?>
                    </div>
                </div>
            </div>
            <!-- <?php if ($h['duration_sec']): ?>
                <span class="badge bg-light text-dark border small">
                    <?= $h['duration_sec'] ?>s
                </span>
            <?php endif; ?> -->
        </div>
    <?php endforeach; ?>
<?php endif; ?>