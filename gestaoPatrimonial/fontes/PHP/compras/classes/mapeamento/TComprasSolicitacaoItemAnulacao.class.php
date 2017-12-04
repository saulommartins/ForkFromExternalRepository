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
    * Classe de mapeamento da tabela compras.solicitacao_item_anulacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 18593 $
    $Name$
    $Author: rodrigo $
    $Date: 2006-12-07 14:51:56 -0200 (Qui, 07 Dez 2006) $

    * Casos de uso: uc-03.04.01
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.solicitacao_item_anulacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasSolicitacaoItemAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasSolicitacaoItemAnulacao()
{
    parent::Persistente();
    $this->setTabela("compras.solicitacao_item_anulacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_entidade,cod_solicitacao,timestamp,cod_centro,cod_item');

    $this->AddCampo('exercicio'      ,      'char', true,    '4',  true,  true);
    $this->AddCampo('cod_entidade'   ,   'integer', true,     '',  true,  true);
    $this->AddCampo('cod_solicitacao',   'integer', true,     '',  true,  true);
    $this->AddCampo('timestamp'      , 'timestamp', true,     '',  true,  true);
    $this->AddCampo('cod_centro'     ,   'integer', true,     '',  true,  true);
    $this->AddCampo('cod_item'       ,   'integer', true,     '',  true,  true);
    $this->AddCampo('quantidade'     ,   'numeric', true, '14,4', false, false);
    $this->AddCampo('vl_total'       ,   'numeric', true, '14,2', false, false);

}

 function recuperaSolicitacaoItemAnulacao(&$rsRecordSet, $stFiltro="" , $boTransacao = "")
 {
     $obErro      = new Erro;
     $obConexao   = new Conexao;
     $rsRecordSet = new RecordSet;

     $stSql = $this->montaRecuperaSolicitacaoItemAnulacao().$stFiltro;
     $this->setDebug( $stSql );
     $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

     return $obErro;
 }

 function montaRecuperaSolicitacaoItemAnulacao()
 {
     $stSql = " SELECT SUM(quantidade) AS quantidade     \n";
     $stSql.= "       ,SUM(vl_total)   AS vl_total       \n";
     $stSql.= "   FROM compras.solicitacao_item_anulacao \n";

     return $stSql;
 }
}
