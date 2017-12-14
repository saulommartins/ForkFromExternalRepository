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
* $Id: fn_lista_localizacoes.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.09
*               uc-05.01.08
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_lista_localizacoes(INT,INT ) RETURNS SETOF RECORD AS '
DECLARE
    inCodNivel      ALIAS FOR $1;
    inCodVigencia   ALIAS FOR $2;
    stSql           VARCHAR   := '''';
    reRegistro      RECORD;

BEGIN
CREATE TEMP TABLE tmp_loc as 
        SELECT l.* , ln.cod_nivel, ln.cod_vigencia, ln.valor 
        FROM    (
                        SELECT * 
                          FROM imobiliario.localizacao loc
                     WHERE NOT ( EXISTS (
                                            SELECT 
                                                BAL.cod_localizacao, 
                                                BAL.timestamp, 
                                                BAL.justificativa
                                            FROM 
                                                imobiliario.baixa_localizacao AS BAL,
                                                (                                    
                                                    SELECT
                                                        MAX (TIMESTAMP) AS TIMESTAMP,
                                                        cod_localizacao              
                                                    FROM                         
                                                        imobiliario.baixa_localizacao
                                                    GROUP BY
                                                        cod_localizacao
                                                ) AS BL
                                            WHERE 
                                                BAL.cod_localizacao = BL.cod_localizacao AND
                                                BAL.timestamp = BL.timestamp AND
                                                BL.cod_localizacao = loc.cod_localizacao AND
                                                BAL.dt_inicio IS NOT NULL AND 
                                                BAL.dt_termino IS NULL
                                        )
                               )
                ) l 
        INNER JOIN imobiliario.localizacao_nivel ln on ln.cod_localizacao = l.cod_localizacao 
        WHERE ln.cod_nivel =1 and ln.cod_vigencia=inCodVigencia and ln.valor<>''0'' and publico.fn_nivel(l.codigo_composto)=1 ;

stSql :=''
        SELECT cod_nivel::integer, cod_vigencia::integer,
valor::integer,cod_localizacao::integer,nom_localizacao::varchar, codigo_composto::varchar , 
publico.fn_mascarareduzida(codigo_composto) as valor_reduzido FROM tmp_loc
        '';
        FOR reRegistro IN EXECUTE stSql LOOP    
            return next reRegistro;
        END LOOP;
drop table tmp_loc;
    RETURN;

END;
'language 'plpgsql';

