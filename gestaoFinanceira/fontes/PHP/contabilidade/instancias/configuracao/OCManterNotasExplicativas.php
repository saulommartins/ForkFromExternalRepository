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
    * Página Oculto de Incluir Notas Explicativas
    * Data de Criação   : 03/09/2007

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo S. Rodrigues

    * @ignore

    * $Id: OCManterNotasExplicativas.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-02.02.34
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';
include_once CAM_GF_CONT_MAPEAMENTO.'TContabilidadeNotasExplicativas.class.php';
include CAM_GF_CONT_NEGOCIO."RContabilidadeNotasExplicativas.class.php";

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterNotasExplicativas";

$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRContabilidadeNotasExplicativas  = new RContabilidadeNotasExplicativas;

$arValores = Sessao::read('arValores');
switch ($stCtrl) {

 case 'incluirListaCadastro':

     $boDotacaoRepetida = false;
     foreach ($arValores as $arTEMP) {
         if (  $arTEMP['inCodAcao']   == $_REQUEST['stNomAcao'] AND ($_GET['alterar']!='true')
            && $arTEMP['stDtInicial'] == $_REQUEST['stDtInicial']
            && $arTEMP['stDtFinal']   == $_REQUEST['stDtFinal'] ){
             $boDotacaoRepetida = true ;
             break;
         }
     }

     if (!($boDotacaoRepetida)) {
         $obTContabilidadeNotaExplicativa   = new TContabilidadeNotasExplicativas;
         $obTContabilidadeNotaExplicativa->setDado('cod_acao',$_REQUEST['stNomAcao']);
         $obTContabilidadeNotaExplicativa->setDado('dt_inicial', '');
         $obTContabilidadeNotaExplicativa->setDado('dt_final', '');
         $obTContabilidadeNotaExplicativa->recuperaAnexo ( $rsAnexoInserir ,'');

         $inCount = sizeof($arValores);
         $arValores[$inCount]['id'               ] = $inCount + 1;
         $arValores[$inCount]['stNomAcao'        ] = $rsAnexoInserir->getCampo('nom_acao');
         $arValores[$inCount]['stComplemento'    ] = $rsAnexoInserir->getCampo('complemento_acao');
         $arValores[$inCount]['inCodAcao'        ] = $rsAnexoInserir->getCampo('cod_acao');
         $arValores[$inCount]['stDtInicial'      ] = $_REQUEST['stDtInicial'];
         $arValores[$inCount]['stDtFinal'        ] = $_REQUEST['stDtFinal'];

         $stNotaExplicativa = str_replace('\\\n', " \n", $_REQUEST['stNotaExplicativaQuebra']);
         $arValores[$inCount]['stNotaExplicativa'] = $stNotaExplicativa;
         $arValores[$inCount]['stAnexo'          ] = $rsAnexoInserir->getCampo('nom_acao')." ".$rsAnexoInserir->getCampo('complemento_acao');

         $js  = "document.frm.action                   = '".$pgProc."?".Sessao::getId()."';";
         $js .= "document.frm.stCtrl.value             = ''; ";
         $js .= "document.frm.stNomAcao.value          = ''; ";
         $js .= "document.frm.stNotaExplicativa.value  = ''; ";
         $js .= "document.frm.stDtInicial.value        = ''; ";
         $js .= "document.frm.stDtFinal.value          = ''; ";
         echo $js;

     } else {
         echo"alertaAviso('Esse item já consta na listagem.','form','erro','".Sessao::getId()."');";
     }
     Sessao::write('arValores', $arValores);
     echo montaListaItens( $arValores);

 break;

 case 'excluirItemLista' :

     $boDotacaoRepetida = false;
     $arTEMP            = array();
     $inCount           = 0;

     foreach ($arValores as $key => $value) {
         if (($key + 1) != $_REQUEST['id']) {
             $arTEMP[$inCount]['id'                  ] = $inCount + 1;
             $arTEMP[$inCount]['stNomAcao'           ] = $value[ "stNomAcao"         ];
             $arTEMP[$inCount]['stComplemento'       ] = $value[ "stComplemento"     ];
             $arTEMP[$inCount]['inCodAcao'           ] = $value[ "inCodAcao"         ];
             $arTEMP[$inCount]['stDtInicial'         ] = $value[ "stDtInicial"       ];
             $arTEMP[$inCount]['stDtFinal'           ] = $value[ "stDtFinal"         ];
             $arTEMP[$inCount]['stNotaExplicativa'   ] = $value[ "stNotaExplicativa" ];
             $arTEMP[$inCount]['stAnexo'             ] = $value[ "stAnexo"           ];
             $inCount++;
         }
     }
     Sessao::write('arValores', $arTEMP);
     echo montaListaItens($arTEMP);
     $js.="document.getElementById('limpar').disabled=false; ";
     $js.="document.getElementById('stNotaExplicativa').disabled =false;";
     $js.="document.getElementById('stDtInicial').disabled =false;";
     $js.="document.getElementById('stDtFinal').disabled =false;";
     $js.="limparCadastro();";
     echo $js;

 break;

 case 'carregarItem':

     $rsRecordSetItem = new RecordSet;
     $obTContabilidadeNotaExplicativa = new TContabilidadeNotasExplicativas;
     $obTContabilidadeNotaExplicativa->recuperaNotaExplicativa($rsRecordSetItem, $stFiltro);

     if (!($rsRecordSetItem->EOF())) {
         while (!($rsRecordSetItem->EOF())) {
             $boDotacaoRepetida = false;
             foreach ($arValores as $arTEMP) {
                 if (  $arTEMP['inCodAcao']   == $rsRecordSetItem->getCampo('cod_acao')
                    && $arTEMP['stDtInicial'] == $rsRecordSetItem->getCampo('dt_inicial')
                    && $arTEMP['stDtFinal']   == $rsRecordSetItem->getCampo('dt_final') ){

                     $boDotacaoRepetida = true ;
                     break;
                 }
             }

             if (!($boDotacaoRepetida)) {
                 $inCount = sizeof($arValores);
                 $arValores[$inCount]['id'               ] = $inCount + 1;
                 $arValores[$inCount]['stNomAcao'        ] = $rsRecordSetItem->getCampo('nom_acao');
                 $arValores[$inCount]['stComplemento'    ] = $rsRecordSetItem->getCampo('complemento_acao');
                 $arValores[$inCount]['inCodAcao'        ] = $rsRecordSetItem->getCampo('cod_acao');
                 $arValores[$inCount]['stDtInicial'      ] = sistemaLegado::dataToBr($rsRecordSetItem->getCampo('dt_inicial'));
                 $arValores[$inCount]['stDtFinal'        ] = sistemaLegado::dataToBr($rsRecordSetItem->getCampo('dt_final'));
                 $arValores[$inCount]['stNotaExplicativa'] = $rsRecordSetItem->getCampo('nota_explicativa');
                 $arValores[$inCount]['stAnexo'          ] = $rsRecordSetItem->getCampo('nom_acao')." ".$rsRecordSetItem->getCampo('complemento_acao');
             }
             $rsRecordSetItem->Proximo();
         }
     }
     Sessao::write('arValores', $arValores);
     echo montaListaItens($arValores);

 break;

 case 'alterarItem':

     foreach ($arValores as $key => $value) {
         if (($arValores[$key]['id'])==$_REQUEST['id']) {
             $js.="f.stNotaExplicativa.value ='".str_replace("\n", '\n', $arValores[$key]['stNotaExplicativa'])."';";
             $js.="$('stHdnId').value='".$_REQUEST['id']."'; ";
             $js.="f.stDtInicial.value='".$arValores[$key]['stDtInicial']."';";
             $js.="f.stDtFinal.value='".$arValores[$key]['stDtFinal']."';";
             $js.="f.stNomAcao.value ='".$arValores[$key]['inCodAcao']."';";
             $js.="document.getElementById('incluir').value ='Alterar';";
             $js.="document.getElementById('limpar').disabled=false; ";
             $js.="document.getElementById('stNotaExplicativa').disabled =false;";
             $js.="document.getElementById('stDtInicial').disabled =false;";
             $js.="document.getElementById('stDtFinal').disabled =false;";
             $js.="d.getElementById('incluir').setAttribute('onclick','JavaScript:alterarCadastro(\'alterarListaCadastro\',".$_REQUEST['id'].",true,\'true\');');";
             $js.="f.stNotaExplicativa.focus();";
             $js.="document.getElementById('stNomAcao').disabled='true'; ";
         }
     }
     echo $js;

 break;

 case 'consultarItem':

     foreach ($arValores as $key => $value) {
         if (($arValores[$key]['id'])==$_REQUEST['id']) {
             $js.="f.stNotaExplicativa.value ='".str_replace("\n", '\n', $arValores[$key]['stNotaExplicativa'])."';";
             $js.="$('stHdnId').value='".$_REQUEST['id']."'; ";
             $js.="f.stDtInicial.value='".$arValores[$key]['stDtInicial']."';";
             $js.="f.stDtFinal.value='".$arValores[$key]['stDtFinal']."';";
             $js.="f.stNomAcao.value ='".$arValores[$key]['inCodAcao']."';";
             $js.="document.getElementById('incluir').value ='Retornar';";
             $js.="document.getElementById('limpar').disabled ='true';";
             $js.="document.getElementById('stNotaExplicativa').disabled ='true';";
             $js.="document.getElementById('stDtInicial').disabled ='true';";
             $js.="document.getElementById('stDtFinal').disabled ='true';";
             $js.="d.getElementById('incluir').setAttribute('onclick','JavaScript:consultarCadastro(\'consultarListaCadastro\',".$_REQUEST['id'].",true,\'true\');');";
             $js.="document.getElementById('stNomAcao').disabled='true'; ";
         }
     }
     echo $js;

 break;

 case 'alterarListaCadastro':
    $inCodAcao = $arValores[$_REQUEST['id']-1]['inCodAcao'];

    $boDotacaoRepetida = false;
    foreach ($arValores as $arTEMP) {
        if ($arTEMP['inCodAcao']   == $inCodAcao
           && $arTEMP['stDtInicial'] == $_REQUEST['stDtInicial']
           && $arTEMP['stDtFinal']   == $_REQUEST['stDtFinal']
           && $arTEMP['id']          != $_REQUEST['id']) {

            $boDotacaoRepetida = true ;
            echo "alertaAviso('Esse item já consta na listagem.','form','erro','".Sessao::getId()."');";
        }
    }

    if (!$boDotacaoRepetida) {
        $stNotaExplicativa = str_replace('\\\n', " \n", $_REQUEST['stNotaExplicativa']);

        $arValores[$_REQUEST['id']-1]['stNotaExplicativa'] = $stNotaExplicativa;
        $arValores[$_REQUEST['id']-1]['stDtInicial'] = $_REQUEST['stDtInicial'];
        $arValores[$_REQUEST['id']-1]['stDtFinal'] = $_REQUEST['stDtFinal'];

        $js  = "document.frm.action                   = '".$pgProc."?".Sessao::getId()."';";
        $js .= "document.frm.stCtrl.value             = ''; ";
        $js .= "document.frm.stNomAcao.value          = ''; ";
        $js .= "document.frm.stNotaExplicativa.value  = ''; ";
        $js .= "document.frm.stDtInicial.value        = ''; ";
        $js .= "document.frm.stDtFinal.value          = ''; ";
        $js .= "document.getElementById('incluir').value  = 'Incluir'; ";
        $js .= "document.getElementById('incluir').setAttribute('onclick','JavaScript:incluirCadastro(\'incluirListaCadastro\',true,\'false\');'); ";
        $js .= "document.getElementById('stNomAcao').disabled = false; ";
        $js .= "alertaAviso('Nota Explicativa alterada com sucesso!','form','erro','".Sessao::getId()."');";
        echo $js;
    }
    Sessao::write('arValores', $arValores);
    echo montaListaItens( $arValores);

 break;

 case 'consultarListaCadastro':
    $js  = "document.frm.action                   = '".$pgProc."?".Sessao::getId()."';";
    $js .= "document.frm.stCtrl.value             = ''; ";
    $js .= "document.frm.stNomAcao.value          = ''; ";
    $js .= "document.frm.stNotaExplicativa.value  = ''; ";
    $js .= "document.frm.stDtInicial.value        = ''; ";
    $js .= "document.frm.stDtFinal.value          = ''; ";
    $js .= "document.getElementById('incluir').value  = 'Incluir'; ";
    $js .= "document.getElementById('incluir').setAttribute('onclick','JavaScript:incluirCadastro(\'incluirListaCadastro\',true,\'false\');'); ";
    $js .= "document.getElementById('stNomAcao').disabled = false; ";
    echo $js;

 break;

}

