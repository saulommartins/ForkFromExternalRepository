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
* Classe de agrupamentos de objetos para o Filtro por Contrato
* Data de Criação: 10/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package framework
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class IFiltroCGMPensionista extends IFiltroCGMContrato
{
    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroCGMPensionista()
    {
        parent::IFiltroCGMContrato();

        $this->setTipoContrato("pensionista");
        $this->obBscCGM->setTipoContrato( $this->stTipoContrato );
        $this->obBscCGM->setId("inCampoInnerPensionista");
        $this->obBscCGM->obCampoCod->setName("inNumCGMPensionista");
        $this->obBscCGM->obCampoCod->obEvento->setOnBlur( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inNumCGMPensionista='+this.value, 'buscaCGMPensionista' );");
        $this->obCmbContrato->setName("inContratoPensionista");
        $this->obCmbContrato->setId("inContratoPensionista");

        $this->setTituloFormulario("Filtro por CGM/Matrícula do Pensionista");
    }

    /**
        * Monta os combos de localização conforme o nível setado
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        parent::geraFormulario($obFormulario);

    }

}
?>
