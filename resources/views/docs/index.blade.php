@extends('layouts.docs')

@section('title', 'Introduction')

@section('toc')
    <a href="#getting-started" class="block hover:text-supabase-accent transition-colors">Getting Started</a>
    <a href="#architecture" class="block hover:text-supabase-accent transition-colors">How it works</a>
    <a href="#android-setup" class="block hover:text-supabase-accent transition-colors">Android Setup</a>
    <a href="#authentication" class="block hover:text-supabase-accent transition-colors">Authentication</a>
    <a href="#create-invoice" class="block hover:text-supabase-accent transition-colors">Creating Invoices</a>
    <a href="#receiving-webhook" class="block hover:text-supabase-accent transition-colors">Webhooks</a>
@endsection

@section('content')
<div class="prose prose-invert max-w-none">
    <section id="getting-started" class="mb-16">
        <h1 class="text-5xl font-extrabold text-white mb-6">Getting <span class="text-supabase-accent">Started</span></h1>
        <p class="text-xl text-slate-400 mb-8 leading-relaxed">
            Welcome to Cekbayar. Cekbayar is a high-performance payment notification bridge that connects your Android financial apps directly to your web application.
        </p>
        
        <div class="bg-supabase-surface/50 border border-supabase-border rounded-2xl p-8 mb-8">
            <h3 class="text-white font-bold mb-6 flex items-center">
                <span class="w-2 h-2 bg-supabase-accent rounded-full mr-3"></span>
                Quick Integration Overview
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-xl bg-supabase-accent text-supabase-dark flex items-center justify-center font-bold shadow-lg shadow-supabase-accent/20">1</div>
                    <p class="text-sm font-medium text-slate-200">Register account</p>
                </div>
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-xl bg-supabase-accent text-supabase-dark flex items-center justify-center font-bold shadow-lg shadow-supabase-accent/20">2</div>
                    <p class="text-sm font-medium text-slate-200">Pair Android device</p>
                </div>
                <div class="space-y-4">
                    <div class="w-10 h-10 rounded-xl bg-supabase-accent text-supabase-dark flex items-center justify-center font-bold shadow-lg shadow-supabase-accent/20">3</div>
                    <p class="text-sm font-medium text-slate-200">Set Webhook URL</p>
                </div>
            </div>
        </div>
    </section>

    <section id="architecture" class="mb-16 pt-16 border-t border-supabase-border">
        <h2 class="text-3xl font-bold text-white mb-6">How <span class="text-supabase-accent">Cekbayar</span> Works</h2>
        <p class="mb-8 text-lg">
            Unlike traditional gateways, Cekbayar doesn't hold your funds. It acts as a real-time listener for your payment notifications.
        </p>
        <div class="bg-supabase-input border border-supabase-border rounded-2xl p-10 text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-supabase-accent/5 blur-3xl rounded-full"></div>
            <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                <div class="flex-1 w-full">
                    <div class="bg-supabase-surface p-5 rounded-xl border border-supabase-border mb-3 font-mono text-xs shadow-xl">Financial Apps<br><span class="text-supabase-muted">(BCA, Dana, etc)</span></div>
                    <p class="text-xs text-supabase-muted font-bold uppercase tracking-wider">Triggers Notification</p>
                </div>
                <div class="text-supabase-accent animate-pulse">
                    <svg class="w-6 h-6 rotate-90 md:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
                <div class="flex-1 w-full">
                    <div class="bg-supabase-accent text-supabase-dark p-5 rounded-xl font-bold mb-3 text-xs shadow-xl shadow-supabase-accent/10">Cekbayar Android App</div>
                    <p class="text-xs text-supabase-muted font-bold uppercase tracking-wider">Captures & Forwards</p>
                </div>
                <div class="text-supabase-accent animate-pulse">
                    <svg class="w-6 h-6 rotate-90 md:rotate-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </div>
                <div class="flex-1 w-full">
                    <div class="bg-supabase-surface p-5 rounded-xl border border-supabase-border mb-3 font-mono text-xs shadow-xl">Your Webhook</div>
                    <p class="text-xs text-supabase-muted font-bold uppercase tracking-wider">Confirm Order</p>
                </div>
            </div>
        </div>
    </section>

    <section id="android-setup" class="mb-16 pt-16 border-t border-supabase-border">
        <h2 class="text-3xl font-bold text-white mb-6">Android App Setup</h2>
        <p class="mb-6 text-lg">To start receiving notifications, follow these steps on your Android device:</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="p-6 bg-supabase-surface rounded-xl border border-supabase-border">
                <h4 class="text-white font-bold mb-2">1. Installation</h4>
                <p class="text-sm text-slate-400">Download the Cekbayar Android APK from your dashboard and install it.</p>
            </div>
            <div class="p-6 bg-supabase-surface rounded-xl border border-supabase-border">
                <h4 class="text-white font-bold mb-2">2. Permissions</h4>
                <p class="text-sm text-slate-400">Enable <strong>Notification Access</strong> permission in your Android settings.</p>
            </div>
            <div class="p-6 bg-supabase-surface rounded-xl border border-supabase-border">
                <h4 class="text-white font-bold mb-2">3. Authentication</h4>
                <p class="text-sm text-slate-400">Log in to the app using your Merchant API Key (Production/Sandbox).</p>
            </div>
            <div class="p-6 bg-supabase-surface rounded-xl border border-supabase-border">
                <h4 class="text-white font-bold mb-2">4. Monitoring</h4>
                <p class="text-sm text-slate-400">Ensure financial apps have banners enabled for all notifications.</p>
            </div>
        </div>
    </section>

    <section id="authentication" class="mb-16 pt-16 border-t border-supabase-border">
        <h2 class="text-3xl font-bold text-white mb-6">Authentication</h2>
        <p class="mb-6 text-lg">Cekbayar uses Bearer Tokens to authenticate requests. You can find your API keys in the dashboard settings.</p>
        <div class="bg-supabase-input rounded-xl border border-supabase-border p-6 mb-6 font-mono relative group">
            <div class="absolute right-4 top-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <button class="text-supabase-accent hover:text-white"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg></button>
            </div>
            <span class="text-supabase-accent">Authorization:</span> <span class="text-white">Bearer YOUR_API_KEY</span>
        </div>
        <div class="bg-amber-400/5 border border-amber-400/20 rounded-xl p-6">
            <div class="flex items-start space-x-3">
                <svg class="w-5 h-5 text-supabase-accent mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <p class="text-sm text-supabase-accent/80">
                    Use your <strong>Sandbox Key</strong> for testing and <strong>Production Key</strong> for live transactions.
                </p>
            </div>
        </div>
    </section>

    <section id="create-invoice" class="mb-16 pt-16 border-t border-supabase-border">
        <h2 class="text-3xl font-bold text-white mb-6">Creating Invoices</h2>
        <p class="mb-8 text-lg">Send a POST request to create a new payment invoice.</p>
        
        <div class="bg-[#0f0f0f] rounded-2xl border border-supabase-border overflow-hidden mb-8 shadow-2xl">
            <div class="flex items-center justify-between px-6 py-3 bg-supabase-surface border-b border-supabase-border">
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-0.5 bg-supabase-accent text-supabase-dark text-[10px] font-bold rounded">POST</span>
                    <span class="text-xs font-mono text-white">/api/v1/invoices</span>
                </div>
                <span class="text-[10px] text-supabase-muted font-mono uppercase">cURL</span>
            </div>
            <pre class="p-6 text-xs font-mono text-slate-300 overflow-x-auto leading-relaxed">curl -X POST https://Cekbayar.yourdomain.com/api/v1/invoices \
  -H "<span class="text-supabase-accent">Authorization:</span> Bearer YOUR_API_KEY" \
  -H "<span class="text-supabase-accent">Content-Type:</span> application/json" \
  -d '{
    "amount": 50000,
    "customer_name": "Budi Santoso",
    "external_id": "ORDER-12345",
    "channel_type": "qris"
  }'</pre>
        </div>

        <h4 class="text-white font-bold mb-4 flex items-center">
            <span class="w-1 h-1 bg-supabase-accent mr-3"></span>
            Response Object
        </h4>
        <div class="bg-[#0f0f0f] rounded-2xl border border-supabase-border p-6 mb-8 shadow-2xl">
<pre class="text-xs text-slate-300 leading-relaxed">{
  "success": <span class="text-supabase-accent">true</span>,
  "data": {
    "invoice_number": "INV-20260516-ABCDEF",
    "pay_amount": <span class="text-supabase-accent">50123</span>,
    "qris_string": "00020101021226650016...",
    "expires_at": "2026-05-16T10:30:00Z"
  }
}</pre>
        </div>
        <p class="text-sm text-slate-400">
            <strong class="text-white">Pro Tip:</strong> Always use the <code class="text-supabase-accent bg-supabase-accent/10">pay_amount</code> (unique amount) to ensure automatic matching in our system.
        </p>
    </section>

    <footer class="mt-32 pt-12 border-t border-supabase-border text-center">
        <p class="text-supabase-muted mb-4">Need help or have questions?</p>
        <div class="flex items-center justify-center space-x-6">
            <a href="mailto:support@Cekbayar.local" class="text-sm text-white hover:text-supabase-accent transition-colors">support@Cekbayar.local</a>
            <a href="#" class="text-sm text-white hover:text-supabase-accent transition-colors">API Status</a>
        </div>
    </footer>
</div>
@endsection
