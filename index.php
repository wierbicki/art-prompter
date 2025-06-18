<?php
// Am Anfang der index.php - Prompt-Historie aus Cookie laden
$promptHistory = [];
if (isset($_COOKIE['prompt_history'])) {
    $promptHistory = json_decode($_COOKIE['prompt_history'], true) ?: [];
}

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
    'Digital Painting' => ['icon' => '💻', 'class' => 'digital']
];

// Stilspezifische Techniken (zusätzlich zu den allgemeinen Techniken)
$styleSpecificTechniques = [
    'Nihonga' => [
        'Mineralfarben' => ['icon' => '🗻', 'class' => 'iwa-enogu', 'subtitle' => '岩絵具 (Iwa-enogu)'],
        'Tusche' => ['icon' => '🖋️', 'class' => 'sumi-e', 'subtitle' => '墨絵 (Sumi-e)'],
        'Muschelkalk' => ['icon' => '🐚', 'class' => 'gofun', 'subtitle' => '胡粉 (Gofun)'],
        'Blattgold' => ['icon' => '🥇', 'class' => 'kin-paku', 'subtitle' => '金箔 (Kin-paku)'],
        'Tropftechnik' => ['icon' => '💧', 'class' => 'tarashikomi', 'subtitle' => 'たらし込み (Tarashikomi)']
    ]
];

