<?php

namespace Database\Seeders;

use App\Models\IndustriesPage;
use App\Models\Partner;
use App\Models\PartnersPage;
use App\Models\Project;
use App\Models\Sector;
use App\Models\SolutionPage;
use App\Models\UseCasesPage;
use Illuminate\Database\Seeder;

class PagesSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSolutionPages();
        $this->seedUseCasesPage();
        $this->seedPartnersPage();
        $this->seedIndustriesPage();
    }

    protected function seedSolutionPages(): void
    {
        // 1. Digital Solutions (/digital)
        SolutionPage::updateOrCreate(
            ['slug' => 'digital'],
            [
                'title' => [
                    'en' => 'Digital Solutions',
                    'ar' => 'الحلول الرقمية',
                ],
                'subtitle' => [
                    'en' => 'Smarter, connected and data-driven solutions for a more efficient industrial future.',
                    'ar' => 'حلول أكثر ذكاءً وترابطاً ومبنية على البيانات لمستقبل صناعي أكثر كفاءة.',
                ],
                'hero_image' => 'images/digital-hero.webp',
                'badges' => [
                    'en' => [
                        ['icon' => 'factory', 'title' => 'Manufacturing Intelligence', 'description' => 'Turn your data into value'],
                        ['icon' => 'settings', 'title' => 'Operational Efficiency', 'description' => 'Smarter decisions, higher performance'],
                        ['icon' => 'public', 'title' => 'Global Connected Enterprise', 'description' => 'From factory to cloud'],
                    ],
                    'ar' => [
                        ['icon' => 'factory', 'title' => 'الذكاء التصنيعي', 'description' => 'تحويل بياناتك إلى قيمة مضافة'],
                        ['icon' => 'settings', 'title' => 'الكفاءة التشغيلية', 'description' => 'قرارات أذكى وأداء أعلى'],
                        ['icon' => 'public', 'title' => 'المؤسسة المتصلة عالمياً', 'description' => 'من أرض المصنع إلى السحابة'],
                    ],
                ],
                'services' => [
                    'en' => [
                        ['icon' => 'factory', 'title' => 'MES', 'description' => 'Manufacturing Execution Systems for smart production.', 'link' => '/services/mes'],
                        ['icon' => 'build', 'title' => 'CMMS', 'description' => 'Computerized Maintenance Management Systems.', 'link' => '/services/cmms'],
                        ['icon' => 'business', 'title' => 'Enterprise Management', 'description' => 'Integrated business management solutions.', 'link' => '/services/enterprise-management'],
                        ['icon' => 'hub', 'title' => 'IIoT Platforms', 'description' => 'Industrial Internet of Things for real-time connectivity.', 'link' => '/services/iiot-platforms'],
                        ['icon' => 'cloud', 'title' => 'Cloud Integration', 'description' => 'Seamless cloud-based solutions for your business.', 'link' => '/services/cloud-integration'],
                        ['icon' => 'bar_chart', 'title' => 'Data Analytics', 'description' => 'Turn data into actionable insights.', 'link' => '/services/data-analytics'],
                        ['icon' => 'dashboard', 'title' => 'Dashboard & Reporting', 'description' => 'Custom dashboards for better decision making.', 'link' => '/services/dashboard-reporting'],
                        ['icon' => 'storage', 'title' => 'Historian Systems', 'description' => 'Long-term data storage and analysis.', 'link' => '/services/historian-systems'],
                        ['icon' => 'memory', 'title' => 'Edge Computing', 'description' => 'Real-time processing at the edge.', 'link' => '/services/edge-computing'],
                        ['icon' => 'smartphone', 'title' => 'Mobile Applications', 'description' => 'Access and monitor operations anywhere.', 'link' => '/services/mobile-applications'],
                    ],
                    'ar' => [
                        ['icon' => 'factory', 'title' => 'MES', 'description' => 'أنظمة تنفيذ التصنيع للإنتاج الذكي.', 'link' => '/services/mes'],
                        ['icon' => 'build', 'title' => 'CMMS', 'description' => 'أنظمة إدارة الصيانة المحوسبة.', 'link' => '/services/cmms'],
                        ['icon' => 'business', 'title' => 'إدارة المؤسسات', 'description' => 'حلول إدارة الأعمال المتكاملة.', 'link' => '/services/enterprise-management'],
                        ['icon' => 'hub', 'title' => 'منصات IIoT', 'description' => 'إنترنت الأشياء الصناعي للاتصال اللحظي.', 'link' => '/services/iiot-platforms'],
                        ['icon' => 'cloud', 'title' => 'التكامل السحابي', 'description' => 'حلول سحابية سلسة لأعمالك.', 'link' => '/services/cloud-integration'],
                        ['icon' => 'bar_chart', 'title' => 'تحليلات البيانات', 'description' => 'تحويل البيانات إلى رؤى قابلة للتنفيذ.', 'link' => '/services/data-analytics'],
                        ['icon' => 'dashboard', 'title' => 'لوحات المعلومات والتقارير', 'description' => 'لوحات تحكم مخصصة لاتخاذ قرارات أفضل.', 'link' => '/services/dashboard-reporting'],
                        ['icon' => 'storage', 'title' => 'أنظمة Historian', 'description' => 'تخزين وتحليل البيانات على المدى الطويل.', 'link' => '/services/historian-systems'],
                        ['icon' => 'memory', 'title' => 'الحوسبة الطرفية (Edge)', 'description' => 'معالجة البيانات في الوقت الحقيقي عند الأطراف.', 'link' => '/services/edge-computing'],
                        ['icon' => 'smartphone', 'title' => 'تطبيقات الهاتف', 'description' => 'الوصول إلى العمليات ومراقبتها في أي مكان.', 'link' => '/services/mobile-applications'],
                    ],
                ],
                'approach_title' => [
                    'en' => 'DIGITAL TRANSFORMATION APPROACH',
                    'ar' => 'منهجية التحول الرقمي',
                ],
                'approach_steps' => [
                    'en' => [
                        ['step' => '01', 'title' => 'Discover & Analyze', 'icon' => 'search'],
                        ['step' => '02', 'title' => 'Design & Plan', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'Integrate & Develop', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'Deploy & Test', 'icon' => 'cloud_upload'],
                        ['step' => '05', 'title' => 'Operate & Optimize', 'icon' => 'trending_up'],
                    ],
                    'ar' => [
                        ['step' => '01', 'title' => 'الاستكشاف والتحليل', 'icon' => 'search'],
                        ['step' => '02', 'title' => 'التصميم والتخطيط', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'التكامل والتطوير', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'النشر والاختبار', 'icon' => 'cloud_upload'],
                        ['step' => '05', 'title' => 'التشغيل والتحسين', 'icon' => 'trending_up'],
                    ],
                ],
                'why_us_title' => [
                    'en' => 'WHY SIS?',
                    'ar' => 'لماذا تختار SIS؟',
                ],
                'why_us_points' => [
                    'en' => [
                        ['text' => 'Proven Track Record', 'icon' => 'verified'],
                        ['text' => 'Multi-Vendor Expertise', 'icon' => 'groups'],
                        ['text' => 'Scalable Solutions', 'icon' => 'trending_up'],
                        ['text' => 'Data & Security Focus', 'icon' => 'security'],
                        ['text' => 'Future-Ready Technologies', 'icon' => 'language'],
                    ],
                    'ar' => [
                        ['text' => 'سجل حافل بالنجاحات', 'icon' => 'verified'],
                        ['text' => 'خبرة واسعة مع مختلف المصنعين', 'icon' => 'groups'],
                        ['text' => 'حلول قابلة للتوسع والنمو', 'icon' => 'trending_up'],
                        ['text' => 'تركيز عميق على أمن وسرية البيانات', 'icon' => 'security'],
                        ['text' => 'تقنيات جاهزة لمتطلبات المستقبل', 'icon' => 'language'],
                    ],
                ],
                'industries_title' => [
                    'en' => 'INDUSTRIES WE SERVE',
                    'ar' => 'الصناعات والقطاعات التي نخدمها',
                ],
                'industries' => [
                    'en' => [
                        ['name' => 'Oil & Gas', 'icon' => 'factory', 'slug' => 'oil-gas'],
                        ['name' => 'Chemical', 'icon' => 'science', 'slug' => 'chemical'],
                        ['name' => 'Power & Energy', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'Water & Wastewater', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'Pharmaceutical', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                    'ar' => [
                        ['name' => 'النفط والغاز', 'icon' => 'factory', 'slug' => 'oil-gas'],
                        ['name' => 'الصناعات الكيميائية', 'icon' => 'science', 'slug' => 'chemical'],
                        ['name' => 'الطاقة والكهرباء', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'المياه ومعالجة مياه الصرف', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'الصناعات الدوائية', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                ],
                'cta_headline' => [
                    'en' => 'Ready to Build a Smarter, Connected Operation?',
                    'ar' => 'هل أنت جاهز لبناء عمليات تشغيلية أكثر ذكاءً وترابطاً؟',
                ],
                'cta_subheadline' => [
                    'en' => "Let's unlock the power of data for your business.",
                    'ar' => 'دعنا نطلق العنان لقوة البيانات لتطوير أعمالك.',
                ],
                'cta_button_text' => [
                    'en' => 'Explore Our Digital Solutions',
                    'ar' => 'استكشف حلولنا الرقمية',
                ],
                'cta_button_link' => '/contact',
                'is_active' => true,
            ]
        );

        // 2. Industrial AI (/ai)
        SolutionPage::updateOrCreate(
            ['slug' => 'ai'],
            [
                'title' => [
                    'en' => 'Industrial AI',
                    'ar' => 'الذكاء الاصطناعي الصناعي',
                ],
                'subtitle' => [
                    'en' => 'Intelligent AI-powered solutions that turn data into actionable insights, optimize operations, and drive smarter decisions.',
                    'ar' => 'حلول مدعومة بالذكاء الاصطناعي تحول البيانات إلى رؤى تنفيذية، وتحسن العمليات التشغيلية، وتقود قرارات أكثر ذكاءً.',
                ],
                'hero_image' => 'images/ai-hero.webp',
                'badges' => [
                    'en' => [
                        ['icon' => 'psychology', 'title' => 'AI Powered', 'description' => 'Advanced AI models built for industrial environments.'],
                        ['icon' => 'trending_up', 'title' => 'Real-Time Insights', 'description' => 'Actionable insights in real-time to improve efficiency.'],
                        ['icon' => 'security', 'title' => 'Secure & Reliable', 'description' => 'Enterprise-grade security and industrial-grade reliability.'],
                        ['icon' => 'memory', 'title' => 'Scalable Solutions', 'description' => 'Scalable from edge devices to cloud platforms.'],
                    ],
                    'ar' => [
                        ['icon' => 'psychology', 'title' => 'مدعوم بالذكاء الاصطناعي', 'description' => 'نماذج ذكاء اصطناعي متقدمة مخصصة للبيئات الصناعية.'],
                        ['icon' => 'trending_up', 'title' => 'رؤى فورية ولحظية', 'description' => 'تحليلات مباشرة لتحسين الكفاءة في الوقت الفعلي.'],
                        ['icon' => 'security', 'title' => 'آمن وموثوق', 'description' => 'أمان على مستوى المؤسسات وموثوقية صناعية عالية.'],
                        ['icon' => 'memory', 'title' => 'حلول قابلة للتوسع', 'description' => 'مرونة من الأجهزة الطرفية إلى المنصات السحابية.'],
                    ],
                ],
                'services' => [
                    'en' => [
                        ['icon' => 'query_stats', 'title' => 'Predictive Maintenance', 'description' => 'Predict equipment failures before they happen and reduce downtime.', 'link' => '/services/predictive-maintenance'],
                        ['icon' => 'device_hub', 'title' => 'MCB Connections', 'description' => 'Intelligent monitoring and analysis of MCBs for safe and efficient operations.', 'link' => '/services/mcb-connections'],
                        ['icon' => 'visibility', 'title' => 'Computer Vision', 'description' => 'Vision-based inspection solutions for quality control and automation.', 'link' => '/services/computer-vision'],
                        ['icon' => 'troubleshoot', 'title' => 'AI Inspection', 'description' => 'AI-powered defect detection to ensure higher quality and consistency.', 'link' => '/services/ai-inspection'],
                        ['icon' => 'insights', 'title' => 'AI Analytics', 'description' => 'Advanced analytics to uncover patterns and improve performance.', 'link' => '/services/ai-analytics'],
                        ['icon' => 'smart_toy', 'title' => 'Industrial Copilot', 'description' => 'AI copilots that assist operators and engineers in real-time.', 'link' => '/services/industrial-copilot'],
                        ['icon' => 'hub', 'title' => 'Machine Learning Models', 'description' => 'Custom ML models tailored for your industrial applications.', 'link' => '/services/machine-learning-models'],
                        ['icon' => 'memory', 'title' => 'Edge AI', 'description' => 'Run AI at the edge for faster decisions and reduced latency.', 'link' => '/services/edge-ai'],
                        ['icon' => 'crisis_alert', 'title' => 'Anomaly Detection', 'description' => 'Detect anomalies in real-time to prevent failures and ensure safety.', 'link' => '/services/anomaly-detection'],
                        ['icon' => 'dashboard', 'title' => 'AI Dashboards', 'description' => 'Interactive dashboards for AI insights and KPI monitoring.', 'link' => '/services/ai-dashboards'],
                    ],
                    'ar' => [
                        ['icon' => 'query_stats', 'title' => 'الصيانة التنبؤية', 'description' => 'التنبؤ بأعطال المعدات قبل وقوعها وتقليل فترات التوقف.', 'link' => '/services/predictive-maintenance'],
                        ['icon' => 'device_hub', 'title' => 'اتصالات MCB', 'description' => 'مراقبة وتحليل ذكي للقواطع الكهربائية لتشغيل آمن وفعال.', 'link' => '/services/mcb-connections'],
                        ['icon' => 'visibility', 'title' => 'الرؤية الحاسوبية', 'description' => 'حلول فحص بصرية لضبط الجودة والأتمتة.', 'link' => '/services/computer-vision'],
                        ['icon' => 'troubleshoot', 'title' => 'الفحص بالذكاء الاصطناعي', 'description' => 'كشف العيوب والمشكلات لضمان أعلى جودة واتساق.', 'link' => '/services/ai-inspection'],
                        ['icon' => 'insights', 'title' => 'تحليلات الذكاء الاصطناعي', 'description' => 'تحليلات متطورة لاكتشاف الأنماط وتحسين الأداء العام.', 'link' => '/services/ai-analytics'],
                        ['icon' => 'smart_toy', 'title' => 'المساعد الصناعي الذكي (Copilot)', 'description' => 'مساعد رقمي ذكي يدعم المشغلين والمهندسين لحظياً.', 'link' => '/services/industrial-copilot'],
                        ['icon' => 'hub', 'title' => 'نماذج تعلم الآلة (ML)', 'description' => 'نماذج مخصصة مصممة خصيصاً لتطبيقاتك الصناعية.', 'link' => '/services/machine-learning-models'],
                        ['icon' => 'memory', 'title' => 'الذكاء الاصطناعي الطرفي (Edge AI)', 'description' => 'تشغيل الذكاء الاصطناعي طرفياً لسرعة الاستجابة وتقليل التأخير.', 'link' => '/services/edge-ai'],
                        ['icon' => 'crisis_alert', 'title' => 'اكتشاف الأنماط غير الطبيعية', 'description' => 'كشف الانحرافات اللحظية لمنع الأعطال وضمان السلامة.', 'link' => '/services/anomaly-detection'],
                        ['icon' => 'dashboard', 'title' => 'لوحات معلومات AI', 'description' => 'لوحات تفاعلية لعرض مؤشرات الأداء والرؤى الذكية.', 'link' => '/services/ai-dashboards'],
                    ],
                ],
                'approach_title' => [
                    'en' => 'OUR AI APPROACH',
                    'ar' => 'منهجيتنا في الذكاء الاصطناعي',
                ],
                'approach_steps' => [
                    'en' => [
                        ['step' => '01', 'title' => 'Data Collection', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'Data Processing', 'icon' => 'storage'],
                        ['step' => '03', 'title' => 'Model Development', 'icon' => 'hub'],
                        ['step' => '04', 'title' => 'Deployment', 'icon' => 'verified_user'],
                        ['step' => '05', 'title' => 'Monitoring & Optimization', 'icon' => 'bar_chart'],
                    ],
                    'ar' => [
                        ['step' => '01', 'title' => 'جمع البيانات', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'معالجة وتنقية البيانات', 'icon' => 'storage'],
                        ['step' => '03', 'title' => 'تطوير وتدريب النماذج', 'icon' => 'hub'],
                        ['step' => '04', 'title' => 'النشر والتشغيل', 'icon' => 'verified_user'],
                        ['step' => '05', 'title' => 'المراقبة والتحسين المستمر', 'icon' => 'bar_chart'],
                    ],
                ],
                'why_us_title' => [
                    'en' => 'WHY CHOOSE SIS?',
                    'ar' => 'لماذا تختار SIS؟',
                ],
                'why_us_points' => [
                    'en' => [
                        ['text' => 'Industry-specific AI expertise', 'icon' => 'check_circle'],
                        ['text' => 'Proven AI solutions with measurable ROI', 'icon' => 'check_circle'],
                        ['text' => 'Edge to cloud AI capabilities', 'icon' => 'check_circle'],
                        ['text' => 'Secure, scalable & future-ready solutions', 'icon' => 'check_circle'],
                        ['text' => '24/7 support and continuous improvement', 'icon' => 'check_circle'],
                    ],
                    'ar' => [
                        ['text' => 'خبرة ذكاء اصطناعي متخصصة في المجال الصناعي', 'icon' => 'check_circle'],
                        ['text' => 'حلول مثبتة تحقق عائداً استثمارياً قابلاً للقياس (ROI)', 'icon' => 'check_circle'],
                        ['text' => 'قدرات متكاملة من الأجهزة الطرفية حتى السحابة', 'icon' => 'check_circle'],
                        ['text' => 'حلول آمنة، قابلة للتطوير وجاهزة للمستقبل', 'icon' => 'check_circle'],
                        ['text' => 'دعم فني على مدار الساعة وتحسين مستمر', 'icon' => 'check_circle'],
                    ],
                ],
                'industries_title' => [
                    'en' => 'INDUSTRIES WE SERVE',
                    'ar' => 'الصناعات التي نخدمها',
                ],
                'industries' => [
                    'en' => [
                        ['name' => 'Manufacturing', 'icon' => 'factory', 'slug' => 'manufacturing'],
                        ['name' => 'Oil & Gas', 'icon' => 'local_gas_station', 'slug' => 'oil-gas'],
                        ['name' => 'Power & Energy', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'Water & Wastewater', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'Pharmaceutical', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                    'ar' => [
                        ['name' => 'التصنيع والإنتاج', 'icon' => 'factory', 'slug' => 'manufacturing'],
                        ['name' => 'النفط والغاز', 'icon' => 'local_gas_station', 'slug' => 'oil-gas'],
                        ['name' => 'الطاقة والكهرباء', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'المياه ومعالجة مياه الصرف', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'الصناعات الدوائية', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                ],
                'cta_headline' => [
                    'en' => 'Ready to unlock the power of Industrial AI?',
                    'ar' => 'هل أنت مستعد للاستفادة من قوة الذكاء الاصطناعي الصناعي؟',
                ],
                'cta_subheadline' => [
                    'en' => "Let's build intelligent solutions that drive real results.",
                    'ar' => 'دعنا نبني حلولاً ذكية تحقق نتائج حقيقية وملموسة.',
                ],
                'cta_button_text' => [
                    'en' => 'Talk to Our AI Experts',
                    'ar' => 'تحدث مع خبراء الذكاء الاصطناعي لدينا',
                ],
                'cta_button_link' => '/contact',
                'is_active' => true,
            ]
        );

        // 3. Process Automation (/process)
        SolutionPage::updateOrCreate(
            ['slug' => 'process'],
            [
                'title' => [
                    'en' => 'Process Automation',
                    'ar' => 'أتمتة العمليات الصناعية',
                ],
                'subtitle' => [
                    'en' => 'Intelligent process control and instrumentation solutions that ensure efficiency, accuracy and safety across your operations.',
                    'ar' => 'حلول ذكية للتحكم في العمليات وأجهزة القياس تضمن الكفاءة والدقة والسلامة عبر جميع عملياتك.',
                ],
                'hero_image' => 'images/process-hero.webp',
                'badges' => [
                    'en' => [
                        ['icon' => 'track_changes', 'title' => 'Precise Control', 'description' => 'Advanced control strategies for stable and optimized processes.'],
                        ['icon' => 'verified_user', 'title' => 'Safety First', 'description' => 'SIS and safety solutions to protect people, assets and the environment.'],
                        ['icon' => 'trending_up', 'title' => 'Operational Excellence', 'description' => 'Improve reliability, reduce costs and maximize production efficiency.'],
                    ],
                    'ar' => [
                        ['icon' => 'track_changes', 'title' => 'تحكم دقيق', 'description' => 'استراتيجيات تحكم متقدمة لعمليات مستقرة ومحسنة.'],
                        ['icon' => 'verified_user', 'title' => 'السلامة أولاً', 'description' => 'حلول وأنظمة أمان لحماية الأفراد والأصول والبيئة.'],
                        ['icon' => 'trending_up', 'title' => 'التميز التشغيلي', 'description' => 'تعزيز الموثوقية، خفض التكاليف وتعظيم كفاءة الإنتاج.'],
                    ],
                ],
                'services' => [
                    'en' => [
                        ['icon' => 'dns', 'title' => 'DCS', 'description' => 'Distributed Control System solutions for complex industrial processes.', 'link' => '/services/dcs'],
                        ['icon' => 'speed', 'title' => 'Instrumentation', 'description' => 'Design, selection and integration of field instruments.', 'link' => '/services/instrumentation'],
                        ['icon' => 'tune', 'title' => 'Process Control', 'description' => 'Advanced control strategies including PID, APC and MPC solutions.', 'link' => '/services/process-control'],
                        ['icon' => 'propane_tank', 'title' => 'Batch Control', 'description' => 'Reliable batch process management with recipes, sequences and reporting.', 'link' => '/services/batch-control'],
                        ['icon' => 'valve', 'title' => 'Flow Measurement', 'description' => 'Accurate flow measurement solutions for liquids, gases and steam.', 'link' => '/services/flow-measurement'],
                        ['icon' => 'speed', 'title' => 'Pressure Measurement', 'description' => 'High-accuracy pressure measurement for all applications.', 'link' => '/services/pressure-measurement'],
                        ['icon' => 'thermostat', 'title' => 'Temperature Measurement', 'description' => 'Temperature measurement solutions for precise process monitoring.', 'link' => '/services/temperature-measurement'],
                        ['icon' => 'water', 'title' => 'Level Measurement', 'description' => 'Continuous and point level measurement for tanks and vessels.', 'link' => '/services/level-measurement'],
                        ['icon' => 'science', 'title' => 'Analyzer Systems', 'description' => 'Gas, liquid and process analyzers for quality and compliance.', 'link' => '/services/analyzer-systems'],
                        ['icon' => 'security', 'title' => 'Safety Instrumented Systems (SIS)', 'description' => 'SIS and ESD solutions to ensure functional safety and risk reduction.', 'link' => '/services/safety-instrumented-systems'],
                    ],
                    'ar' => [
                        ['icon' => 'dns', 'title' => 'أنظمة التحكم الموزع (DCS)', 'description' => 'حلول DCS المتقدمة للعمليات الصناعية الكبرى والمعقدة.', 'link' => '/services/dcs'],
                        ['icon' => 'speed', 'title' => 'أجهزة القياس والتحكم (Instrumentation)', 'description' => 'تصميم واختيار وتكامل أجهزة القياس الحقلية.', 'link' => '/services/instrumentation'],
                        ['icon' => 'tune', 'title' => 'التحكم في العمليات', 'description' => 'استراتيجيات تحكم متقدمة تشمل حلول PID و APC و MPC.', 'link' => '/services/process-control'],
                        ['icon' => 'propane_tank', 'title' => 'التحكم في الدفعات (Batch Control)', 'description' => 'إدارة موثوقة للعمليات غير المستمرة مع الوصفات والتقارير.', 'link' => '/services/batch-control'],
                        ['icon' => 'valve', 'title' => 'قياس التدفق (Flow Measurement)', 'description' => 'حلول دقيقة لقياس تدفق السوائل والغازات والبخار.', 'link' => '/services/flow-measurement'],
                        ['icon' => 'speed', 'title' => 'قياس الضغط (Pressure Measurement)', 'description' => 'أجهزة قياس الضغط عالية الدقة لجميع التطبيقات.', 'link' => '/services/pressure-measurement'],
                        ['icon' => 'thermostat', 'title' => 'قياس درجات الحرارة', 'description' => 'حلول قياس درجات الحرارة للمراقبة الدقيقة.', 'link' => '/services/temperature-measurement'],
                        ['icon' => 'water', 'title' => 'قياس المنسوب (Level Measurement)', 'description' => 'قياس منسوب السوائل المستمر والنقطي للخزانات والأوعية.', 'link' => '/services/level-measurement'],
                        ['icon' => 'science', 'title' => 'أنظمة التحليل الكيميائي والغازي', 'description' => 'محللات الغاز والسوائل لضمان الجودة والامتثال.', 'link' => '/services/analyzer-systems'],
                        ['icon' => 'security', 'title' => 'أنظمة السلامة المجهزة (SIS)', 'description' => 'حلول SIS و ESD لضمان السلامة الوظيفية والحد من المخاطر.', 'link' => '/services/safety-instrumented-systems'],
                    ],
                ],
                'approach_title' => [
                    'en' => 'OUR PROCESS AUTOMATION APPROACH',
                    'ar' => 'منهجية أتمتة العمليات لدينا',
                ],
                'approach_steps' => [
                    'en' => [
                        ['step' => '01', 'title' => 'Requirement Analysis', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'Process Design', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'Engineering & Configuration', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'FAT / SAT', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'Commissioning & Startup', 'icon' => 'rocket_launch'],
                        ['step' => '06', 'title' => 'Support & Optimization', 'icon' => 'support_agent'],
                    ],
                    'ar' => [
                        ['step' => '01', 'title' => 'تحليل المتطلبات', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'تصميم العمليات', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'الهندسة والتهيئة الفنية', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'اختبارات FAT و SAT', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'التشغيل وبدء الإنتاج', 'icon' => 'rocket_launch'],
                        ['step' => '06', 'title' => 'الدعم والتحسين المستمر', 'icon' => 'support_agent'],
                    ],
                ],
                'why_us_title' => [
                    'en' => 'WHY CHOOSE SIS?',
                    'ar' => 'لماذا تختار SIS؟',
                ],
                'why_us_points' => [
                    'en' => [
                        ['text' => 'Multi-vendor expertise', 'icon' => 'check_circle'],
                        ['text' => 'Experienced process automation engineers', 'icon' => 'check_circle'],
                        ['text' => 'Compliance with international standards', 'icon' => 'check_circle'],
                        ['text' => 'Proven solutions across industries', 'icon' => 'check_circle'],
                        ['text' => '24/7 support and lifecycle services', 'icon' => 'check_circle'],
                    ],
                    'ar' => [
                        ['text' => 'خبرة واسعة مع مختلف الأنظمة والمصنعين', 'icon' => 'check_circle'],
                        ['text' => 'مهندسو أتمتة عمليات ذوو خبرة طويلة معتمدة', 'icon' => 'check_circle'],
                        ['text' => 'الامتثال الكامل للمعايير القياسية العالمية', 'icon' => 'check_circle'],
                        ['text' => 'حلول مجربة وناجحة في كبرى الصناعات', 'icon' => 'check_circle'],
                        ['text' => 'دعم فني متواصل وخدمات دورة حياة المشروع كاملة', 'icon' => 'check_circle'],
                    ],
                ],
                'industries_title' => [
                    'en' => 'INDUSTRIES WE SERVE',
                    'ar' => 'الصناعات التي نخدمها',
                ],
                'industries' => [
                    'en' => [
                        ['name' => 'Oil & Gas', 'icon' => 'science', 'slug' => 'oil-gas'],
                        ['name' => 'Chemical', 'icon' => 'propane_tank', 'slug' => 'chemical'],
                        ['name' => 'Power', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'Water & Wastewater', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'Pharmaceutical', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                    'ar' => [
                        ['name' => 'النفط والغاز', 'icon' => 'science', 'slug' => 'oil-gas'],
                        ['name' => 'الصناعات الكيميائية', 'icon' => 'propane_tank', 'slug' => 'chemical'],
                        ['name' => 'الطاقة', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'المياه والصرف', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'الأدوية', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                ],
                'cta_headline' => [
                    'en' => 'Ready to automate your processes?',
                    'ar' => 'هل أنت مستعد لأتمتة عملياتك الصناعية؟',
                ],
                'cta_subheadline' => [
                    'en' => "Let's design intelligent and safe automation solutions for your operations.",
                    'ar' => 'دعنا نصمم حلول أتمتة ذكية وآمنة لعملياتك التشغيلية.',
                ],
                'cta_button_text' => [
                    'en' => 'Talk to Our Experts',
                    'ar' => 'تحدث مع خبرائنا',
                ],
                'cta_button_link' => '/contact',
                'is_active' => true,
            ]
        );

        // 4. Industrial Automation (/industrial)
        SolutionPage::updateOrCreate(
            ['slug' => 'industrial'],
            [
                'title' => [
                    'en' => 'Industrial Automation',
                    'ar' => 'الأتمتة الصناعية',
                ],
                'subtitle' => [
                    'en' => 'Advanced automation solutions for machines, processes and production lines to improve efficiency, quality and safety.',
                    'ar' => 'حلول أتمتة متطورة للآلات والعمليات وخطوط الإنتاج لرفع الكفاءة والجودة والأمان.',
                ],
                'hero_image' => 'images/industrial-hero.webp',
                'badges' => [
                    'en' => [
                        ['icon' => 'smart_toy', 'title' => 'Smart Automation', 'description' => 'Intelligent control solutions for modern industries.'],
                        ['icon' => 'trending_up', 'title' => 'High Performance', 'description' => 'Reliable and scalable systems for maximum productivity.'],
                        ['icon' => 'security', 'title' => 'Safe & Reliable', 'description' => 'Built-in safety, diagnostics and system redundancy.'],
                    ],
                    'ar' => [
                        ['icon' => 'smart_toy', 'title' => 'أتمتة ذكية', 'description' => 'حلول تحكم ذكية للصناعات الحديثة.'],
                        ['icon' => 'trending_up', 'title' => 'أداء فائق', 'description' => 'أنظمة موثوقة وقابلة للتطوير لأقصى إنتاجية.'],
                        ['icon' => 'security', 'title' => 'آمن وموثوق', 'description' => 'أمان مدمج، تشخيص أعطال متقدم وازدواجية الأنظمة.'],
                    ],
                ],
                'services' => [
                    'en' => [
                        ['icon' => 'code', 'title' => 'PLC Programming', 'description' => 'Efficient PLC programming for various platforms and applications.', 'link' => '/services/plc-programming'],
                        ['icon' => 'monitor', 'title' => 'SCADA Development', 'description' => 'Custom SCADA systems for real-time monitoring and control.', 'link' => '/services/scada-development'],
                        ['icon' => 'touch_app', 'title' => 'HMI Development', 'description' => 'User-friendly HMI solutions for seamless operator interaction.', 'link' => '/services/hmi-development'],
                        ['icon' => 'precision_manufacturing', 'title' => 'Motion Control', 'description' => 'Precise motion control solutions for advanced automation needs.', 'link' => '/services/motion-control'],
                        ['icon' => 'hub', 'title' => 'Industrial Networking', 'description' => 'Reliable industrial network infrastructure for secure communication.', 'link' => '/services/industrial-networking'],
                        ['icon' => 'router', 'title' => 'Remote I/O Systems', 'description' => 'Flexible and scalable remote I/O solutions for all field devices.', 'link' => '/services/remote-io-systems'],
                        ['icon' => 'settings_input_component', 'title' => 'Drive Systems (VFD & Servo)', 'description' => 'Integrated drive solutions for speed, torque and position control.', 'link' => '/services/drive-systems'],
                        ['icon' => 'electrical_services', 'title' => 'Control Panels', 'description' => 'Design and build of high-quality control panels tailored to your needs.', 'link' => '/services/control-panels'],
                        ['icon' => 'smart_toy', 'title' => 'Machine Automation', 'description' => 'Automation solutions to improve machine efficiency and reduce downtime.', 'link' => '/services/machine-automation'],
                        ['icon' => 'visibility', 'title' => 'Process Visualization', 'description' => 'Real-time visualization for better decision-making and process transparency.', 'link' => '/services/process-visualization'],
                    ],
                    'ar' => [
                        ['icon' => 'code', 'title' => 'برمجة الـ PLC', 'description' => 'برمجة فعالة لأنظمة PLC عبر مختلف المنصات والتطبيقات.', 'link' => '/services/plc-programming'],
                        ['icon' => 'monitor', 'title' => 'تطوير أنظمة SCADA', 'description' => 'أنظمة SCADA مخصصة للمراقبة والتحكم في الوقت الحقيقي.', 'link' => '/services/scada-development'],
                        ['icon' => 'touch_app', 'title' => 'تطوير واجهات المستخدم HMI', 'description' => 'واجهات HMI سهلة الاستخدام لتفاعل سلس للمشغلين.', 'link' => '/services/hmi-development'],
                        ['icon' => 'precision_manufacturing', 'title' => 'التحكم الحركي (Motion Control)', 'description' => 'حلول تحكم حركي دقيقة لاحتياجات الأتمتة المتقدمة.', 'link' => '/services/motion-control'],
                        ['icon' => 'hub', 'title' => 'الشبكات الصناعية', 'description' => 'بنية تحتية موثوقة للشبكات الصناعية لضمان أمن الاتصالات.', 'link' => '/services/industrial-networking'],
                        ['icon' => 'router', 'title' => 'أنظمة Remote I/O', 'description' => 'حلول وحدات إدخال/إخراج طرفية مرنة وقابلة للتوسع.', 'link' => '/services/remote-io-systems'],
                        ['icon' => 'settings_input_component', 'title' => 'أنظمة المحركات (VFD & Servo)', 'description' => 'حلول قيادة متكاملة للتحكم في السرعة وعزم الدوران والموقع.', 'link' => '/services/drive-systems'],
                        ['icon' => 'electrical_services', 'title' => 'لوحات التحكم الكهربائية', 'description' => 'تصميم وتجميع لوحات تحكم عالية الجودة ومخصصة.', 'link' => '/services/control-panels'],
                        ['icon' => 'smart_toy', 'title' => 'أتمتة الماكينات والآلات', 'description' => 'حلول أتمتة لرفع كفاءة الآلات وتقليل فترات التوقف.', 'link' => '/services/machine-automation'],
                        ['icon' => 'visibility', 'title' => 'تصوير وعرض العمليات (Visualization)', 'description' => 'عرض مباشر للعمليات التشغيلية لشفافية أفضل وسرعة اتخاذ القرار.', 'link' => '/services/process-visualization'],
                    ],
                ],
                'approach_title' => [
                    'en' => 'OUR AUTOMATION PROCESS',
                    'ar' => 'خطوات عملية الأتمتة لدينا',
                ],
                'approach_steps' => [
                    'en' => [
                        ['step' => '01', 'title' => 'Analysis & Consultation', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'Design & Engineering', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'Development & Programming', 'icon' => 'code'],
                        ['step' => '04', 'title' => 'Testing & Simulation', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'Installation & Commissioning', 'icon' => 'settings'],
                        ['step' => '06', 'title' => 'Support & Optimization', 'icon' => 'support_agent'],
                    ],
                    'ar' => [
                        ['step' => '01', 'title' => 'التحليل والاستشارة الفنية', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'التصميم والهندسة', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'التطوير والبرمجة', 'icon' => 'code'],
                        ['step' => '04', 'title' => 'الاختبار والمحاكاة', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'التركيب والتشغيل الفعلي', 'icon' => 'settings'],
                        ['step' => '06', 'title' => 'الدعم الفني والتحسين', 'icon' => 'support_agent'],
                    ],
                ],
                'why_us_title' => [
                    'en' => 'WHY CHOOSE SIS?',
                    'ar' => 'لماذا تختار SIS؟',
                ],
                'why_us_points' => [
                    'en' => [
                        ['text' => 'Experienced Automation Engineers', 'icon' => 'check_circle'],
                        ['text' => 'Multi-vendor & Multi-platform Expertise', 'icon' => 'check_circle'],
                        ['text' => 'Scalable & Future-ready Solutions', 'icon' => 'check_circle'],
                        ['text' => 'High Performance & Reliability', 'icon' => 'check_circle'],
                        ['text' => '24/7 Technical Support', 'icon' => 'check_circle'],
                    ],
                    'ar' => [
                        ['text' => 'مهندسو أتمتة محترفون وذوو خبرة عملية', 'icon' => 'check_circle'],
                        ['text' => 'خبرة عبر مختلف المنصات والشركات العالمية', 'icon' => 'check_circle'],
                        ['text' => 'حلول قابلة للتطوير ومواكبة للمستقبل', 'icon' => 'check_circle'],
                        ['text' => 'أعلى مستويات الأداء والموثوقية', 'icon' => 'check_circle'],
                        ['text' => 'دعم فني متخصص على مدار الساعة', 'icon' => 'check_circle'],
                    ],
                ],
                'industries_title' => [
                    'en' => 'INDUSTRIES WE SERVE',
                    'ar' => 'الصناعات التي نخدمها',
                ],
                'industries' => [
                    'en' => [
                        ['name' => 'Manufacturing', 'icon' => 'factory', 'slug' => 'manufacturing'],
                        ['name' => 'Oil & Gas', 'icon' => 'local_gas_station', 'slug' => 'oil-gas'],
                        ['name' => 'Power & Energy', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'Water & Wastewater', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'Pharmaceutical', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                    'ar' => [
                        ['name' => 'التصنيع', 'icon' => 'factory', 'slug' => 'manufacturing'],
                        ['name' => 'النفط والغاز', 'icon' => 'local_gas_station', 'slug' => 'oil-gas'],
                        ['name' => 'الطاقة', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'المياه ومعالجة الصرف', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'الأدوية', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                    ],
                ],
                'cta_headline' => [
                    'en' => 'Ready to automate your operations?',
                    'ar' => 'هل أنت مستعد لأتمتة عملياتك التشغيلية؟',
                ],
                'cta_subheadline' => [
                    'en' => "Let's build smarter, safer and more efficient systems together.",
                    'ar' => 'دعنا نبني معاً أنظمة أكثر ذكاءً وأماناً وكفاءة.',
                ],
                'cta_button_text' => [
                    'en' => 'Talk to Our Experts',
                    'ar' => 'تحدث مع خبرائنا',
                ],
                'cta_button_link' => '/contact',
                'is_active' => true,
            ]
        );

        // 5. General Services (/general-services)
        SolutionPage::updateOrCreate(
            ['slug' => 'general-services'],
            [
                'title' => [
                    'en' => 'General Services',
                    'ar' => 'الخدمات الهندسية العامة',
                ],
                'subtitle' => [
                    'en' => 'End-to-end engineering services from concept, design and commissioning to lifecycle support.',
                    'ar' => 'خدمات هندسية شاملة من مرحلة المفهوم والتصميم والتشغيل حتى دعم دورة حياة المشروع بالكامل.',
                ],
                'hero_image' => 'images/general-services-hero.webp',
                'badges' => [
                    'en' => [
                        ['icon' => 'verified', 'title' => 'Quality Assured', 'description' => 'International standards and best practices.'],
                        ['icon' => 'groups', 'title' => 'Expert Team', 'description' => 'Certified engineers with multi-vendor expertise.'],
                        ['icon' => 'schedule', 'title' => 'Always Support', 'description' => 'Global support 24/7.'],
                    ],
                    'ar' => [
                        ['icon' => 'verified', 'title' => 'جودة مضمونة', 'description' => 'مطابقة للمعايير الدولية وأفضل الممارسات.'],
                        ['icon' => 'groups', 'title' => 'فريق من الخبراء', 'description' => 'مهندسون معتمدون بخبرات متعددة.'],
                        ['icon' => 'schedule', 'title' => 'دعم دائم', 'description' => 'دعم فني عالمي على مدار 24/7.'],
                    ],
                ],
                'services' => [
                    'en' => [
                        ['icon' => 'extension', 'title' => 'System Integration', 'description' => 'Seamless integration of PLC, SCADA, HMI and field devices into a unified system.', 'link' => '/services/system-integration'],
                        ['icon' => 'assignment', 'title' => 'FAT / SAT', 'description' => 'Factory and Site Acceptance Testing to ensure quality and performance.', 'link' => '/services/fat-sat'],
                        ['icon' => 'settings', 'title' => 'Commissioning', 'description' => 'Professional commissioning services to ensure smooth startup and operation.', 'link' => '/services/commissioning'],
                        ['icon' => 'trending_up', 'title' => 'Migration & Upgrade', 'description' => 'Upgrade legacy systems with modern technology for better performance.', 'link' => '/services/migration-upgrade'],
                        ['icon' => 'architecture', 'title' => 'Design & Consultation', 'description' => 'Engineering design and expert consultation tailored to your project needs.', 'link' => '/services/design-consultation'],
                        ['icon' => 'description', 'title' => 'Technical Documentation', 'description' => 'Complete technical documentation including drawings, manuals and test reports.', 'link' => '/services/technical-documentation'],
                        ['icon' => 'location_on', 'title' => 'Site Survey', 'description' => 'On-site assessment and data collection for accurate planning and execution.', 'link' => '/services/site-survey'],
                        ['icon' => 'bar_chart', 'title' => 'Feasibility Study', 'description' => 'Detailed feasibility analysis to evaluate technical and economic viability.', 'link' => '/services/feasibility-study'],
                        ['icon' => 'groups', 'title' => 'Project Management', 'description' => 'End-to-end project management to deliver solutions on time and within budget.', 'link' => '/services/project-management'],
                        ['icon' => 'sync', 'title' => 'Lifecycle Support', 'description' => 'Ongoing support and maintenance to maximize system availability and reliability.', 'link' => '/services/lifecycle-support'],
                    ],
                    'ar' => [
                        ['icon' => 'extension', 'title' => 'تكامل الأنظمة (System Integration)', 'description' => 'تكامل سلس بين أنظمة PLC و SCADA و HMI والأجهزة الحقلية في نظام موحد.', 'link' => '/services/system-integration'],
                        ['icon' => 'assignment', 'title' => 'اختبارات القبول (FAT / SAT)', 'description' => 'اختبارات قبول المصنع والموقع لضمان الجودة والأداء الفائق.', 'link' => '/services/fat-sat'],
                        ['icon' => 'settings', 'title' => 'التدشين والتشغيل (Commissioning)', 'description' => 'خدمات تشغيل احترافية لضمان بدء تشغيل سلس واستقرار تام.', 'link' => '/services/commissioning'],
                        ['icon' => 'trending_up', 'title' => 'الترقية والتحديث (Migration & Upgrade)', 'description' => 'ترقية الأنظمة القديمة بأحدث التقنيات لأداء أفضل وأمان أعلى.', 'link' => '/services/migration-upgrade'],
                        ['icon' => 'architecture', 'title' => 'التصميم والاستشارات الهندسية', 'description' => 'تصاميم هندسية دقيقة واستشارات متخصصة وفقاً لاحتياجات مشروعك.', 'link' => '/services/design-consultation'],
                        ['icon' => 'description', 'title' => 'التوثيق الفني (Technical Documentation)', 'description' => 'توثيق فني كامل يشمل المخططات الهندسية والأدلة وتقارير الاختبار.', 'link' => '/services/technical-documentation'],
                        ['icon' => 'location_on', 'title' => 'المعاينة الميدانية (Site Survey)', 'description' => 'تقييم ميداني وجمع بيانات دقيقة لتخطيط وتنفيذ احترافي.', 'link' => '/services/site-survey'],
                        ['icon' => 'bar_chart', 'title' => 'دراسات الجدوى الفنية والاقتصادية', 'description' => 'تحليل جدوى تفصيلي لتقييم الجوانب الفنية والمالية للمشروع.', 'link' => '/services/feasibility-study'],
                        ['icon' => 'groups', 'title' => 'إدارة المشاريع الهندسية', 'description' => 'إدارة متكاملة للمشروع لضمان التسليم في الوقت المحدد وضمن الميزانية.', 'link' => '/services/project-management'],
                        ['icon' => 'sync', 'title' => 'دعم دورة حياة المشروع', 'description' => 'دعم وصيانة مستمرة لتعظيم استمرارية وموثوقية الأنظمة.', 'link' => '/services/lifecycle-support'],
                    ],
                ],
                'approach_title' => [
                    'en' => 'OUR PROCESS',
                    'ar' => 'منهجية العمل',
                ],
                'approach_steps' => [
                    'en' => [
                        ['step' => '01', 'title' => 'Consultation', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'Design', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'Engineering', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'FAT / SAT', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'Commissioning', 'icon' => 'rocket_launch'],
                        ['step' => '06', 'title' => 'Support', 'icon' => 'support_agent'],
                    ],
                    'ar' => [
                        ['step' => '01', 'title' => 'الاستشارة والتخطيط', 'icon' => 'group'],
                        ['step' => '02', 'title' => 'التصميم الهندسي', 'icon' => 'architecture'],
                        ['step' => '03', 'title' => 'الهندسة والتطوير', 'icon' => 'settings'],
                        ['step' => '04', 'title' => 'اختبارات FAT / SAT', 'icon' => 'assignment'],
                        ['step' => '05', 'title' => 'التشغيل الفعلي', 'icon' => 'rocket_launch'],
                        ['step' => '06', 'title' => 'الدعم المستمر', 'icon' => 'support_agent'],
                    ],
                ],
                'why_us_title' => [
                    'en' => 'WHY SIS?',
                    'ar' => 'لماذا SIS؟',
                ],
                'why_us_points' => [
                    'en' => [
                        ['text' => 'Experienced & Certified Engineers', 'icon' => 'check_circle'],
                        ['text' => 'Multi-Vendor & Multi-Discipline Expertise', 'icon' => 'check_circle'],
                        ['text' => 'Fast & Efficient Project Delivery', 'icon' => 'check_circle'],
                        ['text' => 'Compliance with International Standards', 'icon' => 'check_circle'],
                        ['text' => 'Global Presence & Local Support', 'icon' => 'check_circle'],
                    ],
                    'ar' => [
                        ['text' => 'مهندسون معتمدون وذوو خبرة عالية', 'icon' => 'check_circle'],
                        ['text' => 'خبرة عبر تخصصات وأنظمة متعددة', 'icon' => 'check_circle'],
                        ['text' => 'تسليم سريع وفعال للمشاريع', 'icon' => 'check_circle'],
                        ['text' => 'التزام تام بأعلى المعايير الدولية', 'icon' => 'check_circle'],
                        ['text' => 'تواجد عالمي مع دعم محلي مباشر', 'icon' => 'check_circle'],
                    ],
                ],
                'industries_title' => [
                    'en' => 'INDUSTRIES WE SERVE',
                    'ar' => 'القطاعات التي نخدمها',
                ],
                'industries' => [
                    'en' => [
                        ['name' => 'Oil & Gas', 'icon' => 'factory', 'slug' => 'oil-gas'],
                        ['name' => 'Power & Energy', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'Water & Wastewater', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'Manufacturing', 'icon' => 'settings', 'slug' => 'manufacturing'],
                        ['name' => 'Pharmaceutical', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                        ['name' => 'Infrastructure', 'icon' => 'domain', 'slug' => 'infrastructure'],
                    ],
                    'ar' => [
                        ['name' => 'النفط والغاز', 'icon' => 'factory', 'slug' => 'oil-gas'],
                        ['name' => 'الطاقة والكهرباء', 'icon' => 'bolt', 'slug' => 'power-energy'],
                        ['name' => 'المياه والصرف الصحي', 'icon' => 'water_drop', 'slug' => 'water-wastewater'],
                        ['name' => 'الصناعة والتصنيع', 'icon' => 'settings', 'slug' => 'manufacturing'],
                        ['name' => 'الأدوية والمستحضرات', 'icon' => 'medication', 'slug' => 'pharmaceutical'],
                        ['name' => 'البنية التحتية', 'icon' => 'domain', 'slug' => 'infrastructure'],
                    ],
                ],
                'cta_headline' => [
                    'en' => 'Ready to Start Your Project?',
                    'ar' => 'هل أنت مستعد لبدء مشروعك؟',
                ],
                'cta_subheadline' => [
                    'en' => "Let's discuss how we can help you achieve operational excellence.",
                    'ar' => 'دعنا نناقش كيف يمكننا مساعدتك في تحقيق التميز التشغيلي.',
                ],
                'cta_button_text' => [
                    'en' => 'Contact Our Experts',
                    'ar' => 'تواصل مع خبرائنا',
                ],
                'cta_button_link' => '/contact',
                'is_active' => true,
            ]
        );
    }

    protected function seedUseCasesPage(): void
    {
        UseCasesPage::updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Projects & Case Studies',
                    'ar' => 'المشاريع ودراسات الحالة',
                ],
                'subtitle' => [
                    'en' => 'Real-world automation and digital transformation projects delivered across multiple industries using leading technologies.',
                    'ar' => 'مشاريع أتمتة وتحول رقمي واقعية تم تنفيذها عبر صناعات متعددة باستخدام أحدث التقنيات.',
                ],
                'cta_text' => [
                    'en' => 'Explore All Projects',
                    'ar' => 'استكشف جميع المشاريع',
                ],
                'cta_link' => '/projects',
                'stats' => [
                    'en' => [
                        ['value' => '500+', 'label' => 'Projects Delivered', 'icon' => 'inventory_2'],
                        ['value' => '250+', 'label' => 'Happy Clients', 'icon' => 'groups'],
                        ['value' => '15+', 'label' => 'Years of Experience', 'icon' => 'calendar_today'],
                        ['value' => '12+', 'label' => 'Countries Served', 'icon' => 'public'],
                    ],
                    'ar' => [
                        ['value' => '500+', 'label' => 'مشروعاً منجزاً', 'icon' => 'inventory_2'],
                        ['value' => '250+', 'label' => 'عميلاً راضياً', 'icon' => 'groups'],
                        ['value' => '15+', 'label' => 'عاماً من الخبرة', 'icon' => 'calendar_today'],
                        ['value' => '12+', 'label' => 'دولة نخدمها', 'icon' => 'public'],
                    ],
                ],
                'pillars' => [
                    'en' => [
                        ['title' => 'Innovation', 'description' => 'Driving smarter solutions with cutting-edge technologies', 'icon' => 'diamond'],
                        ['title' => 'Reliability', 'description' => 'Proven performance and long-term partnership', 'icon' => 'verified_user'],
                        ['title' => 'Global Reach', 'description' => 'Delivering value across 12+ countries', 'icon' => 'public'],
                        ['title' => 'Sustainability', 'description' => 'Building a smarter and cleaner tomorrow', 'icon' => 'eco'],
                    ],
                    'ar' => [
                        ['title' => 'الابتكار', 'description' => 'قيادة حلول أكثر ذكاءً بأحدث التقنيات المتطورة', 'icon' => 'diamond'],
                        ['title' => 'الموثوقية', 'description' => 'أداء مثبت وشراكات استراتيجية طويلة الأمد', 'icon' => 'verified_user'],
                        ['title' => 'الانتشار العالمي', 'description' => 'تقديم قيمة استثنائية عبر أكثر من 12 دولة', 'icon' => 'public'],
                        ['title' => 'الاستدامة', 'description' => 'بناء مستقبل صناعي أنظف وأكثر ذكاءً واستدامة', 'icon' => 'eco'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Update / Seed Featured Case Study Project
        $featured = Project::where('slug', 'smart-energy-management-system')->first() ?: new Project();
        $sector = Sector::first();

        $featured->sector_id = $sector ? $sector->id : 1;
        $featured->slug = 'smart-energy-management-system';
        $featured->cover_image = 'images/projects/smart-energy.webp';
        $featured->is_featured = true;
        $featured->scope = ['SCADA', 'Energy Management', 'IoT', 'BMS'];
        $featured->technologies = ['Siemens', 'Schneider Electric', 'Cisco'];

        $featured->setTranslation('title', 'en', 'Smart Energy Management System');
        $featured->setTranslation('title', 'ar', 'نظام إدارة الطاقة الذكي');
        $featured->setTranslation('client_name', 'en', 'Elsewedy Electric · Oil & Gas');
        $featured->setTranslation('client_name', 'ar', 'السويدي إليكتريك · النفط والغاز');
        $featured->setTranslation('location', 'en', 'Alexandria, Egypt');
        $featured->setTranslation('location', 'ar', 'الإسكندرية، مصر');
        $featured->setTranslation('summary', 'en', 'Integrated automation and energy management solution for a large-scale petrochemical complex, enhancing operational efficiency and sustainability.');
        $featured->setTranslation('summary', 'ar', 'حل أتمتة متكامل وإدارة ذكية للطاقة لمجمع بتروكيماويات ضخم لتعزيز الكفاءة التشغيلية والاستدامة.');
        $featured->setTranslation('description', 'en', 'Integrated automation and energy management solution for a large-scale petrochemical complex, enhancing operational efficiency and sustainability.');
        $featured->setTranslation('description', 'ar', 'حل أتمتة متكامل وإدارة ذكية للطاقة لمجمع بتروكيماويات ضخم لتعزيز الكفاءة التشغيلية والاستدامة.');

        $featured->setTranslation('metrics', 'en', [
            ['value' => '28%', 'label' => 'Energy Consumption Reduction'],
            ['value' => '99.9%', 'label' => 'System Availability'],
            ['value' => '15%', 'label' => 'Operational Cost Savings'],
        ]);
        $featured->setTranslation('metrics', 'ar', [
            ['value' => '28%', 'label' => 'خفض استهلاك الطاقة'],
            ['value' => '99.9%', 'label' => 'جاهزية واستمرارية النظام'],
            ['value' => '15%', 'label' => 'توفير في تكاليف التشغيل'],
        ]);

        $featured->setTranslation('results', 'en', [
            '28% Energy Consumption Reduction',
            '99.9% System Availability',
            '15% Operational Cost Savings',
        ]);
        $featured->setTranslation('results', 'ar', [
            'خفض بنسبة 28% في استهلاك الطاقة',
            'نسبة جاهزية 99.9% للنظام بدون توقف',
            'توفير 15% في مصاريف التشغيل السنوية',
        ]);

        $featured->save();
    }

    protected function seedPartnersPage(): void
    {
        PartnersPage::updateOrCreate(
            ['id' => 1],
            [
                'title' => [
                    'en' => 'Our Trusted Partners',
                    'ar' => 'شركاؤنا المعتمدون والموثوقون',
                ],
                'subtitle' => [
                    'en' => 'We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.',
                    'ar' => 'نتعاون مع رواد التكنولوجيا العالميين لتقديم حلول مبتكرة وموثوقة وجاهزة للمستقبل لعملائنا.',
                ],
                'icon' => 'handshake',
                'value_badges' => [
                    'en' => [
                        ['title' => 'Strong Alliances', 'description' => 'Strategic partnerships with global technology leaders.', 'icon' => 'handshake'],
                        ['title' => 'Certified Excellence', 'description' => 'Certified experts delivering industry best practices.', 'icon' => 'verified'],
                        ['title' => 'Integrated Solutions', 'description' => 'Seamless integration of leading technologies.', 'icon' => 'extension'],
                        ['title' => 'Shared Success', 'description' => 'Driving innovation and growth together.', 'icon' => 'trending_up'],
                    ],
                    'ar' => [
                        ['title' => 'تحالفات استراتيجية قوية', 'description' => 'شراكات وثيقة مع كبرى شركات التكنولوجيا العالمية.', 'icon' => 'handshake'],
                        ['title' => 'تميز هندسي معتمد', 'description' => 'خبراء معتمدون يطبقون أعلى المعايير الصناعية.', 'icon' => 'verified'],
                        ['title' => 'حلول تقنية متكاملة', 'description' => 'دمج سلس لأحدث التقنيات في منظومة واحدة.', 'icon' => 'extension'],
                        ['title' => 'نجاح مشترك', 'description' => 'قيادة الابتكار وتحقيق النمو المستدام معاً.', 'icon' => 'trending_up'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Featured Tier-1 Partners
        $featuredPartners = [
            [
                'name' => 'Siemens',
                'tagline_en' => 'Ingenuity for life',
                'tagline_ar' => 'الإبداع في خدمة الحياة',
                'desc_en' => 'Driving innovation and digital transformation with industry-leading automation and digitalization technologies.',
                'desc_ar' => 'قيادة الابتكار والتحول الرقمي بأحدث تقنيات الأتمتة والرقمنة الصناعية الرائدة عالمياً.',
                'logo' => 'partners/Siemens.png',
                'url' => 'https://www.siemens.com',
                'sort' => 1,
            ],
            [
                'name' => 'Schneider Electric',
                'tagline_en' => 'Life Is On',
                'tagline_ar' => 'الحياة مستمرة مع شنايدر',
                'desc_en' => 'Enabling sustainable and efficient solutions for energy management and industrial automation.',
                'desc_ar' => 'تمكين حلول مستدامة وعالية الكفاءة لإدارة الطاقة والأتمتة الصناعية المتقدمة.',
                'logo' => 'partners/schnider.webp',
                'url' => 'https://www.se.com',
                'sort' => 2,
            ],
            [
                'name' => 'AVEVA',
                'tagline_en' => 'Transforming Industrial Operations',
                'tagline_ar' => 'تحول العمليات الصناعية',
                'desc_en' => 'Empowering industries with software that drives performance improvement, engineering and innovation.',
                'desc_ar' => 'تمكين الصناعات ببرمجيات تدفع تحسين الأداء والهندسة والابتكار الرقمي.',
                'logo' => 'partners/aveva.webp',
                'url' => 'https://www.aveva.com',
                'sort' => 3,
            ],
            [
                'name' => 'odoo',
                'tagline_en' => 'All-in-One Business Software',
                'tagline_ar' => 'برمجيات أعمال متكاملة',
                'desc_en' => 'All-in-one business management software to streamline operations, boost productivity and drive business growth.',
                'desc_ar' => 'نظام متكامل لإدارة الأعمال لتبسيط العمليات ورفع الإنتاجية ودفع عجلة النمو.',
                'logo' => 'partners/odoo.svg',
                'url' => 'https://www.odoo.com',
                'sort' => 4,
            ],
        ];

        foreach ($featuredPartners as $p) {
            $partner = Partner::where('name', $p['name'])->orWhereRaw("json_extract(name, '$.en') = ?", [$p['name']])->first() ?: new Partner();
            $partner->logo_path = $p['logo'];
            $partner->website_url = $p['url'];
            $partner->is_featured = true;
            $partner->type = 'technology_partner';
            $partner->solutions_link = '/services';
            $partner->sort_order = $p['sort'];

            $partner->setTranslation('name', 'en', $p['name']);
            $partner->setTranslation('name', 'ar', $p['name']);
            $partner->setTranslation('tagline', 'en', $p['tagline_en']);
            $partner->setTranslation('tagline', 'ar', $p['tagline_ar']);
            $partner->setTranslation('description', 'en', $p['desc_en']);
            $partner->setTranslation('description', 'ar', $p['desc_ar']);
            $partner->save();
        }
    }

    protected function seedIndustriesPage(): void
    {
        IndustriesPage::updateOrCreate(
            ['id' => 1],
            [
                'tagline' => [
                    'en' => 'INDUSTRY EXPERTISE',
                    'ar' => 'الخبرة الصناعية المتخصصة',
                ],
                'headline' => [
                    'en' => 'Solutions Tailored for Every Industry',
                    'ar' => 'حلول مصممة خصيصاً لكل قطاع صناعي',
                ],
                'description' => [
                    'en' => 'Deep domain expertise and proven experience across a wide range of industries to deliver measurable results and long-term value.',
                    'ar' => 'خبرة قطاعية عميقة وسجل إنجازات مثبت عبر مختلف الصناعات لتقديم نتائج ملموسة وقيمة مستدامة.',
                ],
                'button_text' => [
                    'en' => 'Explore Industries',
                    'ar' => 'استكشف القطاعات الصناعية',
                ],
                'button_link' => '/sectors',
                'image' => 'images/industries-hero.webp',
                'stats' => [
                    'en' => [
                        ['value' => '20+', 'label' => 'Industries Served', 'icon' => 'group'],
                        ['value' => '150+', 'label' => 'Projects Delivered', 'icon' => 'public'],
                        ['value' => '25+', 'label' => 'Countries Reached', 'icon' => 'factory'],
                        ['value' => '100%', 'label' => 'Commitment to Excellence', 'icon' => 'verified'],
                        ['value' => 'Long-term', 'label' => 'Industry Partnerships', 'icon' => 'handshake'],
                    ],
                    'ar' => [
                        ['value' => '20+', 'label' => 'قطاعاً صناعياً نخدمه', 'icon' => 'group'],
                        ['value' => '150+', 'label' => 'مشروعاً تم تسليمه', 'icon' => 'public'],
                        ['value' => '25+', 'label' => 'دولة نصل إليها', 'icon' => 'factory'],
                        ['value' => '100%', 'label' => 'التزام تام بالتميز', 'icon' => 'verified'],
                        ['value' => 'شراكات ممتدة', 'label' => 'علاقات استراتيجية متينة', 'icon' => 'handshake'],
                    ],
                ],
                'is_active' => true,
            ]
        );

        // Update/Seed 8 Sectors with Images and Details matching Screenshot 8
        $sectorsData = [
            [
                'slug' => 'manufacturing',
                'name_en' => 'Manufacturing',
                'name_ar' => 'التصنيع والإنتاج',
                'desc_en' => 'Smart automation solutions to increase productivity, quality and operational efficiency.',
                'desc_ar' => 'حلول أتمتة ذكية لزيادة الإنتاجية والجودة والكفاءة التشغيلية.',
                'icon' => 'settings',
                'image' => 'images/sectors/manufacturing.webp',
                'sort' => 1,
            ],
            [
                'slug' => 'food-beverage',
                'name_en' => 'Food & Beverage',
                'name_ar' => 'الأغذية والمشروبات',
                'desc_en' => 'Safe, efficient and reliable solutions for food and beverage processing and packaging.',
                'desc_ar' => 'حلول آمنة وفعالة وموثوقة لمعالجة وتعبئة الأغذية والمشروبات.',
                'icon' => 'local_cafe',
                'image' => 'images/sectors/food-beverage.webp',
                'sort' => 2,
            ],
            [
                'slug' => 'water-wastewater',
                'name_en' => 'Water & Wastewater',
                'name_ar' => 'المياه ومعالجة مياه الصرف',
                'desc_en' => 'Intelligent water management solutions for treatment, distribution and sustainability.',
                'desc_ar' => 'حلول ذكية لإدارة المياه في محطات المعالجة والتوزيع والاستدامة.',
                'icon' => 'water_drop',
                'image' => 'images/sectors/water.webp',
                'sort' => 3,
            ],
            [
                'slug' => 'infrastructure',
                'name_en' => 'Infrastructure',
                'name_ar' => 'البنية التحتية',
                'desc_en' => 'Smart infrastructure solutions for cities, transportation and public facilities.',
                'desc_ar' => 'حلول بنية تحتية ذكية للمدن وشبكات النقل والمرافق العامة.',
                'icon' => 'domain',
                'image' => 'images/sectors/infrastructure.webp',
                'sort' => 4,
            ],
            [
                'slug' => 'oil-gas',
                'name_en' => 'Oil & Gas',
                'name_ar' => 'النفط والغاز',
                'desc_en' => 'End-to-end automation for upstream, midstream and downstream operations.',
                'desc_ar' => 'أتمتة شاملة لعمليات الاستخراج والنقل والتكرير.',
                'icon' => 'oil_barrel',
                'image' => 'images/sectors/oil-gas.webp',
                'sort' => 5,
            ],
            [
                'slug' => 'energy-utilities',
                'name_en' => 'Energy & Utilities',
                'name_ar' => 'الطاقة والمرافق',
                'desc_en' => 'Reliable solutions for power generation, transmission and distribution.',
                'desc_ar' => 'حلول موثوقة لتوليد الكهرباء ونقل وتوزيع الطاقة.',
                'icon' => 'bolt',
                'image' => 'images/sectors/energy.webp',
                'sort' => 6,
            ],
            [
                'slug' => 'pharmaceuticals',
                'name_en' => 'Pharmaceuticals',
                'name_ar' => 'الصناعات الدوائية',
                'desc_en' => 'Regulated and validated solutions for pharmaceutical manufacturing excellence.',
                'desc_ar' => 'حلول معتمدة ومطابقة للمعايير الصيدلانية لضمان جودة التصنيع.',
                'icon' => 'medication',
                'image' => 'images/sectors/pharma.webp',
                'sort' => 7,
            ],
            [
                'slug' => 'data-centers',
                'name_en' => 'Data Centers',
                'name_ar' => 'مراكز البيانات',
                'desc_en' => 'Advanced automation and infrastructure solutions for modern data centers.',
                'desc_ar' => 'حلول أتمتة وبنية تحتية متطورة لمراكز البيانات الحديثة.',
                'icon' => 'dns',
                'image' => 'images/sectors/data-centers.webp',
                'sort' => 8,
            ],
        ];

        foreach ($sectorsData as $s) {
            $sector = Sector::where('slug', $s['slug'])->first() ?: new Sector();
            $sector->slug = $s['slug'];
            $sector->icon = $s['icon'];
            $sector->image = $s['image'];
            $sector->sort_order = $s['sort'];
            $sector->is_active = true;

            $sector->setTranslation('name', 'en', $s['name_en']);
            $sector->setTranslation('name', 'ar', $s['name_ar']);
            $sector->setTranslation('description', 'en', $s['desc_en']);
            $sector->setTranslation('description', 'ar', $s['desc_ar']);
            $sector->save();
        }
    }
}
