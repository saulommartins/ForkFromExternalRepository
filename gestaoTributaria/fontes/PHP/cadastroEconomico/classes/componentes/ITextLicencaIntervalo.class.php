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
    * Arquivo que monta inner de busca licenca
    * Data de Criação: 15/02/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: ITextLicencaIntervalo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12

*/

/*
$Log$
*/

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicenca.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ITextLicencaIntervalo extends Objeto
{
    public $boNull;
    public $inCodLicencaInicio;
    public $inCodLicencaFim;
    public $stMascara;
    public $stTipoLicenca;
    public $obLblPeriodo;
    public $obTxtLicencaInicio;
    public $obTxtLicencaFim;
    public $obHdnTipoLicenca;

    public function setTipoLicenca($valor) { $this->stTipoLicenca = $valor; }
    public function getTipoLicenca() { return $this->stTipoLicenca; }
    public function setNull($valor) { $this->boNull = $valor; }
    public function getNull() { return $this->boNull; }

    public function ITextLicencaIntervalo()
    {
        #parent::BuscaInner();
        ;

        $this->obHdnTipoLicenca = new Hidden;
        $this->obHdnTipoLicenca->setName ('stTipoLicenca');
        $this->obHdnTipoLicenca->setName ( $this->getTipoLicenca() );

        $this->obTxtLicencaInicio = new TextBox;
        $this->obTxtLicencaFim = new TextBox;

        $obTConfiguracao = new TAdministracaoConfiguracao;
        $obTConfiguracao->setDado ( 'cod_modulo', 14 );
        $obTConfiguracao->setDado ( 'parametro', 'numero_licenca' );
        $obTConfiguracao->setDado ( 'exercicio', Sessao::getExercicio() );
        $obTConfiguracao->recuperaPorChave ( $rsNumeroLicenca );
        $inNumeroLicenca = $rsNumeroLicenca->getCampo('valor');

        $obTConfiguracao->setDado ( 'parametro', 'mascara_licenca' );
        $obTConfiguracao->recuperaPorChave ( $rsMascaraLicenca );
        $contNumLicenca = strlen ( $rsMascaraLicenca->getCampo('valor') );
        $i = 0;
        $this->stMascara = null;
        while ($i < $contNumLicenca) {
            $this->stMascara .= "9";
            $i++;
        }

        if ( $contNumLicenca <= 0 )
            $this->stMascara .= "9";

        if ( $inNumeroLicenca != 0 )
            $this->stMascara .= '/9999';

        $inNumeroCaracteres = strlen ( $this->stMascara );

        $this->obTxtLicencaInicio->setRotulo      ( 'Número da Licença' );
        $this->obTxtLicencaInicio->setTitle       ( 'Selecione o número da Licença.' );
        $this->obTxtLicencaInicio->setName        ( "stLicencaInicio" );
        $this->obTxtLicencaInicio->setSize        ( $inNumeroCaracteres );
        $this->obTxtLicencaInicio->setMaxLength   ( $inNumeroCaracteres );
        $this->obTxtLicencaInicio->setAlign       ( "left" );
        $this->obTxtLicencaInicio->setMascara	  ( $this->stMascara );
        $this->obTxtLicencaInicio->setInteiro     ( false );

        $this->obLblPeriodo = new Label;
        $this->obLblPeriodo->setValue( " até " );

        $this->obTxtLicencaFim->setRotulo      ( 'Número da Licença' );
        $this->obTxtLicencaFim->setTitle       ( 'Selecione o número da Licença.' );
        $this->obTxtLicencaFim->setName        ( "stLicencaFim" );
        $this->obTxtLicencaFim->setSize        ( $inNumeroCaracteres );
        $this->obTxtLicencaFim->setMaxLength   ( $inNumeroCaracteres );
        $this->obTxtLicencaFim->setAlign       ( "left" );
        $this->obTxtLicencaFim->setMascara     ( $this->stMascara );
        $this->obTxtLicencaFim->setInteiro     ( false );

        $pgOcul = CAM_GT_CEM_INSTANCIAS."licenca/OCManterLicenca.php?".Sessao::getId();
        $stOnChange = "ajaxJavaScriptSincrono('".$pgOcul."&".$this->obTxtLicencaInicio->getName()."='+this.value+'&stNomCampoCod=".$this->obTxtLicencaInicio->getName()."','buscaLicencaComponente');";
        $this->obTxtLicencaInicio->obEvento->setOnChange ( $stOnChange );

        $stOnChange = "ajaxJavaScriptSincrono('".$pgOcul."&".$this->obTxtLicencaFim->getName()."='+this.value+'&stNomCampoCod=".$this->obTxtLicencaFim->getName()."','buscaLicencaComponente');";
        $this->obTxtLicencaFim->obEvento->setOnChange ( $stOnChange );
   }

    public function geraFormulario(&$obFormulario)
    {
           ;

        $this->obTxtLicencaInicio->setNull        ( $this->getNull() );
        $this->obTxtLicencaFim->setNull        ( $this->getNull() );
        $obFormulario->addHidden      ( $this->obHdnTipoLicenca );
        $obFormulario->agrupaComponentes( array( $this->obTxtLicencaInicio, $this->obLblPeriodo, $this->obTxtLicencaFim ) );
    }

}
?>
