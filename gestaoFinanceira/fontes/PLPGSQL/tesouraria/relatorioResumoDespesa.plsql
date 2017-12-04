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
* $Revision: 28625 $
* $Name$
* $Author: tonismar $
* $Date: 2008-03-18 16:42:56 -0300 (Ter, 18 Mar 2008) $
*
* Casos de uso: uc-02.04.14
*/
CREATE OR REPLACE FUNCTION tesouraria.fn_relatorio_resumo_despesa(VARCHAR,VARCHAR,VARCHAR,VARCHAR,VARCHAR,BIGINT,BIGINT,BIGINT,BIGINT,BIGINT, VARCHAR,VARCHAR,VARCHAR)
RETURNS SETOF RECORD AS $$
DECLARE

    stEntidade              ALIAS FOR $1;
    stExercicio             ALIAS FOR $2;
    stDtInicial             ALIAS FOR $3;
    stDtFinal               ALIAS FOR $4;
    stTipoRelatorio         ALIAS FOR $5;
    inDespesaInicial        ALIAS FOR $6;
    inDespesaFinal          ALIAS FOR $7;
    inContaBancoInicial     ALIAS FOR $8;
    inContaBancoFinal       ALIAS FOR $9;
    inRecurso               ALIAS FOR $10;
    stDestinacaoRecurso     ALIAS FOR $11;
    inCodDetalhamento       ALIAS FOR $12;
    boUtilizaEstruturalTCE  ALIAS FOR $13;

    boTabela                BOOLEAN   := false;

    stSql                   VARCHAR   := '';
    stFiltroPag             VARCHAR   := '';
    stFiltroPagEst          VARCHAR   := '';
    stFiltroTransf          VARCHAR   := '';
    stFiltroTransfEst       VARCHAR   := '';
    stFiltroPagRest         VARCHAR   := '';
    stFiltroPagRestEst      VARCHAR   := '';
    stCampos                VARCHAR   := '';
    stCampos2               VARCHAR   := '';
    reRegistro              RECORD;