function montaListaItens($arRecordSet , $boExecuta = true)
{
    $stPrograma = "ManterNotasExplicativas";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche( $arRecordSet );

    $table = new Table();
    $table->setRecordset( $rsDotacoesItem );
    $table->setSummary('Itens Incluídos');

    //$table->setConditional( true , "#efefef" );

    $table->Head->addCabecalho( 'Anexo' , 60);
    $table->Head->addCabecalho( 'Data Inicial' , 10);
    $table->Head->addCabecalho( 'Data Final' , 10);

    $stTitle = "[stTitle]";

    $table->Body->addCampo( "stAnexo", "E", $stTitle );
    $table->Body->addCampo( "stDtInicial", "C", $stTitle );
    $table->Body->addCampo( "stDtFinal", "C", $stTitle );

    $table->Body->addAcao( 'consultar' ,  'consultarItem(%s)'    , array( 'id' ) );
    $table->Body->addAcao( 'alterar'   ,  'montaParametrosGET(alterarItem(%s), \'\', true)'      , array( 'id' ) );
    $table->Body->addAcao( 'excluir'   ,  'excluirItemLista(%s)' , array( 'id' ) );

    $table->montaHTML();
    $stHTML = $table->getHtml();

    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    if ($boExecuta) {
        $js ="document.getElementById('spnListaItens').innerHTML = '".$stHTML."';";

        return $js;
    } else {
        return $stHTML;
    }

}
