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
    * Pagina de Oculto para impugnar edital
    * Data de Criação   : 13/11/2006
    * @author Analista: Gelson Wolowski Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * $Id: OCManterImpugnacaoEdital.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.27
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once(TLIC."TLicitacaoEditalImpugnado.class.php");
include_once(TLIC."TLicitacaoEdital.class.php");
//include_once(TLIC."TLicitacaoAnulacaoImpugnacaoEdital.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterImpugnacaoEdital";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js" ;

$inMascara = explode("/",SistemaLegado::pegaConfiguracao('mascara_processo', 5, Sessao::getExercicio() ));
$inMascara = strlen($inMascara[0]);
$stJs="";

function montaListaProcesso($arRecordSet , $boExecuta = true)
{
    $rsRecordSet = new RecordSet;
    if ( is_array( $arRecordSet ) ) {
        $rsRecordSet->preenche( $arRecordSet );
    }
    $obListaProcesso = new Lista();

    $obListaProcesso->setTitulo                ( "Lista de Processos Impugnados"                );
    $obListaProcesso->setMostraPaginacao( false );
    $obListaProcesso->setRecordSet( $rsRecordSet );
    $obListaProcesso->addCabecalho();

    $obListaProcesso->ultimoCabecalho->addConteudo("&nbsp;");
    $obListaProcesso->ultimoCabecalho->setWidth( 5 );
    $obListaProcesso->commitCabecalho();

    $obListaProcesso->addCabecalho();
    $obListaProcesso->ultimoCabecalho->addConteudo("Processo");
    $obListaProcesso->ultimoCabecalho->setWidth( 50 );
    $obListaProcesso->commitCabecalho();

    $obListaProcesso->addCabecalho();
    $obListaProcesso->ultimoCabecalho->addConteudo("Status");
    $obListaProcesso->ultimoCabecalho->setWidth( 40 );
    $obListaProcesso->commitCabecalho();

    $obListaProcesso->addCabecalho();
    $obListaProcesso->ultimoCabecalho->addConteudo("Ação");
    $obListaProcesso->ultimoCabecalho->setWidth( 5 );
    $obListaProcesso->commitCabecalho();

    $obListaProcesso->addDado();
    $obListaProcesso->ultimoDado->setCampo( "[inCodProcesso]/[inExercicio]" );
    $obListaProcesso->ultimoDado->setTitle( "Código do Processo" );
    $obListaProcesso->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaProcesso->commitDado();

    $obListaProcesso->addDado();
    $obListaProcesso->ultimoDado->setCampo( "stParecerJuridico" );
    $obListaProcesso->ultimoDado->setTitle( "Status do Processo" );
    $obListaProcesso->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obListaProcesso->commitDado();

    $obListaProcesso->addAcao();
    $obListaProcesso->ultimaAcao->setAcao( "EXCLUIR" );
    $obListaProcesso->ultimaAcao->setFuncaoAjax( true );
    $obListaProcesso->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluiItensProcesso');" );
    $obListaProcesso->ultimaAcao->addCampo("1","inId");
    $obListaProcesso->commitAcao();

    $obListaProcesso->montaInnerHTML();

    $stHTML = $obListaProcesso->getHTML();

    if ($boExecuta) {
        return "d.getElementById('spnListaProcesso').innerHTML = '".$stHTML."';";
    }

}
switch ($_REQUEST['stCtrl']) {
    case "carregaLabel":
        if ( isset($_REQUEST['stNumEdital']) && $_REQUEST['stNumEdital']!= '' ) {
            $boErro = false;
            $stMensagem = "";
            $arEdital = explode('/', $_REQUEST['stNumEdital']);
            $arEdital[1] = ( $arEdital[1] == '' ) ? Sessao::getExercicio() : $arEdital[1];
            $obTLicitacaoEdital = new TLicitacaoEdital;
            $obTLicitacaoEdital->setDado( "exercicio", $arEdital[1]."::varchar"  );
            $obTLicitacaoEdital->setDado( "num_edital", $arEdital[0] );
            $obTLicitacaoEdital->recuperaEdital( $rsLicitacaoEdital );
            $obTLicitacaoEdital->recuperaLicitacao($rsLicitacao);
            if ($rsLicitacaoEdital->getCampo("dias_abertura_propostas") < 2 ) {
                $boErro = true;
                $stMensagem = "O prazo para impugnar o edital expirou.";
            }

            if ( $rsLicitacaoEdital->getNumLinhas() == -1 ) {
                $boErro = true;
                $stMensagem = "Número de Edital Não Cadastrado.";
            }
            if (!$boErro) {
                include_once ( CAM_GP_LIC_COMPONENTES. "ILabelNumeroLicitacao.class.php" );
                $obForm = new Form();
                $obLblNumeroLicitacao = new ILabelNumeroLicitacao( $obForm );
                $obLblNumeroLicitacao->setMostrarObjeto( true );
                $obLblNumeroLicitacao->setExercicio( $arEdital[1] );
                $obLblNumeroLicitacao->setNumEdital( $arEdital[0] );

                $obFormulario = new Formulario($obForm);
                $obLblNumeroLicitacao->geraFormulario( $obFormulario );
                $obFormulario->montaInnerHTML();

                $stJs .= "d.getElementById('inNumLicitacao').innerHTML = '". $obFormulario->getHTML() ."';\n";
                $stJs .= "document.getElementById('inNumLicitacao').value = '". $rsLicitacao->getCampo('cod_licitacao').'/'.$rsLicitacao->getCampo('exercicio') ."';\n";

                //Formulário de Processos da Impugnação
                $obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado();
                $obTLicitacaoEditalImpugnado->setDado( "exercicio", $arEdital[1]."::varchar" );
                $obTLicitacaoEditalImpugnado->setDado( "num_edital", $arEdital[0] );
                $obTLicitacaoEditalImpugnado->recuperaProcessos( $rsRecordSet );
                //unset(sessao->transf['item']);
                Sessao::remove('item');
                $arItem = array();
                if (!($rsRecordSet->EOF())) {
                    while (!($rsRecordSet->EOF())) {
                        $boNumEdital = false;
                        if (!$boNumEdital) {
                            $inCount = sizeof($arItem);
                            $arItem[$inCount]['inId'           ] = $inCount+1;
                            $arItem[$inCount]['inCodProcesso'] = str_pad($rsRecordSet->getCampo('cod_processo'),$inMascara,"0",STR_PAD_LEFT);
                            $arItem[$inCount]['inExercicio'  ] = $rsRecordSet->getCampo('exercicio_processo' );
                            if ( is_null($rsRecordSet->getCampo('parecer_juridico' ) ) ) {
                                $arItem[$inCount]['stParecerJuridico'  ] = "Aguardando parecer jurídico";
                            } else {
                                $arItem[$inCount]['stParecerJuridico'  ] = "Anulado";
                            }
                        }
                        $rsRecordSet->proximo();
                    }
                }
                $stJs.= montaListaProcesso( $arItem );
                Sessao::write('item', $arItem);

                $stJs.= "document.getElementById('Ok').disabled = false;";
                $stJs.= "document.getElementById('stNumEdital').value = '".$arEdital[0]."/".$arEdital[1]."';";
            } else
                $stJs.= " alertaAviso('".$stMensagem."','erro','aviso','".Sessao::getId()."');\n";
        } else {
            $stJs.= "d.getElementById('inNumLicitacao').innerHTML = '';\n";
            $stJs.= "d.getElementById('stNumEdital').value = '';\n";
            $stJs.= "d.getElementById('spnListaProcesso').innerHTML = '';\n";
            $stJs.= " alertaAviso('Número de Edital Inválido','erro','aviso','".Sessao::getId()."');\n";
        }
    break;
    case "carregaLabelAnular":
        $arTemp = explode("/",$_REQUEST["stNumEdital"]);
        $i=0;
        foreach ($arTemp as $key) {
            $i+=( $key!="" )?1:0;
        }
        if ($i==2) {
            $boErro = false;
            $stMensagem = "";
            $arEdital = explode('/', $_REQUEST['stNumEdital']);
            $obTLicitacaoEdital = new TLicitacaoEdital;
            $obTLicitacaoEdital->setDado( "exercicio", $arEdital[1] );
            $obTLicitacaoEdital->setDado( "num_edital", $arEdital[0] );
            $obTLicitacaoEdital->recuperaEdital( $rsLicitacaoEdital );
            if ( $rsLicitacaoEdital->getNumLinhas() == -1 ) {
                $boErro = true;
                $stMensagem = "Número de Edital Não Cadastrado.";
            }
            if (!$boErro) {
                $obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado;
                $obTLicitacaoEditalImpugnado->setDado( "exercicio", $arEdital[1]);
                $obTLicitacaoEditalImpugnado->setDado( "num_edital", $arEdital[0] );
                $obTLicitacaoEditalImpugnado->recuperaImpugnacaoAnulada( $rsProcessosImpugnados );
                if ( $rsProcessosImpugnados->getNumLinhas() > 0 ) {
                    //aplica a máscara no número do processo
                    if (!($rsProcessosImpugnados->EOF())) {
                        while ( !$rsProcessosImpugnados->EOF() ) {
                            $rsProcessosImpugnados->setCampo("cod_processo",str_pad($rsProcessosImpugnados->getCampo("cod_processo"),$inMascara,0,STR_PAD_LEFT ) );
                            $rsProcessosImpugnados->proximo();
                        }
                        $rsProcessosImpugnados->setPrimeiroElemento();
                    }

                    //Select com os Processos impugnados
                    $obCmbProcessos = new Select();
                    //$obCmbProcessos->setTitle ( "Selecione o Processo da Impugnação" );
                    $obCmbProcessos->setName ( "stCodProcesso" );
                    $obCmbProcessos->setRotulo ( "Processo" );
                    $obCmbProcessos->setCampoId ( "[cod_processo]/[exercicio_processo]" );
                    $obCmbProcessos->setCampoDesc ( "[cod_processo]/[exercicio_processo]" );
                    $obCmbProcessos->setNull ( false );
                    $obCmbProcessos->addOption ( "", "Selecione" );
                    $obCmbProcessos->preencheCombo ( $rsProcessosImpugnados );

                    //textarea para a Justificativa do processo
                    $obTxtParecerJuridico = new TextArea();
                    $obTxtParecerJuridico->setName  ( "stParecerJuridico" );
                    $obTxtParecerJuridico->setRotulo( "Parecer Jurídico" );
                    $obTxtParecerJuridico->setTitle ( "Informe o parecer jurídico que anula a impugnação." );
                    $obTxtParecerJuridico->setNull ( false );
                    $obTxtParecerJuridico->setCols  ( 40 );
                    $obTxtParecerJuridico->setRows  ( 5 );

                    $obFormulario = new Formulario;
                    $obFormulario->setAjuda     ( "UC-03.05.27" );
                    $obFormulario->addTitulo    ( "Dados da Impugnação");
                    $obFormulario->addComponente( $obCmbProcessos );
                    $obFormulario->addComponente( $obTxtParecerJuridico );
                    $obFormulario->montaInnerHTML();
                    $stHTML = $obFormulario->getHTML();
                    $stJs.= "d.getElementById('spnListaProcesso').innerHTML = '".$stHTML."';";
                    $stJs.= "document.getElementById('Ok').disabled = false;";
                } else {
                    $stJs.= "d.getElementById('spnListaProcesso').innerHTML = '';";
                    $stJs.= " alertaAviso('Não existem processos de impugnação a serem anulados para este edital','erro','aviso','".Sessao::getId()."');\n";
                }
            } else {
                $stJs.= " alertaAviso('".$stMensagem."','erro','aviso','".Sessao::getId()."');\n";
            }
        } else {
            $stJs.= "d.getElementById('inNumLicitacao').innerHTML = '';\n";
            $stJs.= "d.getElementById('stNumEdital').value = '';\n";
            $stJs.= "d.getElementById('spnListaProcesso').innerHTML = '';\n";
            $stJs.= " alertaAviso('Número de Edital Inválido','erro','aviso','".Sessao::getId()."');\n";
        }

    break;
    case "incluirListaProcesso":
        $boIncluir = true;
        $stMensagem = "";
        $arItem = Sessao::read('item');
        if ( is_array( $arItem ) ) {
            foreach ($arItem as $campo => $valor) {
                $stChave = $arItem[$campo]['inCodProcesso']."/".$arItem[$campo]['inExercicio'];
                if ($stChave == $_REQUEST["stCodigoProcesso"]) {
                    $boIncluir = false;
                    $stMensagem = "Este registro já existe na lista.";
                }

            }
        }
        if ($boIncluir) {
            $arProcesso = explode( "/",$_REQUEST["stCodigoProcesso"] );
            $arEdital = explode( "/",$_REQUEST["stNumeroEdital"] );
            include_once(CAM_GA_PROT_MAPEAMENTO."TProcesso.class.php");

            $obTProcesso = new TProcesso();
            $obTProcesso->setDado("cod_processo",$arProcesso[0]);
            $obTProcesso->setDado("exercicio_processo",$arProcesso[1]);
            $obTProcesso->recuperaPorChave( $rsProcesso );
            if ( $rsProcesso->getNumLinhas()==-1 ) {
                $boIncluir = false;
                $stMensagem = "O Processo ".$_REQUEST["stCodigoProcesso"]." não existe!";
            } else {
                $obTLicitacaoEditalImpugnado = new TLicitacaoEditalImpugnado();
                $obTLicitacaoEditalImpugnado->setDado("num_edital",$arEdital[0]);
                $obTLicitacaoEditalImpugnado->setDado("exercicio",$arEdital[1]."::varchar");
                $obTLicitacaoEditalImpugnado->setDado("cod_processo",$arProcesso[0]);
                $obTLicitacaoEditalImpugnado->setDado("exercicio_processo",$arProcesso[1]."::varchar");
                $obTLicitacaoEditalImpugnado->recuperaProcessoEditalImpugnado( $rsProcesso );
                if ( $rsProcesso->getNumLinhas() > 0 ) {
                    $boIncluir = false;
                    $stMensagem = "O processo ".$arProcesso[0]."/".$arProcesso[1]." já foi utilizado para impugnar o edital ".str_pad($rsProcesso->getCampo("num_edital"),$inMascara,0,STR_PAD_LEFT)."/".$rsProcesso->getCampo("exercicio").".";
                }
            }
        }
        if ($boIncluir) {
            $inCount = sizeof( $arItem ) + 1;
            $arItemProcesso['inId'             ] = $inCount;
            $arItemProcesso['inCodProcesso'    ] = $arProcesso[0];
            $arItemProcesso['inExercicio'      ] = $arProcesso[1];
            $arItemProcesso['stParecerJuridico'] = $arProcesso[1];
            $arItem[] = $arItemProcesso;
            Sessao::write('item', $arItem);
            $stJs.= montaListaProcesso( $arItem );
        } else {
            // mudado para funcionar com Ajax
            $stJs.= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');\n";
        }
        Sessao::write('item', $arItem);
        $stJs.= "d.getElementById('stCodigoProcesso').value = '';\n";
    break;
    case "excluiItensProcesso":
        $arTMP = array();
        $id = $_REQUEST['inId'];
        $inCount = 0;
        $arItem = Sessao::read('item');
        foreach ($arItem as $campo => $valor) {

            if ($arItem[$campo]['inId'] != $id) {
                $arItens['inId'         ] = ++$inCount;
                $arItens['inCodProcesso'] = $arItem[$campo]['inCodProcesso'];
                $arItens['inExercicio'  ] = $arItem[$campo]['inExercicio'  ];
                $arItens['stParecerJuridico'] = $arItem[$campo]['stParecerJuridico'];
                $arTMP[] = $arItens;

            } else {
                if ($arItem[$campo]['stParecerJuridico'] == "Anulado") {
                    $arItens['inId'             ] = ++$inCount;
                    $arItens['inCodProcesso'    ] = $arItem[$campo]['inCodProcesso'    ];
                    $arItens['inExercicio'      ] = $arItem[$campo]['inExercicio'      ];
                    $arItens['stParecerJuridico'] = $arItem[$campo]['stParecerJuridico'];
                    $arTMP[] = $arItens;
                    $stJs.= "alertaAviso('Este processo já foi anulado.','form','erro','".Sessao::getId()."');\n";
                }
            }

        }
        Sessao::write('item', $arTMP);
        $stJs .= montaListaProcesso( $arTMP );
    break;
}

echo $stJs;
