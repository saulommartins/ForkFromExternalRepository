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
* Classe de negócio OrganogramaNivel
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.05.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaNivel.class.php"         );

class ROrganogramaNivel
{
/**
    * @access Private
    * @var Integer
*/
var $inCodNivel;
/**
    * @access Private
    * @var Integer
*/
var $inNumNivel;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $stMascaraCodigo;
/**
    * @access Private
    * @var Object
*/
var $obTNivel;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodNivel($valor) { $this->inCodNivel            = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setNumNivel($valor) { $this->inNumNivel            = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao           = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setMascaraCodigo($valor) { $this->stMascaraCodigo       = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTNivel($valor) { $this->obTNivel   = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodNivel() { return $this->inCodNivel            ; }
/**
    * @access Public
    * @return Integer
*/
function getNumNivel() { return $this->inNumNivel            ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao           ; }
/**
    * @access Public
    * @return String
*/
function getMascaraCodigo() { return $this->stMascaraCodigo       ; }
/**
    * @access Public
    * @return Object
*/
function getTNivel() { return $this->obTNivel   ; }

/**
     * Método construtor
     * @access Private
*/
function ROrganogramaNivel()
{
    $this->setTNivel        ( new TOrganogramaNivel );
    $this->obTransacao      = new Transacao;
}

/**
    * Salva dados de Nivel no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
/*
function salvar($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $this->obTNorma->setDado("dt_publicacao" , $this->getDataPublicacao() );
        $this->obTNorma->setDado("nom_norma"     , $this->getNomeNorma() );
        $this->obTNorma->setDado("descricao"     , $this->getDescricaoNorma() );
        $this->obTNorma->setDado("url"           , $this->getUrl() );
        $this->obTNorma->setDado("localizacao"   , $this->getLocalizacao() );
        $this->obTNorma->setDado("cod_tipo_norma", $this->obRTipoNorma->getCodTipoNorma() );
        if ( $this->getCodNorma() ) {
            $this->obTNorma->setDado("cod_norma", $this->getCodNorma() );
            $obErro = $this->obTNorma->alteracao( $boTransacao );
        } else {
            $this->obTNorma->proximoCod( $inCodNorma , $boTransacao );
            $this->setCodNorma( $inCodNorma );
            $this->obTNorma->setDado("cod_norma", $this->getCodNorma() );
            $obErro = $this->obTNorma->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            //O Restante dos valores vem setado da página de processamento
            $this->obRTipoNorma->obRCadastroDinamico->setChavePersistenteValores( array( "cod_tipo_norma" => $this->obRTipoNorma->getCodTipoNorma() ) );
            $obErro = $this->obRTipoNorma->obRCadastroDinamico->salvarValores( $boTransacao );
        }

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

    return $obErro;
}
*/
/**
    * Exclui dados de Nivel do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stCompChave = $this->obTNivel->getComplementoChave();
        $this->obTNivel->setComplementoChave('');
        $this->obTNivel->setDado("cod_nivel", $this->getCodNivel() );
        $obErro = $this->obTNivel->exclusao( $boTransacao );
        $this->obTNivel->setComplementoChave( $stCompChave );

        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    }

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
function listar(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    if( $this->inCodNivel )
        $stFiltro .= " cod_nivel = " . $this->inCodNivel . " AND ";
    if( $this->stDescricao )
        $stFiltro .= " descricao = " . $this->stDescricao . " AND ";
    if( $this->inMascaraCodigo )
        $stFiltro .= " mascara_codigo = " . $this->inMascaraCodigo . " AND ";

    $stOrder = ($stOrder)?$stOrder:" ORDER BY cod_nivel ";
    $obErro = $this->obTNivel->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
    $this->obTNivel->setDado( "cod_nivel" , $this->inCodNivel );
    $obErro = $this->obTNivel->recuperaPorChave( $rsRecordSet, $boTransacao );

    return $obErro;
}

}
