@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white border rounded-xl shadow-sm">
        <h1 class="text-2xl font-bold mb-6">Video Manager</h1>

        {{-- Status box --}}
        <div id="statusBox" class="hidden mb-4 p-3 rounded bg-gray-100 text-gray-800"></div>

        {{-- =========================
      LIST (GET /api/videos)
  ========================= --}}
        <div class="border rounded-lg p-5 mb-10">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Your Videos</h2>
                <div class="flex gap-2 items-center">
                    <input id="searchInput" type="text" class="border rounded p-2 text-sm" placeholder="Search title...">
                    <select id="perPageSelect" class="border rounded p-2 text-sm">
                        <option>10</option>
                        <option selected>20</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                    <button id="refreshBtn" class="px-3 py-2 border rounded text-sm">Refresh</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm border rounded">
                    <thead class="bg-gray-50">
                        <tr class="text-left">
                            <th class="p-2 border">ID</th>
                            <th class="p-2 border">Title</th>
                            <th class="p-2 border">Src</th>
                            <th class="p-2 border">Poster</th>
                            <th class="p-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="videosTbody"></tbody>
                </table>
            </div>

            <div id="pagination" class="flex gap-2 mt-4 flex-wrap"></div>
        </div>

        {{-- =========================
      CREATE (POST /api/videos)
  ========================= --}}
        <div class="border rounded-lg p-5 mb-10">
            <h2 class="text-xl font-semibold mb-4">Create a Video</h2>
            <form id="createForm" enctype="multipart/form-data">
                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input name="title" type="text" class="w-full border rounded p-2" placeholder="My great video"
                            required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Video URL (src)</label>
                        <input name="src" type="url" class="w-full border rounded p-2"
                            placeholder="https://example.com/video.mp4">
                        <p class="text-xs text-gray-500 mt-1">If a file is uploaded, it will override this URL.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Poster URL</label>
                        <input name="poster" type="url" class="w-full border rounded p-2"
                            placeholder="https://example.com/poster.jpg">
                        <p class="text-xs text-gray-500 mt-1">If a file is uploaded, it will override this URL.</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Upload Video File</label>
                        <input name="video_file" type="file" accept="video/*" class="w-full border rounded p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Upload Poster File</label>
                        <input name="poster_file" type="file" accept="image/*" class="w-full border rounded p-2">
                    </div>
                </div>

                {{-- Tracks repeater --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium">Tracks</label>
                        <button type="button" id="addTrackBtn" class="px-3 py-1 border rounded text-sm">+ Add
                            Track</button>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">
                        Optional: e.g. subtitles/captions. Each track supports kind, src, srclang, label.
                    </p>
                    <div id="tracksWrapper" class="space-y-3"></div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create</button>
                </div>
            </form>
        </div>

        {{-- =========================
      UPDATE (PUT /api/videos/{id})
  ========================= --}}
        <div class="border rounded-lg p-5 mb-10">
            <h2 class="text-xl font-semibold mb-4">Update a Video</h2>
            <form id="updateForm" enctype="multipart/form-data">
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium mb-1">Video ID to Update</label>
                        <input name="id" id="updateVideoId" type="number" min="1"
                            class="w-full border rounded p-2" placeholder="e.g. 42" required>
                        <p class="text-xs text-gray-500 mt-1">Request will target /api/videos/{id}.</p>
                    </div>

                    <div class="md:col-span-2 grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Title</label>
                            <input name="title" type="text" class="w-full border rounded p-2"
                                placeholder="Leave blank to keep">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Video URL (src)</label>
                            <input name="src" type="url" class="w-full border rounded p-2"
                                placeholder="Leave blank to keep">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Poster URL</label>
                            <input name="poster" type="url" class="w-full border rounded p-2"
                                placeholder="Leave blank to keep">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Upload New Video File</label>
                            <input name="video_file" type="file" accept="video/*" class="w-full border rounded p-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Upload New Poster File</label>
                            <input name="poster_file" type="file" accept="image/*" class="w-full border rounded p-2">
                        </div>
                    </div>
                </div>

                {{-- Tracks repeater (Update) --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium">Tracks (replace existing if provided)</label>
                        <button type="button" id="addTrackBtnUpdate" class="px-3 py-1 border rounded text-sm">+ Add
                            Track</button>
                    </div>
                    <div id="tracksWrapperUpdate" class="space-y-3"></div>
                </div>

                <div class="mt-6">
                    <button type="submit" class="px-4 py-2 bg-amber-600 text-white rounded">Update</button>
                </div>
            </form>
        </div>

        {{-- =========================
      DELETE (DELETE /api/videos/{id})
  ========================= --}}
        {{-- <div class="border rounded-lg p-5">
            <h2 class="text-xl font-semibold mb-4">Delete a Video</h2>
            <form id="deleteForm">
                <div class="grid md:grid-cols-3 gap-4 items-end">
                    <div class="md:col-span-1">
                        <label class="block text-sm font-medium mb-1">Video ID to Delete</label>
                        <input name="id" id="deleteVideoId" type="number" min="1"
                            class="w-full border rounded p-2" placeholder="e.g. 42" required>
                    </div>
                    <div class="md:col-span-2 flex items-center gap-3">
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                        <span class="text-xs text-gray-500">This will call DELETE /api/videos/{id}.</span>
                    </div>
                </div>
            </form>
        </div> --}}
    </div>

    {{-- Token/Config for JS --}}
    <script>
        const API_BASE = `{{ rtrim(env('API_BASE_URL', url('/api')), '/') }}/`; // ensure trailing slash
        const API_TOKEN = `{{ session()->get('api_token') }}`; // Sanctum personal access token for this page
    </script>

    {{-- Minimal JS helpers + List/Edit/Delete integration --}}
    <script>
        const statusBox = document.getElementById('statusBox');

        function showStatus(msg, ok = true) {
            statusBox.classList.remove('hidden');
            statusBox.className = 'mb-4 p-3 rounded ' + (ok ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800');
            statusBox.textContent = msg;
        }

        // Build a row for tracks array inputs
        function trackRowHtml(base, i) {
            return `
      <div class="grid md:grid-cols-4 gap-3 border rounded p-3">
        <div>
          <label class="block text-xs font-medium mb-1">Kind</label>
          <input name="${base}[${i}][kind]" type="text" class="w-full border rounded p-2" placeholder="subtitles">
        </div>
        <div>
          <label class="block text-xs font-medium mb-1">Src</label>
          <input name="${base}[${i}][src]" type="url" class="w-full border rounded p-2" placeholder="https://.../subs.vtt">
        </div>
        <div>
          <label class="block text-xs font-medium mb-1">Srclang</label>
          <input name="${base}[${i}][srclang]" type="text" class="w-full border rounded p-2" placeholder="en">
        </div>
        <div>
          <label class="block text-xs font-medium mb-1">Label</label>
          <input name="${base}[${i}][label]" type="text" class="w-full border rounded p-2" placeholder="English">
        </div>
      </div>`;
        }

        // CREATE tracks
        const tracksWrapper = document.getElementById('tracksWrapper');
        const addTrackBtn = document.getElementById('addTrackBtn');
        let trackIndex = 0;
        addTrackBtn?.addEventListener('click', () => {
            tracksWrapper.insertAdjacentHTML('beforeend', trackRowHtml('tracks', trackIndex++));
        });

        // UPDATE tracks
        const tracksWrapperUpdate = document.getElementById('tracksWrapperUpdate');
        const addTrackBtnUpdate = document.getElementById('addTrackBtnUpdate');
        let trackIndexUpdate = 0;
        addTrackBtnUpdate?.addEventListener('click', () => {
            tracksWrapperUpdate.insertAdjacentHTML('beforeend', trackRowHtml('tracks', trackIndexUpdate++));
        });

        // ---------- FETCH HELPERS ----------
        async function apiFetch(url, options = {}) {
            const headers = options.headers ? {
                ...options.headers
            } : {};
            headers['Authorization'] = `Bearer ${API_TOKEN}`;
            return fetch(url, {
                ...options,
                headers
            });
        }

        function collectFormData(formEl) {
            return new FormData(formEl);
        }

        function extractId(formEl) {
            const id = (formEl.querySelector('input[name="id"]')?.value || '').trim();
            if (!id) throw new Error('Please provide a valid numeric ID.');
            return id;
        }

        // ---------- LISTING ----------
        let currentPage = 1;
        const tbody = document.getElementById('videosTbody');

        function buildIndexUrl(page = 1) {
            const q = encodeURIComponent(document.getElementById('searchInput')?.value?.trim() || '');
            const perPage = encodeURIComponent(document.getElementById('perPageSelect')?.value || 20);
            const params = new URLSearchParams();
            if (q) params.set('q', q);
            params.set('per_page', perPage);
            params.set('page', page);
            return `${API_BASE}videos?${params.toString()}`;
        }

        async function loadVideos(page = 1) {
            try {
                currentPage = page;
                const res = await apiFetch(buildIndexUrl(page));
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to fetch videos.');
                renderVideosTable(data);
                renderPagination(data);
            } catch (err) {
                showStatus(err.message, false);
            }
        }

        function escapeHtml(s) {
            return String(s || '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function renderVideosTable(payload) {
            tbody.innerHTML = '';
            const items = payload.data || [];
            if (!items.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="p-3 text-center text-gray-500">No videos found.</td></tr>`;
                return;
            }

            for (const v of items) {
                const tr = document.createElement('tr');
                tr.innerHTML = `
        <td class="p-2 border align-top">${v.id}</td>
        <td class="p-2 border align-top">${escapeHtml(v.title || '')}</td>
        <td class="p-2 border align-top break-all">
          ${v.src ? `<a href="${v.src}" class="text-blue-600 underline" target="_blank" rel="noopener">src</a>` : '<span class="text-gray-400">—</span>'}
        </td>
        <td class="p-2 border align-top">
          ${v.poster ? `<img src="${v.poster}" alt="poster" class="h-10 w-16 object-cover rounded border">` : '<span class="text-gray-400">—</span>'}
        </td>
        <td class="p-2 border align-top">
          <div class="flex gap-2">
            <button class="px-2 py-1 text-xs bg-amber-600 text-white rounded btn-edit" data-id="${v.id}">Edit</button>
            <button class="px-2 py-1 text-xs bg-red-600 text-white rounded btn-delete" data-id="${v.id}">Delete</button>
          </div>
        </td>
      `;
                tbody.appendChild(tr);
            }
        }

        function renderPagination(payload) {
            const links = payload.links || [];
            const container = document.getElementById('pagination');
            container.innerHTML = '';
            if (!links.length) return;

            for (const l of links) {
                const btn = document.createElement('button');
                btn.className =
                    `px-3 py-1 border rounded ${l.active ? 'bg-blue-600 text-white' : ''} ${l.url ? '' : 'opacity-50 cursor-not-allowed'}`;
                btn.innerHTML = l.label.replace('&laquo;', '«').replace('&raquo;', '»');
                if (l.url) {
                    const url = new URL(l.url);
                    const page = Number(url.searchParams.get('page') || 1);
                    btn.addEventListener('click', () => loadVideos(page));
                }
                container.appendChild(btn);
            }
        }

        document.getElementById('searchInput')?.addEventListener('input', debounce(() => loadVideos(1), 400));
        document.getElementById('perPageSelect')?.addEventListener('change', () => loadVideos(1));
        document.getElementById('refreshBtn')?.addEventListener('click', () => loadVideos(currentPage));

        function debounce(fn, ms) {
            let t;
            return (...args) => {
                clearTimeout(t);
                t = setTimeout(() => fn(...args), ms);
            };
        }

        // Inline Edit/Delete
        tbody?.addEventListener('click', async (e) => {
            const editBtn = e.target.closest('.btn-edit');
            const delBtn = e.target.closest('.btn-delete');

            if (editBtn) {
                const id = editBtn.dataset.id;
                await loadVideoAndPrefillUpdate(id);
            }

            if (delBtn) {
                const id = delBtn.dataset.id;
                if (!confirm(`Delete video #${id}?`)) return;
                try {
                    const res = await apiFetch(`${API_BASE}videos/${id}`, {
                        method: 'DELETE'
                    });
                    if (!res.ok) {
                        const data = await res.json().catch(() => ({}));
                        throw new Error(data.message || 'Failed to delete video.');
                    }
                    showStatus('Video deleted successfully.', true);
                    await loadVideos(currentPage);
                    const delInput = document.getElementById('deleteVideoId');
                    if (delInput && delInput.value == id) delInput.value = '';
                } catch (err) {
                    showStatus(err.message, false);
                }
            }
        });

        // Fetch single video then prefill Update form
        async function loadVideoAndPrefillUpdate(id) {
            try {
                const res = await apiFetch(`${API_BASE}videos/${id}`);
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Failed to load video.');
                const v = data.data || data;

                document.getElementById('updateVideoId').value = v.id;
                const updForm = document.getElementById('updateForm');
                updForm.querySelector('input[name="title"]').value = v.title || '';
                updForm.querySelector('input[name="src"]').value = v.src || '';
                updForm.querySelector('input[name="poster"]').value = v.poster || '';

                tracksWrapperUpdate.innerHTML = '';
                trackIndexUpdate = 0;
                const tracks = Array.isArray(v.tracks) ? v.tracks : [];
                tracks.forEach((t) => {
                    tracksWrapperUpdate.insertAdjacentHTML('beforeend', trackRowHtml('tracks',
                        trackIndexUpdate++));
                    const row = tracksWrapperUpdate.lastElementChild;
                    row.querySelector(`input[name="tracks[${trackIndexUpdate-1}][kind]"]`).value = t.kind || '';
                    row.querySelector(`input[name="tracks[${trackIndexUpdate-1}][src]"]`).value = t.src || '';
                    row.querySelector(`input[name="tracks[${trackIndexUpdate-1}][srclang]"]`).value = t
                        .srclang || '';
                    row.querySelector(`input[name="tracks[${trackIndexUpdate-1}][label]"]`).value = t.label ||
                        '';
                });

                document.getElementById('updateForm').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } catch (err) {
                showStatus(err.message, false);
            }
        }

        // ---------- CREATE ----------
        document.getElementById('createForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const fd = collectFormData(e.target);
                const res = await apiFetch(`${API_BASE}videos`, {
                    method: 'POST',
                    body: fd
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Failed to create video.');
                showStatus('Video created successfully.', true);
                e.target.reset();
                tracksWrapper.innerHTML = '';
                trackIndex = 0;
                await loadVideos(1);
            } catch (err) {
                showStatus(err.message, false);
            }
        });

        // ---------- UPDATE ----------
        document.getElementById('updateForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const id = extractId(e.target);
                const fd = collectFormData(e.target);
                const res = await apiFetch(`${API_BASE}videos/${id}`, {
                    method: 'POST',
                    body: fd,
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    } // safer with multipart
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Failed to update video.');
                showStatus('Video updated successfully.', true);
                await loadVideos(currentPage);
            } catch (err) {
                showStatus(err.message, false);
            }
        });

        // ---------- DELETE ----------
        document.getElementById('deleteForm')?.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const id = extractId(e.target);
                const res = await apiFetch(`${API_BASE}videos/${id}`, {
                    method: 'DELETE'
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    throw new Error(data.message || 'Failed to delete video.');
                }
                showStatus('Video deleted successfully.', true);
                e.target.reset();
                await loadVideos(currentPage);
            } catch (err) {
                showStatus(err.message, false);
            }
        });

        // Initial load
        document.addEventListener('DOMContentLoaded', () => loadVideos(1));
    </script>
@endsection
