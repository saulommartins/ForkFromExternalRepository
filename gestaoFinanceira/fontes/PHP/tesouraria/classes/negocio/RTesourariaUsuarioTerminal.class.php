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
    * Classe de Regra de Negócio para usuario terminal
    * Data de Criação   : 06/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Regra

    $Id: RTesourariaUsuarioTerminal.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 31936 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.02,uc-02.04.20
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                          );
include_once ( CAM_GF_TES_MAPEAMENTO   ."TTesourariaUsuarioTerminal.class.php"         );
include_once ( CAM_GF_TES_MAPEAMENTO   ."TTesourariaUsuarioTerminalExcluido.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO      ."RCGM.class.php"                               );
include_once ( CAM_GA_ADM_NEGOCIO      ."RUsuario.class.php"              );

/**
    * Classe de Regra de Assinatura
    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo
*/
class RTesourariaUsuarioTerminal extends RUsuario
{
/*
    * @var Object
    * @access Private
*/
var $roRTesourariaTerminal;
/*
    * @var String
    * @access Private
*/
var $stTimestampUsuario;
/*
    * @var String
    * @access Private
*/
var $stTimestampExcluido;
/*
    * @var Boolean
    * @access Private
*/
var $boResponsavel;

/*
    * @access Public
    * @param String $valor
*/
function setTimestampUsuario($valor) { $this->stTimestampUsuario           = $valor; }
/*
    * @access Public
    * @param String $valor
*/
function setTimestampExcluido($valor) { $this->stTimestampExcluido          = $valor; }
/*
    * @access Public
    * @param Boolean $valor
*/
function setResponsavel($valor) { $this->boResponsavel                = $valor; }

/*
    * @access Public
    * @return String
*/
function getTimestampUsuario() { return $this->stTimestampUsuario;           }
/*
    * @access Public
    * @return String
*/
function getTimestampExcluido() { return $this->stTimestampExcluido;          }
/*
    * @access Public
    * @return Boolean
*/
function getResponsavel() { return $this->boResponsavel;                }

/**
    * Método Construtor
    * @access Private
*/
function RTesourariaUsuarioTerminal(&$roRTesourariaTerminal)
{
    parent::RUsuario();
    $this->roRTesourariaTerminal                =  &$roRTesourariaTerminal;
}

/**
    * Salva os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    $obTransacao                          =  new Transacao;
    $obTTesourariaUsuarioTerminal         =  new TTesourariaUsuarioTerminal;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaUsuarioTerminal->setDado( "timestamp_usuario" , $this->stTimestampUsuario                           );
        $obTTesourariaUsuarioTerminal->setDado( "timestamp_terminal", $this->roRTesourariaTerminal->getTimestampTerminal());
        $obTTesourariaUsuarioTerminal->setDado( "cod_terminal"      , $this->roRTesourariaTerminal->getCodTerminal()      );
        $obTTesourariaUsuarioTerminal->setDado( "cgm_usuario"       , $this->obRCGM->getNumCGM()                          );
        $obTTesourariaUsuarioTerminal->setDado( "responsavel"       , $this->boResponsavel                                );
        $obErro = $obTTesourariaUsuarioTerminal->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaUsuarioTerminal );

    return $obErro;
}

function alterar($boTransacao = "")
{
    $obTransacao                          =  new Transacao;
    $obTTesourariaUsuarioTerminal         =  new TTesourariaUsuarioTerminal;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaUsuarioTerminal->setDado( "timestamp_usuario" , $this->stTimestampUsuario                           );
        $obTTesourariaUsuarioTerminal->setDado( "timestamp_terminal", $this->roRTesourariaTerminal->getTimestampTerminal());
        $obTTesourariaUsuarioTerminal->setDado( "cod_terminal"      , $this->roRTesourariaTerminal->getCodTerminal()      );
        $obTTesourariaUsuarioTerminal->setDado( "cgm_usuario"       , $this->obRCGM->getNumCGM()                          );
        $obTTesourariaUsuarioTerminal->setDado( "responsavel"       , $this->boResponsavel                                );
        $obErro = $obTTesourariaUsuarioTerminal->alteracao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaUsuarioTerminal );

    return $obErro;
}

/**
    * Apaga os dados no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    $obTransacao                          =  new Transacao;
    $obTTesourariaUsuarioTerminalExcluido =  new TTesourariaUsuarioTerminalExcluido;
    $obErro = $obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTTesourariaUsuarioTerminalExcluido->setDado( "timestamp_usuario" , $this->stTimestampUsuario                           );
        $obTTesourariaUsuarioTerminalExcluido->setDado( "timestamp_excluido", $this->stTimestampExcluido                          );
        $obTTesourariaUsuarioTerminalExcluido->setDado( "timestamp_terminal", $this->roRTesourariaTerminal->getTimestampTerminal());
        $obTTesourariaUsuarioTerminalExcluido->setDado( "cod_terminal"      , $this->roRTesourariaTerminal->getCodTerminal()      );
        $obTTesourariaUsuarioTerminalExcluido->setDado( "cgm_usuario"       , $this->obRCGM->getNumCGM()                          );
        $obErro = $obTTesourariaUsuarioTerminalExcluido->inclusao( $boTransacao );
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTesourariaUsuarioTerminalExcluido );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe de Mapeamento
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsRecordSet, $stFiltro = "", $stOrder = "", $boTransacao = "")
{
    if( $this->stTimestampUsuario )
        $stFiltro .= " AND TUT.timestamp_usuario = '".$this->stTimestampUsuario."' ";
    if( $this->roRTesourariaTerminal->getTimestampTerminal() )
        $stFiltro .= " AND TUT.timestamp_terminal = '".$this->roRTesourariaTerminal->getTimestampTerminal()."' ";
    if( $this->roRTesourariaTerminal->getCodTerminal() )
        $stFiltro .= " AND TUT.cod_terminal = ".$this->roRTesourariaTerminal->getCodTerminal();
    if( $this->obRCGM->getNumCGM() >= 0 and $this->obRCGM->getNumCGM() != null )
        $stFiltro .= " AND TUT.cgm_usuario = ".$this->obRCGM->getNumCGM();
    if( $this->roRTesourariaTerminal->getCodVerificador() )
        $stFiltro .= " AND TT.cod_verificador = '".$this->roRTesourariaTerminal->getCodVerificador()."' ";

    $stOrder = ($stOrder) ? $stOrder : " TUT.cod_terminal,TUT.cgm_usuario";
    $obTTesourariaUsuarioTerminal         =  new TTesourariaUsuarioTerminal;
    $obErro = $obTTesourariaUsuarioTerminal->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarUsuariosAtivosTerminalAtivo(&$rsRecordSet, $boTransacao = "")
{
    $stOrder = "";
    $stFiltro  = " AND TTD.cod_terminal is null ";
    $stFiltro .= " AND TUTE.cod_terminal is null ";
    $obErro = $this->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarUsuariosAtivos(&$rsRecordSet, $boTransacao = "")
{
    $stOrder = "";
    $stFiltro  = " AND  TUTE.timestamp_excluido is null ";
    $obErro = $this->listar( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Método para verificar se o usuário tem permissão no terminal
    * @access Public
    * @param Object $boTransacao
    * @return Object $obErro
*/
function verificarPermissaoUsuarioTerminal($boTransacao = "")
{
    $obErro = $this->listar( $rsRecordSet, '', '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if( $rsRecordSet->eof() )
        $obErro->setDescricao( "Este usuário não tem permissão neste terminal para realizar esta operação!" );
    }

    return $obErro;
}

}
