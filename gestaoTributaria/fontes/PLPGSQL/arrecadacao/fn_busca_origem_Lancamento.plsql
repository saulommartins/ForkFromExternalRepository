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
* $Id: fn_busca_origem_Lancamento.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.15  2007/09/12 19:55:32  cercato
correcao dos documentos.

Revision 1.14  2007/07/23 12:48:37  dibueno
*** empty log message ***

Revision 1.13  2007/07/12 12:59:02  dibueno
Bug #9641#

Revision 1.12  2006/11/28 13:19:10  dibueno
Bug #7667#

Revision 1.11  2006/11/28 09:54:05  dibueno
Melhoria na consulta de lancamentos, pra listagem da Consulta da Arrecadacao

Revision 1.10  2006/11/16 18:16:32  dibueno
Bug #7471#

Revision 1.9  2006/11/02 18:56:11  dibueno
Bug #7325#

Revision 1.8  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_origem_lancamento( integer, integer, integer, integer ) RETURNS VARCHAR AS $$
DECLARE
    inCodLancamento    	ALIAS FOR $1;
    inExercicio         ALIAS FOR $2;
    inTipoGrupo         ALIAS FOR $3;
    inTipoCredito       ALIAS FOR $4;
    stOrigem            VARCHAR := '''';
    stGrupo             VARCHAR := '''';
BEGIN
            
-- TIPO GRUPO:
    -- caso esteja com valor 0, mostra codigo do grupo / grupo_descricao
    -- caso esteja com valor 1, mostra codigo do grupo / ano exercicio
    -- caso valor = 2, mostra cod_grupo, cod_modulo , descricao e ano_exercicio

-- TIPO CREDITO:
    -- caso esteja com valor 0, mostra codigo do credito - cod_especie - cod_genero - cod_natureza - descricao
    -- caso esteja com valor 1, mostra apenas descricao_credito
    

    SELECT
        (   CASE WHEN al.divida = true THEN
                'DA'
            ELSE
                CASE WHEN acgc.cod_grupo IS NOT NULL THEN
                    CASE WHEN inTipoGrupo = 1 THEN
                        agc.descricao ||' / '|| acgc.ano_exercicio
                    ELSE
                        CASE WHEN inTipoGrupo = 2 THEN
                            '§'|| acgc.cod_grupo ||'§'|| agc.descricao ||'§'|| acgc.ano_exercicio ||'§§'|| agc.cod_modulo
                        ELSE
                            CASE WHEN inTipoGrupo = 3 THEN
                                to_char(mc.cod_credito,'FM999099')||'.'|| to_char(mc.cod_especie,'FM999099')||'.'|| to_char(mc.cod_genero,'FM999099')||'.    '|| mc.cod_natureza||' - '|| mc.descricao_credito||' '|| acgc.cod_grupo ||' / '|| acgc.ano_exercicio ||' - '|| agc.descricao
                            ELSE
                                acgc.cod_grupo ||' § '|| agc.descricao
                            END
                        END
                    END
                ELSE
                    CASE WHEN inTipoCredito = 1 THEN
                        mc.descricao_credito ||' / '|| ac.exercicio
                    ELSE
                        CASE WHEN inTipoGrupo = 2 THEN
                            mc.cod_credito ||'§§'|| mc.descricao_credito ||'§'|| ac.exercicio ||'§§'|| mc.cod_especie ||'§'|| mc.cod_genero ||'§'|| mc.cod_natureza
                        ELSE
                            to_char(mc.cod_credito,'FM999099')||'.'|| to_char(mc.cod_especie,'FM999099')||'.'|| to_char(mc.cod_genero,'FM999099')||'.    '|| mc.cod_natureza||' - '|| mc.descricao_credito
                        END
                    END
                END
            END
        )::varchar
    INTO 
        stOrigem
    FROM
        arrecadacao.lancamento as al

        INNER JOIN (
            SELECT
                cod_lancamento
                , max(cod_calculo) as cod_calculo
            FROM arrecadacao.lancamento_calculo
            GROUP BY
                cod_lancamento
        ) as alc
        ON alc.cod_lancamento = al.cod_lancamento

        INNER JOIN arrecadacao.calculo as ac
        ON ac.cod_calculo = alc.cod_calculo
        
        LEFT JOIN arrecadacao.calculo_grupo_credito as acgc
        ON acgc.cod_calculo = ac.cod_calculo
        AND acgc.ano_exercicio = ac.exercicio

        LEFT JOIN arrecadacao.grupo_credito as agc
        ON agc.cod_grupo = acgc.cod_grupo
        AND agc.ano_exercicio = acgc.ano_exercicio

        LEFT JOIN monetario.credito as mc
        ON mc.cod_credito = ac.cod_credito
        AND mc.cod_especie = ac.cod_especie
        AND mc.cod_genero = ac.cod_genero
        AND mc.cod_natureza = ac.cod_natureza
        
    WHERE
        al.cod_lancamento = inCodLancamento
        and ac.exercicio = quote_literal(inExercicio);


    IF ( stOrigem = 'DA' ) THEN
        SELECT DISTINCT
            '§§DA - '||  dp.numero_parcelamento  ||'§'|| ddp.exercicio
        INTO
            stOrigem
        FROM
            divida.parcelamento as dp
            INNER JOIN divida.divida_parcelamento as ddp
            ON ddp.num_parcelamento = dp.num_parcelamento
            INNER JOIN divida.parcela as dpar
            ON dpar.num_parcelamento = dp.num_parcelamento
            INNER JOIN divida.parcela_calculo as dpc
            ON dpc.num_parcelamento = dpar.num_parcelamento
            AND dpc.num_parcela = dpar.num_parcela
            INNER JOIN arrecadacao.lancamento_calculo as alc
            ON alc.cod_calculo = dpc.cod_calculo
        WHERE
            alc.cod_lancamento = inCodLancamento;
    END IF;


    return stOrigem;
    --
end;
$$ LANGUAGE 'plpgsql';
