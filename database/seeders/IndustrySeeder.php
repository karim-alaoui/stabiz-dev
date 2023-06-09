<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::unprepared(<<<STATMENT
insert into industry_categories (id, name)
values  (1, 'IT/communication'),
        (2, 'Manufacturer (mechanical/electrical)'),
        (3, 'Manufacturer (Material/Chemistry/Food/Cosmetics/Others)'),
        (4, 'trading company'),
        (5, 'Pharmaceuticals, medical devices, life sciences, medical services'),
        (6, 'Finance'),
        (7, 'Construction/plant/real estate'),
        (8, 'Consulting, specialized offices, audit corporations, tax accountant corporations, research'),
        (9, 'Human Resources Service Outsourcing Call Center'),
        (10, 'Internet/Advertising/Media'),
        (11, 'retail'),
        (12, 'Transportation/Logistics'),
        (13, 'Energy (electricity, gas, oil, new energy)'),
        (14, 'Travel/accommodation/leisure'),
        (15, 'Security/cleaning'),
        (16, 'Barber/Beauty/Esthetic'),
        (17, 'education'),
        (18, 'Agriculture, Forestry and Fisheries/Mining'),
        (19, 'Public corporations, government offices, schools, research facilities'),
        (20, 'Other'),
        (21, 'Eating out'),
        (22, 'Ceremonial occasion') on conflict do nothing;
insert into industries (id, name, industry_category_id)
values  (1, 'General electronics manufacturer', 1),
        (2, 'Heavy industry/shipbuilding', 1),
        (3, 'Industrial equipment (machine tools, semiconductor manufacturing equipment, robots, etc.)', 1),
        (4, 'Machine parts/molds', 1),
        (5, 'Home appliances, mobiles, network equipment, copiers, printers', 1),
        (6, 'General electronics manufacturer', 2),
        (7, 'Heavy industry/shipbuilding', 2),
        (8, 'Machine parts/molds', 2),
        (9, 'Home appliances, mobiles, network equipment, copiers, printers', 2),
        (10, 'Electronic components', 2),
        (11, 'semiconductor', 2),
        (12, 'Game/Amusement/Entertainment', 2),
        (13, 'Measuring equipment, optical equipment, precision equipment, analytical equipment', 2),
        (14, 'Automobile (four-wheeled/two-wheeled)', 2),
        (15, 'Auto parts', 2),
        (16, 'Construction machinery and other transportation equipment', 2),
        (17, 'Contract processing industry (various types of processing and surface treatment)', 2),
        (18, 'Functional chemistry (organic/polymer)', 3),
        (19, 'Reagent manufacturer/Contract synthesis/Contract analysis', 3),
        (20, 'Tobacco', 3),
        (21, 'Cosmetics', 3),
        (22, 'Toiletries', 3),
        (23, 'Fragrance', 3),
        (24, 'fertilizer', 3),
        (25, 'feed', 3),
        (26, 'Nanotech bio', 3),
        (27, 'Housing equipment/building materials', 3),
        (28, 'Furniture/interior/household goods', 3),
        (29, 'Sports/outdoor equipment', 3),
        (30, 'toy', 3),
        (31, 'Baby products', 3),
        (32, 'Pet related', 3),
        (33, 'Comprehensive chemistry', 3),
        (34, 'Fashion apparel accessories', 3),
        (35, 'Pesticides', 3),
        (36, 'Metals, ropes, mining, non-ferrous metals', 3),
        (37, 'petrochemistry', 3),
        (38, 'Paper and pulp', 3),
        (39, 'Functional chemistry (inorganic, glass, carbon, ceramic, cement, ceramics)', 3),
        (40, 'Food/beverage manufacturers (including raw materials)', 3),
        (41, 'fiber', 3),
        (42, 'Stationery/office/office supplies', 3),
        (43, 'Other manufacturers', 3),
        (44, 'General trading company', 4),
        (45, 'Copier/printer', 4),
        (46, 'Electronic components', 4),
        (47, 'game', 4),
        (48, 'Construction machinery and other transportation equipment', 4),
        (49, 'Other electrical/electronic/mechanical', 4),
        (50, 'Chemical/pharmaceutical raw materials (organic/polymer)', 4),
        (51, 'Chemicals (inorganic, glass, carbon, ceramic, cement, ceramics)', 4),
        (52, 'toy', 4),
        (53, 'Stationery and office equipment related', 4),
        (54, 'Jewelery and precious metals', 4),
        (55, 'Pulp/paper/wood', 4),
        (56, 'Non-ferrous metal', 4),
        (57, 'Other chemistry/materials/food/energy', 4),
        (58, 'Automobile (importer/sales)', 4),
        (59, 'energy', 4),
        (60, 'Feed, fertilizer, pesticide', 4),
        (61, 'Sports/leisure goods', 4),
        (62, 'Industrial equipment (machine tools, semiconductor manufacturing equipment, robots, etc.)', 4),
        (63, 'Machine parts/molds', 4),
        (64, 'Home appliances', 4),
        (65, 'semiconductor', 4),
        (66, 'Amusement/play equipment', 4),
        (67, 'Measuring equipment, optical equipment, precision equipment, analytical equipment', 4),
        (68, 'Auto parts', 4),
        (69, 'Mining, metal products, steel', 4),
        (70, 'Resin parts/resin products', 4),
        (71, 'Food/Beverage/Tobacco', 4),
        (72, 'Apparel/textile', 4),
        (73, 'Daily necessities/miscellaneous goods', 4),
        (74, 'Building materials', 4),
        (75, 'Books/magazines', 4),
        (76, 'Other trading companies', 4),
        (77, 'Dispensing pharmacy/drug store', 5),
        (78, 'Bio-venture', 5),
        (79, 'Universities/research facilities', 5),
        (80, 'Welfare/long-term care related services', 5),
        (81, 'Other medical related', 5),
        (82, 'Diagnostic agent/clinical testing equipment/clinical testing reagent manufacturer', 5),
        (83, 'SMO', 5),
        (84, 'CSO', 5),
        (85, 'CMO', 5),
        (86, 'Medical consulting', 5),
        (87, 'Medical advertising agency/publisher/marketing/research', 5),
        (88, 'Pharmaceutical manufacturer', 5),
        (89, 'Medical device manufacturer', 5),
        (90, 'Pharmaceutical wholesale', 5),
        (91, 'Medical device wholesale', 5),
        (92, 'CRO', 5),
        (93, 'Hospitals/university hospitals/clinics', 5),
        (94, 'City bank', 6),
        (95, 'Local bank', 6),
        (96, 'Trust bank', 6),
        (97, 'Shinkin banks/unions', 6),
        (98, 'Securities company', 6),
        (99, 'Life insurance', 6),
        (100, 'Non-life insurance', 6),
        (101, 'Insurance agency', 6),
        (102, 'Credit/credit', 6),
        (103, 'consumer loan', 6),
        (104, 'Housing loan', 6),
        (105, 'Stock exchange', 6),
        (106, 'Investment Trust/Investment Advisor', 6),
        (107, 'Venture Capital Private Equity', 6),
        (108, 'Debt collection (servicer)', 6),
        (109, 'Commodity futures trading', 6),
        (110, 'Foreign exchange', 6),
        (111, 'lease', 6),
        (112, 'Short capital', 6),
        (113, 'Rating agency', 6),
        (114, 'Other finance', 6),
        (115, 'Other banks', 6),
        (116, 'Financial information vendor', 6),
        (117, 'Design office', 7),
        (118, 'Plant maker/plant engineering', 7),
        (119, 'Housing equipment/building materials', 7),
        (120, 'General contractor', 7),
        (121, 'Subcontractor', 7),
        (122, 'Housing (house maker)', 7),
        (123, 'Interior/Interior/Remodeling', 7),
        (124, 'Developer', 7),
        (125, 'Real estate agent', 7),
        (126, 'Real estate management', 7),
        (127, 'Equipment management/maintenance', 7),
        (128, 'Land utilization', 7),
        (129, 'Real estate finance', 7),
        (130, 'Construction consultant', 7),
        (131, 'Management/strategy consulting', 8),
        (132, 'Organizational personnel consulting', 8),
        (133, 'Finance and Accounting Advisory (FAS)', 8),
        (134, 'Risk consulting', 8),
        (135, 'Other specialized consulting', 8),
        (136, 'think tank', 8),
        (137, 'marketing research', 8),
        (138, 'Audit corporation', 8),
        (139, 'Tax accountant corporation', 8),
        (140, 'a law office', 8),
        (141, 'accounting firm', 8),
        (142, 'Patent office/patent attorney office', 8),
        (143, 'Judicial scrivener office/administrative scrivener office', 8),
        (144, 'Social Insurance Labor and Social Security Attorney Office', 8),
        (145, 'Comprehensive consulting', 8),
        (146, 'Human resources introduction/employment introduction', 9),
        (147, 'Temporary staffing', 9),
        (148, 'Training service', 9),
        (149, 'Technical outsourcing (dispatch of specific engineers)', 9),
        (150, 'Recruiting site/recruiting media', 9),
        (151, 'outsourcing', 9),
        (152, 'Call center', 9),
        (153, 'General advertising agency', 10),
        (154, 'Advertising production', 10),
        (155, 'SP agency (event, sales promotion proposal, etc.)', 10),
        (156, 'Broadcasting/newspaper/publishing', 10),
        (157, 'Web marketing (advertising agency, consulting, production)', 10),
        (158, 'Web services/Web media (EC/Portal/Social)', 10),
        (159, 'Specialized advertising agency (magazine, newspaper, transportation, outdoor, insert, etc.)', 10),
        (160, 'PR agency', 10),
        (161, 'printing', 10),
        (162, 'Games (online/social)', 10),
        (163, 'Department store', 11),
        (164, 'Food, GMS, discount store', 11),
        (165, 'convenience store', 11),
        (166, 'Home center', 11),
        (167, 'Car dealer', 11),
        (168, 'Mail order/online sales', 11),
        (169, 'Drugstore/Dispensing pharmacy', 11),
        (170, 'Specialty store (home electronics mass retailer)', 11),
        (171, 'Specialty store (apparel and accessories)', 11),
        (172, 'Specialty stores and other retailers', 11),
        (173, 'Railway industry', 12),
        (174, 'Road freight transportation business (courier service, truck transportation, etc.)', 12),
        (175, 'Shipping industry', 12),
        (176, 'Air transportation industry', 12),
        (177, 'Warehouse/packing', 12),
        (178, 'Road passenger transportation business', 12),
        (179, 'Oil and resources', 13),
        (180, 'Electric power', 13),
        (181, 'gas', 13),
        (182, 'New energy (solar, wind, geothermal, bio, etc.)', 13),
        (183, 'Travel/travel agency', 14),
        (184, 'Hotels/Ryokan/Accommodations', 14),
        (185, 'Leisure amusement', 14),
        (186, 'Sports and health related facilities', 14),
        (187, 'Security/cleaning', 15),
        (188, 'Barber/Beauty/Esthetic', 16),
        (189, 'Cram school/preparatory school/vocational school', 17),
        (190, 'Other/Various schools', 17),
        (191, 'Agriculture, Forestry and Fisheries/Mining', 18),
        (192, 'Public corporations, government offices, schools, research facilities', 19),
        (193, 'Special corporations, foundations, other organizations, federations', 20),
        (194, 'Other', 20),
        (195, 'Fast food related', 21),
        (196, 'Izakaya/Bar', 21),
        (197, 'Funeral', 22),
        (198, 'wedding', 22) on conflict do nothing;
STATMENT
        );
    }
}
