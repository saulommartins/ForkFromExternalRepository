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
* Classe de negócio Cadastro
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RModulo.class.php"                    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );

class RCadastro
{
var $inCodCadastro;
var $stNomeCadastro;
var $stMapeamento;
var $obRModulo;
var $obTAdministracaoCadastro;

function setCodCadastro($valor) { $this->inCodCadastro        = $valor; }
function setNomeCadastro($valor) { $this->stNomeCadastro          = $valor; }
function setMapeamento($valor) { $this->stMapeamento            = $valor; }
function setRModulo($valor) { $this->obRModulo               = $valor; }
function setTAdministracaoCadastro($valor) { $this->obTAdministracaoCadastro = $valor; }

function getCodCadastro() { return $this->inCodCadastro;    }
function getNomeCadastro() { return $this->stModuloCadastro; }
function getMapeamento() { return $this->stMapeamento;     }
function getRModulo() { return $this->obRModulo;        }

function RCadastro(&$obRModulo)
{
    $this->obRModulo = &$obRModulo;
    $this->setTAdministracaoCadastro( new TAdministracaoCadastro );
}

//MÉTODO PRIVADO
function listar(&$rsCadastros,$stFiltro = "",$stOrdem = "", $boTransacao = "")
{
    return $this->obTAdministracaoCadastro->recuperaTodos( $rsCadastros, $stFiltro, $stOrdem, $boTransacao );
}

function listarCadastro(&$rsCadastros, $boTransacao = "")
{
    $stFiltro = " WHERE COD_MODULO = ".$this->obRModulo->getCodModulo();
    $stOrdem = " ORDER BY COD_CADASTRO ";

    return $this->listar( $rsCadastros, $stFiltro, $stOrdem, $boTransacao );
}

function consultarCadastro($boTransacao = "")
{
    $obErro = $this->obRModulo->consultar( $rsModulo, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro  = " WHERE COD_MODULO = ".$this->obRModulo->getCodModulo()." AND ";
        $stFiltro .= " COD_CADASTRO = ".$this->getCodCadastro();
        $stOrdem = " ORDER BY COD_CADASTRO ";
        $obErro = $this->listar( $rsCadastro, $stFiltro, $stOrdem, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $rsCadastro->eof() ) {
                $obErro->setDescricao( "Cadastro inexistente." );
            } else {
                $this->setNomeCadastro ( $rsCadastro->getCampo("nom_cadastro") );
                $this->setMapeamento   ( $rsCadastro->getCampo("mapemaneto")   );
            }
        }
    }

    return $obErro;
}

function listarAtributosSemFuncao(&$rsAtributos, $boTransacao = "")
{
    $stFiltro  = " AND ad.cod_modulo = ".$this->obRModulo->getCodModulo()." \n";
    $stFiltro .= " AND ad.cod_cadastro = ".$this->getCodCadastro();
    $obErro = $this->obTAdministracaoCadastro->recuperaAtributosSemFuncao( $rsAtributos, $stFiltro, "", $boTransacao );

    return $obErro;
}

function listarAtributosComFuncao(&$rsAtributos, $boTransacao = "")
{
    $stFiltro  = " AND ad.cod_modulo = ".$this->obRModulo->getCodModulo()." \n";
    $stFiltro .= " AND ad.cod_cadastro = ".$this->getCodCadastro();
    $obErro = $this->obTAdministracaoCadastro->recuperaAtributosComFuncao( $rsAtributos, $stFiltro, "", $boTransacao );

    return $obErro;
}

function consultarCaminhoCadastro($rsRecordSet, $boTransacao = '')
{
    $stFiltro  = " AND ad.cod_modulo = ".$this->obRModulo->getCodModulo()." \n";
    $stFiltro .= " AND ad.cod_cadastro = ".$this->getCodCadastro();
    $obErro = $this->obTAdministracaoCadastro->recuperaRelacionamento( $rsRecordSet, $stFiltro, "", $boTransacao );

    return $obErro;
}

}

?>
