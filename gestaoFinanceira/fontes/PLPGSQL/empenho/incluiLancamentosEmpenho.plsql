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

CREATE OR REPLACE FUNCTION public.tmp_empenhos_empenhados(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
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
    varCodConta       Varchar;
    varAux            Varchar;
    bolOk             Boolean;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''Select empenho.cod_empenho
                         , empenho.exercicio
                         , item_pre_empenho.tot_emp as valor
                         , BTrim(To_char( empenho.cod_empenho, ''''99999'''')) || ''''/'''' || empenho.exercicio  as complemento
                         , ''''Empenho n° '''' || BTrim(To_Char(empenho.cod_empenho,''''99999'''')) || ''''/'''' || '''''' || stExercicio || ''''''  as comp_lote
                         , empenho.cod_entidade
                         , To_Char(empenho.dt_empenho, ''''DD/MM/YYYY'''' ) as data_empenho
                         , empenho.cod_pre_empenho as cod_pre_empenho
                         , analitica.cod_plano
                      From empenho.empenho
                          , (Select item_pre_empenho.exercicio, item_pre_empenho.cod_pre_empenho, Sum(item_pre_empenho.vl_total) as tot_emp
                               from empenho.item_pre_empenho
                           Group By item_pre_empenho.exercicio, item_pre_empenho.cod_pre_empenho) as item_pre_empenho
                          , empenho.pre_empenho_despesa
                          , orcamento.conta_despesa  Left Outer Join ( SELECT plano_conta.cod_estrutural, plano_conta.exercicio,
                                                                              plano_analitica.cod_plano
                                                                         FROM contabilidade.plano_conta ,contabilidade.plano_analitica
                                                                        Where plano_conta.exercicio = plano_analitica.exercicio
                                                                          And plano_conta.cod_conta = plano_analitica.cod_conta ) as analitica
                                                     On conta_despesa.exercicio              = analitica.exercicio
                                                    And ''''3.'''' || conta_despesa.cod_estrutural = analitica.cod_estrutural
        Where empenho.exercicio                = '''''' || stExercicio || ''''''
          And empenho.cod_entidade             IN ('''''' || stCodEntidades || '''''')
          And empenho.exercicio                = item_pre_empenho.exercicio
          And empenho.cod_pre_empenho          = item_pre_empenho.cod_pre_empenho
          And item_pre_empenho.exercicio       = pre_empenho_despesa.exercicio
          And item_pre_empenho.cod_pre_empenho = pre_empenho_despesa.cod_pre_empenho
          And pre_empenho_despesa.exercicio    = conta_despesa.exercicio
          And pre_empenho_despesa.cod_conta    = conta_despesa.cod_conta '';

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
        Order by empenho.cod_entidade, empenho.exercicio, empenho.cod_empenho '';


/*

   For recEmpenho In execute stSql LOOP

varCodModalidade := Btrim(Coalesce(PEGAEMPENHOEMPENHOMODALIDADE(  recEmpenho.exercicio, recEmpenho.cod_pre_empenho  ), ''Não informado'' ));

      bolOk := True;
      If varCodModalidade = ''Não informado'' Or varCodModalidade = '''' Then
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - pre_emp => '' || recEmpenho.cod_pre_empenho || '' - entidade =>'' || recEmpenho.cod_entidade;
         bolOk := False;
      End If;

      If recEmpenho.cod_plano Is Null Then
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - pre_emp => '' || recEmpenho.cod_pre_empenho || '' - entidade =>'' || recEmpenho.cod_entidade;
         bolOk := False;
      End If;

      If bolOk Then
         intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                   , recEmpenho.cod_entidade   -- inCodEntidade
                                                   , ''E''                     -- stTipo
                                                   , recEmpenho.comp_lote      -- stNomeLote
                                                   , recEmpenho.data_empenho   -- stDataLote
                                                   );

         intNumSeq  := empenhoemissao( stExercicio               -- EXERCICIO
                                    , recEmpenho.valor           -- VALOR
                                    , recEmpenho.complemento     -- COMPLEMENTO
                                    , intCodLote                 -- CODLOTE
                                    , ''E''                      -- TIPOLOTE
                                    , recEmpenho.cod_entidade    -- CODENTIDADE
                                    , recEmpenho.cod_pre_empenho -- CODPREEMPENHO
                                    );

        Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                         tipo        ,
                                                         sequencia   ,
                                                         exercicio   ,
                                                         cod_entidade,
                                                         estorno     )
                                                Values ( intCodLote
                                                      , ''E''
                                                      , intNumSeq
                                                      , stExercicio
                                                      , recEmpenho.cod_entidade
                                                      , False              );

         Insert Into contabilidade.empenhamento ( exercicio
                                                , sequencia
                                                , tipo
                                                , cod_lote
                                                , cod_entidade
                                                , exercicio_empenho
                                                , cod_empenho       )
                                       Values ( stExercicio
                                                , intNumSeq
                                                , ''E''
                                                , intCodLote
                                                , recEmpenho.cod_entidade
                                                , recEmpenho.exercicio
                                                , recEmpenho.cod_empenho );
      End If;
   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';


CREATE OR REPLACE FUNCTION public.tmp_empenhos_anulacao(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
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
    varCodConta       Varchar;
    varAux            Varchar;
    bolOk             Boolean;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
        Select empenho_anulado_item.exercicio
             , empenho_anulado_item.cod_empenho
             , Btrim(To_char(Date_part( ''''year'''', empenho_anulado_item.timestamp), ''''9999''''))  as anu_exercicio -- Exercicio anulado.
             , Sum(empenho_anulado_item.vl_anulado)                                          as anu_valor -- Valor Anulado.
             , BTrim(To_Char(empenho_anulado_item.cod_empenho,''''99999'''')) || ''''/'''' || empenho_anulado_item.exercicio as comp_lanc -- Complemento lancamento.
             , ''''Anulação Empenho n° '''' || BTrim(To_Char(empenho_anulado_item.cod_empenho,''''99999'''')) || ''''/'''' || '''''' || stExercicio || ''''''         as comp_lote -- Complemento Lote.
             , To_Char(To_date(empenho_anulado_item.timestamp, ''''YYYY-MM-DD''''), ''''DD/MM/YYYY'''')  as anu_data  -- Data anulacao.
             , empenho_anulado_item.cod_entidade  -- Cod. Entidade.
             , empenho_anulado_item.cod_pre_empenho  -- Cod. Pre-empenho.
          From empenho.empenho_anulado_item,
                empenho.empenho
        Where
            empenho_anulado_item.exercicio = empenho.exercicio  AND
            empenho_anulado_item.cod_entidade = empenho.cod_entidade  AND
            empenho_anulado_item.cod_empenho = empenho.cod_empenho  AND

           empenho.exercicio                = '''''' || stExercicio || ''''''
          And empenho.cod_entidade             IN ('''''' || stCodEntidades || '''''')
          And  Date_part( ''''year'''', empenho_anulado_item.timestamp ) = '''''' || stExercicio || '''''' '';

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
         Group By empenho_anulado_item.exercicio,
                  empenho_anulado_item.cod_entidade,
                  empenho_anulado_item.cod_empenho,
                  empenho_anulado_item.cod_pre_empenho,
                  timestamp
         Order By empenho_anulado_item.exercicio, empenho_anulado_item.cod_empenho '';
/*

   For recEmpenho In execute stSql LOOP

varCodModalidade := Btrim(Coalesce(PEGAEMPENHOEMPENHOMODALIDADE(  stExercicio, recEmpenho.cod_pre_empenho  ), ''Não informado'' ));

      If varCodModalidade !=  ''Não informado''  And varCodModalidade != '''' Then
         intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                   , recEmpenho.cod_entidade   -- inCodEntidade
                                                   , ''E''                     -- stTipo
                                                   , recEmpenho.comp_lote      -- stNomeLote
                                                   , recEmpenho.anu_data       -- stDataLote
                                                   );


         intNumSeq  := empenhoemissaoanulacao( stExercicio                   -- EXERCICIO
                                             , recEmpenho.anu_valor           -- VALOR
                                             , recEmpenho.comp_lanc           -- COMPLEMENTO
                                             , intCodLote                     -- CODLOTE
                                             , ''E''                          -- TIPOLOTE
                                             , recEmpenho.cod_entidade        -- CODENTIDADE
                                             , recEmpenho.cod_pre_empenho -- CODPREEMPENHO
                                             );

         Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                         tipo        ,
                                                         sequencia   ,
                                                         exercicio   ,
                                                         cod_entidade,
                                                         estorno     )
                                                Values ( intCodLote
                                                      , ''E''
                                                      , intNumSeq
                                                      , stExercicio
                                                      , recEmpenho.cod_entidade
                                                      , True );


                                        Insert Into contabilidade.empenhamento ( exercicio
                                                , sequencia
                                                , tipo
                                                , cod_lote
                                                , cod_entidade
                                                , exercicio_empenho
                                                , cod_empenho       )
                                       Values ( stExercicio
                                                , intNumSeq
                                                , ''E''
                                                , intCodLote
                                                , recEmpenho.cod_entidade
                                                , recEmpenho.exercicio
                                                , recEmpenho.cod_empenho );
      Else
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - pre_emp => '' || recEmpenho.cod_pre_empenho || '' - entidade =>'' || recEmpenho.cod_entidade;
      End If;
   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';

CREATE OR REPLACE FUNCTION public.tmp_empenhos_anulacao_rp(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
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
    varCodConta       Varchar;
    varAux            Varchar;
    bolOk             Boolean;

    stSql               VARCHAR   := '''';

BEGIN
   stSql := ''
        Select Btrim(To_char(Date_part( ''''year'''', empenho_anulado_item.timestamp), ''''9999'''')) as est_rp_exercicio   -- Exercicio
            , Sum(empenho_anulado_item.vl_anulado)                                           as est_rp_valor     -- Valor Anulado.
            , BTrim(To_Char(empenho_anulado_item.cod_empenho,''''99999'''')) || ''''/'''' || empenho_anulado_item.exercicio  as comp_lanc -- Complemento lancamento.
            , ''''Anulação Empenho n° '''' || BTrim(To_Char(empenho_anulado_item.cod_empenho,''''99999'''')) || ''''/''''|| empenho_anulado_item.exercicio  as comp_lote  -- stNomeLote
            , To_Char(To_date(empenho_anulado_item.timestamp, ''''YYYY-MM-DD''''), ''''DD/MM/YYYY'''')  as data_lote -- stDataLote
            , empenho_anulado_item.cod_entidade                                                              -- Cod. Entidade.
            , empenho_anulado_item.cod_pre_empenho                                                           -- Cod. Pre-empenho.
            , empenho_anulado_item.exercicio                                                                 -- exercicio do empenho.
            , empenho_anulado_item.cod_empenho
         From
            empenho.empenho_anulado_item,
            empenho.empenho
        Where
            empenho_anulado_item.exercicio = empenho.exercicio  AND
            empenho_anulado_item.cod_entidade = empenho.cod_entidade  AND
            empenho_anulado_item.cod_empenho = empenho.cod_empenho  AND

          empenho.cod_entidade             IN ('''''' || stCodEntidades || '''''')

        AND Date_part( ''''year'''', empenho_anulado_item.timestamp)  = '''''' || stExercicio || ''''''
          And To_Number( empenho_anulado_item.exercicio, ''''9999'''' ) < '''''' || stExercicio || '''''' '';

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
        Group By empenho_anulado_item.exercicio,
                 empenho_anulado_item.cod_entidade,
                 empenho_anulado_item.cod_empenho,
                 empenho_anulado_item.cod_pre_empenho,
                 timestamp
        Order by empenho_anulado_item.exercicio, empenho_anulado_item.cod_empenho '';


/*
   For recEmpenho In execute stSql LOOP

      varRestos :=  Btrim( Coalesce(pegaempenhoempenhorestos(recEmpenho.est_rp_exercicio , recEmpenho.cod_pre_empenho ), ''''));

      If varRestos != '''' Then
         intCodLote := contabilidade.fn_insere_lote( stExercicio              -- stExercicio
                                                   , recEmpenho.cod_entidade   -- inCodEntidade
                                                   , ''E''                     -- stTipo
                                                   , recEmpenho.comp_lote      -- stNomeLote
                                                   , recEmpenho.data_lote      -- stDataLote
                                                   );

         intNumSeq  := empenhoestornorestosapagar( recEmpenho.est_rp_exercicio  -- EXERCICIO
                                                , recEmpenho.est_rp_valor       -- VALOR
                                                , recEmpenho.comp_lanc          -- COMPLEMENTO
                                                , intCodLote                    -- CODLOTE
                                                , ''E''                      -- TIPOLOTE
                                                , recEmpenho.cod_entidade    -- CODENTIDADE
                                                , recEmpenho.cod_pre_empenho -- CODPREEMPENHO
                                                , recEmpenho.exercicio
                                                );

         Insert Into contabilidade.lancamento_empenho ( cod_lote    ,
                                                         tipo        ,
                                                         sequencia   ,
                                                         exercicio   ,
                                                         cod_entidade,
                                                         estorno     )
                                                Values ( intCodLote
                                                      , ''E''
                                                      , intNumSeq
                                                      , stExercicio
                                                      , recEmpenho.cod_entidade
                                                      , True              );

         Insert Into contabilidade.empenhamento ( exercicio
                                                , sequencia
                                                , tipo
                                                , cod_lote
                                                , cod_entidade
                                                , exercicio_empenho
                                                , cod_empenho       )
                                       Values ( stExercicio
                                                , intNumSeq
                                                , ''E''
                                                , intCodLote
                                                , recEmpenho.cod_entidade
                                                , recEmpenho.exercicio
                                                , recEmpenho.cod_empenho );
      Else
         varAux := To_Char( recEmpenho.cod_empenho, ''99999'') || '' - exercicio => '' || recEmpenho.exercicio || '' - entidade =>'' || recEmpenho.cod_entidade || '' - pre_empenho => '' || recEmpenho.cod_pre_empenho;
      End If;
   End Loop;
*/
    RETURN true;
END;
'language 'plpgsql';

CREATE OR REPLACE FUNCTION empenho.fn_inclui_lancamentos_empenho(varchar, varchar,varchar, varchar,varchar, varchar) RETURNS BOOLEAN AS '
DECLARE
    stExercicio                     ALIAS FOR $1;
    stCodEntidades                  ALIAS FOR $2;
    inCodEmpenhoInicial             ALIAS FOR $3;
    inCodEmpenhoFinal               ALIAS FOR $4;
    stDataInicial                   ALIAS FOR $5;
    stDataFinal                     ALIAS FOR $6;

    stSql  VARCHAR := '''';

BEGIN

stSql = '' select tmp_empenhos_empenhados( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

stSql = '' select tmp_empenhos_anulacao( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

stSql = '' select tmp_empenhos_anulacao_rp( '''''' || stExercicio || '''''', '''''' || stCodEntidades || '''''', '''''' || inCodEmpenhoInicial || '''''', '''''' || inCodEmpenhoFinal || '''''', '''''' || stDataInicial || '''''', '''''' || stDataFinal || ''''''); '';
EXECUTE stSql;

    RETURN true;
END;
'language 'plpgsql';
