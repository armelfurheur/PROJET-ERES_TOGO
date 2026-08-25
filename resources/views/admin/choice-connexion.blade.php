@extends('layouts.app')

@section('title', 'Choix d’accès | ERES-TOGO')

@section('content')
<div class="access-page">

    {{-- Couche image de fond --}}
    <div class="access-bg" aria-hidden="true">
        <img src="{{ asset('img/camion.jpeg') }}" alt="">
    </div>
    {{-- Bande de sécurité (haut) --}}
    <div class="hazard-strip hazard-strip--top" aria-hidden="true"></div>

    <div class="access-shell">

        {{-- Statut système --}}
        <div class="status-bar">
            <span class="status-dot"></span>
            <span>Système opérationnel</span>
            <span class="status-sep">&middot;</span>
            <span>{{ now()->format('d/m/Y') }}</span>
        </div>

        <header class="access-header">
            <h1>Bonjour, <span class="accent">{{ auth()->user()->firstname }}</span></h1>
            <p>Sélectionnez votre poste d’accès pour continuer.</p>
        </header>

        {{-- Badges d'accès --}}
        <div class="badges-grid">

            <a href="{{ route('dashboard') }}" class="access-badge access-badge--dash">
                <div class="badge-top">
                    <span class="badge-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="3" y="4" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M3 9H21" stroke="currentColor" stroke-width="1.6"/>
                            <path d="M7 13H12" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <path d="M7 16.5H10" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                    </span>
                </div>
                <span class="badge-title">Tableau de bord</span>
                <span class="badge-desc">Gestion des anomalies &amp; rapport</span>
                <span class="badge-footline">
                    <span class="badge-bars" aria-hidden="true"></span>
                    Accès administrateur
                </span>
            </a>

            <a href="{{ route('formulaire.anomalie') }}" class="access-badge access-badge--alert">
                <div class="badge-top">
                    <span class="badge-icon">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2.5L22 20.5H2L12 2.5Z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/>
                            <path d="M12 9.5V13.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                            <circle cx="12" cy="16.8" r="0.95" fill="currentColor"/>
                        </svg>
                    </span>
                </div>
                <span class="badge-title">Formulaire de remontée</span>
                <span class="badge-desc">Signaler une anomalie constatée sur le terrain</span>
                <span class="badge-footline">
                    <span class="badge-bars" aria-hidden="true"></span>
                    Signalement rapide
                </span>
            </a>

        </div>

        <p class="access-footnote">ERES TOGO SA &middot; Plateforme de remontée d’anomalies</p>
    </div>

    {{-- Bande de sécurité (bas) --}}
    <div class="hazard-strip hazard-strip--bottom" aria-hidden="true"></div>
</div>

<style>
   .access-page{
    position:relative;
    min-height:100vh;
    width:100%;
    border-radius: 30px;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:3rem 1.25rem;
    background:linear-gradient(180deg,#0a2540 0%,#0d3159 55%,#0a2540 100%);
    overflow:hidden;
    font-family:'DM Sans', system-ui, sans-serif;
}

.access-bg::after{
    content:"";
    position:absolute;
    inset:0;
    background:
      radial-gradient(circle at 15% 10%, rgba(58, 100, 133, 0.07), transparent 100%),
      linear-gradient(180deg, rgba(10,37,64,0.90) 0%, rgba(13,49,89,0.85) 55%, rgba(10,37,64,0.94) 100%);
}
.access-bg{
    position:absolute;
    inset:0;
    z-index:0;
}
.access-bg img{
    width:100%;
    height:100%;
    object-fit:cover;
    object-position:center;
    opacity:.32;
}
.access-bg::after{
    content:"";
    position:absolute;
    inset:0;
   
}
    /* Bandes de sécurité type signalétique industrielle */
    .hazard-strip{
        position:absolute;
        left:0; right:0;
        height:10px;
        background:repeating-linear-gradient(
            135deg,
            #c9a227 0 18px,
            #0a2540 18px 36px
        );
        opacity:.90;
    }
    .hazard-strip--top{ top:0; }
    .hazard-strip--bottom{ bottom:0; }

    .access-shell{
        position:relative;
        z-index:2;
        width:100%;
        max-width:780px;
        display:flex;
        flex-direction:column;
        align-items:center;
        text-align:center;
        gap:2rem;
        animation:fadeInUp .7s ease both;
    }

    /* Barre de statut style console */
    .status-bar{
        display:inline-flex;
        align-items:center;
        gap:.5rem;
        font-family:'JetBrains Mono', 'DM Sans', monospace;
        font-size:.72rem;
        letter-spacing:.05em;
        color:rgba(255,255,255,0.65);
        background:rgba(255,255,255,0.06);
        border:1px solid rgba(255,255,255,0.12);
        padding:.4rem .9rem;
        border-radius:999px;
    }
    .status-dot{
        width:7px; height:7px;
        border-radius:50%;
        background:#3ddc84;
        box-shadow:0 0 0 0 rgba(61,220,132,.6);
        animation:pulse 2s infinite;
    }
    .status-sep{ opacity:.4; }

    .access-header h1{
        font-family:'Playfair Display', Georgia, serif;
        font-weight:700;
        font-size:clamp(1.7rem, 3.2vw, 2.3rem);
        color:#fff;
        margin:0 0 .5rem;
        letter-spacing:-0.01em;
    }
    .access-header h1 .accent{ color:#c9a227; }
    .access-header p{
        color:rgba(255,255,255,0.72);
        font-size:.98rem;
    }

    /* Badges d'accès */
    .badges-grid{
        width:100%;
        display:grid;
        grid-template-columns:1fr;
        gap:1.25rem;
    }
    @media (min-width:768px){
        .badges-grid{ grid-template-columns:1fr 1fr; gap:1.5rem; }
    }

    .access-badge{
        position:relative;
        display:flex;
        flex-direction:column;
        align-items:flex-start;
        text-align:left;
        gap:.55rem;
        padding:1.6rem 1.6rem 1.4rem;
        border-radius:.75rem;
        text-decoration:none;
        color:#fff; 
        background:rgba(255,255,255,0.045);
        border:1px solid rgba(255,255,255,0.14);
        box-shadow:0 14px 32px -14px rgba(0,0,0,0.55);
        transition:transform .3s ease, border-color .3s ease, background .3s ease;
    }
    .access-badge:hover{
        transform:translateY(-4px);
        background:rgba(255,255,255,0.075);
    }
    .access-badge--dash:hover{ border-color:rgba(61,220,132,0.55); }
    .access-badge--alert:hover{ border-color:rgba(201,162,39,0.55); }

    .badge-top{
        width:100%;
        display:flex;
        align-items:center;
        justify-content:space-between;
    }
    .badge-code{
        font-family:'JetBrains Mono', monospace;
        font-size:.68rem;
        letter-spacing:.1em;
        color:rgba(255,255,255,0.45);
    }
    .badge-icon{
        width:1.9rem; height:1.9rem;
        color:#c9a227;
    }
    .access-badge--dash .badge-icon{ color:#3ddc84; }
    .badge-icon svg{ width:100%; height:100%; }

    .badge-title{
        font-family:'Playfair Display', Georgia, serif;
        font-size:1.3rem;
        font-weight:700;
    }
    .badge-desc{
        font-size:.86rem;
        color:rgba(255,255,255,0.72);
        line-height:1.4;
    }

    .badge-footline{
        margin-top:.5rem;
        display:flex;
        align-items:center;
        gap:.5rem;
        font-family:'JetBrains Mono', monospace;
        font-size:.65rem;
        letter-spacing:.08em;
        text-transform:uppercase;
        color:rgba(255,255,255,0.4);
    }
    .badge-bars{
        display:inline-block;
        width:22px; height:12px;
        background:repeating-linear-gradient(
            90deg,
            rgba(255,255,255,0.5) 0 2px,
            transparent 2px 4px
        );
    }

    .access-footnote{
        color:rgba(255,255,255,0.38);
        font-size:.76rem;
        letter-spacing:.03em;
    }

    @keyframes fadeInUp{
        0%{ opacity:0; transform:translateY(22px); }
        100%{ opacity:1; transform:translateY(0); }
    }
    @keyframes pulse{
        0%{ box-shadow:0 0 0 0 rgba(61,220,132,.55); }
        70%{ box-shadow:0 0 0 7px rgba(61,220,132,0); }
        100%{ box-shadow:0 0 0 0 rgba(61,220,132,0); }
    }

    @media (prefers-reduced-motion: reduce){
        .access-shell{ animation:none; }
        .access-badge{ transition:none; }
        .status-dot{ animation:none; }
    }
</style>
@endsection
