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
    * Página de Processamento de Configuração do módulo Tesouraria
    * Data de Criação   : 02/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 31732 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.04.01
*/

/*
$Log$
Revision 1.8  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_TES_NEGOCIO."RTesourariaConfiguracao.class.php" );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRTesourariaConfiguracao = new RTesourariaConfiguracao();
$obRTesourariaConfiguracao->setExercicio( Sessao::getExercicio() );
$obRTesourariaConfiguracao->setFormaComprovacao( $_POST['inFormaComprovacao'] );
$obRTesourariaConfiguracao->setNumeracaoComprovacao( $_POST['inNumeracaoComprovacao'] );
$obRTesourariaConfiguracao->setViasComprovacao( $_POST['inNumeroVias'] );
$obRTesourariaConfiguracao->setReiniciarNumeracao( $_POST['boReiniciarNumeracao'] );
$obRTesourariaConfiguracao->setDigitos( $_POST['stDigitos'] );
$boOcultarMovimentacoes = ( $_POST['stOcultarMovimentacoes'] == 'S' ) ? true : false;
$obRTesourariaConfiguracao->setOcultarMovimentacoes( $boOcultarMovimentacoes );

$arAssinatura = Sessao::read('assinaturas');

if ( count($arAssinatura) > 0 ) {
    foreach ($arAssinatura as $arValues) {
        $obRTesourariaConfiguracao->addAssinatura();
        $obRTesourariaConfiguracao->roUltimaAssinatura->obRCGM->setNumCGM( $arValues['numcgm'] );
        $obRTesourariaConfiguracao->roUltimaAssinatura->obROrcamentoEntidade->setCodigoEntidade( $arValues['cod_entidade'] );
        $obRTesourariaConfiguracao->roUltimaAssinatura->setExercicio( Sessao::getExercicio() );
        $obRTesourariaConfiguracao->roUltimaAssinatura->setCargo( $arValues['cargo'] );
        $obRTesourariaConfiguracao->roUltimaAssinatura->setTipo( "BO" );
        $boSituacao = ( $arValues['situacao'] == 't' ) ? 'true' : 'false';
        $obRTesourariaConfiguracao->roUltimaAssinatura->setSituacao( $boSituacao );
    }
}

$obErro = $obRTesourariaConfiguracao->salvar( $boTransacao );

if ( !$obErro->ocorreu() )
    SistemaLegado::alertaAviso($pgForm,"Configuração da Tesouraria","alterar","aviso", Sessao::getId(), "../");
else
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

?>
