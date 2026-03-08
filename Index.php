<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>GlassPay Pro | Secure Wallet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap');
        body { background: #000; color: white; font-family: 'Plus Jakarta Sans', sans-serif; -webkit-tap-highlight-color: transparent; overflow-x: hidden; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        /* Animations */
        @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
        .animate-waterfall { opacity: 0; animation: slideUp 0.6s cubic-bezier(0.23, 1, 0.32, 1) forwards; }
        @keyframes shimmer { 0% { opacity: 0.3; } 50% { opacity: 0.6; } 100% { opacity: 0.3; } }
        .skeleton { animation: shimmer 1.5s infinite; background: rgba(255,255,255,0.05); }
        .shake { animation: shake 0.4s focus; }
        @keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-10px); } 75% { transform: translateX(10px); } }
    </style>
</head>
<body class="pb-32">

    <div id="authScreen" class="fixed inset-0 z-[500] bg-black flex flex-col items-center justify-between p-12 transition-all duration-700 ease-in-out">
        <div class="mt-16 text-center animate-waterfall">
            <div class="w-20 h-20 bg-gradient-to-tr from-purple-600 to-indigo-600 rounded-[2.5rem] mx-auto mb-6 flex items-center justify-center shadow-2xl shadow-purple-500/30">
                <i class="fas fa-fingerprint text-3xl"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tighter">GlassPay</h1>
            <p class="text-[10px] opacity-40 font-black uppercase tracking-[0.4em] mt-3">Enter Secure PIN</p>
        </div>

        <div id="pinContainer" class="flex gap-6 mb-8">
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-white/20 transition-all duration-300"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-white/20 transition-all duration-300"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-white/20 transition-all duration-300"></div>
            <div class="pin-dot w-4 h-4 rounded-full border-2 border-white/20 transition-all duration-300"></div>
        </div>

        <div class="grid grid-cols-3 gap-6 w-full max-w-xs mb-16">
            <script>
                for (let i = 1; i <= 9; i++) document.write(`<button onclick="pressPin(${i})" class="w-20 h-20 glass rounded-full text-2xl font-black active:scale-90 transition">${i}</button>`);
            </script>
            <button onclick="triggerBiometrics()" class="flex items-center justify-center text-purple-400 active:scale-90 transition"><i class="fas fa-face-viewfinder text-2xl"></i></button>
            <button onclick="pressPin(0)" class="w-20 h-20 glass rounded-full text-2xl font-black active:scale-90 transition">0</button>
            <button onclick="backspacePin()" class="flex items-center justify-center opacity-40 active:scale-90 transition"><i class="fas fa-backspace text-xl"></i></button>
        </div>
    </div>

    <header class="p-6 flex justify-between items-center sticky top-0 z-50 bg-black/50 backdrop-blur-lg">
        <div>
            <p class="text-[10px] opacity-40 font-black uppercase tracking-widest">Available Balance</p>
            <div class="flex items-center gap-2">
                <span class="text-xl font-black opacity-30">₦</span>
                <h2 id="balanceText" class="text-2xl font-black tracking-tight">540,250.00</h2>
                <i id="balanceIcon" onclick="toggleBalance()" class="fas fa-eye text-xs opacity-30 ml-1 cursor-pointer"></i>
            </div>
        </div>
        <div class="w-12 h-12 glass rounded-2xl flex items-center justify-center border-white/20" onclick="switchTab('profile')">
            <i class="fas fa-user-circle opacity-50"></i>
        </div>
    </header>

    <div class="px-4 mt-4 animate-waterfall" style="animation-delay: 100ms">
        <div class="glass p-6 rounded-[2.5rem]">
            <div class="flex justify-between items-center mb-6">
                <h4 class="text-[10px] font-black opacity-30 uppercase tracking-widest">Weekly Spend</h4>
                <span class="text-[10px] text-emerald-400 font-bold bg-emerald-400/10 px-2 py-1 rounded-lg">↓ 12%</span>
            </div>
            <div class="flex items-end justify-between h-20 gap-3 px-2">
                <div class="flex-1 bg-white/5 rounded-full h-16 relative flex items-end"><div class="w-full bg-purple-600 rounded-full h-[40%]"></div></div>
                <div class="flex-1 bg-white/5 rounded-full h-16 relative flex items-end"><div class="w-full bg-purple-600 rounded-full h-[70%]"></div></div>
                <div class="flex-1 bg-white/5 rounded-full h-16 relative flex items-end"><div class="w-full bg-purple-600 rounded-full h-[30%]"></div></div>
                <div class="flex-1 bg-white/5 rounded-full h-16 relative flex items-end"><div class="w-full bg-purple-600 rounded-full h-[90%]"></div></div>
                <div class="flex-1 bg-white/10 border border-white/10 rounded-full h-16 relative flex items-end"><div class="w-full bg-white rounded-full h-[50%] shadow-[0_0_10px_white]"></div></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-4 gap-3 px-4 mt-8 animate-waterfall" style="animation-delay: 200ms">
        <button onclick="openModal('data')" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-purple-400 active:scale-90 transition"><i class="fas fa-wifi"></i></div>
            <span class="text-[9px] font-bold opacity-50 uppercase">Data</span>
        </button>
        <button onclick="openModal('electric')" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-amber-400 active:scale-90 transition"><i class="fas fa-bolt"></i></div>
            <span class="text-[9px] font-bold opacity-50 uppercase">Power</span>
        </button>
        <button onclick="openModal('cable')" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-blue-400 active:scale-90 transition"><i class="fas fa-tv"></i></div>
            <span class="text-[9px] font-bold opacity-50 uppercase">Cable</span>
        </button>
        <button onclick="switchTab('profile')" class="flex flex-col items-center gap-2">
            <div class="w-14 h-14 glass rounded-2xl flex items-center justify-center text-emerald-400 active:scale-90 transition"><i class="fas fa-cog"></i></div>
            <span class="text-[9px] font-bold opacity-50 uppercase">Setup</span>
        </button>
    </div>

    <div class="mt-10 px-4 animate-waterfall" style="animation-delay: 300ms">
        <h3 class="text-[10px] font-black opacity-30 uppercase tracking-widest mb-4 ml-2">Recent Activity</h3>
        <div id="transactionList" class="space-y-3">
            </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 bg-black/90 backdrop-blur-xl z-[400] hidden items-end sm:items-center justify-center opacity-0 transition-all duration-300">
        <div class="glass w-full max-w-md rounded-t-[3rem] sm:rounded-[3rem] p-10 transform translate-y-20 transition-transform">
            <h3 id="modalTitle" class="text-2xl font-black mb-8">Payment</h3>
            <form id="paymentForm" class="space-y-6">
                <input type="text" id="phone" placeholder="Recipient Number" class="w-full glass p-6 rounded-2xl outline-none focus:border-purple-500">
                <input type="text" id="amount" inputmode="numeric" placeholder="₦ 0" class="w-full glass p-6 rounded-2xl outline-none focus:border-purple-500 text-xl font-bold">
                <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 p-6 rounded-2xl font-black text-lg active:scale-95 transition">PROCEED</button>
            </form>
            <button onclick="closeModal()" class="w-full mt-4 p-4 text-xs font-bold opacity-30">CANCEL</button>
        </div>
    </div>

    <nav class="fixed bottom-6 left-4 right-4 z-[100]">
        <div class="glass max-w-md mx-auto rounded-[2.5rem] p-2 flex justify-between items-center">
            <button onclick="switchTab('home')" class="flex-1 flex flex-col items-center py-3 text-purple-400 bg-white/5 rounded-[2rem]"><i class="fas fa-home"></i></button>
            <button onclick="switchTab('history')" class="flex-1 flex flex-col items-center py-3 text-white/20"><i class="fas fa-receipt"></i></button>
            <button onclick="switchTab('profile')" class="flex-1 flex flex-col items-center py-3 text-white/20"><i class="fas fa-user-cog"></i></button>
        </div>
    </nav>

    <script>
        // --- 1. CONFIGURATION ---
        const supabaseUrl = 'YOUR_SUPABASE_URL';
        const supabaseKey = 'YOUR_ANON_KEY';
        // const supabase = supabase.createClient(supabaseUrl, supabaseKey);

        let balanceVal = "540,250.00";
        let isVisible = true;
        let currentPin = "";
        const correctPin = "1234";

        // --- 2. AUTHENTICATION LOGIC ---
        function pressPin(num) {
            if (currentPin.length < 4) {
                currentPin += num;
                updatePinUI();
                haptic(10);
                if (currentPin.length === 4) setTimeout(validatePin, 300);
            }
        }

        function updatePinUI() {
            const dots = document.querySelectorAll('.pin-dot');
            dots.forEach((dot, i) => i < currentPin.length ? dot.classList.add('bg-white', 'scale-125') : dot.classList.remove('bg-white', 'scale-125'));
        }

        function validatePin() {
            if (currentPin === correctPin) {
                document.getElementById('authScreen').classList.add('-translate-y-full', 'opacity-0');
                haptic(50);
                loadDashboard();
            } else {
                document.getElementById('pinContainer').classList.add('shake');
                haptic([50, 50, 50]);
                setTimeout(() => { currentPin = ""; updatePinUI(); document.getElementById('pinContainer').classList.remove('shake'); }, 400);
            }
        }

        function backspacePin() { currentPin = currentPin.slice(0, -1); updatePinUI(); }

        async function handleBioToggle(checkbox) {
            const available = await window.PublicKeyCredential.isUserVerifyingPlatformAuthenticatorAvailable();
            if (available) localStorage.setItem('useBio', checkbox.checked);
            else { checkbox.checked = false; alert("Device not supported"); }
        }

        // --- 3. UI DASHBOARD LOGIC ---
        function toggleBalance() {
            isVisible = !isVisible;
            document.getElementById('balanceText').innerText = isVisible ? balanceVal : "••••••";
            document.getElementById('balanceIcon').classList.toggle('fa-eye-slash');
        }

        function openModal(type) {
            document.getElementById('modalTitle').innerText = 'Pay ' + type;
            const m = document.getElementById('paymentModal');
            m.classList.remove('hidden'); m.classList.add('flex');
            setTimeout(() => { m.classList.add('opacity-100'); m.querySelector('div').classList.remove('translate-y-20'); }, 10);
        }

        function closeModal() {
            const m = document.getElementById('paymentModal');
            m.classList.remove('opacity-100'); m.querySelector('div').classList.add('translate-y-20');
            setTimeout(() => m.classList.add('hidden'), 300);
        }

        function loadDashboard() {
            const list = document.getElementById('transactionList');
            list.innerHTML = Array(3).fill('<div class="glass p-8 rounded-[2rem] skeleton"></div>').join('');
            setTimeout(() => {
                const data = [{t:'Data Bundle', a:'5,000', d:'Mar 08'}, {t:'Power', a:'12,500', d:'Mar 07'}];
                list.innerHTML = data.map((trx, i) => `
                    <div class="glass p-5 rounded-[2rem] flex items-center justify-between animate-waterfall" style="animation-delay: ${i*100}ms">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-white/5 flex items-center justify-center text-white/30"><i class="fas fa-bolt"></i></div>
                            <div><p class="font-bold text-sm">${trx.t}</p><p class="text-[9px] opacity-30 uppercase font-black">${trx.d}</p></div>
                        </div>
                        <p class="font-black text-emerald-400">-₦${trx.a}</p>
                    </div>
                `).join('');
            }, 1000);
        }

        function haptic(p) { if(navigator.vibrate) navigator.vibrate(p); }
    </script>
</body>
</html>
