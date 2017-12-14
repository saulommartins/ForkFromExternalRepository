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
* Classe de regra de negócio para RBeneficioConcessaoValeTransporte
* Data de Criação: 12/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Id: RBeneficioConcessaoValeTransporte.class.php 65736 2016-06-10 20:18:11Z michel $

* Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporte.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteDiario.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteSemanal.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioGrupoConcessaoValeTransporte.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioTipoConcessaoValeTransporte.class.php";
include_once CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteCalendario.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoMes.class.php";
include_once CAM_GRH_CAL_NEGOCIO."RCalendario.class.php";
include_once CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php";
include_once CAM_GRH_BEN_NEGOCIO."RBeneficioConcessaoValeTransporteSemanal.class.php";

class RBeneficioConcessaoValeTransporte
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
var $obTBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $obTBeneficioConcessaoValeTransporteCalendario;
/**
   * @access Private
   * @var Object
*/
var $obTBeneficioGrupoConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $obTBeneficioTipoConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $obTAdministracaoMes;
/**
   * @access Private
   * @var Array
*/
var $arRBeneficioConcessaoValeTransporteSemanal;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporteSemanal;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioContratoServidorConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioGrupoContrato;
/**
   * @access Private
   * @var Object
*/
var $obRCalendario;
/**
   * @access Private
   * @var Object
*/
var $obRBeneficioValeTransporte;
/**
   * @access Private
   * @var Integer
*/
var $inCodConcessao;
/**
   * @access Private
   * @var Integer
*/
var $inCodMes;
/**
   * @access Private
   * @var Boolean
*/
var $boInicializacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodTipo;
/**
   * @access Private
   * @var String
*/
var $stExercicio;
/**
   * @access Private
   * @var Integer
*/
var $inQuantidade;
/**
   * @access Private
   * @var Date
*/
var $dtVigencia;
/**
   * @access Private
   * @var Integer
*/
var $inCodMesFinal;
/**
   * @access Private
   * @var String
*/
var $stExercicioFinal;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioConcessaoValeTransporte($valor) { $this->obTBeneficioConcessaoValeTransporte                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioConcessaoValeTransporteCalendario($valor) { $this->obTBeneficioConcessaoValeTransporteCalendario      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioGrupoConcessaoValeTransporte($valor) { $this->obTBeneficioGrupoConcessaoValeTransporte           = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioTipoConcessaoValeTransporte($valor) { $this->obTBeneficioTipoConcessaoValeTransporte            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTAdministracaoMes($valor) { $this->obTAdministracaoMes                                = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRBeneficioConcessaoValeTransporteSemanal($valor) { $this->arRBeneficioConcessaoValeTransporteSemanal         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioConcessaoValeTransporteSemanal(&$valor) { $this->roRBeneficioConcessaoValeTransporteSemanal         = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioContratoServidorConcessaoValeTransporte(&$valor) { $this->roRBeneficioContratoServidorConcessaoValeTransporte= &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORBeneficioGrupoConcessao(&$valor) { $this->roRBeneficioGrupoConcessao                         = &$valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRCalendario($valor) { $this->obRCalendario                                      = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRBeneficioValeTransporte($valor) { $this->obRBeneficioValeTransporte                         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodConcessao($valor) { $this->inCodConcessao                                     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMes($valor) { $this->inCodMes                                           = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setInicializacao($valor) { $this->boInicializacao                                    = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo                                          = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio                                        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setQuantidade($valor) { $this->inQuantidade                                       = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setVigencia($valor) { $this->dtVigencia                                         = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodMesFinal($valor) { $this->inCodMesFinal                                      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setExercicioFinal($valor) { $this->stExercicioFinal                                   = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                        }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioConcessaoValeTransporte() { return $this->obTBeneficioConcessaoValeTransporte;                }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioConcessaoValeTransporteCalendario() { return $this->obTBeneficioConcessaoValeTransporteCalendario;      }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioGrupoConcessaoValeTransporte() { return $this->obTBeneficioGrupoConcessaoValeTransporte;           }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioTipoConcessaoValeTransporte() { return $this->obTBeneficioTipoConcessaoValeTransporte;            }
/**
    * @access Public
    * @return Object
*/
function getTAdministracaoMes() { return $this->obTAdministracaoMes;                                }
/**
    * @access Public
    * @return Array
*/
function getARRBeneficioConcessaoValeTransporteSemanal() { return $this->arRBeneficioConcessaoValeTransporteSemanal;         }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioConcessaoValeTransporteSemanal() { return $this->roRBeneficioConcessaoValeTransporteSemanal;         }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioContratoServidorConcessaoValeTransporte() { return $this->roRBeneficioContratoServidorConcessaoValeTransporte;        }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioGrupoContrato() { return $this->roRBeneficioGrupoContrato;                          }
/**
    * @access Public
    * @return Object
*/
function getRCalendario() { return $this->obRCalendario;                                      }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioValeTransporte() { return $this->obRBeneficioValeTransporte;                         }
/**
    * @access Public
    * @return Integer
*/
function getCodConcessao() { return $this->inCodConcessao;                                     }
/**
    * @access Public
    * @return Integer
*/
function getCodMes() { return $this->inCodMes;                                           }
/**
    * @access Public
    * @return Boolean
*/
function getInicializacao() { return $this->boInicializacao;                                    }
/**
    * @access Public
    * @return Integer
*/
function getCodTipo() { return $this->inCodTipo;                                          }
/**
    * @access Public
    * @return String
*/
function getExercicio() { return $this->stExercicio;                                        }
/**
    * @access Public
    * @return Integer
*/
function getQuantidade() { return $this->inQuantidade;                                       }
/**
    * @access Public
    * @return Date
*/
function getVigencia() { return $this->dtVigencia;                                         }
/**
    * @access Public
    * @return Integer
*/
function getCodMesFinal() { return $this->inCodMesFinal;                                      }
/**
    * @access Public
    * @return String
*/
function getExercicioFinal() { return $this->stExercicioFinal;                                   }

/**
     * Método construtor
     * @access Private
*/
function __construct()
{
    $this->setTransacao                                         ( new Transacao                                         );
    $this->setTBeneficioConcessaoValeTransporte                 ( new TBeneficioConcessaoValeTransporte                 );
    $this->setTBeneficioConcessaoValeTransporteCalendario       ( new TBeneficioConcessaoValeTransporteCalendario       );
    $this->setTBeneficioGrupoConcessaoValeTransporte            ( new TBeneficioGrupoConcessaoValeTransporte            );
    $this->setTBeneficioTipoConcessaoValeTransporte             ( new TBeneficioTipoConcessaoValeTransporte             );
    $this->setTAdministracaoMes                                 ( new TAdministracaoMes                                 );
    $this->setRCalendario                                       ( new RCalendario                                       );
    $this->setRBeneficioValeTransporte                          ( new RBeneficioValeTransporte                          );
    $this->setARRBeneficioConcessaoValeTransporteSemanal        ( array()                                               );
}

/**
    * Cadastra Concessao Vale-Transporte
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function incluirConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    //Validação de concessão já cadastrada para o contrato ou grupo
    if ( !$obErro->ocorreu() ) {
        $inCodConcessao         = $this->getCodConcessao();
        $inCodTipo              = $this->getCodTipo();
        $inQuantidade           = $this->getQuantidade();
        $this->setCodConcessao('');
        $this->setCodTipo('');
        $this->setQuantidade('');
        $obErro = $this->listarGruposCadastrados($rsConcessaoValeTransporte,$boTransacao);
        $this->setCodConcessao($inCodConcessao);
        $this->setCodTipo($inCodTipo);
        $this->setQuantidade($inQuantidade);
        if ( $rsConcessaoValeTransporte->getNumLinhas() > 0 ) {
            if ( is_object($this->roRBeneficioGrupoConcessao) ) {
                $obErro->setDescricao("Já existe uma concessão cadastrada com estas características para o grupo selecionado, para adicionar uma nova concessão para este grupo no mês corrente você deve alterar a concessão do grupo já existente.");
            } else {
                $obErro->setDescricao("Já existe uma concessão cadastrada com estas características para o contrato selecionado, para adicionar uma nova concessão para este contrato no mês corrente você deve alterar a concessão do contrato já existente.");
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $inCodTipo    = $this->getCodTipo();
        $inQuantidade = $this->getQuantidade();
        $inCodMes     = $this->getCodMes();
        $inExercicio  = $this->getExercicio();
        $this->setCodTipo('');
        $this->setQuantidade('');
        $this->setCodMes('');
        $this->setExercicio('');
        if ( is_object($this->roRBeneficioContratoServidorConcessaoValeTransporte) ) {
            $obErro = $this->listarConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
        } else {
            $obErro = $this->listarGrupoConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
        }
        $this->setCodTipo($inCodTipo);
        $this->setQuantidade($inQuantidade);
        $this->setCodMes($inCodMes);
        $this->setExercicio($inExercicio);
        if ( !$obErro->ocorreu() and $rsConcessaoValeTransporte->getNumLinhas() > 0 ) {
            $this->setCodConcessao($rsConcessaoValeTransporte->getCampo('cod_concessao'));
        }
        if ( !$this->getCodConcessao() ) {
            $inCampoCod         = $this->obTBeneficioConcessaoValeTransporte->getCampoCod();
            $inComplementoChave = $this->obTBeneficioConcessaoValeTransporte->getComplementoChave();
            $this->obTBeneficioConcessaoValeTransporte->setCampoCod( 'cod_concessao' );
            $this->obTBeneficioConcessaoValeTransporte->setComplementoChave( '' );
            $obErro = $this->obTBeneficioConcessaoValeTransporte->proximoCod($inCodConcessao,$boTransacao);
            $this->setCodConcessao( $inCodConcessao );
            $this->obTBeneficioConcessaoValeTransporte->setCampoCod( $inCampoCod );
            $this->obTBeneficioConcessaoValeTransporte->setComplementoChave( $inComplementoChave );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioConcessaoValeTransporte->setDado('cod_concessao',        $this->getCodConcessao()                                    );
            $this->obTBeneficioConcessaoValeTransporte->setDado('cod_mes',              $this->getCodMes()                                          );
            $this->obTBeneficioConcessaoValeTransporte->setDado('exercicio',            $this->getExercicio()                                       );
            $this->obTBeneficioConcessaoValeTransporte->setDado('cod_vale_transporte',  $this->obRBeneficioValeTransporte->getCodValeTransporte()   );
            $this->obTBeneficioConcessaoValeTransporte->setDado('cod_tipo',             $this->getCodTipo()                                         );
            $this->obTBeneficioConcessaoValeTransporte->setDado('quantidade',           $this->getQuantidade()                                      );
            $obErro = $this->obTBeneficioConcessaoValeTransporte->inclusao( $boTransacao );
        }
    }
    if ( $this->getCodTipo() == 2 ) {
        if ( !$obErro->ocorreu() ) {
            $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_mes',        $this->getCodMes()                          );
            $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('exercicio',      $this->getExercicio()                       );
            $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_concessao',  $this->getCodConcessao()                    );
            $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_calendario', $this->obRCalendario->getCodCalendar()      );
            $obErro = $this->obTBeneficioConcessaoValeTransporteCalendario->inclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporteSemanal);$inIndex++) {
                $obRBeneficioConcessaoValeTransporteSemanal = &$this->arRBeneficioConcessaoValeTransporteSemanal[$inIndex];
                $obErro = $obRBeneficioConcessaoValeTransporteSemanal->incluirConcessaoValeTransporteSemanal( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    if ( is_object($this->roRBeneficioGrupoConcessao) and !$obErro->ocorreu() ) {
        $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_grupo',       $this->roRBeneficioGrupoConcessao->getCodGrupo()    );
        $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao',   $this->getCodConcessao()                            );
        $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('exercicio',       $this->getExercicio()                               );
        $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_mes',         $this->getCodMes()                                  );
        $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('vigencia',        $this->getVigencia()                                );
        $obErro =  $this->obTBeneficioGrupoConcessaoValeTransporte->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporte );

    return $obErro;
}

/**
    * Alterar Concessao Vale-Transporte
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function alterarConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_concessao',        $this->getCodConcessao()                                    );
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_mes',              $this->getCodMes()                                          );
        $this->obTBeneficioConcessaoValeTransporte->setDado('exercicio',            $this->getExercicio()                                       );
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_vale_transporte',  $this->obRBeneficioValeTransporte->getCodValeTransporte()   );
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_tipo',             $this->getCodTipo()                                         );
        $this->obTBeneficioConcessaoValeTransporte->setDado('quantidade',           $this->getQuantidade()                                      );
        $obErro = $this->obTBeneficioConcessaoValeTransporte->alteracao( $boTransacao );

        if ( $this->getCodTipo() == 2 ) {
            if ( !$obErro->ocorreu() ) {
                $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_mes',        $this->getCodMes()                          );
                $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('exercicio',      $this->getExercicio()                       );
                $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_concessao',  $this->getCodConcessao()                    );
                $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_calendario', $this->obRCalendario->getCodCalendar()      );
                $obErro = $this->obTBeneficioConcessaoValeTransporteCalendario->alteracao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporteSemanal);$inIndex++) {
                    $obRBeneficioConcessaoValeTransporteSemanal = &$this->arRBeneficioConcessaoValeTransporteSemanal[$inIndex];
                    $obErro = $obRBeneficioConcessaoValeTransporteSemanal->alterarConcessaoValeTransporteSemanal( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
        if ( is_object($this->roRBeneficioGrupoConcessao) and !$obErro->ocorreu() ) {
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_grupo',       $this->roRBeneficioGrupoConcessao->getCodGrupo()    );
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao',   $this->getCodConcessao()                            );
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('exercicio',       $this->getExercicio()                               );
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_mes',         $this->getCodMes()                                  );
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('vigencia',        $this->getVigencia()                                );
            $obErro =  $this->obTBeneficioGrupoConcessaoValeTransporte->alteracao($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporte );

    return $obErro;
}
/**
    * Exclui Concessao Vale-Transporte
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function excluirConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( is_object($this->roRBeneficioGrupoConcessao) ) {
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao', $this->getCodConcessao()) ;
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_mes'      , $this->getCodMes());
            $this->obTBeneficioGrupoConcessaoValeTransporte->setDado('exercicio'    , $this->getExercicio());
            $obErro = $this->obTBeneficioGrupoConcessaoValeTransporte->exclusao( $boTransacao );
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->addRBeneficioConcessaoValeTransporteSemanal();
        $obRBeneficioConcessaoValeTransporteSemanal = &$this->arRBeneficioConcessaoValeTransporteSemanal[0];
        $obErro = $obRBeneficioConcessaoValeTransporteSemanal->excluirConcessaoValeTransporteSemanal($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_concessao',  $this->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('cod_mes',        $this->getCodMes());
        $this->obTBeneficioConcessaoValeTransporteCalendario->setDado('exercicio',      $this->getExercicio());
        $obErro = $this->obTBeneficioConcessaoValeTransporteCalendario->exclusao($boTransacao);
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_concessao',    $this->getCodConcessao());
        $this->obTBeneficioConcessaoValeTransporte->setDado('cod_mes',          $this->getCodMes());
        $this->obTBeneficioConcessaoValeTransporte->setDado('exercicio',        $this->getExercicio());
        $obErro = $this->obTBeneficioConcessaoValeTransporte->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioConcessaoValeTransporte );

    return $obErro;
}

/**
    * Adiciona um RBeneficioConcessaoValeTransporteSemanal ao array de referencia-objeto
    * @access Public
*/
function addRBeneficioConcessaoValeTransporteSemanal()
{
     $this->arRBeneficioConcessaoValeTransporteSemanal[] = new RBeneficioConcessaoValeTransporteSemanal ();
     $this->roRBeneficioConcessaoValeTransporteSemanal   = &$this->arRBeneficioConcessaoValeTransporteSemanal[count($this->arRBeneficioConcessaoValeTransporteSemanal) - 1 ];
     $this->roRBeneficioConcessaoValeTransporteSemanal->setRORBeneficioConcessaoValeTransporte( $this );
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaRelacionamento($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarConcessaoValeTransporte
    * @access Public
*/
function listarConcessaoValeTransporte(&$rsRecordSet,$boTransacao="")
{
    if ( $this->getCodConcessao() ) {
        $stFiltro .= " AND bt.cod_concessao = ".$this->getCodConcessao();
    }
    if ( $this->getCodMes() ) {
        $stFiltro .= " AND bt.cod_mes = ".$this->getCodMes();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND bt.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    if ( $this->getCodTipo() ) {
        $stFiltro .= " AND bt.cod_tipo = ".$this->getCodTipo();
    }
    if ( $this->getQuantidade() ) {
        $stFiltro .= " AND bt.quantidade = ".$this->getQuantidade();
    }
    if ( is_object($this->roRBeneficioContratoServidorConcessaoValeTransporte) ) {
        if ($this->roRBeneficioContratoServidorConcessaoValeTransporte->getCodContrato()) {
            $stFiltro .= " AND bv.cod_contrato = ".$this->roRBeneficioContratoServidorConcessaoValeTransporte->getCodContrato();
        }
    }
    $stOrder = "";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarMes
    * @access Public
*/
function listarMes(&$rsRecordSet,$boTransacao="")
{
    $obErro = $this->obTAdministracaoMes->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarTipo
    * @access Public
*/
function listarTipo(&$rsRecordSet,$boTransacao="")
{
    $obErro = $this->obTBeneficioTipoConcessaoValeTransporte->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarGrupoConcessaoValeTransporte
    * @access Public
*/
function listarGrupoConcessaoValeTransporte(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRBeneficioGrupoConcessao->getCodGrupo() ) {
        $stFiltro .= " AND cod_grupo = ".$this->roRBeneficioGrupoConcessao->getCodGrupo();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND bt.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    $stOrder = " dt_dia";
    $obErro = $this->obTBeneficioGrupoConcessaoValeTransporte->recuperaGrupoConcessaoValeTransporte($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    *Método listarConcessoesCadastradasPorGrupo
    *@access Public
*/
function listarConcessoesCadastradasPorGrupo(&$rsRecordSet,$boTransacao="")
{
    if ( $this->roRBeneficioGrupoConcessao->getCodGrupo() ) {
        $stFiltro .= " AND cod_grupo = ".$this->roRBeneficioGrupoConcessao->getCodGrupo();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodMes() ) {
        $stFiltro .= " AND cod_mes = ".$this->getCodMes();
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrder = "";
    $obErro = $this->obTBeneficioGrupoConcessaoValeTransporte->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    *Método listarConcessoesCadastradasPorContrato
    *@access Public
*/
function listarConcessoesCadastradasPorContrato(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    if ( $this->getCodConcessao() ) {
        $stFiltro .= " AND bt.cod_concessao = ".$this->getCodConcessao();
    }
    if ( $this->getCodMes() ) {
        $stFiltro .= " AND bt.cod_mes = ".$this->getCodMes();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND bt.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    if ( $this->getCodTipo() ) {
        $stFiltro .= " AND bt.cod_tipo = ".$this->getCodTipo();
    }
    if ( $this->getQuantidade() ) {
        $stFiltro .= " AND bt.quantidade = ".$this->getQuantidade();
    }
    if ( is_object( $this->roRBeneficioContratoServidorConcessaoValeTransporte ) ) {
        if ( $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro() ) {
            $stFiltro .= " AND bc.registro = ".$this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro();
        }
        if ( $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM() ) {
            $stFiltro .= " AND bc.numcgm = ".$this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->roPessoalServidor->obRCGMPessoaFisica->getNumCGM();
        }

    }
    if ( is_object( $this->roRBeneficioGrupoConcessao ) ) {
        if ( $this->roRBeneficioGrupoConcessao->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro() ) {
            $stFiltro .= " AND bc.registro = ".$this->roRBeneficioGrupoConcessao->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro();
        }

    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrder  = " registro,grupo,cod_mes,exercicio,vigencia";
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaConcessoesCadastradasPorContrato($rsRecordSet,$stFiltro,$stOrder.$boTransacao);

    return $obErro;
}

/**
    *Método listarGruposCadastrados
    *@access Public
*/
function listarGruposCadastrados(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    if ( $this->getCodConcessao() ) {
        $stFiltro .= " AND bt.cod_concessao = ".$this->getCodConcessao();
    }
    if ( $this->getCodMes() ) {
        $stFiltro .= " AND bt.cod_mes = ".$this->getCodMes();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND bt.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    if ( $this->getCodTipo() ) {
        $stFiltro .= " AND bt.cod_tipo = ".$this->getCodTipo();
    }
    if ( $this->getQuantidade() ) {
        $stFiltro .= " AND bt.quantidade = ".$this->getQuantidade();
    }
    if ( is_object( $this->roRBeneficioGrupoConcessao ) ) {
        if ( $this->roRBeneficioGrupoConcessao->getDescricao() ) {
            $stFiltro .= " AND UPPER(bg.descricao) like '%".strtoupper($this->roRBeneficioGrupoConcessao->getDescricao())."%'";
        }
        if ( $this->roRBeneficioGrupoConcessao->getCodGrupo() ) {
            $stFiltro .= " AND bg.cod_grupo = ".$this->roRBeneficioGrupoConcessao->getCodGrupo();
        }
        $stOrder .= " bg.descricao,bg.vigencia,";
    }
    if ( is_object( $this->roRBeneficioContratoServidorConcessaoValeTransporte ) ) {
        if ($this->roRBeneficioContratoServidorConcessaoValeTransporte->getCodContrato()) {
            $stFiltro .= " AND bg.cod_contrato = ".$this->roRBeneficioContratoServidorConcessaoValeTransporte->getCodContrato();
        }
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrder  = " bt.exercicio";
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaGruposCadastrados($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    *Método listarValesTransportesCadastrados
    *@access Public
*/
function listarValesTransportesCadastrados(&$rsRecordSet,$boTransacao="")
{
    $stFiltro = "";
    if ( $this->getCodConcessao() ) {
        $stFiltro .= " AND bt.cod_concessao = ".$this->getCodConcessao();
    }
    if ( $this->getCodMes() ) {
        $stFiltro .= " AND bt.cod_mes = ".$this->getCodMes();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND bt.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    if ( $this->getCodTipo() ) {
        $stFiltro .= " AND bt.cod_tipo = ".$this->getCodTipo();
    }
    if ( $this->getQuantidade() ) {
        $stFiltro .= " AND bt.quantidade = ".$this->getQuantidade();
    }
    if ( $this->obRBeneficioValeTransporte->getCodValeTransporte() ) {
        $stFiltro .= " AND bvt.cod_vale_transporte = ".$this->obRBeneficioValeTransporte->getCodValeTransporte();
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $stOrder  = " vale_transporte";
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaValesTransportesCadastrados($rsRecordSet,$stFiltro,$stOrder.$boTransacao);

    return $obErro;
}

/**
    * Busca os dados necessarios para inicializar uma concessao
    * @access Public
*/
function listarConcessaoValeTransporteInicializar(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaConcessaoValeTransporte($rsRecordSet, $stFiltro, "", $boTransacao);

    return $obErro;
}

/**
 * Lista os totais por fornecedor para relatório
 * @access Public
 */
function listarTotaisPorFornecedor(&$rsRecordSet,$boTransacao="")
{
    if ($inNumCGM = $this->obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->getNumCGM())
        $stFiltro .= " AND tabela.numcgm IN (".$inNumCGM.") \n";
    if ($inCodItinerario = $this->obRBeneficioValeTransporte->obRBeneficioItinerario->getCodItinerario())
        $stFiltro .= " AND tabela.cod_vale_transporte IN (".$inCodItinerario.") \n";
    if ($inCodOrgao = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaOrgao->getCodOrgao())
        $stFiltro .= " AND tabela.cod_orgao IN (".$inCodOrgao.") \n";
    if ($inCodLocal = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaLocal->getCodLocal())
       $stFiltro .= " AND tabela.cod_local IN (".$inCodLocal.") \n";
    if ($inExercicio = $this->getExercicio())
        $stFiltro .= " AND tabela.exercicio::integer IN (".$inExercicio.") \n";
    if ($inCodMes = $this->getCodMes())
        $stFiltro .= " AND tabela.cod_mes IN (".$inCodMes.") \n";
    if( $stFiltro )
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));

    $this->obTBeneficioConcessaoValeTransporte->setDado('dtVigencia',$this->getVigencia());
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaTotaisPorFornecedor($rsRecordSet, $stFiltro, "", $boTransacao);

    return $obErro;
}

/**
 * Usado no relatório de Concessão de Vale-Transporte
 * @access Public
 */
function listarConcessaoValeTransporteRelatorio(&$rsRecordSet,$boTransacao="")
{
    if ($inRegistro = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro())
        $stFiltro .= " AND tabela.registro = ".$inRegistro." \n";
    if ($inCodGrupo = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRBeneficioGrupoConcessao->getCodGrupo())
        $stFiltro .= " AND tabela.cod_grupo IN (".$inCodGrupo.") \n";
    if ($inCodOrgao = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaOrgao->getCodOrgao())
        $stFiltro .= " AND tabela.cod_orgao IN (".$inCodOrgao.") \n";
    if ($inCodLocal = $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->obROrganogramaLocal->getCodLocal())
        $stFiltro .= " AND tabela.cod_local IN (".$inCodLocal.") \n";
    if (($stExercicioFinal = $this->getExercicioFinal()) && ($inCodMesFinal = $this->getCodMesFinal())) {
        $stFiltro .= " AND tabela.exercicio||'-'||lpad(tabela.mes::varchar,2,'0') <= '".$stExercicioFinal."-".$inCodMesFinal."' \n";
        if (($stExercicio = $this->getExercicio()) && ($inCodMes = $this->getCodMes()))
            $stFiltro .= " AND tabela.exercicio||'-'||lpad(tabela.mes::varchar,2,'0') >= '".$stExercicio."-".$inCodMes."' \n";
    } elseif (($stExercicio = $this->getExercicio()) && ($inCodMes = $this->getCodMes())) {
        $stFiltro .= " AND tabela.mes = ".$inCodMes."            \n";
        $stFiltro .= " AND tabela.exercicio = '".$stExercicio."' \n";
    }
    $stOrder = " ORDER BY tabela.numcgm, tabela.registro, tabela.cod_grupo ";
    $obErro = $this->obTBeneficioConcessaoValeTransporte->recuperaConcessaoValeTransporteRelatorio($rsRecordSet, $stFiltro, $stOrder, $boTransacao);

    return $obErro;
}

/**
    * Faz a inclusao de inicialização de vale-transporte
    * @access Public
    * @param Object $rsVigencia      Parametro de entrada de dados da concessão vigente
    * @param Array  $boTransacao     Transação
*/
function incluirInicializacaoValeTransporte($rsVigencia,$boTransacao="")
{
    //Lista os tipos de vale-transporte
    $this->listarTipo($rsTipo,$boTransacao);
    $arTipo = $rsTipo->getElementos();

    //Consulta os dados da concessao vigente
    $stFiltro  = " WHERE bcvt.cod_concessao = ".$rsVigencia->getCampo('cod_concessao')."  \n";
    $stFiltro .= "   AND bcvt.exercicio = '".$rsVigencia->getCampo('exercicio')."'        \n";
    $stFiltro .= "   AND bcvt.cod_mes = ".$rsVigencia->getCampo('cod_mes')."              \n";
    $obErro = $this->listarConcessaoValeTransporteInicializar($rsConcessaoVigente, $stFiltro, "", $boTransacao);

    if (!$obErro->ocorreu()) {
        $obTBeneficioConcessaoValeTransporte = new TBeneficioConcessaoValeTransporte;
        $obTBeneficioConcessaoValeTransporte->setDado('cod_concessao'      , $rsConcessaoVigente->getCampo('cod_concessao')             );
        $obTBeneficioConcessaoValeTransporte->setDado('exercicio'          , $this->getExercicio()                                      );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_mes'            , $this->getCodMes()                                         );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_vale_transporte', $rsConcessaoVigente->getCampo('cod_vale_transporte')       );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_tipo'           , $rsConcessaoVigente->getCampo('cod_tipo')                  );
        $obTBeneficioConcessaoValeTransporte->setDado('quantidade'         , $rsConcessaoVigente->getCampo('quantidade')                );
        $obTBeneficioConcessaoValeTransporte->setDado('inicializado'       , 'true'                                                     );
        $obErro = $obTBeneficioConcessaoValeTransporte->inclusao($boTransacao);
    }

    if (!$obErro->ocorreu()) {
        if ($rsVigencia->getCampo('cod_contrato')) {
            $obTBeneficioContratoServidorConcessaoValeTransporte = new TBeneficioContratoServidorConcessaoValeTransporte;
            $obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao', $rsConcessaoVigente->getCampo('cod_concessao')             );
            $obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio'    , $this->getExercicio() );
            $obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes'      , $this->getCodMes()    );
            $obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_contrato' , $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getCodContrato() );
            $obTBeneficioContratoServidorConcessaoValeTransporte->setDado('vigencia'     , $rsVigencia->getCampo('vigencia')                          );
            $obErro = $obTBeneficioContratoServidorConcessaoValeTransporte->inclusao($boTransacao);
        } elseif ($rsVigencia->getCampo('cod_grupo')) {
            $obTBeneficioGrupoConcessaoValeTransporte = new TBeneficioGrupoConcessaoValeTransporte;
            $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_concessao', $rsConcessaoVigente->getCampo('cod_concessao')             );
            $obTBeneficioGrupoConcessaoValeTransporte->setDado('exercicio'    , $this->getExercicio() );
            $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_mes'      , $this->getCodMes()    );
            $obTBeneficioGrupoConcessaoValeTransporte->setDado('cod_grupo'    , $this->roRBeneficioGrupoConcessao->getCodGrupo() );
            $obTBeneficioGrupoConcessaoValeTransporte->setDado('vigencia'     , $rsVigencia->getCampo('vigencia')                          );
            $obErro = $obTBeneficioGrupoConcessaoValeTransporte->inclusao($boTransacao);
        }
    }

    if (!$obErro->ocorreu() && $rsConcessaoVigente->getCampo('cod_dia')) {
        //Insere a inicializacao semanal.
        $obTBeneficioConcessaoValeTransporteSemanal = new TBeneficioConcessaoValeTransporteSemanal;
        while ( !$rsConcessaoVigente->eof() ) {
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_concessao' , $rsConcessaoVigente->getCampo('cod_concessao')             );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('exercicio'     , $this->getExercicio() );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_dia'       , $rsConcessaoVigente->getCampo('cod_dia')                   );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_mes'       , $this->getCodMes()    );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('quantidade'    , $rsConcessaoVigente->getCampo('qtd_dia')                   );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('obrigatorio'   , $rsConcessaoVigente->getCampo('obrigatorio')               );
            $obErro = $obTBeneficioConcessaoValeTransporteSemanal->inclusao($boTransacao);
            $rsConcessaoVigente->proximo();
        }
        $rsConcessaoVigente->setPrimeiroElemento();
        $arConcessaoVigente = $rsConcessaoVigente->getElementos();

        if (!$obErro->ocorreu()) {
            //Insere a inicializacao diaria.
            $obCalendario = new Calendario;
            $inMes = $this->getCodMes();
            $inMes = (strlen($inMes) == 1) ? '0'.$inMes : $inMes;
            $inAno = $this->getExercicio();
            $inDiasMes = $obCalendario->retornaUltimoDiaMes($inMes,$inAno);
            $dtInicial = "01/".$inMes."/".$inAno;
            $dtFinal   = $inDiasMes."/".$inMes."/".$inAno;
            //Busca os feriados do periodo inicializado
            $this->obRCalendario->setCodCalendar( $rsConcessaoVigente->getCampo('cod_calendario') );
            $this->obRCalendario->addFeriadoVariavel();
            $this->obRCalendario->ultimoFeriadoVariavel->setDtInicial($dtInicial);
            $this->obRCalendario->ultimoFeriadoVariavel->setDtFinal($dtFinal);
            $obErro = $this->obRCalendario->listarFeriados( $rsFeriados , "" , $boTransacao );
            //Monta array com os dias que sao feriado.
            $arFeriados = array();
            while (!$rsFeriados->eof()) {
                $arFeriados[] = substr($rsFeriados->getCampo('dt_feriado'),0,2);
                $rsFeriados->proximo();
            }
            $inTotalVales = 0;
            $obTBeneficioConcessaoValeTransporteDiario = new TBeneficioConcessaoValeTransporteDiario;
            for ($inDia=1;$inDia<=$inDiasMes;$inDia++) {
                $inDiaSemana = (int) $obCalendario->retornaDiaSemana($inDia,$inMes,$inAno) + 1;
                $inDia2 = (strlen($inDia) == 1) ? '0'.$inDia : $inDia;
                if (!in_array($inDia,$arFeriados) || $arConcessaoVigente[$inDiaSemana-1]['obrigatorio'] == 't') {
                    $inTotalVales += $arConcessaoVigente[$inDiaSemana-1]['qtd_dia'];
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('cod_concessao' , $rsConcessaoVigente->getCampo('cod_concessao')             );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('exercicio'     , $this->getExercicio() );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('cod_mes'       , $this->getCodMes()    );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('cod_dia'       , $inDiaSemana                                               );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('dt_dia'        , $inDia2."/".$inMes."/".$inAno                              );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('obrigatorio'   , $arConcessaoVigente[$inDiaSemana-1]['obrigatorio']         );
                    $obTBeneficioConcessaoValeTransporteDiario->setDado('quantidade'    , $arConcessaoVigente[$inDiaSemana-1]['qtd_dia']             );
                    $obErro = $obTBeneficioConcessaoValeTransporteDiario->inclusao($boTransacao);
                }
            }
        }
    }

    $inTotalVales = (isset($inTotalVales)) ? $inTotalVales :  $rsConcessaoVigente->getCampo('quantidade');

    //Atualiza total de vales na tabela concessao_vale_transporte
    if (!$obErro->ocorreu()) {
        $obTBeneficioConcessaoValeTransporte = new TBeneficioConcessaoValeTransporte;
        $obTBeneficioConcessaoValeTransporte->setDado('cod_concessao'      , $rsConcessaoVigente->getCampo('cod_concessao')             );
        $obTBeneficioConcessaoValeTransporte->setDado('exercicio'          , $this->getExercicio()                                      );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_mes'            , $this->getCodMes()                                         );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_vale_transporte', $rsConcessaoVigente->getCampo('cod_vale_transporte')       );
        $obTBeneficioConcessaoValeTransporte->setDado('cod_tipo'           , $rsConcessaoVigente->getCampo('cod_tipo')                  );
        $obTBeneficioConcessaoValeTransporte->setDado('quantidade'         , $inTotalVales                                              );
        $obTBeneficioConcessaoValeTransporte->setDado('inicializado'       , 'true'                                                     );
        $obErro = $obTBeneficioConcessaoValeTransporte->alteracao($boTransacao);
    }

    if ($rsVigencia->getCampo('cod_contrato')) {

        $arContratoInicializado = array (
            'contrato'   => $this->roRBeneficioContratoServidorConcessaoValeTransporte->obRPessoalContratoServidor->getRegistro() ,
            'concessao'  => $rsConcessaoVigente->getCampo('cod_concessao')                    ,
            'tipo'       => $arTipo[$rsConcessaoVigente->getCampo('cod_tipo')-1]['descricao'] ,
            'mes'        => $this->getCodMes()           ,
            'ano'        => $this->getExercicio()        ,
            'quantidade' => $inTotalVales
             );

        $arSessaoContratosInicializados   = Sessao::read('arContratosInicializados');
        $arSessaoContratosInicializados[] = $arContratoInicializado;
        Sessao::write('arContratosInicializados', $arSessaoContratosInicializados);

    } elseif ($rsVigencia->getCampo('cod_grupo')) {
        $this->roRBeneficioGrupoConcessao->listarGrupoConcessao($rsGrupo,$boTransacao);

        $arGrupoInicializado = array (
           'grupo'      => trim($rsGrupo->getCampo('descricao')),
           'concessao'  => $rsConcessaoVigente->getCampo('cod_concessao')                    ,
           'tipo'       => $arTipo[$rsConcessaoVigente->getCampo('cod_tipo')-1]['descricao'] ,
           'mes'        => $this->getCodMes()           ,
           'ano'        => $this->getExercicio()        ,
           'quantidade' => $inTotalVales
            );

        $arSessaoGruposInicializados   = Sessao::read('arGruposInicializados');
        $arSessaoGruposInicializados[] = $arGrupoInicializado;
        Sessao::write('arGruposInicializados', $arSessaoGruposInicializados);

    }

    return $obErro;
}

/**
    * Faz a exclusao de inicialização de vale-transporte
    * @access Public
    * @param Object $boTransacao Transação
*/
function excluirAssociacao($boTransacao="")
{
    $obErro = new Erro;
    if ($this->getCodConcessao() && $this->getExercicio() && $this->getCodMes()) {
        $obTBeneficioConcessaoValeTransporteDiario = new TBeneficioConcessaoValeTransporteDiario;
        $obTBeneficioConcessaoValeTransporteDiario->setComplementoChave('cod_concessao,exercicio,cod_mes');
        $obTBeneficioConcessaoValeTransporteDiario->setDado('cod_concessao',$this->getCodConcessao());
        $obTBeneficioConcessaoValeTransporteDiario->setDado('exercicio'    ,$this->getExercicio()   );
        $obTBeneficioConcessaoValeTransporteDiario->setDado('cod_mes'      ,$this->getCodMes()      );
        $obErro = $obTBeneficioConcessaoValeTransporteDiario->exclusao($boTransacao);
        if (!$obErro->ocorreu()) {
            $obTBeneficioConcessaoValeTransporteSemanal = new TBeneficioConcessaoValeTransporteSemanal;
            $obTBeneficioConcessaoValeTransporteSemanal->setComplementoChave('cod_concessao,exercicio,cod_mes');
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_concessao',$this->getCodConcessao());
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('exercicio'    ,$this->getExercicio()   );
            $obTBeneficioConcessaoValeTransporteSemanal->setDado('cod_mes'      ,$this->getCodMes()      );
            $obErro = $obTBeneficioConcessaoValeTransporteSemanal->exclusao($boTransacao);
        }
        if (!$obErro->ocorreu()) {
            $obTBeneficioConcessaoValeTransporte = new TBeneficioConcessaoValeTransporte;
            $obTBeneficioConcessaoValeTransporte->setDado('cod_concessao',$this->getCodConcessao());
            $obTBeneficioConcessaoValeTransporte->setDado('exercicio'    ,$this->getExercicio()   );
            $obTBeneficioConcessaoValeTransporte->setDado('cod_mes'      ,$this->getCodMes()      );
            $obErro = $obTBeneficioConcessaoValeTransporte->exclusao($boTransacao);
        }
    }

    return $obErro;
}

}
?>
