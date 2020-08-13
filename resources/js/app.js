import Prism from 'prismjs';
import animate from 'animateplus';
import Alpine from 'alpinejs';

window.animate = animate;

// Load languages
import 'prismjs/components/prism-markup';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-clike';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-sass';
import 'prismjs/plugins/line-highlight/prism-line-highlight.js';

Prism.highlightAll();
