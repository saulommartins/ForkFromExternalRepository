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
 * Classe de regra de Relatório de Cadastro de Imóveis
 * Data de Criação: 28/04/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo B. Paulino

 * @package URBEM
 * @subpackage Regra

* $Id: RCIMRelatorioCadastroImobiliario.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMAtributoImovelValor.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMAtributoLoteRuralValor.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMAtributoLoteUrbanoValor.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."FCIMRelatorioCadastroImobiliario.class.php";
include_once CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

set_time_limit(0);

/**
    * Classe de Regra para relatório de CadastroImobiliario
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioCadastroImobiliario
{
/**
    * @var Object
    * @access Private
*/
var $obTCIMImovel;
/**
    * @var Object
    * @access Private
*/
var $obFCIMRelatorioCadastroImobiliario;
/**
    * @var Object
    * @access Private
*/
var $obTCIMAtributoImovelValor;
/**
    * @var Object
    * @access Private
*/
var $obRCIMConfiguracao;
/**
    * @access Private
    * @var String
*/
var $stTipoRelatorio;
/**
    * @access Private
    * @var Array
*/
var $arAtributos;
var $arAtributosLote2;
var $arAtributosLote3;
/**
    * @access Private
    * @var Intger
*/
var $inCodInicioLocalizacao;
/**
    * @access Private
    * @var Intger
*/
var $inCodInicioLote;
/**
    * @access Private
    * @var Intger
*/
var $inCodInicioInscricao;
/**
    * @access Private
    * @var Intger
*/
var $inCodInicioLogradouro;
/**
    * @access Private
    * @var Intger
*/
var $inCodInicioBairro;
/**
    * @access Private
    * @var Intger
*/
var $inCodTerminoLocalizacao;
/**
    * @access Private
    * @var Intger
*/
var $inCodTerminoLote;
/**
    * @access Private
    * @var Intger
*/
var $inCodTerminoInscricao;
/**
    * @access Private
    * @var Intger
*/
var $inCodTerminoLogradouro;
/**
    * @access Private
    * @var Intger
*/
var $inCodTerminoBairro;
/**
    * @access Private
    * @var Intger
*/
var $stOrder;

/**
    * @access Private
    * @var Intger
*/
var $inFiltroEdificacao;
/**
    * @access Public
    * @param Integer $valor
*/

var $stTipoSituacao;
var $inCGMInicio;
var $inCGMTermino;

