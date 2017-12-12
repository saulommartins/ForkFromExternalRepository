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
    * Classe de mapeamento da funcao tcers.exportacaoFolhaPagamento
    * Data de Criação: 06/03/2009

    * @author Desenvolvedor: André Machado

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FExportacaoFolhaPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FExportacaoFolhaPagamento()
{
    parent::Persistente();
    $this->setTabela('tcers.exportacaoFolhaPagamento');
    $this->addCampo('stEntidade', 'integer', false, '', false, false);
    $this->addCampo('dt_inicial', 'varchar', false, '', false, false);
    $this->addCampo('dt_final'  , 'varchar', false, '', false, false);
}

function montaRecuperaDadosExportacao()
{
    $stSql  = " SELECT * ";
    $stSql .= "  FROM tcers.exportacaoFolhaPagamento('".$this->getDado('stEntidade')."', ";
    $stSql .= "                                      '".$this->getDado('dt_inicial')."', ";
    $stSql .= "                                      '".$this->getDado('dt_final')."') ";

    return $stSql;
}

function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosExportacao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}
