<?php
$page_title = 'Vestibular Therapy Associates';
$active_page = 'home';
$extra_scripts = [
  "<script>
    $(document).ready(function(){ $('.collapsible').collapsible(); });
    (function(){
      var slides = document.querySelectorAll('.testimonial-slide');
      var dots = document.querySelectorAll('.testimonial-dots .dot');
      function show(idx) {
        slides.forEach(function(s){ s.classList.add('hide'); });
        dots.forEach(function(d){ d.classList.remove('active'); });
        if(slides[idx]) { slides[idx].classList.remove('hide'); }
        if(dots[idx]) { dots[idx].classList.add('active'); }
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

<section class="hero-section">
  <div class="container">
    <div class="row valign-wrapper" style="flex-wrap:wrap;">
      <div class="col s12 m6">
        <h1 class="hero-title"><u>Welcome to Vestibular Therapy Associates</u></h1>
        <p class="hero-text">Vestibular Therapy Associates (VTA) is a specialist clinical service providing expert assessment and rehabilitation for people with dizziness, vertigo, and balance disorders, working in partnership with healthcare, rehabilitation, insurance, and legal professionals.</p>
        <p class="hero-text">The service focuses on early identification and effective management of vestibular problems so that clients can return to safer, more confident, and productive lives.</p>
        <br>
        <a href="/VTA_NEW/About-Us/" class="btn btn-white waves-effect waves-light">Learn more</a>
      </div>
      <div class="col s12 m6 hero-image">
        <img src="gallery/Samy.jpg" alt="Vestibular Therapy Associates">
      </div>
    </div>
  </div>
</section>

<section class="section-gold" style="padding:28px 0;">
  <div class="container">
    <div class="row center" style="margin:0;">
      <div class="col s12 m4">
        <div class="stat-number">30-70%</div>
        <div class="stat-label">of TBI patients experience dizziness</div>
      </div>
      <div class="col s12 m4">
        <div class="stat-number">9+</div>
        <div class="stat-label">Specialist Vestibular Clinicians</div>
      </div>
      <div class="col s12 m4">
        <div class="stat-number">Nationwide</div>
        <div class="stat-label">Coverage across England</div>
      </div>
    </div>
  </div>
</section>

<section class="section-light">
  <div class="container">
    <div class="section-header">
      <h2>Our Mission &amp; Vision</h2>
      <div class="underline"></div>
    </div>
    <div class="row">
      <div class="col s12 m6">
        <div class="card-panel z-depth-1-half" style="border-radius:12px;height:100%;">
          <i class="material-icons" style="font-size:44px;color:var(--primary);">visibility</i>
          <h5 style="font-weight:700;margin-top:18px;font-size:20px;color:var(--text-dark);">Vision</h5>
          <p style="color:var(--text-muted);font-size:16px;line-height:1.8;">To make specialist vestibular rehabilitation accessible, consistent, and integrated across healthcare, rehabilitation, insurance, and legal pathways.</p>
        </div>
      </div>
      <div class="col s12 m6">
        <div class="card-panel z-depth-1-half" style="border-radius:12px;height:100%;">
          <i class="material-icons" style="font-size:44px;color:var(--primary);">track_changes</i>
          <h5 style="font-weight:700;margin-top:18px;font-size:20px;color:var(--text-dark);">Mission</h5>
          <p style="color:var(--text-muted);font-size:16px;line-height:1.8;">To ensure people receive timely and appropriate vestibular input so that dizziness and imbalance are no longer barriers to progress.</p>
        </div>
      </div>
    </div>
    <div class="center" style="margin-top:20px;">
      <a href="/VTA_NEW/About-Us/" class="btn waves-effect waves-light" style="background:var(--primary);color:#fff;border-radius:20px;font-weight:600;">Learn more</a>
    </div>
  </div>
</section>

<section class="section-white">
  <div class="container">
    <div class="section-header">
      <h2 style="color:var(--primary);font-weight:800;">Dizziness in TBI</h2>
      <div class="underline"></div>
    </div>

    <ul class="collapsible">
      <li>
        <div class="collapsible-header"><i class="material-icons">info_outline</i>Dizziness<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Dizziness is a non-specific term used to describe a range of sensations that can make a person feel unsteady, lightheaded, faint, or as if they or their surroundings are spinning or moving. It is a common symptom that can result from various underlying causes, including issues with the inner ear, neurological conditions, cardiovascular problems, medication side effects, dehydration, anxiety, and more.</p>
          <p><strong>There are different types of dizziness:</strong></p>
          <p><strong>Vertigo:</strong> The sensation that the person or their surroundings are spinning or moving, even when standing still. Often occurs due to problems with the inner ear or the vestibular system.</p>
          <ul class="browser-default" style="padding-left:20px;">
            <li><strong>Presyncope:</strong> Feeling faint or lightheaded without losing consciousness, often from a temporary decrease in blood flow to the brain.</li>
            <li><strong>Disequilibrium:</strong> A feeling of unsteadiness or imbalance, as if the person is going to fall.</li>
            <li><strong>Lightheadedness:</strong> Dizziness characterised by feelings of floating, disorientation, or difficulty concentrating.</li>
          </ul>
          <p>Dizziness can be accompanied by additional symptoms such as nausea, vomiting, sweating, ringing in the ears (tinnitus), headache, or visual disturbances.</p>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">bar_chart</i>How common is Dizziness in TBI?<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Dizziness is a common symptom following brain injuries and can persist for varying durations. The prevalence ranges widely depending on severity and type of injury.</p>
          <ul class="browser-default" style="padding-left:20px;">
            <li><strong>Traumatic Brain Injuries:</strong> Dizziness occurs in approximately <strong>30% to over 70%</strong> of individuals with TBIs.</li>
            <li><strong>Mild TBIs (Concussions):</strong> Dizziness occurs in <strong>30% to 90%</strong> of individuals who sustain concussions.</li>
            <li><strong>Moderate to Severe TBIs:</strong> Dizziness is prevalent across the entire spectrum of TBI severity.</li>
            <li><strong>Post-Concussion Syndrome:</strong> Dizziness can persist for weeks or months after the initial injury.</li>
            <li><strong>Other Brain Injuries:</strong> Dizziness can also occur in haemorrhages, strokes, or tumours depending on location and extent of damage.</li>
          </ul>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">warning</i>Impacts of Dizziness in TBI<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Dizziness can have profound consequences for TBI patients, affecting their physical, emotional, and social well-being.</p>
          <ul class="browser-default" style="padding-left:20px;">
            <li><strong>Increased Risk of Falls</strong> &mdash; impairs balance, heightening injury risk</li>
            <li><strong>Functional Impairment</strong> &mdash; disrupts daily activities, work, and recreation</li>
            <li><strong>Anxiety and Depression</strong> &mdash; chronic dizziness leads to social isolation</li>
            <li><strong>Difficulty Concentrating</strong> &mdash; impairs cognitive function and rehab progress</li>
            <li><strong>Increased Fatigue</strong> &mdash; physically and mentally exhausting</li>
            <li><strong>Decreased Independence</strong> &mdash; leads to reliance on others</li>
            <li><strong>Impact on Relationships</strong> &mdash; strains family and caregiver dynamics</li>
            <li><strong>Poor Engagement with Therapies</strong> &mdash; delays recovery</li>
            <li><strong>Work and Financial Challenges</strong> &mdash; hinders ability to work</li>
            <li><strong>Avoidance of Activities</strong> &mdash; results in social withdrawal</li>
          </ul>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">search</i>Diagnosis<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Diagnosing dizziness in individuals with TBIs involves a comprehensive evaluation by a specialist. The diagnosis is based on characteristic symptoms, history of head trauma, and clinical findings.</p>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">remove_red_eye</i>Visual Vertigo<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Visual vertigo, also known as visually induced dizziness, is characterized by symptoms of dizziness, vertigo, and imbalance that are triggered or worsened by specific visual stimuli or environments.</p>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">accessibility</i>Cervicogenic Dizziness<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Cervicogenic dizziness refers to dizziness that arises from dysfunction in the cervical spine/neck. It is characterized by symptoms of dizziness, unsteadiness, or imbalance related to cervical musculoskeletal disorders.</p>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">all_inclusive</i>Multi-Sensory Balance Dysfunction<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Balance control is a complex process that relies on the integration of sensory inputs from multiple systems: vestibular, visual, and somatosensory. TBI often disrupts this integration, leading to significant balance issues.</p>
        </div>
      </li>
      <li>
        <div class="collapsible-header"><i class="material-icons">report_problem</i>Barriers of VRT to patients with TBI<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body">
          <p>Many TBI patients do not routinely undergo comprehensive vestibular assessment due to a combination of cognitive, communicative, and sensory factors. A lack of awareness and a shortage of specialists can lead to gaps in effective treatment.</p>
        </div>
      </li>
    </ul>
  </div>
</section>

<section class="section-dark">
  <div class="container">
    <div class="section-header">
      <h2 style="color:var(--white);">Customised Vestibular Rehabilitation</h2>
      <div class="underline"></div>
    </div>
    <p style="color:var(--white); opacity:0.9; font-size:16px; margin-bottom:30px; text-align:center; max-width:800px; margin-left:auto; margin-right:auto;">Vestibular disorders are common sequelae of TBIs, affecting the inner ear and related brain structures responsible for balance control. Here are some specific vestibular disorders that we deal with in patients with TBIs.</p>
    
    <ul class="collapsible" style="border:none; box-shadow:none;">
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">cached</i>BPPV<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>Benign Paroxysmal Positional Vertigo (BPPV) is a prevalent vestibular disorder marked by brief episodes of vertigo triggered by specific head movements. It results from the displacement of tiny calcium carbonate crystals (otoconia) within the inner ear's semicircular canals.</p>
          <p><strong>Types of BPPV:</strong> Posterior Canalithiasis, Posterior Cupulolithiasis, Lateral Canalithiasis, Lateral Cupulolithiasis, Anterior Canalithiasis, and Apogeotropic Posterior Canalithiasis.</p>
          <p>Each type requires specific diagnostic and treatment strategies (such as repositioning maneuvers) to manage symptoms effectively.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">hearing</i>Post-Traumatic Meniere's Disease<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>A condition characterised by episodic vertigo, fluctuating hearing loss, tinnitus, and a feeling of fullness in the affected ear, typically resulting from abnormal fluid buildup in the inner ear following head trauma.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">blur_on</i>Labyrinthine Concussion<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>A type of inner ear injury caused by head trauma, leading to symptoms such as hearing loss, tinnitus, and vertigo due to damage to the delicate structures within the labyrinth.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">flash_on</i>Vestibular Migraine<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>A condition where migraine headaches are accompanied by vestibular symptoms such as vertigo, dizziness, and imbalance. It can be triggered or exacerbated by head injuries.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">waves</i>Vestibular Nerve Concussion<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>Direct trauma to the VIIIth cranial nerve can result in vestibular nerve concussion, causing acute vertigo and imbalance.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">bubble_chart</i>Perilymph Fistula<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>A tear or defect in the membranes separating the middle and inner ear, which can occur due to head trauma, leading to the leakage of inner ear fluid and symptoms of vertigo and hearing loss.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">loop</i>PPPD (Persistent Postural-Perceptual Dizziness)<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>A chronic vestibular disorder characterised by a persistent sensation of non-spinning dizziness and unsteadiness, which is often worsened by upright posture, head movements, and complex visual environments. TBIs can be a precipitating factor for PPPD.</p>
        </div>
      </li>
      <li style="margin-bottom:10px;">
        <div class="collapsible-header" style="border-radius:8px; border:none; background:rgba(255,255,255,0.1); color:#fff;"><i class="material-icons">device_hub</i>Central Vestibular Disorders<span class="secondary-content"><i class="material-icons">expand_more</i></span></div>
        <div class="collapsible-body" style="border:none; background:rgba(255,255,255,0.05); color:#fff; border-radius:0 0 8px 8px;">
          <p>TBIs can also damage central vestibular pathways in the brainstem and cerebellum, resulting in complex vestibular symptoms that often require specialised rehabilitation approaches.</p>
        </div>
      </li>
    </ul>
  </div>
</section>

<section class="section-light">
  <div class="container">
    <div class="section-header">
      <h2>Patient Testimonials</h2>
      <div class="underline"></div>
    </div>
    <div class="testimonial-card" id="testimonial-container">
      <div class="testimonial-slide" data-index="0">
        <p>"Samy was the first medical professional to believe me and listen to me about my very real symptoms of very poor balance, coordination and feeling unwell. He noticed abnormal eye movements called oscillopsia which I hadn't known about. I truly believe without seeing Samy first, I would still be undiagnosed. He is a lovely person, knowledgeable and I was very impressed by his curiosity to figure out the cause of my problems when many doctors previously had shown little interest, claimed it was 'all in my head'."</p>
      </div>
      <div class="testimonial-slide hide" data-index="1">
        <p>"Samy was highly recommended at a time I was extremely ill. Other medical professionals were unable to diagnose my illness and after hitting a very low point I saw Samy. Straight away he diagnosed Vestibular migraines. My road to recovery started then. My illness started September and now its February. My journey to recovery is slow and steady all with the help of Samy. I cant thank Samy enough for all his help and support."</p>
      </div>
      <div class="testimonial-slide hide" data-index="2">
        <p>"After suffering with balance, lack of concentration, motion sickness and tiredness for over 12 months and being told I had PPPD&hellip; I took matters into my own hands. Fortunately for me I found Sammy at the Dizzy Clinic. On my first visit he confirmed I was suffering from Vestibular Migraine and PPPD. I have had three consultations with Sammy. I'm confident in dealing with my symptoms and able to take back control of my life."</p>
      </div>
    </div>
    <div class="testimonial-dots">
      <span class="dot active" data-idx="0"></span>
      <span class="dot" data-idx="1"></span>
      <span class="dot" data-idx="2"></span>
    </div>
  </div>
</section>

<section class="section-white" style="padding: 60px 0;">
  <div class="container center">
    <h2 style="font-weight:800; color:var(--primary); margin-bottom:20px;">Need a Vestibular Specialist?</h2>
    <p style="font-size:18px; color:var(--text-muted); max-width:600px; margin:0 auto 30px;">Send us an email and we will get in touch with you as soon as possible to discuss how we can help with your vestibular rehabilitation needs.</p>
    <a href="/VTA_NEW/Contact-Us/" class="btn waves-effect waves-light" style="background:var(--primary); border-radius:25px; padding:0 30px; font-weight:600; height:45px; line-height:45px;">
      <i class="material-icons left">email</i>Contact Us Today
    </a>
  </div>
</section>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/footer.php'); ?>
