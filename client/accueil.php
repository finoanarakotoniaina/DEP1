<?php 
require_once '../config.php'; 

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperCar | L'Élégance d'Occasion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Syncopate:wght@400;700&family=Plus+Jakarta+Sans:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../style.css">
    
    <style>
        .reveal-right { transform: translateX(100px); opacity: 0; transition: all 1s ease-out; }
        .reveal-left { transform: translateX(-100px); opacity: 0; transition: all 1s ease-out; }
        .reveal-active { transform: translateX(0); opacity: 1; }
        .desc-font { font-family: 'Times New Roman', serif; }
    </style>
</head>

<body class="home-bg">

    <?php include 'composants_client/navbar.php'; ?>

    <section class="relative h-screen w-full flex items-center px-6 lg:px-24 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full z-0">
            <div class="absolute inset-0 video-overlay z-10"></div>
            <video autoplay muted loop playsinline class="w-full h-full object-cover">
                <source src="<?= ASSETS_URL ?>/video/mercedes.mp4" type="video/mp4">
            </video>
        </div>

        <div class="relative z-20 w-full mt-20">
            <div class="max-w-3xl">
                <h1 class="font-title text-4xl md:text-7xl font-bold leading-none mb-8">
                    <?php echo getContent('hero_title'); ?>
                </h1>
                <p class="text-lg text-gray-200 max-w-xl font-light leading-relaxed mb-10">
                    <?php echo e(getContent('hero_subtitle')); ?>
                </p>
                <div class="flex flex-wrap gap-6">
                    <a href="voiture.php?from=accueil" class="bg-[var(--crimson)] px-8 py-4 font-title text-xs tracking-widest hover:bg-white hover:text-black transition-all">DÉCOUVRIR LE STOCK</a>
                    <a href="contact.php?from=accueil" class="border border-white/30 px-8 py-4 font-title text-xs tracking-widest hover:border-[var(--crimson)] transition-all">NOUS CONTACTER</a>
                </div>
            </div>
        </div>
    </section>

    <section class="py-32 px-6 lg:px-24 relative overflow-hidden">
        <div class="absolute right-[-5%] top-10 outline-text font-title text-[15vw] opacity-10 select-none leading-none">
            GERMAN
        </div>
        
        <div class="relative z-10 mb-20">
            <h2 class="font-title text-4xl md:text-5xl mb-4 text-[var(--varden)]">NOS <span class="text-[var(--crimson)]">SÉLECTIONS</span></h2>
            <div class="w-32 h-1 bg-[var(--crimson)]"></div>
        </div>

        <div class="relative z-10 flex flex-col gap-12 max-w-3xl mx-auto">
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-right" data-delay="0">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4"><?php echo e(getContent('brand_1_name')); ?></h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" 
                   data-text="<?php echo e(getContent('brand_1_desc')); ?>"></p>
            </div>
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-left" data-delay="300">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4"><?php echo e(getContent('brand_2_name')); ?></h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" 
                   data-text="<?php echo e(getContent('brand_2_desc')); ?>"></p>
            </div>
            
            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-right" data-delay="600">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4"><?php echo e(getContent('brand_3_name')); ?></h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" 
                   data-text="<?php echo e(getContent('brand_3_desc')); ?>"></p>
            </div>

            <div class="brand-card p-8 bg-white/5 backdrop-blur-sm reveal-right" data-delay="450">
                <h3 class="font-title text-[var(--marble)] text-2xl mb-4"><?php echo e(getContent('brand_4_name')); ?></h3>
                <p class="text-gray-300 text-lg leading-relaxed desc-font opacity-0 transition-opacity duration-300 typewriter-text" 
                   data-text="<?php echo e(getContent('brand_4_desc')); ?>"></p>
            </div>
        </div>
    </section>

    <script>
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const delay = entry.target.getAttribute('data-delay');
                    setTimeout(() => {
                        entry.target.classList.add('reveal-active');
                    }, delay);
                }
            });
        }, { threshold: 0.2 });

        document.querySelectorAll('.reveal-right, .reveal-left').forEach(el => observer.observe(el));

        document.querySelectorAll('.brand-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                const textElement = this.querySelector('.typewriter-text');
                if (!textElement.dataset.hasTyped) {
                    textElement.dataset.hasTyped = "true";
                    textElement.classList.remove('opacity-0');
                    textElement.classList.add('opacity-100');
                    
                    const text = textElement.getAttribute('data-text');
                    let i = 0;
                    function typing() {
                        if (i < text.length) {
                            textElement.textContent += text.charAt(i);
                            i++;
                            setTimeout(typing, 15);
                        }
                    }
                    typing();
                }
            });
        });
    </script>

    <?php include 'composants_client/footer.php'; ?>