<template>
    <div
        class="font-display bg-background-light dark:bg-navy-deep text-slate-800 dark:text-slate-100 min-h-screen flex items-center justify-center relative overflow-hidden">
        <!-- Ambient Background Elements -->
        <div class="absolute inset-0 bg-navy-deep z-0">
            <!-- Grid Pattern -->
            <div class="absolute inset-0 bg-grid-pattern opacity-[0.07] tech-grid pointer-events-none"></div>
            <!-- Abstract Glows -->
            <div
                class="absolute top-[-20%] left-[-10%] w-[50%] h-[50%] rounded-full bg-primary/10 blur-[120px] pointer-events-none">
            </div>
            <div
                class="absolute bottom-[-20%] right-[-10%] w-[40%] h-[40%] rounded-full bg-blue-600/10 blur-[100px] pointer-events-none">
            </div>
        </div>
        <!-- Scanline Effect -->
        <div class="scanline-overlay z-0"></div>
        <!-- Top Navigation / Brand (Absolute) -->
        <div class="absolute top-0 left-0 w-full p-6 z-20 flex justify-between items-center">
            <div class="flex items-center gap-2 group cursor-default">
                <div
                    class="w-8 h-8 rounded bg-primary/20 flex items-center justify-center border border-primary/40 group-hover:border-primary transition-colors duration-300">
                    <span class="material-icons text-primary text-sm transform -rotate-45">hub</span>
                </div>
                <span class="text-lg font-bold tracking-tight text-white">LICIT<span
                        class="text-primary">AI</span></span>
            </div>
            <div class="hidden md:flex items-center gap-2 text-xs font-mono text-slate-tech">
                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-navy-light border border-slate-700/50">
                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 anim-pulse-slow"></span>
                    SYSTEM: OPERATIONAL
                </span>
                <span class="px-3 py-1">V 2.4.0.1</span>
            </div>
        </div>
        <!-- Main Content Container -->
        <main
            class="relative z-10 w-full max-w-6xl mx-auto p-4 flex flex-col md:flex-row items-center justify-center min-h-[600px] gap-8">
            <!-- Left Side: Visual / Context (Desktop Only) -->
            <div class="hidden md:flex flex-col flex-1 max-w-lg space-y-8 pr-8 relative">
                <!-- Decorative line -->
                <div
                    class="absolute left-[-20px] top-0 bottom-0 w-[1px] bg-gradient-to-b from-transparent via-primary/20 to-transparent">
                </div>
                <div class="space-y-4">
                    <h1 class="text-4xl md:text-5xl font-bold leading-tight text-white">
                        Secure Enterprise <br />
                        <span
                            class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-blue-400">Intelligence
                            Gateway</span>
                    </h1>
                    <p class="text-slate-tech text-lg font-light max-w-md">
                        Access real-time legal analytics and predictive compliance modeling powered by LicitAI Neural
                        Core.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4 mt-8">
                    <div class="p-4 rounded-lg bg-navy-light/50 border border-white/5 backdrop-blur-sm">
                        <div class="text-primary mb-2 font-mono text-xs uppercase tracking-wider">Active Nodes</div>
                        <div class="text-2xl font-bold text-white">842 TB</div>
                        <div class="text-xs text-slate-500 mt-1">Processed Data</div>
                    </div>
                    <div class="p-4 rounded-lg bg-navy-light/50 border border-white/5 backdrop-blur-sm">
                        <div class="text-primary mb-2 font-mono text-xs uppercase tracking-wider">Security Level</div>
                        <div class="text-2xl font-bold text-white">Tier-1</div>
                        <div class="text-xs text-slate-500 mt-1">End-to-End Encrypted</div>
                    </div>
                </div>
                <!-- Abstract Map Visualization -->
                <div class="relative h-32 w-full rounded-lg overflow-hidden border border-white/5 mt-4 group">
                    <div
                        class="absolute inset-0 bg-navy-light/80 z-10 flex items-center justify-center group-hover:bg-navy-light/60 transition-colors">
                        <span class="font-mono text-xs text-primary flex items-center gap-2">
                            <span class="material-icons text-sm">public</span>
                            GLOBAL SERVER DISTRIBUTION
                        </span>
                    </div>
                    <img class="w-full h-full object-cover opacity-50 grayscale mix-blend-overlay"
                        data-alt="Abstract dark blue earth map with digital connections" data-location="Global Network"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDAaOnu8prgLCRFQhN1WdLg28fu4qGF0Igk-ZuBI9qPMGYN-wemgvyeq-oc7Zl21dxgnZd8sgrnjHvoqUp3YNQMnjhKzEr-XSjcnF03Ye0hqhlsFnhDgpZXNkoeFI8aTNGN_872bKLoUVwcl4r4LmsCyehoAmCyPZDdVEjrogvRmWNs3Wq9rV_CuhI6FCzuDTFyN2B_dWR3ePT8vybOsVbdu4jVG--hPD9uLBtm2EWUhH4TR3BMp3SwaMUkk7v3JQmfaba9WR_rMmg2" />
                </div>
            </div>
            <!-- Right Side: Login Card -->
            <div class="w-full max-w-md">
                <div class="glass-panel rounded-2xl p-8 md:p-10 relative overflow-hidden shadow-2xl">
                    <!-- Top Badge -->
                    <div class="flex justify-center mb-8">
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-primary/10 border border-primary/20 text-xs font-mono text-primary shadow-[0_0_10px_rgba(13,242,242,0.1)]">
                            <span class="relative flex h-2 w-2">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                            </span>
                            TENANT: GLOBAL CORP (US-EAST)
                        </div>
                    </div>
                    <slot />
                </div>
                <!-- Footer Links -->
                <div class="mt-6 text-center space-y-2">
                    <p class="text-xs text-slate-500">
                        Protected by reCAPTCHA and Subject to the LicitAI <a
                            class="text-slate-400 hover:text-primary transition-colors underline decoration-slate-600 underline-offset-2"
                            href="#">Privacy Protocol</a>.
                    </p>
                    <p class="text-[10px] text-slate-600 font-mono">
                        SESSION ID: 0x4F92A-392
                    </p>
                </div>
            </div>
        </main>
        <!-- Corner Decorative Elements -->
        <div class="fixed bottom-0 left-0 p-8 hidden xl:block z-10">
            <div class="border-l border-b border-primary/30 w-16 h-16 rounded-bl-3xl"></div>
        </div>
        <div class="fixed top-0 right-0 p-6 hidden xl:block z-10">
            <div class="border-r border-t border-primary/30 w-16 h-16 rounded-tr-3xl"></div>
        </div>
    </div>
</template>