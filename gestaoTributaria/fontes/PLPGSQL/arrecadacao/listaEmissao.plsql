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
* $Id: listaEmissao.plsql 59612 2014-09-02 12:00:51Z gelson $
*
* Caso de uso: uc-5.3.19
* Caso de uso: uc-05.03.19
* Caso de uso: uc-05.03.11
*/

/*                                                       1    2     3    4   5   6   7   8   9  10  11  12  13  14      15*/
CREATE OR REPLACE FUNCTION arrecadacao.fn_lista_emissao(int,varchar,int,int,int,int,int,int,int,int,int,int,int,varchar,varchar)  RETURNS SETOF RECORD AS $$
DECLARE
    inExercicio     ALIAS FOR $1;   
    stNumeracaoAnt  ALIAS FOR $2;   
    inCodGrupo      ALIAS FOR $3;   
    inCodCredito    ALIAS FOR $4;   
    inCodEspecie    ALIAS FOR $5;   
    inCodGenero     ALIAS FOR $6;   
    inCodNatureza   ALIAS FOR $7;   
    inCodCgmInicial ALIAS FOR $8;   
    inCodCgmFinal   ALIAS FOR $9;   
    inCodIIInicial  ALIAS FOR $10;   
    inCodIIFinal    ALIAS FOR $11;   
    inCodIEInicial  ALIAS FOR $12;   
    inCodIEFinal    ALIAS FOR $13;
    stLocInicial    ALIAS FOR $14;
    stLocFinal      ALIAS FOR $15;
    inRetorno       integer;
    reRegistro      RECORD;
    stSql           VARCHAR;
    stFiltro        varchar := '';
    stFiltroCGM     varchar := '';
    stJoins         varchar := '';
    stFrom          varchar := '';
