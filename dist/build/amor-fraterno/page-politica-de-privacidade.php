<?php
/**
 * Privacy policy page template.
 *
 * Template Name: Política de Privacidade
 *
 * @package Amor_Fraterno
 */

get_header();

$site_name = get_bloginfo('name');
$location = amor_get('location', 'Aparecida de Goiânia - Goiás');
$email = amor_get('email', 'centroterapeuticoamorfraterno@gmail.com');
$phone_display = amor_get('phone_display', '(62) 99209-6062');
$privacy_message = 'Olá, vim pela Política de Privacidade do site da Amor Fraterno e quero falar sobre meus dados pessoais.';
$whatsapp_url = amor_whatsapp_url($privacy_message);
$mailto_url = 'mailto:' . sanitize_email($email) . '?subject=' . rawurlencode('Privacidade e dados pessoais - ' . $site_name);
?>
<main id="conteudo" class="privacy-main">
    <?php while (have_posts()) : the_post(); ?>
        <section class="privacy-hero" aria-labelledby="privacy-title">
            <div class="privacy-hero-bg" data-parallax="0.18"></div>
            <div class="privacy-hero-overlay"></div>
            <div class="privacy-hero-inner privacy-hero-grid" data-aos="fade-up">
                <div>
                    <?php amor_breadcrumbs(); ?>
                    <span class="eyebrow">LGPD, sigilo e transparência</span>
                    <h1 id="privacy-title">Política de Privacidade</h1>
                    <p>Entenda como o <?php echo esc_html($site_name); ?> coleta, utiliza, protege e compartilha dados pessoais em seus canais digitais e no atendimento inicial às famílias.</p>
                    <span class="policy-updated">Atualizada em <?php echo esc_html(get_the_modified_date()); ?></span>
                </div>

                <div class="privacy-hero-card" aria-label="Dados do controlador">
                    <span>Controlador dos dados</span>
                    <strong><?php echo esc_html($site_name); ?></strong>
                    <p>Centro terapêutico situado em <?php echo esc_html($location); ?>.</p>
                    <div class="privacy-hero-actions">
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener">
                            <i data-lucide="message-circle" aria-hidden="true"></i>
                            WhatsApp
                        </a>
                        <a href="<?php echo esc_url($mailto_url); ?>">
                            <i data-lucide="mail" aria-hidden="true"></i>
                            E-mail
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <div class="privacy-layout">
            <aside class="privacy-toc" aria-label="Navegação da política">
                <strong>Nesta página</strong>
                <a href="#controlador">Quem somos</a>
                <a href="#dados">Dados tratados</a>
                <a href="#bases-legais">Bases legais</a>
                <a href="#finalidades">Finalidades</a>
                <a href="#cookies">Cookies</a>
                <a href="#compartilhamento">Compartilhamento</a>
                <a href="#seguranca-retencao">Segurança e retenção</a>
                <a href="#direitos">Direitos do titular</a>
                <a href="#contato-privacidade">Contato</a>
            </aside>

            <div class="policy-content">
                <section class="policy-section policy-intro" id="controlador" data-aos="fade-up">
                    <span class="section-kicker">Controlador</span>
                    <h2>Quem é responsável pelos seus dados</h2>
                    <p>O <?php echo esc_html($site_name); ?> é o controlador dos dados pessoais tratados por este site, pelos formulários de contato, WhatsApp, e-mail, telefone e demais canais digitais vinculados ao centro terapêutico.</p>
                    <p>Esta política foi preparada com base na Lei Geral de Proteção de Dados Pessoais (Lei nº 13.709/2018), que protege direitos fundamentais de liberdade, privacidade e livre desenvolvimento da personalidade.</p>
                    <div class="policy-info-strip">
                        <span><i data-lucide="map-pin" aria-hidden="true"></i><?php echo esc_html($location); ?></span>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener"><i data-lucide="message-circle" aria-hidden="true"></i><?php echo esc_html($phone_display); ?></a>
                        <a href="<?php echo esc_url($mailto_url); ?>"><i data-lucide="mail" aria-hidden="true"></i><?php echo esc_html($email); ?></a>
                    </div>
                </section>

                <section class="policy-section" id="dados" data-aos="fade-up">
                    <span class="section-kicker">Dados pessoais</span>
                    <h2>Quais dados podemos tratar</h2>
                    <ul>
                        <li><strong>Dados de identificação e contato:</strong> nome, telefone, e-mail, cidade, vínculo familiar e conteúdo das mensagens enviadas.</li>
                        <li><strong>Dados de atendimento:</strong> informações compartilhadas voluntariamente para acolhimento inicial, orientação à família, avaliação de disponibilidade e encaminhamento responsável.</li>
                        <li><strong>Dados sensíveis:</strong> informações sobre saúde, dependência química, histórico familiar ou contexto terapêutico, quando fornecidas pelo titular ou responsável e necessárias para orientação, proteção da vida, tutela da saúde, cumprimento de obrigação legal ou exercício regular de direitos.</li>
                        <li><strong>Dados técnicos:</strong> endereço IP, data e horário de acesso, navegador, dispositivo, páginas visitadas, origem do acesso e preferências de cookies.</li>
                        <li><strong>Dados de campanhas:</strong> interações com anúncios, conversões e métricas de navegação, apenas quando houver consentimento para cookies opcionais ou outra base legal aplicável.</li>
                    </ul>
                </section>

                <section class="policy-section" id="bases-legais" data-aos="fade-up">
                    <span class="section-kicker">LGPD</span>
                    <h2>Bases legais utilizadas</h2>
                    <div class="policy-card-grid">
                        <article><strong>Consentimento</strong><p>Quando você autoriza cookies opcionais, envio de mensagens ou uso de dados para uma finalidade específica.</p></article>
                        <article><strong>Execução de procedimentos preliminares</strong><p>Quando os dados são necessários para responder solicitações feitas por você ou por familiar/responsável.</p></article>
                        <article><strong>Tutela da saúde e proteção da vida</strong><p>Quando informações de saúde são necessárias para orientação inicial, acolhimento ou encaminhamento adequado.</p></article>
                        <article><strong>Obrigação legal e exercício de direitos</strong><p>Quando o tratamento é necessário para cumprir normas, preservar registros e proteger direitos do centro, do titular ou de terceiros.</p></article>
                    </div>
                </section>

                <section class="policy-section" id="finalidades" data-aos="fade-up">
                    <span class="section-kicker">Finalidades</span>
                    <h2>Para que usamos os dados</h2>
                    <ul>
                        <li>Responder contatos recebidos pelo site, telefone, WhatsApp, e-mail ou redes digitais.</li>
                        <li>Realizar acolhimento inicial, entender a necessidade apresentada e orientar familiares ou responsáveis.</li>
                        <li>Organizar retornos, registrar histórico mínimo de atendimento e evitar perda de informações importantes.</li>
                        <li>Manter o site seguro, estável, acessível e adequado ao público interessado nos serviços do centro terapêutico.</li>
                        <li>Mensurar desempenho de páginas, campanhas e canais de contato quando você permitir cookies de análise ou publicidade.</li>
                        <li>Cumprir obrigações legais, regulatórias, administrativas ou solicitações de autoridades competentes.</li>
                    </ul>
                </section>

                <section class="policy-section" id="cookies" data-aos="fade-up">
                    <span class="section-kicker">Cookies</span>
                    <h2>Cookies e tecnologias semelhantes</h2>
                    <div class="cookie-policy-grid">
                        <article><strong>Necessários</strong><p>Essenciais para funcionamento do site, segurança, carregamento de páginas e registro das suas preferências de privacidade.</p></article>
                        <article><strong>Análise</strong><p>Ajudam a entender visitas, páginas acessadas e desempenho do site, sem buscar atendimento individualizado sem necessidade.</p></article>
                        <article><strong>Publicidade</strong><p>Podem medir conversões de campanhas e melhorar anúncios, sempre conforme sua escolha e configurações disponíveis.</p></article>
                    </div>
                    <p>Você pode revisar sua escolha a qualquer momento em <button class="policy-inline-button" type="button" data-cookie-customize>Preferências de cookies</button>.</p>
                </section>

                <section class="policy-section" id="compartilhamento" data-aos="fade-up">
                    <span class="section-kicker">Compartilhamento</span>
                    <h2>Com quem os dados podem ser compartilhados</h2>
                    <p>O <?php echo esc_html($site_name); ?> não vende dados pessoais. O compartilhamento ocorre apenas quando necessário para atendimento, operação do site, comunicação, segurança, cumprimento de obrigações legais ou proteção de direitos.</p>
                    <ul>
                        <li>Fornecedores de hospedagem, segurança, manutenção do site e ferramentas de análise ou publicidade.</li>
                        <li>Plataformas de comunicação, como WhatsApp, e-mail, telefonia e ferramentas de atendimento.</li>
                        <li>Profissionais ou equipes envolvidos no acolhimento, sempre observando necessidade, sigilo e finalidade.</li>
                        <li>Autoridades públicas, órgãos reguladores ou terceiros quando houver obrigação legal ou ordem válida.</li>
                    </ul>
                </section>

                <section class="policy-section" id="seguranca-retencao" data-aos="fade-up">
                    <span class="section-kicker">Proteção</span>
                    <h2>Segurança, sigilo e retenção</h2>
                    <p>Adotamos medidas administrativas, técnicas e organizacionais para reduzir riscos de acesso não autorizado, perda, alteração ou uso inadequado dos dados pessoais. O acesso às informações é limitado a pessoas e fornecedores que precisam delas para cumprir as finalidades informadas.</p>
                    <p>Os dados são mantidos pelo tempo necessário para atendimento, cumprimento de obrigações legais, prevenção a fraudes, auditoria, exercício regular de direitos ou enquanto houver base legal aplicável. Quando não forem mais necessários, poderão ser eliminados, anonimizados ou bloqueados conforme a LGPD.</p>
                </section>

                <section class="policy-section" id="direitos" data-aos="fade-up">
                    <span class="section-kicker">Direitos</span>
                    <h2>Seus direitos como titular</h2>
                    <p>Você pode solicitar confirmação de tratamento, acesso aos dados, correção, anonimização, bloqueio, eliminação, portabilidade quando aplicável, informação sobre compartilhamento, revisão de decisões automatizadas, oposição ao tratamento e revogação do consentimento.</p>
                    <p>Pedidos podem exigir confirmação de identidade para proteger sua privacidade e evitar acesso indevido por terceiros. Responderemos dentro de prazo razoável e conforme a legislação aplicável.</p>
                </section>

                <section class="policy-section policy-notice" id="menores" data-aos="fade-up">
                    <span class="section-kicker">Crianças e adolescentes</span>
                    <h2>Dados de menores de idade</h2>
                    <p>Quando houver informações de crianças ou adolescentes, o tratamento será realizado com prioridade ao melhor interesse do menor, com participação de pais, responsáveis legais ou base legal adequada para proteção da vida, saúde, segurança e direitos.</p>
                </section>

                <section class="policy-section policy-contact" id="contato-privacidade" data-aos="fade-up">
                    <span class="section-kicker">Contato</span>
                    <h2>Como falar sobre privacidade</h2>
                    <p>Para exercer direitos, tirar dúvidas ou solicitar informações sobre o tratamento de dados pessoais, use os canais abaixo.</p>
                    <div class="policy-contact-grid">
                        <a href="<?php echo esc_url($mailto_url); ?>"><i data-lucide="mail" aria-hidden="true"></i><?php echo esc_html($email); ?></a>
                        <a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener"><i data-lucide="message-circle" aria-hidden="true"></i><?php echo esc_html($phone_display); ?></a>
                    </div>
                </section>
            </div>
        </div>
    <?php endwhile; ?>
</main>
<?php get_footer(); ?>
