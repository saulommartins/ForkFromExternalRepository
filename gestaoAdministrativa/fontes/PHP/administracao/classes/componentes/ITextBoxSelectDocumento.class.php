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
    * Arquivo que monta o combo do modelo de documento
    * Data de Criação: 13/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.03.100
                    uc-03.05.15

*/

include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );

/*
    administracao.modelo_documento e administracao.modelo_arquivo_documento
    iTextBoxDocumento.class.php
    ga/fontes/php/administracao/classes/componentes
    ga/php/administracao/instancias/modeloDocumento/OCManterModeloDocumento.php
*/

$pgOc  = CAM_GA_ADM_PROCESSAMENTO.'OCTextBoxSelectDocumento.php?'.Sessao::getId();

class ITextBoxSelectDocumento extends Objeto
{
    public $obTextBoxSelectDocumento;
    public $stCodModeloDocumento;
    public $obCodTipoDocumento;
    public $stCodAcao;
    public $obTModeloDocumento;
    public $boDisabledSelectDoc = false;

    public function ITextBoxSelectDocumento()
    {
        $this->stCodAcao = 1;

        $this->obTModeloDocumento = new TAdministracaoModeloDocumento;
        $this->obTextBoxSelectDocumento = new TextBoxSelect;

        $this->obTextBoxSelectDocumento->setRotulo              ( "Modelo de Documento" );
        $this->obTextBoxSelectDocumento->setName                ( "stCodDocumento" );
        $this->obTextBoxSelectDocumento->setTitle               ( "Selecione o documento." );

        $this->obTextBoxSelectDocumento->obTextBox->setRotulo   ( "Documento"              );
        $this->obTextBoxSelectDocumento->obTextBox->setTitle    ( "Selecione o documento." );
        $this->obTextBoxSelectDocumento->obTextBox->setName     ( "stCodDocumentoTxt" );
        $this->obTextBoxSelectDocumento->obTextBox->setId       ( "stCodDocumentoTxt" );
        $this->obTextBoxSelectDocumento->obTextBox->setSize     ( 12 );
        $this->obTextBoxSelectDocumento->obTextBox->setMaxLength( 10 );
        $this->obTextBoxSelectDocumento->obTextBox->setInteiro  ( true );
        $this->obTextBoxSelectDocumento->obTextBox->setCaracteresAceitos( "[0-9]" );

        $this->obTextBoxSelectDocumento->obSelect->setRotulo    ( "Documento" );
        $this->obTextBoxSelectDocumento->obSelect->setName      ( "stCodDocumento" );
        $this->obTextBoxSelectDocumento->obSelect->setId        ( "stCodDocumento" );
        $this->obTextBoxSelectDocumento->obSelect->setCampoID   ( "cod_documento" );
        $this->obTextBoxSelectDocumento->obSelect->setCampoDesc ( "nome_documento" );
        $this->obTextBoxSelectDocumento->obSelect->addOption    ( "", "Selecione" );
        $this->obTextBoxSelectDocumento->obSelect->setStyle     ( "width: 200px" );

        $this->obCodTipoDocumento = new Hidden();
        $this->obCodTipoDocumento->setName( 'inCodTipoDocumento' );
   }

   public function setCodModeloDocumento($stValor)
   {
       $this->stCodModeloDocumento = $stValor;
   }

   public function setCodAcao($stValor)
   {
       $this->stCodAcao = $stValor;
   }

   public function setDisabledSelectDoc($stValor) { $this->boDisabledSelectDoc = $stValor;}
   public function geraFormulario(&$obFormulario)
   {
       global $pgOc;
       if ($this->stCodAcao) {
            $stFiltro = "where a.cod_acao = '".$this->stCodAcao."'";
            $this->obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);
            if ( !$rsDocumentos->Eof() ) {
                $this->obTextBoxSelectDocumento->obSelect->preencheCombo( $rsDocumentos );

            }

            if ($this->stCodModeloDocumento) {
                $this->obTextBoxSelectDocumento->obTextBox->setValue( $this->stCodModeloDocumento );
                $this->obTextBoxSelectDocumento->obSelect->setValue( $this->stCodModeloDocumento );

        $this->obTextBoxSelectDocumento->obTextBox->setDisabled ( $this->boDisabledSelectDoc);
        $this->obTextBoxSelectDocumento->obSelect->setDisabled ( $this->boDisabledSelectDoc );
            }
       }
       $this->obTextBoxSelectDocumento->obSelect->obEvento->setOnChange($this->obTextBoxSelectDocumento->obSelect->obEvento->getOnChange()."ajaxJavaScript('".$pgOc."&stCodDocumento='+this.value+'&stCodAcao=".$this->stCodAcao."', 'buscaTipo');");
       $this->obTextBoxSelectDocumento->obTextBox->obEvento->setOnBlur($this->obTextBoxSelectDocumento->obTextBox->obEvento->getOnBlur()."ajaxJavaScript('".$pgOc."&stCodDocumento='+this.value+'&stCodAcao=".$this->stCodAcao."', 'buscaTipo');");
       $obFormulario->addComponente( $this->obTextBoxSelectDocumento );
       $obFormulario->addHidden( $this->obCodTipoDocumento );
   }

}
?>
