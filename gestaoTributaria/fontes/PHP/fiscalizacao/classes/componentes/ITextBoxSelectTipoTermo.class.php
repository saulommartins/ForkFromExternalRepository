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
    * Arquivo ITextBoxSelectTipoTermo
    * Data de Criação: 11/11/2008

    * @author Analista     : Heleno Santos
    * @author Desenvolvedor: Marcio Medeiros

    * @package URBEM
    * @subpackage

*/

include_once ( CAM_GT_FIS_MAPEAMENTO."TFISTipoTermo.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextBoxSelectTipoTermo extends Objeto
{
    public $obTxtTipoTermo;
    public $obCmbTipoTermo;

    public function setValue($inCodTipo)
    {
        $this->obTxtTipoTermo->setValue( $inCodTipo );
        $this->obCmbTipoTermo->setValue( $inCodTipo );
    }

    public function setNull($boNull = '')
    {
        $this->obTxtTipoTermo->setNull ( $boNull );
        $this->obCmbTipoTermo->setNULL ( $boNull );
    }

    public function setTitle($stTitle)
    {
        $this->obTxtTipoTermo->setTitle ( $stTitle );
        $this->obCmbTipoTermo->setTitle ( $stTitle );
    }

    public function ITextBoxSelectTipoTermo()
    {

        $obTFISTipoTermo = new TFISTipoTermo;
        $obTFISTipoTermo->recuperaTodos( $rsTipoTermo );
        $this->obTxtTipoTermo = new TextBox;

        $this->obTxtTipoTermo->setRotulo  ( 'Tipo de Termo');
        $this->obTxtTipoTermo->setTitle   ( 'Informe a tipo de termo.');
        $this->obTxtTipoTermo->setName    ( 'inTipoTermo');
        $this->obTxtTipoTermo->setInteiro ( true );

        $this->obCmbTipoTermo = new Select;
        $this->obCmbTipoTermo->setRotulo       ( "Tipo do Termo" );
        $this->obCmbTipoTermo->setTitle        ( "Informe o Tipo do Termo." );
        $this->obCmbTipoTermo->setName         ( "cmbTipoTermo" );
        $this->obCmbTipoTermo->addOption       ( "", "Selecione" );
        $this->obCmbTipoTermo->setCampoId      ( "cod_termo" );
        $this->obCmbTipoTermo->setCampoDesc    ( "nom_termo" );
        $this->obCmbTipoTermo->preencheCombo   ( $rsTipoTermo );
        $this->obCmbTipoTermo->setStyle        ( "width: 40%;" );

   }

   public function geraFormulario(&$obFormulario)
   {
       $obFormulario->addComponenteComposto  ( $this->obTxtTipoTermo, $this->obCmbTipoTermo );
   }

}
