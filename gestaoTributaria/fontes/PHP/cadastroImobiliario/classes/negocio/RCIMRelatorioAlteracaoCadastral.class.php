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
 * Classe de regra de Relatório de Alteração Cadastral
 * Data de Criação: 13/04/2005

 * @author Analista: Fábio Bertoldi Rodrigues
 * @author Desenvolvedor: Marcelo B. Paulino

 * @package URBEM
 * @subpackage Regra

 * $Id: RCIMRelatorioAlteracaoCadastral.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.01.25
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_BANCO_DADOS."PersistenteRelatorio.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMImovel.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."TCIMAtributoImovelValor.class.php";
include_once CAM_GT_CIM_MAPEAMENTO."FCIMRelatorioAlteracaoCadastral.class.php";
include_once CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php";

set_time_limit(0);

/**
    * Classe de Regra para relatório de AlteracaoCadastral
    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Marcelo B. Paulino
*/
class RCIMRelatorioAlteracaoCadastral
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
var $obFCIMRelatorioAlteracaoCadastral;
/**
    * @var Object
    * @access Private
*/
var $obTCIMAtributoImovelValor;
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
/**
    * Método Construtor
    * @access Private
*/
function RCIMRelatorioAlteracaoCadastral()
{
    $this->obTCIMImovel                      = new TCIMImovel;
    $this->obTCIMAtributoImovelValor         = new TCIMAtributoImovelValor;
    $this->obRCadastroDinamico               = new RCadastroDinamico;
    $this->obFCIMRelatorioAlteracaoCadastral = new FCIMRelatorioAlteracaoCadastral;

    $this->obRCadastroDinamico->setPersistenteValores   ( new TCIMAtributoImovelValor );
    $this->obRCadastroDinamico->setCodCadastro          ( 4 );
    $this->obRCadastroDinamico->obRModulo->setCodModulo ( 12 );

}
/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$rsRecordSet , &$arCabecalho, $stOrder = "")
{
    $stFiltro = $stFiltroLote = $stFiltroImovel = "";

    if ( $this->getCodInicioInscricao() AND !$this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inCodInicioInscricao;
    } elseif ( !$this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal = ".$this->inCodTerminoInscricao;
    } elseif ( $this->getCodInicioInscricao() AND $this->getCodTerminoInscricao() ) {
        $stFiltroLote .= " AND I.inscricao_municipal BETWEEN ".$this->inCodInicioInscricao." AND ".$this->inCodTerminoInscricao;
    }

    if ( $this->getCodInicioLote() AND !$this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND L.cod_lote = ".$this->inCodInicioLote;
    } elseif ( !$this->getCodInicioLote() AND $this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND L.cod_lote = ".$this->inCodTerminoLote;
    } elseif ( $this->getCodInicioLote() AND $this->getCodTerminoLote() ) {
        $stFiltroLote .= " AND L.cod_lote BETWEEN ".$this->inCodInicioLote." AND ".$this->inCodTerminoLote;
    }

    if ( $this->getCodInicioLocalizacao() AND !$this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto = ''".$this->inCodInicioLocalizacao."''";
    } elseif ( !$this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto = ''".$this->inCodTerminoLocalizacao."''";
    } elseif ( $this->getCodInicioLocalizacao() AND $this->getCodTerminoLocalizacao() ) {
        $stFiltroLote .= " AND LOC.codigo_composto BETWEEN ''".$this->inCodInicioLocalizacao."'' AND ''".$this->inCodTerminoLocalizacao."''" ;
    }

    if ( $this->getCodInicioBairro() AND !$this->getCodTerminoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro = ".$this->inCodInicioBairro;
    } elseif ( !$this->getCodInicioBairro() AND $this->getCodTerminoBairro() ) {
        $stFiltroImovel .= " AND B.cod_bairro = ".$this->inCodTerminoBairro;
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

    switch ($this->stOrder) {
        case 'inscricao':   $stOrder = " inscricao_municipal, localizacao";      break;
        case 'localizacao': $stOrder = " localizacao, inscricao_municipal";      break;
        case 'lote':        $stOrder = " cod_lote, localizacao";                 break;
        case 'logradouro':  $stOrder = " nom_logradouro, inscricao_municipal";   break;
        case 'bairro':      $stOrder = " nom_bairro, inscricao_municipal";       break;
        default: $stOrder = " inscricao_municipal, localizacao";
    }
    $stOrder = " ORDER BY ".$stOrder;

    $arAtributos = $this->getAtributos();
    $arAtributos = $arAtributos[0];

    $this->obFCIMRelatorioAlteracaoCadastral->setDado( "stFiltroLote"   , $stFiltroLote    );
    $this->obFCIMRelatorioAlteracaoCadastral->setDado( "stFiltroImovel" , $stFiltroImovel  );
    $obErro = $this->obFCIMRelatorioAlteracaoCadastral->recuperaTodos( $rsRecordSet, $stOrder );

    $arRecord       = array();
    $arProprietario = array();
    $inCount        = 0;
    $inFirstLoop    = true;
    $boProprietario = false;

    while ( !$rsRecordSet->eof() ) {

        if ( $inFirstLoop == true  OR  ( $rsRecordSet->getCampo('inscricao_municipal') == $inInscricaoMunicipalAnterior ) ) {

         if ($inFirstLoop == true) {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['inscricao'   ] = $rsRecordSet->getCampo('inscricao_municipal');
                $arRecord[$inCount]['localizacao' ] = $rsRecordSet->getCampo('localizacao');
                $arRecord[$inCount]['lote'        ] = $rsRecordSet->getCampo('cod_lote')." - ".$rsRecordSet->getCampo('tipo_lote');
                $arRecord[$inCount]['logradouro'  ] = $rsRecordSet->getCampo('logradouro');
                $arRecord[$inCount]['bairro'      ] = $rsRecordSet->getCampo('nom_bairro');
                $arRecord[$inCount]['situacao'    ] = $rsRecordSet->getCampo('situacao');
                $boProprietario = false;
            } else {
                $arRecord[$inCount]['pagina'      ] = 0;
                $arRecord[$inCount]['inscricao'   ] = "";
                $arRecord[$inCount]['localizacao' ] = "";
                $arRecord[$inCount]['lote'        ] = "";
                $arRecord[$inCount]['logradouro'  ] = "";
                $arRecord[$inCount]['bairro'      ] = "";
                $arRecord[$inCount]['situacao'    ] = "";
                $boProprietario = true;

            }
            $arRecord[$inCount]['proprietario'] = $rsRecordSet->getCampo('proprietario_cota')."%";
        } else {
            $arRecord[$inCount]['pagina'      ] = 0;
            $arRecord[$inCount]['inscricao'   ] = $rsRecordSet->getCampo('inscricao_municipal');
            $arRecord[$inCount]['localizacao' ] = $rsRecordSet->getCampo('localizacao');
            $arRecord[$inCount]['lote'        ] = $rsRecordSet->getCampo('cod_lote')." - ".$rsRecordSet->getCampo('tipo_lote');
            $arRecord[$inCount]['logradouro'  ] = $rsRecordSet->getCampo('logradouro');
            $arRecord[$inCount]['bairro'      ] = $rsRecordSet->getCampo('nom_bairro');
            $arRecord[$inCount]['proprietario'] = $rsRecordSet->getCampo('proprietario_cota')."%";
            $arRecord[$inCount]['situacao'    ] = $rsRecordSet->getCampo('situacao');
            $boProprietario = false;
        }
        if ( ( $this->getTipoRelatorio() == 'analitico' ) AND ( is_array($arAtributos) ) AND $boProprietario == false ) {
            //monta array com os atributos que serao exibidos no relatorio

            $arChaveAtributoImovel = array( "inscricao_municipal" => $rsRecordSet->getCampo('inscricao_municipal') );
            $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovel );
            $this->obRCadastroDinamico->recuperaAtributosSelecionadosValoresHistorico( $rsAtributos,'','ORDER BY valor.inscricao_municipal,valor.cod_cadastro,valor.timestamp,valor.cod_atributo' );

            while ( !$rsAtributos->eof() ) {

                if ( in_array($rsAtributos->getCampo('cod_atributo') , $arAtributos ) ) {
                    //monta array de cabecalho dos atributos
                    $boIncluir = true;
                    for ( $inX=0; $inX<count( $arCabecalho ); $inX++ ) {
                        if ( $arCabecalho[$inX] == $rsAtributos->getCampo('nom_atributo') ) {
                            $boIncluir = false;
                            break;
                        }
                    }

                    if ($boIncluir) {
                        if ( count($arCabecalho) < count($arAtributos) ) {
                            $arCabecalho[] = $rsAtributos->getCampo('nom_atributo');
                        }
                    }

                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {
                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case 'Texto': $valor = $rsAtributos->getCampo('valor'); break;
                            case 'Numerico': $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' ); break;
                            case 'Lista':
                                $arValorPadrao  = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc') );
                                $arIndiceValor  = explode( ',' , $rsAtributos->getCampo('valor_padrao') );
                                $i = 0;
                                $arCombinado = array();
                                foreach ($arIndiceValor as $valorIndice) {
                                    $arCombinado[$valorIndice] = $arValorPadrao[$i];
                                    $i++;
                                }
                                $inPosicao     = $rsAtributos->getCampo('valor');// - 1;
                                //$valor         = $arValorPadrao[$inPosicao];
                                $valor         = $arCombinado[$inPosicao];
                            break;
                            default: $valor = $rsAtributos->getCampo('valor');
                        }
                    }
                    $arRecord[$inCount][$rsAtributos->getCampo('nom_atributo')] = $valor;
                }
                $stTimeStampOld = $rsAtributos->getCampo('timestamp');

                $rsAtributos->proximo();
                if (!$rsAtributos->eof()) {
                    if ($rsAtributos->getCampo('timestamp') != $stTimeStampOld and in_array($rsAtributos->getCampo('cod_atributo') , $arAtributos ) ) {
                        $inCount++;
                        $arRecord[$inCount]['inscricao'   ] = $rsRecordSet->getCampo('inscricao_municipal') ;
                        $arRecord[$inCount]['localizacao' ] = $rsRecordSet->getCampo('localizacao');
                        $arRecord[$inCount]['lote'        ] = $rsRecordSet->getCampo('cod_lote')." - ".$rsRecordSet->getCampo('tipo_lote');
                        $arRecord[$inCount]['logradouro'  ] = $rsRecordSet->getCampo('logradouro');
                        $arRecord[$inCount]['bairro'      ] = $rsRecordSet->getCampo('nom_bairro');
                        $arRecord[$inCount]['proprietario'] = $rsRecordSet->getCampo('proprietario_cota')."%";
                        $arRecord[$inCount]['situacao'    ] = $rsRecordSet->getCampo('situacao');
                    }
                }
            }
            $inCountCabecalho     = count( $arCabecalho );
            $inWidth              = 69 / $inCountCabecalho;
            $arCabecalho['width'] = $inWidth;
        }
        $inInscricaoMunicipalAnterior = $rsRecordSet->getCampo('inscricao_municipal');

        $inCount++;
//tira o espaço em branco quando o tipo de relatório é analítico e existe mais de 1 propietário para a mesma inscrição municipal
        if (( $boProprietario) && ($this->getTipoRelatorio() == 'analitico')) {
            $inCount--;
        }
        $inFirstLoop = false;
        $rsRecordSet->proximo();
    }
    $rsRecordSet = new RecordSet;
    $rsRecordSet->preenche( $arRecord );

    return $obErro;
}
}
