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
* Classe de regra de negócio para RPessoalFaixaTurno
* Data de Criação: 13/09/2005

* @author Analista: Vandré Miguel Ramos
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
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalFaixaTurno.class.php"                 );

class RPessoalFaixaTurno
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
var $inCodTurno;
/**
   * @access Private
   * @var Integer
*/
var $inCodDia;
/**
   * @access Private
   * @var String
*/
var $stHoraEntrada;
/**
   * @access Private
   * @var String
*/
var $stHoraSaida;
/**
   * @access Private
   * @var String
*/
var $stHoraEntrada2;
/**
   * @access Private
   * @var String
*/
var $stHoraSaida2;
/**
   * @access Private
   * @var Object
*/
var $obTPessoalFaixaTurno;
/**
   * @access Private
   * @var Object
*/
var $roRPessoalGradeHorario;

/**
    * @access Public
    * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao             = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTurno($valor) { $this->inCodTurno              = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodDia($valor) { $this->inCodDia              = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHoraEntrada($valor) { $this->stHoraEntrada           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHoraSaida($valor) { $this->stHoraSaida             = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHoraEntrada2($valor) { $this->stHoraEntrada2           = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setHoraSaida2($valor) { $this->stHoraSaida2             = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setTPessoalFaixaTurno($valor) { $this->obTPessoalFaixaTurno    = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRPessoalGradeHorario(&$valor) { $this->roRPessoalGradeHorario  = &$valor; }

/**
    * @access Public
    * @return Object
*/
function getTransacao() { return $this->obTransacao;             }
/**
    * @access Public
    * @return Integer
*/
function getCodTurno() { return $this->inCodTurno;              }
/**
    * @access Public
    * @return Integer
*/
function getCodDia() { return $this->inCodDia;              }
/**
    * @access Public
    * @return String
*/
function getHoraEntrada() { return $this->stHoraEntrada;           }
/**
    * @access Public
    * @return String
*/
function getHoraSaida() { return $this->stHoraSaida;             }
/**
    * @access Public
    * @return String
*/
function getHoraEntrada2() { return $this->stHoraEntrada2;         }
/**
    * @access Public
    * @return String
*/
function getHoraSaida2() { return $this->stHoraSaida2;           }
/**
    * @access Public
    * @return Object
*/
function getTPessoalFaixaTurno() { return $this->obTPessoalFaixaTurno;    }
/**
    * @access Public
    * @return Object
*/
function getRPessoalGradeHorario() { return $this->roRPessoalGradeHorario;  }

/**
     * Método construtor
     * @access Private
*/
function RPessoalFaixaTurno(&$obRPessoalGradeHorario)
{
    $this->setTransacao                         ( new Transacao                         );
    $this->setTPessoalFaixaTurno                ( new TPessoalFaixaTurno                );
    $this->setRPessoalGradeHorario              ( $obRPessoalGradeHorario              );
}

/**
    * Método incluir
    * @access Public
*/
function incluirFaixaTurno($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $tmpComplementoChave =  $this->obTPessoalFaixaTurno->getComplementoChave();
        $tmpComplementoCod   =  $this->obTPessoalFaixaTurno->getCampoCod();
        $this->obTPessoalFaixaTurno->setDado("cod_grade"      , $this->roRPessoalGradeHorario->getCodGrade()   );
        $this->obTPessoalFaixaTurno->setDado("cod_dia"        , $this->getCodDia()                             );
        $this->obTPessoalFaixaTurno->setCampoCod('cod_turno');
        $this->obTPessoalFaixaTurno->setComplementoChave('cod_grade,cod_dia');
        $this->obTPessoalFaixaTurno->proximoCod( $inCodTurno , $boTransacao );
        $this->setCodTurno( $inCodTurno );
        $this->obTPessoalFaixaTurno->setComplementoChave($tmpComplementoChave);
        $this->obTPessoalFaixaTurno->setCampoCod($tmpComplementoCod);
        $this->obTPessoalFaixaTurno->setDado("cod_turno"      , $this->getCodTurno()                           );
        $this->obTPessoalFaixaTurno->setDado("hora_entrada"   , $this->getHoraEntrada()                        );
        $this->obTPessoalFaixaTurno->setDado("hora_saida"     , $this->getHoraSaida()                          );
        $this->obTPessoalFaixaTurno->setDado("hora_entrada_2" , $this->getHoraEntrada2()                       );
        $this->obTPessoalFaixaTurno->setDado("hora_saida_2"   , $this->getHoraSaida2()                         );

        $obErro = $this->obTPessoalFaixaTurno->inclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalFaixaTurno );

    return $obErro;
}

/**
    * Método incluir
    * @access Public
*/
function excluirFaixaTurno($boTransacao="")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalFaixaTurno->setDado("cod_grade"    ,$this->roRPessoalGradeHorario->getCodGrade() );
        $obErro = $this->obTPessoalFaixaTurno->exclusao($boTransacao);
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalFaixaTurno );

    return $obErro;
}

/**
    * Método listar
    * @access Private
*/
function listar(&$rsRecordSet,$stFiltro,$stOrder,$boTransacao)
{
    $obErro = $this->obTPessoalFaixaTurno->recuperaGrade($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

/**
    * Método listarFaixaTurno
    * @access Public
*/
function listarFaixaTurno(&$rsRecordSet,$boTransacao)
{
    if ( $this->roRPessoalGradeHorario->getCodGrade() ) {
        $stFiltro .= " AND pt.cod_grade = ".$this->roRPessoalGradeHorario->getCodGrade();
    }
    $stOrder = " ORDER BY dt.cod_dia";
    $obErro = $this->listar($rsRecordSet,$stFiltro,$stOrder,$boTransacao);

    return $obErro;
}

}
?>
