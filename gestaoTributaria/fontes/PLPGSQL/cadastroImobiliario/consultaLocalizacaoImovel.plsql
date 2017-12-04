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
* $Id: consultaLocalizacaoImovel.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Casos de uso: uc-05.01.03
*               uc-05.01.09
*/

/*
$Log$
Revision 1.3  2006/09/15 10:19:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION imobiliario.fn_consulta_localizacao_imovel( INTEGER ) RETURNS VARCHAR[] AS '

    DECLARE
        reRecord   RECORD;
        arRetorno  VARCHAR[] := array[0];
        stSql      VARCHAR;
        reRegistro RECORD;

        inCodLote ALIAS FOR $1;
    BEGIN
        stSql := ''
            SELECT
                LOC.cod_nivel,
                LOC.cod_vigencia,
                LOC.valor_composto,
                LOC.valor_reduzido,
                LOC.valor AS valor_localizacao,
                LOC.nom_localizacao,
                LOC.mascara,
                LOC.nom_nivel,
                LOC.cod_localizacao,
                LL.valor
            FROM
                imobiliario.lote_localizacao     AS LL,
                imobiliario.vw_localizacao_ativa AS LOC
            WHERE
                    LL.cod_localizacao = LOC.cod_localizacao
                AND LL.cod_lote = ''||inCodLote||''
            '';


        FOR reRegistro IN EXECUTE stSql LOOP
            arRetorno[1] := reRegistro.cod_nivel;
            arRetorno[2] := reRegistro.cod_vigencia;
            arRetorno[3] := reRegistro.valor_composto;
            arRetorno[4] := reRegistro.valor_reduzido;
            arRetorno[5] := reRegistro.valor_localizacao;
            arRetorno[6] := reRegistro.nom_localizacao;
            arRetorno[7] := reRegistro.mascara;
            arRetorno[8] := reRegistro.nom_nivel;
            arRetorno[9] := reRegistro.cod_localizacao;
            arRetorno[10] := reRegistro.valor;
        END LOOP;

        RETURN arRetorno;
    END;

'language 'plpgsql';
