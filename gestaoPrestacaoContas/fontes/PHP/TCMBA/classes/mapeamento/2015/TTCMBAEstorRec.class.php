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
    * Data de Criação: 14/06/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63115 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAEstorRec extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    function __construct()
    {
        parent::Persistente();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    
    function recuperaDadosTribunal(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stSql = $this->montaRecuperaDadosTribunal().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaDadosTribunal()
    {
        
        $stSql = "   SELECT                              
                            1 AS tipo_registro
                          , '".$this->getDado('unidade_gestora')."' AS unidade_gestora
                          , SUBSTR(REPLACE(cod_estrutural, '.', ''), 0, 8) AS item_receita
                          , TO_CHAR(dt_arrecadacao,'yyyymm') AS competencia
                          , REPLACE(conta_contabil_recebedora, '.', '') AS conta_contabil
                          , vl_arrecadacao AS vl_estorno
                          , TO_CHAR(dt_arrecadacao,'ddmmyyyy') AS dt_estorno
                          , TO_CHAR(dt_arrecadacao,'ddmmyyyy') AS dt_receita
                  
                        FROM (
                        SELECT arrecadacao.cod_arrecadacao
                           , vl_estornado AS vl_arrecadacao
                           , arrecadacao_estornada_receita.timestamp_estornada AS dt_arrecadacao
                           , arrecadacao_receita.cod_receita
                           , conta_receita.descricao
                           , conta_receita.cod_estrutural
                           , recurso.cod_fonte
                           , recurso.cod_recurso
                           , recurso.nom_recurso
                           , arrecadacao.cod_plano
                           , conta_corrente.num_conta_corrente
                           , banco.nom_banco
                           , entidade.cod_entidade
                           , entidade_cgm.nom_cgm AS nom_entidade
                           --, 'EST' AS tipo_arrecadacao
                           , arrecadacao.exercicio
                           , timestamp_estornada AS teste
                           , plano_conta.cod_estrutural AS conta_contabil_recebedora
                        FROM tesouraria.arrecadacao   
                  INNER JOIN tesouraria.arrecadacao_receita
                          ON arrecadacao_receita.cod_arrecadacao = arrecadacao.cod_arrecadacao
                         AND  arrecadacao_receita.exercicio = arrecadacao.exercicio
                         AND  arrecadacao_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
                  
                  INNER JOIN  contabilidade.plano_banco
                          ON  plano_banco.cod_plano = arrecadacao.cod_plano
                         AND  plano_banco.exercicio = arrecadacao.exercicio
                  INNER JOIN  monetario.conta_corrente
                          ON  conta_corrente.cod_banco = plano_banco.cod_banco
                         AND  conta_corrente.cod_agencia = plano_banco.cod_agencia
                         AND  conta_corrente.cod_conta_corrente = plano_banco.cod_conta_corrente
                  INNER JOIN  monetario.banco
                          ON  banco.cod_banco = conta_corrente.cod_banco
                  INNER JOIN  orcamento.entidade
                          ON  entidade.cod_entidade = arrecadacao.cod_entidade
                         AND  entidade.exercicio = arrecadacao.exercicio
                  INNER JOIN  sw_cgm AS entidade_cgm
                          ON  entidade_cgm.numcgm = entidade.numcgm
                  INNER JOIN  orcamento.receita
                          ON  receita.exercicio = arrecadacao_receita.exercicio
                         AND  receita.cod_receita = arrecadacao_receita.cod_receita
                  INNER JOIN  orcamento.conta_receita
                          ON  conta_receita.exercicio = receita.exercicio
                         AND  conta_receita.cod_conta = receita.cod_conta
                  INNER JOIN  orcamento.recurso AS recurso
                          ON  recurso.exercicio = receita.exercicio
                         AND  recurso.cod_recurso = receita.cod_recurso
                  INNER JOIN  tesouraria.arrecadacao_estornada_receita
                          ON  arrecadacao_estornada_receita.cod_arrecadacao = arrecadacao.cod_arrecadacao
                         AND  arrecadacao_estornada_receita.exercicio = arrecadacao.exercicio
                         AND  arrecadacao_estornada_receita.timestamp_arrecadacao = arrecadacao.timestamp_arrecadacao
                  
                  INNER JOIN contabilidade.configuracao_lancamento_receita
                          ON configuracao_lancamento_receita.cod_conta_receita = receita.cod_receita
                         AND configuracao_lancamento_receita.exercicio = receita.exercicio
                  
                  INNER JOIN contabilidade.plano_conta
                          ON plano_conta.cod_conta = configuracao_lancamento_receita.cod_conta
                         AND plano_conta.exercicio = configuracao_lancamento_receita.exercicio
                         
                  ) AS relacao
                   
                   WHERE relacao.exercicio = '".$this->getDado('exercicio')."'
                     AND relacao.dt_arrecadacao BETWEEN to_date('".$this->getDado('dt_inicial')."'::varchar,'yyyy-mm-dd') AND to_date('".$this->getDado('dt_final')."'::varchar,'dd/mm/yyyy')
                     AND relacao.cod_entidade IN (".$this->getDado('entidades').")
                     
                GROUP BY  tipo_registro
                        , unidade_gestora
                        , item_receita
                        , competencia
                        , conta_contabil
                        , vl_estorno
                        , dt_estorno
                        , dt_receita ";
                     
        return $stSql;
    }

}

?>