BEGIN

    IF (stTipoRelatorio = 'B') THEN
        stCampos  := ' , CAST(conta_banco as varchar) as conta_banco';
        stCampos2 := ' , conta_banco';
    ELSIF (stTipoRelatorio = 'R') THEN
        stCampos  := ' , CAST(recurso as varchar) as recurso';
        stCampos2 := ' , recurso';
    ELSIF (stTipoRelatorio = 'E') THEN
        stCampos  := ' , CAST(entidade as varchar) as entidade';
        stCampos2 := ' , entidade';
    ELSE
        stCampos  := ' , CAST(entidade as varchar) as complemento';
        stCampos2 := ' , complemento';
    END IF;

    IF (stDtInicial = stDtFinal ) THEN
        stFiltroPag       := ' AND TO_DATE(TO_CHAR(TP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stFiltroPagEst    := ' AND TO_DATE(TO_CHAR(TPE.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stFiltroTransf    := ' AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stFiltroTransfEst := ' AND TO_DATE(TO_CHAR(TTE.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stFiltroPagRest   := ' AND TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
        stFiltroPagRestEst:= ' AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') = to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'') ';
    ELSE
        stFiltroPag       := ' AND TO_DATE(TO_CHAR(TP.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
        stFiltroPagEst    := ' AND TO_DATE(TO_CHAR(TPE.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
        stFiltroTransf    := ' AND TO_DATE(TO_CHAR(TT.timestamp_transferencia,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
        stFiltroTransfEst := ' AND TO_DATE(TO_CHAR(TTE.timestamp_estornada,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
        stFiltroPagRest   := ' AND TO_DATE(TO_CHAR(tp.timestamp,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
        stFiltroPagRestEst:= ' AND TO_DATE(TO_CHAR(tpe.timestamp_anulado,''dd/mm/yyyy''),''dd/mm/yyyy'') BETWEEN to_date(' || quote_literal(stDtInicial) || ',''dd/mm/yyyy'')  AND to_date(' || quote_literal(stDtFinal) || ',''dd/mm/yyyy'') ';
    END IF;

    IF ((inContaBancoInicial <> 0) OR (inContaBancoFinal <> 0)) THEN
        IF (inContaBancoInicial = inContaBancoFinal) THEN
            stFiltroPag       := stFiltroPag      ||' AND CPC.cod_plano = '|| inContaBancoInicial ||' ';
            stFiltroPagEst    := stFiltroPagEst   ||' AND CPC.cod_plano = '|| inContaBancoInicial ||' ';
            stFiltroTransf    := stFiltroTransf   ||' AND CPC.cod_plano = '|| inContaBancoInicial ||' ';
            stFiltroTransfEst := stFiltroTransfEst||' AND CPC.cod_plano = '|| inContaBancoInicial ||' ';
            stFiltroPagRest   := stFiltroPagRest  ||' AND plano_banco.cod_plano = '|| inContaBancoInicial ||' ';
            stFiltroPagRestEst:= stFiltroPagRestEst  ||' AND plano_banco.cod_plano = '|| inContaBancoInicial ||' ';
        ELSE
            stFiltroPag       := stFiltroPag      ||' AND CPC.cod_plano BETWEEN '|| inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
            stFiltroPagEst    := stFiltroPagEst   ||' AND CPC.cod_plano BETWEEN '|| inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
            stFiltroTransf    := stFiltroTransf   ||' AND CPC.cod_plano BETWEEN '|| inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
            stFiltroTransfEst := stFiltroTransfEst||' AND CPC.cod_plano BETWEEN '|| inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
            stFiltroPagRest   := stFiltroPagRest  ||' AND plano_banco.cod_plano BETWEEN ' || inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
            stFiltroPagRestEst:= stFiltroPagRestEst  ||' AND plano_banco.cod_plano BETWEEN ' || inContaBancoInicial || ' AND ' || inContaBancoFinal ||' ';
        END IF;
    END IF;

    IF ((inDespesaInicial <> 0) OR (inDespesaFinal <> 0)) THEN
        IF (inDespesaInicial = inDespesaFinal) THEN
            stFiltroPag    := stFiltroPag   ||' AND ODE.cod_despesa = '||inDespesaInicial||' ';
            stFiltroPagEst := stFiltroPagEst||' AND ODE.cod_despesa = '||inDespesaInicial||' ';
        ELSE
           stFiltroPag    := stFiltroPag   ||' AND ODE.cod_despesa BETWEEN '||inDespesaInicial||' AND '||inDespesaFinal||' ';
           stFiltroPagEst := stFiltroPagEst||' AND ODE.cod_despesa BETWEEN '||inDespesaInicial||' AND '||inDespesaFinal||' ';
        END IF;
    END IF;

    IF (inRecurso > 0) THEN
        stFiltroPag    := stFiltroPag   ||' AND  ORE.cod_recurso = ' || inRecurso || ' ';
        stFiltroPagEst := stFiltroPagEst||' AND  ORE.cod_recurso = ' || inRecurso || ' ';
    END IF;

    stFiltroPag       := stFiltroPag      ||' AND TO_CHAR(TP.timestamp,''yyyy'')           = '|| quote_literal(stExercicio) || ' AND TP.cod_entidade  in ( ' || stEntidade || ' ) ';
    stFiltroPagEst    := stFiltroPagEst   ||' AND TO_CHAR(TPE.timestamp_anulado,''yyyy'')  = ' || quote_literal(stExercicio) || ' AND TPE.cod_entidade in ( ' || stEntidade || ' ) ';
    stFiltroTransf    := stFiltroTransf   ||' AND TT.exercicio  = ' || quote_literal(stExercicio) || ' AND TT.cod_entidade  in ( ' || stEntidade || ' ) ';
    stFiltroTransfEst := stFiltroTransfEst||' AND TTE.exercicio = ' || quote_literal(stExercicio) || ' AND TTE.cod_entidade in ( ' || stEntidade || ' ) ';
    stFiltroPagRest   := stFiltroPagRest  ||' AND TO_CHAR(tp.timestamp,''yyyy'') = ' || quote_literal(stExercicio) || ' AND cp.cod_entidade  in ( ' || stEntidade || ' ) ';
    stFiltroPagRestEst:= stFiltroPagRestEst||' AND TO_CHAR(tpe.timestamp_anulado,''yyyy'') = ' || quote_literal(stExercicio) || ' AND cp.cod_entidade  in ( ' || stEntidade || ' ) ';

    stSql := '
      SELECT plano
           , conta
           , sum(pago) as pago
           , sum(estornado) as estornado
           , tipo_despesa
           '|| stCampos ||'
        FROM (
              ----------------
              -- PAGAMENTOS --
              ----------------
              SELECT ODE.cod_despesa as plano
                   , OCD.descricao as conta
                   , SUM(coalesce(NLP.vl_pago,0.00)) as pago
                   , CAST(''0.00'' as NUMERIC(14,2)) as estornado
                   , CPC.conta_banco
                   , ORE.recurso
                   , OE.entidade
                   , ODE.cod_recurso
                   , CASE WHEN (emp.exercicio = to_char(TP.timestamp,''yyyy''))
                          THEN CAST(''O'' as varchar)
                          ELSE CAST(''E'' as varchar)
                      END as tipo_despesa 
                FROM tesouraria.pagamento as TP
          --BUSCA CONTA BANCO
          INNER JOIN (
                      SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as conta_banco                
                           , CPA.cod_plano
                           , CPA.exercicio 
                           , CPC.cod_estrutural
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio 
                     ) as CPC
                  on TP.cod_plano        = CPC.cod_plano
                 AND TP.exercicio_plano  = CPC.exercicio 

          --LIGAÇÃO COM NOTA_LIQUIDACAO_PAGA
          INNER JOIN empenho.nota_liquidacao_paga as NLP
                  on NLP.exercicio       = TP.exercicio
                 AND NLP.cod_nota        = TP.cod_nota
                 AND NLP.cod_entidade    = TP.cod_entidade
                 AND NLP.timestamp       = TP.timestamp

          --LIGAÇÃO COM NOTA_LIQUIDACAO
          INNER JOIN empenho.nota_liquidacao as NL
                  on NL.exercicio       = NLP.exercicio
                 AND NL.cod_nota        = NLP.cod_nota
                 AND NL.cod_entidade    = NLP.cod_entidade

          --LIGAÇÃO COM EMPENHO
          INNER JOIN empenho.empenho as EMP
                  on EMP.exercicio       = NL.exercicio_empenho
                 AND EMP.cod_empenho     = NL.cod_empenho
                 AND EMP.cod_entidade    = NL.cod_entidade

          --LIGAÇÃO COM PRE_EMPENHO_DESPESA
          INNER JOIN empenho.pre_empenho_despesa as PED
                  on PED.exercicio       = EMP.exercicio
                 AND PED.cod_pre_empenho = EMP.cod_pre_empenho   

          --LIGAÇÃO COM DESPESA
          INNER JOIN orcamento.despesa as ODE
                  on ODE.exercicio       = PED.exercicio
                 AND ODE.cod_despesa     = PED.cod_despesa   

          --LIGAÇÃO COM RECURSO
          INNER JOIN (
                      SELECT ORE.nom_recurso as recurso
                           , ORE.exercicio
                           , ORE.cod_recurso
                           , ORE.masc_recurso_red
                           , ORE.cod_detalhamento
                        FROM orcamento.recurso('||quote_literal(stExercicio)||') as ORE 
                     ) as ORE
                  on ORE.exercicio    = ODE.exercicio
                 AND ORE.cod_recurso  = ODE.cod_recurso
    ';

    if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
        stSql := stSql || ' AND ore.masc_recurso_red like '''||stDestinacaoRecurso||'%'||''' ';
    end if;

    if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
        stSql := stSql || ' AND ore.cod_detalhamento = '||inCodDetalhamento||' ';
    end if;

    stSql := stSql || '

          --LIGAÇÃO COM CONTA_DESPESA
           LEFT JOIN orcamento.conta_despesa as OCD
                  on OCD.exercicio       = PED.exercicio
                 AND OCD.cod_conta       = PED.cod_conta

          --BUSCA ENTIDADE
          INNER JOIN (
                      SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                           , OE.cod_entidade
                           , OE.exercicio     
                        FROM orcamento.entidade as OE
                           , sw_cgm as CGM 
                       WHERE OE.numcgm = CGM.numcgm
                     ) as OE
                  on OE.cod_entidade = TP.cod_entidade
                 AND OE.exercicio    = TP.exercicio 

               WHERE TP.exercicio is not null
            '||stFiltroPag||'
            GROUP BY ODE.cod_despesa
                   , OCD.descricao
                   , CPC.conta_banco   
                   , ORE.recurso
                   , OE.entidade
                   , ODE.cod_recurso
                   , EMP.exercicio
                   , TP.timestamp

           UNION ALL

              --------------
              -- ESTORNOS --
              --------------
              SELECT ODE.cod_despesa as plano
                   , OCD.descricao  as conta
                   , CAST(''0.00'' as NUMERIC(14,2)) as pago
                   , SUM(coalesce(NLPA.vl_anulado,0.00)) as estornado
                   , CPC.conta_banco
                   , ORE.recurso
                   , OE.entidade
                   , ODE.cod_recurso
                   , CASE WHEN (emp.exercicio = to_char(TP.timestamp,''yyyy''))
                          THEN CAST(''O'' as varchar) 
                          ELSE CAST(''E'' as varchar) 
                      END as tipo_despesa 
                FROM tesouraria.pagamento as TP
          INNER JOIN tesouraria.pagamento_estornado as TPE
                  on TPE.cod_entidade    = TP.cod_entidade
                 AND TPE.exercicio       = TP.exercicio
                 AND TPE.timestamp       = TP.timestamp
                 AND TPE.cod_nota        = TP.cod_nota

          --BUSCA CONTA BANCO
          INNER JOIN (
                      SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as conta_banco
                           , CPA.cod_plano
                           , CPA.exercicio
                           , CPC.cod_estrutural 
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio
                     ) as CPC
                  on TP.cod_plano        = CPC.cod_plano
                 AND TP.exercicio_plano  = CPC.exercicio

          --LIGAÇÃO COM NOTA_LIQUIDACAO_PAGA_ANULADA
          INNER JOIN empenho.nota_liquidacao_paga_anulada NLPA
                  on NLPA.exercicio          = TPE.exercicio
                 AND NLPA.cod_nota           = TPE.cod_nota
                 AND NLPA.cod_entidade       = TPE.cod_entidade
                 AND NLPA.timestamp          = TPE.timestamp
                 AND NLPA.timestamp_anulada  = TPE.timestamp_anulado

          --LIGAÇÃO COM NOTA_LIQUIDACAO
          INNER JOIN empenho.nota_liquidacao as NL
                  on NL.exercicio       = NLPA.exercicio
                 AND NL.cod_nota        = NLPA.cod_nota
                 AND NL.cod_entidade    = NLPA.cod_entidade

          --LIGAÇÃO COM EMPENHO
          INNER JOIN empenho.empenho as EMP
                  on EMP.exercicio       = NL.exercicio_empenho
                 AND EMP.cod_empenho     = NL.cod_empenho
                 AND EMP.cod_entidade    = NL.cod_entidade

          --LIGAÇÃO COM PRE_EMPENHO_DESPESA
          INNER JOIN empenho.pre_empenho_despesa as PED
                  on PED.exercicio       = EMP.exercicio
                 AND PED.cod_pre_empenho = EMP.cod_pre_empenho

          --LIGAÇÃO COM DESPESA
          INNER JOIN orcamento.despesa as ODE
                  on ODE.exercicio       = PED.exercicio
                 AND ODE.cod_despesa     = PED.cod_despesa

          --LIGAÇÃO COM RECURSO
          INNER JOIN (
                      SELECT ORE.nom_recurso as recurso
                           , ORE.exercicio
                           , ORE.cod_recurso
                           , ORE.masc_recurso_red
                           , ORE.cod_detalhamento
                        FROM orcamento.recurso('||quote_literal(stExercicio)||') as ORE
                     ) as ORE
                  on ORE.exercicio    = ODE.exercicio
                 AND ORE.cod_recurso  = ODE.cod_recurso
    ';

    if (stDestinacaoRecurso is not null and stDestinacaoRecurso <> '') then
        stSql := stSql || ' AND ore.masc_recurso_red like '''|| stDestinacaoRecurso||'%'||''' ';
    end if;

    if (inCodDetalhamento is not null and inCodDetalhamento <> '') then
        stSql := stSql || ' AND ore.cod_detalhamento = '|| inCodDetalhamento ||' ';
    end if;

    stSql := stSql || '

          --LIGAÇÃO COM CONTA_DESPESA
           LEFT JOIN orcamento.conta_despesa as OCD
                  on OCD.exercicio = PED.exercicio
                 AND OCD.cod_conta = PED.cod_conta

          --BUSCA ENTIDADE
          INNER JOIN (
                      SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                           , OE.cod_entidade
                           , OE.exercicio
                        FROM orcamento.entidade as OE
                           , sw_cgm as CGM
                       WHERE OE.numcgm = CGM.numcgm
                     ) as OE
                  on OE.cod_entidade = TP.cod_entidade
                 AND OE.exercicio    = TP.exercicio

               WHERE TP.exercicio is not null
         '||stFiltroPagEst||'
            GROUP BY ODE.cod_despesa
                   , OCD.descricao
                   , CPC.conta_banco
                   , ODE.cod_recurso
                   , ORE.recurso
                   , OE.entidade
                   , EMP.exercicio
                   , TP.timestamp

           UNION ALL
              ----------------------
              -- PAGAMENTOS EXTRA --
              ----------------------
              SELECT CPCD.cod_plano  as plano
                   , CPCD.conta
                   , SUM(coalesce(TT.valor,0.00)) as pago
                   , CAST(''0.00'' as NUMERIC(14,2)) as estornado
                   , CPC.conta_banco
                   , '''' as recurso
                   , OE.entidade
                   , 0 as cod_recurso
                   , CAST(''E'' as varchar) as tipo_despesa
                FROM tesouraria.transferencia as TT
          -- BUSCA CONTA BANCO
          INNER JOIN (
                      SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as conta_banco
                           , CPA.cod_plano
                           , CPA.exercicio
                           , CPC.cod_estrutural
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio
                     ) as CPC
                  on TT.cod_plano_credito= CPC.cod_plano
                 AND TT.exercicio        = CPC.exercicio

          -- BUSCA CONTA DESPESA
          INNER JOIN (
                      SELECT CPC.nom_conta as conta
                           , CPA.cod_plano
                           , CPA.exercicio
                           , CPC.cod_estrutural
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio
                     ) as CPCD
                  on TT.cod_plano_debito = CPCD.cod_plano
                 AND TT.exercicio        = CPCD.exercicio

          --BUSCA ENTIDADE
          INNER JOIN (
                      SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                           , OE.cod_entidade
                           , OE.exercicio
                        FROM orcamento.entidade as OE
                           , sw_cgm as CGM 
                       WHERE OE.numcgm = CGM.numcgm
                     ) as OE
                  on OE.cod_entidade = TT.cod_entidade
                 AND OE.exercicio    = TT.exercicio 
 
               WHERE TT.cod_tipo = 1
            '|| stFiltroTransf ||'
            GROUP BY CPCD.cod_plano
                   , CPCD.conta
                   , CPC.conta_banco   
                   , OE.entidade

           UNION ALL
    --------------------------------------------------------
    --                           ESTORNO DE PAGAMENTOS EXTRA
    --------------------------------------------------------
              SELECT CPCD.cod_plano  as plano
                   , CPCD.conta
                   , CAST(''0.00'' as NUMERIC(14,2))   as pago
                   , SUM(coalesce(TTE.valor,0.00))    as estornado
                   , CPC.conta_banco
                   , '''' as recurso
                   , OE.entidade
                   , 0 as cod_recurso
                   , CAST(''E'' as varchar) as tipo_despesa
                FROM tesouraria.transferencia as TT
          INNER JOIN tesouraria.transferencia_estornada as TTE
                  on TTE.cod_entidade    = TT.cod_entidade
                 AND TTE.tipo            = TT.tipo
                 AND TTE.exercicio       = TT.exercicio
                 AND TTE.cod_lote        = TT.cod_lote
    
          -- BUSCA CONTA BANCO
          INNER JOIN (
                      SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as conta_banco
                           , CPA.cod_plano
                           , CPA.exercicio
                           , CPC.cod_estrutural
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio
                     ) as CPC
                  on TT.cod_plano_credito= CPC.cod_plano
                 AND TT.exercicio        = CPC.exercicio

          -- BUSCA CONTA DESPESA        
          INNER JOIN (
                      SELECT CPC.nom_conta as conta
                           , CPA.cod_plano
                           , CPA.exercicio 
                           , CPC.cod_estrutural
                        FROM contabilidade.plano_conta as CPC
                           , contabilidade.plano_analitica as CPA
                       WHERE CPC.cod_conta = CPA.cod_conta
                         AND CPC.exercicio = CPA.exercicio
                     ) as CPCD
                  on TT.cod_plano_debito = CPCD.cod_plano
                 AND TT.exercicio        = CPCD.exercicio

          --BUSCA ENTIDADE
          INNER JOIN (
                      SELECT OE.cod_entidade || '' - '' || CGM.nom_cgm as entidade
                           , OE.cod_entidade
                           , OE.exercicio     
                        FROM orcamento.entidade as OE
                           , sw_cgm as CGM 
                       WHERE OE.numcgm = CGM.numcgm
                     ) as OE
                  on OE.cod_entidade = TT.cod_entidade
                 AND OE.exercicio    = TT.exercicio

               WHERE TT.cod_tipo = 1
            '||stFiltroTransfEst||'
            GROUP BY CPCD.cod_plano
                   , CPCD.conta
                   , CPC.conta_banco
                   , OE.entidade

           UNION ALL

              ----------------------------------
              -- PAGAMENTO RESTOS - BUG 11338 --
              ----------------------------------
              SELECT plano.cod_plano_debito as plano
                   , CPC.nom_conta as conta
                   , plano.vl_pago as pago
                   , CAST(''0.00'' as NUMERIC(14,2)) as estornado
                   , plano.nom_conta as conta_banco
                   , '''' as recurso
                   , plano.cod_entidade || '' - '' ||  plano.nom_entidade as entidade
                   , 0 as cod_recurso
                   , CAST(''E'' as varchar) as tipo_despesa

                FROM (
                      SELECT tp.exercicio_plano as exercicio
                           , plano_banco.cod_plano
                           , sum(coalesce(nlp.vl_pago, 0.00)) AS vl_pago
                           , plano_banco.nom_conta as nom_conta
                           , oe.cod_entidade
                           , cgm.nom_cgm as nom_entidade
                           , tp.timestamp
                           , '''' as nome_despesa
                           , contabilidade.fn_recupera_conta_lancamento( CP.exercicio
                                                                       , CP.cod_entidade
                                                                       , CP.cod_lote
                                                                       , CP.tipo
                                                                       , CP.sequencia
                                                                       , ''D''
                                                                       ) as cod_plano_debito
                        FROM (
                              SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as nom_conta
                                   , cpa.cod_plano as cod_plano
                                   , cpa.exercicio
                                   , CPC.cod_estrutural
                                FROM contabilidade.plano_conta as cpc
                                   , contabilidade.plano_analitica as cpa
                               WHERE cpa.cod_conta = cpc.cod_conta
                                 AND cpc.exercicio = cpa.exercicio
                             ) as plano_banco
                           , tesouraria.pagamento AS TP

                  INNER JOIN orcamento.entidade as oe
                          ON oe.cod_entidade  = tp.cod_entidade
                         AND oe.exercicio = tp.exercicio

                  INNER JOIN sw_cgm as cgm
                          ON oe.numcgm = cgm.numcgm

                        JOIN empenho.nota_liquidacao_paga as nlp
                          ON nlp.cod_nota     = tp.cod_nota
                         AND nlp.cod_entidade = tp.cod_entidade
                         AND nlp.exercicio    = tp.exercicio
                         AND nlp.timestamp    = tp.timestamp

                        JOIN empenho.nota_liquidacao as nl
                          ON nl.cod_nota     = nlp.cod_nota
                         AND nl.exercicio    = nlp.exercicio
                         AND nl.cod_entidade = nlp.cod_entidade
                         AND nl.exercicio_empenho < ' || quote_literal(stExercicio) || '
 
                        JOIN contabilidade.pagamento as cp
                          ON cp.cod_entidade         = nlp.cod_entidade
                         AND cp.exercicio_liquidacao = nlp.exercicio
                         AND cp.cod_nota             = nlp.cod_nota
                         AND cp.timestamp            = nlp.timestamp

                   LEFT JOIN contabilidade.pagamento_estorno as cpe
                          ON cpe.cod_entidade = cp.cod_entidade
                         AND cpe.exercicio    = cp.exercicio
                         AND cpe.sequencia    = cp.sequencia
                         AND cpe.tipo         = cp.tipo
                         AND cpe.cod_lote     = cp.cod_lote

                        JOIN contabilidade.lancamento_empenho as cle
                          ON cle.cod_lote     = cp.cod_lote
                         AND cle.cod_entidade = cp.cod_entidade
                         AND cle.sequencia    = cp.sequencia
                         AND cle.exercicio    = cp.exercicio
                         AND cle.tipo         = cp.tipo

                       WHERE tp.cod_plano = plano_banco.cod_plano
                         and tp.exercicio_plano = plano_banco.exercicio
                         AND cpe.timestamp_anulada IS NULL
                    '||stFiltroPagRest||'
                    GROUP BY tp.exercicio_plano
                           , plano_banco.cod_plano
                           , plano_banco.nom_conta
                           , oe.cod_entidade
                           , cgm.nom_cgm
                           , tp.timestamp
                           , cod_plano_debito
                     ) as plano
                JOIN contabilidade.plano_analitica as cpa
                  ON plano.cod_plano_debito = cpa.cod_plano
                 AND plano.exercicio        = cpa.exercicio

                JOIN contabilidade.plano_conta as cpc
                  ON cpa.cod_conta = cpc.cod_conta
                 AND cpa.exercicio = cpc.exercicio
    ';
    
    IF boUtilizaEstruturalTCE = 'false' THEN
        stSql := stSql || ' AND CPC.cod_estrutural like ''2.1.2.1.1%'' ';
    END IF;
    stSql := stSql || '
    
           UNION ALL
         
              -----------------------------------
              -- ESTORNOS DE PAGAMENTOS RESTOS --
              -----------------------------------
              SELECT plano.cod_plano_debito as plano
                   , CPC.nom_conta as conta
                   , CAST(''0.00'' as NUMERIC(14,2)) as pago
                   , plano.vl_estornado as estornado
                   , plano.nom_conta as conta_banco
                   , '''' as recurso
                   , plano.cod_entidade || '' - '' ||  plano.nom_entidade as entidade
                   , 0 as cod_recurso
                   , CAST(''E'' as varchar) as tipo_despesa
                FROM (
                      SELECT tp.exercicio_plano as exercicio
                           , plano_banco.cod_plano
                           , sum(coalesce(nlpa.vl_anulado, 0.00)) AS vl_estornado
                           , plano_banco.nom_conta as nom_conta
                           , oe.cod_entidade
                           , cgm.nom_cgm as nom_entidade
                           , tp.timestamp
                           , '''' as nome_despesa
                           , contabilidade.fn_recupera_conta_lancamento( CP.exercicio
                                                                       , CP.cod_entidade
                                                                       , CP.cod_lote
                                                                       , CP.tipo
                                                                       , CP.sequencia
                                                                       , ''C''
                                                                       ) as cod_plano_debito
                        FROM (
                              SELECT CPA.cod_plano || '' - '' || CPC.nom_conta as nom_conta
                                   , cpa.cod_plano as cod_plano
                                   , cpa.exercicio
                                   , CPC.cod_estrutural
                                FROM contabilidade.plano_conta as cpc
                                   , contabilidade.plano_analitica as cpa
                               WHERE cpa.cod_conta = cpc.cod_conta
                                 AND cpc.exercicio = cpa.exercicio
                             ) as plano_banco
                           , tesouraria.pagamento AS TP
                  INNER JOIN tesouraria.pagamento_estornado AS TPE
                          ON tpe.exercicio    = tp.exercicio
                         AND tpe.cod_entidade = tp.cod_entidade
                         AND tpe.cod_nota     = tp.cod_nota
                         AND tpe.timestamp    = tp.timestamp

                  INNER JOIN orcamento.entidade as oe
                          ON oe.cod_entidade  = tpe.cod_entidade
                         AND oe.exercicio = tpe.exercicio

                  INNER JOIN sw_cgm as cgm
                          ON oe.numcgm = cgm.numcgm

                        JOIN empenho.nota_liquidacao_paga_anulada as nlpa
                          on nlpa.cod_nota          = tpe.cod_nota
                         AND nlpa.cod_entidade      = tpe.cod_entidade
                         AND nlpa.exercicio         = tpe.exercicio
                         AND nlpa.timestamp         = tpe.timestamp
                         AND nlpa.timestamp_anulada = tpe.timestamp_anulado

                        JOIN empenho.nota_liquidacao_paga as nlp
                          ON nlp.cod_nota     = nlpa.cod_nota
                         AND nlp.cod_entidade = nlpa.cod_entidade
                         AND nlp.exercicio    = nlpa.exercicio
                         AND nlp.timestamp    = nlpa.timestamp

                        JOIN empenho.nota_liquidacao as nl
                          ON nl.cod_nota     = nlp.cod_nota
                         AND nl.exercicio    = nlp.exercicio
                         AND nl.cod_entidade = nlp.cod_entidade
                         AND nl.exercicio_empenho < '||quote_literal(stExercicio)||'

                        JOIN contabilidade.pagamento as cp
                          ON cp.cod_entidade         = nlp.cod_entidade
                         AND cp.exercicio_liquidacao = nlp.exercicio
                         AND cp.cod_nota             = nlp.cod_nota
                         AND cp.timestamp            = nlp.timestamp

                  INNER JOIN contabilidade.pagamento_estorno as cpe
                          ON cpe.cod_entidade         = cp.cod_entidade
                         AND cpe.exercicio            = cp.exercicio
                         AND cpe.sequencia            = cp.sequencia
                         AND cpe.tipo                 = cp.tipo
                         AND cpe.cod_lote             = cp.cod_lote
                         AND cpe.exercicio_liquidacao = nlpa.exercicio
                         AND cpe.cod_nota             = nlpa.cod_nota
                         AND cpe.cod_entidade         = nlpa.cod_entidade
                         AND cpe.timestamp            = nlpa.timestamp
                         AND cpe.timestamp_anulada    = nlpa.timestamp_anulada

                        JOIN contabilidade.lancamento_empenho as cle
                          ON cle.cod_lote     = cp.cod_lote
                         AND cle.cod_entidade = cp.cod_entidade
                         AND cle.sequencia    = cp.sequencia
                         AND cle.exercicio    = cp.exercicio
                         AND cle.tipo         = cp.tipo

                       WHERE tp.cod_plano = plano_banco.cod_plano
                         and tp.exercicio_plano = plano_banco.exercicio 
                    '|| stFiltroPagRestEst ||'
                    GROUP BY tp.exercicio_plano
                           , plano_banco.cod_plano
                           , plano_banco.nom_conta
                           , oe.cod_entidade
                           , cgm.nom_cgm
                           , tp.timestamp
                           , cp.exercicio
                           , cp.cod_entidade
                           , cp.tipo
                           , cp.sequencia
                           , cp.cod_lote
                     ) as plano

                JOIN contabilidade.plano_analitica as cpa
                  ON plano.cod_plano_debito = cpa.cod_plano
                 AND plano.exercicio        = cpa.exercicio

                JOIN contabilidade.plano_conta as cpc
                  ON cpa.cod_conta = cpc.cod_conta
                 AND cpa.exercicio = cpc.exercicio
    ';
    IF boUtilizaEstruturalTCE = 'false' THEN
        stSql := stSql || ' AND CPC.cod_estrutural like ''2.1.2.1.1%'' ';
    END IF;
    
    stSql := stSql || '
             ) as tbl
    GROUP BY plano
           , conta
           , tipo_despesa
           '|| stCampos2 ||'
    ORDER BY tipo_despesa DESC
           , plano
           , conta
           '|| stCampos2 ||'
    ';

    FOR reRegistro IN EXECUTE stSql
        LOOP
            RETURN next reRegistro;
        END LOOP;
    RETURN;

END;
$$ LANGUAGE 'plpgsql';
