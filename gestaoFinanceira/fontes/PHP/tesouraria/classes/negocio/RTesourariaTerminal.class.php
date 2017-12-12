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
    * Classe de Regra de Negócio para terminal
    * Data de Criação   : 06/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaTerminal.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 32140 $
    $Name$
    $Autor:$
    $Date: 2008-04-02 16:36:27 -0300 (Qua, 02 Abr 2008) $

    * Casos de uso: uc-02.04.02
                    uc-02.04.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_TES_NEGOCIO."RTesourariaUsuarioTerminal.class.php"                               );

/**
    * Classe de Regra de Assinatura
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaTerminal
{
/*
    * @var Object
    * @access Private
*/
var $roUtilmoUsuarioTerminal;
/*
    * @var Integer
    * @access Private
*/
var $inCodTerminal;
/*
    * @var String
    * @access Private
*/
var $stTimestampTerminal;
/*
    * @var String
    * @access Private
*/
var $stTimestampDesativado;
/*
    * @var String
    * @access Private
*/
var $stCodVerificador;
/*
    * @var String
    * @access Private
*/
var $stMac;

/*
    * @var Array
    * @access Private
*/
var $arUsuarioTerminal;
/*
    * @var String
    * @access Private
*/
var $stDataAbertura;
/*
    * @var String
    * @access Private
*/
var $stDataFechamento;
/*
    * @var String
    * @access Private
*/
var $stTimestampAbertura;
/*
    * @var String
    * @access Private
*/
var $stTimestampFechamento;

