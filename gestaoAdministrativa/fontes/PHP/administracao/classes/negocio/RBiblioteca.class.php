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
* Classe de negócio biblioteca
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
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoBiblioteca.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RAtributoFuncao.class.php" );

class RBiblioteca
{
var $inCodigoBiblioteca;
var $stNomeBiblioteca;

var $roRModulo;

var $obTAdministracaoBibilioteca;
var $obTAdministracaoFuncao;

var $arRAtributoFuncao;
var $roRAtributoFuncao;

function setCodigoBiblioteca($valor) { $this->inCodigoBiblioteca = $valor; }
function setNomeBiblioteca($valor) { $this->stNomeBiblioteca   = $valor; }
function setAtributoFuncao($valor) { $this->arRAtributoFuncao  = $valor; }

function getCodigoBiblioteca() { return $this->inCodigoBiblioteca; }
function getNomeBiblioteca() { return $this->stNomeBiblioteca;   }
function getAtributoFuncao() { return $this->arRAtributoFuncao;  }

function RBiblioteca(&$obRModulo)
{
    $this->setAtributoFuncao( array() );
    $this->roRModulo = &$obRModulo;
    $this->obTAdministracaoBibilioteca = new TAdministracaoBiblioteca;
    $this->obTAdministracaoFuncao = new TAdministracaoFuncao;
}

function incluirBiblioteca($boTransacao = "")
{
    return $obErro;
}

function alterarBiblioteca($boTransacao = "")
{
    return $obErro;
}

function excluirBiblioteca($boTransacao = "")
{
    return $obErro;
}

function addFuncao()
{
    $this->arRAtributoFuncao[] = new RAtributoFuncao( $this );
    $this->roRAtributoFuncao = &$this->arRAtributoFuncao[count($this->arRAtributoFuncao) - 1 ];
}

function salvarFuncoes($boTransacao = "")
{
    $obErro = new Erro;
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarAtributosComFuncao( $rsAtributos, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arAtributos = array();
            while ( !$rsAtributos->eof() ) {
                $stNumFuncoes .= $rsAtributos->getCampo('nom_funcao').",";
                $arAtributos[$rsAtributos->getCampo('cod_atributo')] = $rsAtributos->getCampo('cod_funcao');
                $rsAtributos->proximo();
            }
            $stNumFuncoes = substr($stNumFuncoes, 0,-1);            
            Sessao::write('stNomeFuncoes',$stNumFuncoes);
            foreach ($this->arRAtributoFuncao as $obRAtributoFuncao) {
                if ($arAtributos[$obRAtributoFuncao->roRAtributoDinamico->getCodAtributo()]) {
                    $arAtributos[$obRAtributoFuncao->roRAtributoDinamico->getCodAtributo()] = false;
                } else {
                    $obErro = $obRAtributoFuncao->salvarFuncao( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($arAtributos as $inCodAtributo => $inCodFuncao) {
                    if ($inCodFuncao) {
                        $this->addFuncao();
                        $this->roRAtributoFuncao->roRAtributoDinamico->setCodAtributo( $inCodAtributo );
                        $this->roRAtributoFuncao->roRAtributoDinamico->setCodCadastro( $this->roRModulo->roRCadastro->getCodCadastro() );
                        $this->roRAtributoFuncao->roRAtributoDinamico->obRModulo->setCodModulo( $this->roRModulo->getCodModulo() );
                        $obErro = $this->roRAtributoFuncao->roRAtributoDinamico->consultar( $rsAtributo, $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->roRAtributoFuncao->setCodFuncao( $inCodFuncao );
                            $obErro = $this->roRAtributoFuncao->excluirFuncao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        } else {
                            break;
                        }
                    }
                }
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTAdministracaoBibilioteca  );

    return $obErro;
}

function listarFuncoes(&$rsFuncao, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoBiblioteca() ) {
        $stFiltro .= " cod_biblioteca = ".$this->getCodigoBiblioteca()." AND ";
    }
    if ( $this->roRModulo->getCodModulo() ) {
        $stFiltro .= " cod_modulo = ".$this->roRModulo->getCodModulo()." AND ";
    }
    if ( is_object( $this->roRModulo->roRCadastro ) ) {
        if ( $this->roRModulo->roRCadastro->getCodCadastro() ) {
            $stFiltro .= " cod_cadastro = ".$this->roRModulo->roRCadastro->getCodCadastro()." AND ";
        }
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $this->obTAdministracaoFuncao->recuperaTodos( $rsFuncao, $stFiltro, "", $boTransacao );

    return $obErro;
}

function listarAtributosSemFuncao(&$rsAtributos, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRModulo->getCodModulo() ) {
        $stFiltro .= " AND ad.cod_modulo = ".$this->roRModulo->getCodModulo();
    }
    if ( $this->roRModulo->roRCadastro->getCodCadastro() ) {
        $stFiltro .= " AND ad.cod_cadastro = ".$this->roRModulo->roRCadastro->getCodCadastro();
    }
    $stOrdem = " ORDER BY ad.nom_atributo ";
    $obErro =  $this->obTAdministracaoBibilioteca->recuperaAtributosSemFuncao( $rsAtributos, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarAtributosComFuncao(&$rsAtributos, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRModulo->getCodModulo() ) {
        $stFiltro .= " AND ad.cod_modulo = ".$this->roRModulo->getCodModulo();
    }
    if ( $this->roRModulo->roRCadastro->getCodCadastro() ) {
        $stFiltro .= " AND ad.cod_cadastro = ".$this->roRModulo->roRCadastro->getCodCadastro();
    }
    $stOrdem = " ORDER BY ad.nom_atributo ";
    $obErro =  $this->obTAdministracaoBibilioteca->recuperaAtributosComFuncao( $rsAtributos, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarBibliotecasPorResponsavel(&$rsBibliotecas, $boTransacao = "")
{
    if ( $this->roRModulo->getCodResponsavel() == "" ) {

        $this->roRModulo->setCodResponsavel( Sessao::read('numCgm') );
    }
    $stFiltro = " AND m.cod_responsavel = ".$this->roRModulo->getCodResponsavel();
    $stOrdem = " ORDER BY m.cod_modulo, b.nom_biblioteca ";
    $obErro =  $this->obTAdministracaoBibilioteca->recuperaBibliotecasPorResponsavel( $rsBibliotecas, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function consultarBiblioteca($boTransacao = "")
{
    $boErro = $this->roRModulo->consultar( $rsModulo, $boTransacao );
    if ( !$boErro->ocorreu() ) {
        $this->obTAdministracaoBibilioteca->setDado( 'cod_modulo', $this->roRModulo->getCodModulo() );
        $this->obTAdministracaoBibilioteca->setDado( 'cod_biblioteca', $this->getCodigoBiblioteca() );
        $obErro = $this->obTAdministracaoBibilioteca->recuperaPorChave( $rsBiblioteca, $boTransacao );
        if ( !$boErro->ocorreu() ) {
            $this->setNomeBiblioteca( $rsBiblioteca->getCampo( 'nom_biblioteca' ) );
        }
    }

    return $obErro;
}

function listarBibliotecas(&$rsBiblioteca, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro = $this->obTAdministracaoBibilioteca->recuperaTodos( $rsBiblioteca, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarBibliotecasPorModulo(&$rsBiblioteca, $boTransacao = "")
{
    $stFiltro  = " WHERE \n ";
    $stFiltro .= "    cod_modulo = ".$this->roRModulo->getCodModulo();
    $stOrdem   = " ORDER BY nom_biblioteca ";
    $obErro = $this->listarBibliotecas( $rsBiblioteca, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}
?>
