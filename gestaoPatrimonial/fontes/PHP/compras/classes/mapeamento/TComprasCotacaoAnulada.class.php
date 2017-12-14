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
    * Classe de mapeamento da tabela compras.cotacao_anulada
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.04.04

    $Id: TComprasCotacaoAnulada.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.cotacao_anulada
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasCotacaoAnulada extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasCotacaoAnulada()
{
    parent::Persistente();
    $this->setTabela("compras.cotacao_anulada");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_cotacao');

    $this->AddCampo('exercicio','char',true,'4',true,'TComprasCotacao');
    $this->AddCampo('cod_cotacao','integer',true,'',true,'TComprasCotacao');
    $this->AddCampo('motivo','varchar',true,'200',false,false);

}

function recuperaFornecedoresCotacaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFornecedoresCotacaoAnulada().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFornecedoresCotacaoAnulada()
{
    $stSql  = "SELECT --cotacao_item.*
         --cotacao_fornecedor_item.*
          sw_cgm.nom_cgm,
          sw_cgm.numcgm
         --, mapa_cotacao.cod_mapa
         --, mapa_cotacao.exercicio_mapa
     --, cotacao_fornecedor_item.cgm_fornecedor
      FROM compras.cotacao
INNER JOIN compras.mapa_cotacao
        ON cotacao.cod_cotacao = mapa_cotacao.cod_cotacao
       AND cotacao.exercicio = mapa_cotacao.exercicio_cotacao
LEFT JOIN compras.cotacao_item
        ON cotacao.cod_cotacao = cotacao_item.cod_cotacao
       AND cotacao.exercicio   = cotacao_item.exercicio
LEFT JOIN compras.cotacao_fornecedor_item
        ON cotacao_item.cod_cotacao = cotacao_fornecedor_item.cod_cotacao
       AND cotacao_item.exercicio   = cotacao_fornecedor_item.exercicio
       AND cotacao_item.cod_item    = cotacao_fornecedor_item.cod_item
       AND cotacao_item.lote        = cotacao_fornecedor_item.lote
left JOIN sw_cgm
        ON cotacao_fornecedor_item.cgm_fornecedor = sw_cgm.numcgm
        WHERE 1=1";

    return $stSql;
}

function recuperaUltimaCotacaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaUltimaCotacaoAnulada().$stFiltro.$stOrdem;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaUltimaCotacaoAnulada()
{
    $stSql  = 'SELECT cotacao_anulada.cod_cotacao FROM compras.cotacao_anulada
            WHERE 1=1';

    return $stSql;
}

function recuperaCgmParticipanteAnulado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCgmParticipanteAnulado().$stFiltro.$stOrdem;
    //$this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function montaRecuperaCgmParticipanteAnulado()
{
    $stSql  = 'SELECT sw_cgm.numcgm FROM sw_cgm
            WHERE 1=1 ';

    return $stSql;
}
}
