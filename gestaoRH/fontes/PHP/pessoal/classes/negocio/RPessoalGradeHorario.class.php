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
* Classe de regra de negócio para RPessoalGradeHorario
* Data de Criação: 13/09/2005

* @author Analista: Vadré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage Regra de Negócio

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

* Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalFaixaTurno.class.php"                      );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalGradeHorario.class.php"               );

class RPessoalGradeHorario
{
/**
   * @access Private
   * @var Object
*/
var $obTransacao;
/**
   * @access Private
   * @var Integer
*/
var $inCodGrade;
/**
   * @access Private
   * @var String
*/
var $stDescricao;
/**
   * @access Private
   * @var Array
*/
var $arRPessoalFaixaTurno;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalFaixaTurno;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalGradeHorario;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao             = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodGrade($valor) { $this->inCodGrade              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao             = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRPessoalFaixaTurno($valor) { $this->arRPessoalFaixaTurno    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRORPessoalFaixaTurno($valor) { $this->roRPessoalFaixaTurno    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalGradeHorario($valor) { $this->obTPessoalGradeHorario  = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;             }
/**
    * @access Public
    * @return Integer
*/
function getCodGrade() { return $this->inCodGrade;              }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;             }
/**
    * @access Public
    * @return Array
*/
function getRPessoalFaixaTurno() { return $this->arRPessoalFaixaTurno;    }
/**
    * @access Public
    * @return Object
*/
function getRORPessoalFaixaTurno() { return $this->roRPessoalFaixaTurno;    }
/**
    * @access Public
    * @return Object
*/
function getTPessoalGradeHorario() { return $this->obTPessoalGradeHorario;  }

/**
     * Método construtor
     * @access Private
*/
function RPessoalGradeHorario()
{
    $this->setTransacao                         ( new Transacao                         );
    $this->setTPessoalGradeHorario              ( new TPessoalGradeHorario              );
    $this->setRPessoalFaixaTurno                ( array()                               );
}

/**
    * Método addFaixaTurno
    * @access Public
*/
function addFaixaTurno()
{
    $this->arRPessoalFaixaTurno[] = new RPessoalFaixaTurno( $this );
    $this->roRPessoalFaixaTurno   = &$this->arRPessoalFaixaTurno[ count($this->arRPessoalFaixaTurno) - 1 ];
}

/**
    * Método incluir
    * @access Public
*/
function incluirGrade($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count($this->arRPessoalFaixaTurno) < 1 ) {
            $obErro->setDescricao("Tunos inválidos!()");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalGradeHorario->proximoCod( $inCodGrade , $boTransacao       );
        $this->setCodGrade( $inCodGrade );
        $this->obTPessoalGradeHorario->setDado("cod_grade",$this->getCodGrade()     );
        $this->obTPessoalGradeHorario->setDado("descricao",$this->getDescricao()    );

        $obErro = $this->obTPessoalGradeHorario->inclusao($boTransacao);
        if ( !$obErro->ocorreu() ) {
            foreach ($this->arRPessoalFaixaTurno as $obRPessoalFaixaTurno) {
                $obErro = $obRPessoalFaixaTurno->incluirFaixaTurno($boTransacao);
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalGradeHorario );

    return $obErro;
}

/**
    * Método incluir
    * @access Public
*/
function alterarGrade($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count($this->arRPessoalFaixaTurno) < 1 ) {
            $obErro->setDescricao("Turnos não informados!");
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalGradeHorario->setDado("cod_grade",$this->getCodGrade()     );
        $this->obTPessoalGradeHorario->setDado("descricao",$this->getDescricao()    );
        $obErro = $this->obTPessoalGradeHorario->alteracao($boTransacao);
        if ( !$obErro->ocorreu() ) {
            foreach ($this->arRPessoalFaixaTurno as $obRPessoalFaixaTurno) {
                $obErro = $obRPessoalFaixaTurno->incluirFaixaTurno($boTransacao);
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalGradeHorario );

    return $obErro;
}

/**
    * Método excluir
    * @access Public
*/
function excluirGrade($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalGradeHorario->setDado("cod_grade",$this->getCodGrade() );
        $obErro = $this->obTPessoalGradeHorario->validaExclusao("", $boTransacao);
        if ( !$obErro->ocorreu() ) {
            $this->addFaixaTurno();
            $obErro = $this->roRPessoalFaixaTurno->excluirFaixaTurno($boTransacao);
            if ( !$obErro->ocorreu() ) {
                  $obErro = $this->obTPessoalGradeHorario->exclusao($boTransacao);
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalGradeHorario );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    $obErro = $this->obTPessoalGradeHorario->recuperaTodos($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarGrade
    * @access Public
*/
function listarGrade(&$rsRecordSet,$boTransacao)
{
    $stFiltro = "";
    if ( $this->getCodGrade() ) {
        $stFiltro .= " AND cod_grade = ".$this->getCodGrade();
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " AND descricao like '%".$this->getDescricao()."%'";
    }
    $stFiltro = ($stFiltro) ? " WHERE ".substr($stFiltro,4,strlen($stFiltro)) : "";
    $stOrder = " ORDER BY descricao";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
