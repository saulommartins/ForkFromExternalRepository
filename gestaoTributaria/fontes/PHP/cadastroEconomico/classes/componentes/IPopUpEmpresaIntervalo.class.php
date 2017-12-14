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
    * Arquivo que monta inner de busca empresas
    * Data de Criação: 13/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage

    * $Id: IPopUpEmpresaIntervalo.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.6  2007/01/11 12:54:26  dibueno
Bug #8042#

Revision 1.3  2006/09/15 11:57:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomico.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class IPopUpEmpresaIntervalo extends Objeto
{
    public $obInnerEmpresaIntervalo;
    public $obTCEMCadastroEconomico;
    public $obTAdministracaoConfiguracao;
    public $inInscricaoEconomicaInicial;
    public $inInscricaoEconomicaFinal;
    public $stMascaraInscricao;
    public $obHdnCampo2;
    public $boVerificaInscricao;

    public function IPopUpEmpresaIntervalo()
    {
        ;
        $this->boVerificaInscricao = true;
        $this->obTCEMCadastroEconomico = new TCEMCadastroEconomico;

        $this->obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $this->obTAdministracaoConfiguracao->setDado( "cod_modulo", 14 );
        $this->obTAdministracaoConfiguracao->setDado( "exercicio" , Sessao::getExercicio() );
        $this->obTAdministracaoConfiguracao->setDado( "parametro" , "mascara_inscricao_economica");
        $this->obTAdministracaoConfiguracao->recuperaPorChave( $rsConfiguracao );
        if ( !$rsConfiguracao->Eof() ) {
            $this->stMascaraInscricao = $rsConfiguracao->getCampo( "valor" ) ;
        }

        $this->obInnerEmpresaIntervalo = new BuscaInnerIntervalo;
        $this->obInnerEmpresaIntervalo->setRotulo         ( "Inscrição Econômica"    );
        $this->obInnerEmpresaIntervalo->setTitle          ( "Intervalo de valores para inscrição econômica.");
        $this->obInnerEmpresaIntervalo->obLabelIntervalo->setValue ( "até"            );
        $this->obInnerEmpresaIntervalo->obCampoCod->setName       ("inNumInscricaoEconomicaInicial"  );
        $this->obInnerEmpresaIntervalo->obCampoCod->setSize     ( strlen( $this->stMascaraInscricao ) );
        $this->obInnerEmpresaIntervalo->obCampoCod->setMaxLength( strlen( $this->stMascaraInscricao ) );
        $this->obInnerEmpresaIntervalo->obCampoCod->setMascara  ( $this->stMascaraInscricao   );

        $this->obInnerEmpresaIntervalo->setFuncaoBusca("abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaInicial','stCampo','todos','".Sessao::getId()."','800','550');" );

        $this->obInnerEmpresaIntervalo->obCampoCod2->setName          ( "inNumInscricaoEconomicaFinal" );
        $this->obInnerEmpresaIntervalo->obCampoCod2->setSize     ( strlen( $this->stMascaraInscricao ) );
        $this->obInnerEmpresaIntervalo->obCampoCod2->setMaxLength( strlen( $this->stMascaraInscricao ) );
        $this->obInnerEmpresaIntervalo->obCampoCod2->setMascara  ( $this->stMascaraInscricao   );

        $this->obInnerEmpresaIntervalo->setFuncaoBusca2( "abrePopUp('".CAM_GT_CEM_POPUPS."inscricaoeconomica/FLProcurarInscricaoEconomica.php','frm','inNumInscricaoEconomicaFinal','stCampo','todos','".Sessao::getId()."','800','550');" );

        $this->obHdnCampo2 =  new Hidden;
        $this->obHdnCampo2->setName   ( "stCampo" );
        $this->obHdnCampo2->setId     ( "stCampo" );
    }

    public function setVerificaInscricao($valor)
    {
        $this->boVerificaInscricao = $valor;
    }

    public function setInscricaoEconomicaInicial($inValor)
    {
        $this->inInscricaoEconomicaInicial = $inValor;
    }

    public function setInscricaoEconomicaFinal($inValor)
    {
        $this->inInscricaoEconomicaFinal = $inValor;
    }

    public function geraFormulario(&$obFormulario)
    {
        ;
        if ($this->inInscricaoEconomicaInicial) {
                $stFiltro = " AND CE.inscricao_economica = ".$this->inInscricaoEconomicaInicial;
                $this->obTCEMCadastroEconomico->recuperaListaConsulta( $rsEmpresas, $stFiltro );
                if ( !$rsEmpresas->eof() ) {
                    $this->obInnerEmpresaIntervalo->obCampoCod->setValue( $this->inInscricaoEconomicaInicial );
                }
        }

        if ($this->inInscricaoEconomicaFinal) {
                $stFiltro = " AND CE.inscricao_economica = ".$this->inInscricaoEconomicaFinal;
                $this->obTCEMCadastroEconomico->recuperaListaConsulta( $rsEmpresas, $stFiltro );
                if ( !$rsEmpresas->eof() ) {
                    $this->obInnerEmpresaIntervalo->obCampoCod2->setValue( $this->inInscricaoEconomicaFinal );
                }
        }

            $pgOcul = CAM_GT_CEM_INSTANCIAS."inscreconomica/OCManterInscricao.php?".Sessao::getId();
            $stOnChange = "ajaxJavaScript('".$pgOcul."&inInscricaoEconomica='+this.value,'PreencheEmpresaIntervaloInicial');";
            $stOnChange2 = "ajaxJavaScript('".$pgOcul."&inInscricaoEconomica='+this.value,'PreencheEmpresaIntervaloFinal');";
        if ($this->boVerificaInscricao) {
            $this->obInnerEmpresaIntervalo->obCampoCod->obEvento->setOnChange( $stOnChange );
            $this->obInnerEmpresaIntervalo->obCampoCod2->obEvento->setOnChange( $stOnChange2 );
        }
            $obFormulario->addHidden ( $this->obHdnCampo2 );
            $obFormulario->addComponente( $this->obInnerEmpresaIntervalo );
    }
}
?>
