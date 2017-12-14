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
    * Titulo do arquivo : Arquivo de mapeamento da tabela tcmgo.tipo_veiculo_tcm
    * Data de Criação   : 22/12/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    $Id:$
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOTipoVeiculoTCM extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TTGOTipoVeiculoTCM()
{
    parent::Persistente();
    $this->setTabela('tcmgo.tipo_veiculo_tcm');
    $this->setCampoCod('cod_tipo_tcm');
    $this->setComplementoChave('');
    $this->AddCampo('cod_tipo_tcm', 'integer', true, ''   , true , false);
    $this->AddCampo('nom_tipo_tcm', 'varchar', true, '200', false, false);
}

function recuperaTipoVeiculoTCM(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaTipoVeiculoTCM().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoVeiculoTCM()
{
    $stSql  = "select                       \n";
    $stSql .= "    *                        \n";
    $stSql .= "from                         \n";
    $stSql .= "    tcmgo.tipo_veiculo_tcm   \n";
    if ($this->getDado('inCodTipoVeiculo') )
        $stSql .= "    where cod_tipo_tcm = ".$this->getDado('inCodTipoVeiculo')." \n";

    return $stSql;

}
}
