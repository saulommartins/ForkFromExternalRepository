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
    * Paginae Oculta para funcionalidade Manter receitas
    * Data de Criação   : 08/09/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30835 $
    $Name$
    $Autor:$
    $Date: 2007-08-13 15:55:06 -0300 (Seg, 13 Ago 2007) $

    * Casos de uso: uc-02.04.03

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterReceitas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

function montaLista($arRecordSet , $stTipoConta ,$boExecuta = true)
{
    /*
    $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "alterar" );
        $obLista->ultimaAcao->addCampo( "stExercicio"        	, "exercicio"        );
        $obLista->ultimaAcao->addCampo( "inCodConta"      		, "cod_plano"     	 );
        $obLista->ultimaAcao->setLink( "FMDetalhamentoReceitas.php?stAcao=alterar&".Sessao::getId().$stLink );
        $obLista->commitAcao();
    */
    $arRecordSetNew = array();
    $inCount        = 0;
    for ( $x  = 0; $x < count( $arRecordSet ); $x++ ) {
        $inCodPlanoOld = $arRecordSet[$x-1]['cod_plano'];
        if ($arRecordSet[$x]['cod_plano'] != $inCodPlanoOld) {
            $arRecordSetNew[$inCount]['cod_plano']  = $arRecordSet[$x]['cod_plano'];
            $arRecordSetNew[$inCount]['nom_conta']  = $arRecordSet[$x]['nom_conta'];
            $arRecordSetNew[$inCount]['exercicio']  = $arRecordSet[$x]['exercicio'];
            $arRecordSetNew[$inCount]['cod_entidade']  = $arRecordSet[$x]['cod_entidade'];
            $arRecordSetNew[$inCount]['cod_credto'] = $arRecordSet[$x]['cod_credito'];
//            $arRecordSetNew[$inCount]['link']       = "<a href='javascript:detalhaConta(\"".$arRecordSet[$x]['exercicio']."\", \"".$arRecordSet[$x]['cod_plano']."\");'>Detalhar</a>";
            $arRecordSetNew[$inCount]['link']       = "<a href='FMDetalhamentoReceitas.php?stAcao=alterar&".Sessao::getId().$stLink."&stExercicio=".$arRecordSet[$x]['exercicio']."&stCodEntidade=".$arRecordSet[$x]['cod_entidade']."&inCodConta=".$arRecordSet[$x]['cod_plano']."'>Detalhar</a>";
            $arRecordSetNew[$inCount]['visivel']    = false;
            $stCredito = $arRecordSet[$x]['cod_credito'].'.'.$arRecordSet[$x]['cod_especie'].'.'.$arRecordSet[$x]['cod_genero'].'.'.$arRecordSet[$x]['cod_natureza'];
            $arRecordSetNew[$inCount]['credito'] = ( str_replace( '.', '', $stCredito ) ) ? $stCredito : '';
            if ($arRecordSet[$x+1]['cod_plano'] == $arRecordS1et[$x]['cod_plano']) {
                $inCount++;
                $arRecordSetNew[$inCount]['cod_plano']     = '&nbsp;';
                $stCredito = $arRecordSet[$x]['cod_credito'].'.'.$arRecordSet[$x]['cod_especie'].'.'.$arRecordSet[$x]['cod_genero'].'.'.$arRecordSet[$x]['cod_natureza'];
                $arRecordSetNew[$inCount]['nom_conta']     = '     Crédito '.$stCredito.' - '.$arRecordSet[$x]['descricao_credito'] ;
                $arRecordSetNew[$inCount]['link']          = '&nbsp;';
                $arRecordSetNew[$inCount]['visivel']       = true;
                $arRecordSetNew[$inCount-1]['visivel']       = true;
            }
        } else {
            $arRecordSetNew[$inCount]['cod_plano']     = '&nbsp;';
            $stCredito = $arRecordSet[$x]['cod_credito'].'.'.$arRecordSet[$x]['cod_especie'].'.'.$arRecordSet[$x]['cod_genero'].'.'.$arRecordSet[$x]['cod_natureza'];
            if ($arRecordSet[$x]['descricao_acrescimo']) {
                $stNomConta = '     Crédito '.$stCredito.' - '.$arRecordSet[$x]['descricao_credito'] . " - Acréscimo " .$arRecordSet[$x]['descricao_acrescimo'];
            } else {
                $stNomConta = '     Crédito '.$stCredito.' - '.$arRecordSet[$x]['descricao_credito'];
            }
            $arRecordSetNew[$inCount]['nom_conta']     = $stNomConta;
            $arRecordSetNew[$inCount]['link']          = '&nbsp;';
            $arRecordSetNew[$inCount]['visivel']       = true;
            $arRecordSetNew[$inCount-1]['cod_credito'] = '&nbsp;';
        }
        $inCount++;
    }
    $arRecordSet = $arRecordSetNew;
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Cod. Reduzido");
        $obLista->ultimoCabecalho->setWidth( 12 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição");
        $obLista->ultimoCabecalho->setWidth( 60 );
        $obLista->commitCabecalho();
        //$obLista->addCabecalho();
        //$obLista->ultimoCabecalho->addConteudo("Cod. Credito");
        //$obLista->ultimoCabecalho->setWidth( 15 );
        //$obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth(  8 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "cod_plano" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_conta" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        //Define objeto BuscaInner para credito
        //$obBscCredito = new BuscaInner;
        //$obBscCredito->setRotulo              ( "Crédito"                       );
        //$obBscCredito->setTitle               ( "Selecione o Código do Crédito" );
        //$obBscCredito->setNull                ( false                           );
        //$obBscCredito->obCampoCod->setName    ( "stCodCredito_".$stTipoConta."_[exercicio]_[cod_plano]_" );
        //$obBscCredito->obCampoCod->setValue   ( "credito"                       );
        //$obBscCredito->obCampoCod->setMascara (  '9.9.9.9'                      );
        //$obBscCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_MON_POPUPS."credito/FLProcurarCredito.php','frm','stCodCredito','','todos','".Sessao::getId()."','800','550')");
        //$obBscCredito->setValoresBusca( CAM_GT_MON_POPUPS.'credito/OCProcurarCredito.php?'.Sessao::getId(), 'frm' );

        //$obLista->addDadoComponente( $obBscCredito );
        //$obLista->ultimoDado->setOcultaComponente( 'visivel' );
        //$obLista->commitDadoComponente();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( 'link' );
        $obLista->ultimoDado->setAlinhamento( "CENTRO" );
        $obLista->commitDado();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."';");
        } else {
            return $stHTML;
        }
}

