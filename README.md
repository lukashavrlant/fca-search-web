# Webové rozhraní nad FCA vyhledávačem
Tato aplikace slouží jako webové rozhraní vyhledávače [napsaného v Pythonu](https://github.com/havrlant/fca-search).

## API
Kromě klasického rozhraní s formulářem nabízí web také API. Má dvě části, první je dotazovací API přes HTTP GET metodu a druhá část slouží k FCA analýze vlastních dat a používá HTTP POST metodu. API obsluhuje soubor `/api.php`, veškeré dále popsané parametry tak patří k tomuto souboru. 

### Dotazovací API
Jednoduché dotazování je realizováno GET metodou, data se specifikují pomocí URL. Základem je soubor `/api.php`. Parametry, které může uživatel používat:

- `d=<index>` specifikuje index, nad kterým chceme dotazy provádět, je to povinný parametr. 
- `q=<dotaz>` udává dotaz, který chceme vyhledávači položit. 
- `f=<formát>` nastavuje výstupní formát. Podporované jsou dva formáty: `console` a `json`. 
- `links` vrátí seznam zaindexovaných dokumentů.
- `linkscount` vrátí počet zaindexovaných dokumentů.
- `words` vrátí seznam všech slov ze všech dokumentů.
- `freq=<slovo>` vrací počet výskytů slova. Program ze slova automaticky udělá stem. K tomuto parametru můžeme ještě přidat další:
 - `docid=<id>` vrací počet výskytů slova v dokumentu s daným ID. 
 - `tf=<id>` vrací hodnotu tf funkce pro slovo a dokument. 
- `findurl=<řetězec>` vrátí seznam URL adres, které obsahují daný řetězec.
- `finddocid=<řetězec>` vrátí seznam URL, které obsahují daný řetězec a ke každému URL zobrazí i ID dokumentu.
- `docfreq=<slovo>` vrátí hodnotu df funkce pro slovo. Stem se tvoří automaticky.
- `docinfo=<id>` vrací informace o dokumentu s daným ID. Informace lze dále specifikovat pomocí dalších parametrů:
 - `wordscount` vrací počet slov v dokumentu.
 - `title` vrací titulek dokumentu.
 - `description` vrací popisek dokumentu.
 - `keywords` vrací klíčová slova dokumentu.
 - `url` vrací adresu dokumentu.
 - `id` vrací identifikátor dokumentu.

 Příklady volání:

 - `api.php?d=inf&links` vrátí seznam dokumentů z indexu `inf`
 - `api.php?d=inf&freq=logika&docid=42` vrátí počet slov `logika` v dokumentu s ID 42 v indexu `inf`
 - `api.php?d=articles&docinfo=47&title` vrátí název stránky dokumentu s ID 47 v indexu `articles`


### FCA analýza nad externě zaslanými daty

Vyhledávač umožňuje zaslat na vstupu seznam dokumentů, vyhledávač z těchto dat vytvoří dočasný index a provede FCA analýzu jako nad běžně existujícím indexem a vrátí výsledek. Volání tohoto API se provádí přes HTTP POST metodu. Na adresu `/api.php` stačí zaslat metodou POST v proměnné `tempsearch` seznam dokumentů, nad kterými má být provedena FCA analýza. Data musí být ve formátu JSON a musí být v tomto tvaru: 

 	{
		'data' : [
			{
				'content' : 'Obsah prvního dokumentu.',
				'title' : 'Titulek prvního dokumentu',
				'description' : 'Popisek prvního dokumentu',
				'url' : 'http://example.com/stranka1.html',
				'type' : 'txt'
			}, 
			{
				'content' : 'Obsah druhého dokumentu.',
				'title' : 'Titulek druhého dokumentu',
				'description' : 'Popisek druhého dokumentu',
				'url' : 'http://example.com/stranka2.html',
				'type' : 'txt'
			}, 
		],
		'options' : {
			'lang' : 'cs'
		}
	}

## Licence

Zdrojové kódy jsou šířeny pod [modifikovanou BSD licencí](http://www.opensource.org/licenses/bsd-license.php): 

Copyright (c) 2012, Lukáš Havrlant
All rights reserved.

Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

- Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

- Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.