<?php
/**
 * Mavenbird
 *
 * Sample data for Mavenbird_Blog: authors, categories, tags, topics, and a
 * dozen posts with real HTML body content, themed to match the Koti/Luma
 * home-goods demo catalog (Living Room, Bedroom, Dining, Office, Accessories).
 *
 * Idempotent: every post is tagged with an import_source of
 * "sample_data-<slug>", and the patch skips any post whose import_source
 * already exists, using the module's own Post::isImported() helper - so
 * re-running setup:upgrade (or bin/magento sampledata:reset) after a partial
 * run won't create duplicates. Authors/categories/tags/topics are looked up
 * by name and reused if already present.
 *
 * @category    Mavenbird
 * @package     Mavenbird_Blog
 */
declare(strict_types=1);

namespace Mavenbird\Blog\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mavenbird\Blog\Model\AuthorFactory;
use Mavenbird\Blog\Model\CategoryFactory;
use Mavenbird\Blog\Model\PostFactory;
use Mavenbird\Blog\Model\ResourceModel\Post as PostResource;
use Mavenbird\Blog\Model\TagFactory;
use Mavenbird\Blog\Model\TopicFactory;

class InsertSampleData implements DataPatchInterface, PatchRevertableInterface
{
    private const IMPORT_SOURCE = 'sample_data';

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /** @var AuthorFactory */
    private $authorFactory;

    /** @var CategoryFactory */
    private $categoryFactory;

    /** @var TagFactory */
    private $tagFactory;

    /** @var TopicFactory */
    private $topicFactory;

    /** @var PostFactory */
    private $postFactory;

    /** @var PostResource */
    private $postResource;

