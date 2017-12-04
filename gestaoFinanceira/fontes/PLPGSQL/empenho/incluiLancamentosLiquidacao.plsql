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
* $Revision: 12203 $
* $Name$
* $Author: cleisson $
* $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
*
* Casos de uso: uc-02.00.00
*/

/*
$Log$
Revision 1.5  2006/07/05 20:37:38  cleisson
Adicionada tag Log aos arquivos

*/

CREATE OR REPLACE FUNCTION public.tmp_empenhos_liquidacao(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

   recEmpenho        Record;
   intCodLote        Integer;
   intNumSeq         Integer;

   varCodModalidade  Varchar;
   varCodTpCredor    Varchar;

   varAux            Varchar;
   bolOk             Boolean;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
        Select nota_liquidacao.exercicio
             , nota_liquidacao.cod_nota
             , nota_liquidacao.exercicio       as liq_exercicio      -- EXERCICIO
             , nota_liquidacao_item.tot_liq    as liq_valor          -- VALOR
             , BTrim(To_Char(nota_liquidacao.cod_empenho,''''99999''''))   || ''''/'''' || empenho.exercicio as comp_lanc -- Complemento lancamento.
             , ''''Liquidação Empenho '''' || BTrim(To_char( nota_liquidacao.cod_empenho, ''''99999''''))|| ''''/'''' || nota_liquidacao.exercicio_empenho || '''' - Nota:'''' || nota_liquidacao.cod_nota || ''''/'''' || nota_liquidacao.exercicio as comp_lote
             , To_Char(nota_liquidacao.dt_liquidacao, ''''DD/MM/YYYY'''' )  as liq_data
             , nota_liquidacao.cod_entidade                  -- CODENTIDADE
             , nota_liquidacao.cod_nota                      -- CODNOTA
             , conta_despesa.cod_estrutural                  -- CLASDESPESA
             , despesa.num_orgao                             -- NUMORGAO
             , empenho.cod_empenho
             , empenho.exercicio
         From empenho.nota_liquidacao
            , (Select nota_liquidacao_item.exercicio   , nota_liquidacao_item.cod_nota,
                      nota_liquidacao_item.cod_entidade, Sum(nota_liquidacao_item.vl_total) as tot_liq
                 from empenho.nota_liquidacao_item
             Group By nota_liquidacao_item.exercicio, nota_liquidacao_item.cod_nota, nota_liquidacao_item.cod_entidade) as nota_liquidacao_item
            , empenho.empenho
            , empenho.pre_empenho_despesa
            , orcamento.conta_despesa  Left Outer Join ( SELECT plano_conta.cod_estrutural, plano_conta.exercicio,
                                                                plano_analitica.cod_plano
                                                           FROM contabilidade.plano_conta ,contabilidade.plano_analitica
                                                          Where plano_conta.exercicio = plano_analitica.exercicio
                                                            And plano_conta.cod_conta = plano_analitica.cod_conta ) as analitica
                                         On conta_despesa.exercicio                = analitica.exercicio
                                        And ''''3.'''' || conta_despesa.cod_estrutural = analitica.cod_estrutural
           , orcamento.despesa
        Where nota_liquidacao.exercicio         = '''''' || stExercicio || ''''''
          And nota_liquidacao.exercicio_empenho = '''''' || stExercicio || ''''''
          And nota_liquidacao.cod_entidade      IN ('''''' || stCodEntidades || '''''')
          And nota_liquidacao.exercicio         = nota_liquidacao_item.exercicio
          And nota_liquidacao.cod_nota          = nota_liquidacao_item.cod_nota
          And nota_liquidacao.cod_entidade      = nota_liquidacao_item.cod_entidade
          And nota_liquidacao.exercicio_empenho = empenho.exercicio
          And nota_liquidacao.cod_empenho       = empenho.cod_empenho
          And nota_liquidacao.cod_entidade       = empenho.cod_entidade
          And empenho.exercicio                 = pre_empenho_despesa.exercicio
          And empenho.cod_pre_empenho           = pre_empenho_despesa.cod_pre_empenho
          And pre_empenho_despesa.exercicio     = conta_despesa.exercicio
          And pre_empenho_despesa.cod_conta     = conta_despesa.cod_conta
          And pre_empenho_despesa.exercicio     = despesa.exercicio
          And pre_empenho_despesa.cod_despesa   = despesa.cod_despesa
          And analitica.cod_plano Is Not Null '';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'''') then
        stSql := stSql || '' And empenho.cod_empenho >= '' || inCodEmpenhoInicial || '' '';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'''') then
        stSql := stSql || '' And empenho.cod_empenho <= '' || inCodEmpenhoFinal || '' '';
    end if;

    if (stDataInicial is not null and stDataInicial<>'''') then
        stSql := stSql || '' And empenho.dt_empenho >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') '';
    end if;

    if (stDataFinal is not null and stDataFinal<>'''') then
        stSql := stSql || '' And empenho.dt_empenho <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') '';
    end if;

    stSql := stSql || ''
                  Order by empenho.exercicio, empenho.cod_empenho '';


/*

   For recEmpenho In execute stSql LOOP

     bolOk := True;
      varCodModalidade := Btrim(Coalesce(pegaempenholiquidacaomodalidade(  stExercicio , recEmpenho.cod_nota , recEmpenho.cod_entidade ), ''Não informado'' ));
      varCodTpCredor   := Btrim(Coalesce(pegaempenholiquidacaotipocredor(  stExercicio , recEmpenho.cod_nota , recEmpenho.cod_entidade ), ''Não informado'' ));

      If varCodModalidade = ''Não informado'' Or varCodModalidade = '''' Then
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' cod. nota =>'' || recEmpenho.cod_nota;
         bolOk := False;
      End If;

      If varCodTpCredor =  ''Não informado'' Or varCodTpCredor = '''' Then
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' cod. nota =>'' || recEmpenho.cod_nota;
         bolOk := False;
      End If;


      If bolOk Then
         intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                   , recEmpenho.cod_entidade   -- inCodEntidade
                                                   , ''L''                     -- stTipo
                                                   , recEmpenho.comp_lote      -- stNomeLote
                                                   , recEmpenho.liq_data       -- stDataLote
                                                   );

     intNumSeq  := empenholiquidacao( stExercicio                   -- EXERCICIO
                                       , recEmpenho.liq_valor           -- VALOR
                                       , recEmpenho.comp_lanc           -- COMPLEMENTO
                                       , intCodLote                     -- CODLOTE
                                       , ''L''                          -- TIPOLOTE
                                       , recEmpenho.cod_entidade        -- CODENTIDADE
                                       , recEmpenho.cod_nota            -- CODNOTA
                                       , recEmpenho.cod_estrutural      -- CLASDESPESA
                                       , 900                            --  CODHISTORICOPATRIMON
                                       , recEmpenho.num_orgao           -- NUMORGAO
                                       );

         Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                      tipo        ,
                                                      sequencia   ,
                                                      exercicio   ,
                                                      cod_entidade,
                                                      estorno     )
                                             Values ( intCodLote
                                                      , ''L''
                                                      , intNumSeq
                                                      , stExercicio
                                                      , recEmpenho.cod_entidade
                                                      , False );

         Insert Into contabilidade.liquidacao ( exercicio
                                             , sequencia
                                             , tipo
                                             , cod_lote
                                             , cod_entidade
                                             , exercicio_liquidacao
                                             , cod_nota       )
                                       Values ( stExercicio
                                             , intNumSeq
                                             , ''L''
                                             , intCodLote
                                             , recEmpenho.cod_entidade
                                             , recEmpenho.exercicio
                                             , recEmpenho.cod_nota );
      End If;

End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';

CREATE OR REPLACE FUNCTION public.tmp_empenhos_liquidacao_anulacao(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

   recEmpenho        Record;
   intCodLote        Integer;
   intNumSeq         Integer;

   varCodModalidade  Varchar;
   varAux            Varchar;
   bolOk             Boolean;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
        Select nota_liquidacao.exercicio                                                        -- EXERCICIO
             , nota_liquidacao_item_anulado.tot_liq    as anu_liq_valor                         -- VALOR
             , BTrim(To_Char(nota_liquidacao.cod_empenho,''''99999''''))   || ''''/'''' || empenho.exercicio  as comp_lanc -- Complemento lancamento.
             , ''''Anulação Liquidação Empenho nº '''' || BTrim(To_char( nota_liquidacao.cod_empenho, ''''99999'''')) || ''''/'''' || nota_liquidacao.exercicio_empenho || '''' - Nota:'''' || nota_liquidacao.cod_nota || ''''/'''' || nota_liquidacao.exercicio  as comp_lote -- stNomeLote-
             , To_Char(To_date(nota_liquidacao_item_anulado.timestamp, ''''YYYY-MM-DD''''), ''''DD/MM/YYYY'''' )  as anu_liq_data      -- stDataLote
             , nota_liquidacao.cod_entidade                  -- CODENTIDADE
             , nota_liquidacao.cod_nota                      -- CODNOTA
             , conta_despesa.cod_estrutural                  -- CLASDESPESA
             , despesa.num_orgao                             -- NUMORGAO
             , empenho.cod_empenho
             , empenho.exercicio         as empenho_exercicio
        From empenho.nota_liquidacao
           , (Select nota_liquidacao_item_anulado.exercicio   , nota_liquidacao_item_anulado.cod_nota,
                     nota_liquidacao_item_anulado.cod_entidade, nota_liquidacao_item_anulado.timestamp,
                     Sum(nota_liquidacao_item_anulado.vl_anulado) as tot_liq
                from empenho.nota_liquidacao_item_anulado
               Where Date_part(''''YEAR'''',To_date(nota_liquidacao_item_anulado.timestamp, ''''YYYY-MM-DD'''')) = '''''' || stExercicio || ''''''
            Group By nota_liquidacao_item_anulado.exercicio, nota_liquidacao_item_anulado.cod_nota,
                     nota_liquidacao_item_anulado.cod_entidade, nota_liquidacao_item_anulado.timestamp) as nota_liquidacao_item_anulado
           , empenho.empenho
           , empenho.pre_empenho_despesa
           , orcamento.conta_despesa  Left Outer Join ( SELECT plano_conta.cod_estrutural, plano_conta.exercicio,
                                                               plano_analitica.cod_plano
                                                          FROM contabilidade.plano_conta ,contabilidade.plano_analitica
                                                         Where plano_conta.exercicio = plano_analitica.exercicio
                                                          And plano_conta.cod_conta = plano_analitica.cod_conta ) as analitica
                                                   On conta_despesa.exercicio                = analitica.exercicio
                                                  And ''''3.'''' || conta_despesa.cod_estrutural = analitica.cod_estrutural
           , orcamento.despesa
        Where nota_liquidacao.exercicio         = '''''' || stExercicio || ''''''
          And nota_liquidacao.cod_entidade      IN ('''''' || stCodEntidades || '''''')
         And nota_liquidacao.exercicio         = nota_liquidacao_item_anulado.exercicio
         And nota_liquidacao.cod_nota          = nota_liquidacao_item_anulado.cod_nota
         And nota_liquidacao.cod_entidade      = nota_liquidacao_item_anulado.cod_entidade
         And nota_liquidacao.exercicio_empenho = empenho.exercicio
         And nota_liquidacao.cod_empenho       = empenho.cod_empenho
         And nota_liquidacao.cod_entidade      = empenho.cod_entidade
         And empenho.exercicio                 = '''''' || stExercicio || ''''''
         And empenho.exercicio                 = pre_empenho_despesa.exercicio
         And empenho.cod_pre_empenho           = pre_empenho_despesa.cod_pre_empenho
         And pre_empenho_despesa.exercicio     = conta_despesa.exercicio
         And pre_empenho_despesa.cod_conta     = conta_despesa.cod_conta
         And pre_empenho_despesa.exercicio     = despesa.exercicio
         And pre_empenho_despesa.cod_despesa   = despesa.cod_despesa
         And analitica.cod_plano Is Not Null '';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'''') then
        stSql := stSql || '' And empenho.cod_empenho >= '' || inCodEmpenhoInicial || '' '';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'''') then
        stSql := stSql || '' And empenho.cod_empenho <= '' || inCodEmpenhoFinal || '' '';
    end if;

    if (stDataInicial is not null and stDataInicial<>'''') then
        stSql := stSql || '' And empenho.dt_empenho >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') '';
    end if;

    if (stDataFinal is not null and stDataFinal<>'''') then
        stSql := stSql || '' And empenho.dt_empenho <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') '';
    end if;

    stSql := stSql || ''
        Order by nota_liquidacao.cod_empenho, nota_liquidacao.exercicio_empenho '';

/*

   For recEmpenho In execute stSql LOOP
      bolOk := True;
       varCodModalidade := Btrim(Coalesce(pegaempenholiquidacaomodalidade(  stExercicio , recEmpenho.cod_nota , recEmpenho.cod_entidade ), ''Não informado'' ));

       If varCodModalidade = ''Não informado'' Or varCodModalidade = '''' Then
          varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.empenho_exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' cod. nota =>'' || recEmpenho.cod_nota;
          bolOk := False;
       End If;

       If bolOk Then
          intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                    , recEmpenho.cod_entidade   -- inCodEntidade
                                                    , ''L''                     -- stTipo
                                                    , recEmpenho.comp_lote      -- stNomeLote
                                                    , recEmpenho.anu_liq_data   -- stDataLote
                                                    );

          intNumSeq  := EmpenhoAnulacaoLiquidacao( stExercicio                   -- EXERCICIO
                                                 , recEmpenho.anu_liq_valor       -- VALOR
                                                 , recEmpenho.comp_lanc           -- COMPLEMENTO
                                                 , intCodLote                     -- CODLOTE
                                                 , ''L''                          -- TIPOLOTE
                                                 , recEmpenho.cod_entidade        -- CODENTIDADE
                                                 , recEmpenho.cod_nota            -- CODNOTA
                                                 , recEmpenho.cod_estrutural      -- CLASDESPESA
                                                 , 899                            --  CODHISTORICOPATRIMON
                                                 , recEmpenho.num_orgao           -- NUMORGAO
                                                 );
          Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                       tipo        ,
                                                       sequencia   ,
                                                       exercicio   ,
                                                       cod_entidade,
                                                       estorno     )
                                              Values ( intCodLote
                                                       , ''L''
                                                       , intNumSeq
                                                       , stExercicio
                                                       , recEmpenho.cod_entidade
                                                       , True );

          Insert Into contabilidade.liquidacao ( exercicio
                                              , sequencia
                                              , tipo
                                              , cod_lote
                                              , cod_entidade
                                              , exercicio_liquidacao
                                              , cod_nota       )
                                        Values ( stExercicio
                                              , intNumSeq
                                              , ''L''
                                              , intCodLote
                                              , recEmpenho.cod_entidade
                                              , recEmpenho.exercicio
                                              , recEmpenho.cod_nota );
       End If;

   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';

CREATE OR REPLACE FUNCTION public.tmp_empenhos_liquidacao_rp(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

   recEmpenho        Record;
   intCodLote        Integer;
   intNumSeq         Integer;
   varAux            Varchar;
   varRestos         Varchar;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
    Select empenho.exercicio                                                        -- EXERCICIO
            , empenho.cod_pre_empenho
            , nota_liquidacao.cod_empenho
            , nota_liquidacao_item.tot_liq     as liq_rp_valor                          -- VALOR
            , nota_liquidacao.exercicio        as exercicio_liquidacao
            , BTrim(To_Char(nota_liquidacao.cod_empenho,''''99999''''))               || ''''/'''' || empenho.exercicio  as comp_lanc -- Complemento lancamento.
            , ''''Liquidação Empenho RP nº '''' || Btrim(To_char( nota_liquidacao.cod_empenho, ''''99999'''')) || ''''/'''' || nota_liquidacao.exercicio_empenho || '''' - Nota:'''' || nota_liquidacao.cod_nota || ''''/'''' || nota_liquidacao.exercicio   as comp_lote -- stNomeLote
            , To_Char(nota_liquidacao.dt_liquidacao, ''''DD/MM/YYYY'''' )   as liq_rp_data       -- stDataLote
            , nota_liquidacao.cod_entidade                  -- CODENTIDADE
            , nota_liquidacao.cod_nota                      -- CODNOTA
       From empenho.nota_liquidacao
          , (Select nota_liquidacao_item.exercicio   , nota_liquidacao_item.cod_nota,
                    nota_liquidacao_item.cod_entidade, Sum(nota_liquidacao_item.vl_total) as tot_liq
               from empenho.nota_liquidacao_item
           Group By nota_liquidacao_item.exercicio, nota_liquidacao_item.cod_nota, nota_liquidacao_item.cod_entidade) as nota_liquidacao_item
          , empenho.empenho
    Where nota_liquidacao.exercicio         = '''''' || stExercicio || ''''''
      And nota_liquidacao.cod_entidade      IN ('''''' || stCodEntidades || '''''')
      And nota_liquidacao.exercicio             = nota_liquidacao_item.exercicio
      And nota_liquidacao.cod_nota              = nota_liquidacao_item.cod_nota
      And nota_liquidacao.cod_entidade          = nota_liquidacao_item.cod_entidade
      And nota_liquidacao.exercicio_empenho     = empenho.exercicio
      And nota_liquidacao.cod_entidade          = empenho.cod_entidade
      And nota_liquidacao.cod_empenho           = empenho.cod_empenho
      And To_Number(empenho.exercicio,''''9999'''') < To_Number( '''''' || stExercicio || '''''',''''9999'''') '';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'''') then
        stSql := stSql || '' And empenho.cod_empenho >= '' || inCodEmpenhoInicial || '' '';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'''') then
        stSql := stSql || '' And empenho.cod_empenho <= '' || inCodEmpenhoFinal || '' '';
    end if;

    if (stDataInicial is not null and stDataInicial<>'''') then
        stSql := stSql || '' And empenho.dt_empenho >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') '';
    end if;

    if (stDataFinal is not null and stDataFinal<>'''') then
        stSql := stSql || '' And empenho.dt_empenho <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') '';
    end if;

    stSql := stSql || ''
        Order By empenho.exercicio,  nota_liquidacao.cod_empenho '';


/*

   For recEmpenho In execute stSql LOOP
      --varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' cod. nota =>'' || recEmpenho.cod_nota;

      varRestos :=  Btrim( Coalesce(pegaempenholiquidacaorestos(stExercicio, recEmpenho.cod_nota , recEmpenho.cod_entidade), ''''));

      If varRestos != '''' Then
         intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                   , recEmpenho.cod_entidade   -- inCodEntidade
                                                   , ''L''                     -- stTipo
                                                   , recEmpenho.comp_lote      -- stNomeLote
                                                   , recEmpenho.liq_rp_data    -- stDataLote
                                                   );

         intNumSeq  := EmpenhoLiquidacaoRestosAPagar( stExercicio               -- EXERCICIO
                                                   , recEmpenho.liq_rp_valor        -- VALOR
                                                   , recEmpenho.comp_lanc           -- COMPLEMENTO
                                                   , intCodLote                     -- CODLOTE
                                                   , ''L''                          -- TIPOLOTE
                                                   , recEmpenho.cod_entidade        -- CODENTIDADE
                                                   , recEmpenho.cod_nota            -- CODNOTA
                                                   , recEmpenho.exercicio           -- EXERCRP
                                                   );
         Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                      tipo        ,
                                                      sequencia   ,
                                                      exercicio   ,
                                                      cod_entidade,
                                                      estorno     )
                                             Values ( intCodLote
                                                      , ''L''
                                                      , intNumSeq
                                                      , stExercicio
                                                      , recEmpenho.cod_entidade
                                                      , False );

         Insert Into contabilidade.liquidacao ( exercicio
                                             , sequencia
                                             , tipo
                                             , cod_lote
                                             , cod_entidade
                                             , exercicio_liquidacao
                                             , cod_nota       )
                                       Values ( stExercicio
                                             , intNumSeq
                                             , ''L''
                                             , intCodLote
                                             , recEmpenho.cod_entidade
                                             , recEmpenho.exercicio_liquidacao
                                             , recEmpenho.cod_nota );
      Else
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' cod. nota =>'' || recEmpenho.cod_nota;
      End If;
   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';

CREATE OR REPLACE FUNCTION public.tmp_empenhos_liquidacao_anulacao_rp(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

   recEmpenho        Record;
   intCodLote        Integer;
   intNumSeq         Integer;
   varAux            Varchar;
   varRestos         Varchar;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
    Select nota_liquidacao_item_anulado.exercicio        as  exercicio_da_Liq   -- liq_anu_rp_exer    -- EXERCICIO
          , To_Char(To_date(nota_liquidacao_item_anulado.timestamp, ''''YYYY-MM-DD''''), ''''DD/MM/YYYY'''' )  as liq_anu_rp_data        -- stDataLote
          , empenho.exercicio                             -- Exercicio do empenho.
          , empenho.cod_empenho
          , empenho.cod_pre_empenho
          , nota_liquidacao.exercicio                                                                  as nota_exercicio
          , nota_liquidacao_item_anulado.tot_liq                                                       as liq_anu_rp_valor   -- VALOR
          , BTrim(To_Char(nota_liquidacao.cod_empenho,''''99999'''')) || ''''/'''' || empenho.exercicio  as comp_lanc -- Complemento lancamento.
          , ''''Anulação Liquidação Empenho RP nº '''' || BTrim(To_char(nota_liquidacao.cod_empenho,''''99999''''))|| ''''/'''' || nota_liquidacao.exercicio_empenho || '''' - Nota:'''' || nota_liquidacao.cod_nota || ''''/'''' || nota_liquidacao.exercicio   as comp_lote -- stNomeLote-
          , nota_liquidacao.cod_entidade                  -- CODENTIDADE
          , nota_liquidacao.cod_nota                      -- CODNOTA
       From empenho.nota_liquidacao
          , (Select nota_liquidacao_item_anulado.exercicio   , nota_liquidacao_item_anulado.cod_nota,
                    nota_liquidacao_item_anulado.cod_entidade, nota_liquidacao_item_anulado.timestamp,
                    Sum(nota_liquidacao_item_anulado.vl_anulado) as tot_liq
               from empenho.nota_liquidacao_item_anulado
              Where Date_part(''''YEAR'''',To_date(nota_liquidacao_item_anulado.timestamp, ''''YYYY-MM-DD'''')) = '''''' || stExercicio || ''''''
               Group By nota_liquidacao_item_anulado.exercicio, nota_liquidacao_item_anulado.cod_nota,
                        nota_liquidacao_item_anulado.cod_entidade, nota_liquidacao_item_anulado.timestamp) as nota_liquidacao_item_anulado
          , empenho.empenho
       Where nota_liquidacao.exercicio           = nota_liquidacao_item_anulado.exercicio
        And nota_liquidacao.cod_entidade      IN ('''''' || stCodEntidades || '''''')
         And nota_liquidacao.cod_nota            = nota_liquidacao_item_anulado.cod_nota
         And nota_liquidacao.cod_entidade        = nota_liquidacao_item_anulado.cod_entidade
         And nota_liquidacao.exercicio_empenho   = empenho.exercicio
         And nota_liquidacao.cod_empenho         = empenho.cod_empenho
         And nota_liquidacao.cod_entidade        = empenho.cod_entidade
         And To_number(empenho.exercicio,''''9999'''') < To_number( '''''' || stExercicio || '''''',''''9999'''') '';

    if (inCodEmpenhoInicial is not null and inCodEmpenhoInicial<>'''') then
        stSql := stSql || '' And empenho.cod_empenho >= '' || inCodEmpenhoInicial || '' '';
    end if;

    if (inCodEmpenhoFinal is not null and inCodEmpenhoFinal<>'''') then
        stSql := stSql || '' And empenho.cod_empenho <= '' || inCodEmpenhoFinal || '' '';
    end if;

    if (stDataInicial is not null and stDataInicial<>'''') then
        stSql := stSql || '' And empenho.dt_empenho >= to_date('''''' || stDataInicial || '''''',''''dd/mm/yyyy'''') '';
    end if;

    if (stDataFinal is not null and stDataFinal<>'''') then
        stSql := stSql || '' And empenho.dt_empenho <= to_date('''''' || stDataFinal || '''''',''''dd/mm/yyyy'''') '';
    end if;

    stSql := stSql || ''
        Order By empenho.exercicio,  nota_liquidacao.cod_empenho '';


/*

   For recEmpenho In execute stSql LOOP
       varRestos :=  Btrim( Coalesce(pegaempenholiquidacaorestos(recEmpenho.exercicio_da_Liq , recEmpenho.cod_nota , recEmpenho.cod_entidade), ''''));

       If varRestos != '''' Then
          intCodLote := contabilidade.fn_insere_lote( stExercicio                  -- stExercicio
                                                    , recEmpenho.cod_entidade       -- inCodEntidade
                                                    , ''L''                         -- stTipo
                                                    , recEmpenho.comp_lote          -- stNomeLote
                                                    , recEmpenho.liq_anu_rp_data    -- stDataLote
                                                    );

          intNumSeq  := empenhoanulacaoliquidacaorestosapagar( stExercicio                  -- EXERCICIO
                                                             , recEmpenho.liq_anu_rp_valor    -- VALOR
                                                             , recEmpenho.comp_lanc           -- COMPLEMENTO
                                                             , intCodLote                     -- CODLOTE
                                                             , ''L''                          -- TIPOLOTE
                                                             , recEmpenho.cod_entidade        -- CODENTIDADE
                                                             , recEmpenho.cod_nota            -- CODNOTA
                                                             , recEmpenho.exercicio           -- EXERCRP
                                                             , recEmpenho.exercicio_da_Liq );                 -- EXERCLIQUIDACAO

          Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                          tipo        ,
                                                          sequencia   ,
                                                          exercicio   ,
                                                          cod_entidade,
                                                          estorno     )
                                                 Values ( intCodLote
                                                       , ''L''
                                                       , intNumSeq
                                                       , stExercicio
                                                       , recEmpenho.cod_entidade
                                                       , True );

          Insert Into contabilidade.liquidacao ( exercicio
                                              , sequencia
                                              , tipo
                                              , cod_lote
                                              , cod_entidade
                                              , exercicio_liquidacao
                                              , cod_nota       )
                                        Values ( stExercicio
                                              ,  intNumSeq
                                              , ''L''
                                              , intCodLote
                                              , recEmpenho.cod_entidade
                                              , recEmpenho.nota_exercicio
                                              , recEmpenho.cod_nota );
       Else
          varAux := To_Char( recEmpenho.cod_empenho, ''999999'') || '' exerc_emp:'' || recEmpenho.exercicio || '' entidade:'' || recEmpenho.cod_entidade || '' cod.nota:'' || recEmpenho.cod_nota || '' Exer.Liq:'' || recEmpenho.exercicio_da_Liq;
       End If;

   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';



CREATE OR REPLACE FUNCTION empenho.fn_inclui_lancamentos_liquidacao(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

    stSql  VARCHAR := '''';

BEGIN

stSql = '' select tmp_empenhos_liquidacao( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

stSql = '' select tmp_empenhos_liquidacao_anulacao( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

stSql = '' select tmp_empenhos_liquidaca_rp( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

stSql = '' select tmp_empenhos_liquidaca_anulacao_rp( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

    RETURN true;
END;
'language 'plpgsql';
