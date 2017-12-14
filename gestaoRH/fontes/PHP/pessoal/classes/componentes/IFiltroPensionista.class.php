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
* Classe interface para Filtro de Contrato
* Data de Criação: 25/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package framework
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IFiltroPensionista extends IFiltroContrato
{
    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroPensionista()
    {
        parent::IFiltroContrato();
        $this->setTituloFormulario ("Matrícula do Pensionista");
        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->setName( "inContratoPensionista" );
        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->setId  ( "inContratoPensionista" );
        $this->obIContratoDigitoVerificador->obTxtDigitoVerificador->setName( "inDigitoVerificadorPensionista" );
        $this->obIContratoDigitoVerificador->obTxtDigitoVerificador->setId  ( "inDigitoVerificadorPensionista" );
        $this->obIContratoDigitoVerificador->setPensionista();
        $this->obLblCGM->setId("inNomCGMPensionista");
        $this->obLblCGM->setName("inNomCGMPensionista");

        $this->obHdnCGM->setName( "hdnCGMPensionista"   );

        $this->obIContratoDigitoVerificador->setFuncaoBuscaFiltro( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarMatricula.php','frm','".$this->obIContratoDigitoVerificador->obTxtRegistroContrato->getName()."','".$this->obIContratoDigitoVerificador->obTxtRegistroContrato->getId()."','','".Sessao::getId()."&stTipo=pensionista','800','550')" );
    }

    /**
        * Monta os campos do filtro do contrato
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $stOnChange = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnChange();
        $stOnBlur   = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();

        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContratoPensionista='+this.value, 'preencheCGMPensionista' );"/*.$stOnChange*/ );
        $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur   ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContratoPensionista='+this.value, 'preencheCGMPensionista' );"/*.$stOnBlur*/ );
        $obFormulario->addTitulo            ( $this->getTituloFormulario() );
        $obFormulario->addHidden            ( $this->obHdnCGM                             );
        $obFormulario->addComponente        ( $this->obLblCGM                             );
        $this->obIContratoDigitoVerificador->geraFormulario($obFormulario                 );
    }

}
?>