switch ($stCtrl) {
    case 'creditos_receita':

        if ($_REQUEST['tipo_receita'] == 'orcamentaria') {
            require_once( CAM_GF_ORC_MAPEAMENTO . 'TOrcamentoReceita.class.php');
            $obTReceita = new TOrcamentoReceita();
            $obTReceita->setDado('codigo', $_REQUEST['codigo']);
            $obTReceita->setDado('exercicio', Sessao::getExercicio());
            $obTReceita->recuperaClassReceitasCreditosOrcamentarios( $rsCreditos );
        } elseif ($_REQUEST['tipo_receita'] == 'extra') {
            require_once( CAM_GF_CONT_MAPEAMENTO . 'TContabilidadePlanoConta.class.php');
            $obTPlano = new TContabilidadePlanoConta();
            $obTPlano->setDado('codigo', $_REQUEST['codigo']);
            $obTPlano->setDado('exercicio', Sessao::getExercicio());
            $obTPlano->recuperaClassReceitasCreditosExtraOrcamentarios( $rsCreditos );
        }

        for ( $i = 0; $i < count($rsCreditos->arElementos); $i++ ) {
            if ($rsCreditos->arElementos[$i]['descricao_acrescimo'] == '') {
                $rsCreditos->arElementos[$i]['descricao_acrescimo'] = 'Principal';
            }
        }

        $table = new Table();
        $table->setRecordset($rsCreditos);
        $table->setSummary( 'Créditos e Acréscimos vinculados' );
        //$table->setConditional( true , "#efefef" );

        $table->Head->addCabecalho( 'Código' , 10  );
        $table->Head->addCabecalho( 'Descrição' , 40  );
        $table->Head->addCabecalho( 'Acréscimo' , 30  );

        $table->Body->addCampo( 'codigo', 'C' );
        $table->Body->addCampo( 'desc', 'E' );

        $table->Body->addCampo( 'descricao_acrescimo', 'E' );
        $table->montaHTML();

        echo $table->getHTML();

            break;
    case 'montaLista':

        $arFiltro = Sessao::read('filtro');

        if ($arFiltro['stTipoReceita'] != 'extra') {
            $stHTML = montaLista( Sessao::read('arOrcamentaria'), 'orcamentaria', false );
            $stJs .= "d.getElementById('spnListaOrcamentaria').innerHTML = '".$stHTML."';";
        }
        if ($arFiltro['stTipoReceita'] != 'orcamentaria') {
            $stHTML = montaLista( Sessao::read('arExtra'), 'extra', false );
            $stJs .= "d.getElementById('spnListaExtra').innerHTML = '".$stHTML."';";
        }
        SistemaLegado::executaFrameOculto( $stJs );
    break;
    case 'mostraSpanContas':

        // Define objeto BuscaInner para Conta Inicial
        $obBscContaInicial = new BuscaInner();
        $obBscContaInicial->setRotulo                 ( "Conta Inicial"      );
        $obBscContaInicial->setTitle                  ( "Informe o Código Estrutural das Contas a Classificar" );
        $obBscContaInicial->setId                     ( "stDescContaInicial" );
        $obBscContaInicial->setNull                   ( true                 );
        $obBscContaInicial->obCampoCod->setName       ( "stContaInicial"     );
        $obBscContaInicial->obCampoCod->setSize       ( 10                   );
        $obBscContaInicial->obCampoCod->setMaxLength  ( 8                    );
        $obBscContaInicial->obCampoCod->setAlign      ( "left"               );
        $obBscContaInicial->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','stContaInicial','stDescContaInicial','&tipoBusca2=receitas_primarias','".Sessao::getId()."','800','550');");
        $obBscContaInicial->setValoresBusca           ( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), 'frm', 'orcamento_extra&tipoBusca2=receitas_primarias' );

        // Define objeto BuscaInner para Conta Final
        $obBscContaFinal = new BuscaInner();
        $obBscContaFinal->setRotulo                 ( "Conta Final"      );
        $obBscContaFinal->setTitle                  ( "Informe o Código Estrutural das Contas a Classificar" );
        $obBscContaFinal->setId                     ( "stDescContaFinal" );
        $obBscContaFinal->setNull                   ( true               );
        $obBscContaFinal->obCampoCod->setName       ( "stContaFinal"     );
        $obBscContaFinal->obCampoCod->setSize       ( 10                 );
        $obBscContaFinal->obCampoCod->setMaxLength  ( 8                  );
        $obBscContaFinal->obCampoCod->setAlign      ( "left"             );
        $obBscContaFinal->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','stContaFinal','stDescContaFinal','".$_REQUEST['stTipoReceita']."&tipoBusca2=receitas_primarias','".Sessao::getId()."','800','550');");
        $obBscContaFinal->setValoresBusca           ( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), 'frm', 'orcamento_extra&tipoBusca2=receitas_primarias' );

              $paramTipoReceita = $_REQUEST['stTipoReceita'];

                if ($paramTipoReceita == 'extra') {
            // Define objeto BuscaInner para Conta Inicial
            $obBscContaInicial = new BuscaInner();
            $obBscContaInicial->setRotulo                 ( "Conta Inicial"      );
            $obBscContaInicial->setTitle                  ( "Informe o Código Estrutural das Contas a Classificar" );
            $obBscContaInicial->setId                     ( "stDescContaInicial" );
            $obBscContaInicial->setNull                   ( true                 );
            $obBscContaInicial->obCampoCod->setName       ( "stContaInicial"     );
            $obBscContaInicial->obCampoCod->setSize       ( 10                   );
            $obBscContaInicial->obCampoCod->setMaxLength  ( 8                    );
            $obBscContaInicial->obCampoCod->setAlign      ( "left"               );
            $obBscContaInicial->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','stContaInicial','stDescContaInicial','tes_arrecadacao_extra_receita','".Sessao::getId()."','800','550');");
            $obBscContaInicial->setValoresBusca           ( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), 'frm', 'tes_arrecadacao_extra_receita' );
            $obBscContaInicial->obCampoCod->obEvento->setOnChange( "  if (this.value == '0') { this.value = ''; alertaAviso('@Esta conta não é Extra-Orçamentária.','form','erro','".Sessao::getId()."'); \n } ");

            $obBscContaFinal = new BuscaInner();
            $obBscContaFinal->setRotulo                 ( "Conta Final"      );
            $obBscContaFinal->setTitle                  ( "Informe o Código Estrutural das Contas a Classificar" );
            $obBscContaFinal->setId                     ( "stDescContaFinal" );
            $obBscContaFinal->setNull                   ( true               );
            $obBscContaFinal->obCampoCod->setName       ( "stContaFinal"     );
            $obBscContaFinal->obCampoCod->setSize       ( 10                 );
            $obBscContaFinal->obCampoCod->setMaxLength  ( 8                  );
            $obBscContaFinal->obCampoCod->setAlign      ( "left"             );
            $obBscContaFinal->setFuncaoBusca            ("abrePopUp('".CAM_GF_CONT_POPUPS."planoConta/FLPlanoConta.php','frm','stContaFinal','stDescContaFinal','tes_arrecadacao_extra_receita','".Sessao::getId()."','800','550');");
            $obBscContaFinal->setValoresBusca           ( CAM_GF_CONT_POPUPS.'planoConta/OCPlanoConta.php?'.Sessao::getId(), 'frm', 'tes_arrecadacao_extra_receita' );
            $obBscContaFinal->obCampoCod->obEvento->setOnChange( "  if (this.value == '0') { this.value = ''; alertaAviso('@Esta conta não é Extra-Orçamentária.','form','erro','".Sessao::getId()."'); \n } ");
                } else {
                    include_once(CAM_GF_ORC_COMPONENTES."IIntervaloPopUpEstruturalReceita.class.php" );
                    $obIntervaloEstrutural    = new IIntervaloPopUpEstruturalReceita();
                    $obIntervaloEstrutural->obIPopUpEstruturalReceitaInicial->setUsaFiltro ( true );
                    $obIntervaloEstrutural->obIPopUpEstruturalReceitaFinal->setUsaFiltro ( true );

                    include_once ( CAM_GF_ORC_COMPONENTES."IIntervaloPopUpReceita.class.php" );
                    $obIIntervaloPopUpReceita = new IIntervaloPopUpReceita();
                    $obIIntervaloPopUpReceita->obIPopUpReceitaInicial->setUsaFiltro ( true );
                    $obIIntervaloPopUpReceita->obIPopUpReceitaFinal->setUsaFiltro ( true );

                }
        $obFormulario = new Formulario;
                if ($paramTipoReceita == 'extra') {
            $obFormulario->addComponente( $obBscContaInicial );
            $obFormulario->addComponente( $obBscContaFinal );
                } else {
                    $obFormulario->addComponente( $obIntervaloEstrutural );
                    $obFormulario->addComponente( $obIIntervaloPopUpReceita );
                }
                $obFormulario->montaInnerHTML();
        $stHTML = $obFormulario->getHTML ();

        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\\'","\\'",$stHTML );

        SistemaLegado::executaFrameOculto($js."d.getElementById('spnContas').innerHTML = '".$stHTML."';");

    break;

}
