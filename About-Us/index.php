<?php
$page_title = 'About Us - Vestibular Therapy Associates';
$page_description = 'Learn about Vestibular Therapy Associates - specialist vestibular rehabilitation services.';
$active_page = 'about';
$extra_scripts = [
  "<script>
    (function(){
      var slides = document.querySelectorAll('#testimonial-container .testimonial-slide');
      var dots = document.querySelectorAll('.testimonial-dots .dot');
      function show(idx) {
        slides.forEach(function(s){ s.classList.add('hide'); });
        dots.forEach(function(d){ d.classList.remove('active'); });
        if(slides[idx]) slides[idx].classList.remove('hide');
        if(dots[idx]) dots[idx].classList.add('active');
      }
      dots.forEach(function(d){
        d.addEventListener('click', function(){ show(parseInt(this.dataset.idx)); });
      });
      var current = 0;
      setInterval(function(){
        current = (current + 1) % slides.length;
        show(current);
      }, 6000);
    })();
  </script>"
];
require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/header.php');
?>

<section class="page-header">
  <div class="container">
    <h2>About Us</h2>
    <div class="underline"></div>
  </div>
</section>

<section class="section-white" style="padding-top:50px;">
  <div class="container">
    <div class="row content-row valign-wrapper" style="flex-wrap:wrap;">
      <div class="col s12 m6">
        <div class="content-image">
          <img src="/VTA_NEW/gallery_gen/80b28a2c79a51ecd149a66cbae544bd8_600x298_fit.jpg" alt="Overview">
        </div>
      </div>
      <div class="col s12 m6">
        <div class="content-text">
          <h3><u>Overview</u></h3>
          <p>VTA supports people experiencing dizziness, vertigo, unsteadiness, motion sensitivity, visual disturbance, or falls, including symptoms following illness, trauma, or brain injury. The service is particularly suited to clients whose vestibular problems are under-recognised in routine care and are limiting day-to-day function, rehabilitation progress, or return to work.</p>
          <p>VTA also works extensively with insurance providers, case managers, solicitors, and vocational rehabilitation/RTW specialists when vestibular symptoms influence recovery, work capacity, and decisions about rehabilitation input.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-light">
  <div class="container">
    <div class="row">
      <div class="col s12 m6">
        <div class="card-panel z-depth-1-half" style="border-radius:12px;">
          <i class="material-icons" style="font-size:36px;color:var(--primary);">assignment</i>
          <h5 style="font-weight:700;margin:15px 0;font-size:18px;">What We Do</h5>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">Comprehensive vestibular and balance assessment for adults and children, combining detailed clinical history, oculomotor and vestibular testing, and functional balance and gait evaluation. Evidence-based vestibular rehabilitation programmes improve gaze stability, balance, mobility, and confidence.</p>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">We manage BPPV, vestibular neuritis, labyrinthitis, unilateral/bilateral vestibular hypofunction, vestibular migraine, PPPD, and ABI/TBI-related dizziness.</p>
        </div>
      </div>
      <div class="col s12 m6">
        <div class="card-panel z-depth-1-half" style="border-radius:12px;">
          <i class="material-icons" style="font-size:36px;color:var(--primary);">sync_alt</i>
          <h5 style="font-weight:700;margin:15px 0;font-size:18px;">How We Work</h5>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">Flexible, accessible care through clinic-based, virtual, and home-based pathways. Structured, progressive, and goal-driven rehabilitation with regular review and adjustment to match each client's tolerance and stage of recovery.</p>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">Care is delivered using a patient-centred, goal-oriented model emphasising clear explanations, collaborative decision-making, and supported self-management.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-white">
  <div class="container">
    <div class="section-header">
      <h2>Service Delivery</h2>
      <div class="underline"></div>
    </div>
    <div class="row">
      <div class="col s12 m4">
        <div class="card z-depth-1-half" style="border-radius:12px;">
          <div class="card-image">
            <img src="/VTA_NEW/gallery_gen/d414772425cfb657c99baae4004f4ae5_478x318_fit.jpg" alt="Home Visits">
          </div>
          <div class="card-content">
            <span class="card-title" style="font-weight:700;color:var(--primary);font-size:20px;">Home &amp; Community Visits</span>
            <p style="color:#636e72;font-size:14px;line-height:1.7;">Minimises symptom-provoking travel. Associates reach most parts of England within 2-3 hours. Therapy progresses to outdoor, community, and fitness-focused rehab as tolerance improves.</p>
          </div>
        </div>
      </div>
      <div class="col s12 m4">
        <div class="card z-depth-1-half" style="border-radius:12px;">
          <div class="card-image">
            <img src="/VTA_NEW/gallery_gen/f108e8c63d5b7c6f2203435afe5b967c_480x480_fit.png" alt="Virtual Clinics">
          </div>
          <div class="card-content">
            <span class="card-title" style="font-weight:700;color:var(--primary);font-size:20px;">Virtual Clinics</span>
            <p style="color:#636e72;font-size:14px;line-height:1.7;">Remote consultations and therapy across the UK and internationally. Reduces travel burden, improves accessibility, and maintains continuity for clients unable to attend frequent in-person sessions.</p>
          </div>
        </div>
      </div>
      <div class="col s12 m4">
        <div class="card z-depth-1-half" style="border-radius:12px;">
          <div class="card-image">
            <img src="/VTA_NEW/gallery_gen/b5da9d7bc4180b8d4572be59ce195ed2_496x330_fit.jpg" alt="Treatment Approach">
          </div>
          <div class="card-content">
            <span class="card-title" style="font-weight:700;color:var(--primary);font-size:20px;">Treatment Approach</span>
            <p style="color:#636e72;font-size:14px;line-height:1.7;">Patient-centred, goal-oriented model. Education on symptom mechanisms, pacing, and graded exposure. Progress reviewed regularly with exercises adjusted toward higher-level balance and work demands.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-light">
  <div class="container">
    <div class="row">
      <div class="col s12 m6">
        <div class="card-panel" style="border-radius:12px;border-left:4px solid var(--primary);">
          <i class="material-icons" style="font-size:36px;color:var(--primary);">search</i>
          <h5 style="font-weight:700;margin:15px 0;font-size:18px;">Vestibular Screening Service</h5>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">A structured screening service to help funders decide when full assessment is required. Focused review of symptoms, risk factors, and functional impact with a concise report supporting go/no-go decisions. Screening fee offset against subsequent full assessment.</p>
        </div>
      </div>
      <div class="col s12 m6">
        <div class="card-panel" style="border-radius:12px;border-left:4px solid var(--primary);">
          <i class="material-icons" style="font-size:36px;color:var(--primary);">handshake</i>
          <h5 style="font-weight:700;margin:15px 0;font-size:18px;">Partnerships</h5>
          <p style="font-size:15px;line-height:1.8;color:#636e72;">VTA works collaboratively with case managers, insurers, medico-legal teams, and clinical referrers. Clear communication and structured reporting support timely, informed decisions about rehabilitation input and funding.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-white">
  <div class="container">
    <div class="section-header">
      <h2>What Our Patients Say</h2>
      <div class="underline"></div>
    </div>
    <div class="testimonial-card" id="testimonial-container">
      <div class="testimonial-slide" data-index="0">
        <p>"Samy was the first medical professional to believe me and listen to me about my very real symptoms of very poor balance, coordination and feeling unwell. He noticed abnormal eye movements called oscillopsia which I hadn't known about. I truly believe without seeing Samy first, I would still be undiagnosed."</p>
      </div>
      <div class="testimonial-slide hide" data-index="1">
        <p>"Samy was highly recommended at a time I was extremely ill. Other medical professionals were unable to diagnose my illness and after hitting a very low point I saw Samy. Straight away he diagnosed Vestibular migraines. My road to recovery started then."</p>
      </div>
      <div class="testimonial-slide hide" data-index="2">
        <p>"After suffering with balance, lack of concentration, motion sickness and tiredness for over 12 months&hellip; I found Sammy at the Dizzy Clinic. I'm now able to identify my migraine triggers and have worked through several exercises to strengthen my brain/body communication signals."</p>
      </div>
    </div>
    <div class="testimonial-dots">
      <span class="dot active" data-idx="0"></span>
      <span class="dot" data-idx="1"></span>
      <span class="dot" data-idx="2"></span>
    </div>
  </div>
</section>

<section class="section-light" style="padding:0;">
  <div class="map-container" style="border-radius:0;">
    <iframe src="https://www.google.com/maps?q=Oaktree+House,+Oaktree+Rise,+Codsall,+Wolverhampton+WV8+1DP,+UK&amp;output=embed" loading="lazy" allowfullscreen></iframe>
  </div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/footer.php'); ?>
