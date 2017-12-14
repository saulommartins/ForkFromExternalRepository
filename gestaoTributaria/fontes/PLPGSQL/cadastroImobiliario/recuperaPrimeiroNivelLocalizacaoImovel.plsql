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
* $Id: recuperaPrimeiroNivelLocalizacaoImovel.sql 29199 2008-04-15 13:42:29Z fabio $
*
* Casos de uso: uc-05.03.05
*
*/

/*
$Log$
Revision 1.1  2007/01/15 16:29:53  gris
Funcao interna imobiliario.recuperaPrimeiroNivelLocalizacaoImovel.

*/

   CREATE OR REPLACE FUNCTION imobiliario.recuperaPrimeiroNivelLocalizacaoImovel( intInscricaoMunicipal INTEGER )
   RETURNS VARCHAR  AS $$
   DECLARE
      varValor    VARCHAR;
      varRetorno  VARCHAR;
      recAux      Record;
   BEGIN

      SELECT BTRIM(localizacao_nivel.valor)
        INTO varValor
        FROM imobiliario.localizacao_nivel
           , imobiliario.vigencia
           , ( SELECT ( SELECT lote_localizacao.cod_localizacao
                          FROM imobiliario.lote_localizacao
                         WHERE imovel_lote.cod_lote = lote_localizacao.cod_lote ) as cod_localizacao
                          FROM imobiliario.imovel_lote
                         WHERE imovel_lote.inscricao_municipal = intInscricaoMunicipal
                      ORDER BY imovel_lote.timestamp desc
                         LIMIT 1 ) as lote_localizacao
      WHERE localizacao_nivel.cod_localizacao   = lote_localizacao.cod_localizacao
        AND localizacao_nivel.cod_vigencia      = vigencia.cod_vigencia
        AND vigencia.dt_inicio                 <= TO_DATE(now(),'yyyy-mm-dd')
        AND localizacao_nivel.cod_nivel = 1
      ;

      FOR recAux IN SELECT *
                      FROM imobiliario.nivel
                     WHERE nivel.cod_nivel > 1
                     ORDER BY nivel.cod_nivel
      LOOP
         varValor := varValor || '.' ||BTRIM(TO_CHAR(0, Translate(recAux.mascara,'9', '0') ))  ;
      END LOOP;

      SELECT BTRIM(localizacao.nom_localizacao)
        INTO varRetorno
        FROM imobiliario.localizacao
       WHERE localizacao.codigo_composto = varValor
      ;

      RETURN varRetorno;
   END;
   $$ LANGUAGE 'plpgsql';
