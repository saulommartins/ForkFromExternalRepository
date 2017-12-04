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
    * Classe de mapeamento da tabela FN_EMPENHO_EMPENHO_PAGAMENTO_ANULACAO
    * Data de Criação: 29/12/2004

    * @author Analista: Jorge Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Caso de uso uc-02.03.23,uc-02.04.05
*/

/*
$Log$
Revision 1.8  2006/07/05 20:46:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FEmpenhoEmpenhoPagamentoAnulacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FEmpenhoEmpenhoPagamentoAnulacao()
{
    parent::Persistente();
    $this->setTabela('EmpenhoAnulacaoPagamento');

    $this->AddCampo('exercicio'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('valor'                  ,'numeric',false,'14.2',false,false);
    $this->AddCampo('complemento'            ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_lote'               ,'integer',false,''    ,false,false);
    $this->AddCampo('tipo_lote'              ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_entidade'           ,'integer',false,''    ,false,false);
    $this->AddCampo('cod_nota'               ,'integer',false,''    ,false,false);
    $this->AddCampo('conta_pagamento_financ' ,'varchar',false,''    ,false,false);
    $this->AddCampo('cod_estrutural'         ,'varchar',false,''    ,false,false);
    $this->AddCampo('num_orgao'              ,'integer',false,''    ,false,false);
    $this->AddCampo('tcems'                  ,'varchar',false,''    ,false,false);
}

function montaInsereLote()
{
    $stSql  = " SELECT  \n";
    $stSql .= "      contabilidade.fn_insere_lote( ";
    $stSql .= " '".$this->getDado('exercicio')."' ";
    $stSql .= " ,".$this->getDado('cod_entidade');
    $stSql .= " ,'".$this->getDado('tipo_lote')."' ";
    $stSql .= " ,'".$this->getDado('nom_lote')."' ";
    $stSql .= " ,'".$this->getDado('dt_lote')."' ";
    $stSql .= " ) as cod_lote \n";

    return $stSql ;
}

function montaExecutaFuncao()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")               ."', \n";
    $stSql .= "                         ".$this->getDado("valor")                   ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")             ."', \n";
    $stSql .= "                         ".$this->getDado('cod_lote')                ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")               ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")            ." , \n";
    $stSql .= "                         ".$this->getDado("cod_nota")                ." , \n";
    $stSql .= "                        '".$this->getdado("conta_pagamento_financ")  ."', \n";
    $stSql .= "                        '".$this->getdado("cod_estrutural")          ."', \n";
    $stSql .= "                         ".$this->getdado("num_orgao")               ."  \n";
    if ($this->getdado("tcems")) {
        $stSql .= " ,                       ".$this->getdado("tcems")               ."  \n";
    }
    $stSql .= " )  \n";
    $stSql .= "                         as sequencia                                     \n";

    return $stSql;
}

function montaExecutaFuncaoTCEMS()
{
    $stSql  = " SELECT  \n";
    $stSql .= " ".$this->getTabela()."('".$this->getDado("exercicio")               ."', \n";
    $stSql .= "                         ".$this->getDado("valor")                   ." , \n";
    $stSql .= "                        '".$this->getDado("complemento")             ."', \n";
    $stSql .= "                         ".$this->getDado('cod_lote')                ." , \n";
    $stSql .= "                        '".$this->getDado("tipo_lote")               ."', \n";
    $stSql .= "                         ".$this->getDado("cod_entidade")            ." , \n";
    $stSql .= "                         ".$this->getDado("cod_nota")                ." , \n";
    $stSql .= "                        '', \n";
    $stSql .= "                        '', \n";
    $stSql .= "                         ".$this->getdado("num_orgao")               ." , \n";
    $stSql .= "                         ".$this->getdado("tcems")                   ." , \n";
    $stSql .= "                         ".$this->getdado("cod_plano_debito")        ." , \n";
    $stSql .= "                         ".$this->getdado("cod_plano_credito")       ." ) \n";
    $stSql .= "                         as sequencia                                     \n";

    return $stSql;
}

/**
    * Executa funcao EmpenhoEmissao no banco de dados a partir do comando SQL montado no método montaExecutaFuncao.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function executaFuncao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ( !$this->getDado("cod_lote") ) {
        $stSql = $this->montaInsereLote();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSetLote, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("cod_lote", $rsRecordSetLote->getCampo("cod_lote"));
        }
    }
    if ( !$obErro->ocorreu() ) {
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        if (Sessao::getExercicio() > '2012') {
            $stSql = $this->montaExecutaFuncaoTCEMS().$stCondicao.$stOrdem;
        } else {
            $stSql = $this->montaExecutaFuncao().$stCondicao.$stOrdem;
        }
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    }

    return $obErro;
}

}
