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
    * Data de Criação: 27/10/2015

    * @author Analista: Valtair
    * @author Desenvolvedor: Lisiane Morais

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCMBAEditalDotacao extends Persistente
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
        $stSql = " SELECT DISTINCT 1 AS tipo_registro
                        , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                        , edital.exercicio||LPAD(edital.num_edital::varchar, 8,'0') AS nu_edital
                        , CASE WHEN licitacao.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 1 THEN 1
                            WHEN licitacao.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 2 THEN 2
                            WHEN licitacao.cod_modalidade = 3 AND licitacao.registro_precos = TRUE THEN 3
                            WHEN licitacao.cod_modalidade = 5 THEN 4
                            WHEN licitacao.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 1 THEN 5
                            WHEN licitacao.cod_modalidade = 1 AND tipo_objeto.cod_tipo_objeto = 2 THEN 6
                            WHEN licitacao.cod_modalidade = 4 THEN 7
                            WHEN licitacao.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 1 THEN 10
                            WHEN licitacao.cod_modalidade = 2 AND tipo_objeto.cod_tipo_objeto = 2 THEN 12
                            WHEN licitacao.cod_modalidade = 6 AND licitacao.registro_precos = FALSE THEN 14
                            WHEN licitacao.cod_modalidade = 7 AND licitacao.registro_precos = FALSE THEN 15
                            WHEN licitacao.cod_modalidade = 1 AND licitacao.registro_precos = TRUE THEN 16
                            WHEN licitacao.cod_modalidade = 2 AND licitacao.registro_precos = TRUE THEN 17
                            WHEN licitacao.cod_modalidade = 6 AND licitacao.registro_precos = TRUE THEN 18
                            WHEN licitacao.cod_modalidade = 7 AND licitacao.registro_precos = TRUE THEN 19
                            WHEN licitacao.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 4 THEN 22
                            WHEN licitacao.cod_modalidade = 3 AND tipo_objeto.cod_tipo_objeto = 3 THEN 23
                        END AS edital_modalidade
                        , licitacao.num_orgao
                        , licitacao.num_unidade
                        , despesa.cod_funcao AS cod_funcao
                        , despesa.cod_subfuncao AS cod_subfuncao
                        , ppa.programa.num_programa AS cod_programa
                        , orcamento.fn_consulta_tipo_pao(despesa.exercicio, despesa.num_pao) AS tipo_projeto
                        , acao.num_acao AS num_projeto
                        , despesa.cod_recurso AS fonte_recurso      
                        , (LPAD(''||REPLACE(conta_despesa.cod_estrutural, '.', ''),8, '')) AS elemento_despesa
                        , TO_CHAR(edital.dt_aprovacao_juridico, 'yyyymm') AS competencia
                     FROM licitacao.licitacao
               INNER JOIN licitacao.edital
                       ON edital.cod_licitacao       = licitacao.cod_licitacao
                      AND edital.cod_modalidade      = licitacao.cod_modalidade
                      AND edital.cod_entidade        = licitacao.cod_entidade
                      AND edital.exercicio_licitacao = licitacao.exercicio
              INNER JOIN compras.tipo_objeto
                      ON tipo_objeto.cod_tipo_objeto = licitacao.cod_tipo_objeto
              INNER JOIN compras.mapa
                      ON licitacao.exercicio_mapa = mapa.exercicio
                     AND licitacao.cod_mapa = mapa.cod_mapa
              INNER JOIN compras.mapa_solicitacao
                      ON mapa_solicitacao.exercicio = mapa.exercicio
                     AND mapa_solicitacao.cod_mapa = mapa.cod_mapa
              INNER JOIN compras.solicitacao_homologada
                      ON solicitacao_homologada.exercicio=mapa_solicitacao.exercicio_solicitacao
                     AND solicitacao_homologada.cod_entidade=mapa_solicitacao.cod_entidade
                     AND solicitacao_homologada.cod_solicitacao=mapa_solicitacao.cod_solicitacao
              INNER JOIN licitacao.homologacao
                      ON homologacao.cod_licitacao=licitacao.cod_licitacao
                     AND homologacao.cod_modalidade=licitacao.cod_modalidade
                     AND homologacao.cod_entidade=licitacao.cod_entidade
                     AND homologacao.exercicio_licitacao=licitacao.exercicio
                     AND (
                           SELECT homologacao_anulada.num_homologacao FROM licitacao.homologacao_anulada
                            WHERE homologacao_anulada.cod_licitacao=licitacao.cod_licitacao
                              AND homologacao_anulada.cod_modalidade=licitacao.cod_modalidade
                              AND homologacao_anulada.cod_entidade=licitacao.cod_entidade
                              AND homologacao_anulada.exercicio_licitacao=licitacao.exercicio
                              AND homologacao.num_homologacao=homologacao_anulada.num_homologacao
                              AND homologacao.cod_item=homologacao_anulada.cod_item
                         ) IS NULL
              INNER JOIN compras.solicitacao_homologada_reserva
                      ON solicitacao_homologada_reserva.exercicio=solicitacao_homologada.exercicio
                     AND solicitacao_homologada_reserva.cod_entidade=solicitacao_homologada.cod_entidade
                     AND solicitacao_homologada_reserva.cod_solicitacao=solicitacao_homologada.cod_solicitacao
                     AND solicitacao_homologada_reserva.cod_item=homologacao.cod_item
              INNER JOIN orcamento.despesa
                      ON despesa.exercicio = solicitacao_homologada_reserva.exercicio
                     AND despesa.cod_despesa = solicitacao_homologada_reserva.cod_despesa
              INNER JOIN orcamento.conta_despesa
                      ON conta_despesa.exercicio = despesa.exercicio
                     AND conta_despesa.cod_conta = despesa.cod_conta
              INNER JOIN compras.mapa_item_dotacao
                      ON mapa_item_dotacao.exercicio=solicitacao_homologada.exercicio
                     AND mapa_item_dotacao.cod_entidade=solicitacao_homologada.cod_entidade
                     AND mapa_item_dotacao.cod_solicitacao=solicitacao_homologada.cod_solicitacao
                     AND mapa_item_dotacao.cod_item=homologacao.cod_item
                     AND mapa_item_dotacao.cod_mapa=mapa.cod_mapa
                     AND mapa_item_dotacao.cod_despesa=despesa.cod_despesa
              INNER JOIN orcamento.programa
                      ON programa.cod_programa = despesa.cod_programa
                     AND programa.exercicio    = despesa.exercicio
              INNER JOIN orcamento.programa_ppa_programa
                      ON programa_ppa_programa.cod_programa = programa.cod_programa
                     AND programa_ppa_programa.exercicio    = programa.exercicio
             INNER JOIN ppa.programa
                     ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa
              INNER JOIN orcamento.pao
                      ON pao.num_pao   = despesa.num_pao
                     AND pao.exercicio = despesa.exercicio
              INNER JOIN orcamento.pao_ppa_acao
                      ON pao_ppa_acao.num_pao = pao.num_pao
                     AND pao_ppa_acao.exercicio = pao.exercicio
              INNER JOIN ppa.acao
                      ON acao.cod_acao = pao_ppa_acao.cod_acao  
                   WHERE homologacao.cod_entidade IN (".$this->getDado('entidades').")              
                     AND licitacao.exercicio = '".$this->getDado('exercicio')."'
                     AND TO_DATE(TO_CHAR(dt_aprovacao_juridico,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','dd/mm/yyyy')
                                                                                                 AND TO_DATE('".$this->getDado('dt_final')."','dd/mm/yyyy')
                     AND licitacao.cod_modalidade NOT IN (8,9)      
             ";                    
        return $stSql;
    }

}

?>