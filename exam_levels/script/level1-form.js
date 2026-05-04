// Language switching setup
const buttons = document.querySelectorAll('.lang-btn');
const forms = document.querySelectorAll('.lang-form');

buttons.forEach(button => {
  button.addEventListener('click', () => {
    // Toggle button highlight
    buttons.forEach(btn => btn.classList.remove('active'));
    button.classList.add('active');

    const selectedLang = button.dataset.lang;
    forms.forEach(form => {
      form.classList.toggle('active', form.classList.contains('lang-' + selectedLang));
    });
  });
});

// Township lists with localization support for Myanmar and English
const townships = {
  mandalay: [
    { en: "Amarapura", mm: "အမရပူရ" },
    { en: "Aungmyaythazan", mm: "အောင်မြေဦး" },
    { en: "Chanayethazan", mm: "ချမ်းအေးသာဇံ" },
    { en: "Chanmyathazi", mm: "ချမ်းမြသာစည်" },
    { en: "Maha Aungmye", mm: "မဟာအောင်မြေ" },
    { en: "Patheingyi", mm: "ပုသိမ်ကြီး" },
    { en: "Pyigyidagun", mm: "ပြည်ကြီးတံခွန်" },
    { en: "Madaya", mm: "မတ္တရာ" },
    { en: "Singu", mm: "စဉ့်ကူ" },
    { en: "Thabeikkyin", mm: "သပိတ်ကျင်း" }
  ],
  yangon: [
    { en: "Hlaing", mm: "လှိုင်" },
    { en: "Kamayut", mm: "ကမာရွတ်" },
    { en: "Insein", mm: "အင်းစိန်" },
    { en: "Kyauktada", mm: "ကျောက်တံတား" },
    { en: "Thingangyun", mm: "သင်္ဃန်းကျွန်း" },
    { en: "South Okkalapa", mm: "တောင်ဥက္ကလာပ" },
    { en: "North Okkalapa", mm: "မြောက်ဥက္ကလာပ" }
  ],
  sagaing: [
    { en: "Monywa", mm: "မုံရွာ" },
    { en: "Shwebo", mm: "ရွှေဘို" },
    { en: "Kale", mm: "ကလေး" },
    { en: "Khin-U", mm: "ခင်ဦး" },
    { en: "Kanbalu", mm: "ကန့်ဘလူ" }
  ]
};

// Function for candidate/parent region-township selects
function updateTownships(regionId, townshipId, lang) {
  const regionSelect = document.getElementById(regionId);
  const townshipSelect = document.getElementById(townshipId);
  // On change of region, update township select options
  regionSelect.addEventListener("change", function () {
    const selectedRegion = this.value;
    townshipSelect.innerHTML = "";

    if (selectedRegion && townships[selectedRegion]) {
      const defaultOpt = document.createElement("option");
      defaultOpt.value = "";
      defaultOpt.textContent = lang === "mm" ? "မြို့နယ် ရွေးချယ်ပါ" : "Select Township";
      townshipSelect.appendChild(defaultOpt);

      townships[selectedRegion].forEach(town => {
        const option = document.createElement("option");
        option.value = lang === "mm" ? town.mm : town.en;
        option.textContent = lang === "mm" ? town.mm : town.en;
        townshipSelect.appendChild(option);
      });
    } else {
      const option = document.createElement("option");
      option.value = "";
      option.textContent = lang === "mm" ? "အရင်တိုင်းဒေသကြီးရွေးချယ်ပါ" : "Select Region First";
      townshipSelect.appendChild(option);
    }
  });
}

// ----------- Apply for all candidate and parent selects --------
// Candidate (Myanmar)
updateTownships("region-mm", "township-mm", "mm");
// Candidate (English)
updateTownships("region-en", "township-en", "en");
// Parent (Myanmar)
updateTownships("parent-region-mm", "parent-township-mm", "mm");
// Parent (English)
updateTownships("parent-region-en", "parent-township-en", "en");

// Myanmar field labels for review
const labels = {
  name: "ဘွဲ့အမည်",
  dob: "မွေးသက္ကရာဇ်",
  email: "အီးမေးလ်",
  address_region: "တိုင်းဒေသကြီး",
  address_township: "မြို့နယ်",
  address_village: "ရပ်ရွာ",
  father_name: "ခမည်းတော်",
  mother_name: "မယ်တော်အမည်",
  parent_address_region: "မိဘနေရပ် (တိုင်း)",
  parent_address_township: "မိဘနေရပ် (မြို့နယ်)",
  parent_address_village: "မိဘနေရပ် (ရပ်ရွာ)",
  monastery_name: "ကျောင်းတိုက်အမည်",
  abbot_name: "ကျောင်းတိုက်ဆရာတော်ဧ။်ဘွဲ့တော်",
  country: "နိုင်ငံ"
};

// Handle form submission for both languages
document.querySelectorAll('.lang-form').forEach(form => {
  form.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = {};
    formData.forEach((value, key) => {
      data[key] = value;
    });

    fetch("../level1/submit_level1.php", {
      method: "POST",
      body: formData
    })
      .then(response => response.text())
      .then(result => {

        const card=this.closest('.registration-card');
        card.style.display = "none";
        const cardHeader=card.querySelector('.card-header');
        if(cardHeader){
          cardHeader.style.display = "none";
        }

        this.style.display = "none";
        const review = document.createElement("div");
        review.classList.add("form-review");

        review.innerHTML = `
          <h2>✅ Thank You for Registering!</h2>
          <p>Here is what you submitted:</p>
          <ul>
            ${Object.entries(data).map(([key, value]) => 
              `<li><strong>${labels[key] || key}:</strong> ${value}</li>`
            ).join("")}
          </ul>
          <a href="level1.php" class="back-link">← Register another student</a>
          <a href="../../layouts/contact.php" class="contactus-link"> Give Feedback </a>
          <p class="db-status">📡 Server Response: ${result}</p>
        `;
        document.querySelector(".form-container").appendChild(review);
      })
      .catch(error => {
        alert(" Error submitting form: " + error);
      });
  });
});

