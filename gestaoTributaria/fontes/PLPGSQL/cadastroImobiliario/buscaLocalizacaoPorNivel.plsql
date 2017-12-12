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
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Id: buscaLocalizacaoPorNivel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.05
*/

/*
$Log$

*/

CREATE OR REPLACE FUNCTION imobiliario.busca_localizacao_por_nivel( INTEGER , INTEGER )  RETURNS VARCHAR AS '
DECLARE
    inImovel                ALIAS FOR $1;
    inNivel                 ALIAS FOR $2;
    stMascara               VARCHAR;
    inCodLocalizacao        INTEGER;
    stLocalizacaoNivel      VARCHAR;
    stRetorno               VARCHAR;

BEGIN

        SELECT mascara
          INTO stMascara
          FROM imobiliario.nivel
         WHERE cod_nivel    = inNivel
           AND cod_vigencia = (
                                  SELECT cod_vigencia
                                    FROM imobiliario.vigencia
                                   WHERE dt_inicio < now()::date
                                ORDER BY dt_inicio DESC
                                   LIMIT 1
                              );

        SELECT ILN.valor
          INTO stLocalizacaoNivel
          FROM imobiliario.localizacao_nivel                    AS ILN
    INNER JOIN imobiliario.lote_localizacao                     AS ILL
            ON ILN.cod_localizacao     = ILL.cod_localizacao
    INNER JOIN imobiliario.imovel_lote                          AS IIL
            ON IIL.cod_lote            = ILL.cod_lote
           AND IIL.inscricao_municipal = inImovel
         WHERE ILN.cod_nivel           = inNivel;



    stRetorno := lpad( stLocalizacaoNivel ,length( stMascara ),''0'' );

RETURN stRetorno;

END;
' LANGUAGE 'plpgsql';
