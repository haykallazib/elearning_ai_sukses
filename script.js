// ======================== DATA PERTANYAAN ========================
const questions = [
    { text: "Saya merasa lebih mudah menangkap informasi ketika disajikan dalam bentuk diagram atau grafik visual.", options: { "Diagram/Grafik": "visual", "Penjelasan lisan": "auditory", "Teks deskriptif": "reading", "Praktik langsung": "kinesthetic" } },
    { text: "Dalam proses belajar, saya lebih nyaman mendengarkan penjelasan dari pada harus membaca buku teks.", options: { "Mendengarkan audio": "auditory", "Melihat video": "visual", "Membaca modul": "reading", "Mencoba sendiri": "kinesthetic" } },
    { text: "Saya cenderung membuat catatan berwarna-warni atau peta pikiran (mind map) untuk mengorganisir ide.", options: { "Mind map warna": "visual", "Catatan panjang": "reading", "Rekaman suara": "auditory", "Post-it di dinding": "kinesthetic" } },
    { text: "Saya lebih mengingat suatu hal dengan cara mempraktikkannya secara langsung, bukan sekadar teori.", options: { "Praktik langsung": "kinesthetic", "Mendengarkan cerita": "auditory", "Membaca ulang": "reading", "Melihat contoh gambar": "visual" } },
    { text: "Saya senang membaca buku, artikel, atau jurnal untuk memahami konsep-konsep baru.", options: { "Membaca teks": "reading", "Mendengarkan podcast": "auditory", "Melihat infografis": "visual", "Belajar sambil praktek": "kinesthetic" } },
    { text: "Saya lebih cepat paham ketika instruktur memberikan contoh nyata atau simulasi yang bisa langsung diamati.", options: { "Simulasi nyata": "kinesthetic", "Contoh gambar": "visual", "Studi kasus tertulis": "reading", "Diskusi kelompok": "auditory" } },
    { text: "Saya merasa terbantu jika ada teks atau subtitle saat menonton video pembelajaran.", options: { "Teks/subtitle": "reading", "Visual tanpa teks": "visual", "Audio narasi": "auditory", "Interaksi klik": "kinesthetic" } },
    { text: "Saya suka belajar sambil mendengarkan musik atau mengikuti diskusi ringan.", options: { "Diskusi/musik": "auditory", "Membaca sendiri": "reading", "Menggambar": "visual", "Bergerak/berjalan": "kinesthetic" } }
];

let currentUserId = null;
let currentActivityId = null;
let currentStyleTarget = null;
let currentQuizQuestions = null;

// ======================== GENERATE KUESIONER ========================
function generateQuestions() {
    const container = document.getElementById('questions-container');
    if (!container) {
        console.error('Element #questions-container tidak ditemukan');
        return;
    }
    container.innerHTML = '';
    questions.forEach((q, idx) => {
        const div = document.createElement('div');
        div.className = 'question';
        div.innerHTML = `<p><strong>${idx+1}. ${q.text}</strong></p>`;
        for (const [label, value] of Object.entries(q.options)) {
            div.innerHTML += `
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="q${idx}" value="${value}" id="q${idx}_${label.replace(/\s/g, '')}">
                    <label class="form-check-label" for="q${idx}_${label.replace(/\s/g, '')}">${label}</label>
                </div>
            `;
        }
        container.appendChild(div);
    });
    console.log('Kuesioner siap');
}

