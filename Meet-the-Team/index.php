<?php
$page_title = 'Meet the Team - Vestibular Therapy Associates';
$page_description = 'Meet our specialist vestibular physiotherapy team.';
$active_page = 'team';
$extra_head = '<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">';
$extra_scripts = [
  '<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>',
  "<script>
    $(document).ready(function(){ $('.sidenav').sidenav(); });
    var map = L.map('map').setView([54.5,-2.5],6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:18}).addTo(map);
    var areas=[
      {name:'London',coords:[51.5074,-0.1278]},{name:'Greater London',coords:[51.5074,-0.1278]},{name:'East England',coords:[52.2,0.12]},{name:'Cambridgeshire',coords:[52.2053,0.1218]},{name:'Midlands',coords:[52.4862,-1.8904]},{name:'North East England',coords:[54.9783,-1.6178]},{name:'Newcastle',coords:[54.9783,-1.6178]},{name:'North West England',coords:[53.4808,-2.2426]},{name:'Liverpool',coords:[53.4084,-2.9916]},{name:'Manchester',coords:[53.4808,-2.2426]},{name:'South West England',coords:[50.8,-3.6]},{name:'Dorset',coords:[50.7488,-2.3445]},{name:'Yorkshire',coords:[53.8,-1.55]},{name:'Leeds',coords:[53.8008,-1.5491]},{name:'Sheffield',coords:[53.3811,-1.4701]}];
    areas.forEach(function(a){L.marker(a.coords).addTo(map).bindPopup('<b>'+a.name+'</b>');});
    var s={color:'#1d4ed8',fillColor:'#60a5fa',fillOpacity:0.2,weight:2};
    L.circle([51.5074,-0.1278],{radius:35000,...s}).addTo(map);
    L.circle([52.2,0.12],{radius:45000,...s}).addTo(map);
    L.circle([52.4862,-1.8904],{radius:90000,...s}).addTo(map);
    L.circle([54.9783,-1.6178],{radius:70000,...s}).addTo(map);
    L.circle([53.4808,-2.2426],{radius:85000,...s}).addTo(map);
    L.circle([50.8,-3.6],{radius:90000,...s}).addTo(map);
    L.circle([50.7488,-2.3445],{radius:45000,...s}).addTo(map);
    L.circle([53.8,-1.55],{radius:80000,...s}).addTo(map);
    var legend=L.control({position:'bottomright'});
    legend.onAdd=function(){var div=L.DomUtil.create('div','legend');div.innerHTML='<strong>Service Areas</strong><br>Blue = coverage<br>Pins = locations';return div;};
    legend.addTo(map);
  </script>"
];
require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/header.php');
?>

<section class="page-header">
  <div class="container">
    <h2>Vestibular &amp; Rehab Specialists</h2>
    <div class="underline"></div>
    <p>Comprehensive and customised treatments for dizziness and balance issues in TBI patients</p>
  </div>
</section>

