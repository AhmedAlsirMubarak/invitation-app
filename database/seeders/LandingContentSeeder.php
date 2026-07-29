<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use App\Models\LandingFeature;
use App\Models\LandingPackage;
use App\Models\LandingSetting;
use Illuminate\Database\Seeder;

class LandingContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->settings();
        $this->coreFeatures();
        $this->highlightFeatures();
        $this->howItWorks();
        $this->packages();
        $this->faqs();
    }

    private function setting(string $key, string $type, ?array $value = null, ?string $imagePath = null): void
    {
        LandingSetting::updateOrCreate(['key' => $key], [
            'type' => $type,
            'value' => $value,
            'image_path' => $imagePath,
        ]);
    }

    private function settings(): void
    {
        $this->setting('hero_title', 'text', [
            'ar' => 'دعوات إلكترونية ذكية لمناسباتكم',
            'en' => 'Smart digital invitations for your events',
        ]);
        $this->setting('hero_subtitle', 'textarea', [
            'ar' => 'أنشئ دعوتك، أرسلها عبر واتساب، وتابع تأكيد الحضور والدخول لحظيًا — كل ذلك من لوحة تحكم واحدة.',
            'en' => 'Create your invitation, send it over WhatsApp, and track RSVPs and check-ins live — all from one dashboard.',
        ]);
        $this->setting('hero_cta', 'text', [
            'ar' => 'ابدأ الآن',
            'en' => 'Get started',
        ]);
        $this->setting('trust_statement', 'text', [
            'ar' => 'نضمن وصول دعوتك لجميع ضيوفك',
            'en' => "We guarantee your invitation reaches every guest",
        ]);
        $this->setting('features_title', 'text', [
            'ar' => 'كل ما تحتاجه لإدارة مناسبتك',
            'en' => 'Everything you need to run your event',
        ]);
        $this->setting('showcase_title', 'text', [
            'ar' => 'تصاميم تليق بمناسبتك',
            'en' => 'Designs worthy of your event',
        ]);
        $this->setting('highlights_title', 'text', [
            'ar' => 'مزايا إضافية',
            'en' => 'Additional highlights',
        ]);
        $this->setting('how_title', 'text', [
            'ar' => 'كيف يعمل؟',
            'en' => 'How it works',
        ]);
        $this->setting('testimonials_title', 'text', [
            'ar' => 'تجارب عملائنا',
            'en' => 'What our clients say',
        ]);
        $this->setting('packages_title', 'text', [
            'ar' => 'باقاتنا',
            'en' => 'Our packages',
        ]);
        $this->setting('faq_title', 'text', [
            'ar' => 'الأسئلة الشائعة',
            'en' => 'Frequently asked questions',
        ]);
        $this->setting('contact_title', 'text', [
            'ar' => 'تواصل معنا',
            'en' => 'Get in touch',
        ]);
        $this->setting('contact_subtitle', 'textarea', [
            'ar' => 'لديك استفسار؟ فريقنا جاهز لمساعدتك.',
            'en' => 'Have a question? Our team is ready to help.',
        ]);
        $this->setting('contact_phone', 'text', ['ar' => '', 'en' => '']);
        $this->setting('footer_rights', 'text', [
            'ar' => 'جميع الحقوق محفوظة',
            'en' => 'All rights reserved',
        ]);
    }

    private function coreFeatures(): void
    {
        $items = [
            ['icon' => '💌', 'title' => ['ar' => 'دعوات إلكترونية أنيقة', 'en' => 'Elegant digital invitations'], 'body' => ['ar' => 'صمّم دعوتك مرة واحدة، بألوان وخطوط تناسب مناسبتك، وشاركها برابط لكل ضيف.', 'en' => 'Design your invitation once, in colors and fonts that fit your event, then share a personal link with every guest.']],
            ['icon' => '✅', 'title' => ['ar' => 'تأكيد الحضور والاعتذار', 'en' => 'Accept or decline RSVPs'], 'body' => ['ar' => 'يستطيع كل ضيف تأكيد الحضور أو الاعتذار مباشرة من صفحة الدعوة، وتظهر النتيجة فورًا في لوحتك.', 'en' => 'Every guest can confirm or decline right from their invitation page, and it shows up instantly on your dashboard.']],
            ['icon' => '📲', 'title' => ['ar' => 'إضافة الضيوف وإرسال واتساب', 'en' => 'Add guests & send on WhatsApp'], 'body' => ['ar' => 'أضف ضيوفك بسهولة (فردًا أو عبر ملف CSV)، وأرسل الدعوة لكل ضيف عبر واتساب برابط جاهز يتضمن موقع المناسبة.', 'en' => "Add guests one by one or import a CSV, then send each one their invite over WhatsApp with the venue location."]],
            ['icon' => '⏰', 'title' => ['ar' => 'تذكير قبل المناسبة', 'en' => 'Day-before reminders'], 'body' => ['ar' => 'قائمة يومية بالضيوف المؤكدين الذين موعد مناسبتهم غدًا، مع رابط واتساب جاهز لتذكيرهم بضغطة واحدة.', 'en' => 'A daily list of confirmed guests whose event is tomorrow, each with a one-click WhatsApp reminder ready to send.']],
            ['icon' => '🔒', 'title' => ['ar' => 'رمز QR دخول لمرة واحدة', 'en' => 'Single-use QR entry'], 'body' => ['ar' => 'كل ضيف يحصل على رمز QR خاص به، يُستخدم مرة واحدة فقط ولا يُقبل الدخول به مجددًا بعد استخدامه.', 'en' => "Every guest gets their own QR code. Once scanned for entry, it can't be used again."]],
            ['icon' => '📊', 'title' => ['ar' => 'متابعة الحضور لحظيًا', 'en' => 'Live attendance tracking'], 'body' => ['ar' => 'لوحة تحكم تعرض عدد المؤكدين والمعتذرين، وعدد الحاضرين داخل القاعة الآن، لحظة بلحظة.', 'en' => 'A dashboard showing confirmed, declined, and how many guests are inside the venue right now — updated live.']],
        ];

        foreach ($items as $i => $item) {
            LandingFeature::updateOrCreate(
                ['group' => 'core', 'sort_order' => $i],
                ['icon' => $item['icon'], 'title' => $item['title'], 'body' => $item['body'], 'is_active' => true]
            );
        }
    }

    private function highlightFeatures(): void
    {
        $items = [
            ['icon' => '👥', 'title' => ['ar' => 'منظمون متعددون', 'en' => 'Multiple organizers'], 'body' => ['ar' => 'أضف أي عدد من المنظمين، كل واحد بلوحة تحكم مستقلة لإدارة قائمة ضيوفه الخاصة.', 'en' => 'Add as many organizers as you need, each with their own dashboard to manage their own guest list.']],
            ['icon' => '🔁', 'title' => ['ar' => 'دعوات بديلة', 'en' => 'Free replacement invitations'], 'body' => ['ar' => 'عند اعتذار أحد الضيوف، أضف دعوة بديلة فورًا للاستفادة من المقعد الشاغر.', 'en' => 'When a guest declines, issue a free replacement invitation right away to fill the open seat.']],
            ['icon' => '💬', 'title' => ['ar' => 'إرسال عبر واتساب', 'en' => 'WhatsApp delivery'], 'body' => ['ar' => 'كل دعوة وتذكير يُرسلان برابط واتساب جاهز، مع تتبع حالة الإرسال لكل ضيف.', 'en' => 'Every invite and reminder goes out as a ready WhatsApp link, with delivery status tracked per guest.']],
        ];

        foreach ($items as $i => $item) {
            LandingFeature::updateOrCreate(
                ['group' => 'highlight', 'sort_order' => $i],
                ['icon' => $item['icon'], 'title' => $item['title'], 'body' => $item['body'], 'is_active' => true]
            );
        }
    }

    private function howItWorks(): void
    {
        $items = [
            ['title' => ['ar' => 'أنشئ المناسبة', 'en' => 'Create the event'], 'body' => ['ar' => 'حدد التاريخ والمكان واختر تصميم الدعوة من مكتبتك.', 'en' => 'Set the date and venue, and pick an invitation design from your library.']],
            ['title' => ['ar' => 'أضف الضيوف وأرسل', 'en' => 'Add guests & send'], 'body' => ['ar' => 'أضف قائمة الضيوف وأرسل لكل واحد دعوته عبر واتساب.', 'en' => 'Add your guest list and send each one their invitation over WhatsApp.']],
            ['title' => ['ar' => 'استقبل وتابع', 'en' => 'Welcome & track'], 'body' => ['ar' => 'امسح رموز QR عند الباب وتابع الحضور لحظيًا من لوحتك.', 'en' => 'Scan QR codes at the door and watch attendance update live on your dashboard.']],
        ];

        foreach ($items as $i => $item) {
            LandingFeature::updateOrCreate(
                ['group' => 'how_it_works', 'sort_order' => $i],
                ['icon' => null, 'title' => $item['title'], 'body' => $item['body'], 'is_active' => true]
            );
        }
    }

    private function packages(): void
    {
        $packages = [
            [
                'name' => ['ar' => 'الباقة الأساسية', 'en' => 'Basic package'],
                'price_label' => ['ar' => 'تواصل معنا للأسعار', 'en' => 'Contact us for pricing'],
                'description' => ['ar' => 'دعوة إلكترونية أنيقة بدون خدمة الإرسال أو تأكيد الحضور.', 'en' => 'An elegant digital invitation without sending or RSVP tracking.'],
                'features' => [
                    ['ar' => 'صفحة دعوة مخصصة', 'en' => 'Custom invitation page'],
                    ['ar' => 'رمز QR للدخول', 'en' => 'QR code entry'],
                    ['ar' => 'إدارة الضيوف', 'en' => 'Guest management'],
                ],
                'badge' => null,
                'cta_label' => ['ar' => 'ابدأ الآن', 'en' => 'Get started'],
            ],
            [
                'name' => ['ar' => 'الباقة المتكاملة', 'en' => 'Premium package'],
                'price_label' => ['ar' => 'تواصل معنا للأسعار', 'en' => 'Contact us for pricing'],
                'description' => ['ar' => 'كل شيء في الباقة الأساسية، إضافة إلى الإرسال والتأكيد والتذكيرات.', 'en' => 'Everything in Basic, plus WhatsApp sending, RSVP tracking, and reminders.'],
                'features' => [
                    ['ar' => 'كل مزايا الباقة الأساسية', 'en' => 'Everything in Basic'],
                    ['ar' => 'تأكيد الحضور والاعتذار', 'en' => 'RSVP accept/decline'],
                    ['ar' => 'إرسال الدعوات عبر واتساب', 'en' => 'WhatsApp invite sending'],
                    ['ar' => 'تذكير قبل المناسبة', 'en' => 'Day-before reminders'],
                ],
                'badge' => ['ar' => 'الأكثر طلبًا', 'en' => 'Most popular'],
                'cta_label' => ['ar' => 'ابدأ الآن', 'en' => 'Get started'],
            ],
        ];

        foreach ($packages as $i => $package) {
            LandingPackage::updateOrCreate(
                ['sort_order' => $i],
                array_merge($package, ['cta_url' => null, 'is_active' => true])
            );
        }
    }

    private function faqs(): void
    {
        $items = [
            ['q' => ['ar' => 'كيف أطلب دعوتي الإلكترونية؟', 'en' => 'How do I order my digital invitation?'], 'a' => ['ar' => 'تواصل معنا عبر واتساب أو من خلال لوحة التحكم بعد إنشاء حسابك، ثم أنشئ مناسبتك واختر التصميم المناسب.', 'en' => 'Contact us over WhatsApp or through your dashboard once your account is set up, then create your event and pick a design.']],
            ['q' => ['ar' => 'هل يمكن للضيف الدخول برمز QR أكثر من مرة؟', 'en' => 'Can a guest enter more than once with the same QR code?'], 'a' => ['ar' => 'لا. كل رمز QR يُستخدم لمرة واحدة فقط، ولا يُقبل الدخول به مجددًا بعد استخدامه.', 'en' => "No. Each QR code is single-use — it won't be accepted again after check-in."]],
            ['q' => ['ar' => 'هل أستطيع إضافة أكثر من منظم لنفس المناسبة؟', 'en' => 'Can I add more than one organizer to the same event?'], 'a' => ['ar' => 'نعم، يمكن إضافة عدد غير محدود من المنظمين، لكل منهم صلاحيات مستقلة.', 'en' => 'Yes, you can add an unlimited number of organizers, each with independent access.']],
            ['q' => ['ar' => 'كيف تُرسل الدعوات للضيوف؟', 'en' => 'How are invitations sent to guests?'], 'a' => ['ar' => 'تُرسل الدعوة عبر رابط واتساب جاهز لكل ضيف، يتضمن رابط الدعوة وموقع المناسبة.', 'en' => "Each guest gets a ready WhatsApp link containing their invitation link and the event's location."]],
            ['q' => ['ar' => 'هل يمكنني متابعة عدد الحاضرين أثناء المناسبة؟', 'en' => 'Can I track attendance live during the event?'], 'a' => ['ar' => 'نعم، تعرض لوحة التحكم عدد الحاضرين داخل القاعة لحظة بلحظة أثناء تسجيل الدخول عبر الماسح.', 'en' => 'Yes, your dashboard shows how many guests are inside the venue in real time as they check in via the scanner.']],
        ];

        foreach ($items as $i => $item) {
            LandingFaq::updateOrCreate(
                ['sort_order' => $i],
                ['question' => $item['q'], 'answer' => $item['a'], 'is_active' => true]
            );
        }
    }
}