/**
    * @access Public
    * @param String $valor
*/
function setFiltroCGMInicio($valor) { $this->inCGMInicio = $valor; }
function setFiltroCGMTermino($valor) { $this->inCGMTermino = $valor; }
function setFiltroEdificacao($valor) { $this->inFiltroEdificacao = $valor; }
function getFiltroCGMInicio() { return $this->inCGMInicio; }
function getFiltroCGMTermino() { return $this->inCGMTermino; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioLocalizacao($valor) { $this->inCodInicioLocalizacao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioLote($valor) { $this->inCodInicioLote = $valor;        }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioInscricao($valor) { $this->inCodInicioInscricao = $valor;   }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioLogradouro($valor) { $this->inCodInicioLogradouro  = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodInicioBairro($valor) { $this->inCodInicioBairro = $valor;      }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoLocalizacao($valor) { $this->inCodTerminoLocalizacao = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoLote($valor) { $this->inCodTerminoLote = $valor;       }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoInscricao($valor) { $this->inCodTerminoInscricao = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoLogradouro($valor) { $this->inCodTerminoLogradouro = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTerminoBairro($valor) { $this->inCodTerminoBairro = $valor;     }
/**
    * @access Public
    * @param String $valor
*/
function setOrder($valor) { $this->stOrder = $valor;                }
/**
    * @access Public
    * @param String $valor
*/
function setTipoRelatorio($valor) { $this->stTipoRelatorio = $valor;         }
/**
    * @access Public
    * @param Array $valor
*/
function setAtributos($valor) { $this->arAtributos[] = $valor;           }
function setAtributosLote2($valor) { $this->arAtributosLote2[] = $valor;      }
function setAtributosLote3($valor) { $this->arAtributosLote3[] = $valor;      }

function setTipoSituacao($valor) {$this->stTipoSituacao = $valor;}
function getTipoSituacao() { return $this->stTipoSituacao;}
/**
    * @access Public
    * @return Integer
*/
function getFiltroEdificacao() { return $this->inFiltroEdificacao; }

/**
    * @access Public
    * @return Integer
*/
function getCodInicioLocalizacao() { return $this->inCodInicioLocalizacao;  }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioLote() { return $this->inCodInicioLote;         }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioInscricao() { return $this->inCodInicioInscricao;     }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioBairro() { return $this->inCodInicioBairro;        }
/**
    * @access Public
    * @return Integer
*/
function getCodInicioLogradouro() { return $this->inCodInicioLogradouro;    }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoLocalizacao() { return $this->inCodTerminoLocalizacao;  }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoLote() { return $this->inCodTerminoLote;         }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoInscricao() { return $this->inCodTerminoInscricao;    }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoBairro() { return $this->inCodTerminoBairro;       }
/**
    * @access Public
    * @return Integer
*/
function getCodTerminoLogradouro() { return $this->inCodTerminoLogradouro;   }
/**
    * @access Public
    * @return Integer
*/
function getOrder() { return $this->stOrder;                  }
/**
    * @access Public
    * @return String
*/
function getTipoRelatorio() { return $this->stTipoRelatorio;           }
/**
    * @access Public
    * @return Array
*/
function getAtributos() { return $this->arAtributos;               }
function getAtributosLote2() { return $this->arAtributosLote2;          }
function getAtributosLote3() { return $this->arAtributosLote3;          }

/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioCadastroImobiliario()
{
    $this->obTCIMImovel                       = new TCIMImovel;
    $this->obTCIMAtributoImovelValor          = new TCIMAtributoImovelValor;
    $this->obRCadastroDinamico                = new RCadastroDinamico;
    $this->obFCIMRelatorioCadastroImobiliario = new FCIMRelatorioCadastroImobiliario;
    $this->obRCIMConfiguracao                 = new RCIMConfiguracao;

    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoImovelValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 4 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );

    $this->obRCIMConfiguracao->setCodigoModulo( 12 );
    $this->obRCIMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $this->obRCIMConfiguracao->consultarConfiguracao();
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , &$arCabecalho, $stOrder = "")
{
    $stFiltro = $stFiltroLote = $stFiltroImovel = "";

    if ( $this->getCodInicioInscricao() AND !$this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal >= ".$this->inCodInicioInscricao;
    } elseif ( !$this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal <= ".$this->inCodTerminoInscricao;
    } elseif ( $this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal BETWEEN ".$this->inCodInicioInscricao." AND ".$this->inCodTerminoInscricao;
    }

    if ( $this->getCodInicioLote() AND !$this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND LL.valor >= ".$this->inCodInicioLote;
    } elseif ( !$this->getCodInicioLote() AND $this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND LL.valor <= ".$this->inCodTerminoLote;
    } elseif ( $this->getCodInicioLote() AND $this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND LL.valor BETWEEN ''".$this->inCodInicioLote."'' AND ''".$this->inCodTerminoLote."''";
    }

    if ( $this->getCodInicioLocalizacao() AND !$this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto >= ''".$this->inCodInicioLocalizacao."''";
    } elseif ( !$this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto <= ''".$this->inCodTerminoLocalizacao."''";
    } elseif ( $this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto BETWEEN ''".$this->inCodInicioLocalizacao."'' AND ''".$this->inCodTerminoLocalizacao."''" ;
    }

    if ( $this->getFiltroCGMInicio() && $this->getFiltroCGMTermino() ) {
        $stFiltroImovel .= " AND I.numcgm BETWEEN ".$this->getFiltroCGMInicio()." AND ".$this->getFiltroCGMTermino();
    } elseif ( $this->getFiltroCGMInicio() && !$this->getFiltroCGMTermino() ) {
        $stFiltroImovel .= " AND I.numcgm >= ".$this->getFiltroCGMInicio();
    } elseif ( !$this->getFiltroCGMInicio() && $this->getFiltroCGMTermino() ) {
        $stFiltroImovel .= " AND I.numcgm <= ".$this->getFiltroCGMTermino();
    }

    if ( $this->getCodInicioBairro() AND !$this->getCodTerminoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro >= ".$this->inCodInicioBairro;
    } elseif ( !$this->getCodInicioBairro() AND $this->getCodTerminoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro <= ".$this->inCodTerminoBairro;
    } elseif ( $this->getCodInicioBairro() AND $this->getCodTerminoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro BETWEEN ".$this->inCodInicioBairro." AND ".$this->getCodTerminoBairro() ;
    }

    if ( $this->getCodInicioLogradouro() AND !$this->getCodTerminoLogradouro() ) {
        $stFiltroImovel .= " AND LO.cod_logradouro = ".$this->inCodInicioLogradouro;
    } elseif ( !$this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltroImovel .= " AND LO.cod_logradouro = ".$this->inCodTerminoLogradouro;
    } elseif ( $this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltroImovel .= " AND LO.cod_logradouro BETWEEN ".$this->inCodInicioLogradouro." AND ".$this->inCodTerminoLogradouro ;
    }

//filtro por edificacao

    if ( $this->getFiltroEdificacao() == 1 ) {
        $stFiltroImovel .= " AND IUA.inscricao_municipal IS NOT NULL ";
    }else
    if ( $this->getFiltroEdificacao() == 0 ) {
        $stFiltroImovel .= " AND IUA.inscricao_municipal IS NULL ";
    }
//---------------------

    switch ($this->stOrder) {
        case 'inscricao':   $stOrder = " inscricao_municipal, localizacao";      break;
        case 'localizacao': $stOrder = " localizacao, inscricao_municipal";      break;
        case 'lote':        $stOrder = " to_number(valor_lote,'9999999999'), localizacao";               break;
        //case 'logradouro':  $stOrder = " nom_logradouro, inscricao_municipal";   break;
        case 'logradouro':  $stOrder = " endereco";   break;
        case 'bairro':      $stOrder = " nom_bairro, inscricao_municipal";       break;
        //case 'cep':         $stOrder = " B.nom_bairro, U.cod_uf, M.nom_municipio";    break;
        default: $stOrder = " inscricao_municipal, localizacao";
    }
    $stOrder = " ORDER BY ".$stOrder;

    $arAtributos = $this->getAtributos();
    $arAtributos = $arAtributos[0];

    $arAtributosLote2 = $this->getAtributosLote2();
    $arAtributosLote2 = $arAtributosLote2[0];

    $arAtributosLote3 = $this->getAtributosLote3();
    $arAtributosLote3 = $arAtributosLote3[0];

    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stDistinct"     , 'TRUE'              );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroLote"   , $stFiltroLote    );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stFiltroImovel" , $stFiltroImovel  );
    $this->obFCIMRelatorioCadastroImobiliario->setDado( "stTipoSituacao" , $this->getTipoSituacao() );
    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaTodos( $rsRecordSet, $stOrder );

    $arRecord       = array();
    $arProprietario = array();
    $inCount        = 0;
    $inFirstLoop    = true;
    $boProprietario = false;

    while ( !$rsRecordSet->eof() ) {

        $nuIM   = str_pad( $rsRecordSet->getCampo('inscricao_municipal') , strlen( $this->obRCIMConfiguracao->getMascaraIM() )   , "0", STR_PAD_LEFT );
        $nuLote = str_pad( $rsRecordSet->getCampo('valor_lote')          , strlen( $this->obRCIMConfiguracao->getMascaraLote() ) , "0", STR_PAD_LEFT );

        if ( $inFirstLoop == true OR  ( $rsRecordSet->getCampo('inscricao_municipal') == $inInscricaoMunicipalAnterior ) ) {

            if ($inFirstLoop == true) {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['inscricao'   ] = $nuIM;
                $arRecord[$inCount]['localizacao' ] = $rsRecordSet->getCampo('localizacao');
                $arRecord[$inCount]['lote'        ] = $nuLote." - ".$rsRecordSet->getCampo('tipo_lote');
                $arRecord[$inCount]['endereco'    ] = $rsRecordSet->getCampo('endereco');
                $arRecord[$inCount]['cep'         ] = $rsRecordSet->getCampo('cep');
                //$arRecord[$inCount]['bairro'      ] = $rsRecordSet->getCampo('nom_bairro');
                $arRecord[$inCount]['situacao'    ] = $rsRecordSet->getCampo('situacao');
                $boProprietario = false;
            } else {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['inscricao'   ] = "";
                $arRecord[$inCount]['localizacao' ] = "";
                $arRecord[$inCount]['lote'        ] = "";
                $arRecord[$inCount]['logradouro'  ] = "";
                //$arRecord[$inCount]['bairro'      ] = "";
                $arRecord[$inCount]['situacao'    ] = "";
                $boProprietario = true;
            }
            $arRecord[$inCount]['proprietario'] = $rsRecordSet->getCampo('proprietario_cota')."%";
        } else {
            $arRecord[$inCount]['pagina'      ] = 0;
            $arRecord[$inCount]['inscricao'   ] = $nuIM;
            $arRecord[$inCount]['localizacao' ] = $rsRecordSet->getCampo('localizacao');
            $arRecord[$inCount]['lote'        ] = $nuLote." - ".$rsRecordSet->getCampo('tipo_lote');
            $arRecord[$inCount]['endereco'    ] = $rsRecordSet->getCampo('endereco');
            $arRecord[$inCount]['cep'         ] = $rsRecordSet->getCampo('cep');
            //$arRecord[$inCount]['bairro'      ] = $rsRecordSet->getCampo('nom_bairro');
            $arRecord[$inCount]['proprietario'] = $rsRecordSet->getCampo('proprietario_cota')."%";
            $arRecord[$inCount]['situacao'    ] = $rsRecordSet->getCampo('situacao');
            $boProprietario = false;
        }

        //trata atributos se o relatorio for analitico
        if ( ( $this->getTipoRelatorio() == 'analitico' ) AND ( is_array($arAtributos) ) AND $boProprietario == false ) {
            //monta array com os atributos que serao exibidos no relatorio
            $arChaveAtributoImovel = array( "inscricao_municipal" => $rsRecordSet->getCampo('inscricao_municipal') );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovel );
            $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );

            while ( !$rsAtributos->eof() ) {
                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributos ) ) {

                    //monta array de cabecalho dos atributos
                    $boAdicionar = true;
                    for ( $inZ=0; $inZ<count($arCabecalho); $inZ++ ) {
                        if ( $arCabecalho[$inZ] == $rsAtributos->getCampo('nom_atributo') ) {
                            $boAdicionar = false;
                            break;
                        }
                    }

                    if ( $boAdicionar )
                        $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');

                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {

                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":    $valor = $rsAtributos->getCampo('valor'); break;
                            case "Numerico": $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor');
                                $valor         = $arValorPadrao[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }

                    $str = preg_replace( "/[^a-zA-Z0-9 ]/", "", strtr($rsAtributos->getCampo('nom_atributo'), " áàãâéêíóôõúüçñÁÀÃÂÉÊÍÓÔÕÚÜÇÑ", "_aaaaeeiooouucnAAAAEEIIOOOUUCN"));
                    // Converte a string para maiuscula
                    $str = strtoupper( $str );
                    // Retira os espacos em branco do inicio e do fim
                    $str = trim( $str );
                    $arRecord[$inCount][$str] = $valor;
                }
                $rsAtributos->proximo();
            }
            $inCountCabecalho     = count( $arCabecalho );
            $inWidth              = 55 / $inCountCabecalho;

            $arCabecalho['width'] = $inWidth;
        }

        if ( ( $this->getTipoRelatorio() == 'analitico' ) AND ( is_array($arAtributosLote2) ) AND $boProprietario == false ) {
            //monta array com os atributos que serao exibidos no relatorio
            $arChaveAtributoLote = array( "cod_lote" => $rsRecordSet->getCampo('cod_lote') );

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoLoteUrbanoValor );
            $obRCadastroDinamico->setCodCadastro          ( 2 );
            $obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
            $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
            $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            unset( $obRCadastroDinamico );

            while ( !$rsAtributos->eof() ) {
                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributosLote2 ) ) {

                    //monta array de cabecalho dos atributos
                    $boAdicionar = true;
                    for ( $inZ=0; $inZ<count($arCabecalho); $inZ++ ) {
                        if ( $arCabecalho[$inZ] == $rsAtributos->getCampo('nom_atributo') ) {
                            $boAdicionar = false;
                            break;
                        }
                    }

                    if ( $boAdicionar )
                        $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');

                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {

                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":    $valor = $rsAtributos->getCampo('valor'); break;
                            case "Numerico": $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }

                    $str = preg_replace( "/[^a-zA-Z0-9 ]/", "", strtr($rsAtributos->getCampo('nom_atributo'), " áàãâéêíóôõúüçñÁÀÃÂÉÊÍÓÔÕÚÜÇÑ", "_aaaaeeiooouucnAAAAEEIIOOOUUCN"));
                    // Converte a string para maiuscula
                    $str = strtoupper( $str );
                    // Retira os espacos em branco do inicio e do fim
                    $str = trim( $str );
                    $arRecord[$inCount][$str] = $valor;
                }
                $rsAtributos->proximo();
            }
            $inCountCabecalho     = count( $arCabecalho );
            $inWidth              = 69 / $inCountCabecalho;
            $arCabecalho['width'] = $inWidth;
        }

        if ( ( $this->getTipoRelatorio() == 'analitico' ) AND ( is_array($arAtributosLote3) ) AND $boProprietario == false ) {
            //monta array com os atributos que serao exibidos no relatorio
            $arChaveAtributoLote = array( "cod_lote" => $rsRecordSet->getCampo('cod_lote') );

            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoLoteUrbanoValor );
            $obRCadastroDinamico->setCodCadastro          ( 3 );
            $obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
            $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
            $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            unset( $obRCadastroDinamico );

            while ( !$rsAtributos->eof() ) {
                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributosLote3 ) ) {

                    //monta array de cabecalho dos atributos
                    $boAdicionar = true;
                    for ( $inZ=0; $inZ<count($arCabecalho); $inZ++ ) {
                        if ( $arCabecalho[$inZ] == $rsAtributos->getCampo('nom_atributo') ) {
                            $boAdicionar = false;
                            break;
                        }
                    }

                    if ( $boAdicionar )
                        $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');

                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {

                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":    $valor = $rsAtributos->getCampo('valor'); break;
                            case "Numerico": $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }

                    $str = preg_replace( "/[^a-zA-Z0-9 ]/", "", strtr($rsAtributos->getCampo('nom_atributo'), " áàãâéêíóôõúüçñÁÀÃÂÉÊÍÓÔÕÚÜÇÑ", "_aaaaeeiooouucnAAAAEEIIOOOUUCN"));
                    // Converte a string para maiuscula
                    $str = strtoupper( $str );
                    // Retira os espacos em branco do inicio e do fim
                    $str = trim( $str );
                    $arRecord[$inCount][$str] = $valor;
                }
                $rsAtributos->proximo();
            }
            $inCountCabecalho     = count( $arCabecalho );
            $inWidth              = 69 / $inCountCabecalho;
            $arCabecalho['width'] = $inWidth;
        }

        $inInscricaoMunicipalAnterior = $rsRecordSet->getCampo('inscricao_municipal');
        $inCount++;
        $inFirstLoop = false;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}

