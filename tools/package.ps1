$ErrorActionPreference = 'Stop'
$workspaceDirectory = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$releaseDirectory = Join-Path $workspaceDirectory 'dist'
$pluginSource = Get-Content -LiteralPath (Join-Path $workspaceDirectory 'product-carousel-elementor.php') -Raw
$versionMatch = [regex]::Match($pluginSource, 'Version:\s*([0-9.]+)')
if (-not $versionMatch.Success) { throw 'Plugin version is missing.' }
$version = $versionMatch.Groups[1].Value
$readme = Get-Content -LiteralPath (Join-Path $workspaceDirectory 'readme.txt') -Raw
if ($readme -notmatch "Stable tag: $([regex]::Escape($version))") { throw 'Readme stable tag does not match the plugin version.' }
$requiredFiles = @('product-carousel-elementor.php', 'widgets/carousel.php', 'includes/class-pce-settings.php', 'includes/class-pce-products.php', 'includes/templates/carousel.php', 'includes/controls/content.php', 'assets/js/script.js', 'assets/css/style.css', 'assets/vendor/swiper/swiper-bundle.min.js', 'assets/vendor/swiper/swiper-bundle.min.css', 'assets/vendor/swiper/LICENSE', 'languages/advanced-carousel-pro-fa_IR.mo', 'languages/advanced-carousel-pro-fa_IR.po', 'languages/advanced-carousel-pro.pot', 'docs/UPGRADE_REPORT_FA_2026-10-03.md', 'readme.txt', 'README_FA.md', 'LICENSE')
foreach ($relativePath in $requiredFiles) {
    if (-not (Test-Path -LiteralPath (Join-Path $workspaceDirectory $relativePath))) { throw "Missing release file: $relativePath" }
}
New-Item -ItemType Directory -Force -Path $releaseDirectory | Out-Null
$stagingDirectory = Join-Path $releaseDirectory ('stage-' + [guid]::NewGuid().ToString('N'))
$pluginDirectory = Join-Path $stagingDirectory 'DbsProductSlider'
New-Item -ItemType Directory -Force -Path $pluginDirectory | Out-Null
foreach ($entry in @('product-carousel-elementor.php', 'widgets', 'includes', 'assets', 'languages', 'docs', 'readme.txt', 'README_FA.md', 'LICENSE')) {
    Copy-Item -LiteralPath (Join-Path $workspaceDirectory $entry) -Destination $pluginDirectory -Recurse
}
$archivePath = Join-Path $releaseDirectory "DbsProductSlider-$version.zip"
if (Test-Path -LiteralPath $archivePath) { Remove-Item -LiteralPath $archivePath }
Add-Type -AssemblyName System.IO.Compression.FileSystem
[System.IO.Compression.ZipFile]::CreateFromDirectory($stagingDirectory, $archivePath)
$archive = [System.IO.Compression.ZipFile]::OpenRead($archivePath)
try {
    $names = @($archive.Entries | ForEach-Object { $_.FullName.Replace('\', '/') })
    foreach ($entry in $requiredFiles) {
        if ($names -notcontains "DbsProductSlider/$entry") { throw "Release archive missing: $entry" }
    }
    if ($names -match 'node_modules|test-results|\.git/|Audit_FA|package-lock') { throw 'Development files leaked into release.' }
} finally { $archive.Dispose() }
# Check the resolved target before recursive cleanup, and keep operations in PowerShell.
$resolvedStaging = (Resolve-Path -LiteralPath $stagingDirectory).Path
$resolvedRelease = (Resolve-Path -LiteralPath $releaseDirectory).Path
if (-not $resolvedStaging.StartsWith($resolvedRelease + [System.IO.Path]::DirectorySeparatorChar, [System.StringComparison]::OrdinalIgnoreCase)) { throw 'Staging directory escaped the release directory.' }
Remove-Item -LiteralPath $resolvedStaging -Recurse
Write-Output "Verified release archive: $archivePath"
Get-FileHash -LiteralPath $archivePath -Algorithm SHA256
