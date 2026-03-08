<?php
/**
 * Template Name: Course Discovery
 * Template Post ID: 35
 */

get_header();

// 1. DATA CAPTURE & SANITIZATION
$search_keyword       = isset($_GET['q']) ? sanitize_text_field($_GET['q']) : '';
$selected_instructors = isset($_GET['instructor_filter']) ? (array)$_GET['instructor_filter'] : [];
$selected_providers   = isset($_GET['provider_filter']) ? (array)$_GET['provider_filter'] : [];

// 2. QUERY BUILDING
$args = array(
    'post_type'      => 'course',
    'posts_per_page' => 12,
    's'              => $search_keyword,
    'meta_query'     => array('relation' => 'AND'),
);

// Filter by Instructor (ACF Post Object Relationship)
if (!empty($selected_instructors)) {
    $instructor_query = array('relation' => 'OR');
    foreach ($selected_instructors as $id) {
        $instructor_query[] = array(
            'key'     => 'instructors', // Matches your ACF Field Name
            'value'   => '"' . $id . '"',
            'compare' => 'LIKE',
        );
    }
    $args['meta_query'][] = $instructor_query;
}

// Filter by Provider (ACF Post Object Relationship)
if (!empty($selected_providers)) {
    $args['meta_query'][] = array(
        'key'     => 'providers', // Matches your ACF Field Name
        'value'   => $selected_providers,
        'compare' => 'IN',
    );
}

$course_query = new WP_Query($args);

// Pre-fetch filter options
$all_instructors = get_posts(array('post_type' => 'instructor', 'posts_per_page' => -1));
$all_providers   = get_posts(array('post_type' => 'provider', 'posts_per_page' => -1));
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');
    :root { --primary: #002147; --accent: #D4AF37; --bg: #F8FAFC; }

    .premium-discovery { font-family: 'Plus Jakarta Sans', sans-serif; background-color: var(--bg); min-height: 100vh; padding-bottom: 80px; }
    .hero-banner { background: var(--primary); padding: 80px 20px; text-align: center; color: white; border-bottom: 4px solid var(--accent); }
    .hero-banner h1 { font-size: 2.8rem; font-weight: 800; margin: 0; letter-spacing: -1px; }
    
    .main-grid { max-width: 1400px; margin: 40px auto; display: grid; grid-template-columns: 350px 1fr; gap: 40px; padding: 0 20px; }
    
    .filter-card { background: #fff; padding: 40px; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.05); height: fit-content; position: sticky; top: 20px; border: 1px solid #eee; }
    .filter-group { margin-bottom: 25px; }
    .filter-group label { display: block; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; color: var(--primary); margin-bottom: 10px; letter-spacing: 1px; }
    
    .premium-input { width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #E2E8F0; background: #F8FAFC; font-size: 0.95rem; transition: 0.3s; }
    .premium-input:focus { border-color: var(--accent); outline: none; background: #fff; box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1); }
    
    .btn-search { background: var(--primary); color: white; width: 100%; padding: 18px; border-radius: 12px; border: none; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 10px; }
    .btn-search:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,33,71,0.2); }

    .course-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px; }
    .course-card { background: white; border-radius: 24px; padding: 35px; border: 1px solid #EDF2F7; transition: 0.3s; display: flex; flex-direction: column; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .course-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); border-color: var(--accent); }
    
    .course-card h3 { color: var(--primary); margin: 0 0 15px 0; font-size: 1.6rem; font-weight: 700; line-height: 1.2; }
    .course-description { color: #64748B; font-size: 1rem; line-height: 1.6; flex-grow: 1; }
    
    .instructor-chip { display: inline-block; background: #F1F5F9; padding: 8px 14px; border-radius: 10px; font-size: 0.85rem; margin-top: 8px; color: var(--primary); font-weight: 600; margin-right: 6px; }
    .faculty-label { font-size: 0.7rem; font-weight: 800; color: #94A3B8; text-transform: uppercase; margin-bottom: 10px; letter-spacing: 0.5px; }
    
    .btn-reset { display: block; text-align: center; margin-top: 20px; font-size: 0.85rem; color: #94A3B8; text-decoration: none; font-weight: 600; }
</style>

<div class="premium-discovery">
    <div class="hero-banner">
        <h1>Course Discovery Portal</h1>
    </div>

    <div class="main-grid">
        <aside>
            <form method="GET" action="<?php echo esc_url(get_permalink()); ?>" class="filter-card" aria-label="Course Discovery Filters">
                <div class="filter-group">
                    <label for="q">Search by Keyword</label>
                    <input type="text" name="q" id="q" class="premium-input" value="<?php echo esc_attr($search_keyword); ?>" placeholder="e.g. Computer Science...">
                </div>

                <div class="filter-group">
                    <label for="instructor_filter">Filter by Instructors</label>
                    <select name="instructor_filter[]" id="instructor_filter" class="premium-input" multiple style="height:150px;">
                        <?php foreach ($all_instructors as $inst) : ?>
                            <option value="<?php echo esc_attr($inst->ID); ?>" <?php echo in_array($inst->ID, $selected_instructors) ? 'selected' : ''; ?>>
                                <?php echo esc_html(get_the_title($inst->ID)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="provider_filter">Filter by Providers</label>
                    <select name="provider_filter[]" id="provider_filter" class="premium-input" multiple style="height:150px;">
                        <?php foreach ($all_providers as $prov) : ?>
                            <option value="<?php echo esc_attr($prov->ID); ?>" <?php echo in_array($prov->ID, $selected_providers) ? 'selected' : ''; ?>>
                                <?php echo esc_html(get_the_title($prov->ID)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn-search">Search Programs</button>
                <a href="<?php echo esc_url(get_permalink()); ?>" class="btn-reset">Clear All Filters</a>
            </form>
        </aside>

        <section class="results-pane" aria-live="polite">
            <?php if ($course_query->have_posts()) : ?>
                <div class="course-grid">
                    <?php while ($course_query->have_posts()) : $course_query->the_post(); 
                        $current_id = get_the_ID();
                    ?>
                        <article class="course-card">
                            <div style="font-size: 0.7rem; font-weight: 800; color: var(--accent); text-transform: uppercase; margin-bottom: 12px; letter-spacing: 1px;">Academic Program</div>
                            
                            <h3><?php the_title(); ?></h3>
                            
                            <div class="course-description">
                                <?php echo esc_html(get_field('short_description', $current_id)); ?>
                            </div>

                            <div style="border-top: 1px solid #F1F5F9; padding-top: 25px; margin-top: 25px;">
                                <div class="faculty-label">Lead Faculty</div>
                                <?php 
                                $linked_instructors = get_field('instructors', $current_id);
                                if($linked_instructors): 
                                    foreach($linked_instructors as $inst): ?>
                                        <div class="instructor-chip">👤 <?php echo esc_html(get_the_title($inst->ID)); ?></div>
                                <?php endforeach; 
                                else: ?>
                                    <span style="font-size: 0.85rem; color: #94A3B8; font-style: italic;">Staff assignments pending</span>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            <?php else : ?>
                <div style="background: white; border-radius: 30px; padding: 100px 40px; text-align: center; border: 1px solid #eee;">
                    <div style="font-size: 3rem; margin-bottom: 20px;">🔍</div>
                    <h2 style="color: var(--primary); font-size: 2rem; margin-bottom: 10px;">No Results Found</h2>
                    <p style="color: #64748B;">Try adjusting your keywords or removing some instructor/provider filters.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php get_footer(); ?>