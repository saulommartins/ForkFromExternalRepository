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
    * Arquivo ITextBoxSelectTipoEdificacao
    * Data de Criação: 18/03/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: ITextBoxSelectTipoEdificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.10

*/

/*
$Log$
*/

include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMTipoEdificacao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextBoxSelectTipoEdificacao extends Objeto
{
    public $obTxtTipoEdificacao;
    public $obCmbTipoEdificacao;

    public function SetNull($boNull = false)
    {
        $this->obTxtTipoEdificacao->setNull ( $boNull );
        $this->obCmbTipoEdificacao->setNULL ( $boNull );
    }

    public function setTitle($stTitle)
    {
        $this->obTxtTipoEdificacao->setTitle ( $stTitle );
        $this->obCmbTipoEdificacao->setTitle ( $stTitle );
    }

    public function ITextBoxSelectTipoEdificacao()
    {
        $obTCIMTipoEdificacao = new TCIMTipoEdificacao;
        $obTCIMTipoEdificacao->recuperaTodos( $rsTiposEdificacao );

        $this->obTxtTipoEdificacao = new TextBox;
        $this->obTxtTipoEdificacao->setRotulo  ( 'Tipo de Edificação');
        $this->obTxtTipoEdificacao->setTitle   ( 'Informe o tipo de edificação.');
        $this->obTxtTipoEdificacao->setName    ( 'inTipoEdificacao');
        $this->obTxtTipoEdificacao->setInteiro ( true );
        $this->obTxtTipoEdificacao->setNull    ( true );

        $this->obCmbTipoEdificacao = new Select;
        $this->obCmbTipoEdificacao->setRotulo       ( "Tipo de Edificação" );
        $this->obCmbTipoEdificacao->setTitle        ( "Informe o tipo de edificação." );
        $this->obCmbTipoEdificacao->setName         ( "cmbTipoEdificacao" );
        $this->obCmbTipoEdificacao->addOption       ( "", "Selecione" );
        $this->obCmbTipoEdificacao->setCampoId      ( "cod_tipo" );
        $this->obCmbTipoEdificacao->setCampoDesc    ( "nom_tipo" );
        $this->obCmbTipoEdificacao->preencheCombo   ( $rsTiposEdificacao );
        $this->obCmbTipoEdificacao->setStyle        ( "width: 40%;" );
        $this->obCmbTipoEdificacao->setNULL         ( true );
   }

   public function geraFormulario(&$obFormulario)
   {
       $obFormulario->addComponenteComposto  ( $this->obTxtTipoEdificacao, $this->obCmbTipoEdificacao );
   }

}
?>