// ======================== UNDUH FILE TEKS ========================
function downloadTxt(content, filename) {
    const blob = new Blob([content], { type: 'text/plain' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename;
    link.click();
    URL.revokeObjectURL(link.href);
}

// ======================== TAMPILKAN KONTEN AKTIVITAS & FORM NILAI ========================
function tampilkanKonten(activityId, styleTarget, tipe, url) {
    currentActivityId = activityId;
    currentStyleTarget = styleTarget;

    const kontenDiv = document.getElementById('kontenAktivitas');
    const performaForm = document.getElementById('performaForm');
    if (!kontenDiv || !performaForm) return;

    kontenDiv.innerHTML = '';
    performaForm.innerHTML = '';

    // Tampilkan konten sesuai tipe
    if (tipe === 'video') {
        kontenDiv.innerHTML = `<div class="ratio ratio-16x9"><iframe src="${url}" allowfullscreen></iframe></div>`;
    } else if (tipe === 'teks') {
        kontenDiv.innerHTML = `
            <div class="border p-2 rounded">
                <iframe src="${url}" width="100%" height="400" style="border:none"></iframe>
                <textarea id="ringkasan" class="form-control mt-2" rows="4" placeholder="Tulis ringkasanmu..."></textarea>
                <button id="unduhRingkasan" class="btn btn-secondary btn-sm mt-1">📥 Unduh Ringkasan</button>
            </div>
        `;
        document.getElementById('unduhRingkasan')?.addEventListener('click', () => {
            const txt = document.getElementById('ringkasan').value;
            if (txt.trim()) downloadTxt(txt, 'ringkasan.txt');
            else alert('Tulis ringkasan terlebih dahulu');
        });
    } else if (tipe === 'praktik') {
        kontenDiv.innerHTML = `
            <div class="border p-2 rounded">
                <p><strong>Instruksi Praktik:</strong> ${url}</p>
                <textarea id="pengalaman" class="form-control mt-2" rows="4" placeholder="Ceritakan pengalaman praktikmu..."></textarea>
                <button id="unduhPengalaman" class="btn btn-secondary btn-sm mt-1">📥 Unduh Pengalaman</button>
            </div>
        `;
        document.getElementById('unduhPengalaman')?.addEventListener('click', () => {
            const txt = document.getElementById('pengalaman').value;
            if (txt.trim()) downloadTxt(txt, 'pengalaman.txt');
            else alert('Tulis pengalaman terlebih dahulu');
        });
    }

    // Form penilaian performa
    performaForm.innerHTML = `
        <div class="mt-3 p-3 bg-light rounded">
            <label class="form-label"><strong>📊 Beri nilai performa aktivitas ini (0-100):</strong></label>
            <div class="input-group">
                <input type="number" id="skorPerforma" class="form-control" min="0" max="100" value="">
                <button id="submitPerformaBtn" class="btn btn-primary">Kirim & Update Profil AI</button>
            </div>
        </div>
    `;

    document.getElementById('submitPerformaBtn')?.addEventListener('click', async () => {
        const skor = document.getElementById('skorPerforma').value;
        const summary = document.getElementById('ringkasan')?.value || document.getElementById('pengalaman')?.value || '';
        if (!currentUserId) {
            alert('User ID tidak ditemukan, refresh halaman');
            return;
        }
        try {
            // Update ke Node.js (AI)
            const resAI = await fetch('http://localhost:3000/update_performance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    userId: currentUserId,
                    activity_id: currentActivityId,
                    score_performance: skor,
                    old_style: currentStyleTarget
                })
            });
            const dataAI = await resAI.json();
            if (!dataAI.success) throw new Error(dataAI.message);

            // Simpan log ke PHP
            await fetch('save_activity.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    userId: currentUserId,
                    activity_id: currentActivityId,
                    score_performance: skor,
                    style_before: currentStyleTarget,
                    summary: summary
                })
            });
            alert(`✅ Profil diperbarui! Gaya baru: ${dataAI.new_learning_style.toUpperCase()}`);
            if (document.getElementById('ringkasan')) document.getElementById('ringkasan').value = '';
            if (document.getElementById('pengalaman')) document.getElementById('pengalaman').value = '';
        } catch (err) {
            alert('❌ Gagal update performa: ' + err.message);
        }
    });
}

