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
* Script de função PLPGSQL - Relatório STN - Anexo1
*
* URBEM Soluções de Gestão Pública Ltda
* www.urbem.cnm.org.br
*
* $Revision: 59612 $
* $Name$
* $Author: gelson $
* $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $
*
* Casos de uso:
*/

/*
$Log:
*/


/*
 * INFORMAÇÕES PARA A PROGRAMAÇÂO NO PHP
 *
 * *** Preencher as linhas abaixo usando o PHP da seguinte maneira
 * Se o valor retornado para a linha 14 for negativo colocar um traço no lugar e informar o valor na linha 20
 * A linha 22 é a linha 1 menos a linha 14
 * A linha 23 é a linha deve ser obtida pela PL do Anexo III
 * A linha 24 é a linha 1 dividida pela linha 23
 * A linha 25 é a linha 14 dividida pela linha 23
 * A linha 35 é a linha 26 menos a linha 29
 *
 */

/*
 * INFORMAÇÕES SOBRE A PL
 *
 * As linhas 7, 12, 13, 17, 27, 31, 33
 * não foi possível determinar de qual contas retirar os lançamentos
 *
 */


create or replace function stn.fn_rgf_anexo2( varchar, integer ) returns setof record as $$
declare
    stCodEntidades               alias for $1;
    inExercicio                  alias for $2;
    inExercicioAnterior          integer := inExercicio-1;
    stInicioExercicioAnterior    varchar := inExercicioAnterior || '01-01';
    stInicioPrimeiroQuadrimestre varchar := inExercicio   || '-01-01';
    stInicioSegundoQuadrimestre  varchar := inExercicio   || '-05-01';
    stInicioTerceiroQuadrimestre varchar := inExercicio   || '-09-01';
    stFimTerceiroQuadrimestre    varchar := inExercicio+1 || '-01-01';
    stSql                        varchar := '';
    reRegistro                   record;
