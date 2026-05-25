
document.addEventListener('DOMContentLoaded',()=>{

const toggle=document.getElementById('menu-toggle');
const menu=document.getElementById('menu');

if(toggle){
toggle.addEventListener('click',()=>{
menu.classList.toggle('active');
toggle.classList.toggle('active');
});
}

const submenuLinks=document.querySelectorAll('.menu li.has-submenu > a');
submenuLinks.forEach(link=>{
    const parent=link.parentElement;
    link.addEventListener('click',e=>{
        if(window.innerWidth <= 992){
            e.preventDefault();
            parent.classList.toggle('active');
        }
    });
});

const images=[
// 'assets/images/banner1.jpg',
'assets/images/banner2.jpg'
];

const slider=document.querySelector('.slider');

if(slider){

images.forEach((img,index)=>{
const slide=document.createElement('div');
slide.classList.add('slide');

if(index===0){
slide.classList.add('active');
}

slide.style.backgroundImage=`url(${img})`;
slider.appendChild(slide);
});

const slides=document.querySelectorAll('.slide');

let current=0;

setInterval(()=>{
slides[current].classList.remove('active');
current=(current+1)%slides.length;
slides[current].classList.add('active');
},5000);

}

});
