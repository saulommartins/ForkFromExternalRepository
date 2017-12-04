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

    $Id: IBuscaInnerCGMServidorDependente.class.php 59612 2014-09-02 12:00:51Z gelson $

    Casos de uso: uc-04.04.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalDependente.class.php"                                );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php"                        );

class IBuscaInnerCGMServidorDependente extends Objeto
{
    /**
        * @access Private
        * @var Objeto
    */
    public $obBscServidorDependente;

    /**
        * @access Private
        * @var Objeto
    */
    public $boPreencheCombo;

    /**
        * @access Private
        * @var Objeto
    */
    public $obCmbServidorDependente;
    public $obHdnCodServidorDependente;
    /**
        * @access Private
        * @var Objeto
    */
    public $obTPessoalServidorDependente;

    /**
        * @access Public
        * @param Objeto $Valor
    */
    public function setServidorDependente($valor) { $this->obBscServidorDependente  = $valor; }
    /**
        * @access Public
        * @param Objeto $Valor
    */
    public function setTPessoalServidorDependente($valor) { $this->obTPessoalServidorDependente     = $valor; }

    /**
        * @access Public
        * @return Objeto
    */
    public function getServidorDependente() { return $this->obBscServidorDependente; }
    /**
        * @access Public
        * @return Objeto
    */
    public function getTPessoalServidorDependente() { return $this->obTPessoalServidorDependente; }

    /**
        * @access Public
        * @return Boolean
        * Tipos:
    */
    public function getPreencheCombo() { return $this->boPreencheCombo; }

    /**
        * Método construtor
        * @access Private
    */
    public function IBuscaInnerCGMServidorDependente($boFiltrarPensaoJudicial=false, $stExtensao="")
    {
        $obTPessoalDependente = new TPessoalDependente();
        $obTPessoalDependente->recuperaTodos($rsDependente, " ORDER BY numcgm DESC LIMIT 1");
        $stMascServidorDependente   = strtr  ( $rsDependente->getCampo('numcgm') , "012345678" , "999999999" );
        $inMaxLenServidorDependente = strlen ( $stMascServidorDependente );

        $pgOcul = "'".CAM_GRH_PES_PROCESSAMENTO."OCIBuscaInnerCGMServidorDependente.php?".Sessao::getId()."&'+this.name+'='+this.value+'&stExtensao=$stExtensao&boFiltrarPensaoJudicial=$boFiltrarPensaoJudicial'";

        $this->setServidorDependente( new BuscaInner );
        $this->obBscServidorDependente->setRotulo                         ( "CGM Servidor com Dependentes"           );
        $this->obBscServidorDependente->setTitle                          ( "Selecione o servidor"                   );
        $this->obBscServidorDependente->setId                             ( "stCGMServidorDependente".$stExtensao       );
        $this->obBscServidorDependente->obCampoCod->setName               ( "inCGMServidorDependente".$stExtensao    );
        $this->obBscServidorDependente->obCampoCod->setValue              ( $inCodServidorDependente                 );
        $this->obBscServidorDependente->obCampoCod->setPreencheComZeros   ( 'D'                                      );
        $this->obBscServidorDependente->obCampoCod->setMaxLength          ( $inMaxLenServidorDependente              );
        $this->obBscServidorDependente->obCampoCod->setMascara            ( $stMascServidorDependente                );
        $this->obBscServidorDependente->obCampoCod->setSize               ( 10                                       );
        $this->obBscServidorDependente->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'preencherServidorDependente');"          );
        $this->obBscServidorDependente->obCampoCod->obEvento->setOnBlur   ( "ajaxJavaScript($pgOcul,'preencherServidorDependente');"          );
        $this->obBscServidorDependente->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_PES_POPUPS."servidor/FLProcurarCgmServidorDependente.php','frm','inCGMServidorDependente$stExtensao','stCGMServidorDependente$stExtensao','','".Sessao::getId()."&boFiltrarPensaoJudicial=$boFiltrarPensaoJudicial','800','550')" );
    }

    /**
        * Monta os componentes
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function setPreencheCombo($valor)
    {
        $this->boPreencheCombo = $valor;
        $this->obBscServidorDependente->obCampoCod->obEvento->setOnBlur( "ajaxJavaScript( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inNumCGM='+this.value+'&boRescindido=&boPreencheCombo=".$this->boPreencheCombo."&campoNum=".$this->obBscServidorDependente->obCampoCod->getName()."&campoNom=".$this->obBscServidorDependente->getId()."', 'montaContrato' );");
    }

    /**
        * Monta os componentes
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente($this->obBscServidorDependente);
    }

}
?>
