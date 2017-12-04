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
    * Classe de mapeamento da tabela compras.solicitacao_item_dotacao_anulacao
    * Data de Criação: 26/11/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela   compras.solicitacao_item_dotacao_anulacao
  * Data de Criação: 30/06/2006

  * Data de Criação: 26/11/2008

    * @author Analista: Gelson Wolowski Goncalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage Mapeamento
*/

class TComprasSolicitacaoItemDotacaoAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasSolicitacaoItemDotacaoAnulacao()
{
    parent::Persistente();
    $this->setTabela("compras.solicitacao_item_dotacao_anulacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio, cod_entidade, cod_solicitacao, timestamp, cod_centro, cod_item, cod_conta, cod_despesa');

    $this->AddCampo('exercicio'      ,'char'   ,true,'4'   ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('cod_entidade'   ,'integer',true,''    ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('cod_solicitacao','integer',true,''    ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('timestamp'      ,'timestamp',true,''  ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('cod_centro'     ,'integer',true,''    ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('cod_item'       ,'integer',true,''    ,true,true  ,'TComprasSolicitacaoItemAnulacao');
    $this->AddCampo('cod_conta'      ,'integer',true,''    ,true,true  ,'TComprasSolicitacaoItemDotacao');
    $this->AddCampo('cod_despesa'    ,'integer',true,''    ,true,true  ,'TComprasSolicitacaoItemDotacao');
    $this->AddCampo('quantidade'     ,'numeric',true,'14,2',false,false);
    $this->AddCampo('vl_anulacao'    ,'numeric',true,'14,2',false,false);

}

function recuperaTotalAnulado(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTotalAnulado();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaTotalAnulado()
{
    $stSql = " SELECT coalesce(sum(quantidade)  , 0.00  ) as quantidade
                    , coalesce(sum(vl_anulacao) , 0.00  ) as vl_anulacao
                 FROM compras.solicitacao_item_dotacao_anulacao
                WHERE exercicio       = '".$this->getDado('exercicio')."'
                  AND cod_entidade    =  ".$this->getDado('cod_entidade')."
                  AND cod_solicitacao =  ".$this->getDado('cod_solicitacao')."
                  AND cod_centro      =  ".$this->getDado('cod_centro')."
                  AND cod_item        =  ".$this->getDado('cod_item')."
                  AND cod_conta       =  ".$this->getDado('cod_conta')."
                  AND cod_despesa     =  ".$this->getDado('cod_despesa')."   ";

    return $stSql;
}

}