// ======================== ANALISIS GAYA BELAJAR ========================
async function analisisGayaBelajar() {
    if (!currentUserId) {
        alert('User ID tidak ditemukan. Silakan refresh halaman.');
        return;
    }
    // Kumpulkan jawaban
    const answers = [];
    for (let i = 0; i < questions.length; i++) {
        const selected = document.querySelector(`input[name="q${i}"]:checked`);
        if (!selected) {
            alert(`Jawab pertanyaan ${i+1} terlebih dahulu.`);
            return;
        }
        answers.push(selected.value);
    }

    const resultArea = document.getElementById('resultArea');
    const resultDetail = document.getElementById('resultDetail');
    resultArea.style.display = 'block';
    resultDetail.innerHTML = '<p>🤖 AI sedang menganalisis...</p>';
    document.getElementById('rekomendasiAktivitas').innerHTML = '';
    document.getElementById('kontenAktivitas').innerHTML = '';
    document.getElementById('performaForm').innerHTML = '';

    try {
        const response = await fetch('http://localhost:3000/analyze', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ answers, userId: currentUserId })
        });
        const data = await response.json();
        if (data.success) {
            resultDetail.innerHTML = `
                <p><strong>Gaya Belajar Utama:</strong> ${data.learning_style.toUpperCase()}</p>
                <p>Skor: Visual ${data.scores.visual}, Auditory ${data.scores.auditory}, Reading ${data.scores.reading}, Kinesthetic ${data.scores.kinesthetic}</p>
            `;
            // Tampilkan rekomendasi aktivitas
            const rekomDiv = document.getElementById('rekomendasiAktivitas');
            if (data.activities && data.activities.length > 0) {
                let recHtml = `<h5>📌 Rekomendasi Aktivitas:</h5><ul class="list-group">`;
                data.activities.forEach(act => {
                    recHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                        <strong>${act.title}</strong> (${act.type})
                        <button class="btn btn-sm btn-outline-primary ambilAktivitasBtn" 
                            data-id="${act.id}" 
                            data-style="${act.style_target}" 
                            data-type="${act.type}" 
                            data-url="${act.content_url}">
                            Ambil Aktivitas
                        </button>
                    </li>`;
                });
                recHtml += `</ul>`;
                rekomDiv.innerHTML = recHtml;
                // Pasang event listener untuk tombol ambil aktivitas
                document.querySelectorAll('.ambilAktivitasBtn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        tampilkanKonten(btn.dataset.id, btn.dataset.style, btn.dataset.type, btn.dataset.url);
                    });
                });
            } else {
                rekomDiv.innerHTML = '<p class="text-danger">Tidak ada rekomendasi aktivitas. Silakan hubungi admin untuk menambahkan aktivitas.</p>';
            }
            // Simpan hasil ke PHP (opsional)
            await fetch('save_result.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ userId: currentUserId, learning_style: data.learning_style, scores: data.scores })
            });
        } else {
            resultDetail.innerHTML = `<p>❌ Error: ${data.message}</p>`;
        }
    } catch (err) {
        resultDetail.innerHTML = `<p>❌ Gagal terhubung ke AI server. Pastikan Node.js berjalan di port 3000.</p>`;
        console.error(err);
    }
}

// ======================== GENERATE QUIZ DENGAN GEMINI ========================
async function generateQuiz() {
    const topic = document.getElementById('quizTopic').value.trim();
    const numQuestions = parseInt(document.getElementById('quizCount').value);
    if (!topic) {
        alert('Masukkan topik terlebih dahulu');
        return;
    }
    if (!currentUserId) {
        alert('User ID tidak ditemukan, refresh halaman');
        return;
    }
    // Ambil gaya belajar dari hasil analisis terakhir (jika ada)
    let learningStyle = 'reading';
    const resultDetailText = document.getElementById('resultDetail')?.innerText || '';
    if (resultDetailText.includes('VISUAL')) learningStyle = 'visual';
    else if (resultDetailText.includes('AUDITORY')) learningStyle = 'auditory';
    else if (resultDetailText.includes('READING')) learningStyle = 'reading';
    else if (resultDetailText.includes('KINESTHETIC')) learningStyle = 'kinesthetic';

    const btn = document.getElementById('generateQuizBtn');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';

    try {
        const response = await fetch('http://localhost:3000/generate-quiz', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ topic, numQuestions, learningStyle, userId: currentUserId })
        });
        const data = await response.json();
        if (data.success) {
            currentQuizQuestions = data.questions;
            displayQuiz(currentQuizQuestions);
            document.getElementById('quizResultArea').style.display = 'block';
            alert(`✅ Quiz berhasil dibuat! ${numQuestions} soal tentang "${topic}" siap dikerjakan.`);
        } else {
            alert('❌ Gagal generate quiz: ' + data.message);
        }
    } catch (err) {
        alert('❌ Error: ' + err.message);
    } finally {
        btn.disabled = false;
        btn.innerHTML = originalText;
    }
}

// ======================== TAMPILKAN QUIZ ========================
function displayQuiz(questions) {
    const container = document.getElementById('quizQuestions');
    if (!container) return;
    container.innerHTML = '';
    questions.forEach((q, idx) => {
        // Bersihkan options: hilangkan "A. ", "B. " dll jika ada duplikasi
        let cleanOptions = q.options.map(opt => {
            // Hapus awalan huruf dan titik (misal "A. Teks" -> "Teks")
            let cleaned = opt.replace(/^[A-D]\.\s*/, '');
            return cleaned;
        });
        const div = document.createElement('div');
        div.className = 'mb-3 border rounded p-2';
        div.innerHTML = `<p><strong>${idx+1}. ${q.question}</strong></p>`;
        cleanOptions.forEach((opt, optIdx) => {
            const letter = String.fromCharCode(65 + optIdx);
            div.innerHTML += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="quiz${idx}" value="${letter}" id="quiz${idx}_${optIdx}">
                    <label class="form-check-label" for="quiz${idx}_${optIdx}">${letter}. ${opt}</label>
                </div>
            `;
        });
        container.appendChild(div);
    });
    document.getElementById('quizFeedback').innerHTML = '';
}