// Empfohlene allgemeine Techniken pro Stil (Teilmenge von $techniques)
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
$chatgptUrl = '';
if ($selectedFormat && $selectedStyle && $selectedTechnique) {
    $formatLabel = $formats[$selectedFormat]['label'];
    $generatedPrompt = "Transformiere dieses Bild im Stil der Richtung {$selectedStyle} als {$selectedTechnique} im {$formatLabel}.";
    
    // Neuen Prompt zur Historie hinzufügen (nur wenn anders als der letzte)
    if (empty($promptHistory) || end($promptHistory) !== $generatedPrompt) {
        $promptHistory[] = $generatedPrompt;
        // Nur die letzten 3 behalten (damit nach Filterung 2 übrig bleiben)
        $promptHistory = array_slice($promptHistory, -3);
        
        // Cookie für 30 Tage setzen
        setcookie('prompt_history', json_encode($promptHistory), time() + (30 * 24 * 60 * 60), '/');
    }
    
    $chatgptUrl = "https://chatgpt.com/?q=" . urlencode($generatedPrompt);
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
                // Empfohlene allgemeine Techniken
                $recommendedGeneral = [];
                if (isset($recommendedTechniques[$selectedStyle])) {
                    foreach ($recommendedTechniques[$selectedStyle] as $technique) {
                        if (isset($techniques[$technique])) {
                            $recommendedGeneral[$technique] = $techniques[$technique];
                        }
                    }
                } else {
                    // Falls kein Stil definiert ist, alle allgemeinen Techniken als empfohlen anzeigen
                    $recommendedGeneral = $techniques;
                }
                
                // Stilspezifische Techniken
                $styleSpecific = [];
                if (isset($styleSpecificTechniques[$selectedStyle])) {
                    $styleSpecific = $styleSpecificTechniques[$selectedStyle];
                }
                
                // Nicht empfohlene allgemeine Techniken
                $notRecommendedGeneral = [];
                if (isset($recommendedTechniques[$selectedStyle])) {
                    foreach ($techniques as $technique => $data) {
                        if (!isset($recommendedGeneral[$technique])) {
                            $notRecommendedGeneral[$technique] = $data;
                        }
                    }
                }
                ?>

                <!-- Stilspezifische Techniken (nur wenn vorhanden) -->
                <?php if (!empty($styleSpecific)): ?>
                    <div class="technique-category special-techniques">
                        <h2 class="category-title">🌟 Stilspezifisch</h2>
                        <div class="styles-grid">
                            <?php foreach ($styleSpecific as $technique => $data): ?>
                                <a href="?step=result&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($selectedStyle) ?>&technique=<?= urlencode($technique) ?>" 
                                   class="style-btn <?= htmlspecialchars($data['class']) ?> style-specific">
                                    <div class="style-icon"><?= $data['icon'] ?></div>
                                    <div class="style-name">
                                        <?= htmlspecialchars($technique) ?>
                                        <?php if (isset($data['subtitle'])): ?>
                                            <span class="japanese-subtitle"><?= htmlspecialchars($data['subtitle']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Empfohlene allgemeine Techniken -->
                <?php if (!empty($recommendedGeneral)): ?>
                    <div class="technique-category">
                        <h2 class="category-title">✅ Empfohlen</h2>
                        <div class="styles-grid">
                            <?php foreach ($recommendedGeneral as $technique => $data): ?>
                                <a href="?step=result&format=<?= urlencode($selectedFormat) ?>&style=<?= urlencode($selectedStyle) ?>&technique=<?= urlencode($technique) ?>" 
                                   class="style-btn <?= htmlspecialchars($data['class']) ?>">
                                    <div class="style-icon"><?= $data['icon'] ?></div>
                                    <div class="style-name"><?= htmlspecialchars($technique) ?></div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Nicht empfohlene allgemeine Techniken -->
                <?php if (!empty($notRecommendedGeneral)): ?>
                    <div class="technique-category not-recommended">
                        <h2 class="category-title">⚠️ Nicht empfohlen</h2>
                        <div class="styles-grid">
                            <?php foreach ($notRecommendedGeneral as $technique => $data): ?>
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
                    <button class="action-btn copy-btn" onclick="copyPrompt('<?= htmlspecialchars($generatedPrompt, ENT_QUOTES) ?>')">
                        <span class="btn-icon">📋</span>
                        <span>Prompt kopieren</span>
                    </button>
                    <a href="<?= htmlspecialchars($chatgptUrl) ?>" class="action-btn chatgpt-btn" target="_blank">
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
                                <!-- Allgemeine Techniken -->
                                <?php foreach ($techniques as $techniqueName => $technique): ?>
                                    <option value="<?= htmlspecialchars($techniqueName) ?>" <?= $techniqueName === $selectedTechnique ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($techniqueName) ?>
                                    </option>
                                <?php endforeach; ?>
                                
                                <!-- Stilspezifische Techniken falls vorhanden -->
                                <?php if (isset($styleSpecificTechniques[$selectedStyle])): ?>
                                    <?php foreach ($styleSpecificTechniques[$selectedStyle] as $techniqueName => $technique): ?>
                                        <option value="<?= htmlspecialchars($techniqueName) ?>" <?= $techniqueName === $selectedTechnique ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($techniqueName) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Prompt-Historie am Ende -->
                <?php if (count($promptHistory) > 1): ?>
                    <div class="prompt-history">
                        <h3>📚 Letzte Prompts</h3>
                        <div class="history-list">
                            <?php 
                            // Zeige die letzten 2 Prompts (außer dem aktuellen)
                            $historyToShow = array_reverse($promptHistory);
                            $shown = 0;
                            foreach ($historyToShow as $oldPrompt): 
                                if ($oldPrompt !== $generatedPrompt && $shown < 2): 
                                    $shown++;
                            ?>
                                <div class="history-item">
                                    <div class="history-prompt-text">
                                        <?= htmlspecialchars($oldPrompt) ?>
                                    </div>
                                    <div class="history-actions">
                                        <button onclick="copyPrompt('<?= htmlspecialchars($oldPrompt, ENT_QUOTES) ?>')" class="action-btn history-copy-btn">
                                            <span class="btn-icon">📋</span>
                                            <span>Kopieren</span>
                                        </button>
                                        <a href="https://chatgpt.com/?q=<?= urlencode($oldPrompt) ?>" target="_blank" class="action-btn history-chatgpt-btn">
                                            <span class="btn-icon">💬</span>
                                            <span>ChatGPT</span>
                                        </a>
                                    </div>
                                </div>
                            <?php 
                                endif; 
                            endforeach; 
                            ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div style="margin-top: 30px; text-align: center;">
                    <a href="?step=format" class="back-btn">🔄 Neuen Prompt erstellen</a>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function copyPrompt(prompt) {
            navigator.clipboard.writeText(prompt).then(() => {
                const copyBtn = event.target.closest('.copy-btn, .history-copy-btn');
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