BEGIN
/**
* FUNCIONAMENTO
*   Antes de executar a consulta, é verificado todos os filtros, aonde a tabela de maior proximidade 
*   com o filtro mais exclusivo torna-se a tabela-mãe.
*/

    /* exercicio*/
    if ( inExercicio > 0) then 
        stFiltro := stFiltro||' and calculo.exercicio = '''||inExercicio||'''';
    end if;

    /* cgm */
    if ( inCodCredito > 0 ) then
        stFrom := '
                 from arrecadacao.calculo
           inner join arrecadacao.lancamento_calculo
                   on lancamento_calculo.cod_calculo = calculo.cod_calculo
           inner join arrecadacao.lancamento
                   on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
              ';

        stFiltro := stFiltro||'  and credito.cod_credito = '||inCodCredito::varchar||'
                                  and credito.cod_especie = '||inCodEspecie::varchar||'
                                  and credito.cod_genero  = '||inCodGenero::varchar||'
                                  and credito.cod_natureza= '||inCodNatureza::varchar||'
                        '; 

    end if;

    if ( inCodCgmInicial > 0 ) then
        stFrom := ' from arrecadacao.calculo
                    inner join arrecadacao.calculo_cgm
                             on calculo_cgm.cod_calculo = calculo.cod_calculo
                left join arrecadacao.calculo_grupo_credito
                       on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
               inner join arrecadacao.lancamento_calculo
                       on lancamento_calculo.cod_calculo = calculo.cod_calculo
               inner join arrecadacao.lancamento
                       on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
              ';
    end if;

    if ( inCodCgmInicial > 0 and inCodCgmFinal > 0 ) then
        stFiltroCGM := ' AND  calculo_cgm.numcgm between '||inCodCgmInicial||' and '||inCodCgmFinal||' ';
    elsif ( inCodCgmInicial > 0 ) then
        stFiltroCGM := '  AND calculo_cgm.numcgm = '||inCodCgmInicial||' ';
    end if;

    if ( inCodGrupo > 0 ) then
        stFrom := ' from arrecadacao.calculo_grupo_credito 
               inner join arrecadacao.calculo 
                       on calculo.cod_calculo = calculo_grupo_credito.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
               inner join arrecadacao.lancamento_calculo
                       on lancamento_calculo.cod_calculo = calculo.cod_calculo
               inner join arrecadacao.lancamento
                       on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
              ';
        stFiltro := stFiltro||' and calculo_grupo_credito.cod_grupo = '||inCodGrupo::varchar;
    end if;

    if ( inCodIEInicial > 0 ) then
        stFrom := ' from arrecadacao.cadastro_economico_calculo
               inner join arrecadacao.calculo 
                       on calculo.cod_calculo = cadastro_economico_calculo.cod_calculo
                left join arrecadacao.calculo_grupo_credito
                       on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
               inner join arrecadacao.lancamento_calculo
                       on lancamento_calculo.cod_calculo = calculo.cod_calculo
               inner join arrecadacao.lancamento
                       on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
              ';

        if ( inCodIEFinal > 0) then 
            stFiltro := stFiltro||' and cadastro_economico_calculo.inscricao_economica between '||inCodIEInicial::varchar||' and '||inCodIEFinal::varchar;
        else 
            stFiltro := stFiltro||' and cadastro_economico_calculo.inscricao_economica = '||inCodIEInicial::varchar;
        end if;
    end if;

    if ( stLocInicial != '' ) then
        stFrom := '
                    from imobiliario.localizacao
            inner join imobiliario.lote_localizacao
                    on lote_localizacao.cod_localizacao = localizacao.cod_localizacao
            inner join imobiliario.imovel_lote
                    on imovel_lote.cod_lote = lote_localizacao.cod_lote
            inner join arrecadacao.imovel_calculo
                    on imovel_calculo.inscricao_municipal = imovel_lote.inscricao_municipal
            inner join arrecadacao.calculo
                    on calculo.cod_calculo = imovel_calculo.cod_calculo
            left join arrecadacao.calculo_grupo_credito
                    on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
            inner join arrecadacao.lancamento_calculo
                    on lancamento_calculo.cod_calculo = calculo.cod_calculo
            inner join arrecadacao.lancamento
                    on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
        ';

        if ( stLocFinal != '' ) then
            stFiltro := stFiltro||' and localizacao.codigo_composto between '''||stLocInicial||'''::varchar and '''||stLocFinal||'''::varchar';
        else
            stFiltro := stFiltro||' and localizacao.codigo_composto = '''||stLocInicial||'''::varchar';
        end if;

        if ( inCodIIInicial > 0 ) then
            if ( inCodIIFinal > 0) then
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal between '||inCodIIInicial::varchar||' and '||inCodIIFinal::varchar;
            else
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal = '||inCodIIInicial::varchar;
            end if;
        end if;

        if ( stNumeracaoAnt != '' ) then
            stFiltro := stFiltro||' and carne.numeracao = '||stNumeracaoAnt::varchar;
        end if;

    else

        if ((stNumeracaoAnt = '') and ( inCodIIInicial > 0  )) then
            stFrom := ' from arrecadacao.imovel_calculo
                    inner join arrecadacao.calculo 
                            on calculo.cod_calculo = imovel_calculo.cod_calculo
                     left join arrecadacao.calculo_grupo_credito
                            on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
                    inner join arrecadacao.lancamento_calculo
                            on lancamento_calculo.cod_calculo = calculo.cod_calculo
                    inner join arrecadacao.lancamento
                            on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                ';

            if ( inCodIIFinal > 0) then 
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal between '||inCodIIInicial::varchar||' and '||inCodIIFinal::varchar;
            else 
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal = '||inCodIIInicial::varchar;
            end if;
        end if;
    
        if ((stNumeracaoAnt != '') and ( inCodIIInicial = 0  )) then
            stFrom := ' from arrecadacao.calculo
                   inner join arrecadacao.lancamento_calculo
                           on lancamento_calculo.cod_calculo = calculo.cod_calculo
                    left join arrecadacao.calculo_grupo_credito
                           on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
                   inner join arrecadacao.lancamento
                           on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                ';

            stFiltro := stFiltro||' and carne.numeracao = '||stNumeracaoAnt::varchar;
        end if;
    
        if ((stNumeracaoAnt != '') and ( inCodIIInicial > 0  )) then
            stFrom := '
                        from arrecadacao.imovel_calculo
                  inner join arrecadacao.calculo
                          on calculo.cod_calculo = imovel_calculo.cod_calculo
                   left join arrecadacao.calculo_grupo_credito
                          on calculo_grupo_credito.cod_calculo = calculo.cod_calculo AND calculo_grupo_credito.ano_exercicio = calculo.exercicio
                  inner join arrecadacao.lancamento_calculo
                          on lancamento_calculo.cod_calculo = calculo.cod_calculo
                  inner join arrecadacao.lancamento
                          on lancamento.cod_lancamento = lancamento_calculo.cod_lancamento
                    ';
        
       
            if ( inCodIIFinal > 0) then
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal between '||inCodIIInicial::varchar||' and '||inCodIIFinal::varchar;
            else
                stFiltro := stFiltro||' and imovel_calculo.inscricao_municipal = '||inCodIIInicial::varchar;
            end if;
        
            stFiltro := stFiltro||' and carne.numeracao = '||stNumeracaoAnt::varchar;
        end if;

    end if;

    stSql := '
          select 
                    carne.numeracao
                  , carne.impresso
                  , carne.exercicio::integer
                  , calculo.exercicio::integer as exercicio_calculo
                  , carne.cod_convenio
                  , carne.cod_carteira
                  , credito.cod_convenio as convenio_atual
                  , carteira.cod_carteira as carteira_atual
                  , parcela.cod_parcela
                  , parcela.nr_parcela
                  , arrecadacao.fn_info_parcela(parcela.cod_parcela) as info_parcela
                  , case 
                          when apr.cod_parcela is not null  then
                              case 
                                  when parcela.vencimento < now()::date then
                                      apr.vencimento
                                  else
                                      parcela.vencimento
                              end 
                      else  
                          parcela.vencimento
                      end as vencimento_parcela

                  , case 
                              when apr.cod_parcela is not null  then
                                  case 
                                          when parcela.vencimento < now()::date then
                                              to_char(apr.vencimento,''dd/mm/YYYY'')::varchar 
                                          else 
                                              to_char(parcela.vencimento,''dd/mm/YYYY'')::varchar 
                                   end 
                    else
                  to_char(parcela.vencimento,''dd/mm/YYYY'')::varchar
                     end as vencimento_parcela_br

                  , case 
                              when apr.cod_parcela is not null  then
                                  apr.vencimento
                              else
                                  parcela.vencimento
                    end as vencimento_original

                  , case 
                              when apr.cod_parcela is not null  then
                                  to_char(apr.vencimento,''dd/mm/YYYY'')::varchar
                              else
                                  to_char(parcela.vencimento,''dd/mm/YYYY'')::varchar
                    end as vencimento_original_br

                  , case 
                              when apr.cod_parcela is not null  then
                                  apr.valor
                              else
                                  parcela.valor
                    end as valor_parcela

                  , lancamento.cod_lancamento
                  , lancamento.vencimento as vencimento_lancamento
                  , lancamento.valor as valor_lancamento
                  , CAST((SELECT array_to_string( ARRAY( select numcgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = calculo.cod_calculo)), ''/'' ) ) AS VARCHAR) AS numcgm
                  , CAST((SELECT array_to_string( ARRAY( select nom_cgm from sw_cgm where numcgm IN ( SELECT numcgm FROM arrecadacao.calculo_cgm WHERE cod_calculo = calculo.cod_calculo)), ''/'' ) ) AS VARCHAR) AS nom_cgm
                  , arrecadacao.buscaVinculoLancamentoSemExercicio ( lancamento.cod_lancamento )::varchar as vinculo
                  , arrecadacao.buscaIdVinculo(lancamento.cod_lancamento, carne.exercicio::integer )::varchar as id_vinculo
                  , md5(arrecadacao.buscaVinculoLancamentoSemExercicio ( lancamento.cod_lancamento ))::varchar as chave_vinculo            
                  , arrecadacao.buscaInscricaoLancamento ( lancamento.cod_lancamento )::integer as inscricao

            '||stFrom||'

     INNER JOIN arrecadacao.parcela
                 ON parcela.cod_lancamento = lancamento.cod_lancamento
        LEFT JOIN
                    (
                            SELECT apr.cod_parcela, vencimento, valor
                               FROM arrecadacao.parcela_reemissao apr
                       INNER JOIN (
                                            SELECT cod_parcela, min(timestamp) as timestamp
                                               FROM arrecadacao.parcela_reemissao as x
                                        GROUP BY cod_parcela
                                        ) as apr2
                                    ON apr2.cod_parcela = apr.cod_parcela 
                                  AND apr2.timestamp = apr.timestamp
                    ) as apr
                ON apr.cod_parcela = parcela.cod_parcela

     INNER JOIN arrecadacao.carne 
                 ON carne.cod_parcela = parcela.cod_parcela 

       LEFT JOIN arrecadacao.carne_devolucao
                 ON carne_devolucao.numeracao = carne.numeracao
               AND carne_devolucao.cod_convenio = carne.cod_convenio

       LEFT JOIN arrecadacao.pagamento
                 ON pagamento.numeracao = carne.numeracao
               AND pagamento.cod_convenio = carne.cod_convenio

     INNER JOIN monetario.credito
                 ON credito.cod_credito = calculo.cod_credito
               AND credito.cod_especie = calculo.cod_especie
               AND credito.cod_genero  = calculo.cod_genero
               AND credito.cod_natureza= calculo.cod_natureza                                                        

       LEFT JOIN monetario.carteira
                ON carteira.cod_convenio = credito.cod_convenio


      LEFT JOIN (
                                SELECT
                                            exercicio
                                            , valor
                                    FROM
                                            administracao.configuracao
                                WHERE parametro = ''baixa_manual_unica''
                            ) as baixa_manual_unica
              ON baixa_manual_unica.exercicio = carne.exercicio


    WHERE carne_devolucao.numeracao is null
         AND pagamento.numeracao is null
         AND ( CASE WHEN parcela.nr_parcela = 0 AND baixa_manual_unica.valor = ''aceita'' THEN
                false
            ELSE
                true
            END )
          AND calculo.cod_calculo = ( select alc.cod_calculo
                                      from arrecadacao.lancamento_calculo alc
                                     where alc.cod_lancamento = lancamento.cod_lancamento
                                  order by cod_calculo desc limit 1 )';

    if ( stNumeracaoAnt = '' ) then
          stSql := stSql||' and  carne.numeracao = ( select ultima_numeracao.numeracao
                                    from arrecadacao.carne as ultima_numeracao
                                   where ultima_numeracao.cod_parcela = parcela.cod_parcela
                                order by timestamp desc limit 1 )';
    end if;
         stSql := stSql||' '||stFiltro||' '||stFiltroCGM;
/* ordenar */
         stSql := stSql||' order by lancamento.cod_lancamento desc, parcela.nr_parcela asc   '; 

    FOR reRegistro IN EXECUTE stSql LOOP
        return next reRegistro;
    END LOOP;
    return;
END;
$$ LANGUAGE 'plpgsql';
