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
* $Id: fn_busca_lancamento_situacao.plsql 65294 2016-05-10 14:39:33Z fabio $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
*/

/*
$Log$
Revision 1.16  2007/10/08 13:05:21  cercato
Ticket#10343#

Revision 1.15  2007/10/03 20:30:12  vitor
Ticket#10303#

Revision 1.14  2007/10/03 13:32:11  cercato
Ticket#10305#

Revision 1.13  2007/09/26 19:07:54  vitor
Ticket#10255#

Revision 1.12  2007/08/23 16:14:26  dibueno
Bug#9990#

Revision 1.11  2007/08/03 19:04:18  dibueno
Bug#CONS#

Revision 1.10  2007/07/25 14:20:17  fabio
Bug#order by#

Revision 1.9  2007/06/07 21:05:41  dibueno
Bug #9377#

Revision 1.8  2007/05/31 21:32:37  dibueno
Bug #9345#

Revision 1.7  2007/05/09 13:11:15  dibueno
Bug #9246#

Revision 1.6  2007/04/24 15:40:44  dibueno
Bug #9209#

Revision 1.5  2007/03/30 19:56:38  dibueno
Bug #8961#

Revision 1.4  2007/03/28 18:25:31  dibueno
Bug #8942#

Revision 1.3  2007/03/27 16:05:49  dibueno
Bug #8896#

Revision 1.2  2007/02/09 17:41:15  dibueno
Bug #8185#

Revision 1.1  2007/02/09 15:10:36  dibueno
Bug #8185#

Revision 1.2  2006/09/15 10:20:09  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

CREATE OR REPLACE FUNCTION arrecadacao.fn_busca_lancamento_situacao( INTEGER )  RETURNS VARCHAR AS $$
DECLARE

    inCodLancamento             ALIAS FOR $1;
    inRetorno                   integer;
    reRegistro                  RECORD;
    stSql                       VARCHAR;
    stMotivo                    VARCHAR;
    stRetorno                   VARCHAR := '';
    stRetorno2                  VARCHAR := '';
    inDivida                    integer;
    inNumeroParcelasN           integer;
    inNumeroParcelasU           integer;
    inNumeroParcelasTotal       integer;
    inNumParcelaGroup           integer;
    inParcelaUnicaPago          integer := 0;
    inNumParcelaGroupPago       integer := 0;
    inNumParcelaGroupDev        integer := 0;
    inNumParcelaGroupPagFalse   integer := 0;
    inPagamentoReemissao        integer := 0;
    inCodMotivoCancelamento     integer := 0;
    stNomParcelaGroup           VARCHAR := '';
    boSentencaLancamento        BOOLEAN := false;
    stSentencaLancamento        VARCHAR := '';
    boValido                    BOOLEAN := true;
    boDesonerado                BOOLEAN := false;

BEGIN

    --numero de parcelas normais
    SELECT coalesce(fn_total_parcelas, 0 )
      INTO inNumeroParcelasN
      FROM arrecadacao.fn_total_parcelas( inCodLancamento )
         ;

    --numero de parcelas unicas
    SELECT coalesce ( count(cod_parcela), 0 )
      INTO inNumeroParcelasU
      FROM arrecadacao.parcela
     WHERE cod_lancamento = inCodLancamento
       AND nr_parcela = 0
         ;

--  IF inNumeroParcelasU > 1 THEN
--  	inNumeroParcelasU := 1;
--  END IF;

    inNumeroParcelasTotal := inNumeroParcelasU + inNumeroParcelasN;
    ----------------------------------------------------------------------------------------------------


    --------------------------------------------------------------------------------
    -- VERIFICAÇÃO 1
    --
    -- Desonerações - retorna descricao da desoneracao
    --------------------------------------------------------------------------------
    IF ( inNumeroParcelasTotal = 0 ) THEN

        -- Verifica se o lancamento está na USA DESONERACAO

            SELECT atd.descricao
              INTO stRetorno
              FROM arrecadacao.lancamento_usa_desoneracao as alud
        INNER JOIN arrecadacao.desoneracao as ad
                ON ad.cod_desoneracao = alud.cod_desoneracao
        INNER JOIN arrecadacao.tipo_desoneracao as atd
                ON atd.cod_tipo_desoneracao = ad.cod_tipo_desoneracao
             WHERE alud.cod_lancamento = inCodLancamento
                 ;

            SELECT count( faturamento_sem_movimento )
              INTO stRetorno2
              FROM arrecadacao.lancamento_calculo
        INNER JOIN arrecadacao.cadastro_economico_calculo
                ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo
        INNER JOIN arrecadacao.faturamento_sem_movimento
                ON faturamento_sem_movimento.inscricao_economica = cadastro_economico_calculo.inscricao_economica
               AND faturamento_sem_movimento.timestamp = cadastro_economico_calculo.timestamp
             WHERE lancamento_calculo.cod_lancamento = inCodLancamento
                 ;

        IF ( stRetorno2::numeric != 0 ) THEN
            boValido        := true;
            boDesonerado    := true;
            stRetorno := 'Sem Movimento';
        ELSEIF ( stRetorno != '' ) THEN
            boValido        := true;
            boDesonerado    := true;
            stRetorno := 'Desonerado - '||stRetorno;
        ELSE
            boValido := false;
            stRetorno := 'Inválido';
        END IF;

    END IF;


    --------------------------------------------------------------------------------
    -- VERIFICAÇÃO 2
    --
    -- Pagamentos False - Verifica se a Cota Unica ou TODAS as normais estão com a mesma situação
    -- Retorno: a descricao desse pagamento
    --------------------------------------------------------------------------------

    --verifica se os pagamentos sao false e de um UNICO jeito
    IF ( ( boValido IS TRUE ) AND ( stRetorno = '' ) ) THEN

            SELECT COUNT(ap.cod_parcela)::integer
                 , atp.nom_resumido
              INTO inNumParcelaGroupPagFalse
                 , stNomParcelaGroup
              FROM arrecadacao.parcela AS ap
        INNER JOIN arrecadacao.carne
                ON carne.cod_parcela = ap.cod_parcela
        INNER JOIN arrecadacao.pagamento AS apag
                ON apag.numeracao = carne.numeracao
               AND apag.cod_convenio = carne.cod_convenio
        INNER JOIN arrecadacao.tipo_pagamento AS atp
                ON atp.cod_tipo = apag.cod_tipo
               AND atp.pagamento = false
             WHERE ap.cod_lancamento = inCodLancamento
          GROUP BY atp.nom_resumido
                 ;

        SELECT count (total.cod_parcela)
          INTO inNumParcelaGroupPago
          FROM (
                     SELECT distinct ap.cod_parcela
                       from arrecadacao.parcela as ap
                 INNER JOIN arrecadacao.carne
                         ON carne.cod_parcela = ap.cod_parcela
                 INNER JOIN arrecadacao.pagamento as apag
                         ON apag.numeracao = carne.numeracao
                        AND apag.cod_convenio = carne.cod_convenio
                 INNER JOIN arrecadacao.tipo_pagamento as atp
                         ON atp.cod_tipo = apag.cod_tipo
                        AND atp.pagamento = true
--                         AND atp.cod_tipo != 5
                      WHERE ap.cod_lancamento = inCodLancamento
               ) AS total
             ;

        IF inNumParcelaGroupPagFalse IS NOT NULL AND ( inNumParcelaGroupPagFalse = inNumeroParcelasTotal ) AND ( inNumParcelaGroupPago = 0 ) THEN
            boValido := false;
            stRetorno := stNomParcelaGroup;
        END IF;

    END IF;


    --------------------------------------------------------------------------------
    -- VERIFICAÇÃO 3
    --
    -- Carne Devoluao - Verifica se a Cota Única ou TODAS as NORMAIS estão estão com a mesma situação
    -- Retorno: a Descrição da devolução
    --------------------------------------------------------------------------------

    IF boValido IS TRUE THEN

          SELECT COUNT(cod_parcela)
               , cod_motivo
               , CASE WHEN EXISTS (
                                        SELECT DISTINCT amd.descricao_resumida
                                          FROM arrecadacao.parcela AS ap
                                    INNER JOIN arrecadacao.carne
                                            ON carne.cod_parcela = ap.cod_parcela
                                    INNER JOIN arrecadacao.carne_devolucao AS acd
                                            ON acd.numeracao = carne.numeracao
                                           AND acd.cod_convenio = carne.cod_convenio
                                    INNER JOIN arrecadacao.motivo_devolucao AS amd
                                            ON amd.cod_motivo = acd.cod_motivo
                                     LEFT JOIN arrecadacao.pagamento AS apag
                                            ON apag.numeracao = carne.numeracao
                                           AND apag.cod_convenio = carne.cod_convenio
                                         WHERE amd.descricao_resumida = 'Inscrito em D.A.'
                                           AND ap.cod_lancamento = inCodLancamento
                                           AND apag.numeracao IS NULL
                                      GROUP BY amd.descricao_resumida
                                  ) THEN 'Inscrito em D.A.'
                      WHEN EXISTS (
                                        SELECT DISTINCT amd.descricao_resumida
                                          FROM arrecadacao.parcela AS ap
                                    INNER JOIN arrecadacao.carne
                                            ON carne.cod_parcela = ap.cod_parcela
                                    INNER JOIN arrecadacao.carne_devolucao AS acd
                                            ON acd.numeracao = carne.numeracao
                                           AND acd.cod_convenio = carne.cod_convenio
                                    INNER JOIN arrecadacao.motivo_devolucao AS amd
                                            ON amd.cod_motivo = acd.cod_motivo
                                     LEFT JOIN arrecadacao.pagamento AS apag
                                            ON apag.numeracao = carne.numeracao
                                           AND apag.cod_convenio = carne.cod_convenio
                                         WHERE amd.descricao_resumida = 'Reemitida'
                                           AND ap.cod_lancamento = inCodLancamento
                                           AND apag.numeracao IS NULL
                                      GROUP BY amd.descricao_resumida
                                  ) THEN 'Reemitida'
                      ELSE
                          trim( descricao_resumida )
                 END as descricao_resumida
            INTO inNumParcelaGroupDev
               , inCodMotivoCancelamento
               , stNomParcelaGroup
            FROM (
                       SELECT DISTINCT ap.cod_parcela::integer
                            , amd.cod_motivo
                            , amd.descricao_resumida
                         FROM arrecadacao.parcela as ap
                   INNER JOIN arrecadacao.carne
                           ON carne.cod_parcela = ap.cod_parcela
                   INNER JOIN arrecadacao.carne_devolucao as acd
                           ON acd.numeracao = carne.numeracao
                          AND acd.cod_convenio = carne.cod_convenio
                   INNER JOIN arrecadacao.motivo_devolucao as amd
                           ON amd.cod_motivo = acd.cod_motivo
                    LEFT JOIN arrecadacao.pagamento as apag
                           ON apag.numeracao = carne.numeracao
                          AND apag.cod_convenio = carne.cod_convenio
                        WHERE ap.cod_lancamento = inCodLancamento
                          AND apag.numeracao IS NULL
                          AND acd.cod_motivo != 10
                     ORDER BY amd.descricao_resumida
                 ) as busca
        GROUP BY cod_motivo
               , descricao_resumida
        ORDER BY cod_motivo
               ;

        SELECT count (total.cod_parcela)
          INTO inNumParcelaGroupPago
          FROM (
                     SELECT distinct ap.cod_parcela
                       from arrecadacao.parcela as ap
                 INNER JOIN arrecadacao.carne
                         ON carne.cod_parcela = ap.cod_parcela
                 INNER JOIN arrecadacao.pagamento as apag
                         ON apag.numeracao = carne.numeracao
                        AND apag.cod_convenio = carne.cod_convenio
                 INNER JOIN arrecadacao.tipo_pagamento as atp
                         ON atp.cod_tipo = apag.cod_tipo
                        AND atp.pagamento = true
                        AND atp.cod_tipo != 5
                      WHERE ap.cod_lancamento = inCodLancamento
               ) AS total
             ;
             
      SELECT count (total.cod_parcela)
          INTO inParcelaUnicaPago
          FROM (
                     SELECT distinct ap.cod_parcela
                       from arrecadacao.parcela as ap
                 INNER JOIN arrecadacao.carne
                         ON carne.cod_parcela = ap.cod_parcela
                 INNER JOIN arrecadacao.pagamento as apag
                         ON apag.numeracao = carne.numeracao
                        AND apag.cod_convenio = carne.cod_convenio
                 INNER JOIN arrecadacao.tipo_pagamento as atp
                         ON atp.cod_tipo = apag.cod_tipo
                        AND atp.pagamento = true
                        AND atp.cod_tipo != 5
                      WHERE ap.cod_lancamento = inCodLancamento
			and ap.nr_parcela = 0
               ) AS total
             ;                

        inNumParcelaGroupPagFalse := coalesce ( inNumParcelaGroupPagFalse, 0 );
        inNumParcelaGroupDev      := coalesce ( inNumParcelaGroupDev,      0 );
        inNumParcelaGroupPago     := coalesce ( inNumParcelaGroupPago,     0 );


        IF inNumParcelaGroupPagFalse = (inNumParcelaGroupDev + inNumParcelaGroupPago) THEN
            inNumParcelaGroupPagFalse := 0;
        END IF;

        IF (inNumParcelaGroupDev IS NOT NULL AND inNumParcelaGroupPago IS NULL AND ( inNumParcelaGroupDev = inNumeroParcelasTotal )) THEN -- OR (inNumParcelaGroupDev IS NOT NULL AND inNumParcelaGroupDev > 0 AND inNumParcelaGroupPago IS NOT NULL) THEN
            boValido := false;
            stRetorno := stNomParcelaGroup;

            IF stRetorno = 'Reemitida' THEN
               SELECT count(carne.cod_parcela)
                 INTO inPagamentoReemissao
                 FROM arrecadacao.carne
                 JOIN arrecadacao.pagamento
                   ON pagamento.numeracao = carne.numeracao
                 JOIN arrecadacao.parcela
                   ON parcela.cod_parcela = carne.cod_parcela
                WHERE parcela.cod_lancamento =  inCodLancamento
                    ;

                   IF inPagamentoReemissao >= inNumeroParcelasTotal THEN
                          stRetorno := 'Quitado';
                   ELSIF inPagamentoReemissao < inNumeroParcelasTotal THEN
                          stRetorno := 'Ativo';
                   END IF;
            END IF;

        ELSIF ( ( inNumParcelaGroupDev IS NOT NULL )
                AND ( inNumParcelaGroupDev > 0 )
                AND ( stNomParcelaGroup = 'Cancelada' OR stNomParcelaGroup = 'Cancelado' )
                AND ((inNumParcelaGroupPagFalse+inNumParcelaGroupDev) = (inNumeroParcelasTotal)) -- - (inNumParcelaGroupPagFalse+inNumParcelaGroupDev)))
            )  THEN

            IF (inCodMotivoCancelamento IS NOT NULL AND (inCodMotivoCancelamento = 100 OR inCodMotivoCancelamento = 101)) THEN
                boValido := false;
                stRetorno := 'Ativo';
            ELSE
                boValido := false;
                stRetorno := stNomParcelaGroup;
            END IF;

        ELSIF ( stNomParcelaGroup = 'Inscrito em D.A.' ) THEN

            boValido := false;
            stRetorno := stNomParcelaGroup;

        END IF;



    END IF;


    --------------------------------------------------------------------------------
    -- VERIFICAÇÃO 4
    --
    -- Pagamento TRUE - Verifica se a Cota Única ou TODAS as NORMAIS estão estão com a mesma situação
    -- Retorno: Quitado
    --------------------------------------------------------------------------------
    -- stRetorno := '''';
    -- boValido:= true;

    IF ( ( boValido IS TRUE ) AND ( stRetorno = '' )) THEN

--        SELECT count (total.cod_parcela)
--          INTO inNumParcelaGroupPago
--          FROM (
--                     SELECT distinct ap.cod_parcela
--                       from arrecadacao.parcela as ap
--                 INNER JOIN arrecadacao.carne
--                         ON carne.cod_parcela = ap.cod_parcela
--                 INNER JOIN arrecadacao.pagamento as apag
--                         ON apag.numeracao = carne.numeracao
--                        AND apag.cod_convenio = carne.cod_convenio
--                 INNER JOIN arrecadacao.tipo_pagamento as atp
--                         ON atp.cod_tipo = apag.cod_tipo
--                        AND atp.pagamento = true
--                        AND atp.cod_tipo != 5
--                      WHERE ap.cod_lancamento = inCodLancamento
--               ) AS total
--             ;

        inNumParcelaGroupPagFalse := coalesce ( inNumParcelaGroupPagFalse, 0 );
        inNumParcelaGroupDev      := coalesce ( inNumParcelaGroupDev,      0 );
        inNumParcelaGroupPago     := coalesce ( inNumParcelaGroupPago,     0 );

            SELECT DISTINCT lancamento_calculo.cod_lancamento
              INTO inDivida
              FROM arrecadacao.lancamento_calculo
        INNER JOIN divida.parcela_calculo
                ON parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo
             WHERE lancamento_calculo.cod_lancamento = inCodLancamento
                 ;

        IF ( inNumParcelaGroupPago = inNumeroParcelasTotal ) OR (inParcelaUnicaPago > 0) THEN
            boValido := false;
            stRetorno := 'Quitado';

        ELSIF ((inNumParcelaGroupPago = (inNumeroParcelasTotal -(inNumParcelaGroupPagFalse+inNumParcelaGroupDev)))) THEN
            boValido := false;
--            IF ( inDivida IS NOT NULL ) THEN
--                SELECT DISTINCT
--                    atp.nom_resumido
--                INTO
--                    stNomParcelaGroup
--                from
--                    arrecadacao.parcela as ap
--                    INNER JOIN arrecadacao.carne
--                    ON carne.cod_parcela = ap.cod_parcela
--                    INNER JOIN arrecadacao.pagamento as apag
--                    ON apag.numeracao = carne.numeracao
--                    AND apag.cod_convenio = carne.cod_convenio
--                    INNER JOIN arrecadacao.tipo_pagamento as atp
--                    ON atp.cod_tipo = apag.cod_tipo
--                    AND atp.pagamento = false
--                WHERE ap.cod_lancamento =  inCodLancamento;
--                stRetorno := stNomParcelaGroup;
--            ELSE
                stRetorno := 'Quitado';
--            END IF;
        END IF;

    END IF;


    -----------------------------------------------------------------------------------------

    IF ( boValido is true AND boDesonerado IS FALSE ) THEN
        --inNumLancAtivos := inNumLancAtivos + 1;
        stRetorno := 'Ativo';
    ELSE

        stRetorno := stRetorno;
    END IF;

    return stRetorno;
END;
$$ LANGUAGE 'plpgsql';
