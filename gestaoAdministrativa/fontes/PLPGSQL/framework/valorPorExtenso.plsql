/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/*
* Script de função PLPGSQL
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 13042 $
* $Name$
* $Author: gris $
* $Date: 2006-07-20 16:12:03 -0300 (Qui, 20 Jul 2006) $
*
* Casos de uso: uc-01.01.00
*/

CREATE OR REPLACE FUNCTION publico.fn_sub_extenso( vValor varchar)
RETURNS VARCHAR AS $$
DECLARE
   aUnidade    varchar[];
   aDezena     varchar[];
   aCentena    varchar[];
   aExcessao   varchar[];
   iPosicao1   integer;
   iPosicao2   integer;
   iPosicao3   integer;
   vCentena    varchar;
   vDezena     varchar;
   vUnidade    varchar;
   vResultado  varchar;
BEGIN
   -- declaração de vetores para Unidades, Dezenas, Centenas e de 10 a 19
   aUnidade    := '{ "",UM ,DOIS ,TRÊS ,QUATRO ,CINCO ,SEIS ,SETE ,OITO ,NOVE }';
   aDezena     := '{ "", "",VINTE E,TRINTA E,QUARENTA E,CINQUENTA E,SESSENTA E,SETENTA E,OITENTA E,NOVENTA E}';
   aCentena    := '{ "",CENTO E,DUZENTOS E,TREZENTOS E,QUATROCENTOS E,QUINHENTOS E,SEISCENTOS E,SETECENTOS E,OITOCENTOS E,NOVECENTOS E}';
   aExcessao   := '{DEZ ,ONZE ,DOZE ,TREZE ,QUATORZE ,QUINZE ,DESESSEIS ,DESESSETE ,DEZOITO ,DESENOVE }';

   -- extrai as posições de centena, dezena e unidade.
   iPosicao1   := substr(vValor,1,1);
   iPosicao2   := substr(vValor,2,1);
   iPosicao3   := substr(vValor,3,1);

   -- busca nos vetores as palavras correspondentes
   vCentena    := aCentena[iPosicao1 +1];
   vDezena     := ' ' || aDezena[iPosicao2 +1];
   vUnidade    := ' ' || aUnidade[iPosicao3 +1];

   -- trata a exceção: 100
   IF substr(vValor,1,3) = '100' THEN
      vCentena := 'CEM ';
   END IF;

   -- trata as exceções de 10 a 19
   IF substr(vValor,2,1) = '1' THEN
      vDezena  := ' ' || aExcessao[iPosicao3 +1];
      vUnidade := '';
   END IF;

   -- monta a string de resultado
   vResultado := vCentena || vDezena || vUnidade;
   vResultado := trim(vResultado);

   -- verifica se sobrou um 'E' no final
   IF substr(vResultado,length(vResultado)-1,2) = ' E' THEN
      vResultado := substr(vResultado, 1, length(vResultado)-1);
   END IF;

   -- retorna o resultado.
   return vResultado;
END;
$$ language plpgsql;



CREATE OR REPLACE function publico.fn_extenso(nVALOR numeric)
returns text as $$
DECLARE
   vMoedaSin      varchar;
   vMoedaPlu      varchar;
   vMilhao        varchar;
   vMilhar        varchar;
   vUnidade       varchar;
   vCentavo       varchar;
   vComplMilhao   varchar;
   vComplMilhar   varchar;
   vComplUnidade  varchar;
   vResultado     text;
   vValor         varchar;
BEGIN
   -- moeda corrente no singular e plural.
   vMoedaSin   := ' REAL';
   vMoedaPlu   := ' REAIS';

   -- formata o valor de acordo com a máscara 999999999.99
   vValor      := replace(substr(to_char(nVALOR, '999999999D00'), 2), ' ', '0');

   -- usa a função fn_sub_extenso para obter quantos milhões.
   vMilhao     := publico.fn_sub_extenso(substr(vValor,1,3));

   IF (substr(vValor,1,3)::integer > 1) THEN
      vMilhao := vMilhao || ' MILHOES';
      ELSE IF (substr(vValor,1,3)::integer = 1) THEN
         vMilhao := vMilhao || ' MILHAO';
      END IF;
   END IF;

   -- usa a função fn_sub_extenso para obter quantos mil.
   vMilhar  := publico.fn_sub_extenso(substr(vValor,4,3));
   IF (substr(vValor,4,3)::integer > 0) THEN
      vMilhar := vMilhar || ' MIL';
   END IF;

   -- usa a função fn_sub_extenso para obter quantas unidades.
   vUnidade := publico.fn_sub_extenso(substr(vValor,7,3));

   IF (substr(vValor,0,10)::integer > 0) THEN
      IF (substr(vValor,7,3)::integer = 1) THEN
          vUnidade := vUnidade || vMoedaSin;
      ELSE
          vUnidade := vUnidade || vMoedaPlu;
      END IF;
   ELSE
      vUnidade := ''; 
   END IF;

   -- usa a função fn_sub_extenso para obter quantos centavos
   vCentavo := publico.fn_sub_extenso('0' || substr(vValor,11,2));
   IF (substr(vValor,11,2)::integer > 1) THEN
      vCentavo := vCentavo || ' CENTAVOS';
      ELSE IF (substr(vValor,11,2)::integer = 1) THEN
         vCentavo := vCentavo || ' CENTAVO';
      END IF;
   END IF;
    
   -- verifica a necessidade de ',' após o milhão.
   IF length(trim(vMilhao))::integer<>0 and length(trim(vMilhar))::integer<>0 THEN
      --vComplMilhao := ', ';
      vComplMilhao := ' E ';
   ELSE
      vComplMilhao := '';
   END IF;

   -- verifica a necessidade de ',' após o mil.
   IF length(trim(vMilhar))::integer<>0 and (length(trim(vUnidade))::integer<>4 and length(trim(vUnidade))::integer<>5) THEN
      --vComplMilhar := ', ';
      vComplMilhar := ' E ';
   ELSE
      vComplMilhar := '';
   END IF;

   -- verifica a necessidade de ',' após as unidades.
   IF length(trim(vUnidade))::integer<>0 and length(trim(vCentavo))::integer<>0 THEN
      --vComplUnidade := ', ';
      vComplUnidade := ' E ';
   ELSE
      vComplUnidade := '';
   END IF;

   vResultado:= vMilhao || vComplMilhao || vMilhar || vComplMilhar ||
   vUnidade || vComplUnidade || vCentavo;

   -- retorna o resultado.
   return replace(vResultado, '  ', ' ');
END;
$$ language plpgsql;
