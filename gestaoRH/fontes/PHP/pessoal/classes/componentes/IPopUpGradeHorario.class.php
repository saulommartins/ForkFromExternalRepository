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
/*
 * Classe do IPopUpGradeHorario
 * Data de Criação   : 13/10/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Diego Lemos de Souza

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class  IPopUpGradeHorario extends BuscaInner
{
var $stFuncaoGrade;

function setFuncaoGrade($stValor) {$this->stFuncaoGrade = $stValor;}

function getFuncaoGrade() {return $this->stFuncaoGrade;}

/**
    * Método construtor
    * @access Private
*/
function IPopUpGradeHorario()
{
    parent::BuscaInner();

    $pgOcul = "'".CAM_GRH_PES_PROCESSAMENTO."OCIPopUpGradeHorario.php?".Sessao::getId()."&'+this.name+'='+this.value";

    $this->setRotulo("Grade de Horários");
    $this->setTitle("Selecione a grade de horários.");
    $this->setId("stGrade");
    $this->obCampoCod->setName("inCodGrade");
    $this->obCampoCod->setId("inCodGrade");
    $this->obCampoCod->setSize(10);
    $this->obCampoCod->obEvento->setOnChange("ajaxJavaScript($pgOcul,'preencherGradeHorarios');");
    $this->setFuncaoBusca("abrePopUp('".CAM_GRH_PES_POPUPS."gradeHorario/FLProcurarGradeHorarios.php','frm','inCodGrade','stGrade','','".Sessao::getId()."','800','550')");
}
}
?>
