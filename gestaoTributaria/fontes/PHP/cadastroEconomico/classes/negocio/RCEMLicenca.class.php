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
    * Classe de regra de negócio para Licença
    * Data de Criação: 30/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMLicenca.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.15  2007/03/14 20:04:00  dibueno
Alterações para busca de dados da licenca com exercicio '0000'

Revision 1.14  2007/03/02 14:50:32  dibueno
Bug #7676#

Revision 1.13  2006/10/23 16:19:51  dibueno
Alterações para emissão de alvará e inclusão de observação

Revision 1.12  2006/10/11 10:27:25  dibueno
Atualizações para utilização da tabela licenca_observacao, licenca_documento

Revision 1.11  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php"              );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDiasSemana.class.php"    );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoDiasSemana.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMBaixaLicenca.class.php"         );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaObservacao.class.php"    );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaDocumento.class.php"    );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoLicenca.class.php"      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoBaixaLicenca.class.php" );
include_once ( CAM_GT_CEM_MAPEAMENTO."VCEMLicencaAtiva.class.php"         );
include_once ( CAM_GT_CEM_MAPEAMENTO."VCEMLicencaSuspensaAtiva.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMConfiguracao.class.php"              );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"        );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php"                          );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                     );

/**
* Classe de regra de negócio para Licença
* Data de Criação: 30/11/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMLicenca
{
/**
* @access Private
* @var Integer
*/
var $inCodigoLicenca;
/*
* @access Private
* @var String
*/
var $stExercicio;
/**
* @access Private
* @var Date
*/
var $dtDataInicio;
/**
* @access Private
* @var Date
*/
var $dtDataTermino;
/**
* @access Private
* @var String
*/
var $stMotivo;
/**
* @access Private
* @var String
*/
var $stEspecieLicenca;
/**
* @access Private
* @var String
*/
var $stObservacao;
/**
* @access Private
* @var Object
*/
var $obTCEMLicenca;
/**
* @access Private
* @var Object
*/
var $obTCEMLicencaDiasSemana;
/**
* @access Private
* @var Object
*/
var $obTDiasSemana;
/**
* @access Private
* @var Object
*/
var $obTCEMBaixaLicenca;
/**
* @access Private
* @var Object
*/
var $obTCEMProcessoLicenca;
/**
* @access Private
* @var Object
*/
var $obTCEMProcessoBaixaLicenca;
/**
* @access Private
* @var Object
*/
var $obVCEMLicencaAtiva;
/**
* @access Private
* @var Object
*/
var $obTCEMLicencaObservacao;
/**
* @access Private
* @var Object
*/
var $obTCEMLicencaDocumento;
/**
* @access Private
* @var Object
*/
var $obVCEMLicencaSuspensaAtiva;
/**
* @access Private
* @var Object
*/
var $obRCEMConfiguracao;
/**
* @access Private
* @var Object
*/
var $obRCEMInscricaoAtividade;
/**
* @access Private
* @var Object
*/
var $obRCGM;
/**
* @access Private
* @var Object
*/
var $obRProcesso;
/**
* @access Private
* @var Array
*/
var $arHorario;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setCodigoLicenca($valor) { $this->inCodigoLicenca = $valor;  }
/**
* @access Public
* @param String $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor;      }
/**
* @access Public
* @param Date $valor
*/
function setDataInicio($valor) { $this->dtDataInicio = $valor;     }
/**
* @access Public
* @param Date $valor
*/
function setDataTermino($valor) { $this->dtDataTermino = $valor;    }
/**
* @access Public
* @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;         }
/**
* @access Public
* @param String $valor
*/
function setEspecieLicenca($valor) { $this->stEspecieLicenca = $valor; }
/**
* @access Public
* @param Array $valor
*/
function setHorario($valor) { $this->arHorario = $valor;        }
/**
* @access Public
* @param Array $valor
*/
function setObservacao($valor) { $this->stObservacao = $valor;        }