function listarCaracteristicasTerreno(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodInicioLote() ) {
        $stFiltro .= " il.cod_lote = ".$this->getCodInicioLote()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaCaracteristicasTerreno( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarCaracteristicasImovel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodInicioInscricao() ) {
        $stFiltro .= " iav.inscricao_municipal = ".$this->getCodInicioInscricao()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaCaracteristicasImovel( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarCaracteristicasEdificacao(&$rsRecordSet, $inCodConstrucao = "", $inCodTipo = "", $boTransacao = "")
{
    $stFiltro = "";
    if ($inCodConstrucao) {
        $stFiltro .= " ic.cod_construcao = ".$inCodConstrucao." AND ";
    }

    if ($inCodTipo) {
        $stFiltro .= " ice.cod_tipo = ".$inCodTipo." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaCaracteristicasEdificacao( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

function listarBoletimCadastroImobiliario(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodInicioInscricao() AND !$this->getCodTerminoInscricao() ) {
        $stFiltro .= " AND ii.inscricao_municipal = ".$this->inCodInicioInscricao."  ";
    } elseif ( !$this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltro .= " AND ii.inscricao_municipal = ".$this->inCodTerminoInscricao." ";
    } elseif ( $this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltro .= " AND ii.inscricao_municipal BETWEEN ".$this->inCodInicioInscricao." AND  ".$this->inCodTerminoInscricao." ";
    }

    if ( $this->getCodInicioLocalizacao() AND !$this->getCodTerminoLocalizacao() ) {
        $stFiltro .= " AND il.codigo_composto = ".$this->inCodInicioLocalizacao."\n";
    } elseif ( !$this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltro .= " AND il.codigo_composto = ".$this->inCodTerminoLocalizacao."\n";
    } elseif ( $this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltro .= " AND il.codigo_composto BETWEEN ".$this->inCodInicioLocalizacao." AND ".$this->inCodTerminoLocalizacao."\n" ;
    }

    if ( $this->getCodInicioBairro() AND !$this->getCodTerminoBairro() ) {
        $stFiltro .= " AND bairro.cod_bairro = ".$this->inCodInicioBairro."\n";
    } elseif ( !$this->getCodInicioBairro() AND $this->getCodTerminoBairro() ) {
        $stFiltro .= " AND bairro.cod_bairro = ".$this->inCodTerminoBairro."\n";
    } elseif ( $this->getCodInicioBairro() AND $this->getCodTerminoBairro() ) {
        $stFiltro .= " AND bairro.cod_bairro BETWEEN ".$this->inCodInicioBairro." AND ".$this->getCodTerminoBairro()."\n";
    }

    if ( $this->getCodInicioLogradouro() AND !$this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND iconftre.cod_logradouro = ".$this->inCodInicioLogradouro."\n";
    } elseif ( !$this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND iconftre.cod_logradouro = ".$this->inCodTerminoLogradouro."\n";
    } elseif ( $this->getCodInicioLogradouro() AND $this->getCodTerminoLogradouro() ) {
        $stFiltro .= " AND iconftre.cod_logradouro BETWEEN ".$this->inCodInicioLogradouro." AND ".$this->inCodTerminoLogradouro."\n";
    }

    if ($stFiltro) {
      //$stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
        $stFiltro = " WHERE 1 = 1 ".$stFiltro;
    }

    $obErro = $this->obFCIMRelatorioCadastroImobiliario->recuperaBoletimCadastroImobiliario( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
}
