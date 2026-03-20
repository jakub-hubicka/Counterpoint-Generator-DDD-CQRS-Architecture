# 🎓 Counterpoint Generator

Tento projekt představuje plně dockerizovanou aplikaci pro skládání a analýzu forem kontrapunktu.

## 🧰 Použité technologie

- 🐳 Symfony 7 stack (PHP-FPM, Nginx, PostgreSQL)
- 🧭 Domain-Driven Design (DDD)
- 🗂️ Doctrine XML mapování se serializací do JSON a lifecycle callbacky
- 🔔 Doménové eventy přes Symfony EventDispatcher
- 🧪 Unit testy + integrační testy (PHPUnit + Foundry)
- 📘 API Platform pro REST API
- 🎼 Doménová logika pro kontrapunktická pravidla

## ✨ Funkce

- **Composition** — tvorba kompozic s cantus firmus a vícero hlasy (soprán, alt, tenor, bas)
- **Counterpoint Rules** — automatická validace paralelních kvint/oktáv, vedení hlasů a řešení disonancí
- **Species Exercises** — workflow pro první až pátou formu kontrapunktu
- **Analysis** — analýza libovolné kompozice s detailním reportem porušení
- **REST API** — kompletní JSON API pro všechny operace

## 🧱 Architektura

- Doctrine XML mapy + JSON serializace
- Doménové události přes Symfony EventDispatcher
- Striktní oddělení domény od infrastruktury/kontrolerů
- CQRS vrstva (Command/Query objekty a handlery ve složce `src/*/Application`)

Bounded Context Domain-Driven Design:

src/
  Composition/          # core kompoziční logiky
    Domain/             # Value Objects, agregáty, pravidla, události
    Application/        # příkazy, dotazy, handlery
    Infrastructure/     # Doctrine repozitáře, API kontrolery
  Exercise/             # správa forem kontrapunktu
  Analysis/             # analytická služba kompozic

## 🧪 Tech stack

- PHP 8.3 / Symfony 7
- PostgreSQL 16
- Doctrine ORM (XML mapping)
- API Platform
- PHPUnit 12
- Docker (PHP-FPM + Nginx + PostgreSQL)

## 🚀 Jak spustit

```bash
# start aplikace
docker compose up -d

# migrace
docker compose exec php php bin/console doctrine:migrations:migrate --no-interaction

# testy (52 testů, 112 asercí)
docker compose exec php php bin/phpunit
```

API běží na `http://localhost:8080/api`.

## 🔗 API endpointy

| Metoda | Cesta | Popis |
|--------|-------|-------|
| POST | `/api/compositions` | založí kompozici s cantus firmus |
| GET | `/api/compositions/{id}` | vrátí kompozici včetně hlasů a not |
| POST | `/api/compositions/{id}/voices` | přidá hlas do kompozice |
| POST | `/api/voices/{id}/notes` | zapíše notu do hlasu |
| POST | `/api/exercises` | založí cvičení |
| GET | `/api/exercises/{id}` | detail cvičení |
| POST | `/api/analysis` | analyzuje kompozici dle pravidel |

## 🎼 Doménová pravidla

- **Paralelní kvinty/oktávy** – dvě po sobě jdoucí perfektní kvinty/oktávy jsou zakázány
- **Vedení hlasů** – skok větší než sextu je odmítnut
- **Řešení disonancí** – disonance musí přejít krokem do konsonance
- **Cantus firmus** – začíná na tónice a postupuje pouze po sekundách