    /** @var DateTime */
    private $date;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        AuthorFactory $authorFactory,
        CategoryFactory $categoryFactory,
        TagFactory $tagFactory,
        TopicFactory $topicFactory,
        PostFactory $postFactory,
        PostResource $postResource,
        DateTime $date
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->authorFactory   = $authorFactory;
        $this->categoryFactory = $categoryFactory;
        $this->tagFactory      = $tagFactory;
        $this->topicFactory    = $topicFactory;
        $this->postFactory     = $postFactory;
        $this->postResource    = $postResource;
        $this->date            = $date;
    }

    public function apply()
    {
        $authorIds   = $this->createAuthors();
        $rootPath    = $this->getRootCategoryPath();
        $categoryIds = $this->createCategories($rootPath);
        $tagIds      = $this->createTags();
        $topicIds    = $this->createTopics();

        $this->createPosts($authorIds, $categoryIds, $tagIds, $topicIds);
    }

    public function revert()
    {
    }

    public function getAliases()
    {
        return [];
    }

    public static function getDependencies()
    {
        return [
            InsertData::class,
            InsertRootCategory::class,
        ];
    }

    // -------------------------------------------------------------------
    // Authors
    // -------------------------------------------------------------------

    /**
     * @return int[] name => author_id
     */
    private function createAuthors(): array
    {
        $authors = [
            'Elena Cross'  => 'Interiors editor covering small-space living and seasonal styling for eight years.',
            'Marcus Vale'  => 'Furniture maker turned writer; focuses on materials, joinery, and how things are built to last.',
            'Priya Nandan' => 'Sustainability researcher writing about responsible sourcing and the lifecycle of home goods.',
            'Tobias Reyes' => 'Customer experience lead who collects the stories behind how people actually use their homes.',
        ];

        $ids = [];
        foreach ($authors as $name => $bio) {
            $author = $this->authorFactory->create();
            $author->getResource()->load($author, $name, 'name');
            if (!$author->getId()) {
                $author->setData([
                    'name'              => $name,
                    'short_description' => $bio,
                    'type'              => 0,
                    'status'            => 1,
                    'created_at'        => $this->date->date(),
                ]);
                $author->save();
            }
            $ids[$name] = (int) $author->getId();
        }

        return $ids;
    }

    // -------------------------------------------------------------------
    // Categories
    // -------------------------------------------------------------------

    private function getRootCategoryPath(): string
    {
        $collection = $this->categoryFactory->create()->getCollection();
        $collection->addFieldToFilter('parent_id', ['null' => true]);
        $root = $collection->getFirstItem();

        return $root->getId() ? (string) $root->getPath() : '1';
    }

    /**
     * @return int[] name => category_id
     */
    private function createCategories(string $rootPath): array
    {
        $categories = [
            'Design Tips'        => 'Layout, color, and styling advice for making small and large rooms work harder.',
            'Product Care'       => 'How to clean, maintain, and repair the pieces you already own.',
            'Sustainability'     => 'Materials, sourcing, and the environmental side of furnishing a home.',
            'Behind the Scenes'  => 'How Koti pieces get designed, tested, and made.',
            'Customer Stories'   => 'Real homes, real layouts, real problems solved.',
        ];

        $ids = [];
        foreach ($categories as $name => $description) {
            $category = $this->categoryFactory->create();
            $category->getResource()->load($category, $name, 'name');
            if (!$category->getId()) {
                $category->setData([
                    'name'        => $name,
                    'description' => $description,
                    'store_ids'   => [0],
                    'enabled'     => 1,
                    'path'        => $rootPath,
                ]);
                $category->save();
            }
            $ids[$name] = (int) $category->getId();
        }

        return $ids;
    }

    // -------------------------------------------------------------------
    // Tags
    // -------------------------------------------------------------------

    /**
     * @return int[] name => tag_id
     */
    private function createTags(): array
    {
        $tags = [
            'minimalist', 'scandinavian', 'small-space', 'eco-friendly',
            'diy', 'trending', 'buying-guide', 'maintenance',
        ];

        $ids = [];
        foreach ($tags as $name) {
            $tag = $this->tagFactory->create();
            $tag->getResource()->load($tag, $name, 'name');
            if (!$tag->getId()) {
                $tag->setData([
                    'name'      => $name,
                    'store_ids' => [0],
                    'enabled'   => 1,
                ]);
                $tag->save();
            }
            $ids[$name] = (int) $tag->getId();
        }

        return $ids;
    }

    // -------------------------------------------------------------------
    // Topics
    // -------------------------------------------------------------------

    /**
     * @return int[] name => topic_id
     */
    private function createTopics(): array
    {
        $topics = [
            "Editor's Picks" => 'Staff-selected reads.',
            'Seasonal'       => 'Content tied to a particular time of year.',
        ];

        $ids = [];
        foreach ($topics as $name => $description) {
            $topic = $this->topicFactory->create();
            $topic->getResource()->load($topic, $name, 'name');
            if (!$topic->getId()) {
                $topic->setData([
                    'name'        => $name,
                    'description' => $description,
                    'store_ids'   => [0],
                    'enabled'     => 1,
                ]);
                $topic->save();
            }
            $ids[$name] = (int) $topic->getId();
        }

        return $ids;
    }

    // -------------------------------------------------------------------
    // Posts
    // -------------------------------------------------------------------

    private function createPosts(array $authorIds, array $categoryIds, array $tagIds, array $topicIds): void
    {
        $a = $authorIds;
        $c = $categoryIds;
        $t = $tagIds;
        $p = $topicIds;

        $posts = $this->getPostDefinitions($a, $c, $t, $p);

        $dayOffset = 0;
        foreach ($posts as $slug => $def) {
            $importSource = self::IMPORT_SOURCE;
            if ($this->postResource->isImported($importSource, $slug)) {
                $dayOffset += 6;
                continue;
            }

            $publishDate = $this->date->date('Y-m-d H:i:s', strtotime('-' . (3 + $dayOffset) . ' days'));

            /** @var \Mavenbird\Blog\Model\Post $post */
            $post = $this->postFactory->create();
            $post->setData([
                'name'                 => $def['name'],
                'short_description'    => $def['short_description'],
                'post_content'         => $def['post_content'],
                'store_ids'            => [0],
                'enabled'              => 1,
                'in_rss'               => 1,
                'allow_comment'        => 1,
                'author_id'            => $def['author_id'],
                'publish_date'         => $publishDate,
                'meta_title'           => $def['name'],
                'meta_description'     => $def['short_description'],
                'import_source'        => $importSource . '-' . $slug,
            ]);
            $post->setCategoriesIds($def['category_ids']);
            $post->setTagsIds($def['tag_ids']);
            if (!empty($def['topic_ids'])) {
                $post->setTopicsIds($def['topic_ids']);
            }
            $post->save();

            $dayOffset += 6;
        }
    }

    /**
     * @return array<string, array{name:string, short_description:string, post_content:string,
     *     author_id:int, category_ids:int[], tag_ids:int[], topic_ids?:int[]}>
     */
    private function getPostDefinitions(array $a, array $c, array $t, array $p): array
    {
        return [
            'small-space-living-room-layouts' => [
                'name'              => '5 Living Room Layouts That Work in Small Spaces',
                'short_description' => 'You do not need a big room for a living room that feels finished. '
                    . 'These five layouts make the most of tight square footage.',
                'post_content'      => '<h2>Start with traffic, not furniture</h2>'
                    . '<p>Before choosing a sofa, walk the room and note how people actually move through it. '
                    . 'In most small living rooms there is one natural path - from the door to the main seat - '
                    . 'and everything else should be built around keeping that path clear.</p>'
                    . '<h2>Five layouts worth trying</h2>'
                    . '<ul>'
                    . '<li><strong>The L-shape hug.</strong> A two-seat sofa and an armchair meet at a corner, '
                    . 'facing a low coffee table. Leaves the center of the room open.</li>'
                    . '<li><strong>Floating against a wall.</strong> Pull the sofa a few inches off the wall and '
                    . 'add a slim console behind it - the gap reads as intentional, not wasted space.</li>'
                    . '<li><strong>Two chairs, no sofa.</strong> A pair of accent chairs takes up less floor area '
                    . 'than a three-seat sofa and can be angled toward each other for conversation.</li>'
                    . '<li><strong>The room divider.</strong> In open-plan homes, a low bookshelf or credenza can '
                    . 'mark the living room boundary without blocking light.</li>'
                    . '<li><strong>Everything on casters.</strong> A side table and an ottoman with wheels let the '
                    . 'layout change for movie night versus a small gathering.</li>'
                    . '</ul>'
                    . '<p>Whichever layout you choose, leave at least 30 inches for walking paths and measure '
                    . 'twice before you order anything.</p>',
                'author_id'         => $a['Elena Cross'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['small-space'], $t['minimalist'], $t['buying-guide']],
                'topic_ids'         => [$p["Editor's Picks"]],
            ],
            'caring-for-solid-wood-furniture' => [
                'name'              => 'How to Care for Solid Wood Furniture So It Lasts Decades',
                'short_description' => 'Solid wood ages well if you treat it right. A short, practical '
                    . 'maintenance routine for tables, dressers, and shelving.',
                'post_content'      => '<h2>Wood moves - plan for it</h2>'
                    . '<p>Solid wood expands and contracts with humidity. A tabletop that fits perfectly in summer '
                    . 'may feel slightly different in a dry winter, and that is normal, not a defect. Keep pieces '
                    . 'away from direct radiator heat and out of direct sun where possible.</p>'
                    . '<h2>A routine that actually gets done</h2>'
                    . '<ul>'
                    . '<li><strong>Weekly:</strong> dust with a dry microfiber cloth, following the grain.</li>'
                    . '<li><strong>Monthly:</strong> wipe with a barely damp cloth to lift buildup, then dry '
                    . 'immediately - standing water is the main enemy of a wood finish.</li>'
                    . '<li><strong>Twice a year:</strong> apply a wood conditioner or wax appropriate to the '
                    . 'finish, working in thin coats.</li>'
                    . '</ul>'
                    . '<p>Skip silicone-based sprays - they build up over time and make future refinishing much '
                    . 'harder. If a scratch shows through the finish, a matching wax stick is usually enough to '
                    . 'hide it without a full refinish.</p>',
                'author_id'         => $a['Marcus Vale'],
                'category_ids'      => [$c['Product Care']],
                'tag_ids'           => [$t['maintenance'], $t['diy']],
            ],
            'sourcing-responsibly-made-furniture' => [
                'name'              => 'What "Responsibly Made" Actually Means When You Buy Furniture',
                'short_description' => 'Sustainability claims on furniture labels are easy to make and hard to '
                    . 'verify. Here is what is worth checking before you buy.',
                'post_content'      => '<h2>Three questions worth asking</h2>'
                    . '<p>"Eco-friendly" is not a regulated term, so it is worth asking three more specific '
                    . 'questions instead of taking a label at face value.</p>'
                    . '<ul>'
                    . '<li><strong>Where did the material come from?</strong> Look for a named forestry '
                    . 'certification rather than a general claim of "sustainably sourced."</li>'
                    . '<li><strong>How far did it travel?</strong> Regional manufacturing usually means a smaller '
                    . 'shipping footprint than a piece assembled on another continent.</li>'
                    . '<li><strong>Can it be repaired?</strong> A piece with replaceable legs, re-coverable '
                    . 'cushions, and accessible joinery will outlast one that is glued and stapled shut.</li>'
                    . '</ul>'
                    . '<p>None of this means the cheapest option is automatically the worst choice, or the most '
                    . 'expensive the best - but a maker who can answer these three questions clearly is usually '
                    . 'one worth trusting.</p>',
                'author_id'         => $a['Priya Nandan'],
                'category_ids'      => [$c['Sustainability']],
                'tag_ids'           => [$t['eco-friendly'], $t['buying-guide']],
            ],
            'dining-table-sizing-guide' => [
                'name'              => 'Dining Table Sizing: How to Get It Right the First Time',
                'short_description' => 'The most common dining table mistake is not about style - it is about '
                    . 'buying a table that does not fit the room.',
                'post_content'      => '<h2>Measure the room, not just the table</h2>'
                    . '<p>Leave at least 36 inches of clearance between the table edge and any wall or '
                    . 'furniture, so chairs can slide out and people can walk past a seated guest.</p>'
                    . '<h2>A rough sizing guide</h2>'
                    . '<ul>'
                    . '<li><strong>4 people:</strong> a 48-inch round or a 60-inch rectangular table.</li>'
                    . '<li><strong>6 people:</strong> a 72-inch rectangular table, or a 54-inch round with '
                    . 'a leaf.</li>'
                    . '<li><strong>8 people:</strong> a 90-96 inch rectangular table, or two smaller tables '
                    . 'pushed together for flexibility.</li>'
                    . '</ul>'
                    . '<p>Allow about 24 inches of table edge per seated person, and remember that a bench seats '
                    . 'more people than the same length in individual chairs - useful if you host often but '
                    . 'do not have room to store extra chairs.</p>',
                'author_id'         => $a['Elena Cross'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['buying-guide'], $t['trending']],
            ],
            'behind-the-design-terra-collection' => [
                'name'              => 'Behind the Design: How the Terra Dinnerware Collection Came Together',
                'short_description' => 'A look at the early sketches, material tests, and small changes that '
                    . 'shaped one of our most-used tableware lines.',
                'post_content'      => '<h2>Starting from a single mug</h2>'
                    . '<p>Every collection starts smaller than people expect. The Terra line began as a single '
                    . 'stoneware mug, tested through more than a dozen glaze variations before the team settled '
                    . 'on the current matte finish.</p>'
                    . '<p>Once the glaze was locked in, the rest of the collection - bowls, side plates, a '
                    . 'serving set - followed the same proportions so pieces would visually belong together on '
                    . 'a table even when mixed and matched.</p>'
                    . '<h2>What changed along the way</h2>'
                    . '<p>Early prototypes had a thinner rim that looked elegant in photos but chipped easily in '
                    . 'daily use. The production version has a slightly thicker edge - a small change that is '
                    . 'barely visible but noticeably more durable.</p>',
                'author_id'         => $a['Marcus Vale'],
                'category_ids'      => [$c['Behind the Scenes']],
                'tag_ids'           => [$t['trending']],
                'topic_ids'         => [$p["Editor's Picks"]],
            ],
            'home-office-in-a-corner' => [
                'name'              => 'Building a Real Home Office Out of an Unused Corner',
                'short_description' => 'You do not need a spare room to get a home office that actually works. '
                    . 'Here is how to make one corner do the job.',
                'post_content'      => '<h2>Pick the corner with the best light</h2>'
                    . '<p>Natural light matters more than square footage. A narrow corner near a window will '
                    . 'feel better to work in over a full afternoon than a larger space lit only by an overhead '
                    . 'fixture.</p>'
                    . '<h2>What actually fits in a corner</h2>'
                    . '<ul>'
                    . '<li>A desk no deeper than 24 inches keeps the footprint small without cramping your '
                    . 'elbows.</li>'
                    . '<li>Wall-mounted shelving above the desk keeps the floor clear and adds storage without '
                    . 'a bulky cabinet.</li>'
                    . '<li>A task lamp with a warm bulb reduces reliance on overhead lighting during evening '
                    . 'work sessions.</li>'
                    . '</ul>'
                    . '<p>If the corner is also visible from your living space, choose a chair that looks as good '
                    . 'from the back as the front - you will be looking at it more than you think.</p>',
                'author_id'         => $a['Elena Cross'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['small-space'], $t['diy']],
            ],
            'refreshing-upholstery-without-replacing-it' => [
                'name'              => 'Refreshing Tired Upholstery Without Replacing the Whole Piece',
                'short_description' => 'A sagging cushion or faded fabric does not always mean it is time for '
                    . 'a new sofa. A few smaller fixes go a long way.',
                'post_content'      => '<h2>Start with the cushions</h2>'
                    . '<p>Cushion foam compresses over years of use. Before replacing a whole sofa, check whether '
                    . 'the cushion inserts can be replaced on their own - most standard sizes are available '
                    . 'separately and this alone can make a piece feel new again.</p>'
                    . '<h2>Fabric fixes worth trying</h2>'
                    . '<ul>'
                    . '<li><strong>Steam and brush</strong> woven fabrics to lift flattened pile.</li>'
                    . '<li><strong>Spot-test a fabric refresher</strong> on an inconspicuous area before treating '
                    . 'the whole piece.</li>'
                    . '<li><strong>Rotate reversible cushions</strong> every few months so wear spreads evenly.</li>'
                    . '</ul>'
                    . '<p>If the fabric itself is beyond saving, reupholstering a single sofa is often cheaper '
                    . 'than it sounds, especially compared to replacing a well-built frame entirely.</p>',
                'author_id'         => $a['Marcus Vale'],
                'category_ids'      => [$c['Product Care']],
                'tag_ids'           => [$t['maintenance'], $t['diy']],
            ],
            'a-real-small-apartment-tour' => [
                'name'              => 'A Real 480-Square-Foot Apartment, Furnished on a Modest Budget',
                'short_description' => 'One customer walks us through the choices behind furnishing a genuinely '
                    . 'small apartment without it feeling cramped.',
                'post_content'      => '<h2>The brief: one room, several jobs</h2>'
                    . '<p>"My living room had to work as an office, a guest space, and somewhere to actually '
                    . 'relax," says the homeowner. "I could not dedicate furniture to just one purpose."</p>'
                    . '<h2>What made the biggest difference</h2>'
                    . '<ul>'
                    . '<li>A daybed instead of a sofa, so it could double as a guest bed.</li>'
                    . '<li>A drop-leaf side table that folds flat against the wall when not in use as a desk.</li>'
                    . '<li>Vertical shelving instead of a low bookcase, to use wall space instead of floor '
                    . 'space.</li>'
                    . '</ul>'
                    . '<p>The result is a room that changes function throughout the day without ever looking '
                    . 'like it is trying to do three jobs at once.</p>',
                'author_id'         => $a['Tobias Reyes'],
                'category_ids'      => [$c['Customer Stories']],
                'tag_ids'           => [$t['small-space'], $t['minimalist']],
            ],
            'accessorizing-without-clutter' => [
                'name'              => 'Accessorizing a Room Without Making It Feel Cluttered',
                'short_description' => 'There is a real difference between "styled" and "crowded." Here is where '
                    . 'that line usually sits.',
                'post_content'      => '<h2>The rule of odd numbers</h2>'
                    . '<p>Groups of three tend to look more intentional than groups of two or four - a shelf '
                    . 'styled with three objects of varying height reads as curated rather than random.</p>'
                    . '<h2>What to skip</h2>'
                    . '<ul>'
                    . '<li>Avoid lining objects up in a single row - stagger heights and depths instead.</li>'
                    . '<li>Leave at least one shelf or surface mostly empty so the eye has somewhere to rest.</li>'
                    . '<li>Repeat one material (wood, brass, ceramic) across a few pieces to tie a grouping '
                    . 'together.</li>'
                    . '</ul>'
                    . '<p>If a surface feels busy no matter what you try, the simplest fix is usually to remove '
                    . 'one item rather than add another.</p>',
                'author_id'         => $a['Elena Cross'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['minimalist'], $t['scandinavian']],
            ],
            'seasonal-bedroom-refresh' => [
                'name'              => 'A Simple Seasonal Refresh for the Bedroom',
                'short_description' => 'You do not need to redecorate every season - a few swaps make a bedroom '
                    . 'feel current without a full overhaul.',
                'post_content'      => '<h2>Three swaps, not a full redo</h2>'
                    . '<ul>'
                    . '<li><strong>Bedding weight.</strong> Swapping a heavy duvet insert for a lighter one (or '
                    . 'back again) matters more for comfort than changing the cover pattern.</li>'
                    . '<li><strong>One textile.</strong> A new throw or a pair of cushions changes the palette of '
                    . 'a room without touching the furniture at all.</li>'
                    . '<li><strong>Lighting temperature.</strong> Warmer bulbs in colder months, cooler-toned '
                    . 'light in summer, can shift how a room feels at the same time of day.</li>'
                    . '</ul>'
                    . '<p>Keep the furniture layout fixed and rotate these smaller elements instead - it is '
                    . 'cheaper, faster, and easier to undo if you change your mind.</p>',
                'author_id'         => $a['Priya Nandan'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['trending'], $t['scandinavian']],
                'topic_ids'         => [$p['Seasonal']],
            ],
            'why-joinery-matters' => [
                'name'              => 'Why Joinery Matters More Than the Wood Species',
                'short_description' => 'Two tables can use the same wood and last completely different amounts '
                    . 'of time. The difference is usually in the joints.',
                'post_content'      => '<h2>Glue and screws are not the whole story</h2>'
                    . '<p>A table held together mostly with glue and fasteners will loosen faster under daily '
                    . 'use than one built with interlocking joinery, where the wood pieces themselves share the '
                    . 'load.</p>'
                    . '<h2>What to look for</h2>'
                    . '<ul>'
                    . '<li><strong>Mortise and tenon</strong> joints at leg-to-frame connections resist wobble '
                    . 'far better than dowels alone.</li>'
                    . '<li><strong>Corner blocks</strong> reinforce chair and table frames at their weakest '
                    . 'points.</li>'
                    . '<li><strong>Drawer construction</strong> - dovetailed drawers outlast stapled ones by '
                    . 'years, especially with regular use.</li>'
                    . '</ul>'
                    . '<p>None of this is visible in a product photo, which is exactly why it is worth asking '
                    . 'about before buying a piece meant to last.</p>',
                'author_id'         => $a['Marcus Vale'],
                'category_ids'      => [$c['Product Care'], $c['Behind the Scenes']],
                'tag_ids'           => [$t['buying-guide'], $t['maintenance']],
            ],
            'reader-question-mixing-metals' => [
                'name'              => 'Reader Question: Is It Okay to Mix Metal Finishes in One Room?',
                'short_description' => 'A common styling worry, answered directly: yes, and here is how to do '
                    . 'it so it looks deliberate.',
                'post_content'      => '<h2>Short answer: yes, with a limit</h2>'
                    . '<p>Mixing brass, black iron, and brushed nickel in one room works - the failure mode is '
                    . 'usually using too many finishes at once, not the mixing itself.</p>'
                    . '<h2>A simple way to keep it controlled</h2>'
                    . '<ul>'
                    . '<li>Pick one dominant finish for the largest pieces (a light fixture, a bed frame).</li>'
                    . '<li>Use a second finish for mid-sized accents (drawer pulls, a lamp base).</li>'
                    . '<li>Limit a third finish to small details only (picture frames, a vase).</li>'
                    . '</ul>'
                    . '<p>Stopping at three finishes keeps a room feeling intentional instead of accidental.</p>',
                'author_id'         => $a['Tobias Reyes'],
                'category_ids'      => [$c['Design Tips']],
                'tag_ids'           => [$t['trending']],
            ],
        ];
    }
}