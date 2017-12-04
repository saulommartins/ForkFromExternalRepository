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
* Página Oculta de Procura de Conta Receita
* Data de Criação: 02/11/2008

* @author Analista: Heleno Menezes dos Santos
* @author Desenvolvedor: Fellipe Esteves dos Santos

* @package URBEM
* @subpackage

* Casos de uso: uc-02.09.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoReceita.class.php" );

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

switch ($stCtrl) {
    case "mascaraClassificacao":
        $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['inCodClassificacao'] );
        $js .= "f.inCodClassificacao.value = '".$arMascClassificacao[1]."'; \n";
        SistemaLegado::executaIFrameOculto( $js );
    break;
    case 'buscaContaReceita':

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
                $inCodConta = $rsLista->getCampo("cod_conta");
            } else {
                SistemaLegado::exibeAviso("Receita não possui conta analítica na contabilidade ou não possui classificação econômica cadastrada! (".$_POST[ $_GET['stNomCampoCod'] ].")","","");
            }
            SistemaLegado::executaFrameOculto($js."retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        } else {
            SistemaLegado::executaFrameOculto("retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', '".$_GET['stNomForm']."', '".$stDescricao."')");
        }
    break;
}
?>
