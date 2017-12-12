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
* Classe de regra de negócio para Pessoal-Padrao
* Data de Criação: 02/12/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Gustavo Passos Tourinho

* @package URBEM
* @subpackage  Regra

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

* Casos de uso: uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadrao.class.php" );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPadraoPadrao.class.php" );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoNivelPadrao.class.php" );

/**
    * Classe de regra de negócio para Pessoal-Padrao
    * Data de Criação: 02/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @package URBEM
    * @subpackage Regra
*/
class RFolhaPagamentoPadrao
{
/**
    * @access Private
    * @var Integer
*/
var $inCodPadrao;
/**
    * @access Private
    * @var String
*/
var $stDescricaoPadrao;
/**
    * @access Private
    * @var float
*/
var $flHorasMensais;
/**
    * @access Private
    * @var float
*/
var $flHorasSemanais;
/**
    * @access Private
    * @var float
*/
var $flValor;
/**
    * @access Private
    * @var Date
*/
var $dtVigencia;
/**
    * @access Private
    * @var Object
*/
var $obRNorma;
/**
    * @access Private
    * @var Object
*/
var $roUltimoNivelPadrao;
/**
    * @access Private
    * @var Array
*/
var $arRFolhaPagamentoNivelPadrao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodPadrao($valor) { $this->inCodPadrao       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricaoPadrao($valor) { $this->stDescricaoPadrao = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setHorasMensais($valor) { $this->flHorasMensais    = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setHorasSemanais($valor) { $this->flHorasSemanais   = $valor; }
/**
    * @access Public
    * @param float $valor
*/
function setValor($valor) { $this->flValor           = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setVigencia($valor) { $this->dtVigencia        = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodPadrao() { return $this->inCodPadrao;       }
/**
    * @access Public
    * @return String
*/
function getDescricaoPadrao() { return $this->stDescricaoPadrao; }
/**
    * @access Public
    * @return float
*/
function getHorasMensais() { return $this->flHorasMensais;    }
/**
    * @access Public
    * @return float
*/
function getHorasSemanais() { return $this->flHorasSemanais;   }
/**
    * @access Public
    * @return float
*/
function getValor() { return $this->flValor;           }
/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia;        }

/**
     * Método construtor
     * @access Private
*/
function RFolhaPagamentoPadrao()
{
    $this->obTFolhaPagamentoPadrao      = new TFolhaPagamentoPadrao;
    $this->obTFolhaPagamentoPadraoPadrao= new TFolhaPagamentoPadraoPadrao;
    $this->obRNorma                     = new RNorma;
    $this->obRFolhaPagamentoNivelPadrao = new RFolhaPagamentoNivelPadrao( $this );
    $this->obTransacao                  = new Transacao;
    $this->arRFolhaPagamentoNivelPadrao = array ();
}

/**
    * Inclui os dados do padrao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvarPadrao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->inCodPadrao) {
            $obErro = $this->obTFolhaPagamentoPadrao->proximoCod ( $this->inCodPadrao, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTFolhaPagamentoPadrao->setDado ( "cod_padrao"    , $this->inCodPadrao              );
                $this->obTFolhaPagamentoPadrao->setDado ( "descricao"     , $this->stDescricaoPadrao        );
                $this->obTFolhaPagamentoPadrao->setDado ( "horas_mensais" , $this->flHorasMensais           );
                $this->obTFolhaPagamentoPadrao->setDado ( "horas_semanais", $this->flHorasSemanais          );
                $obErro = $this->obTFolhaPagamentoPadrao->inclusao ( $boTransacao );
            }
        }
        if ( !$obErro->ocorreu () ) {
            $this->obTFolhaPagamentoPadraoPadrao->setDado ( "cod_padrao"    , $this->inCodPadrao              );
            $this->obTFolhaPagamentoPadraoPadrao->setDado ( "cod_norma"     , $this->obRNorma->getCodNorma () );
            $this->obTFolhaPagamentoPadraoPadrao->setDado ( "valor"         , $this->flValor                  );
            $this->obTFolhaPagamentoPadraoPadrao->setDado ( "vigencia"      , $this->getVigencia()            );
            $obErro = $this->obTFolhaPagamentoPadraoPadrao->inclusao ( $boTransacao );
        }
        if ( !$obErro->ocorreu () ) {
            for ( $inCount = 0; ($inCount < count ($this->arRFolhaPagamentoNivelPadrao)) && (!$obErro->ocorreu ()); $inCount++ ) {
                $obErro = $this->arRFolhaPagamentoNivelPadrao[$inCount]->salvarNivelPadrao ( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPadrao );

    return $obErro;
}

/**
    * Lista os Padroes segundo o filtro setado
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPadrao(&$rsRecordSet , $boTransacao = "" , $stFiltro = "")
{
    if ($this->inCodPadrao != "") {
        $stFiltro .= " AND FPP.cod_padrao = ".$this->inCodPadrao;
    }
    if ($this->stDescricaoPadrao) {
        $stFiltro .= " AND UPPER(descricao) LIKE UPPER('%".$this->stDescricaoPadrao."%')";
    }

    $stOrdem = " ORDER BY UPPER(descricao)";
    $obErro = $this->obTFolhaPagamentoPadrao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Padroes segundo o uma pesquisa realizada no tabela pessoal.contrato_servidor_caso_causa
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarPadraoPorContratosInativos(&$rsRecordSet , $boTransacao = "" , $stFiltro = "")
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
    $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
    $obTPessoalContratoServidorCasoCausa->setDado("cod_contrato",$_REQUEST['inCodContrato']);
    $obTPessoalContratoServidorCasoCausa->recuperaPorChave($rsContrato);
    $stFiltroPadraoParaInativos = "";

    if ($rsContrato->getNumLinhas() > 0) {
        $stFiltroPadraoParaInativos = " WHERE MAXFPP.timestamp <= (SELECT timestamp FROM pessoal.contrato_servidor_caso_causa WHERE cod_contrato=". $_REQUEST['inCodContrato'] .")  ";
    }

    if ($this->inCodPadrao != "") {
        $stFiltro .= " AND FPP.cod_padrao = ".$this->inCodPadrao;
    }
    if ($this->stDescricaoPadrao) {
        $stFiltro .= " AND UPPER(descricao) LIKE UPPER('%".$this->stDescricaoPadrao."%')";
    }

    $stOrdem = " ORDER BY UPPER(descricao)";
    $obErro = $this->obTFolhaPagamentoPadrao->recuperaRelacionamentoPorContratosInativos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao, $stFiltroPadraoParaInativos );

    return $obErro;
}

/**
    * Adiciona um NivelPadrao
    * @access Public
*/

function addNivelPadrao()
{
    $this->arRFolhaPagamentoNivelPadrao[] = new RFolhaPagamentoNivelPadrao ( $this );
    $this->roUltimoNivelPadrao     = &$this->arRFolhaPagamentoNivelPadrao[ count($this->arRFolhaPagamentoNivelPadrao) - 1 ];
}

/**
    * Exclui os dados do padrao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirPadrao($boTransacao = "")
{
    $boFlagTransacao = false;
    $this->obTFolhaPagamentoPadrao->setDado ( "cod_padrao" , $this->inCodPadrao );
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTFolhaPagamentoPadrao->validaExclusao("", $boTransacao);

        if ( !$obErro->ocorreu() ) {
            if ( !$obErro->ocorreu() ) {
                $this->obRFolhaPagamentoNivelPadrao->roRFolhaPagamentoPadrao->setCodPadrao( $this->inCodPadrao );
                $obErro = $this->obRFolhaPagamentoNivelPadrao->listarNivelPadrao($rsNivelPadraoNivel,$boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTFolhaPagamentoPadraoPadrao->setDado ( "cod_padrao" , $this->inCodPadrao );
                $obErro = $this->obTFolhaPagamentoPadraoPadrao->exclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obRFolhaPagamentoNivelPadrao->obTFolhaPagamentoNivelPadraoNivel->setDado ( "cod_padrao" , $this->inCodPadrao );
                $obErro = $this->obRFolhaPagamentoNivelPadrao->obTFolhaPagamentoNivelPadraoNivel->exclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                while ( !$rsNivelPadraoNivel->eof() ) {
                    $this->obRFolhaPagamentoNivelPadrao->obTFolhaPagamentoNivelPadrao->setDado ( "cod_nivel_padrao" , $rsNivelPadraoNivel->getCampo('cod_nivel_padrao') );
                    $obErro = $this->obRFolhaPagamentoNivelPadrao->obTFolhaPagamentoNivelPadrao->exclusao( $boTransacao );
                    $rsNivelPadraoNivel->proximo();
                }
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->obTFolhaPagamentoPadrao->exclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTFolhaPagamentoPadrao );

    return $obErro;
}

}
?>
