require('dotenv').config();
const express = require('express');
const cors = require('cors');
const bodyParser = require('body-parser');
const mysql = require('mysql2/promise');
require('dotenv').config();

const app = express();
const PORT = process.env.PORT || 3000;

app.use(cors());
app.use(bodyParser.json());

let db;
(async () => {
    try {
        db = await mysql.createConnection({
            host: process.env.DB_HOST || 'localhost',
            user: process.env.DB_USER || 'root',
            password: process.env.DB_PASSWORD || '',
            database: process.env.DB_NAME || 'elearning_ai_final'
        });
        console.log('MySQL terhubung');
    } catch (err) {
        console.error('Koneksi DB gagal:', err);
    }
})();

// ========== ENDPOINT ANALISIS GAYA BELAJAR ==========
app.post('/analyze', async (req, res) => {
    const { answers, userId } = req.body;
    if (!answers || answers.length !== 8) {
        return res.status(400).json({ success: false, message: 'Jawaban tidak lengkap' });
    }
    let scores = { visual:0, auditory:0, reading:0, kinesthetic:0 };
    for (let a of answers) scores[a]++;
    
    // Ambil bobot aturan dari DB (opsional)
    let rules = [{ visual_weight:1, auditory_weight:1, reading_weight:1, kinesthetic_weight:1 }];
    try {
        const [rows] = await db.execute('SELECT * FROM classification_rules ORDER BY id DESC LIMIT 1');
        if (rows.length) rules = rows;
    } catch(e) {}
    const w = rules[0];
    let weighted = {
        visual: scores.visual * w.visual_weight,
        auditory: scores.auditory * w.auditory_weight,
        reading: scores.reading * w.reading_weight,
        kinesthetic: scores.kinesthetic * w.kinesthetic_weight
    };
    let maxStyle = Object.keys(weighted).reduce((a,b) => weighted[a] > weighted[b] ? a : b);
    
    // Ambil rekomendasi aktivitas dari database
    let activities = [];
    try {
        if (db) {
            const [rows] = await db.execute('SELECT * FROM activities WHERE style_target = ?', [maxStyle]);
            activities = rows;
            console.log(`Aktivitas ditemukan untuk gaya ${maxStyle}: ${activities.length}`);
        }
    } catch (err) {
        console.error('Gagal ambil aktivitas:', err);
    }
    
    res.json({ success: true, learning_style: maxStyle, scores: weighted, activities: activities });
});


// ========== UPDATE PERFORMANCE ==========
app.post('/update_performance', async (req, res) => {
    const { userId, activity_id, score_performance, old_style } = req.body;
    if (!userId || !activity_id) return res.status(400).json({ success: false });
    try {
        let styleTarget = 'reading';
        if (activity_id !== 999) {
            const [act] = await db.execute('SELECT style_target FROM activities WHERE id = ?', [activity_id]);
            if (act.length) styleTarget = act[0].style_target;
        }
        const increment = (score_performance / 100) * 2;
        await db.execute(`UPDATE users SET ${styleTarget}_score = ${styleTarget}_score + ? WHERE id = ?`, [increment, userId]);
        const [user] = await db.execute('SELECT visual_score, auditory_score, reading_score, kinesthetic_score FROM users WHERE id = ?', [userId]);
        if (user.length) {
            const u = user[0];
            const newScores = { visual: u.visual_score, auditory: u.auditory_score, reading: u.reading_score, kinesthetic: u.kinesthetic_score };
            let newStyle = Object.keys(newScores).reduce((a,b) => newScores[a] > newScores[b] ? a : b);
            await db.execute('UPDATE users SET learning_style = ? WHERE id = ?', [newStyle, userId]);
            return res.json({ success: true, new_learning_style: newStyle });
        }
        res.json({ success: true, new_learning_style: old_style });
    } catch (err) {
        console.error(err);
        res.status(500).json({ success: false, message: err.message });
    }
});

// ========== GENERATE QUIZ ==========
// ========== GENERATE QUIZ DENGAN OPENROUTER ==========
app.post('/generate-quiz', async (req, res) => {
    const { topic, numQuestions, learningStyle, userId } = req.body;
    if (!topic || !numQuestions) {
        return res.status(400).json({ success: false, message: 'Topik dan jumlah soal diperlukan' });
    }

    // Pilih model gratis dari OpenRouter
    const model = 'tencent/hy3-preview:free';
    // Alternatif: 'microsoft/phi-3-mini-128k-instruct', 'meta-llama/llama-3.2-3b-instruct'

    let styleInstruction = '';
    if (learningStyle === 'visual') styleInstruction = 'Gunakan deskripsi visual, diagram, atau skenario yang mudah dibayangkan.';
    else if (learningStyle === 'auditory') styleInstruction = 'Gunakan skenario percakapan atau narasi.';
    else if (learningStyle === 'reading') styleInstruction = 'Gunakan teks yang detail dan deskriptif.';
    else if (learningStyle === 'kinesthetic') styleInstruction = 'Gunakan skenario praktik langsung.';

    const prompt = `Buatkan ${numQuestions} soal pilihan ganda tentang "${topic}". ${styleInstruction} Setiap soal memiliki 4 pilihan (A, B, C, D) dan satu jawaban benar. Format respons harus JSON array dengan struktur:
    [
        {
            "question": "teks soal",
            "options": ["pilihan A", "pilihan B", "pilihan C", "pilihan D"],
            "correct": "huruf jawaban (A/B/C/D)",
            "explanation": "penjelasan singkat"
        }
    ]
    Hanya kirim JSON, tanpa teks tambahan. Batasi total jawaban maksimal 500 kata dalam bahasa indonesia.`;

    try {
        const response = await fetch('https://openrouter.ai/api/v1/chat/completions', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${process.env.OPENROUTER_API_KEY}`,
                'Content-Type': 'application/json',
                'HTTP-Referer': 'http://localhost:8080',
                'X-Title': 'elearning_ai_final'
            },
            body: JSON.stringify({
                model: "tencent/hy3-preview:free",
                messages: [{ role: 'user', content: prompt }]
            })
        });

        const data = await response.json();
        if (data.error) {
            console.error('OpenRouter error:', data.error);
            return res.status(500).json({ success: false, message: data.error.message });
        }

        let content = data.choices[0].message.content;
        // Bersihkan markdown JSON
        content = content.replace(/```json/g, '').replace(/```/g, '').trim();
        const quizData = JSON.parse(content);
        if (!Array.isArray(quizData) || quizData.length !== numQuestions) {
            throw new Error('Format JSON tidak sesuai');
        }

        // Simpan ke database (jika diperlukan)
        if (db && userId) {
            await db.execute(
                'INSERT INTO ai_generated_quizzes (user_id, topic, num_questions, questions) VALUES (?, ?, ?, ?)',
                [userId, topic, numQuestions, JSON.stringify(quizData)]
            );
        }
        res.json({ success: true, questions: quizData });
    } catch (err) {
        console.error('Generate quiz error:', err);
        res.status(500).json({ success: false, message: err.message });
    }
});


app.listen(PORT, () => {
    console.log(`Server AI berjalan di http://localhost:${PORT}`);
});