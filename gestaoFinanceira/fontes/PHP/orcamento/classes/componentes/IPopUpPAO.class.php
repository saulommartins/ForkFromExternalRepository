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
    * Popup de busca do PAO
    * Data de Criação: 11/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30824 $
    $Name$
    $Author: souzadl $
    $Date: 2007-07-17 11:49:55 -0300 (Ter, 17 Jul 2007) $

    * Casos de uso: uc-02.01.03
*/

/*
$Log$
Revision 1.1  2007/07/17 14:49:55  souzadl
construção

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
class IPopUpPAO extends BuscaInner
{
/**
    * Método construtor
    * @access Private
*/
function IPopUpPAO($opcoes = array())
{
    parent::BuscaInner();

    $stDescricaoPAO = "";

    if (!is_array($opcoes)) {
        $opcoes = array('extensao' => $opcoes);
    }
    if (!isset($opcoes['extensao'])) {
        $opcoes['extensao'] = "";
    }

    if (!isset($opcoes['exercicio']) || $opcoes['exercicio'] == "") {
        $opcoes['exercicio'] = Sessao::getExercicio();
    }

    $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCIBuscaInnerPAO.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=".$opcoes['extensao']."&inExercicio=".$opcoes['exercicio']."'";

    $this->setRotulo           ( "PAO" );
    $this->setTitle            ( "Clique para buscar o PAO - Projeto, Atividade ou Operações Especiais do Orçamento." );
    $this->setNullBarra        ( false );
    $this->setValue            ( $stDescricaoPAO );
    $this->setId               ( "campoInnerPAO".$opcoes['extensao'] );
    $this->obCampoCod->setName ( "inNumPAO".$opcoes['extensao'] );
    $this->obCampoCod->setId   ( "inNumPAO".$opcoes['extensao'] );
    $this->obCampoCod->obEvento->setOnChange( "ajaxJavaScript($pgOcul,'preencherPAO');" );
    $this->setFuncaoBusca      ( "abrePopUp('".CAM_GF_ORC_POPUPS."pao/FLProcurarPAO.php','frm','inNumPAO".$opcoes['extensao']."','campoInnerPAO".$opcoes['extensao']."','','".Sessao::getId()."&inExercicio=".$opcoes['exercicio']."','800','550')" );
}
}
?>
