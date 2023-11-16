// Hide message after a few seconds
let msgs = document.querySelectorAll('.msg');

// Get page name
let pageName = window.location.pathname.split('/')[window.location.pathname.split('/').length -1];

msgs.forEach(msg => {
    for(let i = 0; i < msg.children.length; i++) {
        if(msg.children[i].innerHTML != '') {
            setTimeout(() => {
                msg.style.opacity = 0;
                setTimeout(() => {
                    msg.style.display = 'none';
                    msg.innerHTML = '';
                }, 200)
            },3000)
        }else {
            msg.innerHTML = '';
        }
    }
})

allLinks = document.querySelectorAll('.navbar-nav .main-link');

allLinks.forEach(links => {
    links.classList.remove('active')
    if(links.href.split('/')[links.href.split('/').length - 1] ==  pageName) {
        links.classList.add('active')
    }
})


// add product when click on it 

num = 1;
function addPro() {

    let proArea = document.querySelector('.pro-area');


    // إنشاء النص الجديد للعناصر المضافة
    var newElements = `
        <div class="add-pro d-flex position-relative justify-content-between align-items-center mt-4 gap-2 back-one p-4 rounded mb-4">
            <div class="col-6 ">
                <label class="mb-2 mt-md-0 mt-3">Name</label>
                <input type="text" name="products[${num}][pro_name]" class="form-control" placeholder="Product name">
            </div>
            <div class="col-3">
                <label class="mb-2 mt-md-0 mt-3">Price</label>
                <input type="number" name="products[${num}][pro_price]" class="form-control" placeholder="Product price">
            </div>
            <div class="col-3">
                <label class="mb-2 mt-md-0 mt-3">Quantity</label>
                <input type="number" name="products[${num}][quantity]" class="form-control" placeholder="Quantity" value="1">
            </div>
            <i class="fa-solid fa-circle-xmark position-absolute end-0 mt-2 me-2 top-0 fs-4" id="${num}" onclick="deletePro(${num})"></i>
        </div>
    `;

    // إضافة العناصر الجديدة دون فقدان القيم الموجودة
    proArea.insertAdjacentHTML("beforeend", newElements);
    
    num++
}


function deletePro(id) {
    element = document.getElementById(id);


    if(element) {
        element.parentNode.remove()
    }
}





function generatePDF() {
    const element = document.body;
    const options = {
    filename: 'test.pdf',
    margin: 0,
    jsPDF: { format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(options).from(element).save();
}


if(pageName == 'preview.php') {
    generatePDF()

    setTimeout(() => {
        history.back();
    }, 500)
}

// Sroting Element (Descending - Ascending)
let sortButtons = document.querySelectorAll('.sorting-btn');

sortButtons.forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault;
        sortButtons.forEach(ele => ele.classList.remove('active'));
        this.classList.add('active')
    })
})

function getData() {
    sortButtons.forEach(button => { 
        if(button.classList.contains('active')) {
            result = button.innerHTML;
        }
    })

    return result
}

// Fetch Data 
function fetchData(ele, sortingType) {

    document.getElementById(ele.id).nextElementSibling.innerHTML = '';
    nextElement = ele.nextElementSibling;

    let formData = new FormData();

    formData.append('storeid',ele.id)      
    formData.append('sort', sortingType)      

    fetch('api.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
        // تتبع الأيدي المتشابهة
            const addedIDs = [];

            data.forEach((element) => {
            const targetElement = document.getElementById(ele.id).nextElementSibling;
            // التحقق ما إذا كان الـ ID تم تضمينه بالفعل في الصندوق
            const index = addedIDs.indexOf(element.b_id);
            if (index == -1) {
                // إضافة العنصر الجديد إلى صندوق جديد
                targetElement.innerHTML += `
                <a href="show_bill.php?bill=${element.b_id}" class="mt-4 p-4 bg-white shadow-sm rounded position-relative">
                <div class="logo d-flex justify-content-between align-items-center mb-4">
                <div class="right text-center">
                    <img src="images/logo.png" alt="" class="img-fluid rounded-circle shadow-sm mb-2">
                    <p class="color-two fw-bold">Fawatiruk</p>
                </div>
                <div class="left fs-4 color-two fw-bold">
                    ${element.b_id}#
                </div>
            </div>
            <div class="content p-4 rounded">
                <ul class="p-0">
                    <li>Phone number: <span> ${element.phone_number} </span></li>
                    <li>Description: <span>${element.description ? element.description : 'There is no description'}</span></li>
                    <li>Date: <span>${element.date}</span></li>
                </ul>
            </div>
            <span class="show-more">Show more <i class="fa-solid fa-expand fs-1"></i></span>
                </a>
                `;
    
        // إضافة الـ ID إلى قائمة الأيدي المضافة
        addedIDs.push(element.b_id);
        } 
        });
            
    });
        nextElement.classList.add('showEle');
}



let storesName = document.querySelectorAll('.user-billinfo .title');
let infoBox = document.querySelectorAll('.user-billinfo .info-boxes');

storesName.forEach((name) => {

    sortButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault;
            
            fetchData(name, getData());
        })
    })

    fetchData(name, 'ASC');

});


storesName.forEach(storename => {
    storename.addEventListener('click', function(e) {
        
        billsContent = this.nextElementSibling;

        // Check if current billsContent is already shown 
        if(billsContent.classList.contains('showEle')) {

            billsContent.classList.remove('showEle')
            billsContent.classList.add('hiddenEle')
            this.querySelector('i').className = 'fa-solid fa-chevron-up ms-2 fs-3'
            
        }else {
            billsContent.classList.add('showEle')
            billsContent.classList.remove('hiddenEle')
            this.querySelector('i').className = 'fa-solid fa-chevron-down ms-2 fs-3'
        }

    })
})