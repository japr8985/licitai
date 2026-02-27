<template>
    <div class="h-full glass-card rounded-xl p-6">
        <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-4">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary">groups</span>
                <h3 class="text-white font-bold text-lg uppercase tracking-tight">Recommended Squad /
                    Resource Allocation</h3>
            </div>
            <div class="text-[10px] font-mono text-slate-500 uppercase tracking-widest">Team ID:
                UNIT_402_ALPHA</div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div
                class="bg-primary/5 border-2 border-primary/50 rounded-xl p-4 flex flex-col gap-4 relative overflow-hidden glow-teal">
                <div class="absolute top-2 right-2">
                    <span class="bg-primary text-slate-900 text-[8px] font-black px-2 py-0.5 rounded uppercase">Top
                        Match</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-slate-800 border border-primary/30 p-0.5 overflow-hidden">
                        <img alt="Sarah Connor" class="w-full h-full object-cover grayscale brightness-125"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBuu94-xudL3K2n95rXR2zgCyCcHFk9PCEjSMPlr7vSBObprkaUx5KkJ5q8jdRTqCucp2kK09b2ANWsRU_WxCZwDvra_7cnwAs0y6BSeuI-r_UT2Jd8hrMRGVdVI_pGge8tFsYhOc9BZviAZUPbdtiv1q15iKaPp3MTU-gf8R5ZYHZJILqhTpy5s58En0_NuvwvrB6Aa0WTeWgXob_u6jg0naWay0yCOPncGw4MbhxxAh1TNttS97S-SbseD3tEVDSQikENuiKHOUm0" />
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Sarah Connor</p>
                        <p class="text-primary text-[10px] font-mono uppercase">Lead Architect</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span class="text-primary">98% Fit</span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-primary w-[98%]"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 font-mono italic">Matches: Kubernetes, ISO 27001,
                    Hybrid Cloud</p>
            </div>
            <div
                class="bg-slate-800/20 border border-white/10 rounded-xl p-4 flex flex-col gap-4 hover:border-white/20 transition-all">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-slate-800 border border-white/10 p-0.5 overflow-hidden">
                        <img alt="Marcus Wright" class="w-full h-full object-cover grayscale brightness-110"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDrM_9YaY9K7O3dd75q17GP0IYDvRJ4lse023kF0BmnDzmuDhNtKcViZR9asjhXO13SxZAh4DDLNISd8W4lOrR2PZtrcOcJ0yQ5ehaR7-FTkysm71mijMogH6LVTqQRluY2tFHEq6_bY2ZRYhbSoy_giM3lihrpG5UC8-Q3DEDBx3kzDfGDlQ4LW66pnuiDiWsFTH54PfBaWjdkokHNf3YEAHux0qLYd4r6O1deZfkY9Y-rcj-IU7zvUdjPg2GCnLzImDNTeJTeNTZ3" />
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Marcus Wright</p>
                        <p class="text-slate-400 text-[10px] font-mono uppercase">Cybersecurity Spec</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span class="text-slate-300">92% Fit</span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-400 w-[92%]"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 font-mono italic">Matches: Penetration Testing,
                    SOC2</p>
            </div>
            <div
                class="bg-slate-800/20 border border-white/10 rounded-xl p-4 flex flex-col gap-4 hover:border-white/20 transition-all">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-slate-800 border border-white/10 p-0.5 overflow-hidden">
                        <img alt="Grace Harper" class="w-full h-full object-cover grayscale brightness-110"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkhujM6UCHsafkQAEt_vHps8XAyAXdQ-mD0iTAjatvsLyfJT8KNckzpzS8Y1Mo4393ECcqTBsgKjnnEP1Ks2EFd3TZXP0G7Fqit7teOnXEdS5fPVMwOgjouEBE8JYjQZOTjKo1-lshXjYo99qos2Wna7rW7MI-PbIuEPJdEB7zn4lxVohCmYuYiO_hFTMkt7QCFhnZA0n5pCaBY3td5Fua1JJE1mMrlbLPMENa8rOzczADuISArqleFjIgegNaOkCW8XaG3xd46SRr" />
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Grace Harper</p>
                        <p class="text-slate-400 text-[10px] font-mono uppercase">DevOps Engineer</p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span class="text-slate-300">88% Fit</span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-400 w-[88%]"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 font-mono italic">Matches: Terraform, AWS Lambda
                </p>
            </div>
            <div
                class="bg-slate-800/20 border border-white/10 rounded-xl p-4 flex flex-col gap-4 hover:border-white/20 transition-all">
                <div class="flex items-center gap-3">
                    <div class="size-12 rounded bg-slate-800 border border-white/10 p-0.5 overflow-hidden">
                        <img alt="Kyle Reese" class="w-full h-full object-cover grayscale brightness-110"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuCwDro0CdEhSoZiQ4gb-_lmT_wsVAsBJAAQHttcf5P9wS1aR1NuneFbgO4KYvyqYbdgf2vq8cnvEJ3KI_C20OJi_oUUDoYUbpmsPCXGEizl_rthYGxkJphXXxxMkdXwFxVajqKkKQ-cf0shbz1QCIMjSHU1-JiN63sE4MXnvTd68vgYQ9vSVYkWTZzog7D2nJ0j7gEYOwlM98RmqFOiAxNEPJg80-ahiYWFkcSaeoiJ_zZmZoFOc7CESUcAHpoMv697DMgCKegzE7aL" />
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Kyle Reese</p>
                        <p class="text-slate-400 text-[10px] font-mono uppercase">Legal &amp; Compliance
                        </p>
                    </div>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between text-[10px] font-mono">
                        <span class="text-slate-400 uppercase">Match Score</span>
                        <span class="text-slate-300">85% Fit</span>
                    </div>
                    <div class="h-1 bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-slate-400 w-[85%]"></div>
                    </div>
                </div>
                <p class="text-[10px] text-slate-500 font-mono italic">Matches: Public Sector Tenders
                </p>
            </div>
        </div>
    </div>
</template>
<script setup lang="ts">
</script>