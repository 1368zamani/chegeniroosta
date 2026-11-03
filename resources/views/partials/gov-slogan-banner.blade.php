{{-- ============================================
     بنر شعار سال با عکس رهبری
     ============================================ --}}

<style>
    .gov-slogan-banner {
        background: linear-gradient(135deg, #0d3b2e 0%, #14432f 100%);
        position: relative;
        overflow: hidden;
        padding: 40px 20px;
        border-bottom: 4px solid #ffd54f;
    }

    .gov-slogan-banner::before {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(ellipse at 80% 50%, rgba(255,213,79,0.15) 0%, transparent 60%);
        pointer-events: none;
    }

    .gov-slogan-inner {
        width: min(1400px, 96%);
        margin: auto;
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 40px;
        align-items: center;
        position: relative;
        z-index: 1;
    }

    .gov-leader-photo {
        position: relative;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 15px 45px rgba(0,0,0,0.4), 0 0 0 4px rgba(255,213,79,0.3);
        aspect-ratio: 4/3;
        background: #14432f;
    }

    .gov-leader-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.5s;
    }

    .gov-leader-photo:hover img { transform: scale(1.05); }

    .gov-leader-photo::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, transparent 60%, rgba(13,46,79,0.8) 100%);
        pointer-events: none;
    }

    .gov-slogan-content {
        color: white;
        text-align: right;
    }

    .gov-slogan-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255,213,79,0.2);
        border: 1px solid rgba(255,213,79,0.4);
        color: #ffd54f;
        padding: 8px 18px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 700;
        margin-bottom: 22px;
    }

    .gov-slogan-text {
        font-size: 30px;
        font-weight: 800;
        line-height: 1.6;
        margin: 0 0 20px;
        color: white;
        text-shadow: 0 2px 15px rgba(0,0,0,0.3);
    }

    .gov-slogan-text .highlight {
        color: #ffd54f;
        position: relative;
    }

    .gov-slogan-sub {
        font-size: 15px;
        color: rgba(255,255,255,0.85);
        line-height: 2;
        margin: 0 0 24px;
        max-width: 700px;
    }

    .gov-slogan-quote {
        display: flex;
        align-items: center;
        gap: 16px;
        background: rgba(0,0,0,0.2);
        backdrop-filter: blur(10px);
        padding: 16px 22px;
        border-radius: 12px;
        border-right: 4px solid #ffd54f;
    }

    .gov-slogan-quote-icon {
        font-size: 32px;
        color: #ffd54f;
        flex-shrink: 0;
    }

    .gov-slogan-quote p {
        margin: 0;
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        line-height: 1.9;
        font-style: italic;
    }

    @media (max-width: 900px) {
        .gov-slogan-inner {
            grid-template-columns: 1fr;
            gap: 24px;
        }
        .gov-leader-photo {
            max-width: 400px;
            margin: auto;
        }
        .gov-slogan-text { font-size: 22px; text-align: center; }
        .gov-slogan-content { text-align: center; }
        .gov-slogan-label { margin: auto auto 18px; }
        .gov-slogan-sub { text-align: center; }
    }
</style>

<section class="gov-slogan-banner">
    <div class="gov-slogan-inner">

        {{-- عکس رهبری --}}
        <div class="gov-leader-photo">
            <img src="{{ asset('images/gov/leader.jpg') }}" alt="رهبر معظم انقلاب اسلامی">
        </div>

        {{-- محتوای شعار --}}
        <div class="gov-slogan-content">

            <div class="gov-slogan-label">
                🇮🇷 شعار سال ۱۴۰۴
            </div>

            <h2 class="gov-slogan-text">
                «<span class="highlight">سال امنیت ملی و وحدت ملی</span>
                در سایه <span class="highlight">اقتصاد مقاومتی</span>»
            </h2>

            <p class="gov-slogan-sub">
                پایگاه اطلاع‌رسانی روستاهای شهرستان چگنی، در مسیر تحقق اهداف نظام مقدس جمهوری اسلامی ایران و خدمت‌رسانی صادقانه به مردم شریف استان لرستان
            </p>

            <div class="gov-slogan-quote">
                <div class="gov-slogan-quote-icon">❝</div>
                <p>
                    وحدت و انسجام ملی، ضامن پیشرفت و آبادانی ایران اسلامی است و همه ما در قبال آن مسئولیم.
                </p>
            </div>

        </div>

    </div>
</section>
