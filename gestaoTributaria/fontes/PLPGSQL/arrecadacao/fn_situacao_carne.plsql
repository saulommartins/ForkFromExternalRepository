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
* $Id: fn_situacao_carne.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.9  2007/05/02 17:52:04  dibueno
*** empty log message ***

Revision 1.8  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_situacao_carne( VARCHAR, VARCHAR )  RETURNS VARCHAR AS '
DECLARE
    stNumeracao     ALIAS FOR $1;
    stSexo          ALIAS FOR $2;
    stTeste         VARCHAR;
    inCodMotivo     INTEGER;
    stAdd           VARCHAR;
    boAdd           BOOLEAN := TRUE;
    stRetorno       VARCHAR;
BEGIN
    -- verifica sexo pra devolução
    IF stSexo = ''m'' THEN
        stAdd := ''o'';
    ELSIF stSexo = ''f'' THEN
        stAdd := ''a'';
    END IF;


    -- busca situacao do carne
    SELECT
        numeracao
        , cod_motivo
    INTO stTeste, inCodMotivo
    FROM arrecadacao.carne_devolucao
    WHERE numeracao = stNumeracao AND dt_devolucao <= now()::date;



    IF stTeste IS NOT NULL THEN

        select descricao_resumida
        into stRetorno
        from arrecadacao.motivo_devolucao
        where cod_motivo=inCodMotivo;
        boAdd := false;

    ELSE
        SELECT numeracao INTO stTeste FROM arrecadacao.pagamento
        WHERE numeracao = stNumeracao;
        IF stTeste IS NOT NULL THEN
            stRetorno := ''Pag'';
        ELSE
            SELECT b.cod_parcela::varchar INTO stTeste FROM arrecadacao.carne a, arrecadacao.parcela b WHERE a.numeracao = stNumeracao AND b.cod_parcela=a.cod_parcela AND vencimento < now()::date;
            IF stTeste IS NOT NULL THEN
                stRetorno := ''Vencid'';
            ELSE
                stRetorno := ''A Vencer'';
                stAdd := '''';
            END IF;
        END IF;

    END IF;

    if ( boAdd = true) then
        stRetorno := stRetorno||stAdd;
    else
        stRetorno := stRetorno;
    end if;
    RETURN stRetorno;
END;
' LANGUAGE 'plpgsql';
