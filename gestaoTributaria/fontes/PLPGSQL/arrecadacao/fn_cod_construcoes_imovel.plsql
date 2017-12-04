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
* $Id: fn_cod_construcoes_imovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: UC-5.3.5
* Caso de uso: uc-05.03.05
*/

/*
$Log$
Revision 1.7  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_cod_construcoes_imovel(inIM INTEGER)  RETURNS VARCHAR AS $$
DECLARE
    stRetorno           VARCHAR;
    stSql               VARCHAR:='';
    inAux               INTEGER :=1;
    inResultado         INTEGER :=0;
    reRegistro          RECORD;
    boLog               BOOLEAN;
BEGIN
stSql :='
    SELECT
        cod
    FROM(
            SELECT
                a.cod_construcao as cod
            FROM
                imobiliario.unidade_autonoma a,
                imobiliario.construcao_edificacao b
            WHERE
                a.cod_construcao        = b.cod_construcao  AND
                a.cod_tipo              = b.cod_tipo        AND
                a.inscricao_municipal   = '||inIM||'
            UNION
            SELECT
                bb.cod_construcao_dependente
            FROM
                imobiliario.unidade_autonoma aa,
                imobiliario.unidade_dependente bb,
                imobiliario.construcao cc,
                imobiliario.construcao_edificacao dd
            WHERE
                aa.cod_construcao           =  bb.cod_construcao        AND
                aa.cod_tipo                 =  bb.cod_tipo              AND
                aa.inscricao_municipal      =  bb.inscricao_municipal   AND --
                bb.cod_construcao_dependente=  cc.cod_construcao        AND
                cc.cod_construcao           =  dd.cod_construcao        AND
                aa.inscricao_municipal = '||inIM||'
        ) as x
        ';
    FOR reRegistro IN EXECUTE stSql
    LOOP
        IF inAux = 1 THEN
            stRetorno := reRegistro.cod;
            inAux := 2;
        ELSE
            stRetorno := stRetorno||','||reRegistro.cod;
        END IF;
    END LOOP;


    IF NOT FOUND OR inAux < 2 THEN
        stRetorno := '';
        boLog := arrecadacao.salva_log('arrecadacao.fn_cod_construcoes_imovel','Erro:'||stRetorno);
    ELSE
        boLog := arrecadacao.salva_log('arrecadacao.fn_cod_construcoes_imovel',stRetorno);
        stRetorno := stRetorno;
    END IF;

    RETURN stRetorno;
END;
$$ LANGUAGE 'plpgsql';
