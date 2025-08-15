@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto p-6 bg-white border rounded-xl shadow-sm">
        <h1 class="text-2xl font-bold mb-6">Video Manager (Token Auth)</h1>

        {{-- Status box --}}
        <div id="statusBox" class="hidden mb-4 p-3 rounded bg-gray-100 text-gray-800"></div>

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
        <div class="border rounded-lg p-5">
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
        </div>
    </div>

    {{-- Token/Config for JS --}}
    <script>
        const API_BASE = `{{ env('API_BASE_URL') }}`; // e.g., https://your-app.test/api
        const API_TOKEN = `{{ session()->get('api_token') }}`; // Sanctum personal access token for this page
    </script>

    {{-- Minimal JS helpers --}}
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
            const headers = options.headers || {};
            headers['Authorization'] = `Bearer ${API_TOKEN}`;
            // DO NOT set Content-Type when sending FormData (browser sets boundary)
            return fetch(url, {
                ...options,
                headers
            });
        }

        function collectFormData(formEl) {
            return new FormData(formEl); // includes files + fields
        }

        function extractId(formEl) {
            const id = (formEl.querySelector('input[name="id"]')?.value || '').trim();
            if (!id) throw new Error('Please provide a valid numeric ID.');
            return id;
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
                // Reset tracks UI
                tracksWrapper.innerHTML = '';
                trackIndex = 0;
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
                    }
                });
                // Alternative: use method:'PUT' — but some servers need X-HTTP-Method-Override with multipart.
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw new Error(data.message || 'Failed to update video.');
                showStatus('Video updated successfully.', true);
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
            } catch (err) {
                showStatus(err.message, false);
            }
        });
    </script>
@endsection
