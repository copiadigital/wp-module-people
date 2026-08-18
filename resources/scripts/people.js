import Swiper from 'swiper';
import { Navigation, Pagination, Scrollbar } from 'swiper/modules';
import 'swiper/css';

window.Swiper = Swiper;
window.SwiperModules = { Navigation, Pagination, Scrollbar };

document.dispatchEvent(new CustomEvent('people:swiper-ready'));
