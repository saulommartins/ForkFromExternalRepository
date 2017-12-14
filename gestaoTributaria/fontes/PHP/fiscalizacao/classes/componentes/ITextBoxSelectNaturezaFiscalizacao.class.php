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
    * Arquivo ITextBoxSelectNaturezaFiscalizacao
    * Data de Criação: 24/07/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Bruno Ferreira

    * @package URBEM
    * @subpackage

*/

include_once ( CAM_GT_FIS_MAPEAMENTO."TFISNaturezaFiscalizacao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextBoxSelectNaturezaFiscalizacao extends Objeto
{
    public $obTxtNaturezaFiscalizacao;
    public $obCmbNaturezaFiscalizacao;

    public function SetNull($boNull = false)
    {
        $this->obTxtNaturezaFiscalizacao->setNull ( $boNull );
        $this->obCmbNaturezaFiscalizacao->setNULL ( $boNull );
    }

    public function setTitle($stTitle)
    {
        $this->obTxtNaturezaFiscalizacao->setTitle ( $stTitle );
        $this->obCmbNaturezaFiscalizacao->setTitle ( $stTitle );
    }

    public function ITextBoxSelectNaturezaFiscalizacao()
    {

        $obTFISNaturezaFiscalizacao = new TFISNaturezaFiscalizacao;
        $obTFISNaturezaFiscalizacao->recuperaTodos( $rsNaturezaFiscalizacao );

        $this->obTxtNaturezaFiscalizacao = new TextBox;
        $this->obTxtNaturezaFiscalizacao->setRotulo  ( 'Natureza da Fiscalização');
        $this->obTxtNaturezaFiscalizacao->setTitle   ( 'Informe a Natureza da fiscalização.');
        $this->obTxtNaturezaFiscalizacao->setName    ( 'inNaturezaFiscalizacao');
        $this->obTxtNaturezaFiscalizacao->setInteiro ( true );
        $this->obTxtNaturezaFiscalizacao->setNull    ( true );

        $this->obCmbNaturezaFiscalizacao = new Select;
        $this->obCmbNaturezaFiscalizacao->setRotulo       ( "Natureza da Fiscalizacao" );
        $this->obCmbNaturezaFiscalizacao->setTitle        ( "Informe a Natureza da Fiscalizacao." );
        $this->obCmbNaturezaFiscalizacao->setName         ( "cmbNaturezaFiscalizacao" );
        $this->obCmbNaturezaFiscalizacao->addOption       ( "", "Selecione" );
        $this->obCmbNaturezaFiscalizacao->setCampoId      ( "cod_natureza" );
        $this->obCmbNaturezaFiscalizacao->setCampoDesc    ( "descricao" );
    $this->obCmbNaturezaFiscalizacao->preencheCombo   ( $rsNaturezaFiscalizacao );
        $this->obCmbNaturezaFiscalizacao->setStyle        ( "width: 40%;" );
        $this->obCmbNaturezaFiscalizacao->setNULL         ( true );
   }

   public function geraFormulario(&$obFormulario)
   {
       $obFormulario->addComponenteComposto  ( $this->obTxtNaturezaFiscalizacao, $this->obCmbNaturezaFiscalizacao );
   }

}
?>
