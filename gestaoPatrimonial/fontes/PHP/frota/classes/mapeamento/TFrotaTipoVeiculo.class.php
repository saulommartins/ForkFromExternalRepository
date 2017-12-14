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
  * Mapeamento da tabela frota.veiculo
  * Data de criação : 15/03/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Id: TFrotaTipoVeiculo.class.php 61597 2015-02-11 18:46:51Z jean $

    Caso de uso: uc-03.02.10
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TFrotaTipoVeiculo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function TFrotaTipoVeiculo()
{
    parent::Persistente();
    $this->setTabela('frota.tipo_veiculo');
    $this->setCampoCod('cod_tipo');
    $this->setComplementoChave('');
    $this->AddCampo('cod_tipo','integer',true,'',true,false);
    $this->AddCampo('nom_tipo','varchar',true,'"30"',false,false);
    $this->AddCampo('placa','boolean',true,'true',false,false);
    $this->AddCampo('prefixo','boolean',true,'true',false,false);
    $this->AddCampo('controlar_horas_trabalhadas','boolean',true,'false',false,false);
}
function recuperaTipoVeiculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaTipoVeiculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoVeiculo()
{
    $stSql  = "select                       \n";
    $stSql .= "    *                        \n";
    $stSql .= "from                         \n";
    $stSql .= "    frota.tipo_veiculo       \n";
    if ($this->getDado('inCodTipoVeiculo') )
        $stSql .= "    where cod_tipo = ".$this->getDado('inCodTipoVeiculo')." \n";
//    $stSql .= " order by                    \n";
    return $stSql;

}

function recuperaVinculoTipoVeiculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaVinculoTipoVeiculo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVinculoTipoVeiculo()
{
    $stSql  = "select tipo_veiculo.cod_tipo                                     \n";
    $stSql .= "     , tipo_veiculo.nom_tipo                                     \n";
    $stSql .= "     , tipo_veiculo.placa                                        \n";
    $stSql .= "     , tipo_veiculo.prefixo                                      \n";
    $stSql .= "     , tipo_veiculo_vinculo.cod_tipo_tcm                         \n";
    $stSql .= "     , tipo_veiculo_vinculo.cod_subtipo_tcm                      \n";
    $stSql .= "from                                                             \n";
    $stSql .= "    frota.tipo_veiculo                                           \n";
    $stSql .= " LEFT JOIN tcmgo.tipo_veiculo_vinculo                            \n";
    $stSql .= "        ON tipo_veiculo_vinculo.cod_tipo = tipo_veiculo.cod_tipo \n";

    if ($this->getDado('inCodTipoVeiculo') ) {
        $stSql .= "    where tipo_veiculo.cod_tipo = ".$this->getDado('inCodTipoVeiculo')." \n";
    }

    return $stSql;

}

function recuperaVinculoTipoVeiculoTCE(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaVinculoTipoVeiculoTCE().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVinculoTipoVeiculoTCE()
{
    $stSql  = "select tipo_veiculo.cod_tipo                                     \n";
    $stSql .= "     , tipo_veiculo.nom_tipo                                     \n";
    $stSql .= "     , tipo_veiculo.placa                                        \n";
    $stSql .= "     , tipo_veiculo.prefixo                                      \n";
    $stSql .= "     , tipo_veiculo_vinculo.cod_tipo_tce                         \n";
    $stSql .= "     , tipo_veiculo_vinculo.cod_subtipo_tce                      \n";
    $stSql .= "from                                                             \n";
    $stSql .= "    frota.tipo_veiculo                                           \n";
    $stSql .= " LEFT JOIN tcemg.tipo_veiculo_vinculo                            \n";
    $stSql .= "        ON tipo_veiculo_vinculo.cod_tipo = tipo_veiculo.cod_tipo \n";

    if ($this->getDado('inCodTipoVeiculo') ) {
        $stSql .= "    where tipo_veiculo.cod_tipo = ".$this->getDado('inCodTipoVeiculo')." \n";
    }

    return $stSql;
}

function recuperaVinculoTipoVeiculoTCERN(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaVinculoTipoVeiculoTCERN().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVinculoTipoVeiculoTCERN()
{
    $stSql  = "SELECT tipo_veiculo.cod_tipo                                     
                    , tipo_veiculo.nom_tipo                                     
                    , tipo_veiculo.placa                                        
                    , tipo_veiculo.prefixo                                      
                    , tipo_veiculo_vinculo.cod_tipo_tce                
                    , tipo_veiculo_vinculo.cod_especie_tce                             

                 FROM frota.tipo_veiculo

             LEFT JOIN tcern.tipo_veiculo_vinculo                            
                    ON tipo_veiculo_vinculo.cod_tipo = tipo_veiculo.cod_tipo
            ";

    if ($this->getDado('inCodTipoVeiculo') ) {
        $stSql .= "    where tipo_veiculo.cod_tipo = ".$this->getDado('inCodTipoVeiculo')." \n";
    }

    return $stSql;
}

}
