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
* Arquivo de popup de busca de Recurso
/ Data de Criação: 01/09/2006

* @author Analista: Cleisson Barboza
* @author Desenvolvedor: José Eduardo Porto

$Id: IPopUpEstruturalReceita.class.php 59612 2014-09-02 12:00:51Z gelson $

 Casos de uso: uc-02.01.06
*/

/*
$Log: IPopUpEstruturalReceita.class.php,v $
Revision 1.6  2007/06/21 22:01:45  rodrigo_sr
Bug#9436#

Revision 1.5  2007/06/12 21:18:47  cako
Bug #9349#

Revision 1.4  2007/05/11 02:25:57  diego
Bug #9113#

Revision 1.3  2006/11/08 12:42:11  cako
Inclusão da função setUsaFiltro, para utilizar ou não filtro quando abrindo a PopUp . Padrão = false.

Revision 1.2  2006/09/04 12:08:54  jose.eduardo
Ajustes

Revision 1.1  2006/09/01 15:05:50  jose.eduardo
Inclusão de componente

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CLA_BUSCAINNER );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class  IPopUpEstruturalReceita extends BuscaInner
{
var $usaFiltro;
var $boDedutora;

function setUsaFiltro($valor) { $this->usaFiltro = $valor; }
function setDedutora($valor) { $this->boDedutora = $valor; }

function getUsaFiltro() { return $this->usaFiltro; }
function getDedutora() { return $this->boDedutora; }

function IPopUpEstruturalReceita($boDedutora = "")
{
    Sessao::remove('linkPopUp');
    //sessao->linkPopUp = null;

    parent::BuscaInner();

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;

    $obTAdministracaoConfiguracao->setDado( "cod_modulo", 8);
    $obTAdministracaoConfiguracao->setDado( "exercicio", Sessao::getExercicio() );
    if($boDedutora)
        $obTAdministracaoConfiguracao->pegaConfiguracao( $stMascara, "masc_class_receita_dedutora" );
    else
        $obTAdministracaoConfiguracao->pegaConfiguracao( $stMascara, "masc_class_receita" );

    if ($boDedutora) {
        $this->setRotulo               ( "Classificação da Dedutora" );
        $this->setTitle                ( "Informe o código estrutural da dedutora." );
    } else {
        $this->setRotulo               ( "Classificação da Receita" );
        $this->setTitle                ( "Informe o código estrutural da receita." );
    }
    $this->setNull                 ( true );
    $this->setId                   ( "stDescricaoReceita" );
    $this->obCampoCod->setName     ( "stCodReceita" );
    $this->obCampoCod->setId       ( "stCodReceita" );
    $this->obCampoCod->setValue    ( "" );
    $this->obCampoCod->setAlign    ("left");
    $this->obCampoCod->setMascara  ( $stMascara );
    $this->obCampoCod->setPreencheComZeros ( 'D' );
    $this->setUsaFiltro            ( true );

    if($boDedutora) $this->setDedutora( true );
}

function montaHTML()
{
    $pgOcul = "'".CAM_GF_ORC_PROCESSAMENTO."OCEstruturalReceita.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";
    if ($this->getDedutora()) {
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'receitaDedutora');" );
    } else {
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript($pgOcul,'buscaPopup');" );
    }
    $stTipoBusca = $this->getDedutora() ? "receitaDedutora" : "estrutural";
    if ($this->usaFiltro) {
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_ORC_POPUPS."receita/FLReceita.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$stTipoBusca."','".Sessao::getId()."','800','550');");
    } else
        $this->setFuncaoBusca ( "abrePopUp('".CAM_GF_ORC_POPUPS."receita/LSReceita.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','".$stTipoBusca."','".Sessao::getId()."','800','550');");

    parent::montaHTML();
}
}
?>
