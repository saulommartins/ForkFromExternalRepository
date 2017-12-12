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
    * Classe de regra de negócio Pessoal.Assentamento
    * Data de Criação: 06/06/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Lucas Leusin
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Id: RPessoalAssentamento.class.php 66365 2016-08-18 14:39:09Z evandro $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php"       );
include_once ( CAM_GA_NORMAS_NEGOCIO."RNorma.class.php"                                  );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalRegime.class.php"                          );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEsferaOrigem.class.php"                    );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"                      );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSefip.class.php"                           );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCausaRescisao.class.php"                   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoFaixaDesconto.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"            );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoMotivo.class.php"              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPrevidencia.class.php"              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamento.class.php"               );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoRegime.class.php"         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoEvento.class.php"         );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoMovSefipSaida.class.php"  );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoCausaRescisao.class.php"  );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoFaixaDesconto.class.php"  );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                 );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoSubDivisao.class.php"     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoOperador.class.php"       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoValidade.class.php"       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAfastamentoTemporario.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAfastamentoTemporarioDuracao.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoEventoProporcional.class.php" );

class RPessoalAssentamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodAssentamento;
/**
    * @access Private
    * @var Integer
*/
var $inCodRais;
/**
    * @access Private
    * @var String
*/
var $stDescricao;
/**
    * @access Private
    * @var String
*/
var $stSigla;
/**
    * @access Private
    * @var String
*/
var $stAbreviacao;
/**
    * @access Private
    * @var Boolean
*/
var $boEventoAutomatico;
/**
    * @access Private
    * @var Boolean
*/
var $boCancelarDireito;
/**
    * @access Private
    * @var Boolean
*/
var $boAssentamentoAutomatico;
/**
    * @access Private
    * @var Boolean
*/
var $boPartidaDupla;
/**
    * @access Private
    * @var Boolean
*/
var $boRelFuncaoGratificada;
/**
    * @access Private
    * @var Boolean
*/
var $boGradeEfetividade;
/**
    * @access Private
    * @var Integer
*/
var $inCodOperador;
/**
    * @access Private
    * @var Boolean
*/
var $boAssentamentoInicio;
/**
    * @access Private
    * @var Date
*/
var $dtDataInicial;
/**
    * @access Private
    * @var Integer
*/
var $inDiasAfastamento;
/**
    * @access Private
    * @var Date
*/
var $dtDataFinal;

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
var $obRPessoalAssentamentoFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obRPessoalAssentamentoMotivo;
/**
    * @access Private
    * @var Object
*/
var $obRPessoalCondicaoAssentamento;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamentoFaixaDesconto;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamentoSubDivisao;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamentoAssentamento;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalAssentamento;
/**
    * @access Private
    * @var Object
*/
var $obTPessoalRegime;
/**
    * @var Object
    * @access Private
*/
var $obTFolhaPagamentoEvento;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalSefip;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalCausaRescisao;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalAssentamentoOperador;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalAssentamentoValidade;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalAssentamentoAfastamentoTemporario;
/**
    * @var Object
    * @access Private
*/
var $obTPessoalAssentamentoAfastamentoTemporarioDuracao;
/**
    * @var Object
    * @access Private
*/
var $obRNorma;
/**
    * @access Public
    * @param Integer $Valor
*/
var $obRPessoalClassificacaoAssentamento;

/**
    * @access Private
    * @var array
*/
var $arRPessoalSubDivisao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoSubDivisao;

/**
    * @access Private
    * @var array
*/
var $arRPessoalCausaRescisao;
/**
    * @access Private
    * @var Object
*/
var $roUltimoPessoalCausaRescisao;
/**
    * @access Private
    * @var array
*/
var $arRFolhaPagamentoEvento;
/**
    * @access Private
    * @var Object
*/
var $roUltimoFolhaPagamentoEvento;
/**
    * @access Private
    * @var array
*/
var $arRFolhaPagamentoEventoProporcional;
/**
    * @access Private
    * @var Object
*/
var $roRFolhaPagamentoEventoProporcional;
/**
    * @access Private
    * @var Object
*/
var $obRPessoalEsferaOrigem;
/**
    * @access Private
    * @var Object
*/
var $obRPessoalVantagem;
/**
    * @access Private
    * @var String
*/
var $stTimestamp;
/**
    * @access Private
    * @var Object
*/
var $obRFolhaPagamentoPrevidencia;

