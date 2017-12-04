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
* $Revision: 27120 $
* $Name$
* $Author: gris $
* $Date: 2007-12-12 11:54:18 -0200 (Qua, 12 Dez 2007) $
*
* Casos de uso: uc-01.01.00
*/
CREATE OR REPLACE FUNCTION publico.fn_consulta_classificacao(VARCHAR,VARCHAR,VARCHAR) RETURNS VARCHAR AS
$$

DECLARE
    reRecord                RECORD;
    stOut                   VARCHAR := '';
    stSql                   VARCHAR := '';
    stTabelaClassificacao   ALIAS FOR $1;
    stTabelaPosicao         ALIAS FOR $2;
    stFiltro                ALIAS FOR $3;

BEGIN

    IF stTabelaClassificacao != 'orcamento.classificacao_receita'  THEN
       stSql := '
           SELECT *
           FROM  (
               SELECT
                    cla.*
                   ,pos.mascara
               FROM
                    ' || stTabelaClassificacao || '        as cla
                   ,' || stTabelaPosicao || '              as pos
               WHERE   cla.exercicio   = pos.exercicio
               AND     cla.cod_posicao = pos.cod_posicao
               ORDER BY cla.cod_posicao
                 ) as tabela
           ' || stFiltro || '
           ';
    ELSE
           stSql := '
           SELECT *
           FROM  (
               SELECT
                    cla.*
                   ,pos.mascara
               FROM
                    ' || stTabelaClassificacao || '       as cla
                   ,' || stTabelaPosicao || '             as pos
               WHERE   cla.exercicio   = pos.exercicio
               AND     cla.cod_posicao = pos.cod_posicao
               AND     cla.cod_tipo    = pos.cod_tipo
               ORDER BY cla.cod_posicao
                 ) as tabela
           ' || stFiltro || '
           ';
    END IF;

    FOR reRecord IN EXECUTE stSql LOOP
        stOut := stOut||'.'||sw_fn_mascara_dinamica ( ( case when reRecord.mascara = '' then '0' else reRecord.mascara end ) , cast( reRecord.cod_classificacao as VARCHAR) );
    END LOOP;

    stOut := SUBSTR(stOut,2,LENGTH(stOut));

    RETURN stOut;

END;
$$
LANGUAGE 'plpgsql';