// ======================== SUBMIT JAWABAN QUIZ ========================
async function submitQuiz() {
    if (!currentQuizQuestions || currentQuizQuestions.length === 0) {
        alert('Tidak ada quiz yang sedang dikerjakan');
        return;
    }
    
    let score = 0;
    let userAnswers = [];
    let feedback = '';
    
    for (let i = 0; i < currentQuizQuestions.length; i++) {
        const selected = document.querySelector(`input[name="quiz${i}"]:checked`);
        const userAnswer = selected ? selected.value : null;
        userAnswers.push(userAnswer);
        const q = currentQuizQuestions[i];
        const isCorrect = (userAnswer === q.correct);
        if (isCorrect) score++;
        feedback += `<div class="mb-2 p-2 ${isCorrect ? 'bg-success-subtle' : 'bg-danger-subtle'} rounded">
            <strong>Soal ${i+1}:</strong> ${q.question}<br>
            <strong>Jawaban Anda:</strong> ${userAnswer || 'Tidak dijawab'} ${isCorrect ? '✅' : '❌'}<br>
            <strong>Jawaban Benar:</strong> ${q.correct}. ${q.options[q.correct.charCodeAt(0)-65]}<br>
            <strong>Penjelasan:</strong> ${q.explanation}
        </div>`;
    }
    
    const total = currentQuizQuestions.length;
    const percentage = (score / total) * 100;
    feedback = `<h5>Skor Anda: ${score}/${total} (${percentage.toFixed(1)}%)</h5>` + feedback;
    document.getElementById('quizFeedback').innerHTML = feedback;

    // ========== SIMPAN KE RIWAYAT ==========
    const topic = document.getElementById('quizTopic').value.trim();
    const summary = `Topik: ${topic}\nSkor: ${score}/${total} (${percentage.toFixed(1)}%)\nDetail Jawaban: ${JSON.stringify(userAnswers)}`;
    
    // Di dalam fungsi submitQuiz, setelah menghitung skor dan percentage
if (currentUserId) {
    const topic = document.getElementById('quizTopic')?.value.trim() || 'Quiz AI';
    try {
        // 1. Kirim ke AI (update_performance) - opsional
        await fetch('http://localhost:3000/update_performance', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                userId: currentUserId,
                activity_id: 999,
                score_performance: percentage,
                old_style: 'quiz'
            })
        });
        
        // 2. Kirim ke PHP untuk simpan riwayat
        const res = await fetch('save_activity.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                userId: currentUserId,
                activity_id: 999,
                score_performance: percentage,
                style_before: 'quiz',
                summary: `Topik: ${topic} | Skor: ${score}/${total}`
            })
        });
        const result = await res.json();
        if (result.status === 'success') {
            console.log('✅ Riwayat quiz tersimpan');
        } else {
            console.error('Gagal simpan riwayat:', result.message);
        }
    } catch (err) {
        console.error('Error:', err);
    }
}
    
    // (Opsional) Kirim performa ke AI untuk update profil
    if (currentUserId) {
        try {
            await fetch('http://localhost:3000/update_performance', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    userId: currentUserId,
                    activity_id: 999,
                    score_performance: percentage,
                    old_style: 'quiz'
                })
            });
        } catch (err) {
            console.error('Gagal update AI:', err);
        }
    }
}
// ======================== INITIALIZATION ========================
document.addEventListener('DOMContentLoaded', async () => {
    generateQuestions();

    // Ambil user ID dari session PHP
    try {
        const res = await fetch('get_user_id.php');
        const data = await res.json();
        if (data.userId) {
            currentUserId = data.userId;
            console.log('User ID:', currentUserId);
            const navbarNama = document.getElementById('navbarNama');
            if (navbarNama && data.nama) navbarNama.innerText = data.nama;
        } else {
            console.warn('User ID tidak ditemukan');
        }
    } catch (err) {
        console.error('Gagal ambil user ID:', err);
    }

    // Tombol analisis
    const analyzeBtn = document.getElementById('analyzeBtn');
    if (analyzeBtn) analyzeBtn.onclick = analisisGayaBelajar;

    // Tombol generate quiz
    const genQuizBtn = document.getElementById('generateQuizBtn');
    if (genQuizBtn) genQuizBtn.onclick = generateQuiz;

    // Tombol submit quiz
    const submitQuizBtn = document.getElementById('submitQuizBtn');
    if (submitQuizBtn) submitQuizBtn.onclick = submitQuiz;

    // Navbar smooth scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }

    
});
});