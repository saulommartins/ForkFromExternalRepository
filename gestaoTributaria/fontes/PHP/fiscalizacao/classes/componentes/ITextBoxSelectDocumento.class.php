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

    * @author Analista: Heleno Santos
    * @author Desenvolvedor: Jânio Eduardo

    * @package URBEM
    * @subpackage

*/

include_once ( CAM_GT_FIS_MAPEAMENTO."TFISDocumento.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextBoxSelectDocumento extends Objeto
{
    public $obTxtDocumento;
    public $obCmbDocumento;

    public function SetNull($boNull = false)
    {
        $this->obTxtDocumento->setNull ( $boNull );
        $this->obCmbDocumento->setNULL ( $boNull );
    }

    public function setTitle($stTitle)
    {
        $this->obTxtDocumento->setTitle ( $stTitle );
        $this->obCmbDocumento->setTitle ( $stTitle );
    }

    public function ITextBoxSelectDocumento($criterio)
    {
    $stFiltro = ' WHERE cod_tipo_fiscalizacao ='.$criterio;
        $obTFISDocumento = new TFISDocumento;
        $obTFISDocumento->recuperaTodos( $rsDocumento,$stFiltro);

    $this->obTxtDocumento = new TextBox;
        $this->obTxtDocumento->setRotulo  ( 'Documento');
        $this->obTxtDocumento->setTitle   ( 'Documento que será solicitado para atividade solicitada.');
        $this->obTxtDocumento->setName    ( 'inDocumento');
        $this->obTxtDocumento->setInteiro ( true );
        $this->obTxtDocumento->setNull    ( true );

        $this->obCmbDocumento = new Select;
        $this->obCmbDocumento->setRotulo       ( "Documento" );
        $this->obCmbDocumento->setTitle        ( "Documento que será solicitado para atividade solicitada." );
        $this->obCmbDocumento->setName         ( "cmbDocumento" );
        $this->obCmbDocumento->addOption       ( "", "Selecione" );
        $this->obCmbDocumento->setCampoId      ( "cod_documento" );
        $this->obCmbDocumento->setCampoDesc    ( "nom_documento" );
    $this->obCmbDocumento->preencheCombo   ( $rsDocumento );
        $this->obCmbDocumento->setStyle        ( "width: 40%;" );
        $this->obCmbDocumento->setNULL         ( true );
   }

   public function geraFormulario(&$obFormulario)
   {
      $obFormulario->addComponenteComposto( $this->obTxtDocumento, $this->obCmbDocumento );

   }

}