/**
    * @access Private
    * @param integer $valor
*/
function setCodAssentamento($valor) { $this->inCodAssentamento = $valor; }
/**
    * @access Private
    * @param integer $valor
*/
function setCodRais($valor) { $this->inCodRais = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setSigla($valor) { $this->stSigla = $valor; }
/**
    * @access Private
    * @param String $valor
*/
function setAbreviacao($valor) { $this->stAbreviacao = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setEventoAutomatico($valor) { $this->boEventoAutomatico = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setCancelarDireito($valor) { $this->boCancelarDireito = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setAssentamentoAutomatico($valor) { $this->boAssentamentoAutomatico = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setPartidaDupla($valor) { $this->boPartidaDupla = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setRelFuncaoGratificada($valor) { $this->boRelFuncaoGratificada = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setGradeEfetividade($valor) { $this->boGradeEfetividade = $valor; }
/**
    * @access Private
    * @param integer $valor
*/
function setCodOperador($valor) { $this->inCodOperador = $valor; }
/**
    * @access Private
    * @param Boolean $valor
*/
function setAssentamentoInicio($valor) { $this->boAssentamentoInicio = $valor; }
/**
    * @access Private
    * @param Date $valor
*/
function setDataInicial($valor) { $this->dtDataInicial = $valor; }
/**
    * @access Private
    * @param Date $valor
*/
function setDataFinal($valor) { $this->dtDataFinal = $valor; }
/**
    * @access Private
    * @param Integer $valor
*/
function setDiasAfastamento($valor) { $this->inDiasAfastamento = $valor; }
/**
    * @access Public
    * @param String $Valor
*/
function setUltimaFaixa($valor) { $this->obUltimaFaixa       = $valor  ; }
/**
     * @access Public
     * @param Array $valor
*/
function setFaixa($valor) { $this->arFaixa             = $valor  ;  }
/**
    * @access Public
    * @return String
*/
function setTimestamp($valor) { $this->stTimestamp    = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalAssentamentoFaixaDesconto($valor) { $this->obRPessoalAssentamentoFaixaDesconto              = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalAssentamentoMotivo($valor) { $this->obRPessoalAssentamentoMotivo              = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalCondicaoAssentamento($valor) { $this->obRPessoalCondicaoAssentamento              = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRNorma($valor) { $this->obRNorma                                         = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalEsferaOrigem($valor) { $this->obRPessoalEsferaOrigem                           = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalRegime($valor) { $this->obRPessoalRegime                                 = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalClassificacaoAssentamento($valor) { $this->obRPessoalClassificacaoAssentamento              = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalSefip($valor) { $this->obRPessoalSefip                                  = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRPessoalVantagem($valor) { $this->obRPessoalVantagem                   = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTransacao($valor) { $this->obTransacao                                      = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoFaixaDesconto($valor) { $this->obTPessoalAssentamentoFaixaDesconto              = $valor  ; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamento($valor) { $this->obTPessoalAssentamento                           = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoSubDivisao($valor) { $this->obTPessoalAssentamentoSubDivisao                 = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoOperador($valor) { $this->obTPessoalAssentamentoOperador                   = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoRegime($valor) { $this->obTPessoalAssentamentoRegime                     = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoEvento($valor) { $this->obTPessoalAssentamentoEvento                     = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoEventoProporcional($valor) { $this->obTPessoalAssentamentoEventoProporcional         = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoMovSefipSaida($valor) { $this->obTPessoalAssentamentoMovSefipSaida              = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoCausaRescisao($valor) { $this->obTPessoalAssentamentoCausaRescisao              = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalSubDivisao($valor) { $this->obTPessoalSubDivisao                             = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoValidade($valor) { $this->obTPessoalAssentamentoValidade                   = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoAfastamentoTemporario($valor) { $this->obTPessoalAssentamentoAfastamentoTemporario      = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoAfastamentoTemporarioDuracao($valor) { $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao      = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setTPessoalAssentamentoAssentamento($valor) { $this->obTPessoalAssentamentoAssentamento = $valor; }
/**
    * @access Public
    * @param Object $Valor
*/
function setRFolhaPagamentoPrevidencia($valor) { $this->obRFolhaPagamentoPrevidencia = $valor; }

/**
    * @access Private
    * @return integer
*/
function getCodAssentamento() { return $this->inCodAssentamento; }
/**
    * @access Private
    * @return integer
*/
function getCodRais() { return $this->inCodRais; }
/**
    * @access Private
    * @return String
*/
function getDescricao() { return $this->stDescricao ; }
/**
    * @access Private
    * @return String
*/
function getSigla() { return $this->stSigla ; }
/**
    * @access Private
    * @return String
*/
function getAbreviacao() { return $this->stAbreviacao ; }
/**
    * @access Private
    * @return Boolean
*/
function getEventoAutomatico() { return $this->boEventoAutomatico ; }
/**
    * @access Private
    * @return Boolean
*/
function getCancelarDireito() { return $this->boCancelarDireito ; }
/**
    * @access Private
    * @return Boolean
*/
function getAssentamentoAutomatico() { return $this->boAssentamentoAutomatico ; }
/**
    * @access Private
    * @return Boolean
*/
function getPartidaDupla() { return $this->boPartidaDupla ; }
/**
    * @access Private
    * @return Boolean
*/
function getRelFuncaoGratificada() { return $this->boRelFuncaoGratificada ; }
/**
    * @access Private
    * @return Boolean
*/
function getGradeEfetividade() { return $this->boGradeEfetividade ; }
/**
    * @access Private
    * @return integer
*/
function getCodOperador() { return $this->inCodOperador ; }
/**
    * @access Private
    * @return Boolean
*/
function getAssentamentoInicio() { return $this->boAssentamentoInicio ; }
/**
    * @access Private
    * @return Date
*/
function getDataInicial() { return $this->dtDataInicial ; }
/**
    * @access Private
    * @return Date
*/
function getDataFinal() { return $this->dtDataFinal ; }
/**
    * @access Private
    * @return Integer
*/
function getDiasAfastamento() { return $this->inDiasAfastamento ; }
/**
    * @access Public
    * @return String
*/
function getUltimaFaixa() { return $this->obUltimaFaixa       ; }
/**
     * @access Public
     * @return Array
*/
function getFaixa() { return $this->arFaixa             ;  }
/**
    * @access Public
    * @return String
*/
function getTimestamp() { return $this->stTimestamp    ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalAssentamentoFaixaDesconto() { return $this->obRPessoalAssentamentoFaixaDesconto              ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalAssentamentoMotivo() { return $this->obRPessoalAssentamentoMotivo              ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalCondicaoAssentamento() { return $this->obRPessoalCondicaoAssentamento              ; }
/**
    * @access Public
    * @return Object
*/
function getRNorma() { return $this->obRNorma                                         ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalEsferaOrigem() { return $this->obRPessoalEsferaOrigem                           ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalRegime() { return $this->obRPessoalRegime                                 ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalClassificacaoAssentamento() { return $this->obRPessoalClassificacaoAssentamento              ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalSefip() { return $this->obRPessoalSefip                                  ; }
/**
    * @access Public
    * @return Object
*/
function getRPessoalVantagem() { return $this->obRPessoalVantagem                    ; }
/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao                                      ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoFaixaDesconto() { return $this->obTPessoalAssentamentoFaixaDesconto              ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamento() { return $this->obTPessoalAssentamento                           ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoSubDivisao() { return $this->obTPessoalAssentamentoSubDivisao                 ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoOperador() { return $this->obTPessoalAssentamentoOperador                   ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoRegime() { return $this->obTPessoalAssentamentoRegime                     ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoEvento() { return $this->obTPessoalAssentamentoEvento                     ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoEventoProporcional() { return $this->obTPessoalAssentamentoEventoProporcional         ; }

/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoMovSefipSaida() { return $this->obTPessoalAssentamentoMovSefipSaida              ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoCausaRescisao() { return $this->obTPessoalAssentamentoCausaRescisao              ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalSubDivisao() { return $this->obTPessoalSubDivisao                             ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoAfastamentoTemporario() { return $this->obTPessoalAssentamentoAfastamentoTemporario       ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoAfastamentoTemporarioDuracao() { return $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao       ; }
/**
    * @access Public
    * @return Object
*/
function getTPessoalAssentamentoAssentamento() { return $this->obTPessoalAssentamentoAssentamento; }
/**
    * @access Public
    * @return Object
*/
function getRFolhaPagamentoPrevidencia() { return $this->obRFolhaPagamentoPrevidencia; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalAssentamento(&$obRPessoalVantagem)
{
    $this->setTPessoalAssentamento              ( new TPessoalAssentamento              );
    $this->setTPessoalAssentamentoSubDivisao    ( new TPessoalAssentamentoSubDivisao    );
    $this->setTransacao                         ( new Transacao                         );
    $this->setRPessoalClassificacaoAssentamento ( new RPessoalClassificacaoAssentamento );
    $this->setRPessoalEsferaOrigem              ( new RPessoalEsferaOrigem              );
    $this->setRNorma                            ( new RNorma                            );
    $this->setRPessoalAssentamentoFaixaDesconto ( new RPessoalAssentamentoFaixaDesconto );
    $this->setRPessoalCondicaoAssentamento      ( new RPessoalCondicaoAssentamento      );
    $this->setRPessoalRegime                    ( new RPessoalRegime                    );
    $this->setRPessoalVantagem                  ( $obRPessoalVantagem                  );
    $this->setTPessoalAssentamentoRegime        ( new TPessoalAssentamentoRegime        );
    $this->setTPessoalAssentamentoEvento        ( new TPessoalAssentamentoEvento        );
    $this->setRPessoalSefip                     ( new RPessoalSefip                     );
    $this->setTPessoalAssentamentoMovSefipSaida ( new TPessoalAssentamentoMovSefipSaida );
    $this->setTPessoalAssentamentoCausaRescisao ( new TPessoalAssentamentoCausaRescisao );
    $this->setTPessoalSubDivisao                ( new TPessoalSubDivisao                );
    $this->setTPessoalAssentamentoOperador      ( new TPessoalAssentamentoOperador      );
    $this->setTPessoalAssentamentoValidade      ( new TPessoalAssentamentoValidade      );
    $this->setTPessoalAssentamentoAssentamento  ( new TPessoalAssentamentoAssentamento  );
    $this->setTPessoalAssentamentoAfastamentoTemporario         ( new TPessoalAssentamentoAfastamentoTemporario );
    $this->setTPessoalAssentamentoAfastamentoTemporarioDuracao  ( new TPessoalAssentamentoAfastamentoTemporarioDuracao );
    $this->setTPessoalAssentamentoEventoProporcional ( new TPessoalAssentamentoEventoProporcional );
    $this->setRFolhaPagamentoPrevidencia        ( new RFolhaPagamentoPrevidencia        );
    $this->setRPessoalAssentamentoMotivo        ( new RPessoalAssentamentoMotivo        );
    $this->arFaixa                              = array();
    $this->arRPessoalSubDivisao                 = array();
    $this->arRPessoalCausaRescisao              = array();
    $this->arRFolhaPagamentoEvento              = array();

}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addPessoalSubDivisao()
{
   $this->arRPessoalSubDivisao[]      =  new RPessoalSubDivisao($this);
   $this->roUltimoPessoalSubDivisao   = &$this->arRPessoalSubDivisao[ count($this->arRPessoalSubDivisao) - 1 ];
}

/**
* Adiciona um array de referencia-objeto
* @access Public
*/
function addPessoalCausaRescisao()
{
   $this->arRPessoalCausaRescisao[]      =  new RPessoalCausaRescisao($this);
   $this->roUltimoPessoalCausaRescisao   = &$this->arRPessoalCausaRescisao[ count($this->arRPessoalCausaRescisao) - 1 ];
}

//Instancia as classes vinculadas ao Evento
function addEvento()
{
   $this->arRFolhaPagamentoEvento[]      =  new RFolhaPagamentoEvento($this);
   $this->roUltimoFolhaPagamentoEvento   = &$this->arRFolhaPagamentoEvento[ count($this->arRFolhaPagamentoEvento) - 1 ];
}

//Instancia as classes vinculadas ao Evento
function addEventoProporcional()
{
   $this->arRFolhaPagamentoEventoProporcional[] = new RFolhaPagamentoEvento($this);
   $this->roRFolhaPagamentoEventoProporcional   = &$this->arRFolhaPagamentoEventoProporcional[ count($this->arRFolhaPagamentoEventoProporcional) - 1 ];
}

function addFaixa($valor)
{
    $this->arFaixa = $valor;
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
    * Inclui dados de assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirAssentamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoAssentamento->proximoCod( $inCodAssentamento , $boTransacao );
        $this->setCodAssentamento( $inCodAssentamento );
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_norma",             $this->obRNorma->getCodNorma()                  );
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_classificacao",     $this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() );
        $this->obTPessoalAssentamentoAssentamento->setDado("sigla",                 $this->getSigla()                               );
        $this->obTPessoalAssentamentoAssentamento->setDado("abreviacao",            $this->getAbreviacao()                               );
        $this->obTPessoalAssentamentoAssentamento->setDado("descricao",             $this->getDescricao()                           );
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_operador",          $this->getCodOperador()                         );
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_assentamento",      $this->getCodAssentamento()                     );        
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_motivo",            $this->obRPessoalAssentamentoMotivo->getCodMotivo());
        $obErro = $this->obTPessoalAssentamentoAssentamento->inclusao($boTransacao);
        if (!$obErro->ocorreu()) {
            $this->obTPessoalAssentamento->setDado("grade_efetividade",                 $this->getGradeEfetividade()                    );
            $this->obTPessoalAssentamento->setDado("rel_funcao_gratificada",            $this->getRelFuncaoGratificada()                );
            $this->obTPessoalAssentamento->setDado("evento_automatico",                 $this->getEventoAutomatico()                    );
            $this->obTPessoalAssentamento->setDado("assentamento_automatico",           $this->getAssentamentoAutomatico()             );
            $this->obTPessoalAssentamento->setDado("cod_esfera",                        $this->obRPessoalEsferaOrigem->getCodEsfera()   );
            $this->obTPessoalAssentamento->setDado("assentamento_inicio",               $this->getAssentamentoInicio()                  );
            $this->obTPessoalAssentamento->setDado("cod_assentamento",                  $this->getCodAssentamento() );
            $this->obTPessoalAssentamento->setDado("quant_dias_onus_empregador",        $this->obRPessoalAssentamentoMotivo->getQuantDiasOnusEmpregador());
            $this->obTPessoalAssentamento->setDado("quant_dias_licenca_premio",         $this->obRPessoalAssentamentoMotivo->getQuantDiasLicencaPremio());
            $obErro = $this->obTPessoalAssentamento->inclusao( $boTransacao );
        }

        if (!$obErro->ocorreu()) {
            $this->obTPessoalAssentamentoValidade->setDado('cod_assentamento',      $this->getCodAssentamento() );
            $this->obTPessoalAssentamentoValidade->setDado('dt_inicial',            $this->getDataInicial()     );
            $this->obTPessoalAssentamentoValidade->setDado('dt_final',              $this->getDataFinal()       );
            $this->obTPessoalAssentamentoValidade->setDado('cancelar_direito',      $this->getCancelarDireito() );
            $this->obTPessoalAssentamentoValidade->recuperaNow3($stNow, $boTransacao);
            $this->obTPessoalAssentamentoValidade->setDado("timestamp",             $stNow                      );
            $obErro = $this->obTPessoalAssentamentoValidade->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu() and is_array($this->arRFolhaPagamentoEvento)) {
            foreach ($this->arRFolhaPagamentoEvento as $obRFolhaPagamentoEvento) {
                $this->obTPessoalAssentamentoEvento->setDado('cod_assentamento',    $this->getCodAssentamento()                 );
                $this->obTPessoalAssentamentoEvento->setDado('cod_evento',          $obRFolhaPagamentoEvento->getCodEvento()    );
                $this->obTPessoalAssentamentoEvento->setDado('timestamp',           $stNow                                      );
                $this->obTPessoalAssentamentoEvento->setDado('vigencia',            date('d/m/Y')                               );
                $obErro = $this->obTPessoalAssentamentoEvento->inclusao($boTransacao);
            }
        }
        if (!$obErro->ocorreu() and is_array($this->arRFolhaPagamentoEventoProporcional)) {
            foreach ($this->arRFolhaPagamentoEventoProporcional as $obRFolhaPagamentoEvento) {
                $this->obTPessoalAssentamentoEventoProporcional->setDado('cod_assentamento',    $this->getCodAssentamento()                 );
                $this->obTPessoalAssentamentoEventoProporcional->setDado('cod_evento',          $obRFolhaPagamentoEvento->getCodEvento()    );
                $this->obTPessoalAssentamentoEventoProporcional->setDado('timestamp',           $stNow                                      );
                $obErro = $this->obTPessoalAssentamentoEventoProporcional->inclusao($boTransacao);
            }
        }

        if (!$obErro->ocorreu() and is_array($this->arRPessoalSubDivisao)) {
            foreach ($this->arRPessoalSubDivisao as $obRPessoalSubDivisao) {
                $this->obTPessoalAssentamentoSubDivisao->setDado('cod_assentamento'  ,$this->getCodAssentamento());
                $this->obTPessoalAssentamentoSubDivisao->setDado('cod_sub_divisao'   ,$obRPessoalSubDivisao->getCodSubDivisao());
                $this->obTPessoalAssentamentoSubDivisao->setDado('timestamp',$stNow);
                $this->obTPessoalAssentamentoSubDivisao->setDado('vigencia'  ,date('d/m/Y'));
                $obErro = $this->obTPessoalAssentamentoSubDivisao->inclusao($boTransacao);
            }
        }

        // dados do afastamento
        if ( !$obErro->ocorreu()) {
            //dados do afastamento temporario
            $this->obTPessoalAssentamentoAfastamentoTemporario->setDado("cod_assentamento", $this->getCodAssentamento());
            $this->obTPessoalAssentamentoAfastamentoTemporario->setDado("timestamp", $stNow);
            $obErro = $this->obTPessoalAssentamentoAfastamentoTemporario->inclusao($boTransacao);

            //dados do afastamento temporario mov sefip saida
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 2 ) {
                if ( !$obErro->ocorreu() and $this->obRPessoalSefip->getCodSefip() != "") {
                    $this->obTPessoalAssentamentoMovSefipSaida->setDado("cod_assentamento", $this->getCodAssentamento());
                    $this->obTPessoalAssentamentoMovSefipSaida->setDado('timestamp',$stNow);
                    $this->obTPessoalAssentamentoMovSefipSaida->setDado('cod_sefip_saida',$this->obRPessoalSefip->getCodSefip());
                    $obErro = $this->obTPessoalAssentamentoMovSefipSaida->inclusao($boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        $this->obRPessoalAssentamentoFaixaDesconto->setCodAssentamento( $this->getCodAssentamento() );
                        $this->obRPessoalAssentamentoFaixaDesconto->setFaixa(         $this->getFaixa()           );
                        $obErro = $this->obRPessoalAssentamentoFaixaDesconto->salvarFaixas( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("cod_assentamento"   ,$this->getCodAssentamento());
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("timestamp"          ,$stNow);
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("dia"                ,$this->getDiasAfastamento());
                        $obErro = $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->inclusao($boTransacao);
                    }
                }
                if ( !$obErro->ocorreu() and $this->getCodRais() != "") {
                    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoRaisAfastamento.class.php");
                    $obTPessoalAssentamentoRaisAfastamento = new TPessoalAssentamentoRaisAfastamento();
                    $obTPessoalAssentamentoRaisAfastamento->setDado("cod_assentamento", $this->getCodAssentamento());
                    $obTPessoalAssentamentoRaisAfastamento->setDado('timestamp',$stNow);
                    $obTPessoalAssentamentoRaisAfastamento->setDado('cod_rais',$this->getCodRais());
                    $obErro = $obTPessoalAssentamentoRaisAfastamento->inclusao($boTransacao);
                }
            }
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 3 ) {
                if (!$obErro->ocorreu()) {
                    foreach ($this->arRPessoalCausaRescisao as $obRPessoalCausaRescisao) {
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('cod_assentamento'  ,$this->getCodAssentamento());
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('cod_causa_rescisao',$obRPessoalCausaRescisao->getCodCausaRescisao());
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('timestamp',$stNow);
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('vigencia'  ,date('d/m/Y'));
                        $obErro = $this->obTPessoalAssentamentoCausaRescisao->inclusao($boTransacao);
                    }
                }
            }
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 4 ) {
                if (!$obErro->ocorreu()) {
                    $this->obRPessoalVantagem->setCodAssentamento   ( $this->getCodAssentamento()   );
                    $this->obRPessoalVantagem->setTimestamp         ( $stNow                        );
                    $obErro = $this->obRPessoalVantagem->incluirVantagem();
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamento );

    return $obErro;
}

/**
    * Altera dados de assentamento no banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarAssentamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    #Consulta eventos automáticos do assentamento para possível exclusão
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " AND PAE.cod_assentamento = ".$this->getCodAssentamento();
        $obErro = $this->obTPessoalAssentamentoEvento->recuperaRelacionamento($rsEventosAutomativosExcluir,$stFiltro,"",$boTransacao);
    }
    #Consulta eventos automáticos do assentamento para possível exclusão

    #Consulta eventos automáticos porporcionais do assentamento para possível exclusão
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " AND assentamento_evento_proporcional.cod_assentamento = ".$this->getCodAssentamento();
        $obErro = $this->obTPessoalAssentamentoEventoProporcional->recuperaRelacionamento($rsEventosAutomativosProporcionaisExcluir,$stFiltro,"",$boTransacao);
    }
    #Consulta eventos automáticos do assentamento para possível exclusão
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_assentamento",$this->getCodAssentamento());
        $this->obTPessoalAssentamentoAssentamento->consultar($boTransacao);
        $this->obTPessoalAssentamentoAssentamento->setDado("abreviacao",$this->getAbreviacao());
        $this->obTPessoalAssentamentoAssentamento->setDado("cod_operador",$this->getCodOperador()                         );
        $this->obTPessoalAssentamentoAssentamento->alteracao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamento->setDado("grade_efetividade",     $this->getGradeEfetividade()                    );
        $this->obTPessoalAssentamento->setDado("rel_funcao_gratificada",$this->getRelFuncaoGratificada()                );
        $this->obTPessoalAssentamento->setDado("evento_automatico",     $this->getEventoAutomatico()                    );
        $this->obTPessoalAssentamento->setDado("assentamento_automatico",$this->getAssentamentoAutomatico()             );
        $this->obTPessoalAssentamento->setDado("cod_esfera",            $this->obRPessoalEsferaOrigem->getCodEsfera()   );
        $this->obTPessoalAssentamento->setDado("assentamento_inicio",   $this->getAssentamentoInicio()                  );
        $this->obTPessoalAssentamento->recuperaNow3($stNow, $boTransacao);
        $this->obTPessoalAssentamento->setDado("timestamp",$stNow);
        $this->obTPessoalAssentamento->setDado("cod_assentamento", $this->getCodAssentamento() );
        $this->obTPessoalAssentamento->setDado("quant_dias_onus_empregador",            $this->obRPessoalAssentamentoMotivo->getQuantDiasOnusEmpregador());
        $this->obTPessoalAssentamento->setDado("quant_dias_licenca_premio",         $this->obRPessoalAssentamentoMotivo->getQuantDiasLicencaPremio());
        $obErro = $this->obTPessoalAssentamento->inclusao( $boTransacao );
        //Alterações na condição
        if (!$obErro->ocorreu()) {
            $this->obTPessoalAssentamentoValidade->setDado('cod_assentamento',      $this->getCodAssentamento() );
            $this->obTPessoalAssentamentoValidade->setDado('dt_inicial',            $this->getDataInicial()     );
            $this->obTPessoalAssentamentoValidade->setDado('dt_final',              $this->getDataFinal()       );
            $this->obTPessoalAssentamentoValidade->setDado('cancelar_direito',      $this->getCancelarDireito() );
            $this->obTPessoalAssentamentoValidade->setDado("timestamp",             $stNow                      );
            $obErro = $this->obTPessoalAssentamentoValidade->inclusao($boTransacao);
        }
        if (!$obErro->ocorreu() ) {
            if ( count($this->arRFolhaPagamentoEvento) ) {
                foreach ($this->arRFolhaPagamentoEvento as $obRFolhaPagamentoEvento) {
                    $this->obTPessoalAssentamentoEvento->setDado('cod_assentamento',    $this->getCodAssentamento()                 );
                    $this->obTPessoalAssentamentoEvento->setDado('cod_evento',          $obRFolhaPagamentoEvento->getCodEvento()    );
                    $this->obTPessoalAssentamentoEvento->setDado('timestamp',           $stNow                                      );
                    $this->obTPessoalAssentamentoEvento->setDado('vigencia',            date('d/m/Y')                               );
                    $obErro = $this->obTPessoalAssentamentoEvento->inclusao($boTransacao);
                }
            } else {
                #Processo para exclusão dos eventos automáticos
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoParcela.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao();
                $obTFolhaPagamentoUltimoRegistroEvento  = new TFolhaPagamentoUltimoRegistroEvento();
                $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela();
                $obTFolhaPagamentoEventoCalculado       = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoLogErroCalculo        = new TFolhaPagamentoLogErroCalculo();

                $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);
                if (!$obErro->ocorreu()) {
                    while (!$rsEventosAutomativosExcluir->eof()) {
                        $stFiltro  = " AND ultimo_registro_evento.cod_evento = ".$rsEventosAutomativosExcluir->getCampo("cod_evento");
                        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $obErro = $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRegistroEvento,$stFiltro,"",$boTransacao);
                        if (!$obErro->ocorreu()) {
                            while (!$rsRegistroEvento->eof()) {
                                $obTFolhaPagamentoLogErroCalculo->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoEventoCalculado->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));

                                $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                                }
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
                                }
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                                }
                                $rsRegistroEvento->proximo();
                            }
                        } else {
                            break;
                        }
                        $rsEventosAutomativosExcluir->proximo();
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            if (count($this->arRFolhaPagamentoEventoProporcional)) {
                foreach ($this->arRFolhaPagamentoEventoProporcional as $obRFolhaPagamentoEvento) {
                    $this->obTPessoalAssentamentoEventoProporcional->setDado('cod_assentamento',    $this->getCodAssentamento()                 );
                    $this->obTPessoalAssentamentoEventoProporcional->setDado('cod_evento',          $obRFolhaPagamentoEvento->getCodEvento()    );
                    $this->obTPessoalAssentamentoEventoProporcional->setDado('timestamp',           $stNow                                      );
                    $obErro = $this->obTPessoalAssentamentoEventoProporcional->inclusao($boTransacao);
                }
            } else {
                #Processo para exclusão dos eventos automáticos proporcionais
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoUltimoRegistroEvento.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoRegistroEventoParcela.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
                include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoLogErroCalculo.class.php");
                $obTFolhaPagamentoPeriodoMovimentacao   = new TFolhaPagamentoPeriodoMovimentacao();
                $obTFolhaPagamentoUltimoRegistroEvento  = new TFolhaPagamentoUltimoRegistroEvento();
                $obTFolhaPagamentoRegistroEventoParcela = new TFolhaPagamentoRegistroEventoParcela();
                $obTFolhaPagamentoEventoCalculado       = new TFolhaPagamentoEventoCalculado();
                $obTFolhaPagamentoLogErroCalculo        = new TFolhaPagamentoLogErroCalculo();

                $obErro = $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao,"","",$boTransacao);
                if (!$obErro->ocorreu()) {
                    while (!$rsEventosAutomativosProporcionaisExcluir->eof()) {
                        $stFiltro  = " AND ultimo_registro_evento.cod_evento = ".$rsEventosAutomativosProporcionaisExcluir->getCampo("cod_evento");
                        $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                        $stFiltro .= " AND proporcional is true";
                        $obErro = $obTFolhaPagamentoUltimoRegistroEvento->recuperaRelacionamento($rsRegistroEvento,$stFiltro,"",$boTransacao);
                        if (!$obErro->ocorreu()) {
                            while (!$rsRegistroEvento->eof()) {
                                $obTFolhaPagamentoLogErroCalculo->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoEventoCalculado->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoRegistroEventoParcela->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));
                                $obTFolhaPagamentoUltimoRegistroEvento->setDado("cod_registro",$rsRegistroEvento->getCampo("cod_registro"));

                                $obErro = $obTFolhaPagamentoLogErroCalculo->exclusao($boTransacao);
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoEventoCalculado->exclusao($boTransacao);
                                }
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoRegistroEventoParcela->exclusao($boTransacao);
                                }
                                if (!$obErro->ocorreu()) {
                                    $obErro = $obTFolhaPagamentoUltimoRegistroEvento->exclusao($boTransacao);
                                }
                                $rsRegistroEvento->proximo();
                            }
                        } else {
                            break;
                        }
                        $rsEventosAutomativosProporcionaisExcluir->proximo();
                    }
                }
            }
        }
        if (!$obErro->ocorreu() and is_array($this->arRPessoalSubDivisao)) {
            foreach ($this->arRPessoalSubDivisao as $obRPessoalSubDivisao) {
                $this->obTPessoalAssentamentoSubDivisao->setDado('cod_assentamento'  ,$this->getCodAssentamento());
                $this->obTPessoalAssentamentoSubDivisao->setDado('cod_sub_divisao'   ,$obRPessoalSubDivisao->getCodSubDivisao());
                $this->obTPessoalAssentamentoSubDivisao->setDado('timestamp',$stNow);
                $this->obTPessoalAssentamentoSubDivisao->setDado('vigencia'  ,date('d/m/Y'));
                $obErro = $this->obTPessoalAssentamentoSubDivisao->inclusao($boTransacao);
            }
        }
        // dados do afastamento
        if ( !$obErro->ocorreu()) {
            //dados do afastamento temporario
            $this->obTPessoalAssentamentoAfastamentoTemporario->setDado("cod_assentamento", $this->getCodAssentamento());
            $this->obTPessoalAssentamentoAfastamentoTemporario->setDado("timestamp", $stNow);
            $obErro = $this->obTPessoalAssentamentoAfastamentoTemporario->inclusao($boTransacao);

            //dados do afastamento temporario mov sefip saida
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 2 ) {
                if ( !$obErro->ocorreu()) {
                    if ($this->obRPessoalSefip->getCodSefip()) {
                        $this->obTPessoalAssentamentoMovSefipSaida->setDado("cod_assentamento", $this->getCodAssentamento());
                        $this->obTPessoalAssentamentoMovSefipSaida->setDado('timestamp',$stNow);
                        $this->obTPessoalAssentamentoMovSefipSaida->setDado('cod_sefip_saida',$this->obRPessoalSefip->getCodSefip());
                        $obErro = $this->obTPessoalAssentamentoMovSefipSaida->inclusao($boTransacao);
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obRPessoalAssentamentoFaixaDesconto->setCodAssentamento  ( $this->getCodAssentamento()   );
                        $this->obRPessoalAssentamentoFaixaDesconto->setFaixa            ( $this->getFaixa()             );
                        $obErro = $this->obRPessoalAssentamentoFaixaDesconto->salvarFaixas( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("cod_assentamento"   ,$this->getCodAssentamento());
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("timestamp"          ,$stNow);
                        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado("dia"                ,$this->getDiasAfastamento());
                        $obErro = $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->inclusao($boTransacao);
                    }
                }
                if ( !$obErro->ocorreu() and $this->getCodRais() != "") {
                    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoRaisAfastamento.class.php");
                    $obTPessoalAssentamentoRaisAfastamento = new TPessoalAssentamentoRaisAfastamento();
                    $obTPessoalAssentamentoRaisAfastamento->setDado("cod_assentamento", $this->getCodAssentamento());
                    $obTPessoalAssentamentoRaisAfastamento->setDado('timestamp',$stNow);
                    $obTPessoalAssentamentoRaisAfastamento->setDado('cod_rais',$this->getCodRais());
                    $obErro = $obTPessoalAssentamentoRaisAfastamento->inclusao($boTransacao);
                }
            }
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 3 ) {
                if (!$obErro->ocorreu()) {
                    foreach ($this->arRPessoalCausaRescisao as $obRPessoalCausaRescisao) {
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('cod_assentamento'  ,$this->getCodAssentamento());
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('cod_causa_rescisao',$obRPessoalCausaRescisao->getCodCausaRescisao());
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('timestamp',$stNow);
                        $this->obTPessoalAssentamentoCausaRescisao->setDado('vigencia'  ,date('d/m/Y'));
                        $obErro = $this->obTPessoalAssentamentoCausaRescisao->inclusao($boTransacao);
                    }
                }
            }
            if ( $this->obRPessoalClassificacaoAssentamento->getCodTipo() == 4 ) {
                if (!$obErro->ocorreu()) {
                    $this->obRPessoalVantagem->setCodAssentamento   ( $this->getCodAssentamento()   );
                    $this->obRPessoalVantagem->setTimestamp         ( $stNow                        );
                    $obErro = $this->obRPessoalVantagem->incluirVantagem();
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamento );

    return $obErro;
}

/**
    * Exclui dados de assentamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirAssentamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoGerado.class.php");
        $obTPessoalAssentamentoGerado = new TPessoalAssentamentoGerado();
        $stFiltro = " WHERE cod_assentamento = ".$this->getCodAssentamento();
        $obTPessoalAssentamentoGerado->recuperaTodos($rsAssentamentoGerado,$stFiltro,"",$boTransacao);
        if ( !$obErro->ocorreu() and $rsAssentamentoGerado->getNumLinhas() > 0 ) {
            $obErro->setDescricao("O assentamento ".$this->getDescricao()." está sendo utilizado por uma ou mais matrícula(s) através da ação gerar assentamento, por isso não pode ser excluído.");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarAssentamento( $rsRecordSet,"",$boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $this->setDescricao( $rsRecordSet->getCampo('descricao') );
        $this->obRPessoalVantagem->setCodAssentamento   ( $this->getCodAssentamento()   );
        $obErro = $this->obRPessoalVantagem->excluirVantagem($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoCausaRescisao->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoCausaRescisao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoAfastamentoTemporarioDuracao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obRPessoalAssentamentoFaixaDesconto->setCodAssentamento($this->getCodAssentamento());
        $obErro = $this->obRPessoalAssentamentoFaixaDesconto->excluirAssentamentoFaixaDesconto($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoMovSefipSaida->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoMovSefipSaida->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoRaisAfastamento.class.php");
        $obTPessoalAssentamentoRaisAfastamento = new TPessoalAssentamentoRaisAfastamento();
        $obTPessoalAssentamentoRaisAfastamento->setDado("cod_assentamento", $this->getCodAssentamento());
        $obErro = $obTPessoalAssentamentoRaisAfastamento->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoAfastamentoTemporario->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoAfastamentoTemporario->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoSubDivisao->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoSubDivisao->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoEvento->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoEvento->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoEventoProporcional->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoEventoProporcional->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoValidade->setDado('cod_assentamento',$this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoValidade->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamento->setDado("cod_assentamento", $this->getCodAssentamento() );
        $obErro = $this->obTPessoalAssentamento->exclusao( $boTransacao );
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalAssentamentoAssentamento->setDado('cod_assentamento', $this->getCodAssentamento());
        $obErro = $this->obTPessoalAssentamentoAssentamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalAssentamento );

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
function listarAssentamento(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    if( $this->getCodAssentamento() )
        $stFiltro .= " AND A.cod_assentamento = ".$this->getCodAssentamento()." ";
    if( $this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND cod_classificacao = ".$this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento()." ";
    if( $this->getSigla() )
        $stFiltro .= " AND paa.sigla = '".$this->getSigla()."' ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY paa.descricao ";
    $obErro = $this->obTPessoalAssentamento->recuperaAssentamentos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarAssentamentoPorContrato(&$rsRecordSet, $inCod = "", $stOrder="", $comboType = "", $boTransacao = "")
{
    if( $this->getCodAssentamento() )
        $stFiltro .= " AND A.cod_assentamento = ".$this->getCodAssentamento()." ";
    if( $this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND cod_classificacao = ".$this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento()." ";
    if( $this->getSigla() )
        $stFiltro .= " AND paa.sigla = '".$this->getSigla()."' ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY paa.descricao ";
    switch ($comboType) {
        case 'contrato':
        case 'cgm':
            if ($inCod) {
                $stFiltro .= " AND registro = ".$inCod." ";
            }
            break;
        case 'cargo_exercido':
        case 'cargo':
            if ($inCod) {
                $stFiltro .= " AND contrato_servidor.cod_cargo = ".$inCod." ";
            }
            break;
        case 'lotacao':
            if ($inCod) {
                $stFiltro .= " AND contrato_servidor_orgao.cod_orgao = ".$inCod." ";
            }
            break;
    }
    $obErro = $this->obTPessoalAssentamento->recuperaAssentamentosPorContrato( $rsRecordSet, $stFiltro, $stOrder, $comboType, $boTransacao );

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
function listarAssentamentoDisponivel(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    if( $this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND cod_classificacao = ".$this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento()." ";
    $stOrder = ($stOrder)?$stOrder:" ORDER BY paa.descricao ";
    $obErro = $this->obTPessoalAssentamento->recuperaAssentamentoDisponivel( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarAssentamentoNaoVinculado(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    if( $this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento() )
        $stFiltro .= " AND cod_classificacao = ".$this->obRPessoalClassificacaoAssentamento->getCodClassificacaoAssentamento()." ";
    if ( $this->getCodAssentamento() ) {
        $stFiltro .=" AND A.cod_assentamento not in (SELECT cod_assentamento_assentamento from pessoal.assentamento_vinculado where cod_assentamento = ".$this->getCodAssentamento().") AND A.cod_assentamento != ".$this->getCodAssentamento()." \n";
    }
    $stOrder = ($stOrder)?$stOrder:" ORDER BY paa.descricao ";
    $obErro = $this->obTPessoalAssentamento->recuperaAssentamentos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarOperador(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    $stOrder = ($stOrder)?$stOrder:" ORDER BY descricao ";
    $obErro = $this->obTPessoalAssentamentoOperador->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Busca MovSefipSaida do Assentamento
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultaAssentamentoMovSefipSaida(&$rsRecordSet, $stOrder="", $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodAssentamento) {
       $stFiltro .= "and PAS.cod_assentamento =".$this->inCodAssentamento."  ";
    }
    $obErro = $this->obTPessoalAssentamentoMovSefipSaida->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;

}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSubDivisaoSelecionadas(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= " AND pa.cod_assentamento  = '".$this->getCodAssentamento()."' ";
    }
    $obErro = $this->obTPessoalAssentamentoSubDivisao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarSubDivisaoDisponiveis(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= "  and  psd.cod_sub_divisao                                                                                \n";
        $stFiltro .= "        not in(SELECT                                                                               \n";
        $stFiltro .= "                       cod_sub_divisao                                                \n";
        $stFiltro .= "                 FROM                                                                 \n";
        $stFiltro .= "                      pessoal.assentamento_sub_divisao pasd,                          \n";
        $stFiltro .= "                      (SELECT                                                         \n";
        $stFiltro .= "                              MAX(timestamp) as timestamp,                            \n";
        $stFiltro .= "                              cod_assentamento                                        \n";
        $stFiltro .= "                        FROM                                                          \n";
        $stFiltro .= "                              pessoal.assentamento                                    \n";
        $stFiltro .= "                       WHERE  cod_assentamento = '".$this->getCodAssentamento()."'    \n";
        $stFiltro .= "                    GROUP BY  cod_assentamento ) as pa                                \n";
        $stFiltro .= "                WHERE                                                                 \n";
        $stFiltro .= "                       pasd.cod_assentamento = pa.cod_assentamento and                \n";
        $stFiltro .= "                       pasd.timestamp = pa.timestamp)                                 \n";
    }
    $this->addPessoalSubDivisao();
    $obErro = $this->roUltimoPessoalSubDivisao->listarSubDivisao($rsRecordSet,$stFiltro,"",$boTransacao);

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosSelecionados(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= " AND pa.cod_assentamento  = '".$this->getCodAssentamento()."' ";
    }
    $obErro = $this->obTPessoalAssentamentoEvento->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosProporcionaisSelecionados(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= " AND assentamento_evento_proporcional.cod_assentamento  = '".$this->getCodAssentamento()."' ";
    }
    $obErro = $this->obTPessoalAssentamentoEventoProporcional->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosDisponiveis(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= "  and  FPE.cod_evento                                                                                \n";
        $stFiltro .= "        not in(SELECT                                                                               \n";
        $stFiltro .= "                       cod_evento                                                     \n";
        $stFiltro .= "                 FROM                                                                 \n";
        $stFiltro .= "                      pessoal.assentamento_evento pae,                          \n";
        $stFiltro .= "                      (SELECT                                                         \n";
        $stFiltro .= "                              MAX(timestamp) as timestamp,                            \n";
        $stFiltro .= "                              cod_assentamento                                        \n";
        $stFiltro .= "                        FROM                                                          \n";
        $stFiltro .= "                              pessoal.assentamento                                    \n";
        $stFiltro .= "                       WHERE  cod_assentamento = '".$this->getCodAssentamento()."'    \n";
        $stFiltro .= "                    GROUP BY  cod_assentamento ) as pa                                \n";
        $stFiltro .= "                WHERE                                                                 \n";
        $stFiltro .= "                       pae.cod_assentamento = pa.cod_assentamento and                \n";
        $stFiltro .= "                       pae.timestamp = pa.timestamp)                                 \n";
    }
    $stFiltro .= " AND FPE.evento_sistema is false";
    $stFiltro .= " AND FPE.natureza != 'B'";
    $this->addEvento();
    $obErro = $this->roUltimoFolhaPagamentoEvento->listar($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEventosDisponiveisProporcional(&$rsRecordSet, $stFiltro = "",$stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= "  and  FPE.cod_evento                                                                                \n";
        $stFiltro .= "        not in(SELECT                                                                               \n";
        $stFiltro .= "                       cod_evento                                                     \n";
        $stFiltro .= "                 FROM                                                                 \n";
        $stFiltro .= "                      pessoal.assentamento_evento_proporcional pae,                   \n";
        $stFiltro .= "                      (SELECT                                                         \n";
        $stFiltro .= "                              MAX(timestamp) as timestamp,                            \n";
        $stFiltro .= "                              cod_assentamento                                        \n";
        $stFiltro .= "                        FROM                                                          \n";
        $stFiltro .= "                              pessoal.assentamento                                    \n";
        $stFiltro .= "                       WHERE  cod_assentamento = '".$this->getCodAssentamento()."'    \n";
        $stFiltro .= "                    GROUP BY  cod_assentamento ) as pa                                \n";
        $stFiltro .= "                WHERE                                                                 \n";
        $stFiltro .= "                       pae.cod_assentamento = pa.cod_assentamento and                \n";
        $stFiltro .= "                       pae.timestamp = pa.timestamp)                                 \n";
    }
    $stFiltro .= " AND FPE.evento_sistema is false";
    $stFiltro .= " AND FPE.natureza != 'B'";
    $this->addEvento();
    $obErro = $this->roUltimoFolhaPagamentoEvento->listar($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarAssentamentoCausaRescisao(&$rsRecordSet, $stOrder = " descricao ", $boTransacao = "")
{
    if ( $this->getCodAssentamento() ) {
        $stFiltro .= " and pacr.cod_assentamento = ".$this->getCodAssentamento()."";
    }
    $obErro = $this->obTPessoalAssentamentoCausaRescisao->recuperaRelacionamento($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
