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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGORecita.class.php 65220 2016-05-03 21:30:22Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoContaReceita.class.php";

class TTCMGOReceita extends TOrcamentoContaReceita
{

    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::TOrcamentoContaReceita();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function montaRecuperaTodos()
    {
        $stSQL = "
            SELECT tipo_registro
                 , cod_orgao
                 , '01' as num_unidade
                 , rubrica
                 , cod_receita
                 , exercicio
                 , descricao
                 , ABS(vl_original) AS vl_original
                 , ABS(SUM(vl_arrecadacao_mes)) AS vl_arrecadacao_mes
                 , ABS(SUM(vl_arrecadacao_ate)) AS vl_arrecadacao_ate
              FROM (
                    select '10'                as tipo_registro
                         , orgao_plano_banco.num_orgao   as cod_orgao
                         , CASE WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1) = '9'
                               THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                               ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END as rubrica
                       --, SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9) as rubrica
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , SUM(arrecadacao_receita.vl_arrecadacao)
                        -- descontando os valores estornados
                                - (  COALESCE(
                                                ( SELECT SUM(arrecadacao_estornada_receita.vl_estornado)
                                                    FROM tesouraria.arrecadacao_estornada_receita
                                                   WHERE arrecadacao_estornada_receita.cod_receita = receita.cod_receita
                                                     AND arrecadacao_estornada_receita.exercicio   = receita.exercicio
                                                     AND to_date(arrecadacao_estornada_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' ) + 1
                                                ), 0
                                             )
                                  ) AS vl_arrecadacao_mes
                         , (  SELECT SUM(arrec.vl_arrecadacao)
                                FROM tesouraria.arrecadacao_receita AS arrec
                                WHERE arrec.cod_receita = receita.cod_receita
                                AND arrec.exercicio = receita.exercicio
                                AND to_date(arrec.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' ))
                        -- descontando os valores estornados
                                - (  COALESCE(
                                                ( SELECT SUM(arrecadacao_estornada_receita.vl_estornado)
                                                    FROM tesouraria.arrecadacao_estornada_receita
                                                   WHERE arrecadacao_estornada_receita.cod_receita = receita.cod_receita
                                                     AND arrecadacao_estornada_receita.exercicio   = receita.exercicio
                                                     AND to_date(arrecadacao_estornada_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '01/01/".$this->getDado('exercicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                                                ), 0
                                             )
                                  ) AS vl_arrecadacao_ate

                      from orcamento.receita
                      join orcamento.conta_receita
                        on ( receita.exercicio = conta_receita.exercicio
                       and   receita.cod_conta = conta_receita.cod_conta )
                      join tesouraria.arrecadacao_receita
                        on ( receita.cod_receita = arrecadacao_receita.cod_receita
                       and   receita.exercicio   = arrecadacao_receita.exercicio )
                      join tesouraria.arrecadacao
                        on ( arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       and   arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       and   arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao )
                      join contabilidade.plano_analitica
                        on ( arrecadacao.cod_plano = plano_analitica.cod_plano
                       and   arrecadacao.exercicio = plano_analitica.exercicio )
                      join tcmgo.orgao_plano_banco
                        on ( plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       and   plano_analitica.exercicio = orgao_plano_banco.exercicio )

                       --- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                           join tesouraria.boletim
                             on ( arrecadacao.cod_boletim  = boletim.cod_boletim
                            and   arrecadacao.exercicio    = boletim.exercicio
                            and   arrecadacao.cod_entidade = boletim.cod_entidade )
                           join ( select boletim_fechado.cod_boletim
                                       , boletim_fechado.exercicio
                                       , boletim_fechado.cod_entidade
                                    from tesouraria.boletim_fechado
                                    join tesouraria.boletim_liberado
                                      on ( boletim_fechado.cod_boletim            = boletim_liberado.cod_boletim
                                     and   boletim_fechado.cod_entidade           = boletim_liberado.cod_entidade
                                     and   boletim_fechado.exercicio              = boletim_liberado.exercicio
                                     and   boletim_fechado.timestamp_fechamento   = boletim_liberado.timestamp_fechamento )
                                  where not exists ( select 1
                                                       from tesouraria.boletim_reaberto
                                                      where boletim_reaberto.cod_boletim           = boletim_fechado.cod_boletim
                                                        and boletim_reaberto.cod_entidade          = boletim_fechado.cod_entidade
                                                        and boletim_reaberto.exercicio             = boletim_fechado.exercicio
                                                        and boletim_reaberto.timestamp_fechamento  = boletim_fechado.timestamp_fechamento )
                                --  and not exists ( select 1
                                --                     from tesouraria.boletim_liberado_cancelado
                                --                    where boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                                --                      and boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                                --                      and boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                                --                      and boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                                ) as liberados
                             on ( liberados.cod_boletim   = boletim.cod_boletim
                            and   liberados.exercicio     = boletim.exercicio
                            and   liberados.cod_entidade  = boletim.cod_entidade )




                     where to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                     AND arrecadacao.devolucao = FALSE";
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSQL .= "\n  and receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ") ";
        }

        $stSQL .= "\n

                     group by tipo_registro
                            , cod_orgao
                            , 1
                            , receita.cod_receita
                            , rubrica
                            , receita.exercicio
                            , conta_receita.descricao
                            , receita.vl_original

                    UNION

                    select '10'                as tipo_registro
                         , orgao_plano_banco.num_orgao   as cod_orgao
                         , CASE WHEN substr(conta_receita.cod_estrutural, 1, 1) = '9'
                               THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                               ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END as rubrica
                       --, SUBSTR(REPLACE(conta_receita.cod_estrutural,'.',''),1,9) as rubrica
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2) * -1 AS vl_arrecadacao_mes
                         , ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2) * -1 AS vl_arrecadacao_ate

                      from orcamento.receita
                      join orcamento.conta_receita
                        on ( receita.exercicio = conta_receita.exercicio
                       and   receita.cod_conta = conta_receita.cod_conta )
                      join tesouraria.arrecadacao_receita
                        on ( receita.cod_receita = arrecadacao_receita.cod_receita
                       and   receita.exercicio   = arrecadacao_receita.exercicio )
                      join tesouraria.arrecadacao
                        on ( arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       and   arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       and   arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao )
                      join contabilidade.plano_analitica
                        on ( arrecadacao.cod_plano = plano_analitica.cod_plano
                       and   arrecadacao.exercicio = plano_analitica.exercicio )
                      join tcmgo.orgao_plano_banco
                        on ( plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       and   plano_analitica.exercicio = orgao_plano_banco.exercicio )

                       --- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                           join tesouraria.boletim
                             on ( arrecadacao.cod_boletim  = boletim.cod_boletim
                            and   arrecadacao.exercicio    = boletim.exercicio
                            and   arrecadacao.cod_entidade = boletim.cod_entidade )
                           join ( select boletim_fechado.cod_boletim
                                       , boletim_fechado.exercicio
                                       , boletim_fechado.cod_entidade
                                    from tesouraria.boletim_fechado
                                    join tesouraria.boletim_liberado
                                      on ( boletim_fechado.cod_boletim            = boletim_liberado.cod_boletim
                                     and   boletim_fechado.cod_entidade           = boletim_liberado.cod_entidade
                                     and   boletim_fechado.exercicio              = boletim_liberado.exercicio
                                     and   boletim_fechado.timestamp_fechamento   = boletim_liberado.timestamp_fechamento )
                                  where not exists ( select 1
                                                       from tesouraria.boletim_reaberto
                                                      where boletim_reaberto.cod_boletim           = boletim_fechado.cod_boletim
                                                        and boletim_reaberto.cod_entidade          = boletim_fechado.cod_entidade
                                                        and boletim_reaberto.exercicio             = boletim_fechado.exercicio
                                                        and boletim_reaberto.timestamp_fechamento  = boletim_fechado.timestamp_fechamento )
                                --  and not exists ( select 1
                                --                     from tesouraria.boletim_liberado_cancelado
                                --                    where boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                                --                      and boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                                --                      and boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                                --                      and boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                                ) as liberados
                             on ( liberados.cod_boletim   = boletim.cod_boletim
                            and   liberados.exercicio     = boletim.exercicio
                            and   liberados.cod_entidade  = boletim.cod_entidade )




                     where to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                     AND arrecadacao.devolucao = TRUE  ";
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSQL .= "\n  and receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ") ";
        }

