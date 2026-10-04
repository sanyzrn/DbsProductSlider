# Releasing

## Automatic publication

Pushing a new version to `main` starts **Plugin regression checks**. After that workflow passes for all PHP versions, **Publish release** checks the version and publishes a release if its tag does not already have a published release.

For the next version:

1. Update the plugin header and `ACP_Plugin::VERSION` in `nexa-slider.php` and `Stable tag` in `readme.txt` to the same `MAJOR.MINOR.PATCH` version.
2. Add a matching changelog section to `readme.txt` and update the Persian guide when needed.
3. Run `npm run build:translations` to refresh the catalog version, then run `npm test`.
4. Commit and push to `main`. GitHub creates the `vMAJOR.MINOR.PATCH` tag, builds the installable ZIP and publishes the release.

No personal access token or manual tag push is required. The workflow uses the repository's `GITHUB_TOKEN`. Failed checks do not publish, fork and pull-request runs cannot publish, and already published versions are left unchanged. A newer semantic version becomes Latest; older versions never replace it.

For a retry, run **Publish release → Run workflow** on `main`. It requires successful regression checks for that exact commit. A conflicting pre-existing tag is rejected rather than moved.

## Release assets

- `nexa-slider-MAJOR.MINOR.PATCH.zip`: versioned WordPress installation package.
- `nexa-slider.zip`: the same package under a stable filename.
- `SHA256SUMS`: checksums for both ZIP files.

The permanent download URL is:

```text
https://github.com/sanyzrn/DbsProductSlider/releases/latest/download/nexa-slider.zip
```

Use the plugin ZIP for installation. GitHub's automatically generated source ZIP includes development files and is not the release package. Publishing a GitHub release does not add an automatic WordPress updater or publish to WordPress.org.

## Local package

```sh
node tools/release-info.cjs
pwsh -File tools/package.ps1
```

The packager verifies version consistency and includes only an explicit list of runtime files, translations, user guides and licenses. Tests, npm dependencies, workflows and internal reports are excluded. Install and upgrade the finished package in a staging WordPress site before production use.
