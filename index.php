<?php
// Definiere verfügbare Optionen
$formats = [
    '1:2' => ['icon' => '📱', 'label' => 'Hochformat'],
    '2:1' => ['icon' => '🖥️', 'label' => 'Querformat']
];

$styles = [
    'Romantik' => ['icon' => '🌹', 'class' => 'romantik'],
    'Renaissance' => ['icon' => '🏛️', 'class' => 'renaissance'],
    'Impressionismus' => ['icon' => '🎨', 'class' => 'impressionismus'],
    'Symbolismus' => ['icon' => '🔮', 'class' => 'symbolismus'],
    'Expressionismus' => ['icon' => '😱', 'class' => 'expressionismus'],
    'Surrealismus' => ['icon' => '🌀', 'class' => 'surrealismus'],
    'Barock' => ['icon' => '👑', 'class' => 'barock'],
    'Jugendstil' => ['icon' => '🌺', 'class' => 'jugendstil'],
    'Gotik' => ['icon' => '⛪', 'class' => 'gotik'],
    'Futurismus' => ['icon' => '🚀', 'class' => 'futurismus'],
    'Cyberpunk' => ['icon' => '🤖', 'class' => 'cyberpunk'],
    'Popart' => ['icon' => '🎭', 'class' => 'popart'],
    'Nihonga' => ['icon' => '🗾', 'class' => 'nihonga']
];

$techniques = [
    'Ölmalerei' => ['icon' => '🎨', 'class' => 'oelmalerei'],
    'Aquarell' => ['icon' => '💧', 'class' => 'aquarell'],
    'Acrylmalerei' => ['icon' => '🖌️', 'class' => 'acryl'],
    'Pastell' => ['icon' => '🖍️', 'class' => 'pastell'],
    'Wasserfarben' => ['icon' => '🌊', 'class' => 'wasserfarben'],
    'Tempera' => ['icon' => '🥚', 'class' => 'tempera'],
    'Bleistift/Kohle' => ['icon' => '✏️', 'class' => 'bleistift'],
    'Airbrush' => ['icon' => '💨', 'class' => 'airbrush'],
    'Collage' => ['icon' => '📰', 'class' => 'collage'],
    'Spraypaint' => ['icon' => '🎯', 'class' => 'spraypaint'],
    'Siebdruck' => ['icon' => '🖨️', 'class' => 'siebdruck'],
    'Digital Painting' => ['icon' => '💻', 'class' => 'digital'],
    'Mineralfarben' => ['icon' => '⛏️', 'class' => 'mineralfarben']
];

// Empfohlene Techniken pro Stil
$recommendedTechniques = [
    'Nihonga' => [
        'Bleistift/Kohle',
        'Pastell',
        'Aquarell',
        'Wasserfarben',
        'Mineralfarben'
    ]
];

// Verarbeite Form-Eingaben
$currentStep = $_GET['step'] ?? 'format';
$selectedFormat = $_GET['format'] ?? '';
$selectedStyle = $_GET['style'] ?? '';
$selectedTechnique = $_GET['technique'] ?? '';