begin


    stSql = '
                /*
                 * SELECIONA TODAS CONTAS DO REL
                 */
                create temporary table tmp_plano_contas as
                select cod_conta
                     , exercicio
                     , cod_estrutural
                  from contabilidade.plano_conta
                 where exercicio IN ( '|| inExercicio  ||', '|| inExercicioAnterior ||' )
                   and (
                            -- divida mobiliaria
                            cod_estrutural like ''2.2.2.1.1%''
                         or cod_estrutural like ''2.2.2.2.1%''

                            -- divida contratual
                         or cod_estrutural like ''2.1.2.2.2.02%''
                         or cod_estrutural like ''2.1.2.3.1.02%''
                         or cod_estrutural like ''2.1.2.3.2.02%''
                         or cod_estrutural like ''2.2.2.1.2%''
                         or cod_estrutural like ''2.2.2.2.2%''

                            -- precatórios posteriores a 5.5.2000
                         or cod_estrutural like ''2.1.2.1.7.05%''

                            -- operacoes de credito inferiores a 12 meses
                         or cod_estrutural like ''2.1.2.3.1.02.02.03%''

                            -- parcelamento do inss
                         or cod_estrutural like ''2.2.2.1.2.00.100.01%''

                            -- parcelamento do pasep
                         or cod_estrutural like ''2.2.2.4.9.00.00.03%''

                            -- parcelamento do fgts
                         or cod_estrutural like ''2.2.2.4.9.00.00.02%''

                            -- ativo financeiro (ativo circulante)
                         or cod_estrutural like ''1.1%''

                            -- precatórios anteriores a 5.5.2000
                         or cod_estrutural like ''2.1.2.1.7.04%''

                            -- outras obrigações
                         or cod_estrutural like ''2.1.2.1.9.99%''
                         or cod_estrutural like ''2.1.2.9%''
                         or cod_estrutural like ''2.2.2.3.9%''
                         or cod_estrutural like ''2.2.2.4.9%''
                       );
                create index unq_tmp_plano_contas on tmp_plano_contas ( cod_conta, exercicio );';

                execute stSql;

                stSql = '
                /*
                 * LIGACAO PLANO ANALITICA
                 */
                create temporary table tmp_plano_analitica as
                select pa.cod_plano
                     , pa.exercicio
                     , contas.cod_conta
                     , contas.cod_estrutural
                  from contabilidade.plano_analitica as pa
                  join tmp_plano_contas as contas
                    on (     contas.cod_conta = pa.cod_conta
                         and contas.exercicio = pa.exercicio
                       );
                create index unq_tmp_plano_analitica on tmp_plano_analitica ( cod_plano, exercicio );';

                execute stSql;

                stSql = '
                /*
                 * CREDITOS
                 */
                create temporary table tmp_conta_credito as
                select cc.cod_lote
                     , cc.tipo
                     , cc.sequencia
                     , cc.exercicio
                     , cc.tipo_valor
                     , cc.cod_entidade
                     , tpa.cod_estrutural
                  from contabilidade.conta_credito as cc
                  join tmp_plano_analitica as tpa
                    on (     tpa.cod_plano = cc.cod_plano
                         and tpa.exercicio = cc.exercicio
                       );
                create index unq_tmp_conta_credito on tmp_conta_credito (cod_lote, tipo,sequencia,exercicio,tipo_valor,cod_entidade);';

                execute stSql;

                stSql = '
                /*
                 * DEBITOS
                 */
                create temporary table tmp_conta_debito as
                select cd.cod_lote
                     , cd.tipo
                     , cd.sequencia
                     , cd.exercicio
                     , cd.tipo_valor
                     , cd.cod_entidade
                     , tpa.cod_estrutural
                from contabilidade.conta_debito as cd
                join tmp_plano_analitica as tpa
                  on (     tpa.cod_plano = cd.cod_plano
                       and tpa.exercicio = cd.exercicio
                     );
                create index unq_tmp_conta_debito on tmp_conta_debito (cod_lote, tipo,sequencia,exercicio,tipo_valor,cod_entidade);';

                execute stSql;

                stSql = '
                /*
                 * VALOR DOS LANCAMENTOS
                 */
                create temporary table tmp_valor_lancamento as
                select vl.cod_lote
                     , vl.tipo
                     , vl.sequencia
                     , vl.exercicio
                     , vl.tipo_valor
                     , vl.cod_entidade
                     , vl.vl_lancamento
                     , tcc.cod_estrutural
                  from contabilidade.valor_lancamento as vl
                  join tmp_conta_credito as tcc
                    on (     vl.cod_lote     = tcc.cod_lote
                         and vl.tipo         = tcc.tipo
                         and vl.sequencia    = tcc.sequencia
                         and vl.exercicio    = tcc.exercicio
                         and vl.tipo_valor   = tcc.tipo_valor
                         and vl.cod_entidade = tcc.cod_entidade
                       )

                 union

                select vl.cod_lote
                     , vl.tipo
                     , vl.sequencia
                     , vl.exercicio
                     , vl.tipo_valor
                     , vl.cod_entidade
                     , vl.vl_lancamento
                     , tcd.cod_estrutural
                  from contabilidade.valor_lancamento as vl
                  join tmp_conta_debito as tcd
                    on (     vl.cod_lote     = tcd.cod_lote
                         and vl.tipo         = tcd.tipo
                         and vl.sequencia    = tcd.sequencia
                         and vl.exercicio    = tcd.exercicio
                         and vl.tipo_valor   = tcd.tipo_valor
                         and vl.cod_entidade = tcd.cod_entidade
                       );
                create index unq_tmp_valor_lancamento on tmp_valor_lancamento ( cod_lote, tipo, sequencia, exercicio, tipo_valor, cod_entidade );';

                execute stSql;

                stSql = '
                /*
                 * LANCAMENTOS
                 */
                create temporary table tmp_lancamento as
                select lan.sequencia
                     , lan.cod_lote
                     , lan.tipo
                     , lan.exercicio
                     , lan.cod_entidade
                     , tvl.vl_lancamento
                     , tvl.cod_estrutural
                from contabilidade.lancamento as lan
                join tmp_valor_lancamento as tvl
                  on (     tvl.sequencia    = lan.sequencia
                       and tvl.cod_lote     = lan.cod_lote
                       and tvl.tipo         = lan.tipo
                       and tvl.exercicio    = lan.exercicio
                       and tvl.cod_entidade = lan.cod_entidade
                     );
                create index unq_tmp_lancamento on tmp_lancamento ( sequencia, cod_lote, tipo, exercicio, cod_entidade );';

                execute stSql;

                stSql = '
                /*
                 * FILTRA LOTES 
                 */
                create temporary table tmp_lote as
                select lote.cod_lote
                     , lote.exercicio
                     , lote.tipo
                     , lote.cod_entidade
                     , lote.dt_lote
                     , tlan.vl_lancamento
                     , tlan.cod_estrutural
                  from contabilidade.lote as lote
                  join tmp_lancamento as tlan
                    on (     tlan.cod_lote     = lote.cod_lote
                         and tlan.exercicio    = lote.exercicio
                         and tlan.tipo         = lote.tipo
                         and tlan.cod_entidade = lote.cod_entidade
                       )
                 where dt_lote >= ' || stInicioExercicioAnterior || '
                   and dt_lote  < ' || stFimTerceiroQuadrimestre || ' 
                   and lote.cod_entidade IN (' || stCodEntidades || ');
                create index unq_tmp_lote on tmp_lote ( cod_lote, exercicio, tipo, cod_entidade );';

                execute stSql;

                stSql = '
                /*
                 * FILTRA LOTES PREVIDENCIA
                 */
                
                create temporary table tmp_lote_previdencia as
                select lote.cod_lote
                     , lote.exercicio
                     , lote.tipo
                     , lote.cod_entidade
                     , lote.dt_lote
                     , tlan.vl_lancamento
                     , tlan.cod_estrutural
                  from contabilidade.lote as lote
                  join tmp_lancamento as tlan
                    on (     tlan.cod_lote     = lote.cod_lote
                         and tlan.exercicio    = lote.exercicio
                         and tlan.tipo         = lote.tipo
                         and tlan.cod_entidade = lote.cod_entidade
                       )
                 where dt_lote >= ' || stInicioExercicioAnterior || '
                   and dt_lote  < ' || stFimTerceiroQuadrimestre || '
                   and lote.cod_entidade = (
                    select administracao.configuracao.valor
                      from administracao.configuracao
                     where administracao.configuracao.parametro = ''cod_entidade_rpps''
                       and administracao.configuracao.exercicio = ' || inExercicio || '
                    );
                create index unq_tmp_lote_previdencia on tmp_lote_previdencia ( cod_lote, exercicio, tipo, cod_entidade );';

                execute stSql;

                stSql = '
                /*
                 * CRIA TABELA DO RELATORIO ANEXO2
                 */
                create temporary table tmp_rel_anexo2 (
                    num_linha                integer,
                    desc_item                varchar(100) not null,
                    nivel                    integer,
                    saldo_exercicio_anterior numeric,
                    saldo_primeiro_quadrimestre    numeric,
                    saldo_segundo_quadrimestre     numeric,
                    saldo_terceiro_quadrimestre    numeric
                );';

                execute stSql;

                stSql = '
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  1, ''DÍVIDA CONSOLIDADA - DC(I)''                    , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  2, ''Dívida Mobiliária''                             , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  3, ''Dívida Contratual''                             , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  4, ''Precatórios posteriores a 5.5.2000 (inclusive)'', 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  5, ''Operações de Crédito inferiores a 12 meses''    , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  6, ''Parcelamento de Dívidas''                       , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  7, ''De Tributos''                                   , 2 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  8, ''De Contribuições Sociais''                      , 2 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values (  9, ''Previdenciárias''                               , 3 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 10, ''Demais Contribuições Sociais''                  , 3 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 11, ''Do FGTS''                                       , 2 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 12, ''Provisões de PPPs''                           , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 13, ''Outras Dívidas''                                , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 14, ''DEDUÇÕES (II)¹''                                , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 15, ''Ativo Disponível''                              , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 16, ''Haveres Financeiros''                           , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 17, ''(-)Restos a Pagar Processados''                 , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 18, ''OBRIGAÇÕES NÃO INTEGRANTES  DA DC''             , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 19, ''Precatórios anteriores a 5.5.2000''             , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 20, ''Insuficiência Financeira''                      , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 21, ''Outras Obrigações''                             , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 22, ''DÍVIDA CONSOLIDADA LÍQUIDA (DCL)''              , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 23, ''RECEITA CORRENTE LÍQUIDA - RCL''                , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 24, ''% da DC sobre a RCL [(I)/RCL]''                 , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 25, ''% da DCL sobre a RCL [(III)/RCL]''              , 0 );

                --DÍVIDA CONSOLIDADA PREVIDENCIÁRIA
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 26, ''DÍVIDA CONSOLIDADA PREVIDENCIÁRIA (IV)''        , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 27, ''Passivo Atuarial''                              , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 28, ''Demais Dívidas''                                , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 29, ''DEDUÇÕES (V)¹''                                 , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 30, ''Ativo Disponível''                              , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 31, ''Investimentos''                                 , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 32, ''Haveres Financeiros''                           , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 33, ''(-)Restos a Pagar Processados''                 , 1 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 34, ''OBRIGAÇÕES NÃO INTEGRANTES DA DC''              , 0 );
                insert into tmp_rel_anexo2 ( num_linha, desc_item, nivel ) values ( 35, ''DÍVIDA CONSOLIDADA LÍQUIDA PREVIDENCIÁRIA (VI) = (IV-V)'', 0 );

                -- calcula o saldo do exercicio anterior da divida mobiliária 
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.1%''
                             or cod_estrutural like ''2.2.2.2.1%'' )
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                       and tmp_lote.cod_entidade IN ('|| stCodEntidades ||')
                )
                where num_linha = 2;

                -- calcula o saldo do primeiro quadrimestre da divida mobiliária 
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.1%''
                             or cod_estrutural like ''2.2.2.2.1%'' )
                       and tmp_lote.exercicio  = '|| inExercicio  ||'
                       and tmp_lote.cod_entidade IN ('|| stCodEntidades ||')
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre  ||'
                )
                where num_linha = 2;

                -- calcula o saldo do segundo quadrimestre da divida mobiliária
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.1%''
                             or cod_estrutural like ''2.2.2.2.1%'' )
                       and tmp_lote.exercicio  = '|| inExercicio ||'
                       and tmp_lote.cod_entidade = 1
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 2;

                -- calcula o saldo do terceiro quadrimestre da divida mobiliária
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.1%''
                             or cod_estrutural like ''2.2.2.2.1%'' )
                       and tmp_lote.exercicio  = '|| inExercicio ||'
                       and tmp_lote.cod_entidade = 1
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 2;

                -- calcula o saldo do exercicio anterior da divida contratual 
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    tmp_lote.cod_estrutural like ''2.1.2.2.2.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.1.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.2.02%''
                             or tmp_lote.cod_estrutural like ''2.2.2.1.2%''
                             or tmp_lote.cod_estrutural like ''2.2.2.2.2%'' )
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                       and tmp_lote.cod_entidade = 1
                )
                where num_linha = 3;

                -- calcula o saldo do primeiro quadrimestre da divida contratual
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    tmp_lote.cod_estrutural like ''2.1.2.2.2.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.1.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.2.02%''
                             or tmp_lote.cod_estrutural like ''2.2.2.1.2%''
                             or tmp_lote.cod_estrutural like ''2.2.2.2.2%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.cod_entidade = 1
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 3;

                -- calcula o saldo do segundo quadrimestre da divida contratual
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    tmp_lote.cod_estrutural like ''2.1.2.2.2.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.1.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.2.02%''
                             or tmp_lote.cod_estrutural like ''2.2.2.1.2%''
                             or tmp_lote.cod_estrutural like ''2.2.2.2.2%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.cod_entidade = 1
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 3;

                -- calcula o saldo do terceiro quadrimestre da divida contratual
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    tmp_lote.cod_estrutural like ''2.1.2.2.2.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.1.02%''
                             or tmp_lote.cod_estrutural like ''2.1.2.3.2.02%''
                             or tmp_lote.cod_estrutural like ''2.2.2.1.2%''
                             or tmp_lote.cod_estrutural like ''2.2.2.2.2%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.cod_entidade = 1
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 3;

                -- calcula o saldo do exercicio anterior dos precatórios posteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where  cod_estrutural like ''2.1.2.1.7.05%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 4;

                -- calcula o saldo do primeiro quadrimestre dos precatórios posteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where  cod_estrutural like ''2.1.2.1.7.05%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 4;

                -- calcula o saldo do segundo quadrimestre dos precatórios posteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where  cod_estrutural like ''2.1.2.1.7.05%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 4;

                -- calcula o saldo do terceiro quadrimestre dos precatórios posteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where  cod_estrutural like ''2.1.2.1.7.05%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 4;

                -- calcula o saldo do exercicio anterior das operações de crédito inferiores a 12 meses 
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.3.1.02.02.03%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 5;

                -- calcula o saldo do primeiro quadrimestre das operações de crédito inferiores a 12 meses
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.3.1.02.02.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 5;

                -- calcula o saldo do segundo quadrimestre das operações de crédito inferiores a 12 meses
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.3.1.02.02.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 5;

                -- calcula o saldo do terceiro quadrimestre das operações de crédito inferiores a 12 meses
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.3.1.02.02.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 5;

                -- calcula o saldo do exercicio anterior do parcelamento das dividas
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.02%'' 
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 6;

                -- calcula o saldo do primeiro quadrimestre do parcelamento das dividas
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.02%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 6;

                -- calcula o saldo do segundo quadrimestre do parcelamento das dividas
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.02%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 6;

                -- calcula o saldo do terceiro quadrimestre do parcelamento das dividas
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.02%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 6;

                -- calcula o saldo do exercicio anterior do parcelamento de dividas de tributos
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 7;

                -- calcula o saldo do primeiro quadrimestre do parcelamento de dividas de tributos
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 7;

                -- calcula o saldo do segundo quadrimestre do parcelamento de dividas de tributos
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 7;

                -- calcula o saldo do terceiro quadrimestre do parcelamento de dividas de tributos
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 7;

                -- calcula o saldo do exercicio anterior do parcelamento de contribuições sociais
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 8;

                -- calcula o saldo do primeiro quadrimestre do parcelamento de contribuições sociais
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'

                )
                where num_linha = 8;

                -- calcula o saldo do segundo quadrimestre do parcelamento de contribuições sociais
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 8;

                -- calcula o saldo do terceiro quadrimestre do parcelamento de contribuições sociais
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where (    cod_estrutural like ''2.2.2.1.2.00.00.01%''
                             or cod_estrutural like ''2.2.2.4.9.00.00.03%'' )
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 8;

                -- calcula o saldo do exercicio anterior do parcelamento de contribuições sociais previdenciarias
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.1.2.00.00.01%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 9;
                
                -- calcula o saldo do primeiro quadrimestre do parcelamento de contribuições sociais previdenciarias
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.1.2.00.00.01%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                
                )
                where num_linha = 9;
                
                -- calcula o saldo do segundo quadrimestre do parcelamento de contribuições sociais previdenciarias
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.1.2.00.00.01%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 9;
                
                -- calcula o saldo do terceiro quadrimestre do parcelamento de contribuições sociais previdenciarias
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.1.2.00.00.01%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 9;
                
                -- calcula o saldo do exercicio anterior das demais contribuíções sociais
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.03%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 10;
                
                -- calcula o saldo do primeiro quadrimestre das demais contribuíções sociais 
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 10;
                
                -- calcula o saldo do segundo quadrimestr das demais contribuíções sociais
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 10;
                
                -- calcula o saldo do terceiro quadrimestre das demais contribuíções sociais
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.03%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 10;
                  
                -- calcula o saldo do exercicio anterior do parcelamento do FGTS
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.02%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 11;
                
                -- calcula o saldo do primeiro quadrimestre do parcelamento do FGTS
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.02%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 11;
                
                -- calcula o saldo do segundo quadrimestre do parcelamento do FGTS
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.02%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 11;
                
                -- calcula o saldo do terceiro quadrimestre do parcelamento do FGTS
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.2.2.4.9.00.00.02%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 11;
                
                -- calcula o saldo do exercicio anterior das provisões de PPPs 
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 12;
                
                -- calcula o saldo do primeiro quadrimestre das provisões de PPPs
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 12;
                
                -- calcula o saldo do segundo quadrimestre das provisões de PPPs
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 12;
                
                -- calcula o saldo do terceiro quadrimestre das provisões de PPPs
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 12;
                
                -- calcula o saldo do exercicio anterior das outras dívidas 
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 13;
                
                -- calcula o saldo do primeiro quadrimestre das outras dívidas
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 13;
                
                -- calcula o saldo do segundo quadrimestre das outras dívidas
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 13;
                
                -- calcula o saldo do terceiro quadrimestre das outras dívidas
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 13;
                
                -- calcula o saldo do exercicio anterior do ativo disponível
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1.1%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 15;
                
                -- calcula o saldo do primeiro quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1.1%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 15;
                
                -- calcula o saldo do segundo quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1.1%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 15;
                
                -- calcula o saldo do terceiro quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1.1%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 15;
                
                -- calcula o saldo do exercicio anterior dos haveres financeiros
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 16;
                
                -- calcula o saldo do primeiro quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                
                )
                where num_linha = 16;
                
                -- calcula o saldo do segundo quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                
                )
                where num_linha = 16;
                
                -- calcula o saldo do terceiro quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                
                )
                where num_linha = 16;
                
                -- calcula o saldo do exercicio anterior dos restos a pagar
                update tmp_rel_anexo2 set saldo_exercicio_anterior = 
                (
                	select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                		from tmp_lote
                	where cod_estrutural like ''2.1.2.1.7%''
                		and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 17;
                
                -- calcula o saldo do primeiro quadrimestre dos restos a pagar
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = 
                (
                	select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                		from tmp_lote
                	where cod_estrutural like ''2.1.2.1.7%''
                		and tmp_lote.exercicio = '|| inExercicio ||'
                		and tmp_lote.dt_lote >= '|| stInicioPrimeiroQuadrimestre ||'
                		and tmp_lote.dt_lote < '|| stInicioSegundoQuadrimestre ||'
                )
                
                where num_linha = 17;
                
                -- calcula o saldo do segundo quadrimestre dos restos a pagar
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = 
                (
                	select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                		from tmp_lote
                	where cod_estrutural like ''2.1.2.1.7%''
                		and tmp_lote.exercicio = '|| inExercicio ||'
                		and tmp_lote.dt_lote >= '|| stInicioSegundoQuadrimestre ||'
                		and tmp_lote.dt_lote < '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 17;
                
                -- calcula o saldo do terceiro quadrimestre dos restos a pagar
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = 
                (
                	select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                		from tmp_lote
                	where cod_estrutural like ''2.1.2.1.7%''
                		and tmp_lote.exercicio = '|| inExercicio ||'
                		and tmp_lote.dt_lote >= '|| stInicioTerceiroQuadrimestre ||'
                		and tmp_lote.dt_lote < '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 17;
                
                -- calcula o saldo do exercicio anterior dos precatórios anteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.7.04%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 19;
                
                -- calcula o saldo do primeiro quadrimestre dos precatórios anteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.7.04%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 19;
                
                -- calcula o saldo do segundo quadrimestre dos precatórios anteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.7.04%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                
                )
                where num_linha = 19;
                
                -- calcula o saldo do terceiro quadrimestre dos precatórios anteriores a 5.5.2000
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.7.04%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                
                )
                where num_linha = 19;
                
                -- calcula o saldo do exercicio anterior das outras obrigações
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.9.99%''
                        or cod_estrutural like ''2.1.2.9%''
                        or cod_estrutural like ''2.2.2.3.9%''
                        or cod_estrutural like ''2.2.2.4.9%''
                       and tmp_lote.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 21;
                
                -- calcula o saldo do primeiro quadrimestre das outras obrigações 
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.9.99%''
                        or cod_estrutural like ''2.1.2.9%''
                        or cod_estrutural like ''2.2.2.3.9%''
                        or cod_estrutural like ''2.2.2.4.9%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 21;
                
                -- calcula o saldo do segundo quadrimestre das outras obrigações 
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.9.99%''
                        or cod_estrutural like ''2.1.2.9%''
                        or cod_estrutural like ''2.2.2.3.9%''
                        or cod_estrutural like ''2.2.2.4.9%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 21;
                
                -- calcula o saldo do terceiro quadrimestre das outras obrigações
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote
                     where cod_estrutural like ''2.1.2.1.9.99%''
                        or cod_estrutural like ''2.1.2.9%''
                        or cod_estrutural like ''2.2.2.3.9%''
                        or cod_estrutural like ''2.2.2.4.9%''
                       and tmp_lote.exercicio = '|| inExercicio ||'
                       and tmp_lote.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 21;
                
                
                -- calcula o saldo do exercicio anterior da divida consolidada
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( saldo_exercicio_anterior ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 2
                       and num_linha <= 13
                )
                where num_linha = 1;
                
                -- calcula o primeiro quadrimestre da divida consolidada
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( saldo_primeiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 2
                       and num_linha <= 13
                )
                where num_linha = 1;
                
                -- calcula o segundo quadrimestre da divida consolidada
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( saldo_segundo_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 2
                       and num_linha <= 13
                )
                where num_linha = 1;
                
                
                -- calcula o terceiro quadrimestre da divida consolidada
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( saldo_terceiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 2
                       and num_linha <= 13
                )
                where num_linha = 1;
                
                
                -- calcula o saldo do exercicio anterior das deduções
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( saldo_exercicio_anterior ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 15
                       and num_linha <= 17
                )
                where num_linha = 14;
                
                -- calcula o primeiro quadrimestre das deduções
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( saldo_primeiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 15
                       and num_linha <= 17
                )
                where num_linha = 14;
                
                -- calcula o segundo quadrimestre das deduções
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( saldo_segundo_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 15
                       and num_linha <= 17
                )
                where num_linha = 14;
                
                
                -- calcula o terceiro quadrimestre das deduções
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( saldo_terceiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 15
                       and num_linha <= 17
                )
                where num_linha = 14;
                
                -- calcula o saldo do exercicio anterior das obrigações não integrantes da DC
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( saldo_exercicio_anterior ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 19
                       and num_linha <= 21
                )
                where num_linha = 18;
                
                -- calcula o primeiro quadrimestre das obrigações não integrantes da DC
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( saldo_primeiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 19
                       and num_linha <= 21
                )
                where num_linha = 18;
                
                -- calcula o segundo quadrimestre das obrigações não integrantes da DC
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( saldo_segundo_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 19
                       and num_linha <= 21
                )
                where num_linha = 18;
                
                -- calcula o terceiro quadrimestre das obrigações não integrantes da DC
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( saldo_terceiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 19
                       and num_linha <= 21
                )
                where num_linha = 18;
                
                /*****************************************************************
                    REGIME PREVIDENCIÁRIO
                ******************************************************************/
                
                -- calcula o saldo do exercicio anterior do passivo atuarial
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 27;
                
                -- calcula o saldo do primeiro quadrimestre do passivo atuarial
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 27;
                
                -- calcula o saldo do segundo quadrimestre do passivo atuarial
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 27;
                
                -- calcula o saldo do terceiro quadrimestre do passivo atuarial
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 27;
                
                -- calcula o saldo do exercicio anterior das demais dividas da previdencia
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''2.1.1.1.1%''
                       and tmp_lote_previdencia.exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 28;
                
                -- calcula o saldo do primeiro quadrimestre das demais dividas da previdencia
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''2.1.1.1.1%''
                       and tmp_lote_previdencia.exercicio = '|| inExercicio ||'
                       and tmp_lote_previdencia.dt_lote  >= '|| stInicioPrimeiroQuadrimestre ||'
                       and tmp_lote_previdencia.dt_lote   < '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 28;
                
                -- calcula o saldo do segundo quadrimestre das demais dividas da previdencia
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''2.1.1.1.1%''
                       and tmp_lote_previdencia.exercicio = '|| inExercicio ||'
                       and tmp_lote_previdencia.dt_lote  >= '|| stInicioSegundoQuadrimestre ||'
                       and tmp_lote_previdencia.dt_lote   < '|| stInicioTerceiroQuadrimestre ||'
                
                )
                where num_linha = 28;
                
                -- calcula o saldo do terceiro quadrimestre das demais dividas da previdencia
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''2.1.1.1.1%''
                       and tmp_lote_previdencia.exercicio = '|| inExercicio ||'
                       and tmp_lote_previdencia.dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and tmp_lote_previdencia.dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                
                )
                where num_linha = 28;
                
                -- calcula o saldo do exercicio anterior DC da prev
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( saldo_exercicio_anterior ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 19
                       and num_linha <= 21
                )
                where num_linha = 26;
                
                -- calcula o primeiro quadrimestre DC da prev
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( saldo_primeiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 27
                       and num_linha <= 28
                )
                where num_linha = 26;
                
                -- calcula o segundo quadrimestre DC da prev
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( saldo_segundo_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 27
                       and num_linha <= 28
                )
                where num_linha = 26;
                
                -- calcula o terceiro quadrimestre DC da prev
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( saldo_terceiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 27
                       and num_linha <= 28
                )
                where num_linha = 26;
                
                -- calcula o saldo do exercicio anterior do ativo disponível
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1.1%''
                       and exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 30;
                
                -- calcula o saldo do primeiro quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1.1%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 30;
                
                -- calcula o saldo do segundo quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1.1%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 30;
                
                -- calcula o saldo do terceiro quadrimestre do ativo disponível
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1.1%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 30;
                
                -- calcula o saldo do exercicio anterior dos investimetos
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 31;
                
                -- calcula o saldo do primeiro quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 31;
                
                -- calcula o saldo do segundo quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 31;
                
                -- calcula o saldo do terceiro quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 31;
                
                -- calcula o saldo do exercicio anterior dos haveres financeiros
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 32;
                
                -- calcula o saldo do primeiro quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                
                )
                where num_linha = 32;
                
                -- calcula o saldo do segundo quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                
                )
                where num_linha = 32;
                
                -- calcula o saldo do terceiro quadrimestre dos haveres financeiros
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where cod_estrutural like ''1.1%''
                       and not cod_estrutural like ''1.1.1%''
                       and not cod_estrutural like ''1.1.3%''
                       and not cod_estrutural like ''1.1.2.4%''
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                
                )
                where num_linha = 32;
                
                -- calcula o saldo do exercicio anterior dos investimetos
                update tmp_rel_anexo2 set saldo_exercicio_anterior = ''0.00''
                where num_linha = 33;
                
                -- calcula o saldo do primeiro quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre = ''0.00''
                where num_linha = 33;
                
                -- calcula o saldo do segundo quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre = ''0.00''
                where num_linha = 33;
                
                -- calcula o saldo do terceiro quadrimestre dos investimetos
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre = ''0.00''
                where num_linha = 33;
                
                
                -- calcula o saldo do exercicio anterior
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( saldo_exercicio_anterior ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 30
                       and num_linha <= 33
                )
                where num_linha = 29;
                
                -- calcula o primeiro quadrimestre 
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( saldo_primeiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 30
                       and num_linha <= 33
                )
                where num_linha = 29;
                
                -- calcula o segundo quadrimestre 
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( saldo_segundo_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 30
                       and num_linha <= 33
                )
                where num_linha = 29;
                
                -- calcula o terceiro quadrimestre 
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( saldo_terceiro_quadrimestre ), 0.00 ) as valor
                      from tmp_rel_anexo2
                     where num_linha >= 30
                       and num_linha <= 33
                )
                where num_linha = 29;
                
                -- calcula o saldo do exercicio anterior 
                update tmp_rel_anexo2 set saldo_exercicio_anterior =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where (    cod_estrutural like ''2.1.2.1.7.04%''
                             or cod_estrutural like ''2.1.2.1.9.99%''
                             or cod_estrutural like ''2.1.2.9%''
                             or cod_estrutural like ''2.2.2.3.9%''
                             or cod_estrutural like ''2.2.2.4.9%'' )
                       and exercicio = '|| inExercicioAnterior ||'
                )
                where num_linha = 34;
                
                -- calcula o saldo do primeiro quadrimestre 
                update tmp_rel_anexo2 set saldo_primeiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where (    cod_estrutural like ''2.1.2.1.7.04%''
                             or cod_estrutural like ''2.1.2.1.9.99%''
                             or cod_estrutural like ''2.1.2.9%''
                             or cod_estrutural like ''2.2.2.3.9%''
                             or cod_estrutural like ''2.2.2.4.9%'' )
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioPrimeiroQuadrimestre ||'
                       and dt_lote   <  '|| stInicioSegundoQuadrimestre ||'
                )
                where num_linha = 34;
                
                -- calcula o saldo do segundo quadrimestre 
                update tmp_rel_anexo2 set saldo_segundo_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where (    cod_estrutural like ''2.1.2.1.7.04%''
                             or cod_estrutural like ''2.1.2.1.9.99%''
                             or cod_estrutural like ''2.1.2.9%''
                             or cod_estrutural like ''2.2.2.3.9%''
                             or cod_estrutural like ''2.2.2.4.9%'' )
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioSegundoQuadrimestre ||'
                       and dt_lote   <  '|| stInicioTerceiroQuadrimestre ||'
                )
                where num_linha = 34;
                
                -- calcula o saldo do terceiro quadrimestre 
                update tmp_rel_anexo2 set saldo_terceiro_quadrimestre =
                (
                    select coalesce( sum( vl_lancamento ), 0.00 ) as valor
                      from tmp_lote_previdencia
                     where (    cod_estrutural like ''2.1.2.1.7.04%''
                             or cod_estrutural like ''2.1.2.1.9.99%''
                             or cod_estrutural like ''2.1.2.9%''
                             or cod_estrutural like ''2.2.2.3.9%''
                             or cod_estrutural like ''2.2.2.4.9%'' )
                       and exercicio = '|| inExercicio ||'
                       and dt_lote   >= '|| stInicioTerceiroQuadrimestre ||'
                       and dt_lote   <  '|| stFimTerceiroQuadrimestre ||'
                )
                where num_linha = 34;
                
                /*
                 * DROP INDEX
                 */
                drop index unq_tmp_plano_contas;
                drop index unq_tmp_plano_analitica;
                drop index unq_tmp_conta_debito;
                drop index unq_tmp_conta_credito;
                drop index unq_tmp_valor_lancamento;
                drop index unq_tmp_lancamento;
                drop index unq_tmp_lote;
                drop index unq_tmp_lote_previdencia;
                
                /*
                 * DROP TABLES
                 */
                drop table tmp_lote_previdencia;
                drop table tmp_plano_contas;
                drop table tmp_plano_analitica;
                drop table tmp_conta_debito;
                drop table tmp_conta_credito;
                drop table tmp_valor_lancamento;
                drop table tmp_lancamento;
                drop table tmp_lote;
        ';
    execute stSql;

    stSql := ' select *
                from tmp_rel_anexo2;';

    for reRegistro IN EXECUTE stSql
    LOOP
        RETURN next reRegistro;
    END LOOP;

    DROP TABLE tmp_rel_anexo2;

    RETURN;
END;
$$ language 'plpgsql';
