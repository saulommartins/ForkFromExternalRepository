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
* Classe de negócio Configuracao
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27781 $
$Name$
$Author: luiz $
$Date: 2008-01-28 12:53:09 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.03.97
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class RAdministracaoConfiguracao
{
var $boNumeroInscricao;
var $stMascaraLote;
var $stMascaraInscricao;
var $stNomMunicipio;
var $stNomUf;
var $obTConfiguracao;
var $inCodModulo;
var $inExercicio;
var $arTConfiguracao;

//SETTERS
function setNumeroInscricao($valor) { $this->boNumeroInscricao    = $valor; }
function setMascaraLote($valor) { $this->stMascaraLote        = $valor; }
function setMascaraInscricao($valor) { $this->stMascaraInscricao   = $valor; }
function setTConfiguracao($valor) { $this->obTConfiguracao      = $valor; }
function setCodModulo($valor) { $this->inCodModulo          = $valor; }
function setExercicio($valor) { $this->inExercicio          = $valor; }
function setNomMunicipio($valor) { $this->stNomMunicipio       = $valor; }
function setNomUf($valor) { $this->stNomUf              = $valor; }
function setSiglaUf($valor) { $this->stSiglaUf            = $valor; }

//GETTERS
function getNumeroInscricao() { return $this->boNumeroInscricao;    }
function getMascaraLote() { return $this->stMascaraLote;        }
function getMascaraInscricao() { return $this->stMascaraInscricao;   }
function getTConfiguracao() { return $this->obTConfiguracao;      }
function getCodModulo() { return $this->inCodModulo;          }
function getExercicio() { return $this->inExercicio;          }
function getNomMunicipio() { return $this->stNomMunicipio;       }
function getNomUf() { return $this->stNomUf;              }
function getSiglaUf() { return $this->stSiglaUf;            }

//METODO CONSTRUTOR
function RAdministracaoConfiguracao()
{
    $this->setTConfiguracao( new TAdministracaoConfiguracao );
}

function salvaConfiguracao($boTransacao = "")
{
    $this->obTConfiguracao->setDado( "cod_modulo", $this->getCodModulo() );
    $this->obTConfiguracao->setDado( "exercicio", $this->getExercicio() );
    $boFlagTransacao = false;
    if ( empty( $boTransacao ) ) {
        $obTransacao = new Transacao;
        $obTransacao->begin();
        $boTransacao = $obTransacao->getTransacao();
        $boFlagTransacao = true;
    }
    if ( $this->getNumeroInscricao() ) {
        $this->obTConfiguracao->setDado("parametro", "numero_inscricao" );
        $this->obTConfiguracao->setDado( "valor", $this->getNumeroInscricao() );
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    }
    if ( $this->getMascaraLote() and !$obErro->ocorreu() ) {
        $this->obTConfiguracao->setDado("parametro", "mascara_lote" );
        $this->obTConfiguracao->setDado( "valor", $this->getMascaraLote() );
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    }
    if ( $this->getMascaraInscricao() and !$obErro->ocorreu() ) {
        $this->obTConfiguracao->setDado("parametro", "mascara_inscricao" );
        $this->obTConfiguracao->setDado( "valor", $this->getMascaraInscricao() );
        $obErro = $this->obTConfiguracao->alteracao( $boTransacao );
    }
    if ($boFlagTransacao) {
        if ( !$obErro->ocorreu() ) {
            $obErro = $obTransacao->commitAndClose();
        } else {
            $obTransacao->rollbackAndClose();
        }
    }

    return $obErro;
}

function consultarConfiguracao($boTransacao = "")
{
    $arParametro = array( "numero_inscricao", "mascara_inscricao", "mascara_lote" );
    foreach ($arParametro as $stParametro) {
        $stFiltro = " WHERE COD_MODULO = ".$this->getCodModulo()." AND parametro = '".$stParametro."' ";
        $stOrder = " ORDER BY parametro ";
        $obErro = $this->obTConfiguracao->recuperaTodos( $rsConfiguracao, $stFiltro, $stOrder, $boTransacao );
        if ( $obErro->ocorreu() ) {
            break;
        }
        $arParametroConfiguracao[$stParametro] = $rsConfiguracao->getCampo("valor");
    }
    if ( !$obErro->ocorreu() ) {
        $this->setNumeroInscricao( $arParametroConfiguracao["numero_inscricao"] );
        $this->setMascaraInscricao( $arParametroConfiguracao["mascara_inscricao"] );
        $this->setMascaraLote( $arParametroConfiguracao["mascara_lote"] );
    }

    return $obErro;
}
/**
    * Método para consultar Nome e código do Municipio e da UF, de acordo com urbem_configuração
    * @access public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarMunicipio($boTransacao = "")
{
    if ($this->getExercicio()) {
            $this->obTConfiguracao->setDado('exercicio', $this->getExercicio() );
    }
    $obErro = $this->obTConfiguracao->recuperaMunicipio( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomMunicipio = $rsRecordSet->getCampo('nom_municipio');
        $this->stNomUf        = $rsRecordSet->getCampo('nom_uf');
        $this->stSiglaUf      = $rsRecordSet->getCampo('sigla_uf');
    }

    return $obErro;
}

function addConfiguracao($stParametro = '', $stValor = '')
{
    $this->arTConfiguracao[] = new TAdministracaoConfiguracao;

    $nomePrefeitura = str_replace("\\", "", $stValor);

    $this->arTConfiguracao[count($this->arTConfiguracao) - 1]->setDado( 'cod_modulo', $this->getCodModulo() );
    $this->arTConfiguracao[count($this->arTConfiguracao) - 1]->setDado( 'exercicio',  $this->getExercicio() );
    $this->arTConfiguracao[count($this->arTConfiguracao) - 1]->setDado( 'parametro', $stParametro );
    $this->arTConfiguracao[count($this->arTConfiguracao) - 1]->setDado( 'valor' , $nomePrefeitura );
}

function alterarConfiguracao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obTransacao = new Transacao;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
       foreach ($this->arTConfiguracao as $arTConfiguracao) {
           $arTConfiguracao->recuperaPorChave ( $rsConfig );
           if ( $rsConfig->getNumLinhas() <= 0 ) {
               $obErro = $arTConfiguracao->inclusao ( $boTransacao );
           } else {
               $obErro = $arTConfiguracao->alteracao( $boTransacao );
           }
           if ( $obErro->ocorreu() ) {
               $stErro = "Ocorreu o seguinte erro alterando o parametro ".$arTConfiguracao->getDado( 'parametro' ).": ";
               $stErro .= $obErro->getdescricao();
               $obErro->setDescricao( $stErro );
               break;
           }
       }
       $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $arTConfiguracao );
    }

    return $obErro;
}

}
