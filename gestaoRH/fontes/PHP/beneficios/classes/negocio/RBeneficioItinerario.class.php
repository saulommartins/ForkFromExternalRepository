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
* Classe de regra de negócio da tabela BENEFICIO.ITINERARIO
* Data de Criação: 11/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage regra de negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php"                            );
include_once ( CAM_GRH_BEN_MAPEAMENTO."TBeneficioItinerario.class.php"           );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php"                     );

// include_once    ( CAM_GRH_BEN_NEGOCIO."TMunicipio.class.php"                     );

class RBeneficioItinerario
{
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTBeneficioItinerario;
/**
    * @access Private
    * @var Object
*/
var $obTUF;
/**
    * @access Private
    * @var Object
*/
var $obTMunicipio;
/**
    * @access Private
    * @var Integer
*/
var $inCodLinhaDestino;
/**
    * @access Private
    * @var Integer
*/
var $inCodLinhaOrigem;
/**
    * @access Private
    * @var Integer
*/
var $inCodMunicipioOrigem;
/**
    * @access Private
    * @var Integer
*/
var $inCodMunicipioDestino;
/**
    * @access Private
    * @var Integer
*/
var $inCodUFOrigem;
/**
    * @access Private
    * @var Integer
*/
var $inCodUFDestino;
/**
    * @access Private
    * @var Integer
*/
var $inCodItinerario;
/**
    * @access Private
    * @var Object
*/
var $roBeneficioValeTransporte;

/**
    * @access Public
    * @param Object $Valor
*/
function setTBeneficioItinerario($valor) { $this->obTBeneficioItinerario   = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTUF($valor) { $this->obTUF                    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTMunicipio($valor) { $this->obTMunicipio            = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLinhaDestino($valor) { $this->inCodLinhaDestino        = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodLinhaOrigem($valor) { $this->inCodLinhaOrigem         = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodMunicipioDestino($valor) { $this->inCodMunicipioDestino    = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodMunicipioOrigem($valor) { $this->inCodMunicipioOrigem     = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodUFDestino($valor) { $this->inCodUFDestino           = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodUFOrigem($valor) { $this->inCodUFOrigem            = $valor  ; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodItinerario($valor) { $this->inCodItinerario          = $valor  ; }

/**
    * @access Public
    * @return Object
*/
function getTBeneficioItinerario() { return $this->obTBeneficioItinerario    ; }
/**
    * @access Public
    * @return Object
*/
function getTUF() { return $this->obTUF                     ; }
/**
    * @access Public
    * @return Object
*/
function getTMunicipio() { return $this->obTMunicipio              ; }
/**
    * @access Public
    * @return Integer
*/
function getCodLinhaDestino() { return $this->inCodLinhaDestino         ; }
/**
    * @access Public
    * @return Integer
*/
function getCodLinhaOrigem() { return $this->inCodLinhaOrigem          ; }
/**
    * @access Public
    * @return Integer
*/
function getCodMunicipioDestino() { return $this->inCodMunicipioDestino     ; }
/**
    * @access Public
    * @return Integer
*/
function getCodMunicipioOrigem() { return $this->inCodMunicipioOrigem      ; }
/**
    * @access Public
    * @return Integer
*/
function getCodUFDestino() { return $this->inCodUFDestino            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodUFOrigem() { return $this->inCodUFOrigem             ; }
/**
    * @access Public
    * @return Integer
*/
function getCodItinerario() { return $this->inCodItinerario           ; }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioItinerario(&$roBeneficioValeTransporte)
{
    $this->setTBeneficioItinerario   ( new TBeneficioItinerario              );
    $this->setTUF                    ( new TUF                               );
    $this->setTMunicipio             ( new TMunicipio                        );
    $this->roBeneficioValeTransporte = &$roBeneficioValeTransporte;
    $this->obTransacao               = new Transacao;
}

/**
    * Inclui dados do itinerario no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirItinerario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro =  $this->obTBeneficioItinerario->proximoCod( $inCodItinerario , $boTransacao );
        $this->setCodItinerario( $inCodItinerario );
        if ( !$obErro->ocorreu() ) {
             $this->obTBeneficioItinerario->setDado("vale_transporte_cod_vale_transporte"   , $this->getCodItinerario() );
             $this->obTBeneficioItinerario->setDado("cod_linha_destino"                     , $this->getCodLinhaDestino() );
             $this->obTBeneficioItinerario->setDado("cod_linha_origem"                      , $this->getCodLinhaOrigem() );
             $this->obTBeneficioItinerario->setDado("municipio_destino"                     , $this->getCodMunicipioDestino() );
             $this->obTBeneficioItinerario->setDado("municipio_origem"                      , $this->getCodMunicipioOrigem() );
             $this->obTBeneficioItinerario->setDado("uf_destino"                            , $this->getCodUFDestino() );
             $this->obTBeneficioItinerario->setDado("uf_origem"                             , $this->getCodUFOrigem() );
             $obErro = $this->obTBeneficioItinerario->inclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Exclui dados do itinerario no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirItinerario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioItinerario->setDado("vale_transporte_cod_vale_transporte"   , $this->getCodItinerario() );
        $obErro = $this->obTBeneficioItinerario->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioLinha );

    return $obErro;
}

/**
    * Executa um listar UF na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarUF(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTUF->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um listar Municipios na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarMunicipio(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    $obErro = $this->obTMunicipio->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista os Itinerários
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarItinerario(&$rsRecordSet , $boTransacao = "")
{
    if ($inNumCGM = $this->roBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->getNumCGM())
        $stFiltro = " AND Bvt.fornecedor_vale_transporte_fornecedor_numcgm IN (".$inNumCGM.") \n";
    $stOrder = " ORDER BY Sm1.nom_municipio ";
    $obErro = $this->obTBeneficioItinerario->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
