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

    $Id: TTCMGOPassivoFinanceiro.class.php 65168 2016-04-29 16:36:09Z michel $

    * Casos de uso: uc-06.04.00
*/

include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php";

class TTCMGOPassivoFinanceiro extends TContabilidadeBalancoFinanceiro
{
    public function __construct()
    {
        parent::TContabilidadeBalancoFinanceiro();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaArquivoExportacao10(&$rsRecordSet, $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaArquivoExportacao10();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
    }

    public function montaRecuperaArquivoExportacao10()
    {
        $stSql = "  SELECT 
                            '10' AS tipo_registro
                            , consulta.*
                            , COALESCE((( total_creditos - total_debitos ) + saldo_anterior ),0.00) as saldo_atual
                            , '' AS brancos
                            , 0.00 AS vl_encampacao
                            , 0.00 AS vl_cancelamento
                    from (
                            select 
                                REPLACE(plano_conta.cod_estrutural,'.','') as cod_estrutural
                               , plano_conta.nom_conta
                               , plano_conta.exercicio
                               , balancete_extmmaa.tipo_lancamento
                               , balancete_extmmaa.sub_tipo_lancamento
                               , (string_to_array(configuracao_entidade.valor, '_'))[1] AS num_orgao
                               , (string_to_array(configuracao_entidade.valor, '_'))[2] AS num_unidade
                               ---- soma dos debitos
                               , coalesce(  ( select sum ( vl_lancamento  )
                                                from contabilidade.conta_debito
                                                JOIN contabilidade.valor_lancamento
                                                  on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                 and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                 and   conta_debito.tipo         = valor_lancamento.tipo
                                                 and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                 and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                 and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                               where conta_debito.exercicio = plano_analitica.exercicio
                                                 and conta_debito.cod_plano = plano_analitica.cod_plano
                                                 and valor_lancamento.tipo <> 'I'
                                                 and conta_debito.cod_entidade = configuracao_entidade.cod_entidade
                                            ) 
                                          , 0.00 ) as total_debitos
                               ---- soma dos creditos
                               ,coalesce ( (( select sum ( vl_lancamento  )
                                              from contabilidade.conta_credito
                                              JOIN contabilidade.valor_lancamento
                                                on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                               and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                               and   conta_credito.tipo         = valor_lancamento.tipo
                                               and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                               and   conta_credito.sequencia    = valor_lancamento.sequencia
                                               and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                             where conta_credito.exercicio = plano_analitica.exercicio
                                               and conta_credito.cod_plano = plano_analitica.cod_plano
                                               and valor_lancamento.tipo <> 'I'
                                               and conta_credito.cod_entidade = configuracao_entidade.cod_entidade
                                            )
                                            *-1)
                                          , 0.00 ) as total_creditos

                                ---- saldo inicial
                               , COALESCE(((
                                      coalesce(
                                      ( select sum ( vl_lancamento  )
                                               from contabilidade.conta_debito
                                               JOIN contabilidade.valor_lancamento
                                                 on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                and   conta_debito.tipo         = valor_lancamento.tipo
                                                and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                              where conta_debito.exercicio = plano_analitica.exercicio
                                                and conta_debito.cod_plano = plano_analitica.cod_plano
                                                and valor_lancamento.tipo = 'I'
                                                and conta_debito.cod_entidade = configuracao_entidade.cod_entidade ), 0.00 )
                                        +
                                       coalesce (
                                       ( select sum ( vl_lancamento  )
                                           from contabilidade.conta_credito
                                           JOIN contabilidade.valor_lancamento
                                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                            and   conta_credito.tipo         = valor_lancamento.tipo
                                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                          where conta_credito.exercicio = plano_analitica.exercicio
                                            and conta_credito.cod_plano = plano_analitica.cod_plano
                                            and valor_lancamento.tipo = 'I'
                                            and conta_credito.cod_entidade = configuracao_entidade.cod_entidade ), 0.00 )
                                  )*-1),0.00)  as saldo_anterior
                            from contabilidade.plano_conta
                            JOIN contabilidade.plano_analitica
                                on ( plano_analitica.cod_conta = plano_conta.cod_conta
                                and   plano_analitica.exercicio = plano_conta.exercicio )
                            JOIN tcmgo.balancete_extmmaa
                                on ( plano_analitica.exercicio = balancete_extmmaa.exercicio
                                and   plano_analitica.cod_plano = balancete_extmmaa.cod_plano )
                            JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade IN ( ".$this->getDado( 'stEntidades' )." )
                             AND configuracao_entidade.exercicio = balancete_extmmaa.exercicio
                             AND configuracao_entidade.parametro = 'tc_ug_orgaounidade'
                            where plano_conta.exercicio = '".$this->getDado('exercicio')."'
                            order by  plano_conta.cod_estrutural
                    ) as consulta
                    where saldo_anterior <> 0
                    or total_debitos <> 0
                    or total_creditos <> 0
                    ORDER BY cod_estrutural ";
        return $stSql;
    }

    public function recuperaArquivoExportacao11(&$rsRecordSet, $boTransacao = "")
    {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaArquivoExportacao11();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    return $obErro;
    }

    public function montaRecuperaArquivoExportacao11()
    {
        $stSql = "  SELECT 
                            '11' AS tipo_registro
                            , consulta.*
                            , COALESCE((( total_creditos - total_debitos ) + saldo_anterior ),0.00) as saldo_atual
                            , '' AS brancos
                            , 0.00 AS vl_encampacao
                            , 0.00 AS vl_cancelamento

                    FROM (
                            select 
                                    REPLACE(plano_conta.cod_estrutural,'.','') as cod_estrutural
                                    , plano_conta.nom_conta
                                    , plano_conta.exercicio
                                    , balancete_extmmaa.tipo_lancamento
                                    , balancete_extmmaa.sub_tipo_lancamento
                                    , (string_to_array(configuracao_entidade.valor, '_'))[1] AS num_orgao
                                    , (string_to_array(configuracao_entidade.valor, '_'))[2] AS num_unidade
                                    , plano_recurso.cod_recurso
                                    ---- soma dos debitos
                                    , coalesce( ( select sum ( vl_lancamento  )
                                                    from contabilidade.conta_debito
                                                    JOIN contabilidade.valor_lancamento
                                                       on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                      and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                      and   conta_debito.tipo         = valor_lancamento.tipo
                                                      and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                      and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                      and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                                    where conta_debito.exercicio = plano_analitica.exercicio
                                                    and conta_debito.cod_plano = plano_analitica.cod_plano
                                                    and valor_lancamento.tipo <> 'I'
                                                    and conta_debito.cod_entidade = configuracao_entidade.cod_entidade ),0.00 
                                    ) as total_debitos
                                    ---- soma dos creditos
                                    ,coalesce ( (( select sum ( vl_lancamento  )
                                                   from contabilidade.conta_credito
                                                   JOIN contabilidade.valor_lancamento
                                                     on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                                    and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                                    and   conta_credito.tipo         = valor_lancamento.tipo
                                                    and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                                    and   conta_credito.sequencia    = valor_lancamento.sequencia
                                                    and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                                  where conta_credito.exercicio = plano_analitica.exercicio
                                                    and conta_credito.cod_plano = plano_analitica.cod_plano
                                                    and valor_lancamento.tipo <> 'I'
                                                    and conta_credito.cod_entidade = configuracao_entidade.cod_entidade )*-1), 0.00
                                    ) as total_creditos
                                    ---- saldo inicial
                                    , COALESCE(((
                                        coalesce(
                                        ( select sum ( vl_lancamento  )
                                               from contabilidade.conta_debito
                                               JOIN contabilidade.valor_lancamento
                                                 on ( conta_debito.exercicio    = valor_lancamento.exercicio
                                                and   conta_debito.cod_entidade = valor_lancamento.cod_entidade
                                                and   conta_debito.tipo         = valor_lancamento.tipo
                                                and   conta_debito.cod_lote     = valor_lancamento.cod_lote
                                                and   conta_debito.sequencia    = valor_lancamento.sequencia
                                                and   conta_debito.tipo_valor   = valor_lancamento.tipo_valor )
                                              where conta_debito.exercicio = plano_analitica.exercicio
                                                and conta_debito.cod_plano = plano_analitica.cod_plano
                                                and valor_lancamento.tipo = 'I'
                                                and conta_debito.cod_entidade = configuracao_entidade.cod_entidade ), 0.00 )
                                        +
                                        coalesce (
                                        ( select sum ( vl_lancamento  )
                                           from contabilidade.conta_credito
                                           JOIN contabilidade.valor_lancamento
                                             on ( conta_credito.exercicio    = valor_lancamento.exercicio
                                            and   conta_credito.cod_entidade = valor_lancamento.cod_entidade
                                            and   conta_credito.tipo         = valor_lancamento.tipo
                                            and   conta_credito.cod_lote     = valor_lancamento.cod_lote
                                            and   conta_credito.sequencia    = valor_lancamento.sequencia
                                            and   conta_credito.tipo_valor   = valor_lancamento.tipo_valor )
                                          where conta_credito.exercicio = plano_analitica.exercicio
                                            and conta_credito.cod_plano = plano_analitica.cod_plano
                                            and valor_lancamento.tipo = 'I'
                                            and conta_credito.cod_entidade = configuracao_entidade.cod_entidade ), 0.00 )
                                    )*-1),0.00)  as saldo_anterior
                            FROM contabilidade.plano_conta
                            JOIN contabilidade.plano_analitica
                                on plano_analitica.cod_conta = plano_conta.cod_conta
                                and   plano_analitica.exercicio = plano_conta.exercicio
                            JOIN contabilidade.plano_recurso
                                ON plano_recurso.exercicio = plano_analitica.exercicio
                                AND plano_recurso.cod_plano = plano_analitica.cod_plano 
                            JOIN tcmgo.balancete_extmmaa
                              ON plano_analitica.exercicio = balancete_extmmaa.exercicio
                             AND plano_analitica.cod_plano = balancete_extmmaa.cod_plano
                            JOIN administracao.configuracao_entidade
                              ON configuracao_entidade.cod_entidade IN ( ".$this->getDado( 'stEntidades' )." )
                             AND configuracao_entidade.exercicio = balancete_extmmaa.exercicio
                             AND configuracao_entidade.parametro = 'tc_ug_orgaounidade'
                            where plano_conta.exercicio = '".$this->getDado('exercicio')."'
                            order by  plano_conta.cod_estrutural
                    ) as consulta
                    where saldo_anterior <> 0
                    or total_debitos <> 0
                    or total_creditos <> 0
                    ORDER BY cod_estrutural 
            ";
        return $stSql;
    }

}

?>
