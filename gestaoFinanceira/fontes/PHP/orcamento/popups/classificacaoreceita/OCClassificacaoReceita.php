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
    * Página de Listagem de Itens
    * Data de Criação   : 03/08/2004

    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: OCClassificacaoReceita.php 60024 2014-09-25 18:10:15Z evandro $

    * Casos de uso: uc-02.01.06
*/

/*
$Log: OCClassificacaoReceita.php,v $
Revision 1.9  2006/07/19 18:30:08  jose.eduardo
Bug #6575#

Revision 1.8  2006/07/17 20:37:40  jose.eduardo
Bug #6575#

Revision 1.7  2006/07/05 20:43:48  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];
$stTipoBusca = $_GET['stTipoBusca'] ?  $_GET['stTipoBusca'] : $_POST['stTipoBusca'];

switch ($stCtrl) {
    case "mascaraClassificacao":    
        //monta mascara da RUBRICA DE DESPESA
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "parent.document.frm.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        
        echo "<script>".$js."</script>";
    break;
    case 'buscaPopup':
        if ($_POST[ $_GET['stNomCampoCod'] ]) {

            if ($_POST['stMascClassificacao']) {
                $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST[ $_GET['stNomCampoCod'] ] );
                $_POST[ $_GET['stNomCampoCod'] ] = $arMascClassificacao[1];
                $js = "if( f.inCodReceita ) f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
            }

            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;
            if($_REQUEST['stTipoBusca'] == 'receitaDedutora')
                $obROrcamentoClassificacaoReceita->setDedutora ( true );

            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST[ $_GET['stNomCampoCod'] ] );
            $obROrcamentoClassificacaoReceita->setListarAnaliticas('true');
            $obROrcamentoClassificacaoReceita->consultar( $rsLista );
            if ( $rsLista->getNumLinhas() > -1 ) {
                $stDescricao = $rsLista->getCampo("descricao");
            } else {
                SistemaLegado::exibeAviso("Receita não possui conta analítica na contabilidade ou não possui classificação econômica cadastrada! (".$_POST[ $_GET['stNomCampoCod'] ].")","","");
            }
            SistemaLegado::executaFrameOculto($js."retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        } else {
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        }
    break;
}

switch ($stTipoBusca) {
    case 'buscaAnalitica':
        if ($_POST[ $_GET['stNomCampoCod'] ]) {

            if ($_POST['stMascClassificacao']) {
                $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST[ $_GET['stNomCampoCod'] ] );
                $_POST[ $_GET['stNomCampoCod'] ] = $arMascClassificacao[1];
                $js = "if( f.inCodReceita ) f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
            }

            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;

            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST[ $_GET['stNomCampoCod'] ] );
            $obROrcamentoClassificacaoReceita->setListarAnaliticas('true');
            $obROrcamentoClassificacaoReceita->consultarReceitaAnalitica( $rsLista );
            if ( $rsLista->getCampo("tipo_nivel_conta") == 'A' ) {
                $stDescricao = $rsLista->getCampo("descricao");
            } else {
                $stDescricao = '';
                SistemaLegado::exibeAviso("Classificação informada inválida, informe uma classificação de receita analítica! (".$_POST[ $_GET['stNomCampoCod'] ].")","","");
            }            
            SistemaLegado::executaFrameOculto($js."retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        } else {
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        }
    break;
    
    case 'receitaDedutora':
        if ($_POST[ $_GET['stNomCampoCod'] ]) {

            if ($_POST['stMascClassificacao']) {
                $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST[ $_GET['stNomCampoCod'] ] );
                $_POST[ $_GET['stNomCampoCod'] ] = $arMascClassificacao[1];
                $js = "if( f.inCodReceita ) f.inCodReceita.value = '".$arMascClassificacao[1]."'; \n";
            }

            $obROrcamentoClassificacaoReceita = new ROrcamentoClassificacaoReceita;
            $obROrcamentoClassificacaoReceita->setDedutora ( true );

            $obROrcamentoClassificacaoReceita->setExercicio( Sessao::getExercicio() );
            $obROrcamentoClassificacaoReceita->setMascClassificacao( $_POST[ $_GET['stNomCampoCod'] ] );
            $obROrcamentoClassificacaoReceita->setListarAnaliticas('true');
            $obROrcamentoClassificacaoReceita->consultarReceitaAnalitica( $rsLista );
            if ( $rsLista->getCampo("tipo_nivel_conta") == 'A' ) {
                $stDescricao = $rsLista->getCampo("descricao");
            } else {
                $stDescricao = '';
                SistemaLegado::exibeAviso("Classificação informada inválida, informe uma classificação de dedutora analítica! (".$_POST[ $_GET['stNomCampoCod'] ].")","","");
            }
            SistemaLegado::executaFrameOculto($js."retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        } else {
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        }
    break;
    

}


?>
