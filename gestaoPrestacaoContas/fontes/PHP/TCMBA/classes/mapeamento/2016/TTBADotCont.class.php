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
    * Data de Criação: 04/09/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 63694 $
    $Name$
    $Author: domluc $
    $Date: 2008-08-18 10:43:34 -0300 (Seg, 18 Ago 2008) $

    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TTBADotCont extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBADotacao()
{
    parent::TOrcamentoDespesa();
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
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
    $stSql .= " SELECT 1 AS tipo_registro
                     , ".$this->getDado('unidade_gestora')." AS unidade_gestora
                     , contrato.num_contrato        
                     , solicitacao_item_dotacao.cod_despesa          
                     , despesa.num_unidade
                     , despesa.exercicio        
                     , despesa.cod_funcao        
                     , despesa.cod_subfuncao          
                     , despesa.cod_programa          
                     , despesa.num_pao          
                     , REPLACE(conta_despesa.cod_estrutural,'.','') AS estrutural          
                     , orcamento.fn_consulta_tipo_pao(despesa.exercicio,despesa.num_pao) AS tipo_pao          
                     , despesa.num_orgao     
                     , despesa.cod_recurso
                     , TO_CHAR(contrato.dt_assinatura,'YYYYMM') AS competencia

                 FROM licitacao.licitacao

           INNER JOIN licitacao.contrato_licitacao
                   ON contrato_licitacao.cod_licitacao       = licitacao.cod_licitacao
                  AND contrato_licitacao.cod_modalidade      = licitacao.cod_modalidade
                  AND contrato_licitacao.cod_entidade        = licitacao.cod_entidade
                  AND contrato_licitacao.exercicio_licitacao = licitacao.exercicio

           INNER JOIN licitacao.contrato
                   ON contrato.num_contrato = contrato_licitacao.num_contrato
                  AND contrato.cod_entidade = contrato_licitacao.cod_entidade
                  AND contrato.exercicio    = contrato_licitacao.exercicio

           INNER JOIN compras.mapa
                   ON mapa.exercicio = licitacao.exercicio_mapa
                  AND mapa.cod_mapa  = licitacao.cod_mapa

           INNER JOIN compras.mapa_solicitacao
                   ON mapa_solicitacao.exercicio = mapa.exercicio
                  AND mapa_solicitacao.cod_mapa  = mapa.cod_mapa

           INNER JOIN compras.mapa_item
                   ON mapa_item.exercicio             = mapa_solicitacao.exercicio
                  AND mapa_item.cod_entidade          = mapa_solicitacao.cod_entidade
                  AND mapa_item.cod_solicitacao       = mapa_solicitacao.cod_solicitacao
                  AND mapa_item.cod_mapa              = mapa_solicitacao.cod_mapa
                  AND mapa_item.exercicio_solicitacao = mapa_solicitacao.exercicio_solicitacao

           INNER JOIN compras.solicitacao_item
                   ON solicitacao_item.exercicio       = mapa_item.exercicio_solicitacao
                  AND solicitacao_item.cod_entidade    = mapa_item.cod_entidade
                  AND solicitacao_item.cod_solicitacao = mapa_item.cod_solicitacao
                  AND solicitacao_item.cod_centro      = mapa_item.cod_centro
                  AND solicitacao_item.cod_item        = mapa_item.cod_item

           INNER JOIN compras.solicitacao_item_dotacao
                   ON solicitacao_item_dotacao.exercicio       = solicitacao_item.exercicio
                  AND solicitacao_item_dotacao.cod_entidade    = solicitacao_item.cod_entidade
                  AND solicitacao_item_dotacao.cod_solicitacao = solicitacao_item.cod_solicitacao
                  AND solicitacao_item_dotacao.cod_centro      = solicitacao_item.cod_centro
                  AND solicitacao_item_dotacao.cod_item        = solicitacao_item.cod_item

           INNER JOIN orcamento.despesa
                   ON despesa.exercicio   = solicitacao_item_dotacao.exercicio
                  AND despesa.cod_despesa = solicitacao_item_dotacao.cod_despesa

           INNER JOIN orcamento.conta_despesa
                   ON conta_despesa.exercicio = solicitacao_item_dotacao.exercicio
                  AND conta_despesa.cod_conta = solicitacao_item_dotacao.cod_conta

                WHERE contrato.exercicio = '".$this->getDado('exercicio')."'
                  AND contrato.dt_assinatura BETWEEN TO_DATE('".$this->getDado('dt_inicial')."','DD/MM/YYYY')
                                                 AND TO_DATE('".$this->getDado('dt_final')."','DD/MM/YYYY')
                  AND contrato.cod_entidade IN (".$this->getDado('entidades').") ";
    return $stSql;
}

}

?>