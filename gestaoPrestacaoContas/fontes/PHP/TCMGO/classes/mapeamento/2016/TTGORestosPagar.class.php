<?php
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
?>
<?php
/**
    * Extensão da Classe de mapeamento
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: LUCAS STEPHANOU

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTGORestosPagar.class.php 65190 2016-04-29 19:36:51Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTGORestosPagar extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSql .=" select * from (                                                                            \n";
        $stSql .=" select '10' as tipo_registro                                                               \n";
        $stSql .="      , case when pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001' then    \n";
        $stSql .="          restos_pre_empenho.num_orgao                                                      \n";
        $stSql .="        else                                                                                \n";
        $stSql .="          despesa.num_orgao                                                                 \n";
        $stSql .="        end as num_orgao                                                                    \n";
        $stSql .="      , pre_empenho.implantado                                                              \n";
        $stSql .="      , pre_empenho.exercicio                                                               \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= 2001 then       \n";
        $stSql .="          (                                                                                 \n";
        $stSql .="           LPAD( restos_pre_empenho.num_unidade,4,'0') ||                                   \n";
        $stSql .="           LPAD( restos_pre_empenho.cod_funcao,2,'0')||                                     \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa,1,2),2,'0') ||                      \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa,3,3),3,'0') ||                      \n";
        $stSql .="           LPAD( restos_pre_empenho.num_pao,4,'0') ||                                       \n";
        $stSql .="           LPAD( SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,'.',''),1,6),6,'0')       \n";
        $stSql .="          )::varchar                                                                        \n";
        $stSql .="        else                                                                                \n";
        $stSql .="          LPAD( '' ,21,'0')::varchar                                                        \n";
        $stSql .="        end as dop2001                                                                      \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= 2001 then       \n";
        $stSql .="          LPAD( '' ,23,'0')::varchar                                                        \n";
        $stSql .="        else                                                                                \n";
        $stSql .="          (                                                                                 \n";
        $stSql .="           LPAD ( despesa.cod_programa , 4 , '0' ) ||                                       \n";
        $stSql .="           LPAD ( despesa.num_unidade , 2 , '0' ) ||                                        \n";
        $stSql .="           LPAD ( despesa.cod_funcao , 2 , '0' ) ||                                         \n";
        $stSql .="           '000' ||                                                                         \n";
        $stSql .="           LPAD ( substr( despesa.num_pao , 1,1 ) , 1 , '0') ||                             \n";
        $stSql .="           LPAD ( substr( despesa.num_pao , 2,3 ) , 3 , '0') ||                             \n";
        $stSql .="           LPAD ( substr(replace(despesa.cod_estrutural,'.',''),1,6) , 6, '0') ||           \n";
        $stSql .="           '00'                                                                             \n";
        $stSql .="          )::varchar                                                                        \n";
        $stSql .="        end as dop2002                                                                      \n";
        $stSql .="      , empenho.cod_empenho as num_empenho                                                  \n";
        $stSql .="      , to_char(empenho.dt_empenho, 'ddmmyyyy') as data_empenho                             \n";
        $stSql .="      , ( select nom_cgm from sw_cgm where numcgm = pre_empenho.cgm_beneficiario ) as nom_credor \n";
        $stSql .="      , case                                                                                \n";
        $stSql .="          when ( empenho_anulado.cod_empenho is not null) then                              \n";
        $stSql .="              to_char(empenho_anulado.timestamp, 'ddmmyyyy')::varchar                       \n";
        $stSql .="          else                                                                              \n";
        $stSql .="              '00000000'::varchar                                                           \n";
        $stSql .="        end as data_cancelamento                                                            \n";
        $stSql .="      , case                                                                                \n";
        $stSql .="          when ( empenho_anulado.cod_empenho is not null) then                              \n";
        $stSql .="              lpad( tc.numero_anulacao_empenho( empenho.exercicio::varchar, empenho.cod_entidade, empenho.cod_empenho , empenho_anulado.timestamp ) ,3,'0')::varchar                                                                       \n";
        $stSql .="          else                                                                              \n";
        $stSql .="              '000'                                                                         \n";
        $stSql .="        end as numero_cancelamento                                                          \n";
        $stSql .="      , LPAD ( REPLACE (  (                                                                 \n";
        $stSql .="              select coalesce(sum(item_pre_empenho.vl_total) , 0.00)                        \n";
        $stSql .="                from empenho.item_pre_empenho                                               \n";
        $stSql .="               where item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho         \n";
        $stSql .="                 and item_pre_empenho.exercicio = pre_empenho.exercicio                     \n";
        $stSql .="          ) ,  '.' , ',' ) , 13 ,'0' )                                                      \n";
        $stSql .="        as valor_original                                                                   \n";
        $stSql .=" --- valor pago                                                                             \n";
        $stSql .=",coalesce( (                                                                                \n";
        $stSql .="                                                                                            \n";
        $stSql .="  ( select sum ( nota_liquidacao_paga.vl_pago)                                              \n";
        $stSql .="     from empenho.nota_liquidacao                                                           \n";
        $stSql .="     join empenho.nota_liquidacao_paga                                                      \n";
        $stSql .="       on ( nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio                   \n";
        $stSql .="      and   nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade                \n";
        $stSql .="      and   nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota    )               \n";
        $stSql .="   where nota_liquidacao.exercicio_empenho = empenho.exercicio                              \n";
        $stSql .="     and nota_liquidacao.cod_entidade      = empenho.cod_entidade                           \n";
        $stSql .="     and nota_liquidacao.cod_empenho       = empenho.cod_empenho                            \n";
        $stSql .="     and  ( nota_liquidacao_paga.timestamp between to_date (  '". $this->getDado( 'dtInicio' )  ."', 'dd/mm/yyyy' ) and to_date ( '".$this->getDado( 'dtFim') ."' , 'dd/mm/yyyy' )                   ) )                                   \n";
        $stSql .="     -                                                                                      \n";
        $stSql .="   --- valor anulado                                                                        \n";
        $stSql .="    coalesce(                                                                               \n";
        $stSql .="    (                                                                                       \n";
        $stSql .="     select sum ( nota_liquidacao_paga_anulada.vl_anulado )                                 \n";
        $stSql .="       from empenho.nota_liquidacao                                                         \n";
        $stSql .="       join empenho.nota_liquidacao_paga_anulada                                            \n";
        $stSql .="         on ( nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao.exercicio         \n";
        $stSql .="        and   nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao.cod_entidade      \n";
        $stSql .="        and   nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao.cod_nota    )     \n";
        $stSql .="      where nota_liquidacao.exercicio_empenho = empenho.exercicio                           \n";
        $stSql .="        and nota_liquidacao.cod_entidade      = empenho.cod_entidade                        \n";
        $stSql .="        and nota_liquidacao.cod_empenho       = empenho.cod_empenho                         \n";
        $stSql .="        and (  nota_liquidacao_paga_anulada.timestamp_anulada between to_date (  '". $this->getDado( 'dtInicio' )  ."', 'dd/mm/yyyy' ) and to_date ( '".$this->getDado( 'dtFim') ."' , 'dd/mm/yyyy' ) ) ),0 )                              \n";
        $stSql .=" ), 0)    as valor_pagamento                                                                \n";
        $stSql .="      , LPAD ( REPLACE ( tcmgo.fn_consultar_anulado_empenho   ( empenho.cod_empenho, empenho.exercicio, '" . $this->getDado('mes')  . "' , '" . ( $this->getDado('ano') ) . "' ) ,'.' , ',' ) , 13 ,'0'  ) as valor_anulado                \n";
        $stSql .="      , LPAD ( REPLACE ( tc.fn_saldo_empenho ( empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio::int, '31/12/" . ( $this->getDado('ano') - 1 ) . "' ) , '.' , ',' ) , 13 , '0' ) as saldo_do_empenho                           \n";
        $stSql .="      , 0 as numero_sequencial                                                              \n";
        $stSql .=" from empenho.empenho                                                                       \n";
        $stSql .="      inner join empenho.pre_empenho                                                        \n";
        $stSql .="              on pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho                      \n";
        $stSql .="             and pre_empenho.exercicio = empenho.exercicio                                  \n";
        $stSql .="       LEFT JOIN empenho.restos_pre_empenho                                                 \n";
        $stSql .="              ON restos_pre_empenho.exercicio = pre_empenho.exercicio                       \n";
        $stSql .="             AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho           \n";
        $stSql .="       LEFT JOIN (       SELECT  despesa.*                                                  \n";
        $stSql .="                              ,  conta_despesa.cod_estrutural                               \n";
        $stSql .="                              ,  pre_empenho_despesa.cod_pre_empenho                        \n";
        $stSql .="                           FROM  empenho.pre_empenho_despesa                                \n";
        $stSql .="                     INNER JOIN  orcamento.despesa                                          \n";
        $stSql .="                             ON  despesa.exercicio = pre_empenho_despesa.exercicio          \n";
        $stSql .="                            AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa      \n";
        $stSql .="                     INNER JOIN  orcamento.conta_despesa                                    \n";
        $stSql .="                             on  conta_despesa.exercicio = despesa.exercicio                \n";
        $stSql .="                            and  conta_despesa.cod_conta = despesa.cod_conta                \n";
        $stSql .="                 )   AS  despesa                                                            \n";
        $stSql .="              ON despesa.exercicio = pre_empenho.exercicio                                  \n";
        $stSql .="             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                      \n";
        $stSql .="      inner join empenho.pre_empenho_despesa                                                \n";
        $stSql .="              on pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho          \n";
        $stSql .="             and pre_empenho_despesa.exercicio = pre_empenho.exercicio                      \n";
        $stSql .="       left join empenho.empenho_anulado                                                    \n";
        $stSql .="              on empenho_anulado.exercicio    = empenho.exercicio                           \n";
        $stSql .="             and empenho_anulado.cod_entidade = empenho.cod_entidade                        \n";
        $stSql .="             and empenho_anulado.cod_empenho  = empenho.cod_empenho                         \n";
        $stSql .="           where empenho.cod_entidade in ( " . $this->getDado('stCodEntidades')  .  "  )    \n";
        $stSql .="             and empenho.exercicio < " . $this->getDado('exercicio')  . "                   \n";
        $stSql .="  ) as tabela                                                                               \n";
        $stSql .="  where valor_pagamento > 0   or valor_anulado <> '0000000000,00'                           \n";

        return $stSql;
    }

    public function recuperaDetalhamentoRestos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoRestos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoRestos()
    {
        $stSql .=" select * from (                                                                          \n";
        $stSql .=" select '10' as tipo_registro                                                             \n";
        $stSql .="      , case when pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001' then  \n";
        $stSql .="          restos_pre_empenho.num_orgao                                                    \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          despesa.num_orgao                                                               \n";
        $stSql .="        end as num_orgao                                                                  \n";
        $stSql .="      , pre_empenho.implantado                                                            \n";
        $stSql .="      , pre_empenho.exercicio                                                             \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= '2001' then     \n";
        $stSql .="          (                                                                               \n";
        $stSql .="           LPAD( restos_pre_empenho.num_unidade::varchar,4,'0') ||                                 \n";
        $stSql .="           LPAD( restos_pre_empenho.cod_funcao::varchar,2,'0')||                                   \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||                    \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||                    \n";
        $stSql .="           LPAD( restos_pre_empenho.num_pao::varchar,4,'0') ||                                     \n";
        $stSql .="           LPAD( SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,'.',''),1,6),6,'0')     \n";
        $stSql .="          )::varchar                                                                      \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          LPAD( '' ,21,'0')::varchar                                                      \n";
        $stSql .="        end as dop2001                                                                    \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= '2001' then     \n";
        $stSql .="          LPAD( '' ,23,'0')::varchar                                                      \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          (                                                                               \n";
        $stSql .="           LPAD ( despesa.cod_programa::varchar , 4 , '0' ) ||                                     \n";
        $stSql .="           LPAD ( despesa.num_unidade::varchar , 2 , '0' ) ||                                      \n";
        $stSql .="           LPAD ( despesa.cod_funcao::varchar , 2 , '0' ) ||                                       \n";
        $stSql .="           '000' ||                                                                       \n";
        $stSql .="           LPAD ( substr( despesa.num_pao::varchar , 1,1 ) , 1 , '0') ||                           \n";
        $stSql .="           LPAD ( substr( despesa.num_pao::varchar , 2,3 ) , 3 , '0') ||                           \n";
        $stSql .="           LPAD ( substr(replace(despesa.cod_estrutural,'.',''),1,6) , 6, '0') ||         \n";
        $stSql .="           '00'                                                                           \n";
        $stSql .="          )::varchar                                                                      \n";
        $stSql .="        end as dop2002                                                                    \n";
        $stSql .="      , empenho.cod_empenho as num_empenho                                                \n";
        $stSql .="      , to_char(empenho.dt_empenho, 'ddmmyyyy') as data_empenho                           \n";
        $stSql .="      , ( select nom_cgm from sw_cgm where numcgm = pre_empenho.cgm_beneficiario ) as nom_credor \n";
        $stSql .="      , LPAD ( REPLACE (  (                                                               \n";
        $stSql .="              select coalesce(sum(item_pre_empenho.vl_total) , 0.00)                      \n";
        $stSql .="                from empenho.item_pre_empenho                                             \n";
        $stSql .="               where item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho       \n";
        $stSql .="                 and item_pre_empenho.exercicio = pre_empenho.exercicio                   \n";
        $stSql .="          )::varchar ,  '.' , ',' ) , 13 ,'0' )                                                    \n";
        $stSql .="        as valor_original                                                                 \n";
        $stSql .=" --- valor pago                                                                           \n";
        $stSql .=",coalesce( (                                                                              \n";
        $stSql .="                                                                                          \n";
        $stSql .="  ( select sum ( nota_liquidacao_paga.vl_pago)                                            \n";
        $stSql .="     from empenho.nota_liquidacao                                                         \n";
        $stSql .="     join empenho.nota_liquidacao_paga                                                    \n";
        $stSql .="       on ( nota_liquidacao_paga.exercicio    = nota_liquidacao.exercicio                 \n";
        $stSql .="      and   nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade              \n";
        $stSql .="      and   nota_liquidacao_paga.cod_nota     = nota_liquidacao.cod_nota    )             \n";
        $stSql .="   where nota_liquidacao.exercicio_empenho = empenho.exercicio                            \n";
        $stSql .="     and nota_liquidacao.cod_entidade      = empenho.cod_entidade                         \n";
        $stSql .="     and nota_liquidacao.cod_empenho       = empenho.cod_empenho                          \n";
        $stSql .="     and  ( nota_liquidacao_paga.timestamp between to_date (  '". $this->getDado( 'dtInicio' )  ."', 'dd/mm/yyyy' ) and to_date ( '".$this->getDado( 'dtFim') ."' , 'dd/mm/yyyy' )                   ) )  \n";
        $stSql .="     -                                                                                    \n";
        $stSql .="   --- valor anulado                                                                      \n";
        $stSql .="    coalesce(                                                                             \n";
        $stSql .="    (                                                                                     \n";
        $stSql .="     select sum ( nota_liquidacao_paga_anulada.vl_anulado )                               \n";
        $stSql .="       from empenho.nota_liquidacao                                                       \n";
        $stSql .="       join empenho.nota_liquidacao_paga_anulada                                          \n";
        $stSql .="         on ( nota_liquidacao_paga_anulada.exercicio    = nota_liquidacao.exercicio       \n";
        $stSql .="        and   nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao.cod_entidade    \n";
        $stSql .="        and   nota_liquidacao_paga_anulada.cod_nota     = nota_liquidacao.cod_nota    )   \n";
        $stSql .="      where nota_liquidacao.exercicio_empenho = empenho.exercicio                         \n";
        $stSql .="        and nota_liquidacao.cod_entidade      = empenho.cod_entidade                      \n";
        $stSql .="        and nota_liquidacao.cod_empenho       = empenho.cod_empenho                       \n";
        $stSql .="        and (  nota_liquidacao_paga_anulada.timestamp_anulada between to_date (  '". $this->getDado( 'dtInicio' )  ."', 'dd/mm/yyyy' ) and to_date ( '".$this->getDado( 'dtFim') ."' , 'dd/mm/yyyy' ) ) ),0 )                            \n";
        $stSql .=" ), 0)    as valor_pagamento                                                              \n";
        $stSql .="      , LPAD ( REPLACE ( tc.fn_saldo_empenho ( empenho.cod_empenho, empenho.cod_entidade, empenho.exercicio::int, '31/12/" . ( $this->getDado('ano') - 1 ) . "' )::varchar , '.' , ',' ) , 13 , '0' ) as saldo_do_empenho                         \n";
        $stSql .="      , 0 as numero_sequencial                                                            \n";
        $stSql .=" from empenho.empenho                                                                     \n";
        $stSql .="      inner join empenho.pre_empenho                                                      \n";
        $stSql .="              on pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho                    \n";
        $stSql .="             and pre_empenho.exercicio = empenho.exercicio                                \n";
        $stSql .="       LEFT JOIN empenho.restos_pre_empenho                                               \n";
        $stSql .="              ON restos_pre_empenho.exercicio = pre_empenho.exercicio                     \n";
        $stSql .="             AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho         \n";
        $stSql .="       LEFT JOIN (       SELECT  despesa.*                                                \n";
        $stSql .="                              ,  conta_despesa.cod_estrutural                             \n";
        $stSql .="                              ,  pre_empenho_despesa.cod_pre_empenho                      \n";
        $stSql .="                           FROM  empenho.pre_empenho_despesa                              \n";
        $stSql .="                     INNER JOIN  orcamento.despesa                                        \n";
        $stSql .="                             ON  despesa.exercicio = pre_empenho_despesa.exercicio        \n";
        $stSql .="                            AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa    \n";
        $stSql .="                     INNER JOIN  orcamento.conta_despesa                                  \n";
        $stSql .="                             on  conta_despesa.exercicio = despesa.exercicio              \n";
        $stSql .="                            and  conta_despesa.cod_conta = despesa.cod_conta              \n";
        $stSql .="                 )   AS  despesa                                                          \n";
        $stSql .="              ON despesa.exercicio = pre_empenho.exercicio                                \n";
        $stSql .="             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                    \n";
        $stSql .="      inner join empenho.pre_empenho_despesa                                              \n";
        $stSql .="              on pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho        \n";
        $stSql .="             and pre_empenho_despesa.exercicio = pre_empenho.exercicio                    \n";
        $stSql .="       left join empenho.empenho_anulado                                                  \n";
        $stSql .="              on empenho_anulado.exercicio    = empenho.exercicio                         \n";
        $stSql .="             and empenho_anulado.cod_entidade = empenho.cod_entidade                      \n";
        $stSql .="             and empenho_anulado.cod_empenho  = empenho.cod_empenho                       \n";
        $stSql .="           where empenho.cod_entidade in ( " . $this->getDado('stCodEntidades')  .  "  )  \n";
        $stSql .="             and empenho.exercicio < '" . $this->getDado('exercicio')  . "'                 \n";
        $stSql .="  ) as tabela                                                                             \n";
        $stSql .="  where valor_pagamento > 0                                                               \n";

        return $stSql;
    }

    public function recuperaDetalhamentoCancelamentoRestos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoCancelamentoRestos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoCancelamentoRestos()
    {
        $stSql .=" select * from (                                                                          \n";
        $stSql .=" select '11' as tipo_registro                                                             \n";
        $stSql .="      , case when pre_empenho.implantado = 't'  AND pre_empenho.exercicio <= '2001' then  \n";
        $stSql .="          restos_pre_empenho.num_orgao                                                    \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          despesa.num_orgao                                                               \n";
        $stSql .="        end as num_orgao                                                                  \n";
        $stSql .="      , pre_empenho.implantado                                                            \n";
        $stSql .="      , pre_empenho.exercicio                                                             \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= '2001' then     \n";
        $stSql .="          (                                                                               \n";
        $stSql .="           LPAD( restos_pre_empenho.num_unidade::varchar,4,'0') ||                                 \n";
        $stSql .="           LPAD( restos_pre_empenho.cod_funcao::varchar,2,'0')||                                   \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa::varchar,1,2),2,'0') ||                    \n";
        $stSql .="           LPAD( SUBSTR(restos_pre_empenho.cod_programa::varchar,3,3),3,'0') ||                    \n";
        $stSql .="           LPAD( restos_pre_empenho.num_pao::varchar,4,'0') ||                                     \n";
        $stSql .="           LPAD( SUBSTR(REPLACE(restos_pre_empenho.cod_estrutural,'.',''),1,6),6,'0')     \n";
        $stSql .="          )::varchar                                                                      \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          LPAD( '' ,21,'0')::varchar                                                      \n";
        $stSql .="        end as dop2001                                                                    \n";
        $stSql .="      , case when pre_empenho.implantado = 't' and pre_empenho.exercicio <= '2001' then     \n";
        $stSql .="          LPAD( '' ,23,'0')::varchar                                                      \n";
        $stSql .="        else                                                                              \n";
        $stSql .="          (                                                                               \n";
        $stSql .="           LPAD ( despesa.cod_programa::varchar , 4 , '0' ) ||                                     \n";
        $stSql .="           LPAD ( despesa.num_unidade::varchar , 2 , '0' ) ||                                      \n";
        $stSql .="           LPAD ( despesa.cod_funcao::varchar , 2 , '0' ) ||                                       \n";
        $stSql .="           '000' ||                                                                       \n";
        $stSql .="           LPAD ( substr( despesa.num_pao::varchar , 1,1 ) , 1 , '0') ||                           \n";
        $stSql .="           LPAD ( substr( despesa.num_pao::varchar , 2,3 ) , 3 , '0') ||                           \n";
        $stSql .="           LPAD ( substr(replace(despesa.cod_estrutural,'.',''),1,6) , 6, '0') ||         \n";
        $stSql .="           '00'                                                                           \n";
        $stSql .="          )::varchar                                                                      \n";
        $stSql .="        end as dop2002                                                                    \n";
        $stSql .="      , empenho.cod_empenho as num_empenho                                                \n";
        $stSql .="      , to_char(empenho.dt_empenho, 'ddmmyyyy') as data_empenho                           \n";
        $stSql .="      , ( select nom_cgm from sw_cgm where numcgm = pre_empenho.cgm_beneficiario ) as nom_credor \n";
        $stSql .="      , case                                                                              \n";
        $stSql .="          when ( empenho_anulado.cod_empenho is not null) then                            \n";
        $stSql .="              to_char(empenho_anulado.timestamp, 'ddmmyyyy')::varchar                     \n";
        $stSql .="          else                                                                            \n";
        $stSql .="              '00000000'::varchar                                                         \n";
        $stSql .="        end as data_cancelamento                                                          \n";
        $stSql .="      , case                                                                              \n";
        $stSql .="          when ( empenho_anulado.cod_empenho is not null) then                            \n";
        $stSql .="              lpad( tc.numero_anulacao_empenho( empenho.exercicio, empenho.cod_entidade, empenho.cod_empenho , empenho_anulado.timestamp )::varchar ,3,'0')::varchar                                                                     \n";
        $stSql .="          else                                                                            \n";
        $stSql .="              '000'                                                                       \n";
        $stSql .="        end as numero_cancelamento                                                        \n";
        $stSql .="      , LPAD ( REPLACE ( tcmgo.fn_consultar_anulado_empenho   ( empenho.cod_empenho, empenho.exercicio, '" . $this->getDado('mes')  . "' , '" . ( $this->getDado('ano') ) . "' )::varchar ,'.' , ',' ) , 13 ,'0'  ) as valor_anulado              \n";
        $stSql .="      , '' as espaco_branco                                                               \n";
        $stSql .="      , 0 as numero_sequencial                                                            \n";
        $stSql .=" from empenho.empenho                                                                     \n";
        $stSql .="      inner join empenho.pre_empenho                                                      \n";
        $stSql .="              on pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho                    \n";
        $stSql .="             and pre_empenho.exercicio = empenho.exercicio                                \n";
        $stSql .="       LEFT JOIN empenho.restos_pre_empenho                                               \n";
        $stSql .="              ON restos_pre_empenho.exercicio = pre_empenho.exercicio                     \n";
        $stSql .="             AND restos_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho         \n";
        $stSql .="       LEFT JOIN (       SELECT  despesa.*                                                \n";
        $stSql .="                              ,  conta_despesa.cod_estrutural                             \n";
        $stSql .="                              ,  pre_empenho_despesa.cod_pre_empenho                      \n";
        $stSql .="                           FROM  empenho.pre_empenho_despesa                              \n";
        $stSql .="                     INNER JOIN  orcamento.despesa                                        \n";
        $stSql .="                             ON  despesa.exercicio = pre_empenho_despesa.exercicio        \n";
        $stSql .="                            AND  despesa.cod_despesa = pre_empenho_despesa.cod_despesa    \n";
        $stSql .="                     INNER JOIN  orcamento.conta_despesa                                  \n";
        $stSql .="                             on  conta_despesa.exercicio = despesa.exercicio              \n";
        $stSql .="                            and  conta_despesa.cod_conta = despesa.cod_conta              \n";
        $stSql .="                 )   AS  despesa                                                          \n";
        $stSql .="              ON despesa.exercicio = pre_empenho.exercicio                                \n";
        $stSql .="             AND despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho                    \n";
        $stSql .="      inner join empenho.pre_empenho_despesa                                              \n";
        $stSql .="              on pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho        \n";
        $stSql .="             and pre_empenho_despesa.exercicio = pre_empenho.exercicio                    \n";
        $stSql .="       left join empenho.empenho_anulado                                                  \n";
        $stSql .="              on empenho_anulado.exercicio    = empenho.exercicio                         \n";
        $stSql .="             and empenho_anulado.cod_entidade = empenho.cod_entidade                      \n";
        $stSql .="             and empenho_anulado.cod_empenho  = empenho.cod_empenho                       \n";
        $stSql .="           where empenho.cod_entidade in ( " . $this->getDado('stCodEntidades')  .  "  )  \n";
        $stSql .="             and empenho.exercicio < '" . $this->getDado('exercicio')  . "'                 \n";
        $stSql .="  ) as tabela                                                                             \n";
        $stSql .="  where valor_anulado <> '0000000000,00'                                                  \n";

        return $stSql;
    }

}
