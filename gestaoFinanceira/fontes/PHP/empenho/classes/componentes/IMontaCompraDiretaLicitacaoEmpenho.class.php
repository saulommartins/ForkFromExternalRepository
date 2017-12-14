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
    * Arquivo que monta campos de compra direta e licitação para filtro de lista
    * Data de Criação:	29/08/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Arthur Cruz
*/

include_once ( CLA_OBJETO );

class IMontaCompraDiretaLicitacaoEmpenho extends Objeto
{
    public $obForm;
    public $obISelectModalidadeCompra;
    public $obTxtCompraInicial;
    public $obLabelDireta;
    public $obTxtCompraFinal;
    public $obISelectModalidadeLicitacao;
    public $obTxtLicitacaoInicial;
    public $obLabelLicitacao;
    public $obTxtLicitacaoFinal;
    
    public $stName;
    public $stRotulo;

    public function setRotulo($valor) { $this->stRotulo = $valor; }
    public function setName($valor) { $this->stName   = $valor; }
    
    public function getRotulo() { return $this->stRotulo; }
    public function getName() { return $this->stNme;    }

    public function IMontaCompraDiretaLicitacaoEmpenho(&$obForm)
    {
        parent::Objeto();
        
        include_once ( CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php" );
        
        //Compra Direta  
        $this->obISelectModalidadeCompra = new Select();
        $this->obISelectModalidadeCompra->setNull    ( true                            );
        
        $this->obISelectModalidadeCompra->setTitle   ( "Selecione a modalidade."       );
        $this->obISelectModalidadeCompra->setRotulo  ( " Modalidade"                    );
        $this->obISelectModalidadeCompra->setName    ( "inCodModalidadeCompra"         );
        $this->obISelectModalidadeCompra->setCampoID ( "inCodModalidadeCompra"         );
        $this->obISelectModalidadeCompra->addOption  ( "" ,"Selecione"                 );
        $this->obISelectModalidadeCompra->addOption  ( "8","8 - Dispensa de Licitação" );
        $this->obISelectModalidadeCompra->addOption  ( "9","9 - Inexibilidade"         );
        
        $this->obTxtCompraInicial = new TextBox;
        $this->obTxtCompraInicial->setName           ( "inCompraInicial" );
        $this->obTxtCompraInicial->setId             ( "inCompraInicial" );
        $this->obTxtCompraInicial->setRotulo         ( "Compra Direta" );
        $this->obTxtCompraInicial->setTitle          ( "Informe o intervalo de Compra Direta." );
        $this->obTxtCompraInicial->setNull           ( true );
        $this->obTxtCompraInicial->setInteiro        ( true );
    
        $this->obLabeCompra = new Label;
        $this->obLabeCompra->setValue ( " até " );
    
        $this->obTxtCompraFinal = new TextBox;
        $this->obTxtCompraFinal->setName             ( "inCompraFinal" );
        $this->obTxtCompraFinal->setId               ( "inCompraFinal" );
        $this->obTxtCompraFinal->setRotulo           ( "Compra" );
        //$this->obTxtCompraFinal->setTitle            ( "" );
        $this->obTxtCompraFinal->setNull             ( true );
        $this->obTxtCompraFinal->setInteiro          ( true );
        
        //Licitação
        $this->obISelectModalidadeLicitacao = new ISelectModalidade();
        $this->obISelectModalidadeLicitacao->setNull    ( true );
        $this->obISelectModalidadeLicitacao->setCampoID ( "inCodModalidadeLicitacao" );
        $this->obISelectModalidadeLicitacao->setName    ( "inCodModalidadeLicitacao" );
        
        $this->obTxtLicitacaoInicial = new TextBox;
        $this->obTxtLicitacaoInicial->setName           ( "inLicitacaoInicial" );
        $this->obTxtLicitacaoInicial->setId             ( "inLicitacaoInicial" );
        $this->obTxtLicitacaoInicial->setRotulo         ( "Licitação" );
        $this->obTxtLicitacaoInicial->setTitle          ( "Informe o intervalo de Licitação." );
        $this->obTxtLicitacaoInicial->setNull           ( true );
        $this->obTxtLicitacaoInicial->setInteiro        ( true );
    
        $this->obLabelLicitacao = new Label;
        $this->obLabelLicitacao->setValue( " até " );
    
        $this->obTxtLicitacaoFinal = new TextBox;
        $this->obTxtLicitacaoFinal->setName             ( "inLicitacaoFinal" );
        $this->obTxtLicitacaoFinal->setId               ( "inLicitacaoFinal" );
        $this->obTxtLicitacaoFinal->setRotulo           ( "Licitação" );
        //$this->obTxtLicitacaoFinal->setTitle            ( '' );
        $this->obTxtLicitacaoFinal->setNull             ( true );
        $this->obTxtLicitacaoFinal->setInteiro          ( true );
    }

    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addTitulo( "Compra Direta"  );
        $obFormulario->addComponente( $this->obISelectModalidadeCompra );
        $obFormulario->agrupaComponentes( array( $this->obTxtCompraInicial, $this->obLabeCompra, $this->obTxtCompraFinal ) );
        
        $obFormulario->addTitulo( "Licitação"  );
        $obFormulario->addComponente( $this->obISelectModalidadeLicitacao );
        $obFormulario->agrupaComponentes( array( $this->obTxtLicitacaoInicial, $this->obLabelLicitacao, $this->obTxtLicitacaoFinal ) );
    }
}
?>
