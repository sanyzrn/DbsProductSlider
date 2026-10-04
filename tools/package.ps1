$ErrorActionPreference = 'Stop'
$workspaceDirectory = [System.IO.Path]::GetFullPath((Join-Path $PSScriptRoot '..'))
$releaseDirectory = Join-Path $workspaceDirectory 'dist'
$pluginSource = Get-Content -LiteralPath (Join-Path $workspaceDirectory 'product-carousel-elementor.php') -Raw
$versionMatch = [regex]::Match($pluginSource, 'Version:\s*([0-9.]+)')
if (-not $versionMatch.Success) { throw 'Plugin version is missing.' }
$version = $versionMatch.Groups[1].Value
$readme = Get-Content -LiteralPath (Join-Path $workspaceDirectory 'readme.txt') -Raw
if ($readme -notmatch "(?m)^Stable tag:\s*$([regex]::Escape($version))\s*$") { throw 'Readme stable tag does not match the plugin version.' }
if ($version -notmatch '^(0|[1-9]\d*)\.(0|[1-9]\d*)\.(0|[1-9]\d*)$' -or $pluginSource -notmatch "private const VERSION = '$([regex]::Escape($version))';") { throw 'Plugin header and VERSION must contain the same semantic version.' }
$requiredFiles = @('product-carousel-elementor.php', 'widgets/carousel.php', 'includes/class-pce-settings.php', 'includes/class-pce-products.php', 'includes/class-pce-content.php', 'includes/templates/carousel.php', 'includes/controls/content.php', 'assets/js/script.js', 'assets/css/style.css', 'assets/vendor/swiper/swiper-bundle.min.js', 'assets/vendor/swiper/swiper-bundle.min.css', 'assets/vendor/swiper/LICENSE', 'languages/advanced-carousel-pro-fa_IR.mo', 'languages/advanced-carousel-pro-fa_IR.po', 'languages/advanced-carousel-pro.pot', 'readme.txt', 'README.md', 'README_FA.md', 'LICENSE')
foreach ($relativePath in $requiredFiles) {
    if (-not (Test-Path -LiteralPath (Join-Path $workspaceDirectory $relativePath))) { throw "Missing release file: $relativePath" }
}
New-Item -ItemType Directory -Force -Path $releaseDirectory | Out-Null
$stagingDirectory = Join-Path $releaseDirectory ('stage-' + [guid]::NewGuid().ToString('N'))
$pluginDirectory = Join-Path $stagingDirectory 'DbsProductSlider'
New-Item -ItemType Directory -Force -Path $pluginDirectory | Out-Null
foreach ($entry in $requiredFiles) {
    $destinationPath = Join-Path $pluginDirectory $entry
    New-Item -ItemType Directory -Force -Path (Split-Path -Parent $destinationPath) | Out-Null
    Copy-Item -LiteralPath (Join-Path $workspaceDirectory $entry) -Destination $destinationPath
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
    if ($names.Count -ne $requiredFiles.Count) { throw 'Unexpected files in release archive.' }
    if ($names -match 'node_modules|test-results|\.git/|Audit_FA|package-lock') { throw 'Development files leaked into release.' }
} finally { $archive.Dispose() }
# Check the resolved target before recursive cleanup, and keep operations in PowerShell.
$resolvedStaging = (Resolve-Path -LiteralPath $stagingDirectory).Path
$resolvedRelease = (Resolve-Path -LiteralPath $releaseDirectory).Path
if (-not $resolvedStaging.StartsWith($resolvedRelease + [System.IO.Path]::DirectorySeparatorChar, [System.StringComparison]::OrdinalIgnoreCase)) { throw 'Staging directory escaped the release directory.' }
Remove-Item -LiteralPath $resolvedStaging -Recurse
$stableArchivePath = Join-Path $releaseDirectory 'DbsProductSlider.zip'
Copy-Item -LiteralPath $archivePath -Destination $stableArchivePath -Force
$checksums = @($archivePath, $stableArchivePath) | ForEach-Object { (Get-FileHash -LiteralPath $_ -Algorithm SHA256).Hash.ToLowerInvariant() + '  ' + [System.IO.Path]::GetFileName($_) }
Set-Content -LiteralPath (Join-Path $releaseDirectory 'SHA256SUMS') -Value $checksums -Encoding ascii
Write-Output "Verified release archive: $archivePath"
Get-FileHash -LiteralPath $archivePath -Algorithm SHA256
