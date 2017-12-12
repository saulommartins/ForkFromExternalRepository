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
* Classe de negócio TipoNorma
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 18742 $
$Name$
$Author: cassiano $
$Date: 2006-12-13 09:43:08 -0200 (Qua, 13 Dez 2006) $

Casos de uso: uc-01.04.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TTipoNorma.class.php"       );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TAtributoTipoNorma.class.php"       );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"              );
include_once ( CAM_GA_NORMAS_MAPEAMENTO."TAtributoNormaValor.class.php"       );

class RTipoNorma
{
/**
    * @access Private
    * @var Integer
*/
var $inCodTipoNorma;
/**
    * @access Private
    * @var String
*/
var $stNomeTipoNorma;
/**
    * @access Private
    * @var Object
*/
var $obTTipoNorma;
/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodTipoNorma($valor) { $this->inCodTipoNorma        = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setNomeTipoNorma($valor) { $this->stNomeTipoNorma       = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao           = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTTipoNorma($valor) { $this->obTTipoNorma          = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRCadastroDinamico($valor) { $this->obRCadastroDinamico   = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodTipoNorma() { return $this->inCodTipoNorma;        }
/**
    * @access Public
    * @return String
*/
function getNomeTipoNorma() { return $this->stNomeTipoNorma;       }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;           }
/**
    * @access Public
    * @return Object
*/
function getTTipoNorma() { return $this->obTTipoNorma;          }
/**
    * @access Public
    * @return Object
*/
function getRCadastroDinamico() { return $this->obRCadastroDinamico;   }

/**
     * Método construtor
     * @access Private
*/
function RTipoNorma()
{
    $this->setTTipoNorma         ( new TTipoNorma                   );
    $this->setTransacao          ( new Transacao                    );

    $this->setRCadastroDinamico  ( new RCadastroDinamico            );
    $this->obRCadastroDinamico->setPersistenteAtributos ( new TAtributoTipoNorma );
    $this->obRCadastroDinamico->setCodCadastro( 1 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 15 );
}

/**
    * Salva dados do Tipo de Norma no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTTipoNorma->setDado("nom_tipo_norma", $this->getNomeTipoNorma() );
        $this->obTTipoNorma->setDado("cod_cadastro", $this->obRCadastroDinamico->getCodCadastro() );
        $this->obTTipoNorma->setDado("cod_modulo", $this->obRCadastroDinamico->obRModulo->getCodModulo() );
        if ( $this->getCodTipoNorma() ) {
            $this->obTTipoNorma->setDado("cod_tipo_norma", $this->getCodTipoNorma() );
            $obErro = $this->obTTipoNorma->alteracao( $boTransacao );
        } else {
            $this->obTTipoNorma->proximoCod( $inCodTipoNorma , $boTransacao );
            $this->setCodTipoNorma( $inCodTipoNorma );
            $this->obTTipoNorma->setDado("cod_tipo_norma", $this->getCodTipoNorma() );
            $obErro = $this->obTTipoNorma->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da página de processamento
            $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo_norma" => $this->getCodTipoNorma() ) );
            $obErro = $this->obRCadastroDinamico->salvar( $boTransacao );
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTTipoNorma );

    return $obErro;
}

/**
    * Exclui dados do Tipo de norma do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo_norma" => $this->getCodTipoNorma() ) );
        $obErro = $this->obRCadastroDinamico->excluir( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTTipoNorma->setDado("cod_tipo_norma", $this->getCodTipoNorma() );
            $obErro = $this->obTTipoNorma->exclusao( $boTransacao );

        }
        if ( $obErro->ocorreu() ) {
            if ( strpos($obErro->getDescricao(), 'fk_') ) {
                $obErro->setDescricao('O tipo de norma '.$this->getNomeTipoNorma().' está sendo utilizado.');
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTTipoNorma );

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stOrder = " nom_tipo_norma ", $boTransacao = "")
{
    $stFiltro = "";

    if ($this->getNomeTipoNorma())
        $stFiltro .= " AND nom_tipo_norma like '%".$this->getNomeTipoNorma()."%' ";

    if ($this->getCodTipoNorma())
        $stFiltro .= " AND cod_tipo_norma = ".$this->getCodTipoNorma();

    if (!empty($stFiltro))
        $stFiltro = " WHERE cod_tipo_norma IS NOT NULL ".$stFiltro;

    $obErro = $this->obTTipoNorma->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarTodos(&$rsRecordSet, $stOrder = " nom_tipo_norma ", $boTransacao = "")
{
    $stFiltro = "";
    if( $this->getNomeTipoNorma() )
        $stFiltro .= " AND nom_tipo_norma like '%".$this->getNomeTipoNorma()."%' ";
    if( $this->getCodTipoNorma() )
        $stFiltro .= " AND cod_tipo_norma = ".$this->getCodTipoNorma();
    if($stFiltro)
        $stFiltro = " WHERE cod_tipo_norma IS NOT NULL ".$stFiltro;

    $obErro = $this->obTTipoNorma->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsRecordSet, $boTransacao = "")
{
    $this->obTTipoNorma->setDado("cod_tipo_norma", $this->getCodTipoNorma() );

    $obErro = $this->obTTipoNorma->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->setNomeTipoNorma( $rsRecordSet->getCampo("nom_tipo_norma") );
    }

    return $obErro;
}

}
