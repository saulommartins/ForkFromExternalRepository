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
    * Classe de regra de negócio para Imovel
    * Data de Criação   : 26/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage Regra

    * $Id: RCIMImovel.class.php 65324 2016-05-12 18:18:24Z jean $

    * Casos de uso: uc-05.01.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//MAPEAMENTOS
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php"                    );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMMatriculaImovel.class.php"           );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelCorrespondencia.class.php"     );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelConfrontacao.class.php"        );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelCondominio.class.php"          );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaImovel.class.php"               );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelProcesso.class.php"            );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelImobiliaria.class.php"         );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMImovelLote.class.php"                );
include_once ( CAM_GT_CIM_MAPEAMENTO."FCIMRelatorioCadastroImobiliario.class.php" );
//REGRAS
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImobiliaria.class.php"                    );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMImovel.class.php"                         );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"                   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"                     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLogradouro.class.php"                     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfrontacaoTrecho.class.php"             );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMProprietario.class.php"                   );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                         );
include_once ( CAM_GT_ARR_NEGOCIO."RARRAvaliacaoImobiliaria.class.php"           );

//INCLUSAO DAS CLASSES PARA  O TRATAMNTO DOS ATRIBUTOS
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAtributoImovelValor.class.php"         );

class RCIMImovel
{
/**
    * @access Private
    * @var Integer
*/
var $inNumCGM;
var $inNumeroInscricao;
var $inNumeroInscricaoInicial;
var $inNumeroInscricaoFinal;
/**
    * @access Private
    * @var String
*/
var $stMatriculaRegistroImoveis;
/**
    * @access Private
    * @var String
*/
var $stZona;
/**
    * @access Private
    * @var Date
*/
var $dtDataInscricao;
/**
    * @access Private
    * @var String
*/
var $stEnderecoEntrega;
/**
    * @access Private
    * @var String
*/
var $stComplementoImovel;
/**
    * @access Private
    * @var String
*/
var $stNumeroImovel;
/**
    * @access Private
    * @var String
*/
var $stCepImovel;
/**
    * @access Private
    * @var Date
*/
var $dtDataTermino;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stJustificativaReativar;

/**
    * @access Private
    * @var String
*/
var $stMotivoBaixa;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoSubLote;
/**
    * @access Private
    * @var String
*/
var $stTipoLote;
//ENDERECO DE ENTREGA
/**
    * @access Private
    * @var Integer
*/
var $inCEPEntrega;
/**
    * @access Private
    * @var Integer
*/
var $inNumeroEntrega;
/**
    * @access Private
    * @var Numeric
*/
var $nuAreaImovel;
/**
    * @access Private
    * @var Numeric
*/
var $nuFracaoIdeal;
var $nuAreaEdificada;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoBairroEntrega;
/**
    * @access Private
    * @var String
*/
var $stComplementoEntrega;
/**
    * @access Private
    * @var String
*/
var $stBairroEntrega;
/**
    * @access Private
    * @var String
*/
var $stMunicipioEntrega;
/**
    * @access Private
    * @var String
*/
var $stUFEntrega;
/**
    * @access Private
    * @var String
*/
var $stJustificativa;
/**
    * @access Private
    * @var String
*/
var $stLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obRCIMUnidadeAutonoma;
/**
    * @access Private
    * @var Array
*/
var $arRCIMProprietario;
/**
    * @access Private
    * @var Array
*/
var $arRCIMProprietarioPromitente;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovel;
/**
    * @access Private
    * @var Object
*/
var $obTCIMMatriculaImovel;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovelCorrespondencia;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovelConfrontacao;
/**
    * @access Private
    * @var Object
*/
var $obTCIMBaixaImovel;
/**
    * @access Private
    * @var Object
*/
var $obTCIMImovelLote;
/**
    * @access Private
    * @var Object
*/
var $obTCIMProprietario;
/**
    * @access Private
    * @var Object
*/
var $obTBairroLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obFCIMRelatorioCadastroImobiliario;
/**
    * @access Private
    * @var Object
*/
var $roRCIMLote;
/**
    * @access Private
    * @var Reference Object
*/
var $roUltimoProprietario;
/**
    * @access Private
    * @var Reference Object
*/
var $roUltimoProprietarioPromitente;
/**
    * @access Private
    * @var Object
*/
var $obRCIMConfiguracao;
/**
    * @access Private
    * @var Object
*/
var $obRCIMLogradouro;
/**
    * @access Private
    * @var Object
*/
var $obRCIMBairro;//ENDERECO DE ENTREGA
/**
    * @access Private
    * @var Object
*/
var $obRCIMConfrontacaoTrecho;
/**
    * @access Private
    * @var Object
*/
var $obTransacao;

/**
    * @access Private
    * @var Object
*/
var $obRCadastroDinamico;
/**
    * @access Private
    * @var Timestamp
*/
var $tmTimestampImovel;
/**
    * @access Private
    * @var Timestamp
*/
var $stCaixaPostal;
var $arAtributosDinamicosConsultaImob; //atributos dinamicos utilizados no filtro da consulta imob

// ***************************** SETTERS

function setAtributosDinamicosConsultaImob($valor) { $this->arAtributosDinamicosConsultaImob       = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setNumCGM($valor) { $this->inNumCGM                   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroInscricao($valor) { $this->inNumeroInscricao          = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setMatriculaRegistroImoveis($valor) { $this->stMatriculaRegistroImoveis = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setZona($valor) { $this->stZona                     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroImovel($valor) { $this->stNumeroImovel             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCepImovel($valor) { $this->stCepImovel                = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setComplementoImovel($valor) { $this->stComplementoImovel        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoSubLote($valor) { $this->inCodigoSubLote            = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInscricao($valor) { $this->dtDataInscricao            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setEnderecoEntrega($valor) { $this->stEnderecoEntrega          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMunicipioEntrega($valor) { $this->stMunicipioEntrega         = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setUFEntrega($valor) { $this->stUFEntrega                = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoBairroEntrega($valor) { $this->inCodigoBairroEntrega      = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataTermino($valor) { $this->dtDataTermino              = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa                = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setMotivoBaixa($valor) { $this->stMotivoBaixa              = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCEPEntrega($valor) { $this->inCEPEntrega               = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumeroEntrega($valor) { $this->inNumeroEntrega            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setBairroEntrega($valor) { $this->stBairroEntrega            = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setAreaImovel($valor) { $this->nuAreaImovel               = $valor; }
/**
    * @access Public
    * @param Numeric $valor
*/
function setAreaEdificada($valor) { $this->nuAreaEdificada            = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setComplementoEntrega($valor) { $this->stComplementoEntrega       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setLogradouro($valor) { $this->stLogradouro               = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoLote($valor) { $this->stTipoLote                 = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTimestampImovel($valor) { $this->tmTimestampImovel          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setCaixaPostal($valor) { $this->stCaixaPostal          = $valor; }

//*********************************** GETTERS
/**
    * @access Public
    * @param Date $valor
*/
function getAtributosDinamicosConsultaImob() { return $this->arAtributosDinamicosConsultaImob; }
function getFracaoIdeal() { return $this->nuFracaoIdeal;          }
function getNumCGM() { return $this->inNumeroInscricao;          }
/**
    * @access Public
    * @param Date $valor
*/
function getNumeroInscricao() { return $this->inNumeroInscricao;          }
/**
    * @access Public
    * @param Date $valor
*/
function getMatriculaRegistroImoveis() { return $this->stMatriculaRegistroImoveis; }
/**
    * @access Public
    * @param Date $valor
*/
function getZona() { return $this->stZona; }
/**
    * @access Public
    * @param Date $valor
*/
function getNumeroImovel() { return $this->stNumeroImovel;             }
/**
    * @access Public
    * @param Date $valor
*/
function getCepImovel() { return $this->stCepImovel;                }
/**
    * @access Public
    * @param Date $valor
*/
function getComplementoImovel() { return $this->stComplementoImovel;        }
/**
    * @access Public
    * @param Date $valor
*/
function getCodigoSubLote() { return $this->inCodigoSubLote;            }
/**
    * @access Public
    * @param Date $valor
*/
function getDataInscricao() { return $this->dtDataInscricao;            }
/**
    * @access Public
    * @param Date $valor
*/
function getEnderecoEntrega() { return $this->stEnderecoEntrega;          }
/**
    * @access Public
    * @param String $valor
*/
function getMunicipioEntrega() { return $this->stMunicipioEntrega;         }
/**
    * @access Public
    * @param String $valor
*/
function getUFEntrega() { return $this->stUFEntrega;                }
/**
    * @access Public
    * @param Date $valor
*/
function getCodigoBairroEntrega() { return $this->inCodigoBairroEntrega;      }
/**
    * @access Public
    * @param Date $valor
*/
function getDataTermino() { return $this->dtDataTermino;              }
/**
    * @access Public
    * @param Date $valor
*/
function getDataBaixa() { return $this->dtDataBaixa;                }
/**
    * @access Public
    * @param String $valor
*/
function getMotivoBaixa() { return $this->stMotivoBaixa;              }
/**
    * @access Public
    * @param String $valor
*/
function getCEPEntrega() { return $this->inCEPEntrega;               }
/**
    * @access Public
    * @param Integer $valor
*/
function getNumeroEntrega() { return $this->inNumeroEntrega;            }
/**
    * @access Public
    * @param String $valor
*/
function getBairroEntrega() { return $this->stBairroEntrega;            }
/**
    * @access Public
    * @param Numeric $valor
*/
function getAreaImovel() { return $this->nuAreaImovel;               }
/**
    * @access Public
    * @param Numeric $valor
*/
function getAreaEdificada() { return $this->nuAreaEdificada;            }
/**
    * @access Public
    * @param Date $valor
*/
function getComplementoEntrega() { return $this->stComplementoEntrega;       }

/**
    * @access Public
    * @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
    * @access Public
    * @param String $valor
*/
function getJustificativa() { return $this->stJustificativa;            }
/**
    * @access Public
    * @param String $valor
*/
function getLogradouro() { return $this->stLogradouro;               }
/**
    * @access Public
    * @param Date $valor
*/
function getTipoLote() { return $this->stTipoLote;                 }
/**
    * @access Public
    * @param Timestamp $valor
*/
function getTimestampImovel() { return $this->tmTimestampImovel;          }
/**
    * @access Public
    * @param Timestamp $valor
*/
function getCaixaPostal() { return $this->stCaixaPostal;          }

function RCIMImovel(&$obRCIMLote)
{
    $this->obTransacao                  = new Transacao;
    $this->arRCIMUnidadeAutonoma        = array();
    $this->arRCIMProprietario           = array();
    $this->arRCIMProprietarioPromitente = array();
    $this->obTCIMImovel                 = new TCIMImovel;
    $this->obTCIMMatriculaImovel        = new TCIMMatriculaImovel;
    $this->obTCIMImovelCorrespondencia  = new TCIMImovelCorrespondencia;
    $this->obTCIMImovelConfrontacao     = new TCIMImovelConfrontacao;
    $this->obTCIMImovelLote             = new TCIMImovelLote;
    $this->obTCIMBaixaImovel            = new TCIMBaixaImovel;
    $this->obTCIMImovelProcesso         = new TCIMImovelProcesso;
    $this->obTCIMAtributoImovelValor    = new TCIMAtributoImovelValor;
    $this->obTCIMImovelValor            = new TARRImovelVVenal;
    $this->obTCIMImovelImobiliaria      = new TCIMImovelImobiliaria;
    $this->obTCIMImovelCondominio       = new TCIMImovelCondominio;
    $this->obTBairroLogradouro          = new TBairroLogradouro;
    $this->obFCIMRelatorioCadastroImobiliario = new FCIMRelatorioCadastroImobiliario;
    $this->obRCIMImobiliaria            = new RCIMImobiliaria( $this );
    $this->obRCIMCondominio             = new RCIMCondominio;
    $this->roRCIMLote                   = &$obRCIMLote;
    $this->obRCIMConfrontacaoTrecho     = new RCIMConfrontacaoTrecho( $obRCIMLote );
    $this->obRCIMConfiguracao           = new RCIMConfiguracao;
    $this->obRProcesso                  = new RProcesso;
    $this->obRCIMBairro                 = new RCIMBairro;
    $this->obRCIMLogradouro             = new RCIMLogradouro;
    $this->obRCIMLogradouroEntrega      = new RCIMLogradouro;
    $this->obRCIMConfiguracao->setCodigoModulo ( 12 );
    //AINDA FALTA SETA O EXERCICIO
    $this->obRCadastroDinamico          = new RCadastroDinamico;
    $this->obRCadastroDinamico->setPersistenteValores ( new TCIMAtributoImovelValor );
    $this->obRCadastroDinamico->setCodCadastro( 4 );
}

function incluirImovel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( $obErro->ocorreu() ) { return $obErro;  }
    $obErro = $this->obRCIMConfiguracao->consultarConfiguracao( $boTransacao );
    if ( $obErro->ocorreu() ) { return $obErro; }

    if ( $this->obRCIMConfiguracao->getNumeroIM() == 'false' ) {//MANUAL
        $this->obTCIMImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao );
        $obErro = $this->validaInscricao( $boTransacao );
    } else {
        $this->obTCIMImovel->proximoCod( $this->inNumeroInscricao, $boTransacao );
        $this->obTCIMImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao );
    }
    if ( $obErro->ocorreu()) { return $obErro; }
    $obErro = $this->recuperaProximoSubLote( $boTransacao );
    if ( $obErro->ocorreu() ) { return $obErro; }

    $this->obTCIMImovel->setDado( "cod_sublote", $this->inCodigoSubLote     );
    $this->obTCIMImovel->setDado( "dt_cadastro", $this->dtDataInscricao     );
    $this->obTCIMImovel->setDado( "complemento", $this->stComplementoImovel );
    $this->obTCIMImovel->setDado( "numero",      $this->stNumeroImovel      );
    $this->obTCIMImovel->setDado( "cep",         $this->stCepImovel         );
    $obErro = $this->obTCIMImovel->inclusao( $boTransacao );
    if ( $obErro->ocorreu() ) {      return $obErro;   }

    $this->obTCIMMatriculaImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao          );
    $this->obTCIMMatriculaImovel->setDado( "mat_registro_imovel", $this->stMatriculaRegistroImoveis );
    $this->obTCIMMatriculaImovel->setDado( "zona"               , $this->stZona );
    $obErro = $this->obTCIMMatriculaImovel->inclusao( $boTransacao );
                /**
                *Alterado em 11/04/2005 por Lucas Stephanou(domluc)
                * Objetivo: Adequar a regra a nova tabela imovel_lote
                */
    if ( $obErro->ocorreu() ) { return $obErro; }

    $this->obTCIMImovelLote->setDado ( "inscricao_municipal" , $this->inNumeroInscricao              );
    $this->obTCIMImovelLote->setDado ( "cod_lote"            , $this->roRCIMLote->getCodigoLote()    );
    $this->obTCIMImovelLote->inclusao( $boTransacao );
    if ( $obErro->ocorreu() ) { return $obErro; }

    if ( $this->obRProcesso->getCodigoProcesso() ) {
        $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->inNumeroInscricao                );
        $this->obTCIMImovelProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
        $this->obTCIMImovelProcesso->setDado( "ano_exercicio"      , $this->obRProcesso->getExercicio()      );
        $this->obTCIMImovelProcesso->inclusao( $boTransacao );
    }
    if ( $obErro->ocorreu() ) { return $obErro; }

    $obErro = $this->incluirProprietarios( $boTransacao );
    if ( $obErro->ocorreu() ) { return $obErro; }

    $this->obTCIMImovelConfrontacao->setDado( "inscricao_municipal", $this->inNumeroInscricao                                 );
    $this->obTCIMImovelConfrontacao->setDado( "cod_confrontacao"   , $this->obRCIMConfrontacaoTrecho->getCodigoConfrontacao() );
    $this->obTCIMImovelConfrontacao->setDado( "cod_lote"           , $this->roRCIMLote->getCodigoLote()                       );
    $obErro = $this->obTCIMImovelConfrontacao->inclusao( $boTransacao );
    if ( is_object( $this->obRCIMUnidadeAutonoma ) and !$obErro->ocorreu() ) {
                   $obErro = $this->obRCIMUnidadeAutonoma->incluirUnidadeAutonoma( $boTransacao );
    }
    if ( $this->obRCIMLogradouroEntrega->getCodigoLogradouro() and !$obErro->ocorreu() ) {
        $this->obTCIMImovelCorrespondencia->setDado( "inscricao_municipal", $this->inNumeroInscricao                       );
        $this->obTCIMImovelCorrespondencia->setDado( "cod_logradouro"     , $this->obRCIMLogradouroEntrega->getCodigoLogradouro() );
        $obErro = $this->obRCIMLogradouroEntrega->consultarLogradouro( $rs, $boTransacao );
        if ( $obErro->ocorreu() ) { return $obErro; }

        if ($this->inCodigoBairroEntrega) {
            $obErro = $this->verificarBairroLogradouroCorrepondenciaEntrega(  $boTransacao );
        }
        if ( !$obErro->ocorreu() && $this->inCodigoBairroEntrega && $this->inCEPEntrega && $this->inNumeroEntrega) {
             $this->obTCIMImovelCorrespondencia->setDado( "cod_uf"       , $this->obRCIMLogradouroEntrega->getCodigoUF() );
             $this->obTCIMImovelCorrespondencia->setDado( "cod_municipio", $this->obRCIMLogradouroEntrega->getCodigoMunicipio() );
             $this->obTCIMImovelCorrespondencia->setDado( "cod_bairro"   , $this->inCodigoBairroEntrega );
             $this->obTCIMImovelCorrespondencia->setDado( "caixa_postal" , $this->stCaixaPostal);
             $this->obTCIMImovelCorrespondencia->setDado( "cep"          , $this->inCEPEntrega );
             $this->obTCIMImovelCorrespondencia->setDado( "numero"       , $this->inNumeroEntrega );
             $this->obTCIMImovelCorrespondencia->setDado( "complemento"  , $this->stComplementoEntrega );
             $obErro = $this->obTCIMImovelCorrespondencia->inclusao( $boTransacao );
       }
   }

   //CADASTRO DE ATRIBUTOS
   if ( $obErro->ocorreu() ) { return $obErro; }

   //O Restante dos valores vem setado da página de processamento
   $arChavePersistenteValores = array( "inscricao_municipal" => $this->inNumeroInscricao  );
   $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
   $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
   if ( $obErro->ocorreu() ) { return $obErro; }

   if ( $this->obRCIMImobiliaria->getRegistroCreci() ) {
       $this->obTCIMImovelImobiliaria->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
       $this->obTCIMImovelImobiliaria->setDado( "creci"               , $this->obRCIMImobiliaria->getRegistroCreci() );
       $obErro = $this->obTCIMImovelImobiliaria->inclusao( $boTransacao );
   }
   if ( $obErro->ocorreu() ) { return $obErro; }

   if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
       $this->obTCIMImovelCondominio->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
       $this->obTCIMImovelCondominio->setDado( "cod_condominio"      , $this->obRCIMCondominio->getCodigoCondominio() );
       $obErro = $this->obTCIMImovelCondominio->inclusao( $boTransacao );
    }
    if ( $obErro->ocorreu() ) {
        $obErro->setDescricao("Inscrição Imobiliária já cadastrada no sistema!");
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImovel );

    return $obErro;
}

function recuperaProximoSubLote($boTransacao = "")
{
    $stComplentoChave = $this->obTCIMImovel->getComplementoChave();
    $inCodigo         = $this->obTCIMImovel->getCampoCod();
    $this->obTCIMImovel->setCampoCod        ( "cod_sublote" );
    $this->obTCIMImovel->setComplementoChave( "cod_lote"    );
    $obErro = $this->obTCIMImovel->proximoCod( $this->inCodigoSubLote , $boTransacao );
    $this->obTCIMImovel->setCampoCod( $inCodigo );
    $this->obTCIMImovel->setComplementoChave( $stComplentoChave );

    return $obErro;
}

function alterarImovel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTCIMImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao           );
        $this->obTCIMImovel->setDado( "dt_cadastro"        , $this->dtDataInscricao             );
        $this->obTCIMImovel->setDado( "numero"             , $this->stNumeroImovel              );
        $this->obTCIMImovel->setDado( "complemento"        , $this->stComplementoImovel         );
        $this->obTCIMImovel->setDado( "cep"                , $this->stCepImovel                 );
        $this->obTCIMImovel->setDado( "cod_lote"           , $this->roRCIMLote->getCodigoLote() );
        $this->obTCIMImovel->setDado( "cod_sublote"        , $this->inCodigoSubLote             );
        $obErro = $this->obTCIMImovel->alteracao( $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->obTCIMMatriculaImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao          );
            $this->obTCIMMatriculaImovel->setDado( "mat_registro_imovel", $this->stMatriculaRegistroImoveis );
            $this->obTCIMMatriculaImovel->setDado( "zona"               , $this->stZona );
            //essa tabela trabalha com histórico, por isso sempre inclui um novo
            $obErro = $this->obTCIMMatriculaImovel->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                if ( $this->obRProcesso->getCodigoProcesso() ) {
                    $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->inNumeroInscricao                );
                    $this->obTCIMImovelProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
                    $this->obTCIMImovelProcesso->setDado( "ano_exercicio"      , $this->obRProcesso->getExercicio()      );
                    $this->obTCIMImovelProcesso->setDado( "timestamp"          , $this->getTimestampImovel()             );
                    $this->obTCIMImovelProcesso->inclusao( $boTransacao );
                }

                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMImovelConfrontacao->setDado( "inscricao_municipal", $this->inNumeroInscricao                                 );
                    $this->obTCIMImovelConfrontacao->setDado( "cod_confrontacao"   , $this->obRCIMConfrontacaoTrecho->getCodigoConfrontacao() );
                    $this->obTCIMImovelConfrontacao->setDado( "cod_lote"           , $this->roRCIMLote->getCodigoLote()                       );
                    $obErro = $this->obTCIMImovelConfrontacao->alteracao( $boTransacao );

                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarProprietarios( $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            if ( is_object( $this->obRCIMLogradouro ) ) {
                                $stFiltro = " WHERE inscricao_municipal = ".$this->inNumeroInscricao;

                                if ( $this->obRCIMLogradouroEntrega->getCodigoLogradouro() ) {
                                    $obErro = $this->obTCIMImovelCorrespondencia->recuperaTodos( $rsImovelCorrespondencia, $stFiltro, '', $boTransacao );

                                    if ( !$obErro->ocorreu() ) {
                                        $this->obTCIMImovelCorrespondencia->setDado( "inscricao_municipal", $this->inNumeroInscricao                       );
                                        $this->obTCIMImovelCorrespondencia->setDado( "cod_logradouro"     , $this->obRCIMLogradouroEntrega->getCodigoLogradouro() );
                                        $obErro = $this->obRCIMLogradouroEntrega->consultarLogradouro( $rs, $boTransacao );

                                        if ( !$obErro->ocorreu() ) {
                                            if ($this->inCodigoBairroEntrega) {
                                                $obErro = $this->verificarBairroLogradouroCorrepondenciaEntrega( $boTransacao );
                                            }

                                            if ( !$obErro->ocorreu() ) {
                                                $this->obTCIMImovelCorrespondencia->setDado( "cod_uf"       , $this->obRCIMLogradouroEntrega->getCodigoUF()        );
                                                $this->obTCIMImovelCorrespondencia->setDado( "cod_municipio", $this->obRCIMLogradouroEntrega->getCodigoMunicipio() );
                                                $this->obTCIMImovelCorrespondencia->setDado( "cod_bairro"   , $this->inCodigoBairroEntrega                  );
                                                $this->obTCIMImovelCorrespondencia->setDado( "caixa_postal" , $this->stCaixaPostal);
                                                $this->obTCIMImovelCorrespondencia->setDado( "cep"          , $this->inCEPEntrega                           );
                                                $this->obTCIMImovelCorrespondencia->setDado( "numero"       , $this->inNumeroEntrega                        );
                                                $this->obTCIMImovelCorrespondencia->setDado( "complemento"  , $this->stComplementoEntrega                   );
                                                $obErro = $this->obTCIMImovelCorrespondencia->inclusao( $boTransacao );
                                            }
                                        }
                                    }
                                } else {
                                    $this->obTCIMImovelCorrespondencia->setDado( "inscricao_municipal", $this->inNumeroInscricao  );
                                    $obErro = $this->obTCIMImovelCorrespondencia->exclusao( $boTransacao );
                                }
                            }

                            //CADASTRO DE ATRIBUTOS
                            if ( !$obErro->ocorreu() ) {
                                //O Restante dos valores vem setado da página de processamento
                                $arChavePersistenteValores = array( "inscricao_municipal" => $this->inNumeroInscricao  );
                                $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
                                $obErro = $this->obRCadastroDinamico->alterarValores( $boTransacao );

                                if ( !$obErro->ocorreu() ) {
                                    if ( $this->obRCIMImobiliaria->getRegistroCreci() ) {
                                        $stFiltro = " WHERE inscricao_municipal  = ".$this->inNumeroInscricao;
                                        $obErro = $this->obTCIMImovelImobiliaria->recuperaTodos( $rsCorretagem, $stFiltro, '', $boTransacao );

                                        if ( !$obErro->ocorreu() ) {
                                            $this->obTCIMImovelImobiliaria->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
                                            $this->obTCIMImovelImobiliaria->setDado( "creci"               , $this->obRCIMImobiliaria->getRegistroCreci() );

                                            if ( $rsCorretagem->getNumLinhas() > 0 ) {
                                                $obErro = $this->obTCIMImovelImobiliaria->alteracao( $boTransacao );
                                            } else {
                                                $obErro = $this->obTCIMImovelImobiliaria->inclusao( $boTransacao );
                                            }
                                        }
                                    }

                                    if ( !$obErro->ocorreu() ) {
                                        if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
                                            $stFiltro = " WHERE inscricao_municipal = ".$this->inNumeroInscricao;
                                            $obErro = $this->obTCIMImovelCondominio->recuperaTodos( $rsCondominio, $stFiltro, '', $boTransacao );

                                            if ( !$obErro->ocorreu() ) {
                                                $this->obTCIMImovelCondominio->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
                                                $this->obTCIMImovelCondominio->setDado( "cod_condominio"      , $this->obRCIMCondominio->getCodigoCondominio() );

                                                if ( $rsCondominio->getNumLinhas() > 0 ) {
                                                    $obErro = $this->obTCIMImovelCondominio->alteracao( $boTransacao );
                                                } else {
                                                    $obErro = $this->obTCIMImovelCondominio->inclusao( $boTransacao );
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImovel );

    return $obErro;
}

function excluirImovel($boTransacao = "")
{
    $this->obRCIMUnidadeAutonoma        = new RCIMUnidadeAutonoma( $this );
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $this->getNumeroInscricao() );
        $this->obRCIMUnidadeAutonoma->listarUnidadesAutonomas( $rsUnidadeAutonoma,$boTransacao );
        if ( !$rsUnidadeAutonoma->eof() ) {
            $obErro->setDescricao( "Imóvel possui unidade autonoma!" );

            return $obErro;
        } else {
            //CADASTRO DE ATRIBUTOS
            //O Restante dos valores vem setado da página de processamento
            $arChavePersistenteValores = array( "inscricao_municipal" => $this->inNumeroInscricao  );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChavePersistenteValores );
            $obErro = $this->obRCadastroDinamico->excluirValores( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->inNumeroInscricao );
                $obErro = $this->obTCIMImovelProcesso->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMImovelCondominio->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
                    $obErro = $this->obTCIMImovelCondominio->exclusao( $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $this->obTCIMImovelImobiliaria->setDado( "inscricao_municipal" , $this->inNumeroInscricao );
                        $obErro = $this->obTCIMImovelImobiliaria->exclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->obTCIMImovelCorrespondencia->setDado( "inscricao_municipal", $this->inNumeroInscricao  );
                            $obErro = $this->obTCIMImovelCorrespondencia->exclusao( $boTransacao );
                            if ( !$obErro->ocorreu() ) {
                                $this->obTCIMMatriculaImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao );
                                $obErro = $this->obTCIMMatriculaImovel->exclusao( $boTransacao );
                                if ( !$obErro->ocorreu() ) {
                                    $obErro = $this->salvarProprietarios( $boTransacao, true );
                                    if ( !$obErro->ocorreu() ) {
                                    $this->obTCIMImovelConfrontacao->setDado( "inscricao_municipal", $this->inNumeroInscricao );
                                    $obErro = $this->obTCIMImovelConfrontacao->exclusao( $boTransacao );
                                    if ( !$obErro->ocorreu() ) {
                                        $this->obTCIMImovelLote->setDado( "inscricao_municipal", $this->inNumeroInscricao);
                                        $obErro= $this->obTCIMImovelLote->exclusao($boTransacao);
                                        if ( !$obErro->ocorreu() ) {
                                            $obRARRAvaliacaoImobiliaria = new RARRAvaliacaoImobiliaria();
                                            $rsRARRAvaliacaoImobiliaria = new RecordSet();

                                            $obRARRAvaliacaoImobiliaria->obRCIMImovel = &$this;

                                            $obRARRAvaliacaoImobiliaria->listarVenaisImoveisConsulta( $rsRARRAvaliacaoImobiliaria, $boTransacao );
                                            if (!($rsRARRAvaliacaoImobiliaria->eof())) {
                                                $obErro->setDescricao("Imóvel possui cálculos realizados!");

                                                return $obErro;
                                            }

                                            $this->obTCIMImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao );
                                            $obErro = $this->obTCIMImovel->exclusao( $boTransacao );
                                        }
                                    }
                                }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImovel );

    return $obErro;
}

function baixarImovel($boTransacao = "")
{
    $this->obRCIMUnidadeAutonoma        = new RCIMUnidadeAutonoma( $this );
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obRCIMUnidadeAutonoma->roRCIMImovel->setNumeroInscricao( $this->getNumeroInscricao() );
        $this->obRCIMUnidadeAutonoma->listarUnidadesAutonomas( $rsUnidadeAutonoma,$boTransacao );
        if ( !$rsUnidadeAutonoma->eof() ) {
            $obErro->setDescricao( "Imóvel possui unidade autonoma!" );

            return $obErro;
        }

        if ( !$obErro->ocorreu() ) {
            $dtdiaHOJE = date ("d-m-Y");
            $this->obTCIMBaixaImovel->setDado( "dt_inicio", $dtdiaHOJE );
            $this->obTCIMBaixaImovel->setDado( "inscricao_municipal", $this->getNumeroInscricao() );
            $this->obTCIMBaixaImovel->setDado( "justificativa"      , $this->getJustificativa()   );
            $obErro = $this->obTCIMBaixaImovel->inclusao( $boTransacao );

            if ( !$obErro->ocorreu() and  $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
                $this->obTCIMImovelProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
                $this->obTCIMImovelProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCIMImovelProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
                $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->getNumeroInscricao()       );
                $obErro = $this->obTCIMImovelProcesso->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaImovel );

    return $obErro;
}

function reativarImovel($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaImovel->setDado( "dt_termino", $dtdiaHOJE );
        $this->obTCIMBaixaImovel->setDado( "timestamp", $this->getDataBaixa() );
        $this->obTCIMBaixaImovel->setDado( "inscricao_municipal", $this->getNumeroInscricao() );
        $this->obTCIMBaixaImovel->setDado( "justificativa"      , $this->getJustificativa() );
        $this->obTCIMBaixaImovel->setDado( "justificativa_termino", $this->getJustificativaReativar() );
        $obErro = $this->obTCIMBaixaImovel->alteracao( $boTransacao );

        if ( !$obErro->ocorreu() and  $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
            $this->obTCIMImovelProcesso->setDado( "cod_lote"     , $this->inCodigoLote                     );
            $this->obTCIMImovelProcesso->setDado( "cod_processo" , $this->obRProcesso->getCodigoProcesso() );
            $this->obTCIMImovelProcesso->setDado( "ano_exercicio", $this->obRProcesso->getExercicio()      );
            $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->getNumeroInscricao()       );
            $obErro = $this->obTCIMImovelProcesso->inclusao( $boTransacao );
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaImovel );

    return $obErro;
}

/**
    * Altera os valores dos atributos do Lote setado guardando o histórico
    * @access Public
    * @param  Object $obTransacao ParÃ¢metro Transação
    * @return Object Objeto Erro
*/
function alterarCaracteristicas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //CADASTRO DE ATRIBUTOS
        $this->obTCIMImovel->setDado( "inscricao_municipal", $this->inNumeroInscricao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
               $this->obTCIMImovelProcesso->setDado( "inscricao_municipal", $this->inNumeroInscricao                );
               $this->obTCIMImovelProcesso->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
               $this->obTCIMImovelProcesso->setDado( "ano_exercicio"      , $this->obRProcesso->getExercicio()      );
               $this->obTCIMImovelProcesso->inclusao( $boTransacao );
            }
            //O Restante dos valores vem setado da pÃ¡gina de processamento
            $arChaveAtributoImovel = array( "inscricao_municipal" => $this->inNumeroInscricao );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovel );
            $obErro = $this->obRCadastroDinamico->salvarValores( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMImovel );

    return $obErro;
}

function consultarImovel($boTransacao = "" , $boBuscaProprietario = FALSE)
{
    $stFiltro = " WHERE IMOVEL.inscricao_municipal = ".$this->inNumeroInscricao." ";
    $stOrdem = "";
    $obErro = $this->obTCIMImovel->recuperaRelacionamentoConsulta( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    if ( !$obErro->ocorreu() && $rsRecordSet->eof() ) {
        $obErro->setDescricao( "Inscrição Municipal inválida!" );
    }

    if ( !$obErro->ocorreu() ) {
        $flAreaEdificadaLote = $rsRecordSet->getCampo( "area_imovel_lote" );
        $rsRecordSet->addFormatacao( "area_imovel" , "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "area_imovel_lote" , "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "area_imovel_construcao" , "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "area_real", "NUMERIC_BR" );
        $rsRecordSet->addFormatacao( "fracao_ideal", "NUMERIC_BR" );
        $arLocalizacao = explode ( ",", $rsRecordSet->getCampo("localizacao") );
        $arLogradouro  = explode ( ",", $rsRecordSet->getCampo("logradouro")  );

        function tiraChave($valor)
        {
            $arChaves = array( "{" , "}" );
            $valor = str_replace( $arChaves ,"" ,$valor );
            $valor = str_replace( '"' ,"" ,$valor );

            return $valor;
        }
        function trocaPonto($valor)
        {
            return str_replace( "." , "," , $valor);
        }

        $arLogradouro  = array_map( "tiraChave"  ,$arLogradouro  );
        $arLocalizacao = array_map( "tiraChave"  ,$arLocalizacao );

        $this->obRCIMCondominio->setCodigoCondominio( $rsRecordSet->getCampo( "cod_condominio") );
        $this->stMatriculaRegistroImoveis = $rsRecordSet->getCampo( "mat_registro_imovel"       );
        $this->stZona                     = $rsRecordSet->getCampo( "zona"                      );
        $this->dtDataInscricao            = $rsRecordSet->getCampo( "dt_cadastro"               );
        $this->tmTimestampImovel          = $rsRecordSet->getCampo( "timestamp"                 );
        $this->stLogradouro               = $arLogradouro[0]." - ".$arLogradouro[1];
        $this->stNumeroImovel             = $rsRecordSet->getCampo( "numero"                    );
        $this->stComplementoImovel        = $rsRecordSet->getCampo( "complemento"               );
        $this->stJustificativa            = $rsRecordSet->getCampo( "justificativa"             );
        $this->dtDataBaixa                = $rsRecordSet->getCampo( "dt_baixa"                  );
        $this->dtDataTermino              = $rsRecordSet->getCampo( "dt_termino"                );
        $this->nuAreaEdificada            = $rsRecordSet->getCampo( "area_imovel_construcao"    );
        $this->nuAreaImovel               = $rsRecordSet->getCampo( "area_imovel"               );
        $this->nuFracaoIdeal              = $rsRecordSet->getCampo( "fracao_ideal"              );
        $this->roRCIMLote->setAreaEdificadaLote( number_format($flAreaEdificadaLote,2,',','.') ) ;
        $stFiltro = " WHERE inscricao_municipal = ".$this->inNumeroInscricao;
        $obErro = $this->obTCIMImovelConfrontacao->recuperaTodos( $rsConfrontacao , $stFiltro , '', $boTransacao );
        if ( !$obErro->ocorreu() and !$rsConfrontacao->eof() ) {
            $this->obRCIMConfrontacaoTrecho->setCodigoConfrontacao    ( $rsConfrontacao->getCampo("cod_confrontacao") );
            $this->obRCIMConfrontacaoTrecho->roRCIMLote->setCodigoLote( $rsConfrontacao->getCampo("cod_lote"        ) );
            $this->obRCIMConfrontacaoTrecho->consultarConfrontacao();
        }

        $this->obTCIMImovelCorrespondencia->recuperaTodos( $rsCorrespondencia, $stFiltro, 'timestamp desc limit 1', $boTransacao );
        if ( !$obErro->ocorreu() and !$rsCorrespondencia->eof() ) {
            $this->addImovelCorrespondencia();
            $this->obRCIMLogradouroEntrega->setCodigoLogradouro( $rsCorrespondencia->getCampo( "cod_logradouro" ) );
            $obErro = $this->obRCIMLogradouroEntrega->consultarLogradouro( $rs, $boTransacao );
            $this->obRCIMBairro = new RCIMBairro;
            $this->obRCIMBairro->setCodigoBairro    ( $rsCorrespondencia->getCampo( "cod_bairro"    ) );
            $this->obRCIMBairro->setCodigoUF        ( $rsCorrespondencia->getCampo( "cod_uf"        ) );
            $this->obRCIMBairro->setCodigoMunicipio ( $rsCorrespondencia->getCampo( "cod_municipio" ) );

            $this->obRCIMLogradouro->setCodigoUF       ( $this->obRCIMBairro->getCodigoUF() );
            $this->obRCIMLogradouro->setCodigoMunicipio( $this->obRCIMBairro->getCodigoMunicipio() );
            $this->obRCIMLogradouro->listarMunicipios  ( $rsMunicipio, $boTransacao );
            $this->obRCIMLogradouro->listarUF          ( $rsUF, $boTransacao );

            $this->obRCIMBairro->consultarBairro( $boTransacao );
            $this->stEnderecoEntrega     = $rs->getCampo( "nom_tipo" )." ".$rs->getCampo( "nom_logradouro" );
            $this->stBairroEntrega       = $this->obRCIMBairro->getNomeBairro();
            $this->stMunicipioEntrega    = $rsMunicipio->getCampo( "nom_municipio" );
            $this->stUFEntrega           = $rsUF->getCampo( "sigla_uf" );
            $this->inCodigoBairroEntrega = $rsCorrespondencia->getCampo( "cod_bairro"  );
            $this->inCEPEntrega          = $rsCorrespondencia->getCampo( "cep"         );
            $this->inNumeroEntrega       = $rsCorrespondencia->getCampo( "numero"      );
            $this->stComplementoEntrega  = $rsCorrespondencia->getCampo( "complemento" );
            $this->stCaixaPostal         = $rsCorrespondencia->getCampo( "caixa_postal");
        }

        if ($boBuscaProprietario == TRUE) {
            $this->obTCIMProprietario = new TCIMProprietario;
            $stOrdem = " ORDER BY ordem ";
            $obErro = $this->obTCIMProprietario->recuperaTodos( $rsProprietario, $stFiltro, $stOrdem, $boTransacao );
            while ( !$rsProprietario->eof() ) {
                if ( $rsProprietario->getCampo("promitente") == "f" ) {
                $this->addProprietario();
                $this->roUltimoProprietario->setNumeroCGM( $rsProprietario->getCampo("numcgm") );
                $this->roUltimoProprietario->consultarProprietario( $boTransacao );
                } else {
                $this->addProprietarioPromitente();
                $this->roUltimoProprietarioPromitente->setNumeroCGM( $rsProprietario->getCampo("numcgm") );
                $this->roUltimoProprietarioPromitente->consultarProprietario( $boTransacao );
                }
                $rsProprietario->proximo();
            }
        }

        if ( !$obErro->ocorreu() AND $this->obRCIMCondominio->getCodigoCondominio() ) {
            $this->obRCIMCondominio->consultarCondominio( $rsRecordSet, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRCIMCondominio->setNomCondominio( $rsRecordSet->getCampo('nom_condominio'));
            }
        }
    }

    return $obErro;
}

function consultarImovelAlteracao($boTransacao = "")
{
    $stFiltro = " WHERE IMOVEL.inscricao_municipal = ".$this->inNumeroInscricao." ";
    $stOrdem = "";
    $obErro = $this->obTCIMImovel->recuperaRelacionamentoAlteracao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->obRCIMCondominio->setCodigoCondominio( $rsRecordSet->getCampo( "cod_condominio") );
        $this->obRCIMCondominio->setNomCondominio   ( $rsRecordSet->getCampo('nom_condominio')  );
        $this->stMatriculaRegistroImoveis = $rsRecordSet->getCampo( "mat_registro_imovel"       );
        $this->stZona                     = $rsRecordSet->getCampo( "zona"                      );
        $this->dtDataInscricao            = $rsRecordSet->getCampo( "dt_cadastro"               );
        $this->tmTimestampImovel          = $rsRecordSet->getCampo( "timestamp"                 );
        $this->stNumeroImovel             = $rsRecordSet->getCampo( "numero"                    );
        $this->stCepImovel                = $rsRecordSet->getCampo( "cep"                       );
        $this->stComplementoImovel        = $rsRecordSet->getCampo( "complemento"               );
        $this->stJustificativa            = $rsRecordSet->getCampo( "justificativa"             );
        $this->dtDataBaixa                = $rsRecordSet->getCampo( "dt_baixa"                  );
        $this->nuAreaEdificada            = $rsRecordSet->getCampo( "area_imovel_construcao"    );
        $this->nuAreaImovel               = $rsRecordSet->getCampo( "area_imovel"               );
        $this->obRCIMConfrontacaoTrecho->setCodigoConfrontacao( $rsRecordSet->getCampo("cod_confrontacao") );

        $stFiltro = " WHERE inscricao_municipal = ".$this->inNumeroInscricao;

        // RECUPERA INFORMACOES DE CORRESPONDENCIA
        $this->obTCIMImovelCorrespondencia->recuperaTodos( $rsCorrespondencia, $stFiltro, 'timestamp desc limit 1', $boTransacao );
        
        if ( !$obErro->ocorreu() and !$rsCorrespondencia->eof() ) {
            $this->addImovelCorrespondencia();
            $this->obRCIMLogradouroEntrega->setCodigoLogradouro( $rsCorrespondencia->getCampo( "cod_logradouro" ) );
            $obErro = $this->obRCIMLogradouroEntrega->consultarLogradouro( $rs, $boTransacao );
            $this->obRCIMBairro = new RCIMBairro;
            $this->obRCIMBairro->setCodigoBairro    ( $rsCorrespondencia->getCampo( "cod_bairro"    ) );
            $this->obRCIMBairro->setCodigoUF        ( $rsCorrespondencia->getCampo( "cod_uf"        ) );
            $this->obRCIMBairro->setCodigoMunicipio ( $rsCorrespondencia->getCampo( "cod_municipio" ) );
            $this->obRCIMBairro->consultarBairro( $boTransacao );
            $this->inCodigoBairroEntrega = $rsCorrespondencia->getCampo( "cod_bairro"  );
            $this->inCEPEntrega          = $rsCorrespondencia->getCampo( "cep"         );
            $this->inNumeroEntrega       = $rsCorrespondencia->getCampo( "numero"      );
            $this->stComplementoEntrega  = $rsCorrespondencia->getCampo( "complemento" );
            $this->stCaixaPostal         = $rsCorrespondencia->getCampo( "caixa_postal");
        }

        // MONTA LISTA DE PROPRIETARIOS
        $this->obTCIMProprietario = new TCIMProprietario;
        $stOrdem = " ORDER BY ordem ";
        $obErro = $this->obTCIMProprietario->recuperaTodos( $rsProprietario, $stFiltro, $stOrdem, $boTransacao );
        while ( !$rsProprietario->eof() ) {
            if ( $rsProprietario->getCampo("promitente") == "f" ) {
               $this->addProprietario();
               $this->roUltimoProprietario->setNumeroCGM( $rsProprietario->getCampo("numcgm") );
               $this->roUltimoProprietario->consultarProprietario( $boTransacao );
            } else {
               $this->addProprietarioPromitente();
               $this->roUltimoProprietarioPromitente->setNumeroCGM( $rsProprietario->getCampo("numcgm") );
               $this->roUltimoProprietarioPromitente->consultarProprietario( $boTransacao );
            }
            $rsProprietario->proximo();
        }
    }

    return $obErro;
}

function verificarBairroLogradouroCorrepondencia($boTransacao = "")
{
    $stFiltro  = " AND B.cod_bairro      = ".$this->inCodigoBairroEntrega." ";
    $stFiltro .= " AND B.cod_uf          = ".$this->obRCIMLogradouro->getCodigoUF()." ";
    $stFiltro .= " AND B.cod_municipio   = ".$this->obRCIMLogradouro->getCodigoMunicipio()." ";
    $stFiltro .= " AND BL.cod_logradouro = ".$this->obRCIMLogradouro->getCodigoLogradouro()." ";
    $obErro = $this->obTBairroLogradouro->recuperaRelacionamento( $rsBairroLogradouro, $stFiltro, "",  $boTransacao );
    if ( !$obErro->ocorreu() and $rsBairroLogradouro->eof() ) {
        $obErro->setDescricao( "Endereço de entregra inválido! Logradouro não pertence ao bairro informado!" );
    }

    return $obErro;
}

function verificarBairroLogradouroCorrepondenciaEntrega($boTransacao = "")
{
    $stFiltro  = " AND B.cod_bairro      = ".$this->inCodigoBairroEntrega." ";
    $stFiltro .= " AND B.cod_uf          = ".$this->obRCIMLogradouroEntrega->getCodigoUF()." ";
    $stFiltro .= " AND B.cod_municipio   = ".$this->obRCIMLogradouroEntrega->getCodigoMunicipio()." ";
    $stFiltro .= " AND BL.cod_logradouro = ".$this->obRCIMLogradouroEntrega->getCodigoLogradouro()." ";
    $obErro = $this->obTBairroLogradouro->recuperaRelacionamento( $rsBairroLogradouro, $stFiltro, "",  $boTransacao );
    if ( !$obErro->ocorreu() and $rsBairroLogradouro->eof() ) {
        $obErro->setDescricao( "Endereço de entregra inválido! Logradouro não pertence ao bairro informado!" );
    }

    return $obErro;
}

function listarImoveis(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " LPAD( UPPER( IMOVEL.VALOR ), 10,'0' ) = LPAD( UPPER('".$this->roRCIMLote->getNumeroLote()."'), 10,'0' ) AND ";
    }
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " IMOVEL.COD_LOCALIZACAO = ".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
    }
    if ($this->inNumeroInscricao) {
        $stFiltro .= " IMOVEL.inscricao_municipal = ".$this->inNumeroInscricao." AND ";
    }
    if ($this->stTipoLote) {
        $stFiltro .= " IMOVEL.TIPO_LOTE = '".$this->stTipoLote."' AND ";
    }
    if ( $this->obRCIMBairro->getCodigoBairro() ) {
        $stFiltro .= " IMOVEL.COD_BAIRRO = '".$this->obRCIMBairro->getCodigoBairro()."' AND ";
    }
    if ( $this->getNumeroImovel() ) {
        $stFiltro .= " IMOVEL.NUMERO = '".$this->getNumeroImovel()."' AND ";
    }
    if ( $this->getLogradouro() ) {
        $stFiltro .= " UPPER( IMOVEL.NOM_LOGRADOURO ) like UPPER( '".$this->getLogradouro()."%' ) AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY IMOVEL.inscricao_municipal ";
    $obErro = $this->obTCIMImovel->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisAtivos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inNumeroInscricao) {
        $stFiltro .= " AND I.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " AND I.cod_sublote = ".$this->roRCIMLote->getNumeroLote();
    }
    $stOrdem = " ORDER BY I.inscricao_municipal ";
    $obErro = $this->obTCIMImovel->recuperaImoveisAtivos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisAtivosCgm(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inNumeroInscricao) {
        $stFiltro .= " AND I.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " AND I.cod_sublote = ".$this->roRCIMLote->getNumeroLote();
    }
    $stOrdem = " ";
    $obErro = $this->obTCIMImovel->recuperaImoveisAtivosCgm( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisAtivosLote(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " AND IL.cod_lote = ".$this->roRCIMLote->getNumeroLote();
    }
    $stOrdem = " ORDER BY IL.inscricao_municipal ";
    $obErro = $this->obTCIMImovel->recuperaImoveisAtivosLote( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisLista(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " LPAD( UPPER( IMOVEL.VALOR ), 10,'0' ) = LPAD( UPPER('".$this->roRCIMLote->getNumeroLote()."'), 10,'0' ) AND ";
    }
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " IMOVEL.COD_LOCALIZACAO = ".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
    }
    if ($this->inNumeroInscricao) {
        $stFiltro .= " IMOVEL.inscricao_municipal = ".$this->inNumeroInscricao." AND ";
    }
    if ($this->stTipoLote) {
        $stFiltro .= " IMOVEL.TIPO_LOTE = '".$this->stTipoLote."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY IMOVEL.inscricao_municipal ";
    $obErro = $this->obTCIMImovel->recuperaRelacionamentoListaAlteracao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisMovimentacoes(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " AND LT.COD_LOCALIZACAO = ".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao();
    }
    if ($this->inNumeroInscricao) {
        $stFiltro .= " AND IM.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    if ( is_object($this->roUltimoProprietario) && $this->roUltimoProprietario->getNumeroCGM() ) {
        $stFiltro .= " AND CG.numcgm = ".$this->roUltimoProprietario->getNumeroCGM();
    }
    $stOrdem = "  GROUP BY IM.INSCRICAO_MUNICIPAL, ilr.cod_lote \n";
    $stOrdem .= " ORDER BY IM.INSCRICAO_MUNICIPAL, ilr.cod_lote ";

    $obErro = $this->obTCIMImovel->recuperaRelacionamentoMovimentacoes( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarImoveisConsulta(&$rsRecordSet, $boTransacao = "", $stOrder = " inscricao_municipal ")
{
    $stFiltro = "";
    $stFiltroLote = empty($stFiltroLote) ? "" : $stFiltroLote;
    $stFiltroImovel = empty($stFiltroImovel) ? "" : $stFiltroImovel;
    if ($this->inNumeroInscricao) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    if ($this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal =='') {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricaoInicial;
    } elseif (!$this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricaoFinal;
    } elseif ($this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal) {
        $stFiltroLote .= " AND I.inscricao_municipal BETWEEN ".$this->inNumeroInscricaoInicial." AND ".$this->inNumeroInscricaoFinal;
    }
    if ( $this->roRCIMLote->getNumeroLote() ) {
        $stFiltroLote .= " AND ltrim(LL.valor,''0'') = ''".ltrim($this->roRCIMLote->getNumeroLote(),'0')."'' ";
    }
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto like ''".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()."%''";
    }
    if ($this->roUltimoProprietario) {
        if ( $this->roUltimoProprietario->getNumeroCGM() ) {
            $stFiltroLote .= " AND C.numcgm = ".$this->roUltimoProprietario->getNumeroCGM();
        }
        if ($this->roUltimoProprietario->inNumeroCGMInicial && $this->roUltimoProprietario->inNumeroCGMFinal) {
            $stFiltroLote .= " AND C.numcgm BETWEEN  ".$this->roUltimoProprietario->inNumeroCGMInicial." AND ".$this->roUltimoProprietario->inNumeroCGMFinal;
        }
        if ( $this->roUltimoProprietario->obRCGM->getNomCGM() ) {
            $stFiltroLote .= " AND UPPER( C.nom_cgm ) like UPPER( ''".$this->roUltimoProprietario->obRCGM->getNomCGM()."%'' )";
        }
    }

    if ( $this->getNumeroImovel() ) {
        $novoNumero = ltrim ( $this->getNumeroImovel() , '0');
        $stFiltroLote .= " AND ltrim ( I.numero, ''0'' ) = ''". $novoNumero ."''";
    }

    if ( $this->getComplementoImovel() ) {
        $stFiltroLote .= " AND UPPER( I.complemento ) like UPPER( ''".$this->getComplementoImovel()."%'' )";
    }
    if ( $this->obRCIMLogradouro->getCodigoLogradouro() ) {
        $stFiltroImovel .= " AND LO.cod_logradouro = ".$this->obRCIMLogradouro->getCodigoLogradouro();
    }
    if ( $this->obRCIMBairro->getCodigoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro = ".$this->obRCIMBairro->getCodigoBairro();
    }
    if ( $this->obRCIMImobiliaria->getRegistroCreci() ) {
        $stFiltroImovel .= " AND II.creci = ".$this->obRCIMImobiliaria->getRegistroCreci();
    }
    if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
        $stFiltroImovel .= " AND ICO.cod_condominio = ".$this->obRCIMCondominio->getCodigoCondominio();
    }
    if ( $this->getLogradouro() ) {
        $stFiltro = " WHERE UPPER( logradouro ) like UPPER ('%".$this->getLogradouro()."%')";
    }

    $stFiltroAtrbImovel = "";
    $stFiltroAtrbEdf = "";
    $stFiltroAtrbLote = "";
    $inTotalEdf = 0;
    $inTotalLote = 0;
    $inTotalImovel = 0;
    $boLoteUrbano = true;

    if ($this->arAtributosDinamicosConsultaImob) {
        for ( $inX=0; $inX<count( $this->arAtributosDinamicosConsultaImob ); $inX++ ) {
            switch ($this->arAtributosDinamicosConsultaImob[$inX]["cod_cadastro"]) {
                case 5: //edificacao
                    $stFiltroAtrbEdf .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."'' ) OR ";
                    $stFiltroImovel .= "
                        AND atev.inscricao_municipal = I.inscricao_municipal
                    ";
                    $inTotalEdf++;
                    break;

                case 4: //imovel
                    $stFiltroAtrbImovel .= " ( atributo_imovel_valor.cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND atributo_imovel_valor.valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."' ) OR ";
                    $stFiltroImovel .= "
                        AND aiv.inscricao_municipal = I.inscricao_municipal
                    ";

                    $inTotalImovel++;
                    break;

                case 3: //lote rural
                    $boLoteUrbano = false;
                    $stFiltroAtrbLote .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."'' ) OR ";
                    $inTotalLote++;
                    break;

                case 2: //lote urbano
                    $stFiltroAtrbLote .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."'' ) OR ";
                    $inTotalLote++;
                    break;
            }
        }

        if ($stFiltroAtrbEdf) {
            $stFiltroAtrbEdf = "
                WHERE
                    cod_cadastro = 5
                    AND cod_modulo = 12
                    AND ( ".substr( $stFiltroAtrbEdf, 0, strlen( $stFiltroAtrbEdf ) - 4 )." )

                GROUP BY
                    cod_construcao,
                    cod_tipo

                HAVING COUNT (cod_construcao) >= ".$inTotalEdf;
        } else {
            $stFiltroAtrbEdf = "
                GROUP BY
                    cod_construcao,
                    cod_tipo
            ";
        }

        if ($stFiltroAtrbImovel) {
            $stFiltroAtrbImovel = "
                WHERE
                    cod_cadastro = 4
                    AND cod_modulo = 12
                    AND ( ".substr( $stFiltroAtrbImovel, 0, strlen( $stFiltroAtrbImovel ) - 4 )." )

                GROUP BY
                    inscricao_municipal

                HAVING COUNT (inscricao_municipal) >= ".$inTotalImovel;
        } else {
            $stFiltroAtrbImovel = "
                GROUP BY
                    inscricao_municipal
            ";
        }

        if ($stFiltroAtrbLote) {
            $stFiltroAtrbLote = "
                WHERE
                    cod_modulo = 12
                    AND ( ".substr( $stFiltroAtrbLote, 0, strlen( $stFiltroAtrbLote ) - 4 )." )
            ";

            $stFiltroAtrbLote .= "
                    AND cod_cadastro = ".($boLoteUrbano?2:3)."

                GROUP BY
                    cod_lote

                HAVING COUNT (cod_lote) >= ".$inTotalLote;
        } else {
            $stFiltroAtrbLote .= "
                GROUP BY
                    cod_lote
            ";
        }
    } else {
        $stFiltroAtrbImovel = "
            GROUP BY
                inscricao_municipal
        ";

        $stFiltroAtrbLote .= "
            GROUP BY
                cod_lote
        ";

        $stFiltroAtrbEdf = "
            GROUP BY
                cod_construcao,
                cod_tipo
        ";
    }

    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stDistinct"     , 'TRUE'              );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroLote"   , $stFiltroLote       );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroImovel" , $stFiltroImovel     );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbImovel" , $stFiltroAtrbImovel );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbLote" , $stFiltroAtrbLote );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbEdf" , $stFiltroAtrbEdf );
    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
    
    return $obErro;
}

function listarConsultaCadastroImobilario(&$rsRecordSet, $boTransacao = "", $stOrder = " inscricao_municipal ")
{
    $stFiltro = "";
    $stFiltroLote = empty($stFiltroLote) ? "" : $stFiltroLote;
    $stFiltroImovel = empty($stFiltroImovel) ? "" : $stFiltroImovel;
    if ($this->inNumeroInscricao) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    if ($this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal =='') {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricaoInicial;
    } elseif (!$this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inNumeroInscricaoFinal;
    } elseif ($this->inNumeroInscricaoInicial && $this->inNumeroInscricaoFinal) {
        $stFiltroLote .= " AND I.inscricao_municipal BETWEEN ".$this->inNumeroInscricaoInicial." AND ".$this->inNumeroInscricaoFinal;
    }
    if ( $this->roRCIMLote->getNumeroLote() ) {
        $stFiltroLote .= " AND ltrim(LL.valor,''0'') = ''".ltrim($this->roRCIMLote->getNumeroLote(),'0')."'' ";
    }
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto like ''".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()."%''";
    }
    if ($this->roUltimoProprietario) {
        if ( $this->roUltimoProprietario->getNumeroCGM() ) {
            $stFiltroLote .= " AND C.numcgm = ".$this->roUltimoProprietario->getNumeroCGM();
        }
        if ($this->roUltimoProprietario->inNumeroCGMInicial && $this->roUltimoProprietario->inNumeroCGMFinal) {
            $stFiltroLote .= " AND C.numcgm BETWEEN  ".$this->roUltimoProprietario->inNumeroCGMInicial." AND ".$this->roUltimoProprietario->inNumeroCGMFinal;
        }
        if ( $this->roUltimoProprietario->obRCGM->getNomCGM() ) {
            $stFiltroLote .= " AND UPPER( C.nom_cgm ) like UPPER( ''".$this->roUltimoProprietario->obRCGM->getNomCGM()."%'' )";
        }
    }

    if ( $this->getNumeroImovel() ) {
        $novoNumero = ltrim ( $this->getNumeroImovel() , '0');
        $stFiltroLote .= " AND ltrim ( I.numero, ''0'' ) = ''". $novoNumero ."''";
    }

    if ( $this->getComplementoImovel() ) {
        $stFiltroLote .= " AND UPPER( I.complemento ) like UPPER( ''".$this->getComplementoImovel()."%'' )";
    }
    if ( $this->obRCIMLogradouro->getCodigoLogradouro() ) {
        $stFiltroImovel .= " AND LO.cod_logradouro = ".$this->obRCIMLogradouro->getCodigoLogradouro();
    }
    if ( $this->obRCIMBairro->getCodigoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro = ".$this->obRCIMBairro->getCodigoBairro();
    }
    if ( $this->obRCIMImobiliaria->getRegistroCreci() ) {
        $stFiltroImovel .= " AND II.creci = ".$this->obRCIMImobiliaria->getRegistroCreci();
    }
    if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
        $stFiltroImovel .= " AND ICO.cod_condominio = ".$this->obRCIMCondominio->getCodigoCondominio();
    }
    if ( $this->getLogradouro() ) {
        $stFiltro = " WHERE UPPER( logradouro ) like UPPER ('%".$this->getLogradouro()."%')";
    }

    $stFiltroAtrbImovel = "";
    $stFiltroAtrbEdf = "";
    $stFiltroAtrbLote = "";
    $inTotalEdf = 0;
    $inTotalLote = 0;
    $inTotalImovel = 0;
    $boLoteUrbano = true;

    if ($this->arAtributosDinamicosConsultaImob) {
        for ( $inX=0; $inX<count( $this->arAtributosDinamicosConsultaImob ); $inX++ ) {
            switch ($this->arAtributosDinamicosConsultaImob[$inX]["cod_cadastro"]) {
                case 5: //edificacao
                    $stFiltroAtrbEdf .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."'' ) OR ";
                    $stFiltroImovel .= "
                        AND atev.inscricao_municipal = I.inscricao_municipal
                    ";
                    $inTotalEdf++;
                    break;

                case 4: //imovel
                    $stFiltroAtrbImovel .= " ( atributo_imovel_valor.cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND atributo_imovel_valor.valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."' ) OR ";
                    $stFiltroImovel .= "
                        AND aiv.inscricao_municipal = I.inscricao_municipal
                    ";

                    $inTotalImovel++;
                    break;

                case 3: //lote rural
                    $boLoteUrbano = false;
                    $stFiltroAtrbLote .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor = ''".$this->arAtributosDinamicosConsultaImob[$inX]["valor"]."'' ) OR ";
                    $inTotalLote++;
                    break;

                case 2: //lote urbano                                        
                    if ( trim($this->arAtributosDinamicosConsultaImob[$inX]["valor"]) != '' ){
                        $arValoresAtributo = explode(',',trim($this->arAtributosDinamicosConsultaImob[$inX]["valor"]));
                        if (count($arValoresAtributo) > 0){
                            foreach ($arValoresAtributo as $key => $value) {
                                $stFiltroAtrbLote .= " ( cod_atributo = ".$this->arAtributosDinamicosConsultaImob[$inX]["cod_atributo"]." AND valor ilike ''%".$value."%'' ) OR ";
                                $inTotalLote++;
                            }
                        }
                    }                    
                    break;
            }
        }

        if ($stFiltroAtrbEdf) {
            $stFiltroAtrbEdf = "
                WHERE
                    cod_cadastro = 5
                    AND cod_modulo = 12
                    AND ( ".substr( $stFiltroAtrbEdf, 0, strlen( $stFiltroAtrbEdf ) - 4 )." )

                GROUP BY
                    cod_construcao,
                    cod_tipo

                HAVING COUNT (cod_construcao) >= ".$inTotalEdf;
        } else {
            $stFiltroAtrbEdf = "
                GROUP BY
                    cod_construcao,
                    cod_tipo
            ";
        }

        if ($stFiltroAtrbImovel) {
            $stFiltroAtrbImovel = "
                WHERE
                    cod_cadastro = 4
                    AND cod_modulo = 12
                    AND ( ".substr( $stFiltroAtrbImovel, 0, strlen( $stFiltroAtrbImovel ) - 4 )." )

                GROUP BY
                    inscricao_municipal

                HAVING COUNT (inscricao_municipal) >= ".$inTotalImovel;
        } else {
            $stFiltroAtrbImovel = "
                GROUP BY
                    inscricao_municipal
            ";
        }

        if ($stFiltroAtrbLote) {
            $stFiltroAtrbLote = "
                WHERE
                    cod_modulo = 12
                    AND  ".substr( $stFiltroAtrbLote, 0, strlen( $stFiltroAtrbLote ) - 4 )." 
            ";

            $stFiltroAtrbLote .= "
                    AND cod_cadastro = ".($boLoteUrbano?2:3)."

                GROUP BY
                    cod_lote

                --HAVING COUNT (cod_lote) >= ".$inTotalLote;
        }
    } else {
        $stFiltroAtrbImovel = " GROUP BY inscricao_municipal ";

        $stFiltroAtrbLote .= "";

        $stFiltroAtrbEdf .= "";
    }

    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stDistinct"     , 'TRUE'              );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroLote"   , $stFiltroLote       );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroImovel" , $stFiltroImovel     );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbImovel" , $stFiltroAtrbImovel );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbLote" , $stFiltroAtrbLote );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroAtrbEdf" , $stFiltroAtrbEdf );
    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder );
    return $obErro;
}

function listarImoveisProprietario(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roUltimoProprietario->getNumeroCGM() ) {
            $stFiltro .= " AND IP.numcgm = ".$this->roUltimoProprietario->getNumeroCGM();
    }
    $obErro = $this->obTCIMImovel->recuperaImoveisAtivosCgm( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarProcessos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getNumeroInscricao()) {
        $stFiltro .= " ip.inscricao_municipal = ".$this->getNumeroInscricao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY ip.timestamp";
    $obErro = $this->obTCIMImovel->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function incluirProprietarios($boTransacao = "")
{
    $obErro = new Erro;
    if ( is_array ($this->arRCIMProprietario ) and count( $this->arRCIMProprietario )  ) {
        $inQuotaProprietario = 0;
        $inQuotaPromitente  = 0;
        foreach ($this->arRCIMProprietario as $obRCIMProprietario) {
            $flSubQuotaProprietario = str_replace( ".", "", $obRCIMProprietario->getCota() );
            $flSubQuotaProprietario = str_replace( ",", ".", $flSubQuotaProprietario );
            $inQuotaProprietario += $flSubQuotaProprietario;
            $obErro = $obRCIMProprietario->incluirProprietario( $boTransacao );
            $boFlagProp = true;
            if ( $obErro->ocorreu() ) {
                break;
            }
        }
        if ( !$obErro->ocorreu() && $boFlagProp ) {
            if ( (number_format($inQuotaProprietario,2,".","") - 100) != 0 ) {
                $obErro->setDescricao( "A soma das quotas dos proprietários deve ser igual a 100%!" );
            }
        }

        if ( !$obErro->ocorreu() ) {
            foreach ($this->arRCIMProprietarioPromitente  as $obRCIMProprietario) {
                $flSubQuotaPromitente = str_replace( ".","",$obRCIMProprietario->getCota() );
                $flSubQuotaPromitente = str_replace( ",", ".", $flSubQuotaPromitente );
                $inQuotaPromitente += $flSubQuotaPromitente;
                $obErro = $obRCIMProprietario->incluirProprietario( $boTransacao );
                $boFlagProm = true;
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
            if ( !$obErro->ocorreu() && $boFlagProm ) {
                if ( (number_format($inQuotaPromitente,2,".","") - 100) != 0 ) {
                    $obErro->setDescricao( "A soma das quotas dos proprietários promitentes deve ser igual a 100%!" );
                }
            }
        }
    } else {
        $obErro->setDescricao( "Deve ser informado ao menos um proprietário!" );
    }

    return $obErro;
}

function excluirProprietarios($boTransacao = "")
{
    $obRCIMProprietario = new RCIMProprietario( $this );
    $obErro = $obRCIMProprietario->excluirProprietarioPorImovel( $boTransacao );

    return $obErro;
}

function salvarProprietarios($boTransacao = "" , $boExclusao = false)
{
    $arProprietarios = array();
    $arPromitentes   = array();
    $inQuotaProprietario = 0;
    $inQuotaPromitente  = 0;
    $obErro = new Erro;
    if ( ( is_array ($this->arRCIMProprietario ) and count( $this->arRCIMProprietario ) ) or $boExclusao ) {
        $obRCIMProprietario = new RCIMProprietario( $this );
        $obErro = $obRCIMProprietario->listarProprietariosPorImovel( $rsProrprietarios, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsProrprietarios->eof() ) {
                $boPromitente = $rsProrprietarios->getCampo("promitente");
                $flCota       = $rsProrprietarios->getCampo("cota");
                if ($boPromitente == 'f') {
                    $arProprietarios[$rsProrprietarios->getCampo("numcgm")] = $flCota;
                } else {
                    $arPromitentes[$rsProrprietarios->getCampo("numcgm")] = $flCota;
                }
                $rsProrprietarios->proximo();
            }
            foreach ($this->arRCIMProprietario as $obRCIMProprietario) {
                $inNumeroCGM = $obRCIMProprietario->getNumeroCGM();
                //ALTERRA OS REGISTROS QUE ESTAVAM NA BASE
                if ( isset( $arProprietarios[$inNumeroCGM] ) ) {
                    if ( $arProprietarios[$inNumeroCGM]["cota"] != $obRCIMProprietario->getCota() ) {
                        $flSubQuotaProprietario = str_replace( ".", "", $obRCIMProprietario->getCota() );
                        $flSubQuotaProprietario = str_replace( ",", ".", $flSubQuotaProprietario );
                        $inQuotaProprietario += $flSubQuotaProprietario;
                        $obErro = $obRCIMProprietario->alterarProprietario( $boTransacao );
                        unset( $arProprietarios[$inNumeroCGM] );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                } else {
                    //INCLUSAO DE NOVOS PROPRIETARIOS
                    $flSubQuotaProprietario = str_replace( ".", "", $obRCIMProprietario->getCota() );
                    $flSubQuotaProprietario = str_replace( ",", ".", $flSubQuotaProprietario );
                    $inQuotaProprietario += $flSubQuotaProprietario;
                    $obErro = $obRCIMProprietario->incluirProprietario( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($arProprietarios as $inNumCGM => $flCota) {
                    $obRCIMProprietario->setNumeroCGM( $inNumCGM );
                    $obErro = $obRCIMProprietario->excluirProprietario( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }

            if ( (number_format($inQuotaProprietario,2,".","") - 100) != 0 and !$obErro->ocorreu() and $boExclusao == false ) {
                $obErro->setDescricao( "A soma das quotas dos proprietários deve ser igual a 100%!" );
            }

            if ( !$obErro->ocorreu() ) {
                foreach ($this->arRCIMProprietarioPromitente as $obRCIMProprietarioPromitente) {
                    $inNumeroCGM = $obRCIMProprietarioPromitente->getNumeroCGM();
                    //ALTERRA OS REGISTROS QUE ESTAVAM NA BASE
                    if ( isset( $arPromitentes[$inNumeroCGM] ) ) {
                        if ( $arPromitentes[$inNumeroCGM]["cota"] != $obRCIMProprietarioPromitente->getCota() ) {
                            $flSubQuotaPromitente = str_replace( ".","",$obRCIMProprietarioPromitente->getCota() );
                            $flSubQuotaPromitente = str_replace( ",", ".", $flSubQuotaPromitente );
                            $inQuotaPromitente += $flSubQuotaPromitente;
                            $obErro = $obRCIMProprietarioPromitente->alterarProprietario( $boTransacao );
                            unset( $arPromitentes[$inNumeroCGM] );
                            $boFlagProm = true;
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    } else {
                        //INCLUSAO DE NOVOS PROPRIETARIOS
                        $flSubQuotaPromitente = str_replace( ".","",$obRCIMProprietarioPromitente->getCota() );
                        $flSubQuotaPromitente = str_replace( ",", ".", $flSubQuotaPromitente );
                        $inQuotaPromitente += $flSubQuotaPromitente;
                        $obErro = $obRCIMProprietarioPromitente->incluirProprietario( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
                if ( !$obErro->ocorreu() ) {
                    foreach ($arPromitentes as $inNumCGM => $flCota) {
                        $obRCIMProprietario->setNumeroCGM( $inNumCGM );
                        $obErro = $obRCIMProprietario->excluirProprietario( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
                if ( !$obErro->ocorreu() && $boFlagProm ) {
                    if ( (number_format($inQuotaPromitente,2,".","") - 100) != 0 ) {
                        $obErro->setDescricao( "A soma das quotas dos proprietários promitentes deve ser igual a 100%!" );
                    }
                }
            }
        }
    } else {
        $obErro->setDescricao( "Deve ser informado ao menos um proprietário!" );
    }

    return $obErro;
}

function recuperaDataLoteImovel($boTransacao="")
{
    $stFiltro = "";
    if ($this->inNumeroInscricao) {
        $stFiltro .= " AND I.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    $obErro = $this->obTCIMImovelLote->recuperaDataLoteImovel( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    $this->roRCIMLote->setDataInscricao($rsRecordSet->getCampo('dt_inscricao'));

    return $obErro;
}

function addProprietario()
{
    $this->arRCIMProprietario[] = new RCIMProprietario( $this );
    $this->roUltimoProprietario = &$this->arRCIMProprietario[ count( $this->arRCIMProprietario ) - 1 ];
}

function addProprietarioPromitente()
{
    $this->arRCIMProprietarioPromitente[] = new RCIMProprietario( $this );
    $this->roUltimoProprietarioPromitente = &$this->arRCIMProprietarioPromitente[ count( $this->arRCIMProprietarioPromitente ) - 1 ];
}

function addImovelCorrespondencia()
{
    $this->obRCIMLogradouro = new RCIMLogradouro;
}

function addUnidadeAutonoma()
{
     $this->obRCIMUnidadeAutonoma = new RCIMUnidadeAutonoma( $this );
}

function consultaProprietariosCalculo(&$rsProprietario,$boTransacao = "")
{
    include_once(CAM_GT_CIM_MAPEAMENTO."TCIMProprietario.class.php");
    $this->obTCIMProprietario = new TCIMProprietario;
    if ($this->getNumeroInscricao()) {
        $stFiltro = $this->getNumeroInscricao();
    }
    $obErro = $this->obTCIMProprietario->recuperaProprietariosCalculo($rsProprietario, $stFiltro,'',$boTransacao);

    return $obErro;
}

function listarEnderecoEntrega(&$rsEnderecoEntrega, $boTransacao = "")
{
    if ($this->inNumeroInscricao) {
        $stFiltro = " AND IC.inscricao_municipal = ".$this->inNumeroInscricao;
    }
    $obErro = $this->obTCIMImovelCorrespondencia->recuperaRelacionamento( $rsEnderecoEntrega, $stFiltro, 'timestamp asc', $boTransacao );

    return $obErro;
}

function verificaBaixaImovel(&$rsBaixaImovel, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->roRCIMLote->getNumeroLote() ) {
       $stFiltro .= " LPAD( UPPER( IMOVEL.VALOR ), 10,'0' ) = LPAD( UPPER('".$this->roRCIMLote->getNumeroLote()."'), 10,'0' ) AND ";
    }
    if ( $this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " IMOVEL.COD_LOCALIZACAO = ".$this->roRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()." AND ";
    }
    if ($this->inNumeroInscricao) {
        $stFiltro .= " IMOVEL.inscricao_municipal = ".$this->inNumeroInscricao." AND ";
    }
    if ($this->stTipoLote) {
        $stFiltro .= " IMOVEL.TIPO_LOTE = '".$this->stTipoLote."' AND ";
    }
    $stOrdem = " ORDER BY IMOVEL.inscricao_municipal ";
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $this->obTCIMImovel->recuperaRelacionamentoImovelBaixado( $rsBaixaImovel, $stFiltro, '', $boTransacao );

    return $obErro;
}

function validaInscricao($boTransacao = "")
{
    $stFiltro = " WHERE inscricao_municipal = ".$this->inNumeroInscricao;
    $obErro = $this->obTCIMImovel->recuperaTodos( $rsInscricao, $stFiltro, "", $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$rsInscricao->eof()) {
            $obErro->setDescricao("Inscrição Imobiliária já cadastrada no sistema!");
        }
    }

 return $obErro;
}

} // end of class
?>