// Generiere Prompt wenn alle Optionen ausgewählt sind
$generatedPrompt = '';
if ($selectedFormat && $selectedStyle && $selectedTechnique) {
    $formatLabel = $formats[$selectedFormat]['label'];
    $generatedPrompt = "Transformiere dieses Bild im Stil der Richtung {$selectedStyle} als {$selectedTechnique} im {$formatLabel}.";
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="favicon.png">
    <title>Art Prompter</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <?php if ($currentStep === 'format'): ?>
            <!-- Format-Auswahl -->
            <div class="screen active">
                <h1>Art Prompter</h1>
                <p class="subtitle">Wähle Bildformat</p>
                <div class="format-buttons">
                    <?php foreach ($formats as $formatKey => $format): ?>
                        <a href="?step=style&format=<?= urlencode($formatKey) ?>" class="format-btn">
                            <div class="format-icon"><?= $format['icon'] ?></div>
                            <div class="format-label"><?= $format['label'] ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($currentStep === 'style'): ?>
            <!-- Stil-Auswahl -->
            <div class="screen active">
                <a href="?step=format" class="back-btn">← Zurück</a>
                <h1>Wähle Kunststil</h1>
                <p class="subtitle">Dein Bild wird in diesem Stil transformiert</p>
                <div class="styles-grid">
                    <?php foreach ($styles as $styleName => $style): ?>
                        <a href="?step=technique&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($styleName) ?>" 
                           class="style-btn <?= $style['class'] ?>">
                            <div class="style-icon"><?= $style['icon'] ?></div>
                            <div class="style-name"><?= $styleName ?></div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php elseif ($currentStep === 'technique'): ?>
            <!-- Maltechnik-Auswahl -->
            <div class="screen active">
                <a href="?step=style&format=<?= urlencode($selectedFormat) ?>" class="back-btn">← Zurück</a>
                <h1>Wähle Maltechnik</h1>
                <p class="subtitle">Die Technik bestimmt das Aussehen und die Textur</p>
                
                <?php
                // Techniken in empfohlen/nicht empfohlen unterteilen
                $recommended = [];
                $notRecommended = [];
                
                if (isset($recommendedTechniques[$selectedStyle])) {
                    foreach ($techniques as $technique => $data) {
                        if (in_array($technique, $recommendedTechniques[$selectedStyle])) {
                            $recommended[$technique] = $data;
                        } else {
                            $notRecommended[$technique] = $data;
                        }
                    }
                } else {
                    // Fallback: alle als empfohlen wenn kein Stil definiert
                    $recommended = $techniques;
                }
                ?>

                <?php if (!empty($recommended)): ?>
                    <div class="technique-category">
                        <h2 class="category-title">✅ Empfohlen</h2>
                        <div class="styles-grid">
                            <?php foreach ($recommended as $technique => $data): ?>
                                <a href="?step=result&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($selectedStyle) ?>&technique=<?= urlencode($technique) ?>" 
                                   class="style-btn <?= htmlspecialchars($data['class']) ?>">
                                    <div class="style-icon"><?= $data['icon'] ?></div>
                                    <div class="style-name"><?= htmlspecialchars($technique) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (!empty($notRecommended)): ?>
                    <div class="technique-category not-recommended">
                        <h2 class="category-title">⚠️ Nicht empfohlen</h2>
                        <div class="styles-grid">
                            <?php foreach ($notRecommended as $technique => $data): ?>
                                <a href="?step=result&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($selectedStyle) ?>&technique=<?= urlencode($technique) ?>" 
                                   class="style-btn <?= htmlspecialchars($data['class']) ?> not-recommended-technique">
                                    <div class="style-icon"><?= $data['icon'] ?></div>
                                    <div class="style-name"><?= htmlspecialchars($technique) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

        <?php elseif ($currentStep === 'result'): ?>
            <!-- Prompt-Anzeige -->
            <div class="screen active">
                <a href="?step=technique&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($selectedStyle) ?>" class="back-btn">← Zurück</a>
                <h1>Prompt ist bereit!</h1>
                
                <p class="subtitle">Generierter Prompt:</p>
                <div class="prompt-text"><?= htmlspecialchars($generatedPrompt) ?></div>

                <div class="selection-summary">
                    <div class="summary-item">
                        <span class="summary-label">Format:</span>
                        <span class="summary-value"><?= htmlspecialchars($selectedFormat) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Stil:</span>
                        <span class="summary-value"><?= htmlspecialchars($selectedStyle) ?></span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Technik:</span>
                        <span class="summary-value"><?= htmlspecialchars($selectedTechnique) ?></span>
                    </div>
                </div>

                <div class="action-buttons">
                    <button class="action-btn copy-btn" onclick="copyPrompt('<?= htmlspecialchars($generatedPrompt) ?>')">
                        <span class="btn-icon">📋</span>
                        <span>Prompt kopieren</span>
                    </button>
                    <a href="chatgpt://?prompt=<?= urlencode($generatedPrompt) ?>" class="action-btn chatgpt-btn" target="_blank">
                        <span class="btn-icon">💬</span>
                        <span>ChatGPT öffnen</span>
                    </a>
                </div>

                <div class="edit-options">
                    <h3>Auswahl ändern:</h3>
                    <div class="edit-dropdowns">
                        <div class="dropdown-group">
                            <label for="formatSelect">📱 Format:</label>
                            <select id="formatSelect" class="edit-dropdown" onchange="changeFormat(this.value)">
                                <?php foreach ($formats as $formatKey => $format): ?>
                                    <option value="<?= htmlspecialchars($formatKey) ?>" <?= $formatKey === $selectedFormat ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($format['label']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="dropdown-group">
                            <label for="styleSelect">🎨 Stil:</label>
                            <select id="styleSelect" class="edit-dropdown" onchange="changeStyle(this.value)">
                                <?php foreach ($styles as $styleName => $style): ?>
                                    <option value="<?= htmlspecialchars($styleName) ?>" <?= $styleName === $selectedStyle ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($styleName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="dropdown-group">
                            <label for="techniqueSelect">🖌️ Technik:</label>
                            <select id="techniqueSelect" class="edit-dropdown" onchange="changeTechnique(this.value)">
                                <?php foreach ($techniques as $techniqueName => $technique): ?>
                                    <option value="<?= htmlspecialchars($techniqueName) ?>" <?= $techniqueName === $selectedTechnique ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($techniqueName) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div style="margin-top: 30px; text-align: center;">
                    <a href="?step=format" class="back-btn">🔄 Neuen Prompt erstellen</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyPrompt(prompt) {
            navigator.clipboard.writeText(prompt).then(() => {
                const copyBtn = document.querySelector('.copy-btn');
                const originalHTML = copyBtn.innerHTML;
                copyBtn.classList.add('copied');
                copyBtn.innerHTML = '<span class="btn-icon">✓</span><span>Kopiert!</span>';
                
                setTimeout(() => {
                    copyBtn.classList.remove('copied');
                    copyBtn.innerHTML = originalHTML;
                }, 2000);
            }).catch(() => {
                showManualCopy(prompt);
            });
        }

        function showManualCopy(prompt) {
            const modal = document.createElement('div');
            modal.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1000;
            `;
            modal.innerHTML = `
                <div style="background: white; padding: 30px; border-radius: 10px; max-width: 500px; width: 90%;">
                    <h3 style="margin-bottom: 15px; color: #333;">Prompt zum Kopieren:</h3>
                    <textarea readonly style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; min-height: 100px; resize: none;">${prompt}</textarea>
                    <div style="margin-top: 20px;">
                        <button onclick="this.closest('div[style]').parentElement.remove()" style="width: 100%; padding: 10px; background: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">Schließen</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function changeFormat(newFormat) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('format', newFormat);
            window.location.href = currentUrl.toString();
        }

        function changeStyle(newStyle) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('style', newStyle);
            window.location.href = currentUrl.toString();
        }

        function changeTechnique(newTechnique) {
            const currentUrl = new URL(window.location);
            currentUrl.searchParams.set('technique', newTechnique);
            window.location.href = currentUrl.toString();
        }
    </script>
</body>
</html>
