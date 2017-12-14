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
    * Classe do componente Dependente
    * Data de Criação: 04/03/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package framework
    * @subpackage componentes

    $Id: IBuscaInnerCGMDependente.class.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependente.class.php"                                );

class IBuscaInnerCGMDependente extends Objeto
{
    /**
        * @access Private
        * @var Objeto
    */
    public $obBscDependente;
    /**
        * @access Private
        * @var Objeto
    */
    public $obCmbDependente;
    public $obHdnCodDependente;
    /**
        * @access Private
        * @var Objeto
    */
    public $obTPessoalDependente;

    /**
        * @access Public
        * @param Objeto $Valor
    */
    public function setDependente($valor) { $this->obBscDependente  = $valor; }
    /**
        * @access Public
        * @param Objeto $Valor
    */
    public function setTPessoalDependente($valor) { $this->obTPessoalDependente     = $valor; }

    /**
        * @access Public
        * @return Objeto
    */
    public function getDependente() { return $this->obBscDependente; }
    /**
        * @access Public
        * @return Objeto
    */
    public function getTPessoalDependente() { return $this->obTPessoalDependente; }

    /**
        * Método construtor
        * @access Private
    */
    public function IBuscaInnerCGMDependente($boFiltrarPensaoJudicial=false, $stExtensao="")
    {
        $this->setTPessoalDependente(new TPessoalDependente);
        $this->obTPessoalDependente->recuperaTodos($rsDependente, " ORDER BY numcgm DESC LIMIT 1");
        $stMascDependente   = strtr  ( $rsDependente->getCampo('numcgm') , "012345678" , "999999999" );
        $inMaxLenDependente = strlen ( $stMascDependente );

        $pgOcul = "'".CAM_GRH_PES_PROCESSAMENTO."OCIBuscaInnerCGMDependente.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=$stExtensao&boFiltrarPensaoJudicial=$boFiltrarPensaoJudicial'";

        $this->setDependente( new BuscaInner );
        $this->obBscDependente->setRotulo                         ( "CGM Dependente"                             );
        $this->obBscDependente->setTitle                          ( "Selecione o dependente."                );
        $this->obBscDependente->setId                             ( "stDependente".$stExtensao               );
        $this->obBscDependente->obCampoCod->setName               ( "inCGMDependente".$stExtensao            );
        $this->obBscDependente->obCampoCod->setValue              ( $inCodDependente                         );
        $this->obBscDependente->obCampoCod->setPreencheComZeros   ( 'D'                                      );
        $this->obBscDependente->obCampoCod->setMaxLength          ( $inMaxLenDependente                      );
        $this->obBscDependente->obCampoCod->setMascara            ( $stMascDependente                        );
        $this->obBscDependente->obCampoCod->setSize               ( 10                                       );
        $this->obBscDependente->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherDependente');"          );
        $this->obBscDependente->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript($pgOcul,'preencherDependente');"          );
        $this->obBscDependente->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgmDependente.php','frm','inCGMDependente$stExtensao','stDependente$stExtensao','','".Sessao::getId()."&boFiltrarPensaoJudicial=$boFiltrarPensaoJudicial','800','550')" );
    }

    /**
        * Monta os componentes
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente($this->obBscDependente);
    }

}
?>
