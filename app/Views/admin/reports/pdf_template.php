<?php
/**
 * @var string $companyName
 * @var string $companyAddress
 * @var string $reportTitle
 * @var string $startDate
 * @var string $endDate
 * @var array  $headers
 * @var array  $rows
 * @var array  $summary
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 16px; }
        .company { font-size: 16px; font-weight: bold; color: #0f172a; }
        .meta { font-size: 10px; color: #64748b; margin-top: 2px; }
        .title { font-size: 13px; font-weight: bold; margin: 12px 0 6px; }
        .summary-box { margin-bottom: 14px; }
        .summary-item { display: inline-block; margin-right: 20px; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th { background-color: #0f172a; color: #ffffff; text-align: left; padding: 7px 8px; font-size: 10px; }
        td { border-bottom: 1px solid #e2e8f0; padding: 6px 8px; font-size: 10px; }
        tr:nth-child(even) td { background-color: #f8fafc; }
        .footer { margin-top: 20px; font-size: 9px; color: #94a3b8; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company"><?= esc($companyName) ?></div>
        <?php if ($companyAddress !== '') : ?>
            <div class="meta"><?= esc($companyAddress) ?></div>
        <?php endif; ?>
    </div>

    <div class="title"><?= esc($reportTitle) ?></div>
    <div class="meta" style="margin-bottom: 10px;">
        Periode: <?= esc(date('d M Y', strtotime($startDate))) ?> s/d <?= esc(date('d M Y', strtotime($endDate))) ?>
    </div>

    <div class="summary-box">
        <?php foreach ($summary as $label => $val) : ?>
            <span class="summary-item"><strong><?= esc($label) ?>:</strong> <?= esc((string) $val) ?></span>
        <?php endforeach; ?>
    </div>

    <table>
        <thead>
            <tr>
                <?php foreach ($headers as $h) : ?>
                    <th><?= esc($h) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($rows)) : ?>
                <tr><td colspan="<?= count($headers) ?>" style="text-align:center;padding:16px;">Tidak ada data pada periode ini.</td></tr>
            <?php else : ?>
                <?php foreach ($rows as $r) : ?>
                    <tr>
                        <?php foreach ($r as $cell) : ?>
                            <td><?= esc((string) $cell) ?></td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="footer">Dicetak melalui WORKLOG pada <?= date('d M Y H:i') ?> WIB</div>
</body>
</html>