<section class="section-white" style="padding-top:50px;">
  <div class="container">
    <div class="row" style="display:flex;flex-wrap:wrap;">
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/aed608cb53feacb6d824846bbaafa66d_258x268_0x0_258x288_crop.jpg" alt="Samy Selvanayagam">
          <span class="card-title">Samy Selvanayagam</span>
          <p class="role">MSc PT, Cert.VRT (USA), MCSP<br>Managing Director<br>Consultant - Vestibular Physiotherapist</p>
          <div class="region-badge">Nationwide</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/d27804cf4e95deb663ece2e1b25fefd8_241x258_0x0_241x296_crop.jpg" alt="Kate Bryce">
          <span class="card-title">Kate Bryce</span>
          <p class="role">BSc PT, MCSP, MACPIVR<br>HCPC Registered Physiotherapist<br>Specialist Vestibular Physiotherapist | Falls &amp; Balance Rehabilitation</p>
          <div class="region-badge">North East England</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/1f9d51213fb7bedc754b2aa0a9714841_240x256_0x64_240x320_crop.jpg" alt="Anna Bennett">
          <span class="card-title">Anna Bennett</span>
          <p class="role">BSc PT, MCSP<br>HCPC Registered PH81920<br>Advanced Vestibular Physiotherapist<br>Associate</p>
          <div class="region-badge">Yorkshire</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/99a25fa4005d74ff5f1b174292558e4a_240x253_0x26_240x320_crop.jpg" alt="Lewis Brennan">
          <span class="card-title">Lewis Brennan</span>
          <p class="role">HCPC Registered Physiotherapist<br>Specialist Vestibular Physiotherapist | Musculoskeletal &amp; Vestibular Rehabilitation</p>
          <div class="region-badge">London &amp; Cambridgeshire</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/8cabef1b804e4a7df054bbec8c67c16b_fit.jpg" alt="Georgios Tsiknas">
          <span class="card-title">Georgios Tsiknas</span>
          <p class="role">MSc, MACP, IFOMPT, MACPIVR<br>HCPC Registered Physiotherapist<br>Specialist Vestibular Physiotherapist</p>
          <div class="region-badge">West Midlands</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/aadf7acd0194ef66e46a400e690ddd24_fit.jpg" alt="Ileana Dascalu">
          <span class="card-title">Ileana Dascalu</span>
          <p class="role">Specialist Vestibular Physiotherapist | Paediatric &amp; Adult Rehabilitation<br>HCPC Registered Physiotherapist</p>
          <div class="region-badge">London</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/0b6676db2b73940e294dc022cd3d7eb5_fit.jpg" alt="Nick Hill">
          <span class="card-title">Nick Hill</span>
          <p class="role">BSc (Hons) MRes MCSP MACPIVR MAACP, HCPC<br>Registered Physiotherapist<br>Specialist Vestibular Physiotherapist<br>Clinical Researcher &amp; Research-Informed Practitioner</p>
          <div class="region-badge">North West England</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/0c0722c84b76b32dd8360b206f2f41d8_438x470_fit.jpg" alt="Sultana Parvin">
          <span class="card-title">Sultana Parvin</span>
          <p class="role">HCPC, PH93874<br>Specialist Vesibular Physiotherapist</p>
          <div class="region-badge">Manchester</div>
        </div>
      </div>
      <div class="col s12 m4 team-card">
        <div class="card z-depth-1-half">
          <img src="/VTA_NEW/gallery_gen/65bdc8216e946472028999726be03a56_183x190_0x0_183x245_crop.jpg" alt="Sabash Palanisamy">
          <span class="card-title">Sabash Palanisamy</span>
          <p class="role">HCPC, PH127229<br>Specialist Vesibular Physiotherapist</p>
          <div class="region-badge">Dorset</div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="section-light">
  <div class="container">
    <div class="section-header">
      <h2>Areas We Cover</h2>
      <div class="underline"></div>
    </div>
    <div class="row flex-row" style="margin-top: 30px;">
      
      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">location_city</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">London</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">London (Greater London)</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">map</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">East England</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Cambridgeshire</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">explore</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">Midlands</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Birmingham, Coventry, Leicester, Nottingham</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">location_on</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">North East</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Newcastle upon Tyne</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">place</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">North West</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Liverpool &amp; Manchester</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">terrain</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">South West</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Dorset</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px;">
          <i class="material-icons" style="color:var(--primary); font-size:32px; margin-bottom:10px;">navigation</i>
          <h5 style="color:var(--primary); font-weight:700; font-size:18px; margin-top:0;">Yorkshire</h5>
          <p style="color:var(--text-muted); margin-bottom:0; font-size:14px;">Leeds &amp; Sheffield</p>
        </div>
      </div>

      <div class="col s12 m6 l3" style="display: flex; margin-bottom: 20px;">
        <div class="card-panel z-depth-1-half" style="border-radius:12px; width:100%; text-align:center; padding: 25px 15px; background: var(--primary-light);">
          <i class="material-icons" style="color:var(--primary-dark); font-size:32px; margin-bottom:10px;">public</i>
          <h5 style="color:var(--primary-dark); font-weight:700; font-size:18px; margin-top:0;">Nationwide</h5>
          <p style="color:var(--primary-dark); margin-bottom:0; font-size:14px; font-weight: 500;">Coverage across the UK</p>
        </div>
      </div>

    </div>
  </div>
</section>

<div id="map" style="height:480px;width:100%;"></div>

<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/VTA_NEW/includes/footer.php'); ?>
