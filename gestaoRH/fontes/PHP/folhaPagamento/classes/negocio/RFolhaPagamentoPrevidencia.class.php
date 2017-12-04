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
    * Classe de regra de negócio FolhaPagamentoPrevidencia
    * Data de Criação: 29/11/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Rafael Almeida

    * @package URBEM
    * @subpackage Regra

      $Revision: 30566 $
      $Name$
      $Author: souzadl $
      $Date: 2007-10-11 18:10:24 -0300 (Qui, 11 Out 2007) $

      Caso de uso: uc-04.05.04
                   uc-04.05.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidencia.class.php"              );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php"   );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDesconto.class.php"            );
include_once ( CAM_GRH_FOL_NEGOCIO     ."RFolhaPagamentoFaixaDesconto.class.php"          );
include_once ( CAM_GRH_FOL_NEGOCIO     ."RFolhaPagamentoEvento.class.php"                 );

class RFolhaPagamentoPrevidencia
{
/**
    * @access Private
    * @var Integer
*/
var $inCodPrevidencia;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var Float
*/
var $flAliquota;
/**
    * @access Private
    * @var Float
*/
var $flAcidente;
/**
    * @access Private
    * @var String
*/
var $stTipo;
/**
    * @access Private
    * @var String
*/
var $stRetimePrevidenciario;
/**
    * @access Private
    * @var Float
*/
var $flAliquotaRat;
/**
    * @access Private
    * @var Float
*/
var $flAliquotaFap;
/**
    * @access Private
    * @var Integer
    * 1 - Ativo
    * 2 - Aposentado
    * 3 - Pensionista
*/
var $inVinculo;
/**
    * @access Private
    * @var Date
*/
var $dtVigencia;
/**
    * @access Private
    * @var Array
*/
var $arFaixa;
/**
    * @access Private
    * @var Object
*/
var $obUltimaFaixa;
/**
    * @access Private
    * @var Object
*/
var $obRFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTPrevidencia;
/**
    * @access Private
    * @var Object
*/
var $obTPrevidenciaPrevidencia;
/**
    * @access Private
    * @var Object
*/
var $obTFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Array
*/
var $arRFolhaPagamentoEvento;
/**
    * @access Private
    * @var Object
*/
var $roRFolhaPagamentoEvento;
/**
    * @access Private
    * @var Integer
*/
var $inCodRegimePrevidencia;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodPrevidencia($valor) { $this->inCodPrevidencia      = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao  = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setAliquota($valor) { $this->flAliquota     = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setAliquotaRat($valor) { $this->flAliquotaRat  = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setAliquotaFap($valor) { $this->flAliquotaFap  = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setAcidente($valor) { $this->flAcidente         = $valor ; }

/**
    * @access Public
    * @param String $Valor
*/
function setTipo($valor) { $this->stTipo             = $valor ; }
/**
    * @access Public
    * @param String $Valor
*/
function setRegimePrevidenciario($valor) { $this->stRegimePrevidenciario  = $valor ; }

/**
    * @access Public
    * @param Integer $Valor
    * 1 - Ativo
    * 2 - Aposentado
    * 3 - Pensionista
*/
function setVinculo($valor) { $this->inVinculo             = $valor ; }

/**
    * @access Public
    * @param Date $Valor
*/
function setVigencia($valor) { $this->dtVigencia            = $valor ; }

/**
     * @access Public
     * @param Object $valor
*/
function setUltimaFaixa($valor) { $this->obUltimaFaixa       = $valor  ; }
/**
     * @access Public
     * @param Array $valor
*/
function setFaixa($valor) { $this->arFaixa             = $valor  ;  }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPrevidencia($valor) { $this->obTPrevidencia      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPrevidenciaPrevidencia($valor) { $this->obTPrevidenciaPrevidencia      = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTFaixaDesconto($valor) { $this->obTFaixaDesconto    = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRFaixaDesconto($valor) { $this->obRFaixaDesconto    = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setRCadastroDinamico($valor) { $this->obRCadastroDinamico = $valor; }
/**
    * @access Public
    * @param Array $Valor
*/
function setARRFolhaPagamentoEvento($valor) { $this->arRFolhaPagamentoEvento = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRORFolhaPagamentoEvento($valor) { $this->roRFolhaPagamentoEvento = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setCodRegimePrevidencia($valor) { $this->inCodRegimePrevidencia = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodPrevidencia() { return $this->inCodPrevidencia                ; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao                     ; }
/**
    * @access Public
    * @return Float
*/
function getAliquota() { return $this->flAliquota                       ; }
/**
    * @access Public
    * @return Float
*/
function getAliquotaRat() { return $this->flAliquotaRat                    ; }
/**
    * @access Public
    * @return Float
*/
function getAliquotaFap() { return $this->flAliquotaFap                    ; }

/**
    * @access Public
    * @return Float
*/
function getAcidente() { return $this->flAcidente                       ; }
/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo                               ; }
/**
    * @access Public
    * @return String
*/
function getRegimePrevidenciario() { return $this->stRegimePrevidenciario         ; }
/**
    * @access Public
    * @param Integer $Valor
    * 1 - Ativo
    * 2 - Aposentado
    * 3 - Pensionista

*/
function getVinculo() { return $this->inVinculo                    ; }

/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia                    ; }

/**
     * @access Public
     * @return Object
*/
function getUltimaFaixa() { return $this->obUltimaFaixa       ; }

/**
     * @access Public
     * @return Object
*/
function getTFaixa() { return $this->obRFaixa       ; }

/**
     * @access Public
     * @return Array
*/
function getFaixa() { return $this->arFaixa             ;  }
/**
    * @access Public
    * @return Object
*/
function getTPrevidencia() { return $this->obTPrevidencia      ; }
/**
    * @access Public
    * @return Object
*/
function getTPrevidenciaPrevidencia() { return $this->obTPrevidenciaPrevidencia  ; }
/**
    * @access Public
    * @return Object
*/
function getTFaixaDesconto() { return $this->obTFaixaDesconto      ; }
/**
    * @access Public
    * @return Object
*/
function getRFaixaDesconto() { return $this->obRFaixaDesconto            ; }
/**
    * @access Public
    * @return Object
*/
function getRFaixa() { return $this->obRFaixa            ; }
/**
    * @access Public
    * @return Array
*/
function getARRFolhaPagamentoEvento() { return $this->arRFolhaPagamentoEvento            ; }
/**
    * @access Public
    * @return Object
*/
function getRORFolhaPagamentoEvento() { return $this->roRFolhaPagamentoEvento            ; }
/**
    * @access Public
    * @return Integer
*/
function getCodRegimePrevidencia() { return $this->inCodRegimePrevidencia            ; }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoPrevidencia()
{
    $this->setTPrevidencia            ( new TFolhaPagamentoPrevidencia               );
    $this->setTPrevidenciaPrevidencia ( new TFolhaPagamentoPrevidenciaPrevidencia    );
    $this->setTFaixaDesconto          ( new TFolhaPagamentoFaixaDesconto             );
    $this->setRFaixaDesconto          ( new RFolhaPagamentoFaixaDesconto             );
    $this->setRCadastroDinamico       ( new RCadastroDinamico                        );
    $this->obRCadastroDinamico->setCodCadastro( 2 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo( 27 );
    $this->obTransacao         = new Transacao;
    $this->arFaixa             = array();
}

function addFaixa($valor)
{
    $this->arFaixa = $valor;
    //$this->setUltimaFaixa( new RFolhaPagamentoFaixaDesconto );
}
/**
    * Adiciona o objeto do tipo Nivel ao array
    * @access Public
*/
function commitFaixa()
{
    $arElementos   = $this->getFaixa();
    $arElementos[] = $this->getUltimaFaixa();
    $this->setFaixa( $arElementos );
}

/**
    * Adiciona um objeto do tipo RFolhaPagamentoEvento
    * @access Public
*/
function addRFolhaPagamentoEvento()
{
    $this->arRFolhaPagamentoEvento[] = new RFolhaPagamentoEvento;
    $this->roRFolhaPagamentoEvento = &$this->arRFolhaPagamentoEvento[ count($this->arRFolhaPagamentoEvento)-1 ];
    $this->roRFolhaPagamentoEvento->setRORFolhaPagamentoPrevidencia( $this );
}

/**
    * Salva dados de Organograma no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarPrevidencia($boTransacao = "")
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php");
    $obTFolhaPagamentoPrevidenciaEvento = new TFolhaPagamentoPrevidenciaEvento;
    $obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPrevidenciaPrevidencia->setDado("descricao"              , $this->getDescricao() );
        $this->obTPrevidenciaPrevidencia->setDado("aliquota"               , $this->getAliquota()  );
        $this->obTPrevidenciaPrevidencia->setDado("perc_acidente_trabalho" , $this->getAcidente()  );
        $this->obTPrevidenciaPrevidencia->setDado("tipo_previdencia"       , $this->getTipo()      );
        $this->obTPrevidenciaPrevidencia->setDado("vigencia"               , $this->getVigencia()  );
        if (count($this->getFaixa()) <= 0 ) {
            $obErro->setDescricao ("Faixas de desconto não incluídas");

            return $obErro;
        }
        if (!$obErro->ocorreu() ) {
            if (!$this->getCodPrevidencia() ) {
                $ComplementoChave = $this->obTPrevidenciaPrevidencia->getComplementoChave();
                $CampoCod         = $this->obTPrevidenciaPrevidencia->getCampoCod();
                $this->obTPrevidenciaPrevidencia->setComplementoChave('');
                $this->obTPrevidenciaPrevidencia->setCampoCod( 'cod_previdencia' );

                $obErro =  $this->obTPrevidenciaPrevidencia->proximoCod( $inCodPrevidencia , $boTransacao );

                $this->obTPrevidenciaPrevidencia->setComplementoChave( $ComplementoChave );
                $this->obTPrevidenciaPrevidencia->setCampoCod( $CampoCod );

                $this->setCodPrevidencia( $inCodPrevidencia );
                $this->obTPrevidencia->setDado("cod_previdencia" , $this->getCodPrevidencia() );
                $this->obTPrevidencia->setDado("cod_regime_previdencia", $this->getCodRegimePrevidencia());
                $this->obTPrevidencia->setDado("cod_vinculo"     , $this->getVinculo()        );
                $obErro = $this->obTPrevidencia->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPrevidenciaPrevidencia->setDado("cod_previdencia", $this->getCodPrevidencia() );
                $obErro = $this->obTPrevidenciaPrevidencia->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obTPrevidenciaPrevidencia->recuperaNow3($stTimestamp,$boTransacao);
                }

                if ( !$obErro->ocorreu() ) {
                    $this->obRFaixaDesconto->setCodPrevidencia       ( $this->getCodPrevidencia() );
                    $this->obRFaixaDesconto->setFaixa                ( $this->getFaixa()          );
                    $this->obRFaixaDesconto->setTimestampPrevidencia ( $stTimestamp               );

                    $obErro = $this->obRFaixaDesconto->salvarFaixas( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $arChaveAtributoCandidato =  array( "cod_previdencia" => $this->getCodPrevidencia() );
                        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
                        $this->obRCadastroDinamico->salvarValores( $boTransacao );
                    }
                }
            }
            if ( !$obErro->ocorreu() and $this->getCodRegimePrevidencia() == 1 ) {
                $obTFolhaPagamentoPrevidenciaRegimeRat->setDado('cod_previdencia',  $this->getCodPrevidencia());
                $obTFolhaPagamentoPrevidenciaRegimeRat->setDado('timestamp',        $stTimestamp);
                $obTFolhaPagamentoPrevidenciaRegimeRat->setDado('aliquota_rat',  $this->getAliquotaRat() != '' ? $this->getAliquotaRat() : 0);
                $obTFolhaPagamentoPrevidenciaRegimeRat->setDado('aliquota_fap',  $this->getAliquotaFap() != '' ? $this->getAliquotaFap() : 0);
                $obErro = $obTFolhaPagamentoPrevidenciaRegimeRat->inclusao($boTransacao);
            }

            if ( !$obErro->ocorreu() ) {
                for ($inIndex=0;$inIndex<count($this->arRFolhaPagamentoEvento);$inIndex++) {

                    $obRFolhaPagamentoEvento = $this->arRFolhaPagamentoEvento[$inIndex];
                    $obRFolhaPagamentoEvento->listarEvento($rsEvento,$boTransacao);

                    $obTFolhaPagamentoPrevidenciaEvento->setDado('cod_tipo'       , $obRFolhaPagamentoEvento->getCodTipo()  );
                    $obTFolhaPagamentoPrevidenciaEvento->setDado('cod_previdencia', $this->getCodPrevidencia()              );
                    $obTFolhaPagamentoPrevidenciaEvento->setDado('timestamp'      , $stTimestamp                            );
                    $obTFolhaPagamentoPrevidenciaEvento->setDado('cod_evento'     , $rsEvento->getCampo('cod_evento')       );
                    $obErro = $obTFolhaPagamentoPrevidenciaEvento->inclusao($boTransacao);
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrevidenciaPrevidencia );

    return $obErro;
}
/**
    * Exclui dados de Organograma do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirPrevidencia($boTransacao = "")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php" );
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaRegimeRat.class.php");
    $obTFolhaPagamentoPrevidenciaEvento = new TFolhaPagamentoPrevidenciaEvento;
    $obTFolhaPagamentoPrevidenciaRegimeRat = new TFolhaPagamentoPrevidenciaRegimeRat;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPrevidencia->setDado("cod_previdencia", $this->getCodPrevidencia() );
        $obErro = $this->obTPrevidencia->validaExclusao();
        if ( !$obErro->ocorreu() ) {
            $this->obRFaixaDesconto->setCodPrevidencia( $this->getCodPrevidencia() );
            $obErro = $this->obRFaixaDesconto->excluirFaixaDesconto( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $arChaveAtributoCandidato =  array( "cod_previdencia" => $this->getCodPrevidencia() );
                $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoCandidato );
                $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoPrevidenciaEvento->setDado('cod_previdencia',$this->getCodPrevidencia());
                $obErro =  $obTFolhaPagamentoPrevidenciaEvento->exclusao($boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $obTFolhaPagamentoPrevidenciaRegimeRat->setDado('cod_previdencia',$this->getCodPrevidencia());
                $obErro = $obTFolhaPagamentoPrevidenciaRegimeRat->exclusao($boTransacao);

            }
            if ( !$obErro->ocorreu() ) {
                $this->obTPrevidenciaPrevidencia->setDado("cod_previdencia", $this->getCodPrevidencia() );
                $obErro = $this->obTPrevidenciaPrevidencia->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obTPrevidencia->exclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPrevidenciaPrevidencia );

    return $obErro;
}
/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPrevidencia(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";
    if ($this->getCodPrevidencia() != '') {
       $this->obTPrevidencia->setDado('cod_previdencia',$this->getCodPrevidencia());
    } else {
       $this->obTPrevidencia->setDado('cod_previdencia','');
    }
    $obErro = $this->obTPrevidencia->recuperaLista( $rsRecordSet, $stFiltro, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaLista na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPrevidenciaEvento(&$rsRecordSet, $boTransacao = "" , $inCodTipo="")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php" );
    $obTFolhaPagamentoPrevidenciaEvento = new TFolhaPagamentoPrevidenciaEvento;
    if ($this->getCodPrevidencia()) {
        $stFiltro .= " AND prev_evento.cod_previdencia = ".$this->getCodPrevidencia();
    }
    if ($inCodTipo != "") {
        $stFiltro .= " AND cod_tipo = ".$inCodTipo;
    }
    $stOrdem = " prev_evento.timestamp ";
    $obErro = $obTFolhaPagamentoPrevidenciaEvento->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarPrevidencia($boTransacao = "")
{
    $stFiltro = " AND fp.cod_previdencia = ".$this->getCodPrevidencia();
    $obErro = $this->obTPrevidencia->recuperaRelacionamento( $rsPrevidencia, $stFiltro, $stOrdem,$boTransacao );
    
    if ( !$obErro->ocorreu() ) {
        $rsPrevidencia->addFormatacao("aliquota", "NUMERIC_BR");
        $this->setDescricao( $rsPrevidencia->getCampo("descricao")        );
        $this->setAliquota ( $rsPrevidencia->getCampo("aliquota")         );
        $this->setTipo     ( $rsPrevidencia->getCampo("tipo_previdencia") );
        $this->setVinculo  ( $rsPrevidencia->getCampo('cod_vinculo')      );
        $this->setVigencia ( $rsPrevidencia->getCampo('vigencia')         );
        $this->setCodRegimePrevidencia( $rsPrevidencia->getCampo('cod_regime_previdencia') );
        $this->setAliquotaRat( $rsPrevidencia->getCampo('aliquota_rat') );
        $this->setAliquotaFap( $rsPrevidencia->getCampo('aliquota_fap') );
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaPrevidencia(&$rsRecordSet, $boTransacao = "")
{
    $this->obTPrevidencia->setDado("cod_previdencia", $this->getCodPrevidencia() );
    $obErro = $this->obTPrevidencia->recuperaRelacionamento( $rsRecordSet, $boTransacao );

    return $obErro;
}

function listarRegimePrevidencia(&$rsRecordSet, $boTransacao = "")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegimePrevidencia.class.php" );

    $stFiltro = "";

    if ( $this->getCodRegimePrevidencia() ) {
        $stFiltro = " WHERE cod_regime_previdencia = ".$this->getCodRegimePrevidencia();
    }
    $obTFolhaPagamentoRegimePrevidencia = new TFolhaPagamentoRegimePrevidencia;
    $obErro = $obTFolhaPagamentoRegimePrevidencia->recuperaTodos( $rsRecordSet, "", "", $boTransacao );

    return $obErro;
}

function listarTodosRegimePrevidencia(&$rsRecordSet, $boTransacao = "")
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegimePrevidencia.class.php" );

    $obTFolhaPagamentoRegimePrevidencia = new TFolhaPagamentoRegimePrevidencia;
    $obErro = $obTFolhaPagamentoRegimePrevidencia->recuperaTodos( $rsRecordSet, "", "", $boTransacao );

    return $obErro;
}

/**
    * listarPrevidenciasOficiais: lista todas as previdências oficiais
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPrevidenciasOficiais(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro .= " AND tipo_previdencia = 'o'";
    $stOrdem   = " descricao";
    $obErro = $this->obTPrevidenciaPrevidencia->recuperaRelacionamento( $rsRecordSet, $stFiltro="", $stOrdem="", $boTransacao="" );

    return $obErro;
}

/**
    * listarRelatorioContribuicaoPrevidenciaria: Retorna recordset para relatório de contribuição previdenciária
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarRelatorioContribuicaoPrevidenciaria(&$rsRecordSet, $arFiltros, $stOrdem, $boTransacao = "")
{
    $stFiltro .= " AND evento_base.cod_previdencia = ".$arFiltros['inCodPrevidencia'];
    if ( is_array($arFiltros['arRegistros']) ) {
        $stRegistros = "";
        foreach ($arFiltros['arRegistros'] as $inRegistros) {
            $stRegistros .= $inRegistros.",";
        }
        $stRegistros = substr($stRegistros,0,strlen($stRegistros)-1);
        $stFiltro .= " AND registro IN (".$stRegistros.")";
    }
    if ( isset($arFiltros['cod_periodo_movimentacao']) ) {
        $stFiltro .= " AND contratos_calculados.cod_periodo_movimentacao = ".$arFiltros['cod_periodo_movimentacao'];
    }
    if ( is_array($arFiltros['arCodLotacaoSelecionados']) ) {
        $stLotacao = "";
        foreach ($arFiltros['arCodLotacaoSelecionados'] as $inLotacao) {
            $stLotacao .= "'".$inLotacao."',";
        }
        $stLotacao = substr($stLotacao,0,strlen($stLotacao)-1);
        $stFiltro .= " AND cod_orgao IN (".$stLotacao.")";
    }
    if ( is_array($arFiltros['arCodLocalSelecionados']) ) {
        $stLocal = "";
        foreach ($arFiltros['arCodLocalSelecionados'] as $inLocal) {
            $stLocal .= $inLocal.",";
        }
        $stLocal = substr($stLocal,0,strlen($stLocal)-1);
        $stFiltro .= " AND cod_local IN (".$stLocal.")";
    }
    if ( is_array($arFiltros['arCodRegimeSelecionadosFunc']) ) {
        $stRegime = "";
        foreach ($arFiltros['arCodRegimeSelecionadosFunc'] as $inRegime) {
            $stRegime .= $inRegime.",";
        }
        $stRegime = substr($stRegime,0,strlen($stRegime)-1);
        $stFiltro .= " AND cod_regime_funcao IN (".$stRegime.")";
    }
    if ( is_array($arFiltros['arCodSubDivisaoSelecionadosFunc']) ) {
        $stSubdivisao = "";
        foreach ($arFiltros['arCodSubDivisaoSelecionadosFunc'] as $inSubdivisao) {
            $stSubdivisao .= $inSubdivisao.",";
        }
        $stSubdivisao = substr($stSubdivisao,0,strlen($stSubdivisao)-1);
        $stFiltro .= " AND cod_sub_divisao IN (".$stSubdivisao.")";
    }
    if ($arFiltros['boAtivo'] and $arFiltros['boAposentado']) {
        $stFiltro .= " AND (ativo = true or ativo = false)";
    } elseif ($arFiltros['boAtivo'] and !$arFiltros['boAposentado']) {
         $stFiltro .= " AND ativo = true";
    } elseif (!$arFiltros['boAtivo'] and $arFiltros['boAposentado']) {
         $stFiltro .= " AND ativo = false";
    }
    //if ($arFiltros['boPensionista']) {
    //    $stFiltro .= " AND ativo = true";
    //}

    $obErro = $this->obTPrevidencia->recuperaRelatorioContribuicaoPrevidenciaria( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * listarRelatorioContribuicaoPrevidenciaria: Retorna recordset para relatório de contribuição previdenciária
    * @access Public
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarFaixasDescontosPrevidencias(&$rsRecordSet, $arFiltros, $stOrdem, $boTransacao = "")
{
    if ($arFiltros['inCodContrato'] != "") {
        $stFiltro .= " AND contrato_servidor_previdencia.cod_contrato = ".$arFiltros['inCodContrato'];
    }
    if ($arFiltros['inCodPrevidencia'] != "") {
        $stFiltro .= " AND contrato_servidor_previdencia.cod_previdencia = ".$arFiltros['inCodPrevidencia'];
    }
    if ($arFiltros['flValorUnico'] != "") {
        $stFiltro .= " AND faixa_desconto.valor_inicial <= ".$arFiltros['flValorUnico'];
        $stFiltro .= " AND faixa_desconto.valor_final   >= ".$arFiltros['flValorUnico'];
    }
    if ($arFiltros['stTipoPrevidencia'] != "") {
        $stFiltro .= " AND previdencia_previdencia.tipo_previdencia = '".$arFiltros['stTipoPrevidencia']."'";
    }
    $obErro = $this->obTPrevidencia->recuperaFaixasDescontosPrevidencias( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

}

}
?>
