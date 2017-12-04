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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Id: OCRecurso.php 59612 2014-09-02 12:00:51Z gelson $

Casos de uso: uc-02.01.05,uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoRecurso.class.php"        );

switch ($_GET['stCtrl']) {
    case 'preencheCombos':
        if ($_REQUEST['stDestinacaoRecurso']) {
            include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
            $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
            $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
            $obTOrcamentoConfiguracao->setDado("parametro","masc_recurso_destinacao");
            $obTOrcamentoConfiguracao->consultar();

            $arMascClassificacao = Mascara::validaMascaraDinamica( $obTOrcamentoConfiguracao->getDado('valor') , $_REQUEST['stDestinacaoRecurso'] );
            $stJs .= "document.getElementById('stDestinacaoRecurso').value = '".$arMascClassificacao[1]."'; \n";

            $arDestinacaoRecurso = explode('.',$arMascClassificacao[1]);
            $stJs .= "stOption = recuperaOption(document.getElementById('inCodUso'),'".$arDestinacaoRecurso[0]."');";
            $stJs .= "document.getElementById('inCodUso').options[stOption].selected = true;";
            $stJs .= "stOption = recuperaOption(document.getElementById('inCodDestinacao'),'".$arDestinacaoRecurso[1]."');";
            $stJs .= "document.getElementById('inCodDestinacao').options[stOption].selected = true;";
            $stJs .= "stOption = recuperaOption(document.getElementById('inCodEspecificacao'),'".$arDestinacaoRecurso[2]."');";
            $stJs .= "document.getElementById('inCodEspecificacao').options[stOption].selected = true;";
            $stJs .= "stOption = recuperaOption(document.getElementById('inCodDetalhamento'),'".$arDestinacaoRecurso[3]."');";
            $stJs .= "document.getElementById('inCodDetalhamento').options[stOption].selected = true;";

            include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php"        );
            $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
            $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, " WHERE cod_especificacao = ".$arDestinacaoRecurso[2]." AND exercicio = '".Sessao::getExercicio()."' " );
            $stJs .= "document.getElementById('stDescricaoRecurso').value = '".$rsEspecDestinacao->getCampo('descricao')."';";

        } else {
            $stJs .= "document.getElementById('inCodUso').options[0].selected = true;";
            $stJs .= "document.getElementById('inCodDestinacao').options[0].selected = true;";
            $stJs .= "document.getElementById('inCodEspecificacao').options[0].selected = true;";
            $stJs .= "document.getElementById('inCodDetalhamento').options[0].selected = true;";
            $stJs .= "document.getElementById('stDescricaoRecurso').value = '';";
        }
        echo $stJs;

    break;

    case 'preencheDestinacaoRecurso':
        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoConfiguracao.class.php"        );
        $obTOrcamentoConfiguracao = new TOrcamentoConfiguracao;
        $obTOrcamentoConfiguracao->setDado("exercicio", Sessao::getExercicio() );
        $obTOrcamentoConfiguracao->setDado("parametro","masc_recurso_destinacao");
        $obTOrcamentoConfiguracao->consultar();

        $arMascDestinacao    = Mascara::validaMascaraDinamica( $obTOrcamentoConfiguracao->getDado('valor') , str_replace('9','0',$obTOrcamentoConfiguracao->getDado('valor')) );
        $arMascDestinacao = explode('.',$arMascDestinacao[1]);

        $arMascClassificacao = Mascara::validaMascaraDinamica( $obTOrcamentoConfiguracao->getDado('valor') , $_REQUEST['stDestinacaoRecurso'] );
        $arDestinacaoRecurso = explode('.',$arMascClassificacao[1]);

        $_REQUEST['inCodUso']          ? $arDestinacaoRecurso[0] = $_REQUEST['inCodUso']           : $arDestinacaoRecurso[0] = $arMascDestinacao[0];
        $_REQUEST['inCodDestinacao']   ? $arDestinacaoRecurso[1] = $_REQUEST['inCodDestinacao']    : $arDestinacaoRecurso[1] = $arMascDestinacao[1];
        $_REQUEST['inCodEspecificacao']? $arDestinacaoRecurso[2] = $_REQUEST['inCodEspecificacao'] : $arDestinacaoRecurso[2] = $arMascDestinacao[2];
        $_REQUEST['inCodDetalhamento'] ? $arDestinacaoRecurso[3] = $_REQUEST['inCodDetalhamento']  : $arDestinacaoRecurso[3] = $arMascDestinacao[3];

        $stJs .= "document.getElementById('stDestinacaoRecurso').value = '$arDestinacaoRecurso[0].$arDestinacaoRecurso[1].$arDestinacaoRecurso[2].$arDestinacaoRecurso[3].'; \n";

        include_once ( CAM_GF_ORC_MAPEAMENTO."TOrcamentoEspecificacaoDestinacaoRecurso.class.php"        );
        $obTOrcamentoEspecificacaoDestinacaoRecurso = new TOrcamentoEspecificacaoDestinacaoRecurso;
        $obTOrcamentoEspecificacaoDestinacaoRecurso->recuperaTodos( $rsEspecDestinacao, " WHERE cod_especificacao = ".$arDestinacaoRecurso[2]." AND exercicio = '".Sessao::getExercicio()."' " );

        $stJs .= "document.getElementById('stDescricaoRecurso').value = '".$rsEspecDestinacao->getCampo('descricao')."';";

        echo $stJs;

    break;

    case 'buscaPopup': // IPopUpRecurso
        if ( strlen( $_REQUEST['inCodRecurso'] ) > 0 ) {
            $obRegra = new TOrcamentoRecurso();
            $rsLista = new RecordSet;
            $obRegra->setDado("cod_recurso", "'".$_REQUEST['inCodRecurso']."'" );
            $obRegra->setDado("exercicio"  , Sessao::getExercicio()        );
            $obRegra->recuperaRelacionamento( $rsLista );
            $stDescricaoRecurso = $rsLista->getCampo("nom_recurso");
            $inCodRecurso       = $rsLista->getCampo("cod_recurso");
        } else {
            $stDescricaoRecurso = null;
            $inCodRecurso = null;
        }
        $stJs  = "jQuery('inCodRecurso').val('".$inCodRecurso."'); \n";
        $stJs .= "retornaValorBscInner( 'inCodRecurso', 'stDescricaoRecurso', 'frm', '".$stDescricaoRecurso."');";
        echo $stJs;

    break;
}

?>
