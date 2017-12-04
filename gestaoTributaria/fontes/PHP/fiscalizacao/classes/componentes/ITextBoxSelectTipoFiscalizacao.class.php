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
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage

*/

include_once ( CAM_GT_FIS_MAPEAMENTO."TFISTipoFiscalizacao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextBoxSelectTipoFiscalizacao extends Objeto
{
    public $obTxtTipoFiscalizacao;
    public $obCmbTipoFiscalizacao;

    public function setValue($inCodTipo)
    {
        $this->obTxtTipoFiscalizacao->setValue( $inCodTipo );
        $this->obCmbTipoFiscalizacao->setValue( $inCodTipo );
    }

    public function setNull($boNull = '')
    {
        $this->obTxtTipoFiscalizacao->setNull ( $boNull );
        $this->obCmbTipoFiscalizacao->setNULL ( $boNull );
    }

    public function setTitle($stTitle)
    {
        $this->obTxtTipoFiscalizacao->setTitle ( $stTitle );
        $this->obCmbTipoFiscalizacao->setTitle ( $stTitle );
    }

    public function ITextBoxSelectTipoFiscalizacao()
    {

        $obTFISTipoFiscalizacao = new TFISTipoFiscalizacao;
        $obTFISTipoFiscalizacao->recuperaTodos( $rsTipoFiscalizacao,' WHERE cod_tipo IN(1,2)' );
        $this->obTxtTipoFiscalizacao = new TextBox;

        $this->obTxtTipoFiscalizacao->setRotulo  ( 'Tipo de Fiscalização');
        $this->obTxtTipoFiscalizacao->setTitle   ( 'Informe o tipo de fiscalização.');
        $this->obTxtTipoFiscalizacao->setName    ( 'inTipoFiscalizacao');
        $this->obTxtTipoFiscalizacao->setInteiro ( true );

        $this->obCmbTipoFiscalizacao = new Select;
        $this->obCmbTipoFiscalizacao->setRotulo       ( "Tipo de fiscalizacao" );
        $this->obCmbTipoFiscalizacao->setTitle        ( "Informe o Tipo de Fiscalizacao." );
        $this->obCmbTipoFiscalizacao->setName         ( "cmbTipoFiscalizacao" );
        $this->obCmbTipoFiscalizacao->addOption       ( "", "Selecione" );
        $this->obCmbTipoFiscalizacao->setCampoId      ( "cod_tipo" );
        $this->obCmbTipoFiscalizacao->setCampoDesc    ( "descricao" );
        $this->obCmbTipoFiscalizacao->preencheCombo   ( $rsTipoFiscalizacao );
        $this->obCmbTipoFiscalizacao->setStyle        ( "width: 40%;" );

   }

   public function geraFormulario(&$obFormulario)
   {
       $obFormulario->addComponenteComposto  ( $this->obTxtTipoFiscalizacao, $this->obCmbTipoFiscalizacao );
   }

}