/*
    * @access Public
    * @param Integer $valor
*/
function setCodTerminal($valor) { $this->inCodTerminal                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampTerminal($valor) { $this->stTimestampTerminal             = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampDesativado($valor) { $this->stTimestampDesativado          = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setCodVerificador($valor) { $this->stCodVerificador                      = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setMac($valor) { $this->stMac                         = $valor; }

/*
    * @access Public
    * @param Array $valor
*/
function setUsuarioTerminal($valor) { $this->arUsuarioTerminal            = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDataAbertura($valor) { $this->stDataAbertura                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setDataFechamento($valor) { $this->stDataFechamento                  = $valor; }

/*
    * @access Public
    * @param String $valor
*/

function setTimestampAbertura($valor) { $this->stTimestampAbertura                  = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampFechamento($valor) { $this->stTimestampFechamento                = $valor; }

/*
    * @access Public
    * @return Integer
*/
function getCodTerminal() { return $this->inCodTerminal;                   }
/*
    * @access Public
    * @return String
*/
function getTimestampTerminal() { return $this->stTimestampTerminal;             }

/*
    * @access Public
    * @return String
*/
function getTimestampDesativado() { return $this->stTimestampDesativado;           }
/*
    * @access Public
    * @return String
*/
function getCodVerificador() { return $this->stCodVerificador;                 }
/*
    * @access Public
    * @return String
*/
function getMac() { return $this->stMac;                            }

/*
    * @access Public
    * @return Array
*/
function getUsuarioTerminal() { return $this->arUsuarioTerminal;               }
/*
    * @access Public
    * @return String
*/
function getDataAbertura() { return $this->stDataAbertura;             }
/*
    * @access Public
    * @return String
*/
function getDataFechamento() { return $this->stDataFechamento;             }

/*
    * @access Public
    * @return String
*/

function getTimestampAbertura() { return $this->stTimestampAbertura;             }
/*
    * @access Public
    * @return String
*/
function getTimestampFechamento() { return $this->stTimestampFechamento;             }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaTerminal()
{
}

/*
    * Método para adicionar Usuario Terminal
    * @access Public
*/
function addUsuarioTerminal()
{
    $this->arUsuarioTerminal[] = new RTesourariaUsuarioTerminal( $this );
    $this->roUltimoUsuario = $this->arUsuarioTerminal[ count( $this->arUsuarioTerminal )-1 ];
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php" );
    $obTTesourariaTerminal            =  new TTesourariaTerminal;
    $obTTesourariaTerminal->setDado( "cod_terminal" , $this->inCodTerminal );
    $obTTesourariaTerminal->setDado( "timestamp_terminal"    , $this->stTimestampTerminal   );
    $obErro = $obTTesourariaTerminal->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->stCodVerificador = $rsRecordSet->getCampo("cod_verificador");
        $obErro = $this->consultarUsuarioTerminal( $boTransacao );
    }

    return $obErro;
}

/**
    * Método para recuperar todos as usuarios de terminal
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarUsuarioTerminal($boTransacao = "")
{
    $obRTesourariaUsuarioTerminal = new RTesourariaUsuarioTerminal($this);
    $inNumCgmUsuario = $obRTesourariaUsuarioTerminal->obRCGM->getNumCGM();
    $obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( null );
    $obErro = $obRTesourariaUsuarioTerminal->listarUsuariosAtivos( $rsUsuario, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsUsuario->eof() ) {
        $inNumCgmUsuario = $obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( $inNumCgmUsuario );
        $this->arUsuarioTerminal = array();
        while ( !$rsUsuario->eof() ) {
            $this->addUsuarioTerminal();
            $this->roUltimoUsuario->obRCGM->setNumCGM( $rsUsuario->getCampo( 'cgm_usuario' ) );
            $this->roUltimoUsuario->setResponsavel( $rsUsuario->getCampo( 'responsavel' ) );
            $rsUsuario->proximo();
        }
    }

    return $obErro;

}

/**
    * Inclui usuarios do terminal
    * @access Private
    * @param Object $boTransacao Parâmetro Transacao
    * @return Object $obErro Objeto Erro
*/

function salvarUsuarioTerminal($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
    $obErro = new Erro;
    $inCount = 0;
    $arUsuariosAtuais = array();
    $arUsuariosNovos  = array();
    $obTransacao      =  new Transacao;
    $obRTesourariaUsuarioTerminal = new RTesourariaUsuarioTerminal( $this );
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( is_array( $this->arUsuarioTerminal ) and count( $this->arUsuarioTerminal ) ) {
        $inNumCgmUsuario = $obRTesourariaUsuarioTerminal->obRCGM->getNumCGM();
        $obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( null );
        $obErro = $obRTesourariaUsuarioTerminal->listarUsuariosAtivos( $rsUsuarioTerminal, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obRTesourariaUsuarioTerminal->obRCGM->setNumCGM( $inNumCgmUsuario );
            while (!$rsUsuarioTerminal->eof()) {
                $arUsuariosAtuais[$inCount] = $rsUsuarioTerminal->getCampo('cgm_usuario');
                $arTimestAtuais[$inCount] = $rsUsuarioTerminal->getCampo('timestamp_usuario');
                $inCount++;
                $rsUsuarioTerminal->proximo();
            }
            $inCount=0;
            foreach ($this->arUsuarioTerminal as $obUsuarioNovo) {
                $stCgmUsuarioNovo = $obUsuarioNovo->obRCGM->getNumCGM();
                $arUsuariosNovos[$inCount] = $stCgmUsuarioNovo;
                $arResponsavelNovo[$stCgmUsuarioNovo] = $obUsuarioNovo->getResponsavel();
                $inCount++;
                if (!in_array($stCgmUsuarioNovo,$arUsuariosAtuais)) {
                    $obErro = $obUsuarioNovo->incluir( $boTransacao );
                    if( $obErro->ocorreu() )
                        break;
                }
            }
            foreach ($arUsuariosAtuais as $chave=>$stCgmUsuarioAtual) {
                if (!in_array($stCgmUsuarioAtual,$arUsuariosNovos)) {
                    $obRTesourariaUsuarioTerminal->obRCGM->setNumCgm( $stCgmUsuarioAtual);
                    $obRTesourariaUsuarioTerminal->setTimestampUsuario($arTimestAtuais[$chave]);
                    $obRTesourariaUsuarioTerminal->setTimestampExcluido(date( 'Y-m-d H:i:s.ms' ));
                    $obErro = $obRTesourariaUsuarioTerminal->excluir( $boTransacao );
                    if( $obErro->ocorreu() )
                        break;
                } else {
                    $obRTesourariaUsuarioTerminal->obRCGM->setNumCgm( $stCgmUsuarioAtual);
                    $obRTesourariaUsuarioTerminal->setTimestampUsuario($arTimestAtuais[$chave]);
                    $obRTesourariaUsuarioTerminal->setResponsavel($arResponsavelNovo[$stCgmUsuarioAtual]);
                    $obErro = $obRTesourariaUsuarioTerminal->alterar( $boTransacao );
                }
            }
        }
    } else {
        $obErro->setDescricao( "Deve haver pelo menos um usuário vinculado ao terminal!" );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminal );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"            );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php"  );
    $obTransacao      =  new Transacao;
    $obTTesourariaTerminal            =  new TTesourariaTerminal;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaExistenciaCodTerminal( $boExisteCodTerminal, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            if (!$boExisteCodTerminal) {
                $obErro = $this->verificaExistenciaCodigoVerificador( $boExisteCodigoVerificador, $boTransacao);
                if ( !$obErro->ocorreu() ) {
                    if (!$boExisteCodigoVerificador) {
                        $obTTesourariaTerminal->setDado( "cod_terminal"      , $this->inCodTerminal       );
                        $obTTesourariaTerminal->setDado( "timestamp_terminal", $this->stTimestampTerminal );
                        $obTTesourariaTerminal->setDado( "cod_verificador"   , $this->stCodVerificador    );
                        $obErro = $obTTesourariaTerminal->inclusao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->salvarUsuarioTerminal($boTransacao);
                        }
                    } else {
                        $obErro->setDescricao( "Já existe Terminal cadastrado com este código verificador no exercício atual!" );
                    }
                }
            } else {
                $obErro->setDescricao( "Já existe este Nr. de Terminal cadastrado para o exercício atual!" );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminal );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php" );
    $obTransacao           = new Transacao;
    $obTTesourariaTerminal = new TTesourariaTerminal;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->verificaExistenciaCodTerminal( $boExisteCodTerminal, $boTransacao);
        if ( !$obErro->ocorreu() ) {
            if (!$boExisteCodTerminal) {
                $obErro = $this->verificaExistenciaCodigoVerificador( $boExisteCodigoVerificador, $boTransacao);
                if ( !$obErro->ocorreu() ) {
                    if (!$boExisteCodigoVerificador) {
                        $obTTesourariaTerminal->setDado( "cod_terminal"      , $this->inCodTerminal       );
                        $obTTesourariaTerminal->setDado( "timestamp_terminal", $this->stTimestampTerminal );
                        $obTTesourariaTerminal->setDado( "cod_verificador"   , $this->stCodVerificador    );
                        $obErro = $obTTesourariaTerminal->alteracao( $boTransacao );
                        if ( !$obErro->ocorreu() ) {
                            $this->salvarUsuarioTerminal($boTransacao);
                        }
                    } else {
                        $obErro->setDescricao( "Já existe Terminal cadastrado com este código verificador no exercício atual!" );
                    }
                }
            } else {
                $obErro->setDescricao( "Já existe este Nr. de Terminal cadastrado para o exercício atual!" );
            }
        }
    }

    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminal );

    return $obErro;
}

/**
    * Desativa o terminal
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function desativar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                     );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminalDesativado.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaTerminalDesativado  =  new TTesourariaTerminalDesativado;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaTerminalDesativado->setDado( "cod_terminal"         , $this->getCodTerminal()           );
        $obTTesourariaTerminalDesativado->setDado( "timestamp_terminal"   , $this->getTimestampTerminal()     );
        $obTTesourariaTerminalDesativado->setDado( "timestamp_desativado" , $this->getTimestampDesativado()   );

        $obErro = $obTTesourariaTerminalDesativado->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminalDesativado );

    return $obErro;
}

/**
    * Ativa o terminal
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function ativar($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"                     );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminalDesativado.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaTerminalDesativado  =  new TTesourariaTerminalDesativado;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaTerminalDesativado->setDado( "cod_terminal"         , $this->getCodTerminal()           );
        $obTTesourariaTerminalDesativado->setDado( "timestamp_terminal"   , $this->getTimestampTerminal()     );

        $obErro = $obTTesourariaTerminalDesativado->exclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaTerminalDesativado );

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
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO ."TTesourariaTerminal.class.php" );

    if( $this->getTimestampTerminal() )
        $stFiltro .= " timestamp_terminal = '".$this->getTimestampTerminal()."' AND";

    if( $this->roUltimoUsuario->obRCGM->getNumCGM() )
        $stFiltro .= " cgm_usuario = ".$this->roUltimoUsuario->obRCGM->getNumCGM()." AND";

    if( $this->getCodTerminal() )
        $stFiltro .= " cod_terminal = ".$this->getCodTerminal()." AND";

    if( $this->getCodVerificador() )
        $stFiltro .= " cod_verificador = '".$this->getCodVerificador()."' AND";

   if( $this->getDataAbertura())
       $stFiltro .= " TO_CHAR(timestamp_abertura, 'dd/mm/yyyy') = '".$this->getDataAbertura()."' AND";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $stOrder = ($stOrder) ? $stOrder : "cod_terminal,timestamp_terminal";
    $obTTesourariaTerminal            =  new TTesourariaTerminal;
    $obErro = $obTTesourariaTerminal->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

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
function listarSituacao(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaAbertura.class.php" );
    $obTTesourariaAbertura = new TTesourariaAbertura();
    if( $this->getTimestampTerminal() )
        $stFiltro .= " TT.timestamp_terminal = '".$this->getTimestampTerminal()."' AND";
    if( $this->inCodTerminal )
        $stFiltro .= " TT.cod_terminal = ".$this->inCodTerminal." AND";
    if( $this->getCodVerificador() )
        $stFiltro .= " TT.cod_verificador = '".$this->getCodVerificador()."' AND";
   if( $this->getDataAbertura())
       $stFiltro .= " TO_DATE(TA.timestamp_abertura, 'yyyy-mm-dd') = TO_DATE('".$this->getDataAbertura()."', 'dd/mm/yyyy' ) AND";

    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obErro = $obTTesourariaAbertura->recuperaSituacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para listar situação de um terminal referente a um boletim
    * @access Public
    * @param Object $rsRecordSet Retorna o RecordSet preenchido
    * @param Object $obRTesourariaBoletim Passa o boletim o qual o terminal referencia
    * @param String $stOrder Passa a ordenação da lista
    * @param Object $boTransacao Objeto de Transacao
    * @return Object $obErro Objeto de Erro
*/
function listarSituacaoPorBoletim(&$rsRecordSet, &$obRTesourariaBoletim, $stSituacao = "", $stOrder = "", $boTransacao = "")
{
    $stFiltro = "";
    if ($stSituacao == 'aberto' and Sessao::read('numCgm') != 0) {
        $stFiltro .= " TUT.cgm_usuario = ".Sessao::read('numCgm')." AND ";
    }
    if( $obRTesourariaBoletim->getCodBoletim() )
        $stFiltro .= " TA.cod_boletim = ".$obRTesourariaBoletim->getCodBoletim()." AND ";
    /*else
        $stFiltro .= " TA.cod_boletim = 0 AND "; */
    if( $obRTesourariaBoletim->getExercicio() )
        $stFiltro .= " TA.exercicio_boletim = '".$obRTesourariaBoletim->getExercicio()."' AND ";
    if( $obRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " TA.cod_entidade IN( ".$obRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade()." ) AND ";

    $stFiltro .= " TTD.timestamp_desativado IS NULL AND ";

    if ($stSituacao == 'aberto') {
        $stFiltro .= " TA.timestamp_abertura    IS NOT NULL AND";
        $stFiltro .= " CASE WHEN TF.timestamp_fechamento  IS NOT NULL                   \n";
        $stFiltro .= "   THEN CASE WHEN TA.timestamp_abertura > TF.timestamp_fechamento \n";
        $stFiltro .= "          THEN TRUE                                               \n";
        $stFiltro .= "          ELSE FALSE                                              \n";
        $stFiltro .= "        END                                                       \n";
        $stFiltro .= "   ELSE TRUE                                                      \n";
        $stFiltro .= " END AND ";
    } elseif ($stSituacao == 'fechado') {
        $stFiltro .= " TF.timestamp_fechamento IS NOT NULL AND ";
        $stFiltro .= " TF.timestamp_fechamento >= TA.timestamp_abertura AND ";
    }

    $obErro = $this->listarSituacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para listar Terminais Ativos
    * @access Public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return $obErro
*/
function listarTerminalAtivo(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro = " timestamp_desativado is null AND";
    $obErro = $this->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para listar Terminais Inativos
    * @access Public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return $obErro
*/
function listarTerminalInativo(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro  = "";
    $stFiltro .= " timestamp_desativado is not null AND";
    $obErro = $this->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function verificaExistenciaCodTerminal(&$boExisteCodTerminal, $boTransacao = "")
{
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php" );
    $stFiltro = "";
    $boExisteCodTerminal = false;

    if( $this->getTimestampTerminal() )
        $stFiltro .= " timestamp_terminal != '".$this->getTimestampTerminal()."' AND";

    if( $this->getCodTerminal() )
        $stFiltro .= " cod_terminal = ".$this->getCodTerminal()." AND";

    $stFiltro .= " timestamp_desativado is null AND";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obTTesourariaTerminal            =  new TTesourariaTerminal;
    $obErro = $obTTesourariaTerminal->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if (!$obErro->ocorreu()) {
        if(!$rsRecordSet->eof()) $boExisteCodTerminal = true;
    }

    return $obErro;
}

function verificaExistenciaCodigoVerificador(&$boExisteCodigoVerificador, $boTransacao = "")
{
    include_once( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php" );
    $stFiltro = "";
    $boExisteCodigoVeririficador = false;

    if( $this->getTimestampTerminal() )
        $stFiltro .= " timestamp_terminal != '".$this->getTimestampTerminal()."' AND";

    if( $this->getCodVerificador() )
        $stFiltro .= " cod_verificador = '".$this->getCodVerificador()."' AND";

    $stFiltro .= " timestamp_desativado is null AND";
    $stFiltro = ($stFiltro) ? " WHERE " . substr($stFiltro, 0, strlen($stFiltro)-4) : "";
    $obTTesourariaTerminal            =  new TTesourariaTerminal;
    $obErro = $obTTesourariaTerminal->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if (!$obErro->ocorreu()) {
        if(!$rsRecordSet->eof()) $boExisteCodigoVerificador = true;
    }

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function fecharTerminal(&$obRTesourariaBoletim, $boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"             );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaFechamento.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaFechamento          =  new TTesourariaFechamento;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( !$obErro->ocorreu() ) {
            $obTTesourariaFechamento->setDado( "cod_terminal"         , $this->getCodTerminal()                    );
            $obTTesourariaFechamento->setDado( "timestamp_terminal"   , $this->getTimestampTerminal()              );
            $obTTesourariaFechamento->setDado( "timestamp_abertura"   , $this->getTimestampAbertura()              );
            $obTTesourariaFechamento->setDado( "cgm_usuario"          , $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM());
            $obTTesourariaFechamento->setDado( "timestamp_usuario"    , $obRTesourariaBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario());
            $obTTesourariaFechamento->setDado( "timestamp_fechamento" , date( "Y-m-d H:i:s.ms" )                   );
            $obTTesourariaFechamento->setDado( "cod_boletim"          , $obRTesourariaBoletim->getCodBoletim()     );
            $obTTesourariaFechamento->setDado( "cod_entidade"         , $obRTesourariaBoletim->obROrcamentoEntidade->getCodigoEntidade() );
            $obTTesourariaFechamento->setDado( "exercicio_boletim"    , $obRTesourariaBoletim->getExercicio()      );

            $obErro = $obTTesourariaFechamento->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaFechamento );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function fecharTodosTerminais($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"             );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaFechamento.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaFechamento          =  new TTesourariaFechamento;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->listarSituacaoAbertosAtivos($rsTerminais);
        if ( !$obErro->ocorreu() ) {
            if ($rsTerminais->getNumLinhas()>0) {
                while (!$rsTerminais->eof()) {
                    $obTTesourariaFechamento->setDado( "cod_terminal"         , $rsTerminais->getCampo("cod_terminal"));
                    $obTTesourariaFechamento->setDado( "timestamp_terminal"   , $rsTerminais->getCampo("timestamp_terminal"));
                    $obTTesourariaFechamento->setDado( "cgm_usuario"          , $rsTerminais->getCampo("cgm_usuario"));
                    $obTTesourariaFechamento->setDado( "timestamp_usuario"    , $rsTerminais->getCampo("timestamp_usuario"));
                    $obTTesourariaFechamento->setDado( "timestamp_abertura"   , $rsTerminais->getCampo("timestamp_abertura"));
                    $obTTesourariaFechamento->setDado( "exercicio_boletim"    , $rsTerminais->getCampo("exercicio_boletim"));
                    $obTTesourariaFechamento->setDado( "cod_entidade"         , $rsTerminais->getCampo("cod_entidade"));
                    $obTTesourariaFechamento->setDado( "cod_boletim"          , $rsTerminais->getCampo("cod_boletim"));
                    $obErro = $obTTesourariaFechamento->inclusao( $boTransacao );

                    if( $obErro->ocorreu() )
                        break;

                    $rsTerminais->proximo();
                }
            } else {
              $obErro->setDescricao( "Não existem terminais abertos para esta data!" );
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaFechamento );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $obRTesourariaBoletim Objeto Boletim
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function abrirTerminal(&$obRTerminalBoletim, $boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaAbertura.class.php" );
    $obTransacao      =  new Transacao;
    $obTTesourariaAbertura            =  new TTesourariaAbertura;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if( !count( $this->arUsuarioTerminal ) ) $this->addUsuarioTerminal();
        $this->roUltimoUsuario->roRTesourariaTerminal->setCodTerminal($this->getCodTerminal());
        $this->roUltimoUsuario->roRTesourariaTerminal->setTimestampTerminal($this->getTimestampTerminal());
        $obErro = $this->roUltimoUsuario->listar($rsUsuario, '', '', $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $obTTesourariaAbertura->setDado( "cod_terminal"         , $this->getCodTerminal()                    );
            $obTTesourariaAbertura->setDado( "timestamp_terminal"   , $this->getTimestampTerminal()              );
            $obTTesourariaAbertura->setDado( "timestamp_abertura"   , date( "Y-m-d H:i:s.ms" )                   );
            $obTTesourariaAbertura->setDado( "cgm_usuario"          , $obRTerminalBoletim->obRTesourariaUsuarioTerminal->obRCGM->getNumCGM());
            $obTTesourariaAbertura->setDado( "timestamp_usuario"    , $obRTerminalBoletim->obRTesourariaUsuarioTerminal->getTimestampUsuario());
            $obTTesourariaAbertura->setDado( "cod_boletim"          , $obRTerminalBoletim->getCodBoletim()       );
            $obTTesourariaAbertura->setDado( "exercicio_boletim"    , $obRTerminalBoletim->getExercicio()        );
            $obTTesourariaAbertura->setDado( "cod_entidade"         , $obRTerminalBoletim->obROrcamentoEntidade->getCodigoEntidade() );
            $obErro = $obTTesourariaAbertura->inclusao( $boTransacao );
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaAbertura );

    return $obErro;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function abrirTodosTerminais($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"              );
    include_once ( CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php"     );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaAbertura.class.php" );
    $obTransacao           =  new Transacao;
    $obRTesourariaBoletim  =  new RTesourariaBoletim();
    $obTTesourariaAbertura =  new TTesourariaAbertura;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto( $rsBoletim, '', $boTransacao );
        if ( !$obErro->ocorreu() ) {
            while ( !$rsBoletim->eof() ) {
                $obRTesourariaBoletim->setExercicio ( $rsBoletim->getCampo( "exercicio"   ) );
                $obRTesourariaBoletim->setCodBoletim( $rsBoletim->getCampo( "cod_boletim" ) );
                $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $rsBoletim->getCampo( "cod_entidade" ) );
                $obErro = $this->listarSituacaoPorBoletim( $rsTerminais , $obRTesourariaBoletim, 'fechado', '', $boTransacao );

                if ( !$obErro->ocorreu() ) {
                    if ($rsTerminais->getNumLinhas()>0) {
                        while (!$rsTerminais->eof()) {
                            $obTTesourariaAbertura->setDado( "cod_terminal"         , $rsTerminais->getCampo("cod_terminal"));
                            $obTTesourariaAbertura->setDado( "timestamp_terminal"   , $rsTerminais->getCampo("timestamp_terminal"));
                            $obTTesourariaAbertura->setDado( "cgm_usuario"          , $rsTerminais->getCampo("cgm_usuario"));
                            $obTTesourariaAbertura->setDado( "timestamp_usuario"    , $rsTerminais->getCampo("timestamp_usuario"));
                            $obTTesourariaAbertura->setDado( "exercicio_boletim"    , $rsTerminais->getCampo("exercicio_boletim"));
                            $obTTesourariaAbertura->setDado( "cod_entidade"         , $rsTerminais->getCampo("cod_entidade"));
                            $obTTesourariaAbertura->setDado( "cod_boletim"          , $rsTerminais->getCampo("cod_boletim"));
                            $obErro = $obTTesourariaAbertura->inclusao( $boTransacao );

                            if( $obErro->ocorreu() )
                                break;

                            $rsTerminais->proximo();
                        }
                    } else {
                      $obErro->setDescricao( "Não existem terminais fechados para esta data!" );
                    }
                }
                if( $obErro->ocorreu() )
                    break;
                $rsBoletim->proximo();
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaAbertura );

    return $obErro;
}

/**
    * Método para listar Terminais Ativos que estao abertos/reabertos
    * @access Public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return $obErro
*/
function listarSituacaoAbertosAtivos(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro .= " TA.timestamp_abertura    IS NOT NULL AND";
    $stFiltro .= " TTD.timestamp_desativado IS NULL AND ";
    $stFiltro .= " CASE WHEN TF.timestamp_fechamento  IS NOT NULL                   \n";
    $stFiltro .= "   THEN CASE WHEN TA.timestamp_abertura > TF.timestamp_fechamento \n";
    $stFiltro .= "          THEN TRUE                                               \n";
    $stFiltro .= "          ELSE FALSE                                              \n";
    $stFiltro .= "        END                                                       \n";
    $stFiltro .= "   ELSE TRUE                                                      \n";
    $stFiltro .= " END AND ";
    $obErro = $this->listarSituacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para listar Terminais Ativos que estao fechados
    * @access Public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return $obErro
*/
function listarSituacaoFechadosAtivos(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    $stFiltro .= " TTD.timestamp_desativado IS NULL AND ";
    $stFiltro .= " TF.timestamp_fechamento IS NOT NULL AND ";
    $stFiltro .= " TF.timestamp_fechamento >= TA.timestamp_abertura AND ";
    $obErro = $this->listarSituacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um proximoCodigo na Persistente
    * @access Public
    * @param Object $boTransacao
    * @return Object Objeto Erro
*/
function buscaProximoCodigo($boTransacao = "")
{
    include_once ( CAM_FW_BANCO_DADOS   ."Transacao.class.php"           );
    include_once ( CAM_GF_TES_MAPEAMENTO."TTesourariaTerminal.class.php" );
     $obTransacao               =  new Transacao;
     $obTTesourariaTerminal     =  new TTesourariaTerminal;
     $obErro = $obTTesourariaTerminal->proximoCod( $inCodTerminal, $obTransacao );
     $this->inCodTerminal = $inCodTerminal;

     return $obErro;
}

}