//GETTERS
/**
* @access Public
* @return Integer
*/
function getCodigoLicenca() { return $this->inCodigoLicenca;  }
/**
* @access Public
* @return String
*/
function getExercicio() { return $this->stExercicio;      }
/**
* @access Public
* @return Date
*/
function getDataInicio() { return $this->dtDataInicio;     }
/**
* @access Public
* @return Date
*/
function getDataTermino() { return $this->dtDataTermino;    }
/**
* @access Public
* @return String
*/
function getMotivo() { return $this->stMotivo;         }
/**
* @access Public
* @return String
*/
function getEspecieLicenca() { return $this->stEspecieLicenca; }
/**
* @access Public
* @return Array
*/
function getHorario() { return $this->arHorario;        }
/**
* @access Public
* @return Array
*/
function getObservacao() { return $this->stObservacao;        }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMLicenca()
{
    $this->obTCEMLicenca              = new TCEMLicenca;
    $this->obTCEMLicencaDiasSemana    = new TCEMLicencaDiasSemana;
    $this->obTCEMLicencaDocumento	  = new TCEMLicencaDocumento;
    $this->obTDiasSemana              = new TDiasSemana;
    $this->obTCEMBaixaLicenca         = new TCEMBaixaLicenca;
    $this->obTCEMProcessoLicenca      = new TCEMProcessoLicenca;
    $this->obTCEMProcessoBaixaLicenca = new TCEMProcessoBaixaLicenca;
    $this->obVCEMLicencaAtiva         = new VCEMLicencaAtiva;
    $this->obVCEMLicencaSuspensaAtiva = new VCEMLicencaSuspensaAtiva;
    $this->obRCEMConfiguracao         = new RCEMConfiguracao;
    $this->obRCEMInscricaoAtividade   = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
    $this->obRCGM                     = new RCGM;
    $this->obRProcesso                = new RProcesso;
    $this->obTransacao                = new Transacao;
    $this->arHorario                  = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Licença
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function concederLicenca($boTransacao = "")
{
    ;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if (!$this->inCodigoLicenca) {
            $obErro = $this->obTCEMLicenca->proximoCod( $this->inCodigoLicenca, $boTransacao );
        }
        $arDataInicio    = explode( "/" , $this->dtDataInicio  );
        $arDataTermino   = explode( "/" , $this->dtDataTermino );
        $dtInicioInvert  = $arDataInicio[2].$arDataInicio[1].$arDataInicio[0];
        if ($this->dtDataTermino != '') {
            $dtTerminoInvert = $arDataTermino[2].$arDataTermino[1].$arDataTermino[0];
        }

        if (!$this->dtDataTermino || $dtTerminoInvert >= $dtInicioInvert) {
            if ( !$obErro->ocorreu() ) {

                if (!$this->stExercicio) {
                    $this->stExercicio = '0000';
                }

                $this->obTCEMLicenca->setDado( "cod_licenca"    , $this->inCodigoLicenca );
                $this->obTCEMLicenca->setDado( "exercicio"      , $this->stExercicio     );
                $this->obTCEMLicenca->setDado( "dt_inicio"      , $this->dtDataInicio    );
                if ($this->dtDataTermino) {
                    $this->obTCEMLicenca->setDado( "dt_termino" , $this->dtDataTermino   );
                }
                $obErro = $this->obTCEMLicenca->inclusao( $boTransacao );
                #$this->obTCEMLicenca->debug(); exit;
                if ( !$obErro->ocorreu() ) {

                    if ( $this->obRProcesso->getCodigoProcesso() ) {

                        $this->obTCEMProcessoLicenca->setDado( "cod_licenca"       , $this->inCodigoLicenca );
                        $this->obTCEMProcessoLicenca->setDado( "exercicio"         , $this->stExercicio     );
                        $this->obTCEMProcessoLicenca->setDado( "cod_processo"      , $this->obRProcesso->getCodigoProcesso() );
                        $this->obTCEMProcessoLicenca->setDado( "exercicio_processo", $this->obRProcesso->getExercicio()      );
                        $obErro = $this->obTCEMProcessoLicenca->inclusao( $boTransacao );
                        //$this->obTCEMProcessoLicenca->debug();
                     }
                     if ( !$obErro->ocorreu() ) {
                        if ($this->arHorario) {
                            $obErro = $this->incluirHorario( $boTransacao );
                        }
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    if ( $this->getObservacao() ) {
                        $this->obTCEMLicencaObservacao = new TCEMLicencaObservacao;
                        $this->obTCEMLicencaObservacao->setDado( 'cod_licenca', $this->getCodigoLicenca()	);
                        $this->obTCEMLicencaObservacao->setDado( 'exercicio', 	$this->getExercicio()		);
                        $this->obTCEMLicencaObservacao->setDado( 'observacao', 	$this->getObservacao() 		);
                        $obErro = $this->obTCEMLicencaObservacao->inclusao( $boTransacao );
                    }
                }

                if ( !$obErro->ocorreu() ) {
                    $arDocumentosSessao = Sessao::read( "documentos" );
                    $inRegistros = count ( $arDocumentosSessao );
                    $inX = 0;
                    while ( $inX < $inRegistros && !$obErro->ocorreu() ) {
                        $inCodTipoDocumentoAtual = $arDocumentosSessao[$inX]['cod_tipo_documento'];
                        $inCodDocumentoAtual = $arDocumentosSessao[$inX]['cod_documento'];
                        $this->obTCEMLicencaDocumento->BuscaUltimoNumeroAlvara( $rsAlvara , $boTransacao);
                        $inNumAlvara = $rsAlvara->getCampo('valor') + 1;

                        $this->obTCEMLicencaDocumento->setDado('exercicio', $this->getExercicio() );
                        $this->obTCEMLicencaDocumento->setDado('cod_licenca', $this->getCodigoLicenca() );
                        $this->obTCEMLicencaDocumento->setDado('cod_tipo_documento', $inCodTipoDocumentoAtual);
                        $this->obTCEMLicencaDocumento->setDado('cod_documento', $inCodDocumentoAtual);
                        $this->obTCEMLicencaDocumento->setDado('num_alvara', $inNumAlvara);
                        $obErro = $this->obTCEMLicencaDocumento->inclusao ( $boTransacao );
                        //$this->obTCEMTipoLicencaModeloDocumento->debug();

                        $inX++;
                    }
                }
    //exit ("SAINDO");

            }
        } else {
            $obErro->setDescricao("A Data de Término da licença deve ser posterior à Data de Início!");
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicenca );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Licença
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( !$obErro->ocorreu() ) {
            if ( $this->getObservacao() ) {
                $this->obTCEMLicencaObservacao = new TCEMLicencaObservacao;
                $this->obTCEMLicencaObservacao->setDado( 'cod_licenca', $this->getCodigoLicenca()	);
                $this->obTCEMLicencaObservacao->setDado( 'exercicio', 	$this->getExercicio()		);
                $this->obTCEMLicencaObservacao->setDado( 'observacao', 	$this->getObservacao() 		);
                $obErro = $this->obTCEMLicencaObservacao->alteracao( $boTransacao );
            }
        }

        if ( !$obErro->ocorreu() ) {
            $this->obTCEMLicenca->setDado    ( "cod_licenca" , $this->inCodigoLicenca );
            $this->obTCEMLicenca->setDado    ( "exercicio"   , $this->stExercicio     );
            $this->obTCEMLicenca->setDado    ( "dt_inicio"   , $this->dtDataInicio    );
            if ($this->dtDataTermino) {
                $this->obTCEMLicenca->setDado( "dt_termino"  , $this->dtDataTermino   );
            }
            $obErro = $this->obTCEMLicenca->alteracao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            if ($this->arHorario) {
                $obErro = $this->incluirHorario( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicenca );

    return $obErro;
}

/**
* Altera os Horários setados na tabela de Licença Especial
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarHorario($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arHorario ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos um Horário!" );
        } else {
            $obErro = $this->incluirHorario( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicenca );

    return $obErro;
}

/**
    * Inclui o Horario setado na tabela de Licença Especial Dias Semana
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirHorario($arHorario, $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE cod_licenca = ".$this->inCodigoLicenca;
        $obErro = $this->obTCEMLicencaDiasSemana->recuperaTodos( $rsHorarios, $stFiltro, "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $arHorarioExclusao = array();
            $inCount = 0;
            while ( !$rsHorarios->eof() ) {
                $arHorarioExclusao[ $inCount ] = $rsHorarios->getCampo("cod_dia");
                $rsHorarios->proximo();
                $inCount++;
            }
            foreach ($arHorarioExclusao AS $inCodigoDia) {
                $obErro = $this->excluirHorario( $inCodigoDia , $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
            if ( !$obErro->ocorreu() ) {
                foreach ($this->arHorario as $novoHorario => $arHorario) {
                    $this->obTCEMLicencaDiasSemana->setDado( "cod_licenca", $this->inCodigoLicenca  );
                    $this->obTCEMLicencaDiasSemana->setDado( "exercicio"  , $this->stExercicio      );
                    $this->obTCEMLicencaDiasSemana->setDado( "cod_dia"    , $arHorario["inDia"]     );
                    $this->obTCEMLicencaDiasSemana->setDado( "hr_inicio"  , $arHorario["hrInicio"]  );
                    $this->obTCEMLicencaDiasSemana->setDado( "hr_termino" , $arHorario["hrTermino"] );
                    $obErro = $this->obTCEMLicencaDiasSemana->inclusao ( $boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicenca );
//    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    return $obErro;
}

/**
    * Exclui o Horario setado da tabela de Licença Especial Dias Semana
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirHorario($inCodigoDia , $boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMLicencaDiasSemana->setDado( "cod_licenca", $this->inCodigoLicenca );
        $this->obTCEMLicencaDiasSemana->setDado( "exercicio"  , $this->stExercicio     );
        $this->obTCEMLicencaDiasSemana->setDado( "cod_dia"    , $inCodigoDia           );
        $obErro = $this->obTCEMLicencaDiasSemana->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicenca );
//    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro );
    return $obErro;
}

/**
* Baixa a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "1";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"        , $this->inCodigoLicenca                  );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"          , $this->stExercicio                      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio_processo" , $this->obRProcesso->getExercicio()      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"          , $this->dtDataInicio                     );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaLicenca );

    return $obErro;
}

/**
* Suspende a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function suspenderLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "2";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca"    , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"      , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"      , $this->dtDataInicio    );
        if ($this->dtDataTermino) {
            $this->obTCEMBaixaLicenca->setDado( "dt_termino" , $this->dtDataTermino   );
        }
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"       , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"         , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"        , $this->inCodigoLicenca                  );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"          , $this->stExercicio                      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio_processo" , $this->obRProcesso->getExercicio()      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"          , $this->dtDataInicio                     );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaLicenca );

    return $obErro;
}

/**
* Cancela a suspensão da Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cancelarSuspensao($boTransacao = "")
{
    $codigoTipoBaixa = "2";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "dt_termino"  , $this->dtDataTermino   );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaLicenca );

    return $obErro;
}

/**
* Cassa a Licença setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cassarLicenca($boTransacao = "")
{
    $codigoTipoBaixa = "3";
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMBaixaLicenca->setDado( "cod_licenca" , $this->inCodigoLicenca );
        $this->obTCEMBaixaLicenca->setDado( "exercicio"   , $this->stExercicio     );
        $this->obTCEMBaixaLicenca->setDado( "dt_inicio"   , $this->dtDataInicio    );
        $this->obTCEMBaixaLicenca->setDado( "cod_tipo"    , $codigoTipoBaixa       );
        $this->obTCEMBaixaLicenca->setDado( "motivo"      , $this->stMotivo        );
        $obErro = $this->obTCEMBaixaLicenca->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_licenca"        , $this->inCodigoLicenca                  );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio"          , $this->stExercicio                      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "cod_processo"       , $this->obRProcesso->getCodigoProcesso() );
                $this->obTCEMProcessoBaixaLicenca->setDado( "exercicio_processo" , $this->obRProcesso->getExercicio()      );
                $this->obTCEMProcessoBaixaLicenca->setDado( "dt_inicio"          , $this->dtDataInicio                     );
                $obErro = $this->obTCEMProcessoBaixaLicenca->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaLicenca );

    return $obErro;
}

/**
* Lista as Licenças Ativas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLicencas(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " exercicio = '".$this->getExercicio()."' AND ";
    }
    if ($this->stEspecieLicenca) {
        $stFiltro .= " especie_licenca = '".$this->stEspecieLicenca."' AND ";
    }
    if ( $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCEMLicencaAtiva->debug();
    return $obErro;
}

/**
* Lista as Licenças Suspensas Ativas conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLicencasSuspensasAtivas(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    }
    if ( $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " inscricao_economica = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->obRCGM->getNumCGM() ) {
        $stFiltro .= " numcgm = ".$this->obRCGM->getNumCGM()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaSuspensaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCEMLicencaSuspensaAtiva->debug();
    return $obErro;
}

/**
* Lista todas as licenças de acordo com o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarLicencasConsulta(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " coalesce ( lca.inscricao_economica, lce.inscricao_economica) = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }
    if ( $this->getCodigoLicenca() ) {
        $stFiltro .= " l.cod_licenca = ".$this->getCodigoLicenca()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = "  GROUP BY
    l.cod_licenca
    , l.dt_inicio
    , l.dt_termino
    , l.exercicio
    , bl.dt_inicio
    , bl.dt_termino
    , bl.motivo
    , eld.cod_documento
    , eld.cod_tipo_documento
    , amd.nome_documento
    , amd.nome_arquivo_agt
    , lca.inscricao_economica
    , lce.inscricao_economica
    , lca.ocorrencia_licenca
    , lce.ocorrencia_licenca
    , eld.timestamp
    \n";
    $stOrder .= " ORDER BY eld.timestamp desc ";

    $obErro = $this->obTCEMLicenca->recuperaLicencasConsulta( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//        $this->obTCEMLicenca->debug();
    return $obErro;

}

/**
* Recupera do banco de dados os dados da Licença selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarLicenca($boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obVCEMLicencaAtiva->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->inCodigoLicenca = $rsRecordSet->getCampo( "cod_licenca" );
        $this->stExercicio     = $rsRecordSet->getCampo( "exercicio"   );
        $this->dtDataInicio    = $rsRecordSet->getCampo( "dt_inicio"   );
        $this->dtDataTermino   = $rsRecordSet->getCampo( "dt_termino"  );
    }

    return $obErro;
}

/**
* Recupera do banco de dados os dias e horários da Licença selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarHorarios(&$rsHorarios, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " AND cod_licenca = ".$this->inCodigoLicenca;
    }
    if ($this->stExercicio) {
        $stFiltro .= " AND exercicio = '".$this->stExercicio."'";
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obTCEMLicencaDiasSemana->recuperaRelacionamento( $rsHorarios, $stFiltro, $stOrder, $boTransacao );
    #$this->obTCEMLicencaDiasSemana->debug();

    return $obErro;
}

/**
    * Lista os dias da semana
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDiasSemana(&$rsLista, $boTransacao = "")
{
    $stOrder = " ORDER BY cod_dia ";
    $obErro = $this->obTDiasSemana->recuperaTodos( $rsLista, "", $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Recupera a configuracao do modulo Licenças
    * @access Public
    * @param  Object $arConfiguracao Retorna o array preenchido
    * @return Object Objeto Erro
*/
function recuperaConfiguracao(&$arConfiguracao)
{
    $rsConfiguracao = new RecordSet();
    $this->obRCEMConfiguracao->setCodigoModulo( Sessao::read('modulo')    );
    $this->obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
    $obErro = $this->obRCEMConfiguracao->recuperaConfiguracao( $rsConfiguracao );
    if ( !$obErro->ocorreu() ) {
        while ( !$rsConfiguracao->eof() ) {
            if ( $rsConfiguracao->getCampo("parametro") == "numero_licenca" ) {
                $arConfiguracao['numero_licenca']  = $rsConfiguracao->getCampo("valor");
            } elseif ( $rsConfiguracao->getCampo("parametro") == "mascara_licenca" ) {
                $arConfiguracao['mascara_licenca'] = $rsConfiguracao->getCampo("valor");
            }
            $rsConfiguracao->proximo();
        }
    }

    return $obErro;
}

/**
* Recupera o proximo Codigo para Licença para cadastro de licença Automático por Exercício
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function recuperaMaxCod(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->stExercicio) {
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $obErro = $this->obTCEMLicenca->recuperaRelacionamento( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

}
