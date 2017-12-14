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
    * Classe de mapeamento da tabela compras.mapa_solicitacao
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-03.04.05

    $Id: TComprasMapaSolicitacao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.mapa_solicitacao
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasMapaSolicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasMapaSolicitacao()
{
    parent::Persistente();
    $this->setTabela("compras.mapa_solicitacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_mapa,exercicio_solicitacao,cod_entidade,cod_solicitacao');

    $this->AddCampo('exercicio'            ,'CHAR(4)'  ,true,true,'',true ,'TComprasMapa' );
    $this->AddCampo('cod_mapa'             ,'integer'  ,true,true,'',true ,'TComprasMapa' );
    $this->AddCampo('exercicio_solicitacao','CHAR(4)'  ,true,true,'',true ,true);
    $this->AddCampo('cod_entidade'         ,'integer'  ,true,true,'',true ,true);
    $this->AddCampo('cod_solicitacao'      ,'integer'  ,false,true,'',true ,true);
    $this->AddCampo('timestamp'            ,'timestamp',false,true,'',false,false);

}

function recuperaSolicitacaoEntidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
     $obErro      = new Erro;
     $obConexao   = new Conexao;
     $rsRecordSet = new RecordSet;
     $stSql = $this->montaRecuperaSolicitacaoEntidade().$stFiltro.$stOrdem;
     $this->stDebug = $stSql;
     $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaSolicitacaoEntidade()
{
    $stSql = "";

    $stSql .= " select distinct                                                     \n";
    $stSql .= "      entidade.cod_entidade                                          \n";
    $stSql .= "     ,entidade.numcgm                                                \n";
    $stSql .= "     ,sw_cgm.nom_cgm                                                 \n";
    $stSql .= " from                                                                \n";
    $stSql .= "      orcamento.entidade                                             \n";
    $stSql .= "     ,sw_cgm                                                         \n";
    $stSql .= "     ,compras.mapa_solicitacao                                       \n";
    $stSql .= " where                                                               \n";
    $stSql .= "         sw_cgm.numcgm         = entidade.numcgm                     \n";
    $stSql .= "     and entidade.cod_entidade = mapa_solicitacao.cod_entidade       \n";
    $stSql .= "     and entidade.exercicio    = mapa_solicitacao.exercicio          \n";
    $stSql .= "     and mapa_solicitacao.cod_mapa = ".$this->getDado('cod_mapa')."  \n";
    $stSql .= "     and mapa_solicitacao.exercicio = '".$this->getDado('exercicio')."'\n";

    return $stSql;

}

function recuperaMaiorDataSolicitacaoMapa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaiorDataSolicitacaoMapa().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaMaiorDataSolicitacaoMapa()
{
    $stSql = " SELECT TO_CHAR(max(solicitacao.timestamp),'dd/mm/yyyy') as dt_solicitacao  \n";
    $stSql.= "   FROM compras.mapa_solicitacao                                            \n";
    $stSql.= "      , compras.solicitacao                                                 \n";

    $stSql.= "  WHERE mapa_solicitacao.cod_solicitacao = solicitacao.cod_solicitacao      \n";

    if ($this->getDado('cod_mapa')) {
        $stSql.= " AND mapa_solicitacao.cod_mapa =".$this->getDado('cod_mapa');
    }

    if ($this->getDado('exercicio')) {
        $stSql.= " AND mapa_solicitacao.exercicio = '".$this->getDado('exercicio')."'";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql.= " AND solicitacao.cod_entidade =".$this->getDado('cod_entidade');
    }

    return $stSql;
}

}
