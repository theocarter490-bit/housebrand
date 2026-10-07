@extends('layouts.master')

@section('content')
    <style>
        #canvasWrapper {
            position: relative;
            background-color: #ffffff;
            background-image:
                /* small grid */
                linear-gradient(#180ace 1px, transparent 1px),
                linear-gradient(90deg, #1354ce 1px, transparent 1px),

                /* big grid (every 5 cells) */
                linear-gradient(#bd28b5 1px, transparent 1px),
                linear-gradient(90deg, #e03d23 1px, transparent 1px);
            background-size:
                24px 24px,
                24px 24px,
                120px 120px,
                120px 120px;
        }

        /* -------------------------
                SIDEBAR WITH TABS
            ------------------------- */
        .tool-sidebar {
            background: #f8fafc;
            color: #1e293b;
            height: 80vh;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }

        .tab-header {
            display: flex;
            border-bottom: 1px solid #e2e8f0;
            background: #ffffff;
            border-radius: 12px 12px 0 0;
        }

        .tab-header button {
            flex: 1;
            padding: 8px 0;
            border: none;
            background: #f1f5f9;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            border-bottom: 2px solid transparent;
        }

        .tab-header button.active {
            background: #ffffff;
            border-bottom: 2px solid #2563eb;
            color: #2563eb;
        }

        .tab-content {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }

        .tool-section {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 10px;
            margin-bottom: 12px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .05);
        }

        .tool-section-title {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .tool-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 6px;
            margin-bottom: 4px;
            background: #f1f5f9;
            border: 1px solid #cbd5e1;
            color: #1e293b;
            border-radius: 8px;
            padding: 6px 8px;
            font-size: 12px;
            transition: all .2s;
        }

        .tool-btn:hover {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }

        .product-thumb {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 4px;
            background: #ffffff;
            cursor: grab;
            transition: all .2s;
        }

        .product-thumb:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, .12);
            transform: scale(1.03);
        }

        .product-grid-title {
            font-size: 11px;
            font-weight: 600;
            color: #4b5563;
            margin-bottom: 6px;
        }

        .action-buttons {
            z-index: 1000;
        }

        .btn-float {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: all 0.2s;
            backdrop-filter: blur(6px);
            background-color: rgba(255, 255, 255, 0.85);
        }

        .btn-float.save {
            color: #fff;
            background: #16a34a;
        }

        .btn-float.clear {
            color: #fff;
            background: #ef4444;
        }

        .btn-float.export {
            color: #fff;
            background: #2563eb;
        }

        .btn-float:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
            opacity: 0.95;
        }

        .canvas-delete-btn {
            position: absolute;
            z-index: 2000;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            border: none;
            background: #ef4444;
            color: #fff;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .2);
            transition: all .15s;
        }

        .canvas-delete-btn:hover {
            transform: scale(1.1);
            background: #dc2626;
        }
    </style>

    <div class="container-fluid">
        <div class="row g-3">

            <!-- SIDEBAR -->
            <div class="col-md-3">
                <div class="tool-sidebar">

                    <!-- Tabs -->
                    <div class="tab-header">
                        <button class="active" data-tab="toolsTab">Tools</button>
                        <button data-tab="productsTab">Products</button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="tab-content">

                        <!-- TOOLS TAB -->
                        <div id="toolsTab" class="tab-panel">

                            <!-- CREATE -->
                            <div class="tool-section">
                                <div class="tool-section-title">Create</div>
                                <button class="tool-btn" id="addText">📝 Add Text</button>
                                <button class="tool-btn" id="setBackground">🖼 Set Background</button>
                                <div class="tool-hint">Or paste image with Ctrl+V</div>
                            </div>

                            <!-- DEFAULT BACKGROUNDS -->
                            <div class="tool-section">
                                <div class="tool-section-title">Default Backgrounds</div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <img src="{{ asset('assets/img/blank-room/1.jpeg') }}"
                                            class="img-fluid product-thumb bg-demo"
                                            data-url="{{ asset('assets/img/blank-room/1.jpeg') }}">
                                    </div>
                                    <div class="col-6">
                                        <img src="{{ asset('assets/img/blank-room/2.jpg') }}"
                                            class="img-fluid product-thumb bg-demo"
                                            data-url="{{ asset('assets/img/blank-room/2.jpg') }}">
                                    </div>
                                    <div class="col-6">
                                        <img src="{{ asset('assets/img/blank-room/3.jpg') }}"
                                            class="img-fluid product-thumb bg-demo"
                                            data-url="{{ asset('assets/img/blank-room/3.jpg') }}">
                                    </div>
                                    <div class="col-6">
                                        <input type="file" id="uploadBg" class="form-control form-control-sm mt-1"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- LAYERS -->
                            <div class="tool-section">
                                <div class="tool-section-title">Layer Order</div>
                                <button class="tool-btn" id="bringForward">⬆ Bring Forward</button>
                                <button class="tool-btn" id="sendBackward">⬇ Send Backward</button>
                            </div>

                            <!-- TRANSFORM -->
                            <div class="tool-section">
                                <div class="tool-section-title">Transform</div>
                                <button class="tool-btn" id="rotateLeft">⟲ Rotate Left</button>
                                <button class="tool-btn" id="rotateRight">⟳ Rotate Right</button>
                                <button class="tool-btn" id="flipH">⇋ Flip Horizontal</button>
                                <button class="tool-btn" id="flipV">⇅ Flip Vertical</button>
                            </div>

                            <!-- TEXT STYLING -->
                            <div class="tool-section">
                                <div class="tool-section-title">Text Styling</div>
                                <select id="fontFamily" class="form-select form-select-sm mb-1">
                                    <option value="Arial">Arial</option>
                                    <option value="Georgia">Georgia</option>
                                    <option value="Times New Roman">Times</option>
                                    <option value="Courier New">Courier</option>
                                    <option value="Montserrat">Montserrat</option>
                                    <option value="Poppins">Poppins</option>
                                </select>
                                <input type="number" id="fontSize" class="form-control form-control-sm"
                                    placeholder="Font Size" value="28">
                            </div>

                        </div>

                        <!-- PRODUCTS TAB -->
                        <div id="productsTab" class="tab-panel" style="display:none;">
                            <div class="tool-section">
                                <div class="product-grid-title">🛋 Product Library</div>
                                <div class="row g-2">
                                    @foreach ($products as $product)
                                        <div class="col-6">
                                            <img src="{{ $product->image_url }}"
                                                class="img-fluid product-thumb product-draggable" draggable="true"
                                                data-image="{{ $product->image_url }}" alt="{{ $product->name }}">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <!-- CANVAS -->
            <div class="col-md-9">
                <!-- TITLE ABOVE CANVAS -->
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h4 class="mb-0">Moodboard Designer</h4>
                    <!-- MODERN BUTTONS OUTSIDE CANVAS -->
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-light" onclick="undo()">↶ Undo</button>
                        <button class="btn btn-sm btn-light" onclick="redo()">↷ Redo</button>
                        <button class="btn-float save" id="saveBoard">💾 Save</button>
                        <button class="btn-float clear" id="clearBoard">🧹 Clear</button>
                        <button class="btn-float export" id="exportImage">📤 Export</button>
                    </div>
                </div>

                <div id="canvasWrapper" class="border rounded-3 position-relative">
                    <canvas id="moodboardCanvas" width="1200" height="700"></canvas>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <script>
        // -------------------
        // TAB SWITCHING
        // -------------------
        document.querySelectorAll('.tab-header button').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.tab-header button').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
                document.getElementById(btn.dataset.tab).style.display = 'block';
            });
        });

        // -------------------
        // FABRIC CANVAS INIT
        // -------------------
        const canvas = new fabric.Canvas('moodboardCanvas', {
            backgroundColor: 'transparent',
            preserveObjectStacking: true
        });
        const canvasWrapper = document.getElementById('canvasWrapper');

        function updateGridVisibility() {
            if (canvas.backgroundImage) {
                canvasWrapper.style.backgroundImage = 'none';
            } else {
                canvasWrapper.style.backgroundImage = `
                    linear-gradient(#e5e7eb 1px, transparent 1px),
                    linear-gradient(90deg, #e5e7eb 1px, transparent 1px),
                    linear-gradient(#cbd5e1 1px, transparent 1px),
                    linear-gradient(90deg, #cbd5e1 1px, transparent 1px)
                `;
                canvasWrapper.style.backgroundSize = `
                    24px 24px,
                    24px 24px,
                    120px 120px,
                    120px 120px
                `;
            }
        }

        /* DRAG & DROP PRODUCTS */
        document.querySelectorAll('.product-draggable').forEach(img => {
            img.addEventListener('dragstart', e => {
                e.dataTransfer.setData('image-url', img.dataset.image);
            });
        });
        canvasWrapper.addEventListener('dragover', e => e.preventDefault());
        canvasWrapper.addEventListener('drop', e => {
            e.preventDefault();
            const url = e.dataTransfer.getData('image-url');
            if (!url) return;
            addImageToCanvas(url);
        });

        /* ADD IMAGE FUNCTION */
        function addImageToCanvas(url, isBackground = false) {
            fabric.Image.fromURL(url, img => {
                if (isBackground) {
                    // Scale to cover canvas while maintaining aspect ratio
                    const canvasRatio = canvas.width / canvas.height;
                    const imgRatio = img.width / img.height;
                    let scaleX, scaleY;

                    if (imgRatio > canvasRatio) {
                        // Image is wider → scale by height
                        scaleY = canvas.height / img.height;
                        scaleX = scaleY;
                    } else {
                        // Image is taller → scale by width
                        scaleX = canvas.width / img.width;
                        scaleY = scaleX;
                    }

                    canvas.setBackgroundImage(img, () => {
                            canvas.renderAll();
                            updateGridVisibility();
                        }, {
                        scaleX: scaleX,
                        scaleY: scaleY,
                        originX: 'left',
                        originY: 'top'
                    });

                } else {
                    // Normal object
                    img.set({
                        left: 100,
                        top: 100,
                        scaleX: 0.4,
                        scaleY: 0.4,
                        cornerStyle: 'circle',
                        borderColor: '#2563eb',
                        cornerColor: '#2563eb',
                        transparentCorners: false
                    });
                    canvas.add(img);
                    canvas.setActiveObject(img);
                }
            }, {
                crossOrigin: 'anonymous'
            });
        }

        /* DEFAULT BACKGROUNDS */
        document.querySelectorAll('.bg-demo').forEach(img => {
            img.addEventListener('click', () => addImageToCanvas(img.dataset.url, true));
        });
        document.getElementById('uploadBg').addEventListener('change', e => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = evt => addImageToCanvas(evt.target.result, true);
                reader.readAsDataURL(file);
            }
        });

        /* PASTE IMAGE */
        document.addEventListener('paste', e => {
            for (let item of e.clipboardData.items) {
                if (item.type.indexOf('image') !== -1) {
                    const file = item.getAsFile();
                    const reader = new FileReader();
                    reader.onload = evt => addImageToCanvas(evt.target.result);
                    reader.readAsDataURL(file);
                }
            }
        });

        /* ADD TEXT */
        addText.onclick = () => {
            const text = new fabric.IText('Your Text', {
                left: 150,
                top: 150,
                fontSize: 28,
                fill: '#111827',
                fontFamily: 'Arial'
            });
            canvas.add(text);
            canvas.setActiveObject(text);
        };


        document.querySelectorAll('.bg-demo').forEach(img => {
            // Allow dragging
            img.setAttribute('draggable', true);

            img.addEventListener('dragstart', e => {
                e.dataTransfer.setData('bg-url', img.dataset.url);
            });

            // Optional: click also sets background
            img.addEventListener('click', () => addImageToCanvas(img.dataset.url, true));
        });

        // DROP HANDLER FOR CANVAS
        canvasWrapper.addEventListener('drop', e => {
            e.preventDefault();

            // Check for product image
            let url = e.dataTransfer.getData('image-url');
            if (url) {
                addImageToCanvas(url);
                return;
            }

            // Check for background image
            url = e.dataTransfer.getData('bg-url');
            if (url) {
                addImageToCanvas(url, true);
            }
        });

        canvasWrapper.addEventListener('dragover', e => e.preventDefault());

        /* BACKGROUND */
        setBackground.onclick = () => {
            const url = prompt('Paste room/background image URL');
            if (url) addImageToCanvas(url, true);
        };

        /* LAYERS */
        bringForward.onclick = () => canvas.getActiveObject() && canvas.bringForward(canvas.getActiveObject());
        sendBackward.onclick = () => canvas.getActiveObject() && canvas.sendBackwards(canvas.getActiveObject());

        /* ROTATE */
        rotateLeft.onclick = () => rotate(-10);
        rotateRight.onclick = () => rotate(10);

        function rotate(angle) {
            const obj = canvas.getActiveObject();
            if (!obj) return;
            obj.rotate((obj.angle || 0) + angle);
            canvas.renderAll();
        }

        /* FLIP */
        flipH.onclick = () => {
            const o = canvas.getActiveObject();
            if (o) {
                o.toggle('flipX');
                canvas.renderAll();
            }
        };
        flipV.onclick = () => {
            const o = canvas.getActiveObject();
            if (o) {
                o.toggle('flipY');
                canvas.renderAll();
            }
        };

        /* FONT CONTROLS */
        fontFamily.onchange = () => {
            const o = canvas.getActiveObject();
            if (o && o.type === 'i-text') {
                o.set('fontFamily', fontFamily.value);
                canvas.renderAll();
            }
        };
        fontSize.onchange = () => {
            const o = canvas.getActiveObject();
            if (o && o.type === 'i-text') {
                o.set('fontSize', parseInt(fontSize.value));
                canvas.renderAll();
            }
        };

        /* DELETE BUTTON ON SELECTION */
        let deleteBtn = null;

        function showDeleteIcon() {
            if (!deleteBtn) {
                deleteBtn = document.createElement('button');
                deleteBtn.className = 'canvas-delete-btn';
                deleteBtn.innerHTML = '🗑'; // icon only
                deleteBtn.title = 'Delete (Del / Backspace)';
                deleteBtn.onclick = deleteSelectedObject;
                document.body.appendChild(deleteBtn);
            }
            positionDeleteIcon();
            deleteBtn.style.display = 'flex';
        }

        function hideDeleteIcon() {
            if (deleteBtn) {
                deleteBtn.style.display = 'none';
            }
        }

        function positionDeleteIcon() {
            if (!deleteBtn) return;
            const obj = canvas.getActiveObject();
            if (!obj) return;

            const bound = obj.getBoundingRect();
            const canvasRect = canvasWrapper.getBoundingClientRect();

            deleteBtn.style.left = (canvasRect.left + bound.left + bound.width - 14) + 'px';
            deleteBtn.style.top = (canvasRect.top + bound.top - 14) + 'px';
        }

        function deleteSelectedObject() {
            const obj = canvas.getActiveObject();
            if (obj) {
                canvas.remove(obj);
                canvas.discardActiveObject();
                canvas.renderAll();
                hideDeleteIcon();
            }
        }

        canvas.on('selection:created', () => showDeleteIcon());
        canvas.on('selection:updated', () => showDeleteIcon());
        canvas.on('selection:cleared', () => hideDeleteIcon());

        canvas.on('object:moving', positionDeleteIcon);
        canvas.on('object:scaling', positionDeleteIcon);
        canvas.on('object:rotating', positionDeleteIcon);

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Delete' || e.key === 'Backspace') {
                const obj = canvas.getActiveObject();

                // Prevent deleting when typing in text input
                const tag = document.activeElement.tagName.toLowerCase();
                if (tag === 'input' || tag === 'textarea') return;

                if (obj) {
                    e.preventDefault();
                    deleteSelectedObject();
                }
            }
        });

        /* ---------- UNDO / REDO (FIXED) ---------- */
        let state = [],
            mods = 0,
            isRestoring = false;

        function saveState() {
            if (isRestoring) return;
            state = state.slice(0, state.length - mods);
            state.push(canvas.toDatalessJSON());
            if (state.length > 50) state.shift();
            mods = 0;
        }

        canvas.on('object:added', saveState);
        canvas.on('object:modified', saveState);
        canvas.on('object:removed', saveState);
        saveState();

        function undo() {
            if (state.length <= 1 || mods >= state.length - 1) return;
            isRestoring = true;
            mods++;
            canvas.loadFromJSON(state[state.length - 1 - mods], () => {
                canvas.renderAll();
                isRestoring = false;
                deleteBtn.style.display = 'none';
            });
        }

        function redo() {
            if (mods === 0) return;
            isRestoring = true;
            mods--;
            canvas.loadFromJSON(state[state.length - 1 - mods], () => {
                canvas.renderAll();
                isRestoring = false;
                deleteBtn.style.display = 'none';
            });
        }

        /* SAVE / CLEAR / EXPORT */
        saveBoard.onclick = () => {
            fetch('{{ route('moodboard.save') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    canvas_json: JSON.stringify(canvas.toJSON())
                })
            }).then(() => alert('Saved!'));
        };
        clearBoard.onclick = () => {
            if (confirm('Clear board?')) {
                canvas.clear();
                canvas.setBackgroundColor('transparent', () => {
                    canvas.renderAll();
                    updateGridVisibility();
                });
            }
        };
        exportImage.onclick = () => {
            const link = document.createElement('a');
            link.download = 'moodboard.png';
            link.href = canvas.toDataURL({
                format: 'png',
                quality: 1
            });
            link.click();
        };

        /* LOAD SAVED */
        @if (isset($moodboard) && $moodboard->canvas_json)
        canvas.loadFromJSON(@json($moodboard->canvas_json), () => {
            canvas.renderAll();
            updateGridVisibility();
        });
        @endif

        updateGridVisibility();
        
    </script>
@endsection
