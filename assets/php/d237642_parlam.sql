-- phpMyAdmin SQL Dump
-- version 4.4.15.1
-- http://www.phpmyadmin.net
--
-- Počítač: md406.wedos.net:3306
-- Vytvořeno: Stř 06. lis 2024, 17:58
-- Verze serveru: 10.4.31-MariaDB-log
-- Verze PHP: 5.4.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Databáze: `d237642_parlam`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `other`
--

CREATE TABLE IF NOT EXISTS `other` (
  `id_other` int(11) NOT NULL,
  `text` longtext NOT NULL,
  `aktivni` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `other`
--

INSERT INTO `other` (`id_other`, `text`, `aktivni`) VALUES
(1, '        <div style="display: flex;" id="footer-text">\n            <div class=" button-container">\n            Web vytvořil: Jiří Boucník<br>\n                Grafiku vytvořil: Marcel Mikula<br>\n                Grafiku upravila: Sarah Buchtová\n            </div>\n        </div>', 1),
(2, '        <div class="table-heading">\n  <b>&#x1F499;・Aktuálně・2024/2025</b>\n       </div>\n<div id="poster">\n         <a href="./assets/img/plakat_2.png" target="_blank">\n                <img src="./assets/img/plakat_2.png" id="responsive-image">\n            </a>\n            <a href="./assets/img/plakat_0.png" target="_blank">\n                <img src="./assets/img/plakat_0.png" id="responsive-image">\n            </a>\n       \n            <a href="./assets/img/plakat_1.png" target="_blank">\n                <img src="./assets/img/plakat_1.png" id="responsive-image">\n            </a>\n        </div>', 1),
(3, '        <div class="table-heading">\n  <b>&#x1F499;・Aktuálně・2024/2025</b>\n       </div>\n<div id="poster">\n         <a href="../assets/img/plakat_2.png" target="_blank">\n                <img src="../assets/img/plakat_2.png" id="responsive-image">\n            </a>\n            <a href="../assets/img/plakat_0.png" target="_blank">\n                <img src="../assets/img/plakat_0.png" id="responsive-image">\n            </a>\n       \n            <a href="../assets/img/plakat_1.png" target="_blank">\n                <img src="../assets/img/plakat_1.png" id="responsive-image">\n            </a>\n        </div>', 1);

-- --------------------------------------------------------

--
-- Struktura tabulky `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id_users` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `users`
--

INSERT INTO `users` (`id_users`, `username`, `name`, `password`) VALUES
(1, 'boucnik.jiri@gmail.com', 'Jiří Boucník', 'Qrxn#@37021@#'),
(2, 'vanek.fanda@centrum.cz', 'František Vaněk', 'Oahbelpax92();'),
(3, 'maty', 'Matěj Kořalka', 'nuh uh');

-- --------------------------------------------------------

--
-- Struktura tabulky `zapis`
--

CREATE TABLE IF NOT EXISTS `zapis` (
  `id_zapis` int(11) NOT NULL,
  `id_users` int(11) DEFAULT 1,
  `datum` date NOT NULL,
  `zapis` longtext NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Vypisuji data pro tabulku `zapis`
--

INSERT INTO `zapis` (`id_zapis`, `id_users`, `datum`, `zapis`) VALUES
(1, 2, '2023-11-01', '//Čtvrtletní schůze s panem ředitelem//=**Návrhy pro zlepšení chodu školy ze strany studentů:**=- Nabíječky=-- Projekt úspěšný, požadavek na zvýšení počtu nabíječek.=- Automatické propustky při zrušené výuce=-- Nelze, systém není propojený s EduPage.=- Sodobary=-- Výměna sodobarů v ostatních patrech, aby byly jako ve čtvrtém =patře=-- Sodobary jsou pronajaté, záleží na domluvě s pronajímateli.=- Zrcadlo na chodbě=-- Skleněné ne, ale alternativa hliníkové folie je možná, vytipovat více =možností na jejich umístění.=- Beseda s hejtmanem JMK=-- Je možná, záleží na domluvě studentů.=- Slevové kupóny do kantýny=-- Lze vyzkoušet, kupóny by musely mít nějaký systém (aby nedošlo =k jejich zfalšování). Nutné prodiskutovat s provozovatelem, jako =návrh i s učiteli ekonomiky.=- Lepší káva v Delikomatu=-- V automatech není káva, jedná se pouze o kávovinovou směs. Lze =se poptat, ale cena by byla vyšší. Z důvodu umístění automatu ve =škole by však nešlo o kávu. =- Stížnost na chování personálu jako uklízečky a kuchařky=-- Individuální případy se musí řešit přes třídního učitele. Tolerantnější =přístup ze strany kuchařek především na konci směny bude řešen =přes správu budov.=- Halloween=-- Různé pohledy ze strany učitelů na masky studentů ve výuce o=Učitel nemá právo kohokoliv kárat za to, jak je oblečen, konkrétní =případy řešit přes třídního učitele.=- Potíže se školním internetem a Wi-Fi=-- Škola je propojená s VUT, tam problém není, problém je v =metalických rozvodech, výměna za optické kabely stojí mezi 3-5 =miliony. Aktuálně se řeší tepelný výměník. Příští prázdniny se teprve =bude řešit internet a ty následující serverové řízení.=- Omezení Wi-Fi v hodinách, nefunguje ve výuce=-- Omezeno, aby žáci nebyli na sociálních sítích. Učitelé mají ve výuce =zadávat jen takovou práci, aby byla v danou chvíli technicky =zvládnutelná. =-- Pokud to i přesto učitel vyžaduje, konfrontovat, řešit s TU nebo =s ŘŠ. =- Rozbité rozhlasy a jiná technika v učebnách=-- Na sharepointu je systém pro zadávání požadavků, je třeba učitele =na technický problém upozornit. V PC učebnách existuje i papírová =forma, která by měla být pravidelně kontrolována a požadavky =řešeny přes IT servis.=- Sportovní zařízení=-- Minulý rok byla velká investice do posilovny =-- Rekonstrukce venkovního areálu se pohybuje okolo 15 milionů, finančně =je to nyní pro školu neúnosné. =-- Jednou za rok prochází areál bezpečnostní kontrolou, opravují se pouze =nutné záležitosti.=**Diskuze:**=- Obor kybernetika - odešel učitel maturitního předmětu (obava studentů o =maturitní zkoušku)=-- Obava není na místě, učitelé stihnou látku probrat. S novými obory =se mění ŠVP, upravují se maturitní požadavky. =- Změna učitele češtiny ve třetím ročníku, obava o čtvrtý maturitní ročník=(každý učí jiným způsobem)=-- Učitelé vědí, co potřebujete k maturitě, přístup může mít každý jiný. =- Dlouhodobá absence paní učitelky Altrichterové.=-- Zajištěn odborný zástup.=- Noví učitelé neměli dostatek informací k chodu školy=-- Učitelé prochází adaptací, dvouletou průpravou a různými semináři.=- Požární poplach – noví učitelé nevěděli, co mají dělat. =-- Zkoušku požárního poplachu je třeba opakovat za účelem hladkého =průběhu. Další zkouška bude na jaře.=-- Informace k požárnímu poplachu jsou uvedené na nástěnkách. =- Revize elektro zařízení.=-- Četnost revizí záleží na poptávce, obvykle dvakrát ročně. =- 1. patro – na WC pípá poplach.=-- Nahlásit panu Šístkovi.=- Špatné časy na PC v učebnách.=-- Řešit s Petrem Čížkem.=- Co škola umožňuje zletilým žákům?=-- Škola má povinnost zajistit vždy dohled nad všemi studenty, i těmi =zletilými.'),
(2, 1, '2023-10-03', '**Volby do funkcí školního parlamentu**=- Zapisovatel: Ondra Šteffan (náhradník Kristýna Karaivanova) – vždy =po setkání rychlá kontrola, ten se odešle na Messenger, GODE =vytiskne a Adam Abbod dá na nástěnky a zveřejní se na Instagramu=**Adaptační program Parlamentu – návrhy:**=- Laser game=- Zoo=- Bowling=- Společná venkovní akce=**Halloween**=- Zařizuje: Kristýna Karaivanova (31.10.2023)=**Nabíječky **=- Plakát roznosit po škole + dát na nástěnky=**Nábor členů do školního Parlamentu (Týká se pouze zatím prváků):**=- Čtvrtek 05.10.2023=- V časech 08:00 – 10:45 a 10:55 – 13:30=- 2 skupiny:=-- Martin Sedlář L3, Adam Abbod V3A + Eli (náhradník)=-- Ondra Šteffan V4B, Kristýna Karaivanova S4B'),
(3, 2, '2023-11-17', '**Zhodnocení akce Halloween**=- Bonbóny uspěly=- Účast větší než minulý rok=**Nové výbory parlamentu**=- Nový výbor IT=- Spojka mezi námi a panem Čížkem=- Adam Abbod=- Nový výbor komunikace (vyjednávání)=- Spojka mezi učiteli a námi=- Bohuslav a Jakub=**Stanovení plánu akcí**=- Bude k dispozici na teams=- Řeší výbor plánování akcí=- Do 14.11.=**Pozvání pana hejtmana**=- Beseda 50-100 lidí=- V řešení omezený počet míst =- Formou přihlášení=**Zrcadlo**=- Musí se najít místo=**Časy na počítačích**=- Řeší výbor IT=**Den otevřených dveří**=- Zájemci na dobrovolníky =- Řeší Martin s paní Klobásovou=**Purples**=- Zájemci na dobrovolníky=- Řeší Eli s Purplesem=**Rozhlas**=- Jen důležité akce (odhlasováno)=- Řeší Sarah=**Stále v řešení:**=- Mikuláš =- Promyslet, kdo by chtěl jít=- Logo Parlamentu=-- Bude nápis Purkyňova (odhlasováno)=- Slevy v kantýně'),
(4, 3, '2023-10-10', '**Halloween**=- Skupinová fotka v outfitech=- návrh data - 31.10.=- Halloween bude jako minulý rok, o velké přestávce bude fotka, může být =soutěž, abychom přilákali více lidí=**Adaptační program**=- Návrhy – Bowling, Zoo, venkovní akce=- Hlasování – Bowling =**Výbory**=- Odhlasovaní lidé: =- Vedoucí marketingu – Eli Dvorcova=- Předseda – Richard Skoumal=- Místopředseda – Martin Sedlář=- Sběr nápadů – Adam Abbod=- Zapisovatel – Ondřej Šteffan=- Organizátoři akcí – Kristýna Karaivanova, Eli Dvorcova=**Majáles**=- Zajímavé pro parlament=- Je potřeba najít lidi co to organizovali minulý rok=**Mikiny pro parlament**=- Je potřeba návrh=- Bude se to nosit?=- Typ mikiny?=**Logo parlamentu**=- Změna=- Kačenka zůstává=- Barvy se mění na barvy školy (odstíny modré) =**Plán na tento školní rok**=- Je potřeba odevzdat vedení co se má stát'),
(5, 3, '2023-12-12', '**Tři králové**=- Potřeba další lidi=- Chodí po třídách, vybírají se peníze na charitu=- Angažování:=-- Kačmařík=-- Kropáček=-- Pokorná=-- Němec=-- Buchtová=-- Brandeis=-- Němeček=- Termín: pátek 05.01.2024=**Vánoční program**=- Termín: pátek 22.12.2023=- Alternativa k třídnímu programu=- Místnosti:=-- Kino: Buchtová, také nahoře=-- Klub kávy a her: Karaivanova, U51=-- Deskové hry: Tesař, před aulou=- Dodatek: Volejbal=- Paní učitelka vyřeší veřejnou zprávu, třídní učitelé kontaktují =organizátory=**Výlet za odměnu na konci roku pro 10 nejaktivnějších členů parlamentu**=- Bítov=**Mikiny Parlamentu**=- Ano, chceme=- Propagační materiál a krásná vzpomínky=- Kapuce, klokan, unisex střih, malé logo, černá=- Pracovní tým: Pokorná, Buchtová=**Moderátor příští schůze**=- Elie'),
(6, 1, '2023-11-14', '**Úkoly z minulého zasedání**=- Místo na zrcadlo: bude dále projednávání=- Slevy v kantýně: v běhu =**Střídání moderátorů schůzí**=- Schváleno=**Nabíječky**=- Čeká se na revizi=**Logo parlamentu**=- Schválena nová verze=**Vlastní znělka před hlášením parlamentu**=**Rozhovory**=- Sedlář, Tesař=**Nové podměty**=- Vysadit dveře u schodů=- Posezení v krátké chodbě ve 4. Patře =**Plán parlamentu na školní rok**=**Mikuláš**=- Barek, Karaivanova, Matula, Abbod, Sokolová, Kropáček'),
(7, 1, '2023-10-17', '**MS Teams**=- Oficiální komunikační kanál=- Úpravy a přidání docházky, nápadů na řešení atd.=**Multimediální den**=- Odměna za aktivní účast v parlamentu=- Žáci ve skupině určené na to=**Logo**=- Návrhy nového loga parlamentu=- Hlasování na messengeru=**Halloween**=- Organizační záležitosti=- Výzdoba=- Instagramové hlasování=**Nabíječky**=- Osvědčily se a plánuje se navýšení jejich počtu=- Dokoupení nabíječek=**Discord**=- Aktualizace rolí a textových kanálů=Plán školy (kabinety učitelů)=- Jednoduchý a přehledný plán, kde jsou kabinety učitelů=- Grafická mapa na nástěnku + web=**Grafik**=- Vytváření plakátů a další práce, potřeba více lidí=- Nábor grafiků u učitelů=- Známka navíc za plakát (pro grafiky)=**Ostatní**=- Nové vybavení venku=- Martinova motivační řeč=- Propustky při zrušených hodinách=- Málo viditelné nástěnky=- Tiché nebo nefunkční rozhlasy=- Stránky parlamentu (web)=- Hlasování o mikinách'),
(8, 2, '2023-12-19', '**Řešení vánočního dne**=- Místnosti=- Dozory=- Hlášení tříd a jednotlivců u MAPA=**Tři králové**=- 1. skupina: Kačmařík. Buchtová, Němec=- 2. skupina: Brandejs, Pokorná, Abbod=- Náhradníci: Kropáček'),
(9, 1, '2023-10-24', '**Adaptační program parlamentu**=- Řešení programu=**Schůze s panem ředitelem**=- 01.11.2023 ve středu=**Návrhy studentů**=- Občas písničky místo zvonění=-- (odhlasováno valnou většinou, 24/27)=- Propustky ve studovně=-- Pravděpodobně ne=- Lepší/ silnější Wifi=-- Problém s velkým počtem lidí, byla by potřeba výměna celé sítě a v =hodině je limitována=- Purkyňka Merch ano/ne=-- Odhlasováno důkladnější probrání (20/27)=- Automatické propustky=- Opravit hodiny =-- Ve třídách i na počítačích=- Zrcadlo na chodbě=-- Bylo by tam logo=**Halloween**=- Výzdoba, příspěvek a příběh na Instagram=**Časopis**=- Lidé by si to nečetli=- Mnoho práce=**Logo parlamentu**=- Sbírání návrhů, odhlasuje se na příští schůzi'),
(10, 3, '2023-09-25', '**Přivítání, docházka**=**Doplnění členů školního Parlamentu + správa komunikačních platforem (oficiální komunikační platformou jsou Teamsy, neoficiální je Messenger)**=**V příštích setkáních proběhnou volby do funkcí školního Parlamentu **=**Četnost setkání + termín + celková doba trvání setkání – 1 × týdně, úterý, vždy od 14 hodin (většinou studovna), doba setkání bude vždy maximálně do 15 hodin**=**Naplánovat setkání s ŘŠ**=**Naplánovat adaptační program**=**Vytvoření plánu školního Parlamentu na školní rok – podání návrhů:**=- Místo zvonění písničky=- Tematické dny (historický, formální…)=- Prohození oborů (výuka – na jeden den)=- Halloween=- Valentýn=- Společné snídaně=- No backpack day=- Podcast – různé rozhovory=- Sportovní dny=- Listování=- Autorské čtení=- Barevný týden=- Ples=- Výměna učitel/žák=- Tři králové=- Kvízy=- Trička'),
(11, 1, '2023-11-28', '**Moderátoři parlamentní schůze**=- Příští schůze Kropáček=**Mikuláš**=- Vytvoření rozvrhu, organizace: Martin Němec=**Prohození oborů**=- Plánuje Kačmařík=**Tři králové**=- 5. 1. 2024=- Plánuje Kačmařík=**Den učitelů**=- Výměna učitel <-> žák=- Březen=**Zrcadlo nebude**=**Vánoční program**=- Místo programu se třídou=-- Různé místnosti:=-- VR=-- Konzole=-- Filmy=-- Kafe=-- a další=- 21. 12. 2023'),
(12, 3, '2023-10-31', '**Adaptační program parlamentu**=- Zhodnocení=**Poznámky na ředitele, schůze 01. 11. 2023**=- Chování zaměstnanců=- Kvalitnější káva=- Pozvat hejtmana (Richard)=- Automatické propustky při zrušené výuce=- Funkčnost rozhlasů=- Zrcadlo na chodbě=- Aktualizace sifonu=**Web parlamentu**=- Zápisy ze schůzí=**Nové volby na vedení parlamentu**=- Místopředseda: Martin =- Místo místopředseda: Eli'),
(42, 1, '2024-01-09', '**Mikiny**=- Co, jak a proč. =- Do kdy, cena=- Opravdu mikiny? Nebo trika?=**Schůze s ředitelem**=- Co na ní. Řešení podmětů=**Výlet za odměnu**=- Kam, počet lidí a obecné zahájení řešení=**Nápad Ellie**=- Zmínění projektu=**Valentýn**=- Kdo bude stříhat srdíčka a jak to celé bude'),
(43, 3, '2024-01-23', '**Valentýn**=- Kde budou schránky, jak budou upevněny?=- Jak to bude probíhat a co je potřeba nachystat?=**Mikiny**=- Velikost a pozice loga'),
(44, 1, '2024-01-30', '**Dořešení akce Valentýn**=- Sbíraní valentýnek středa 31. 1. – úterý 13. 2.=- Roznášení valentýnek 14.2.=**Setkání s panem ředitelem**=- Student se poptají ve třídách na dotazy na pana ředitele=- Sběr podnětů – Adam Abbod=**Mikiny**=- Přijdou nové upravené vzorky=- Budou se muset zjistit velikosti=**Project**=- V řešení se školním psychologem=- Více přenést mezi studenty=- Budou se řešit plakáty, prozatím návrhy=**Debatní kroužek**=- Prezentování, vystupování před lidmi, udržení pozornosti diváků, …=- Vedoucí pan učitel Dušek=- Zjištění zájmu=- Nejlépe cca 20 lidí=- Pravděpodobný termín úterý=- Projednání s panem ředitelem na čtvrtletním setkání'),
(45, 2, '2024-02-13', '**Podměty pro ředitele**=- Stěny mezi pisoáry=- Funkční zámky na záchody=- Testy online mimo počítačové učebny=- Mikrovlnky před obědem =- Stránky na telefonu (jejich obsah je jiný než na PC)=- Debatní kroužek =- Posezení na chodbách ve 4. patře=- Automat na kávu ve 2. patře=**Mikiny a trička**=- Značení loga=**Valentýn**=- Vyhodnocení akce a příprava na rozdávání'),
(46, 1, '2024-02-20', '**Zvolení osoby na psaní zápisů**=**Zhodnocení akce Valentýn**=**Řešení návrhu prohození učitele s žákem**=- Má to smysl? Budou s tím souhlasit učitelé?=**Velikonoce**=- Řeší Martin Němec =- Jaké budou hádanky? A jaká budou místa?=- Datum 27. 3.'),
(47, 3, '2024-02-27', '**Velikonoce**=- Stále v řešení místa na QR kódy=- Ceny za první tři výherní místa=- Plakát'),
(48, 1, '2024-03-12', '**Velikonoce**=- Řešení aplikace a vše okolo=- Dořešení míst na QR kódy=- Dodatečné úpravy plakátu=- Dořešení nápověd na místa '),
(49, 2, '2024-03-26', '**Velikonoce**=- Roznesení QR kódů=- Rozhlášení rozhlasem a EduPage=-- Kdo bude hlásit=- Stránka hotová=-- Vzhled dortu=- Vyhlášení výherců=-- Výhry=-- Kdo a kde bude předávat ceny=**Krajský Studentský sněm**=- Řád parlamentu=**Výlet pro parlament**=- Informace z loňského roku=- Kdo, kam, kdy a na jak dlouho by se jelo=- 2. učitel=**Parlament merch**=- Kolik lidí a kdo chce=- Cena'),
(51, 3, '2024-04-02', '**Velikonoce**=- Zprávy výhercům=-- Rozhlas=- Předání výher =-- Sborovna 9:40 3.4. (musí být přítomen učitel)=- Nekompatibilita ios=**Setkání s ředitelem**=- Podněty od žáků=- Uzavírání známek pro čtvrťáky: důvod=**Mikiny** =- Uzavření výběru=**No backpack day**=- Omezení na velikost aktovky, 9.5.=**Výlet za odměnu**=- 1 nebo 2 dny=- Bítov=- kolem 21.6.=**Moderátor na 8.4.**'),
(52, 2, '2023-11-07', '**Zhodnocení akce Halloween**=- Bonbóny uspěly=- Účast větší než minulý rok=**Nové výbory parlamentu**=- Nový výbor IT=- Spojka mezi námi a panem Čížkem=- Adam Abbod=- Nový výbor komunikace (vyjednávání)=- Spojka mezi učiteli a námi=- Bohuslav a Jakub=**Stanovení plánu akcí**=- Bude k dispozici na teams=- Řeší výbor plánování akcí=- Do 14.11.=**Pozvání pana hejtmana **=- Beseda 50-100 lidí=- V řešení omezený počet míst =- Formou přihlášení=**Zrcadlo**=- Musí se najít místo=**Časy na počítačích**=- Řeší výbor IT=**Den otevřených dveří**		=- Zájemci na dobrovolníky =- Řeší Martin s paní Klobásovou=**Purples**=- Zájemci na dobrovolníky=- Řeší Eli s Purplesem=**Rozhlas**=- Jen důležité akce (odhlasováno)=- Řeší Sarah=**Stále v řešení:**=- Mikuláš =- Promyslet, kdo by chtěl jít=- Logo Parlamentu=- Bude nápis Purkyňova (odhlasováno)=- Slevy v kantýně'),
(53, 1, '2024-04-09', '**Schůzka s panem ředitelem**=- 16.4.=- Podměty pro pana ředitele=-- No Backpack Day =-- Známkování DÚ=-- Žíněnky v tělocvičně=-- Rohová sedačka u kačenek=-- Zpětná vazba z minule=**No Backpack Day**=- Omezení rozměrů=-- max. 0.7 x 0.7 x 0.7 m =-- *Pokud vlastníte platný řidičský průkaz třídy C jsou povoleny i větší rozměry. **Nutno doložit***=**Participativní projekt na zvelebení školy**=- Návrhy studentů na úpravu školy skrze formulář=- Výhra 30 000Kč na realizaci projektu=- Autor vítězného návrhu bude odměněn=- Podněty:=-- Posezení na parapetu 4.p=-- Výběh pro studenty=**Parlament merch**=- Výběr peněz, oznámení ceny =**Moderátor příští schůze**=- Sarah Buchtová='),
(54, 3, '2024-04-16', '//Čtvrtletní schůze s panem ředitelem//=**Přivítání a úvodní slovo pana ředitele**=**Příští týden od pondělí do pátku proběhne hodnocení vzdělávacího procesu, vše bude anonymní, přes Google dotazník, bude na to vyčleněna 1 vyuč. hodina**=**Návrhy pro zlepšení chodu školy ze strany studentů:**=- Změna posunu uzavření klasifikace pro 4. ročníky =-- Pan ředitel je na služební cestě, nebyl by schopen zajistit zákonné podepsání vysvědčení=- NO BACKPACK DAY (plánováno na 9.5.) =- Ano, akce se může uskutečnit, avšak pouze za předpokladu omezení velikosti různých náhrad školní tašky =- Stížnost na zadávání DÚ =-- Vzdělávací systém probíhá ve škole, vše je ale na dohodě vyučujícího a studenta=-- Student nesmí být hodnocen stupněm 5 za práci, kterou neudělal=- Požadavek na novou sedačku ve druhém patře =-- Možná ano, je zde jednání s firmou Thermo Fisher; chovejme se ale k sedačce slušně =-- Kolem 1300 studentů je opravdu velká zátěž=- Parapety ve čtvrtém patře -- možnost je nějak zpevnit, aby se na nich dalo sedět -- ne, nejsou koncipovány k sezení=- Žíněnky v TV + stroje v TV (obnova) =-- Říct vyučujícím TEV, aby zadali požadavek dál=- TSP seminář zde na SŠ =-- Toto zajišťují samy VŠ (např. MUNI), SŠ má za cíl připravit na maturitu=- Ekonomika =-- Již ve třetím ročníku vypracováváme maturitní otázky, které se ale příští rok změní (vývoj ekonomiky) =-- Studentka se domluví se svojí třídou, jak budou dále s vyučujícím (popř. TU) komunikovat =- EXPO =-- Nemohla by škola zajistit ubytování? Brzké ranní vstávání (i ve 2:00 hod.), špatná doprava na D1 =-- SRP by mohlo příští rok zajistit ubytování (přenocování v místě) a zeptat se paní uč. Tomanové, paní zástupkyně Führlingerové=- Praktická maturita =-- Ve čtvrtém ročníku je hodně teorie, která se probírá ve velmi krátkém časovém rozmezí =-- Nejde nějaká aktualizace školních programů? Když žák nastoupí do určitého školního programu, musí s ním i maturovat. Ale postupně dochází k aktualizacím jednotlivých oborů. Neučíte se pro maturitu, ale pro budoucí profesi. Případné dotazy řešit s paní učitelkou Vávrovou. =- Proč se učíme cizí jazyky?= Jsme součástí EU + další rozvoj osobnosti=- Proč maturujeme z EKO?= Viz program ŠVP=- Opětovné řešení problematiky online testů mimo výuku =- Bude dotazník Hodnocení vzdělávacího procesu i pro čtvrté ročníky?=-- Až příští rok. Maturanti mohou pomoci s tvorbou otázek.=- Fiktivní firma =-- příští rok bude víc studentů, jak to bude s organizací? Organizaci si zajišťují vyučující EKO. =- Proč nesmím jako žák prvního ročníku jet na lyžařský výcvik se snowboardem?=-- Zeptat se vyučujících TEV, bezpečnost především.='),
(55, 1, '2024-04-23', '**No Backpack Day**=- Rozměry jako v MHD=- Focení=**Rozpočet 30 000Kč na zvelebení školy**=- Rozeslání formulářů na návrhy=- Výtěžný návrh bude odměněn částkou 1000Kč=- Návrh musí být rozumný a přijatelný vedením školy=**Správa sociálních sítí**=- Vedení: Kačmařík=**Pomoc seniorům na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Akce organizovaná parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova=- Chtějí pomoc od 2 členů našeho parlamentu=- Buchtová, Kačmařík'),
(57, 2, '2024-04-30', '**Proběhlá akce pro důchodce na Čichnově**=- Pomáhaly důchodcům s internetem =- Různé srazy po Brně=- Různé přednášky žáků o aplikacích apod.=- Možnost uskutečnit podobné akce i u nás na škole=- Možnost žáků dělat prezentace pro důchodce=**Participativní projekt**=- Formulář vytvořen=- Plakátky rozneseny po škole=- Formuláře otevřeny do konce Listopadu=**Schůzka s ředitelem školy**=- Co se řešilo=**Loučení s Paní učitelkou Machačovou**=- Odchází na mateřskou dovolenou=**Moderátor příští schůzky**=- Sarah Buchtová'),
(58, 3, '2024-05-07', '**Krajský sněm**=- Rozdělení do výborů=- Moudrá síť (pomoc důchodcům na internetu)=- Volby do evropského parlamentu=- Hra Kahoot=- 16.5. sraz s Hejtmanem na Střední škole informatiky, poštovnictví a finančnictví Čichnova=**Spolupráce s parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Soutěže, Akce=-- Sportovní u nás=-- Besední a Esport u nich=**Participativní projekt na zvelebení školy**=- Počet odpovědí: 0=- Žáci mají podvědomí o tom díky plakátkům=**Výlet parlamentu**=- 20.-21.6. přes noc=- Hrad Bítov a okolí=- 15 lidí=**Moderátor příští sezení**=- Anežka Macháčková'),
(59, 3, '2024-05-14', '**Zhodnocení No Backpack Day**=- Malá účast=- Žádné foto=- Malá propagace=- Problém v omezení rozměru=**Schůze s hejtmanem**=- Kdo půjde=- Slušnější oblečení=**Výlet parlamentu**=- Kdo jede=- 20.-21.6.=- Kde budeme jíst=**Letní program na konec roku**=- Sport, Konzole, Promítání, Piknik s tvořivou dílnou, Venkovní hry=**Větší propagace parlamentu**=- Více nástěnek/stojany=**Moderátor příští schůzi**=- Kropáček Jakub'),
(60, 3, '2024-06-04', '**Setkání s panem hejtmanem**=- Nikdo si nepřipravil otázky=- Mluví více než náš ředitel=- Byli jsme oblečeni slušněji než žáci Střední školy informatiky, poštovnictví a finančnictví Čichnova=- Příští rok se uskuteční u nás=**Výlet školního Parlamentu**=- Původní termín 20.6. (6:30) - 21.6. 14:00=- Pasohlávky=- Program:=-- AquaLand=-- Nové mlýny=- S sebou: =-- Stolní/karetní hry=**Letní program**=- 27.6.=- Program: =-- Káva, konzole=-- Sport=-- Šerm=- Propagace'),
(61, 1, '2024-06-11', '**Parlament výlet**=- **Den 1**=- 6:00 sraz Brno hl.n (vestibul)=- Brno **Pálava**=-- Děvičky=- Pálava=> **Lednice**=-- Burger=-- Prohlídka zámku a okolí=-- Minaret=- Lednice => **Pasohlávky**=-- Ubytování=- **Den 2**=- Volná zábava=- Pasohlávky => **Brno**=**Letní program**=- Klub kávy a přátel bude promítat i filmy=- Upoutávky=**Program na Čichnově**=- Den lidských práv=-- Workshopy, přednášky=- Bližší informace později=**Memorandum s parlamentem na Střední škole informatiky, poštovnictví a finančnictví Čichnova**=- Je v pořádku=- Podpis'),
(62, 2, '2024-09-19', '**Čas schůze**=- úterý 8.h / pátek 7.h=**Jak to bude s volbou předsedy**=**O náboru prvních ročníků do školního Parlamentu**=**Dořešení nabíječek**'),
(63, 1, '2024-09-24', '**Demokraticky zvolen nový předseda**=- Jiří Boucník, L2=**Nástěnky**=**Rada mladších**=**První schůzka školního Parlamentu**=**Nábor prváků**=**Pravidelné schůzky paramentu**=**Revizor nabíječek**=- Patrik Brandejs'),
(64, 2, '2024-10-01', '**Nový předseda**: Jiří Boucník (ve spolupráci s Adam Abbod)=**Zapisovatel**: Matěj Kořalka, František Vaněk=**Sociální sítě**: Roman Kačmařík=**Nástěnkářka**: Sarah Buchtová=**Nábor prváků** =- Rozvrh a trasy pro 4 skupiny=- Co říct o náboru=- Slušnost a mluvit pravdu=- 2.10. čtvrtek 1. a 2. vyučovací hodinu=- Popis práce, něco o akcích, odměny=**Nabíječky** =- 8.10. revize=- Štítky a kontroly=- Nepůjčovat učitelům=- Samostatné nekombinované kabely=- Zodpovídá: Patrik Brandejs=**Plán akcí**=- Nábor prváků=- Adapťák=- Halloween=- Zimní program=- 3 králové=- Valentýn=- Letní program=- Výlet za odměnu=- Akce sport=- Participativní projekt na zvelebení školy=- Spolupráce s Střední školou informatiky, poštovnictví a finančnictví Čichnova'),
(65, 3, '2024-10-08', '**Plán akcí**=- Adapťák=- Haloween=- Mikuláš=- Zimní program=- 3 králové=- Valentýn=- Letní program=- Výlet za odměnu=- Participativní projekt=- Sportovní akce=- Spolupráce se střední školou Čichnova=**Další akce**=- Modrá síť (Důchodci na Čichnově)=--Říjen - Listopad=**Halloween**=- Zařizuje: Macháčková, Buchtová=- 31.10.=- Potřeba zařídit odměny za obleky, plakátky a celkovou organizaci a propagaci=**Adapťák**=- Asi 22.10.=- Bowling=**Dělení do sekcí**=- Nástěnky:=-- Buchtová=- Zápisy:=-- Kořalka, Vaněk=- Nabíječky:=-- Brandejs=- Sociální sítě:=-- Kačmařík=- Grafika:=-- Boucník, Macháčková=- Rozvrhy=-- Němec, Pokorná=**Zmínění diskuzního kroužku**=- O kroužku=- Co se tam děje'),
(66, 3, '2024-10-15', '**Zakázka na dřevěné diáře pro školu**=- Na první stranu hlášky používané na škole=- Parlament vymyslí hlášky a slova =- Do týdne donést návrhy=- Propagace pomocí zástupců=**Messenger skupina**=- Přidat nováčky=**Adaptační program**=- Bowling Brno=- 22. 10. úterý v 9:30 =- Placené školou místo vyučování=**Schůze s panem ředitelem**=- 5.11. 8. vyučovací hodinu=**Moudrá síť**=- Pomoc technicky negramotným lidem=- Někdy v listopadu=- Pomoc z řad našeho parlamentu=- Přednášky mohou být i od nás=-- Jak využívat různé aplikace=- Otázky budou směřované i na nás=**Sbírka na den dobrých skutků**=- Výbava pro handicapované=- 1. skupina=-- Kačmarčík =-- Kropáček=-- Šmarda=- 2. skupina=-- Macháčková=-- Hudcová=-- Munclinger=- Říct informace o projektu=- Středa 23. 10.=**Rada mladších**=- Přednášky, mezigenerační psychologie=- 16. 10. Středa=**Participativní rozpočet**=- 30 000,- na zvelebení školy=- Návrhy od studentů=- Plakátky po škole'),
(67, 3, '2024-11-05', '//Školení od Pana Ředitele doc. RNDr. Aleš Ruda, Ph.D., MBA//=**Dotazník ohledně vyučování**=- Informace o přípravě na život ve školství a výuce=- Kritéria a informace v dotazníku: =-- Učitel by na začátku hodiny měl vysvětlit cíl hodiny a na konci vyučovací hodiny říct, jak se žákům povedlo tohoto cíle dosáhnout=-- Učitel by se měl chovat partnersky až přátelsky k žákům a naopak=-- I předměty které se nevztahují k oboru/tématu nás připravují na život=-- Do vyučování by se měly zakomponovat aktivity kde žáci spolupracují/pomáhají si a  připravují je na život=-- Vyučující by měl vysvětlit učivo tak aby ho většina žáků pochopila a neodkazovala se na samostudium=-- Učitelé by měli zakomponovat měkké dovednosti do vyučování (týmová práce, kreativita)=-- Žáci by se měli učit nejen na písemky, ale i do budoucna=-- Učitel by měl ověřovat znalosti žáků více způsoby=- Dotazník je pouze doplněk, protože všechny otázky nemusejí být relevantní=- Při opakovaným špatným hodnocení nebo při více podněty od žáků se vedení školy samo podívá na případ=**Otázky od studentů**=- Zábrany mezi pisoáry: dle možnosti místa mezi pisoáry=- Sifon na každém patře: podle rozvodů vody a SRPŠ=-- Možnost firmy "Lokni"=- Učitelé dělají ze svého předmětu nejpotřebnější věc oproti potřebám žáků=- Renovace školních sítí:=-- V procesu schvalování a předpoklad že o prázdninách se uskuteční=-- Výměna příslušenství k sítím: v příštích letech=- Výpadky systému EduPage: mezi 9-11 hodinou jsou systémy přetížené');

--
-- Klíče pro exportované tabulky
--

--
-- Klíče pro tabulku `other`
--
ALTER TABLE `other`
  ADD PRIMARY KEY (`id_other`);

--
-- Klíče pro tabulku `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- Klíče pro tabulku `zapis`
--
ALTER TABLE `zapis`
  ADD PRIMARY KEY (`id_zapis`);

--
-- AUTO_INCREMENT pro tabulky
--

--
-- AUTO_INCREMENT pro tabulku `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pro tabulku `zapis`
--
ALTER TABLE `zapis`
  MODIFY `id_zapis` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=68;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
