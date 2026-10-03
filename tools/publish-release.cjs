const fs = require('node:fs');
const path = require('node:path');
const crypto = require('node:crypto');
const { execFileSync } = require('node:child_process');
const { readReleaseInfo, releasePlan, writeReleaseNotes } = require('./release-info.cjs');
const root = path.join(__dirname, '..');
const repository = process.env.GITHUB_REPOSITORY || 'sanyzrn/DbsProductSlider';
const sha = process.env.RELEASE_SHA;
if (repository !== 'sanyzrn/DbsProductSlider' || !/^[a-f0-9]{40}$/.test(sha || '')) throw new Error('An upstream repository and exact release commit are required.');
function gh(args) { return execFileSync('gh', args, { cwd: root, encoding: 'utf8', stdio: ['ignore', 'pipe', 'pipe'] }).trim(); }
function api(endpoint, optional = false) {
    try { return JSON.parse(gh(['api', 'repos/' + repository + '/' + endpoint])); }
    catch (error) { if (optional && String(error.stderr).includes('HTTP 404')) return null; throw error; }
}
function resolveTag(ref) {
    let object = ref?.object;
    for (let depth = 0; object?.type === 'tag' && depth < 5; depth++) object = api('git/tags/' + object.sha).object;
    if (object && object.type !== 'commit') throw new Error('Tag does not resolve to a commit.');
    return object?.sha;
}
const info = readReleaseInfo();
if (execFileSync('git', ['rev-parse', 'HEAD'], { cwd: root, encoding: 'utf8' }).trim() !== sha) throw new Error('Checked-out source does not match the release commit.');
const runs = api('actions/workflows/checks.yml/runs?head_sha=' + sha + '&event=push&branch=main&per_page=100').workflow_runs;
if (!runs.some(run => run.head_sha === sha && run.event === 'push' && run.head_branch === 'main' && run.conclusion === 'success')) throw new Error('Successful regression checks for the exact main commit are required.');
const existing = api('releases/tags/' + info.tag, true);
const latest = api('releases/latest', true);
const latestVersion = latest ? latest.tag_name.replace(/^v/, '') : null;
const plan = releasePlan({ version: info.version, sourceSha: sha, mainSha: api('git/ref/heads/main').object.sha, existingRelease: existing,
    tagSha: resolveTag(api('git/ref/tags/' + info.tag, true)), latestVersion });
if (process.env.GITHUB_OUTPUT) fs.appendFileSync(process.env.GITHUB_OUTPUT, 'needed=' + plan.needed + '\nversion=' + info.version + '\ntag=' + info.tag + '\n');
if (!plan.needed) { console.log(plan.reason); process.exit(0); }
if (process.argv.includes('--check')) { console.log('Validated release candidate ' + info.tag + ' at ' + sha); process.exit(0); }

const names = ['DbsProductSlider-' + info.version + '.zip', 'DbsProductSlider.zip', 'SHA256SUMS'];
const files = names.map(name => path.join(root, 'dist', name));
files.forEach(file => { if (!fs.statSync(file).isFile()) throw new Error('Missing release asset: ' + file); });
const notes = writeReleaseNotes(info);
const title = 'Dbs Product Slider ' + info.version;
// Upload and verify a draft first; never replace assets on a published release.
if (!existing) gh(['release', 'create', info.tag, ...files, '--repo', repository, '--target', sha, '--title', title, '--notes-file', notes, '--draft']);
else {
    gh(['release', 'upload', info.tag, ...files, '--repo', repository, '--clobber']);
    gh(['release', 'edit', info.tag, '--repo', repository, '--title', title, '--notes-file', notes]);
}
const draft = api('releases/tags/' + info.tag);
if (!draft.draft) throw new Error('Release changed while uploading. Do not modify it.');
for (let i = 0; i < names.length; i++) {
    const asset = draft.assets.find(asset => asset.name === names[i]);
    const bytes = fs.readFileSync(files[i]);
    const digest = 'sha256:' + crypto.createHash('sha256').update(bytes).digest('hex');
    if (!asset || asset.state !== 'uploaded' || asset.size !== bytes.length || (asset.digest && asset.digest !== digest)) throw new Error('Uploaded asset validation failed: ' + names[i]);
}
gh(['release', 'edit', info.tag, '--repo', repository, '--draft=false', '--latest=' + plan.latest]);
const published = api('releases/tags/' + info.tag);
if (published.draft || published.prerelease) throw new Error('Release was not published as stable.');
console.log(published.html_url);
