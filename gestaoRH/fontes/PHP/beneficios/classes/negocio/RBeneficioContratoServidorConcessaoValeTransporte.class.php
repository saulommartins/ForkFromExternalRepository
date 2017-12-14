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
* Classe de regra de negócio para RBeneficioContratoServidorConcessaoValeTransporte
* Data de Criação: 13/10/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: tiago $
$Date: 2007-06-28 14:46:50 -0300 (Qui, 28 Jun 2007) $

* Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioContratoServidorGrupoConcessaoValeTransporte.class.php"  );
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioContratoServidorConcessaoValeTransporte.class.php"       );
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporte.class.php"                       );
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteSemanal.class.php"                );
include_once (CAM_GRH_BEN_MAPEAMENTO."TBeneficioConcessaoValeTransporteDiario.class.php"                 );
include_once (CAM_GRH_PES_NEGOCIO."RPessoalContratoServidor.class.php"                                   );
include_once (CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php"                                           );
include_once (CAM_GRH_BEN_NEGOCIO."RBeneficioConcessaoValeTransporte.class.php"                          );
include_once (CAM_GRH_BEN_NEGOCIO."RBeneficioGrupoConcessao.class.php"                                   );

class RBeneficioContratoServidorConcessaoValeTransporte
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
var $obTBeneficioContratoServidorGrupoConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $obTBeneficioContratoServidorConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $obRBeneficioGrupoConcessao;
/**
   * @access Private
   * @var Object
*/
var $obRPessoalContratoServidor;
/**
   * @access Private
   * @var Array
*/
var $arRBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Object
*/
var $roRBeneficioConcessaoValeTransporte;
/**
   * @access Private
   * @var Integer
*/
var $inCodContrato;
/**
   * @access Private
   * @var Boolean
*/
var $boUtilizarGrupo;
/**
   * @access Private
  * @var Boolean
*/
var $stTipoConcessao;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao                                               = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioContratoServidorGrupoConcessaoValeTransporte($valor) { $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte  = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTBeneficioContratoServidorConcessaoValeTransporte($valor) { $this->obTBeneficioContratoServidorConcessaoValeTransporte       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRBeneficioGrupoConcessao($valor) { $this->obRBeneficioGrupoConcessao                                = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalContratoServidor($valor) { $this->obRPessoalContratoServidor                                = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setARRBeneficioConcessaoValeTransporte($valor) { $this->arRBeneficioConcessaoValeTransporte                       = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setORRBeneficioConcessaoValeTransporte(&$valor) { $this->roRBeneficioConcessaoValeTransporte                       = &$valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodContrato($valor) { $this->inCodContrato                                             = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setUtilizarGrupo($valor) { $this->boUtilizarGrupo                                            = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipoConcessao($valor) { $this->stTipoConcessao                                            = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;                                               }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioContratoServidorGrupoConcessaoValeTransporte() { return $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte;  }
/**
    * @access Public
    * @return Object
*/
function getTBeneficioContratoServidorConcessaoValeTransporte() { return $this->obTBeneficioContratoServidorConcessaoValeTransporte;       }
/**
    * @access Public
    * @return Object
*/
function getRBeneficioGrupoConcessao() { return $this->obRBeneficioGrupoConcessao;                                }
/**
    * @access Public
    * @return Object
*/
function getRPessoalContratoServidor() { return $this->obRPessoalContratoServidor;                                }
/**
    * @access Public
    * @return Array
*/
function getARRBeneficioConcessaoValeTransporte() { return $this->arRBeneficioConcessaoValeTransporte;                       }
/**
    * @access Public
    * @return Object
*/
function getRORBeneficioConcessaoValeTransporte() { return $this->roRBeneficioConcessaoValeTransporte;                       }
/**
    * @access Public
    * @return Integer
*/
function getCodContrato() { return $this->inCodContrato;                                             }
/**
    * @access Public
    * @return Boolean
*/
function getUtilizarGrupo() { return $this->boUtilizarGrupo;                                           }
/**
    * @access Public
    * @return String
*/
function getTipoConcessao() { return $this->stTipoConcessao;                                           }

/**
     * Método construtor
     * @access Private
*/
function RBeneficioContratoServidorConcessaoValeTransporte()
{
    $this->setTransacao                                              ( new Transacao                                                );
    $this->setTBeneficioContratoServidorGrupoConcessaoValeTransporte ( new TBeneficioContratoServidorGrupoConcessaoValeTransporte   );
    $this->setTBeneficioContratoServidorConcessaoValeTransporte      ( new TBeneficioContratoServidorConcessaoValeTransporte        );
    $this->setRPessoalContratoServidor                               ( new RPessoalContratoServidor(new RPessoalServidor)           );
    $this->setRBeneficioGrupoConcessao                               ( new RBeneficioGrupoConcessao                                 );
    $this->setARRBeneficioConcessaoValeTransporte                    ( array()                                                      );
}

/**
    * Adiciona um RBeneficioConcessaoValeTransporte ao array de referencia-objeto
    * @access Public
*/
function addRBeneficioConcessaoValeTransporte()
{
     $this->arRBeneficioConcessaoValeTransporte[] = new RBeneficioConcessaoValeTransporte ();
     $this->roRBeneficioConcessaoValeTransporte   = &$this->arRBeneficioConcessaoValeTransporte[ count($this->arRBeneficioConcessaoValeTransporte) - 1 ];
     $this->roRBeneficioConcessaoValeTransporte->setRORBeneficioContratoServidorConcessaoValeTransporte( $this );
}

/**
    * Inclusão
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function incluirContratoServidorConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        if ( $this->getUtilizarGrupo() ) {
            $obRBeneficioConcessaoValeTransporte = &$this->obRBeneficioGrupoConcessao->arRBeneficioConcessaoValeTransporte[0];
            if ( $this->getTipoConcessao() == 'contrato' or $this->getTipoConcessao() == 'cgm_contrato' ) {
                $obErro = $this->obRPessoalContratoServidor->listarContratosServidorResumido($rsContrato,$boTransacao);
                $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));
                if ( $rsContrato->getNumLinhas() < 0 and !$obErro->ocorreu() ) {
                    $obErro->setDescricao("Número do contrato inválido, informe um número de contrato válido." );
                }
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = $this->listarContratoServidorGrupoConcessaoValeTransporte($rsContratosGrupo,$boTransacao);
                if ( !$obErro->ocorreu() and $rsContratosGrupo->getNumLinhas() > 0 ) {
                    $obErro->setDescricao("O grupo selecionado já está cadastrado no contrato ".$_POST['inRegistro'].".");
                }
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_grupo',       $this->obRBeneficioGrupoConcessao->getCodGrupo()        );
                $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_contrato',    $this->obRPessoalContratoServidor->getCodContrato()     );
                $obErro = $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->inclusao( $boTransacao );
            }
        } else {
            for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporte);$inIndex++) {

                $obRBeneficioConcessaoValeTransporte = &$this->arRBeneficioConcessaoValeTransporte[$inIndex];

                if ( $this->getTipoConcessao() == 'contrato' or $this->getTipoConcessao() == 'cgm_contrato' ) {
                    $obErro = $this->obRPessoalContratoServidor->listarContratosServidorResumido($rsContrato,$boTransacao);
                    $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));

                    if ( !$obErro->ocorreu() ) {
                        if ( $rsContrato->getNumLinhas() < 0 ) {
                            $obErro->setDescricao("Número do contrato inválido, informe um número de contrato válido." );
                        }
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $inCodTipo    = $obRBeneficioConcessaoValeTransporte->getCodTipo();
                    $inQuantidade = $obRBeneficioConcessaoValeTransporte->getQuantidade();
                    $obRBeneficioConcessaoValeTransporte->setCodTipo('');
                    $obRBeneficioConcessaoValeTransporte->setQuantidade('');
                    $obRBeneficioConcessaoValeTransporte->roRBeneficioContratoServidorConcessaoValeTransporte->setCodContrato($this->obRPessoalContratoServidor->getCodContrato());
                    $obErro = $obRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
                    $obRBeneficioConcessaoValeTransporte->setCodTipo($inCodTipo);
                    $obRBeneficioConcessaoValeTransporte->setQuantidade($inQuantidade);
                }
                if ( !$obErro->ocorreu() ) {
                    if ( $rsConcessaoValeTransporte->getNumLinhas() > 0 ) {
                        $obErro->setDescricao("Já existe uma concessão cadastrada com estas características (linha ".($inIndex+1).") para o contrato selecionado, para adicionar uma nova concessão para este contrato no mês corrente você deve alterar a concessão do contrato já existente.");
                    }
                }

                $inCodConcessao;
                $boAchouCodConcessao = false;

                if ( !$obErro->ocorreu() ) {
                    $inCodTipo    = $obRBeneficioConcessaoValeTransporte->getCodTipo();
                    $inQuantidade = $obRBeneficioConcessaoValeTransporte->getQuantidade();
                    $inCodMes     = $obRBeneficioConcessaoValeTransporte->getCodMes();
                    $inExercicio  = $obRBeneficioConcessaoValeTransporte->getExercicio();
                    $obRBeneficioConcessaoValeTransporte->setCodTipo('');
                    $obRBeneficioConcessaoValeTransporte->setQuantidade('');
                    $obRBeneficioConcessaoValeTransporte->setCodMes('');
                    $obRBeneficioConcessaoValeTransporte->setExercicio('');
                    if ( is_object($obRBeneficioConcessaoValeTransporte->roRBeneficioContratoServidorConcessaoValeTransporte) ) {
                        $obErro = $obRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
                    } else {
                        $obErro = $obRBeneficioConcessaoValeTransporte->listarGrupoConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
                    }
                    $obRBeneficioConcessaoValeTransporte->setCodTipo($inCodTipo);
                    $obRBeneficioConcessaoValeTransporte->setQuantidade($inQuantidade);
                    $obRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
                    $obRBeneficioConcessaoValeTransporte->setExercicio($inExercicio);
                    if ( !$obErro->ocorreu() and $rsConcessaoValeTransporte->getNumLinhas() > 0 ) {
                        $boAchouCodConcessao = true;
                        $inCodConcessao = $rsConcessaoValeTransporte->getCampo('cod_concessao');
                        $obRBeneficioConcessaoValeTransporte->setCodConcessao($inCodConcessao);
                    }
                }
                if ( !$obErro->ocorreu() and $boAchouCodConcessao) {

                   $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado( 'cod_concessao', $inCodConcessao );
                   $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado( 'cod_contrato', $rsContrato->getCampo('cod_contrato') );
                   $this->obTBeneficioContratoServidorConcessaoValeTransporte->recuperaUltimaVigenciaConcessao( $rsUltimaVigencia, $boTransacao  );

                   if ( $rsUltimaVigencia->getNumLinhas() > 0 ) {

                        $arDataUltimaVigencia = explode ('-', $rsUltimaVigencia->getCampo('max_vigencia') );
                        $arDataUltimaVigencia = ($arDataUltimaVigencia[0].$arDataUltimaVigencia[1].$arDataUltimaVigencia[2]);

                        $arDataVigencia = explode('/', $obRBeneficioConcessaoValeTransporte->getVigencia());
                        $arDataVigencia = $arDataVigencia[2].$arDataVigencia[1].$arDataVigencia[0];

                        if ( (int) $arDataVigencia <= (int) $arDataUltimaVigencia ) {
                            $obErro->setDescricao('A vigência deve ser maior do que a última vigência cadastrada para esta concessão. ');
                        }
                   }

                }

                if ( !$obErro->ocorreu() ) {
                    // inclui em BeneficioConcessaoValeTransporte
                    $obErro = $obRBeneficioConcessaoValeTransporte->incluirConcessaoValeTransporte($boTransacao);
                }

                if ( !$obErro->ocorreu() ) {

                    $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_contrato',  $this->obRPessoalContratoServidor->getCodContrato()     );
                    $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes',       $obRBeneficioConcessaoValeTransporte->getCodMes()       );
                    $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao', $obRBeneficioConcessaoValeTransporte->getCodConcessao() );
                    $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio',     $obRBeneficioConcessaoValeTransporte->getExercicio()    );
                    $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('vigencia',      $obRBeneficioConcessaoValeTransporte->getVigencia()     );

                    $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->inclusao($boTransacao);
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obRPessoalContratoServidor->listarContratosServidorResumido($rsContratoServidor,$boTransacao);
        $this->obRPessoalContratoServidor->setRegistro( $rsContratoServidor->getCampo('registro') );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte);

    return $obErro;
}

/**
    * Alteração
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function alterarContratoServidorConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        if ( $this->getUtilizarGrupo() ) {

            if ( $this->getTipoConcessao() == 'contrato' or $this->getTipoConcessao() == 'cgm_contrato' ) {
                if ( $this->obRPessoalContratoServidor->getRegistro() == "" ) {
                    $this->obRPessoalContratoServidor->setRegistro( $this->obRPessoalContratoServidor->getCodContrato() );
                    $this->obRPessoalContratoServidor->setCodContrato('');
                }
                $obErro = $this->obRPessoalContratoServidor->listarContratosServidorResumido($rsContrato,$boTransacao);
                $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));
                $this->obRPessoalContratoServidor->setRegistro($rsContrato->getCampo('registro'));
                $this->obRPessoalContratoServidor->setQuantidade( $inQuantidade );
            }
            for ($inIndex=0;$inIndex<count($this->obRBeneficioGrupoConcessao->arRBeneficioConcessaoValeTransporte);$inIndex++) {
                $obRBeneficioConcessaoValeTransporte = &$this->obRBeneficioGrupoConcessao->arRBeneficioConcessaoValeTransporte[$inIndex];
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->listarContratoServidorGrupoConcessaoValeTransporte($rsContratoServidorGrupoConcessaoValeTransporte,$boTransacao);

                }
                if ( $rsContratoServidorGrupoConcessaoValeTransporte->getNumLinhas() > 0 and !$obErro->ocorreu() ) {
                    $obErro->setDescricao("Já existe uma concessão cadastrada com estas características para o grupo selecionado, para adicionar uma nova concessão para este grupo no mês corrente você deve alterar a concessão do grupo já existente.");
                }
                if ( !$obErro->ocorreu() ) {
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_concessao',   $obRBeneficioConcessaoValeTransporte->getCodConcessao() );
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_grupo',       $this->obRBeneficioGrupoConcessao->getCodGrupo()        );
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('exercicio',       $obRBeneficioConcessaoValeTransporte->getExercicio()    );
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_mes',         $obRBeneficioConcessaoValeTransporte->getCodMes()       );
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_contrato',    $this->obRPessoalContratoServidor->getCodContrato()     );
                    $obErro = $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->inclusao( $boTransacao );
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        } else {
            //Processamento das Concessões no caso de concessoes sem a utilização de grupo
            for ($inIndex=0;$inIndex<count($this->arRBeneficioConcessaoValeTransporte);$inIndex++) {

                //Busca do número do contrato baseado no número do registro
                if ( $this->getTipoConcessao() == 'contrato' or $this->getTipoConcessao() == 'cgm_contrato' ) {
                    if ( $this->obRPessoalContratoServidor->getRegistro() == "" ) {
                        $this->obRPessoalContratoServidor->setRegistro( $this->obRPessoalContratoServidor->getCodContrato() );
                        $this->obRPessoalContratoServidor->setCodContrato('');
                    }
                    $obErro = $this->obRPessoalContratoServidor->listarContratosServidorResumido($rsContrato,$boTransacao);
                    $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));
                }

                $obRBeneficioConcessaoValeTransporte = &$this->arRBeneficioConcessaoValeTransporte[$inIndex];
                $obRBeneficioConcessaoValeTransporte->roRBeneficioContratoServidorConcessaoValeTransporte->roRBeneficioConcessaoValeTransporte =  &$obRBeneficioConcessaoValeTransporte->roRBeneficioContratoServidorConcessaoValeTransporte->arRBeneficioConcessaoValeTransporte[$inIndex] ;

                if ( $obRBeneficioConcessaoValeTransporte->getCodConcessao() != "" ) {
                    //Alteração, caso a concessão já exista
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $obRBeneficioConcessaoValeTransporte->alterarConcessaoValeTransporte( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_contrato',     $this->obRPessoalContratoServidor->getCodContrato()         );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes',          $obRBeneficioConcessaoValeTransporte->getCodMes()           );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao',    $obRBeneficioConcessaoValeTransporte->getCodConcessao()     );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio',        $obRBeneficioConcessaoValeTransporte->getExercicio()        );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('vigencia',         $obRBeneficioConcessaoValeTransporte->getVigencia()         );
                        $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->alteracao($boTransacao);
                    }
                } else {

                    //Inclusão, caso a concessao não exista
                    if ( !$obErro->ocorreu() ) {
                        $inCodTipo    = $obRBeneficioConcessaoValeTransporte->getCodTipo();
                        $inQuantidade = $obRBeneficioConcessaoValeTransporte->getQuantidade();
                        $obRBeneficioConcessaoValeTransporte->setCodTipo('');
                        $obRBeneficioConcessaoValeTransporte->setQuantidade('');
                        $obRBeneficioConcessaoValeTransporte->roRBeneficioContratoServidorConcessaoValeTransporte->setCodContrato($this->obRPessoalContratoServidor->getCodContrato());
                        $obErro = $obRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporte($rsConcessaoValeTransporte,$boTransacao);
                        $obRBeneficioConcessaoValeTransporte->setCodTipo($inCodTipo);
                        $obRBeneficioConcessaoValeTransporte->setQuantidade($inQuantidade);
                    }
                    if ( !$obErro->ocorreu() ) {
                        if ( $rsConcessaoValeTransporte->getNumLinhas() > 0 ) {
                            $obErro->setDescricao("Já existe uma concessão cadastrada com estas características para o contrato selecionado, para adicionar uma nova concessão para este contrato no mês corrente você deve alterar a concessão do contrato já existente.");
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $obRBeneficioConcessaoValeTransporte->incluirConcessaoValeTransporte($boTransacao);
                    }
                    if ( !$obErro->ocorreu() ) {
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_contrato',     $this->obRPessoalContratoServidor->getCodContrato()         );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes',          $obRBeneficioConcessaoValeTransporte->getCodMes()           );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao',    $obRBeneficioConcessaoValeTransporte->getCodConcessao()     );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio',        $obRBeneficioConcessaoValeTransporte->getExercicio()        );
                        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('vigencia',         $obRBeneficioConcessaoValeTransporte->getVigencia()         );
                        $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->inclusao($boTransacao);
                    }

                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte);

    return $obErro;
}

/**
    * Exclui ContratoServidorConcessaoVale-Transporte
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação, caso esta exista
    * @return Object Objeto Erro retorna o valor, validando o método
    *
**/
function excluirContratoServidorConcessaoValeTransporte($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( is_object($this->roRBeneficioConcessaoValeTransporte) ) {
            $inCodConcessao = $this->roRBeneficioConcessaoValeTransporte->getCodConcessao();
            $inCodMes       = $this->roRBeneficioConcessaoValeTransporte->getCodMes();
            $inExercicio    = $this->roRBeneficioConcessaoValeTransporte->getExercicio();
            if ( !$obErro->ocorreu() ) {
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao',    $inCodConcessao);
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes',          $inCodMes);
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio',        $inExercicio);
                $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->exclusao($boTransacao);
            }
            if ( !$obErro->ocorreu() ) {
                $this->roRBeneficioConcessaoValeTransporte->addRBeneficioConcessaoValeTransporteSemanal();
                $obErro = $this->roRBeneficioConcessaoValeTransporte->excluirConcessaoValeTransporte($boTransacao);
            }
        }
        if ( is_object($this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte) ) {
            $inCodContrato  = $this->obRPessoalContratoServidor->getCodContrato();
            $inCodGrupo     = $this->obRBeneficioGrupoConcessao->getCodGrupo();
            if ( !$this->getUtilizarGrupo() ) {
                if ( !$obErro->ocorreu() ) {
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_grupo',        $inCodGrupo);
                    $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->setDado('cod_contrato',     $inCodContrato);
                    $obErro = $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->exclusao($boTransacao);
                }
            } else {
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->obRBeneficioGrupoConcessao->excluirGrupoConcessao($boTransacao);
                }
            }
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratoServidorConcessaoValeTransporte
    * @access Public
*/
function listarContratoServidorConcessaoValeTransporte(&$rsRecordSet,$boTransacao="")
{
    if ( $this->obRPessoalContratoServidor->getCodContrato() ) {
        $stFiltro .= " AND cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
    }
    if ( is_object( $this->roRBeneficioConcessaoValeTransporte ) ) {
        if ( $this->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
            $stFiltro .= " AND cod_mes = ".$this->roRBeneficioConcessaoValeTransporte->getCodMes();
        }
        if ( $this->roRBeneficioConcessaoValeTransporte->getExercicio() ) {
            $stFiltro .= " AND exercicio = '".$this->roRBeneficioConcessaoValeTransporte->getExercicio()."'";
        }
        if ( $this->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
            $stFiltro .= " AND cod_concessao = ".$this->roRBeneficioConcessaoValeTransporte->getCodConcessao();
        }
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratoServidorConcessaoValeTransporteSituacao
    * Lista as concessoes de um determinado contrato, de acordo com sua situacao (inicializado ou nao)
    * Para isso faz um JOIN na tabela de concessao_vale_transporte
    * @access Public
*/
function listarContratoServidorConcessaoValeTransporteSituacao(&$rsRecordSet,$boTransacao="")
{
    if ($this->obRPessoalContratoServidor->getCodContrato() != "" )
        $stFiltro .= " AND Bcscvt.cod_contrato = ".$this->obRPessoalContratoServidor->getCodContrato();
    if ( $this->roRBeneficioConcessaoValeTransporte->getInicializacao() != "" )
        $stFiltro .= " AND Bcvt.inicializado = '".$this->roRBeneficioConcessaoValeTransporte->getInicializacao()."'";
    if ( $this->roRBeneficioConcessaoValeTransporte->getCodMes() != "" )
        $stFiltro .= " AND Bcvt.cod_mes = ".$this->roRBeneficioConcessaoValeTransporte->getCodMes();
    if ( $this->roRBeneficioConcessaoValeTransporte->getExercicio() != "" )
        $stFiltro .= " AND Bcvt.exercicio = '".$this->roRBeneficioConcessaoValeTransporte->getExercicio()."'";
    $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->recuperaContratoServidorConcessaoValeTransporteSituacao($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarContratoServidorGrupoConcessaoValeTransporte
    * @access Public
*/
function listarContratoServidorGrupoConcessaoValeTransporte(&$rsRecordSet,$boTransacao="")
{
    if ( is_object( $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte ) ) {
        //if ( $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
        //    $stFiltro .= " AND cod_concessao = " . $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getCodConcessao();
        //}
        if ( $this->obRPessoalContratoServidor->getCodContrato() ) {
            $stFiltro .= " AND cod_contrato = " . $this->obRPessoalContratoServidor->getCodContrato();
        }
        if ( $this->obRBeneficioGrupoConcessao->getCodGrupo() ) {
            $stFiltro .= " AND cod_grupo = " . $this->obRBeneficioGrupoConcessao->getCodGrupo();
        }
        //if ( $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getExercicio() ) {
        //    $stFiltro .= " AND exercicio = " . $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getExercicio();
        //}
        //if ( $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
        //    $stFiltro .= " AND cod_mes = " . $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->getCodMes();
        //}
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr($stFiltro,4,strlen($stFiltro));
    }

    $obErro = $this->obTBeneficioContratoServidorGrupoConcessaoValeTransporte->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Lista a concessão vigente parar fazer a inicialização de vale-transporte
    * @access Public
*/
function listarContratoServidorVigenciaAtual(&$rsRecordSet, $boTransacao="")
{
    $obErro = new Erro;
    if ($inCodConcessao = $this->roRBeneficioConcessaoValeTransporte->getCodConcessao() ) {
        $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao',$inCodConcessao);
        $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->recuperaContratoServidorVigenciaAtual($rsRecordSet,'','',$boTransacao);
    }

    return $obErro;
}

/**
  * Lista todos contratos que tem uma concessão
  * @access Public
 **/
function listarContratos(&$rsRecordSet,$boTransacao="")
{
    $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->recuperaContratoConcessao($rsRecordSet,'','',$boTransacao);

    return $obErro;
}

/**
    * Inicializa vale-transporte de um contrato
    * @access Public
*/
function inicializarConcessaoValeTransporte($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu() && $this->obRPessoalContratoServidor->getRegistro()) {

        //Lista as concessoes (nao-inicializadas) de um determinado contrato, pega o cod_concessao
        $this->roRBeneficioConcessaoValeTransporte->setInicializacao('f');
        $inCodMes = $this->roRBeneficioConcessaoValeTransporte->getCodMes();
        $stExercicio = $this->roRBeneficioConcessaoValeTransporte->getExercicio();
        $this->roRBeneficioConcessaoValeTransporte->setCodMes('');
        $this->roRBeneficioConcessaoValeTransporte->setExercicio('');
        $obErro = $this->listarContratoServidorConcessaoValeTransporteSituacao($rsConcessao,$boTransacao);

        $this->roRBeneficioConcessaoValeTransporte->setCodMes($inCodMes);
        $this->roRBeneficioConcessaoValeTransporte->setExercicio($stExercicio);
        if (!$obErro->ocorreu() && $rsConcessao->getNumLinhas() > 0) {
            while ( !$rsConcessao->eof() ) {
                $this->roRBeneficioConcessaoValeTransporte->setCodConcessao($rsConcessao->getCampo('cod_concessao'));
                //Verifica se o mes/exercicio a ser inicializado ja esta inserido
                $obErro = $this->listarContratoServidorConcessaoValeTransporte($rsVerificaConcessao,$boTransacao);
                if (!$obErro->ocorreu()) {
                    if ( ($rsVerificaConcessao->getCampo('exercicio') != $this->roRBeneficioConcessaoValeTransporte->getExercicio()) ||
                         ($rsVerificaConcessao->getCampo('cod_mes') ) != $this->roRBeneficioConcessaoValeTransporte->getCodMes() ) {
                        //Consulta a vigencia atual para cada concessao
                        $obErro = $this->listarContratoServidorVigenciaAtual($rsVigenciaAtual,$boTransacao);

                        if (!$obErro->ocorreu()) {
                            //Inicializa o mes
                            if ( $rsVigenciaAtual->getNumLinhas() > 0 ) {
                                $obErro = $this->roRBeneficioConcessaoValeTransporte->incluirInicializacaoValeTransporte($rsVigenciaAtual,$boTransacao);
                            }
                        }
                    }
                }
                $rsConcessao->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte );

    return $obErro;
}

/**
    * Inicializa vale-transporte geral
    * Faz a inicialização de todos os contratos e grupos
    * @access Public
*/
function inicializarConcessaoValeTransporteGeral()
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    //Contratos
    if (!$obErro->ocorreu()) {
        //Lista os contratos para inicializar
        $obErro = $this->listarContratos($rsContrato,$boTransacao);

    }
    if (!$obErro->ocorreu() && $rsContrato->getNumLinhas() > 0) {
        while (!$rsContrato->eof()) {
            $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));
            $this->obRPessoalContratoServidor->setRegistro   ($rsContrato->getCampo('registro')    );
            $obErro = $this->inicializarConcessaoValeTransporte($boTransacao);
            $rsContrato->proximo();
        }
    }

    //Grupos
    $this->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
    $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($this->roRBeneficioConcessaoValeTransporte->getCodMes());
    $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($this->roRBeneficioConcessaoValeTransporte->getExercicio());
    if (!$obErro->ocorreu()) {
        //Lista os grupos para inicializar
        $obErro = $this->obRBeneficioGrupoConcessao->listarGrupos($rsGrupo,$boTransacao);
    }
    if (!$obErro->ocorreu() && $rsGrupo->getNumLinhas() > 0) {
        while (!$rsGrupo->eof()) {
            $this->obRBeneficioGrupoConcessao->setCodGrupo($rsGrupo->getCampo('cod_grupo'));
            $obErro = $this->obRBeneficioGrupoConcessao->inicializarConcessaoValeTransporte($boTransacao);
            $rsGrupo->proximo();
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte );

    return $obErro;
}

/**
    * Monta a lista para exclusao da inicializacao de vale-transporte de um contrato
    * @access Public
*/
function listarConcessaoValeTransporteInicializados(&$rsRecordSet,$boTransacao="")
{
    $rsRecordSet = new RecordSet;
    //Lista os tipos de vale-transporte
    $this->roRBeneficioConcessaoValeTransporte->listarTipo($rsTipo,$boTransacao);
    $arTipo = $rsTipo->getElementos();
    if ($this->obRPessoalContratoServidor->getRegistro()) {
        //Lista as concessoes inicializadas de um determinado contrato
        $this->roRBeneficioConcessaoValeTransporte->setInicializacao('t');
        $obErro = $this->listarContratoServidorConcessaoValeTransporteSituacao($rsConcessao,$boTransacao);
        if (!$obErro->ocorreu() && $rsConcessao->getNumLinhas() > 0) {
            while ( !$rsConcessao->eof() ) {
                //$this->roRBeneficioConcessaoValeTransporte->setCodConcessao($rsConcessao->getCampo('cod_concessao'));
                //Consulta os dados da concessao
                $stFiltro  = " WHERE Bcvt.cod_concessao = ".$rsConcessao->getCampo('cod_concessao')."  \n";
                $stFiltro .= "   AND Bcvt.exercicio = '".$rsConcessao->getCampo('exercicio')."'        \n";
                $stFiltro .= "   AND Bcvt.cod_mes = ".$rsConcessao->getCampo('cod_mes')."              \n";
                $obErro = $this->roRBeneficioConcessaoValeTransporte->listarConcessaoValeTransporteInicializar($rsInicializacao, $stFiltro, "", $boTransacao);
                if (!$obErro->ocorreu()) {
                    $arTemp[] = array (
                        'contrato'   => $rsConcessao->getCampo('registro') ,
                        'concessao'  => $rsConcessao->getCampo('cod_concessao') ,
                        'tipo'       => $arTipo[$rsInicializacao->getCampo('cod_tipo')-1]['descricao'] ,
                        'mes'        => $rsInicializacao->getCampo('cod_mes') ,
                        'ano'        => $rsInicializacao->getCampo('exercicio') ,
                        'quantidade' => $rsInicializacao->getCampo('quantidade')
                    );
                }
                $rsConcessao->proximo();
            }
        }
    }
    if (count($arTemp))
        $rsRecordSet->preenche($arTemp);

    return $obErro;
}

/**
    * Monta a lista para exclusao de inicialização de vale-transporte geral
    * @access Public
*/
function listarConcessaoValeTransporteInicializadosGeral(&$rsRetornoContrato,&$rsRetornoGrupo,$boTransacao="")
{
    $arConcessaoContrato = array();
    $arConcessaoGrupo    = array();
    $rsRetornoContrato   = new RecordSet;
    $rsRetornoGrupo      = new RecordSet;

    //Contratos
    $obErro = $this->listarContratos($rsContrato,$boTransacao);
    if (!$obErro->ocorreu() && $rsContrato->getNumLinhas() > 0) {
        while (!$rsContrato->eof()) {
            $this->obRPessoalContratoServidor->setCodContrato($rsContrato->getCampo('cod_contrato'));
            $this->obRPessoalContratoServidor->setRegistro   ($rsContrato->getCampo('registro')    );
            $obErro = $this->listarConcessaoValeTransporteInicializados($rsConcessaoContrato,$boTransacao);
            if (!$obErro->ocorreu() && $rsConcessaoContrato->getNumLinhas() > 0) {
                $arConcessaoContrato = array_merge($arConcessaoContrato , $rsConcessaoContrato->getElementos());
            }
            $rsContrato->proximo();
        }
    }
    $rsRetornoContrato->preenche($arConcessaoContrato);

    //Grupos
    $this->obRBeneficioGrupoConcessao->addRBeneficioConcessaoValeTransporte();
    $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setCodMes($this->roRBeneficioConcessaoValeTransporte->getCodMes());
    $this->obRBeneficioGrupoConcessao->roRBeneficioConcessaoValeTransporte->setExercicio($this->roRBeneficioConcessaoValeTransporte->getExercicio());
    if (!$obErro->ocorreu()) {
        $obErro = $this->obRBeneficioGrupoConcessao->listarGrupos($rsGrupo,$boTransacao);
    }
    if (!$obErro->ocorreu() && $rsGrupo->getNumLinhas() > 0) {
        while (!$rsGrupo->eof()) {
            $this->obRBeneficioGrupoConcessao->setCodGrupo($rsGrupo->getCampo('cod_grupo'));
            $obErro = $this->obRBeneficioGrupoConcessao->listarConcessaoValeTransporteInicializados($rsConcessaoGrupo,$boTransacao);
            if (!$obErro->ocorreu() && $rsConcessaoGrupo->getNumLinhas() > 0) {
                $arConcessaoGrupo = array_merge($arConcessaoGrupo , $rsConcessaoGrupo->getElementos());
            }
            $rsGrupo->proximo();
        }
    }
    $rsRetornoGrupo->preenche($arConcessaoGrupo);

    return $obErro;
}

/**
    * Faz a exclusao de inicialização de vale-transporte
    * @access Public
    * @param Object $boTransacao Transação
*/
function excluirAssociacao($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        if ($this->obRPessoalContratoServidor->getCodContrato()) {
            foreach ($this->arRBeneficioConcessaoValeTransporte as $obRBeneficioConcessaoValeTransporte) {
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_contrato' ,$this->obRPessoalContratoServidor->getCodContrato() );
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_concessao',$obRBeneficioConcessaoValeTransporte->getCodConcessao());
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('exercicio'    ,$obRBeneficioConcessaoValeTransporte->getExercicio());
                $this->obTBeneficioContratoServidorConcessaoValeTransporte->setDado('cod_mes'      ,$obRBeneficioConcessaoValeTransporte->getCodMes());
                $obErro = $this->obTBeneficioContratoServidorConcessaoValeTransporte->exclusao($boTransacao);
                if (!$obErro->ocorreu()) {
                    $obErro = $obRBeneficioConcessaoValeTransporte->excluirAssociacao($boTransacao);
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTBeneficioContratoServidorConcessaoValeTransporte );

    return $obErro;
}

}
?>
