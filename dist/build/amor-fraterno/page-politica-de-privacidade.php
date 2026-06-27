<?php
/**
 * Privacy policy page template.
 *
 * @package Amor_Fraterno
 */

get_header();
?>
<main id="conteudo" class="privacy-main">
    <?php while (have_posts()) : the_post(); ?>
        <section class="privacy-hero" aria-labelledby="privacy-title">
            <div class="privacy-hero-bg" data-parallax="0.18"></div>
            <div class="privacy-hero-overlay"></div>
            <div class="privacy-hero-inner" data-aos="fade-up">
                <?php amor_breadcrumbs(); ?>
                <span class="eyebrow">LGPD, cookies e transparência</span>
                <h1 id="privacy-title"><?php the_title(); ?></h1>
                <p>Esta política explica como o <?php bloginfo('name'); ?> coleta, utiliza, compartilha e protege dados pessoais em seu site e canais digitais.</p>
                <span class="policy-updated">Atualizada em <?php echo esc_html(get_the_modified_date()); ?></span>
            </div>
        </section>

        <div class="privacy-layout">
            <aside class="privacy-toc" aria-label="Navegação da política">
                <strong>Nesta página</strong>
                <a href="#controlador">Quem somos</a>
                <a href="#dados">Dados tratados</a>
                <a href="#finalidades">Finalidades</a>
                <a href="#cookies">Cookies e anúncios</a>
                <a href="#compartilhamento">Compartilhamento</a>
                <a href="#direitos">Direitos do titular</a>
                <a href="#contato-privacidade">Contato</a>
            </aside>

            <div class="policy-content">
                <?php if (trim(get_the_content())) : ?>
                    <?php the_content(); ?>
                <?php else : ?>
                    <section class="policy-section" id="controlador" data-aos="fade-up">
                        <span class="section-kicker">Controlador</span>
                        <h2>Quem é responsável pelos dados</h2>
                        <p><?php bloginfo('name'); ?>, situado em <?php echo amor_text('location', 'Aparecida de Goiânia - Goiás'); ?>, é responsável pelo tratamento dos dados pessoais coletados por este site e canais digitais.</p>
                        <p>O canal de privacidade é o e-mail <a href="mailto:<?php echo esc_attr(amor_get('email', 'centroterapeuticoamorfraterno@gmail.com')); ?>"><?php echo amor_text('email', 'centroterapeuticoamorfraterno@gmail.com'); ?></a> e o WhatsApp <a href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener"><?php echo amor_text('phone_display', '(62) 99209-6062'); ?></a>.</p>
                    </section>
                    <section class="policy-section" id="dados" data-aos="fade-up">
                        <span class="section-kicker">Dados pessoais</span>
                        <h2>Quais dados podemos tratar</h2>
                        <ul>
                            <li>Dados de contato, como nome, telefone, e-mail e conteúdo da mensagem enviada.</li>
                            <li>Dados técnicos, como endereço IP, navegador, dispositivo, páginas acessadas, data e horário de acesso.</li>
                            <li>Dados de interação com anúncios e estatísticas, quando houver consentimento para cookies de análise ou publicidade.</li>
                            <li>Dados sensíveis informados voluntariamente durante o atendimento, tratados apenas quando necessários para acolhimento, saúde, obrigações legais ou exercício regular de direitos.</li>
                        </ul>
                    </section>
                    <section class="policy-section" id="finalidades" data-aos="fade-up">
                        <span class="section-kicker">Finalidades</span>
                        <h2>Por que usamos esses dados</h2>
                        <ul>
                            <li>Responder contatos feitos pelo site, telefone, WhatsApp ou e-mail.</li>
                            <li>Realizar acolhimento inicial, triagem e orientação para famílias e responsáveis.</li>
                            <li>Melhorar segurança, estabilidade, desempenho e conteúdo do site.</li>
                            <li>Medir acessos, campanhas e conversões quando você permitir cookies opcionais.</li>
                            <li>Cumprir obrigações legais e proteger direitos.</li>
                        </ul>
                    </section>
                    <section class="policy-section" id="cookies" data-aos="fade-up">
                        <span class="section-kicker">Cookies</span>
                        <h2>Cookies e mensuração</h2>
                        <div class="cookie-policy-grid">
                            <article><strong>Necessários</strong><p>Essenciais para funcionamento, segurança e preferências de privacidade.</p></article>
                            <article><strong>Análise</strong><p>Usados para medir visitas, páginas acessadas e desempenho do site.</p></article>
                            <article><strong>Publicidade</strong><p>Usados para medir conversões e melhorar campanhas, quando aplicável.</p></article>
                        </div>
                        <p>Você pode alterar sua escolha em <button class="policy-inline-button" type="button" data-cookie-customize>Preferências de cookies</button>.</p>
                    </section>
                    <section class="policy-section" id="compartilhamento" data-aos="fade-up">
                        <span class="section-kicker">Compartilhamento</span>
                        <h2>Com quem podemos compartilhar dados</h2>
                        <p>Compartilhamos dados apenas quando necessário, com fornecedores de tecnologia, plataformas de comunicação, profissionais envolvidos no atendimento ou autoridades quando houver obrigação legal.</p>
                    </section>
                    <section class="policy-section" id="direitos" data-aos="fade-up">
                        <span class="section-kicker">Direitos</span>
                        <h2>Seus direitos como titular</h2>
                        <p>Você pode solicitar confirmação de tratamento, acesso, correção, anonimização, bloqueio, eliminação, portabilidade quando aplicável, informações sobre compartilhamento, revogação de consentimento e oposição ao tratamento.</p>
                    </section>
                    <section class="policy-section policy-contact" id="contato-privacidade" data-aos="fade-up">
                        <span class="section-kicker">Contato</span>
                        <h2>Como falar sobre privacidade</h2>
                        <div class="policy-contact-grid">
                            <a href="mailto:<?php echo esc_attr(amor_get('email', 'centroterapeuticoamorfraterno@gmail.com')); ?>"><i data-lucide="mail" aria-hidden="true"></i><?php echo amor_text('email', 'centroterapeuticoamorfraterno@gmail.com'); ?></a>
                            <a href="<?php echo amor_whatsapp_url(); ?>" target="_blank" rel="noopener"><i data-lucide="message-circle" aria-hidden="true"></i><?php echo amor_text('phone_display', '(62) 99209-6062'); ?></a>
                        </div>
                    </section>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
