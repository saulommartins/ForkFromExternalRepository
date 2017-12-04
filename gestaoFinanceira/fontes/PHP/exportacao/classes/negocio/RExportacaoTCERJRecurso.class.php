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
    * Data de Criação   : 12/04/2005

    * @author Analista: Cassiano Vasconcelos
    * @author Desenvolvedor: Cleisson Barboza

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.08.11
*/

/*
$Log$
Revision 1.7  2006/07/05 20:46:04  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_ERRO );
include_once ( CAM_GF_EXP_MAPEAMENTO . "TExportacaoTCERJRecurso.class.php" );

class RExportacaoTCERJRecurso
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoRecurso;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoFonte;
/**
    * @access Private
    * @var Integer
*/
var $inExercicio;

/**
    * @access Private
    * @var Object
*/
var $obTExportacaoTCERJRecurso;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoRecurso($valor) { $this->inCodigoRecurso = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoFonte($valor) { $this->inCodigoFonte = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setExercicio($valor) { $this->inExercicio = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoRecurso() { return $this->inCodigoRecurso; }

/**
    * @access Public
    * @return Integer
*/
function getCodigoFonte() { return $this->inCodigoFonte;   }

/**
    * @access Public
    * @return Integer
*/
function getExercicio() { return $this->inExercicio;   }

/**
    * Método Construtor
    * @access Private
*/
function RExportacaoTCERJRecurso()
{
    $this->obTExportacaoTCERJRecurso = new TExportacaoTCERJRecurso;
}

/**
    * Método Salvar
    * @access Public
*/
function salvarRecurso($boTransacao = "")
{
    $this->obTExportacaoTCERJRecurso->setDado( "exercicio",   $this->getExercicio()     );
    $this->obTExportacaoTCERJRecurso->setDado( "cod_recurso", $this->getCodigoRecurso() );
    $this->obTExportacaoTCERJRecurso->setDado( "cod_fonte",   $this->getCodigoFonte()   );

    $this->obTExportacaoTCERJRecurso->recuperaPorChave($rsRecurso, $boTransacao);
    if ( $rsRecurso->eof() ) {
            $obErro = $this->obTExportacaoTCERJRecurso->inclusao( $boTransacao );
    } else {
            $obErro = $this->obTExportacaoTCERJRecurso->exclusao( $boTransacao );
            $obErro = $this->obTExportacaoTCERJRecurso->inclusao( $boTransacao );
    }

    return $obErro;
}

/**
    * Método Listar
    * @access Public
*/
function listarRecursos(&$rsRecordSet, $boTransacao = "")
{
    $this->obTExportacaoTCERJRecurso->setDado('exercicio', $this->getExercicio()   );
    $obErro = $this->obTExportacaoTCERJRecurso->recuperaDadosArqRecurso($rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}
?>