        $stSQL .= "\n

                     group by tipo_registro
                            , cod_orgao
                            , 1
                            , receita.cod_receita
                            , rubrica
                            , receita.exercicio
                            , conta_receita.descricao
                            , receita.vl_original
                ) AS tabela
        GROUP BY tipo_registro
               , cod_orgao
               , 1
               , rubrica
               , cod_receita
               , exercicio
               , descricao
               , vl_original
        ";

        return $stSQL;
    }

    public function recuperaBalanco(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaBalanco",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaBalanco()
    {

        $stSQL = "
                    SELECT '10' AS tipo_registro
                         , orgao_plano_banco.num_orgao   as cod_orgao
                         , receita.cod_receita
                         , CASE WHEN substr(conta_receita.cod_estrutural, 1, 1) = '9'
                              THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                              ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END as rubrica
                         , '01' as num_unidade
                         , receita.exercicio
                         , conta_receita.descricao
                         , CASE WHEN receita.vl_original < 0.00 THEN (receita.vl_original * -1) ELSE receita.vl_original END AS vl_original
                         , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadacao_mes
                         , ABS((
                                SELECT COALESCE(sum ( arrecadacao_receita.vl_arrecadacao),0.00) - COALESCE(sum(arrecadacao_estornada_receita.vl_estornado),0.00)
                                  FROM tesouraria.arrecadacao_receita
                              
                             LEFT JOIN tesouraria.arrecadacao_estornada_receita
                                    ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
                                   AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
                                   AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
                                   AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao

                                 WHERE arrecadacao_receita.cod_receita = receita.cod_receita
                                   AND arrecadacao_receita.exercicio   = receita.exercicio
                                   AND TO_DATE(arrecadacao_receita.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') <= TO_DATE( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                            )) AS vl_arrecadacao_ate
                      
                      FROM orcamento.receita
                
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                       AND to_date(arrecadacao_receita.timestamp_arrecadacao::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                       
                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
		        ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		       AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		       AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		       AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                       AND TO_DATE(arrecadacao_estornada_receita.timestamp_estornada::VARCHAR,'yyyy-mm-dd') BETWEEN TO_DATE( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND TO_DATE( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       
                     WHERE arrecadacao_receita.exercicio = '".Sessao::getExercicio() . "'";
        
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSQL .= "\n  AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ") ";
        }

        $stSQL .= "\n
                    GROUP BY tipo_registro
                            , cod_orgao
                            , 1
                            , receita.cod_receita
                            , rubrica
                            , receita.exercicio
                            , conta_receita.descricao
                            , receita.vl_original ";

        return $stSQL;
    }

    public function recuperaDetalhamentoFonteRecurso(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaDetalhamentoFonteRecurso",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaDetalhamentoFonteRecurso()
    {

        $stSQL = "
             SELECT tipo_registro
                 , cod_orgao
                 , '01' as num_unidade
                 , rubrica
                 , cod_receita
                 , exercicio
                 , descricao
                 , ABS(vl_original) AS vl_original
                 , ABS(SUM(vl_arrecadacao_mes)) AS vl_arrecadacao_mes
                 , fonte
                 , banco
                 , agencia
                 , conta_corrente
                 , digito
                 , tipo_conta
              FROM (
                    SELECT '12' AS tipo_registro
                         , orgao_plano_banco.num_orgao AS cod_orgao
                         , CASE WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1)::integer = 9
                             THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                             ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END AS rubrica
                          , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                               THEN '03'
                               WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                                 THEN '02'
                                 ELSE '01'
                            END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadacao_mes
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , CASE
                           WHEN  banco.num_banco = '999' THEN '999999999999'
                           ELSE ltrim(split_part(num_conta_corrente,'-',1),'0')
                           END AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                         , recurso.cod_fonte as fonte
                      
                      FROM orcamento.receita
                
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                       
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                       
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                       
                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
		        ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		       AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		       AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		       AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                       AND TO_DATE(arrecadacao_estornada_receita.timestamp_estornada::varchar,'yyyy-mm-dd') BETWEEN TO_DATE( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                       
                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio   = receita.exercicio
                       
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                
               -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
               INNER  JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                            --  AND NOT EXISTS ( SELECT 1
                            --                     FROM tesouraria.boletim_liberado_cancelado
                            --                    WHERE boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                            --                      AND boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                            --                      AND boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                            --                      AND boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     
                     WHERE to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       AND arrecadacao.devolucao = FALSE
                       AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                  
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , recurso.cod_fonte
                         , plano_conta.cod_estrutural

                UNION

                    SELECT '12' AS tipo_registro
                         , orgao_plano_banco.num_orgao AS cod_orgao
                         , CASE WHEN substr(conta_receita.cod_estrutural, 1, 1) = '9'
                             THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                             ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END AS rubrica
                                , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                                    THEN '03'
                                    WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                                     THEN '02'
                                     ELSE '01'
                                 END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , ABS(ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2)) * -1 AS vl_arrecadacao_mes
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , ltrim(split_part(num_conta_corrente,'-',1),'0') AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                         , recurso.cod_fonte as fonte
                         
                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                
                INNER JOIN orcamento.recurso
                        ON recurso.cod_recurso = receita.cod_recurso
                       AND recurso.exercicio   = receita.exercicio
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                            --  AND NOT EXISTS ( SELECT 1
                            --                     FROM tesouraria.boletim_liberado_cancelado
                            --                    WHERE boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                            --                      AND boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                            --                      AND boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                            --                      AND boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                       
                     WHERE to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN  to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       AND arrecadacao.devolucao = TRUE
                       AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                  
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , recurso.cod_fonte
                         , tipo_conta
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , 1
            , rubrica
            , cod_receita
            , exercicio
            , descricao
            , vl_original
            , fonte
            , banco
            , agencia
            , conta_corrente
            , digito
            , tipo_conta ";
    
        return $stSQL;
    }

    public function recuperaMovimentacaoFinanceira(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaMovimentacaoFinanceira",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaMovimentacaoFinanceira()
    {

        $stSQL = "
            SELECT tipo_registro
                 , cod_orgao
                 , '01' as num_unidade
                 , rubrica
                 , cod_receita
                 , exercicio
                 , descricao
                 , ABS(vl_original) AS vl_original
                 , ABS(SUM(vl_arrecadacao_mes)) AS vl_arrecadacao_mes
                 , banco
                 , agencia
                 , conta_corrente
                 , digito
                 , tipo_conta
              FROM (
                    SELECT '11' AS tipo_registro
                         , orgao_plano_banco.num_orgao AS cod_orgao
                         , CASE WHEN substr(conta_receita.cod_estrutural::varchar, 1, 1)::integer = 9
                             THEN substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                             ELSE '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END AS rubrica
                                , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01')
                                     THEN '03'
                                     WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4')
                                      THEN '02'
                                      ELSE '01'
                                  END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , ABS(SUM(COALESCE(arrecadacao_receita.vl_arrecadacao,0.00))) AS vl_arrecadacao_mes
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , CASE
                           WHEN  banco.num_banco = '999' THEN '999999999999'
                           ELSE ltrim(split_part(num_conta_corrente,'-',1),'0')
                           END AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito

                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                       
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao

                 LEFT JOIN tesouraria.arrecadacao_estornada_receita
		        ON arrecadacao_estornada_receita.cod_arrecadacao       = arrecadacao_receita.cod_arrecadacao
		       AND arrecadacao_estornada_receita.cod_receita           = arrecadacao_receita.cod_receita
		       AND arrecadacao_estornada_receita.exercicio             = arrecadacao_receita.exercicio
		       AND arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao_receita.timestamp_arrecadacao
                       AND TO_DATE(arrecadacao_estornada_receita.timestamp_estornada::varchar,'yyyy-mm-dd') BETWEEN TO_DATE( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                            --  AND NOT EXISTS ( SELECT 1
                            --                     FROM tesouraria.boletim_liberado_cancelado
                            --                    WHERE boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                            --                      AND boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                            --                      AND boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                            --                      AND boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                       
                     WHERE to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       AND arrecadacao.devolucao = FALSE
                       AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , tipo_conta

                UNION

                    SELECT '11'                             AS tipo_registro
                         , orgao_plano_banco.num_orgao      AS cod_orgao
                         , CASE
                            WHEN substr(conta_receita.cod_estrutural, 1, 1) = '9' THEN
                                       substr(replace(conta_receita.cod_estrutural,'.',''),1,9)
                            ELSE
                                '0' || substr(replace(conta_receita.cod_estrutural,'.',''),1,8)
                           END                              AS rubrica
                                , CASE WHEN (substr(plano_conta.cod_estrutural, 1, 12) = '1.1.1.1.1.01') THEN
                                          '03'
                                     WHEN (substr(plano_conta.cod_estrutural, 1, 5) = '1.1.4') THEN
                                          '02'
                                     ELSE
                                          '01'
                                END as tipo_conta
                         , receita.cod_receita
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , ABS(ROUND(SUM(arrecadacao_receita.vl_arrecadacao), 2)) * -1 AS vl_arrecadacao_mes
                         , banco.num_banco AS banco
                         , ltrim(replace(num_agencia,'-',''),'0') AS agencia
                         , ltrim(split_part(num_conta_corrente,'-',1),'0') AS conta_corrente
                         , ltrim(split_part(num_conta_corrente,'-',2),'0') AS digito
                      
                      FROM orcamento.receita
                      
                INNER JOIN orcamento.conta_receita
                        ON receita.exercicio = conta_receita.exercicio
                       AND receita.cod_conta = conta_receita.cod_conta
                      
                INNER JOIN tesouraria.arrecadacao_receita
                        ON receita.cod_receita = arrecadacao_receita.cod_receita
                       AND receita.exercicio   = arrecadacao_receita.exercicio
                      
                INNER JOIN tesouraria.arrecadacao
                        ON arrecadacao_receita.cod_arrecadacao        = arrecadacao.cod_arrecadacao
                       AND arrecadacao_receita.exercicio              = arrecadacao.exercicio
                       AND arrecadacao_receita.timestamp_arrecadacao  = arrecadacao.timestamp_arrecadacao
                      
                INNER JOIN contabilidade.plano_analitica
                        ON arrecadacao.cod_plano = plano_analitica.cod_plano
                       AND arrecadacao.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_banco
                        ON plano_banco.cod_plano = plano_analitica.cod_plano
                       AND plano_banco.exercicio = plano_analitica.exercicio
                      
                INNER JOIN contabilidade.plano_conta
                        ON plano_conta.cod_conta = plano_analitica.cod_conta
                       AND plano_conta.exercicio = plano_analitica.exercicio
                      
                INNER JOIN monetario.conta_corrente
                        ON conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                       AND conta_corrente.cod_agencia        = plano_banco.cod_agencia
                       AND conta_corrente.cod_banco          = plano_banco.cod_banco
                      
                INNER JOIN monetario.agencia
                        ON agencia.cod_agencia = conta_corrente.cod_agencia
                       AND agencia.cod_banco   = conta_corrente.cod_banco
                      
                INNER JOIN monetario.banco
                        ON banco.cod_banco = agencia.cod_banco
                      
                INNER JOIN tcmgo.orgao_plano_banco
                        ON plano_analitica.cod_plano = orgao_plano_banco.cod_plano
                       AND plano_analitica.exercicio = orgao_plano_banco.exercicio
                        
                -- ligação com o botetim pra garantir q a arrecadação ja foi contabilizada
                INNER JOIN tesouraria.boletim
                        ON arrecadacao.cod_boletim  = boletim.cod_boletim
                       AND arrecadacao.exercicio    = boletim.exercicio
                       AND arrecadacao.cod_entidade = boletim.cod_entidade
                      
                INNER JOIN ( SELECT boletim_fechado.cod_boletim
                                  , boletim_fechado.exercicio
                                  , boletim_fechado.cod_entidade
                               FROM tesouraria.boletim_fechado
                               JOIN tesouraria.boletim_liberado
                                 ON boletim_fechado.cod_boletim          = boletim_liberado.cod_boletim
                                AND boletim_fechado.cod_entidade         = boletim_liberado.cod_entidade
                                AND boletim_fechado.exercicio            = boletim_liberado.exercicio
                                AND boletim_fechado.timestamp_fechamento = boletim_liberado.timestamp_fechamento
                              WHERE not exists ( SELECT 1
                                                   FROM tesouraria.boletim_reaberto
                                                  WHERE boletim_reaberto.cod_boletim          = boletim_fechado.cod_boletim
                                                    AND boletim_reaberto.cod_entidade         = boletim_fechado.cod_entidade
                                                    AND boletim_reaberto.exercicio            = boletim_fechado.exercicio
                                                    AND boletim_reaberto.timestamp_fechamento = boletim_fechado.timestamp_fechamento
                                               )
                            --  AND NOT EXISTS ( SELECT 1
                            --                     FROM tesouraria.boletim_liberado_cancelado
                            --                    WHERE boletim_liberado_cancelado.cod_boletim          = boletim_liberado_cancelado.cod_boletim
                            --                      AND boletim_liberado_cancelado.cod_entidade         = boletim_liberado_cancelado.cod_entidade
                            --                      AND boletim_liberado_cancelado.exercicio            = boletim_liberado_cancelado.exercicio
                            --                      AND boletim_liberado_cancelado.timestamp_fechamento = boletim_liberado_cancelado.timestamp_fechamento   )
                           )                           AS liberados
                        ON liberados.cod_boletim  = boletim.cod_boletim
                       AND liberados.exercicio    = boletim.exercicio
                       AND liberados.cod_entidade = boletim.cod_entidade
                     
                     WHERE to_date(arrecadacao_receita.timestamp_arrecadacao::varchar,'yyyy-mm-dd') BETWEEN  to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
                       AND arrecadacao.devolucao = TRUE
                       AND receita.cod_entidade in ( " .  $this->getDado ( 'stEntidades' ) . ")
                       
                  GROUP BY tipo_registro
                         , cod_orgao
                         , 1
                         , receita.cod_receita
                         , rubrica
                         , receita.exercicio
                         , conta_receita.descricao
                         , receita.vl_original
                         , banco.num_banco
                         , agencia.num_agencia
                         , conta_corrente.num_conta_corrente
                         , tipo_conta
            ) AS tabela
     GROUP BY tipo_registro
            , cod_orgao
            , 1
            , rubrica
            , cod_receita
            , exercicio
            , descricao
            , vl_original
            , banco
            , agencia
            , conta_corrente
            , digito
            , tipo_conta
         ";

        return $stSQL;
    }

    public function recuperaArquivoOrcamento10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaArquivoOrcamento10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaArquivoOrcamento10()
    {
        $stSQL = "SELECT
                            10 as tipo_registro
                            , ( SELECT SUBSTR(valor,1,1)
                                FROM administracao.configuracao_entidade 
                                WHERE configuracao_entidade.exercicio = receita.exercicio
                                AND configuracao_entidade.cod_entidade = receita.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                            ) as cod_orgao
                            , (  SELECT SUBSTR(valor,3,2) 
                                FROM administracao.configuracao_entidade 
                                WHERE configuracao_entidade.exercicio = receita.exercicio
                                AND configuracao_entidade.cod_entidade = receita.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                            ) as cod_unidade
                            , rubrica
                            , TRIM(descricao) as especificacao
                            , tabela.vl_original AS vl_previsto
                            , tabela.cod_recurso as cod_fonte_recurso
                      FROM
                            (
                                SELECT
                                        tabela.cod_recurso
                                        , REPLACE(tabela.cod_estrutural::VARCHAR,'.','') AS rubrica
                                        , tabela.vl_original
                                        , tabela.descricao
                                        , tabela.cod_receita                                        
                                FROM tcmgo.arquivo_exportacao_orcamento_rec('".$this->getDado("exercicio")     ."',
                                                                            '".$this->getDado("stEntidades")  ."',
                                                                            '".$this->getDado("dtInicio")     ."',
                                                                            '".$this->getDado("dtFim")       ."')
                                AS tabela( 
                                            cod_estrutural     varchar,           
                                            cod_recurso        varchar(13),
                                            cod_receita        integer,
                                            descricao          varchar,           
                                            vl_original        numeric
                                        )
                                ORDER BY tabela.cod_estrutural
                            ) AS tabela
                        JOIN orcamento.receita
                            ON receita.cod_receita = tabela.cod_receita
                            AND receita.exercicio = '".$this->getDado("exercicio")."'
        ";
        return $stSQL;
    }

    public function recuperaArquivoOrcamento11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaArquivoOrcamento11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaArquivoOrcamento11()
    {
        $stSQL = "SELECT
                             11 as tipo_registro
                            , ( SELECT SUBSTR(valor,1,1)
                                FROM administracao.configuracao_entidade 
                                WHERE configuracao_entidade.exercicio = receita.exercicio
                                AND configuracao_entidade.cod_entidade = receita.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                            ) as cod_orgao
                            , (  SELECT SUBSTR(valor,3,2) 
                                FROM administracao.configuracao_entidade 
                                WHERE configuracao_entidade.exercicio = receita.exercicio
                                AND configuracao_entidade.cod_entidade = receita.cod_entidade
                                AND  parametro = 'tc_ug_orgaounidade'
                            ) as cod_unidade
                            , rubrica as rubrica
                            , tabela.cod_recurso as cod_fonte_recurso                            
                            , vl_fonte_recurso
                            , '' as brancos
                      FROM
                            (
                                SELECT
                                        tabela.cod_recurso
                                        , REPLACE(tabela.cod_estrutural::VARCHAR,'.','') AS rubrica                                        
                                        , SUM(tabela.vl_original) as vl_fonte_recurso
                                        , tabela.cod_receita                                     
                                FROM tcmgo.arquivo_exportacao_orcamento_rec('".$this->getDado("exercicio")     ."',
                                                                            '".$this->getDado("stEntidades")  ."',
                                                                            '".$this->getDado("dtInicio")     ."',
                                                                            '".$this->getDado("dtFim")       ."')
                                AS tabela( 
                                            cod_estrutural     varchar,           
                                            cod_recurso        varchar(13),
                                            cod_receita        integer,
                                            descricao          varchar,           
                                            vl_original        numeric
                                        )
                                 
                                GROUP BY tabela.cod_recurso, tabela.cod_estrutural, tabela.cod_receita
                                ORDER BY  cod_recurso , cod_estrutural
                            ) AS tabela
                    JOIN orcamento.receita
                            ON receita.cod_receita = tabela.cod_receita
                            AND receita.exercicio = '".$this->getDado("exercicio")."'
        ";
        return $stSQL;
    }

}

?>