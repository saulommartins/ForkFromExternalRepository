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
/*
    * Página Oculto para contrato
    * Data de Criação   : 02/10/2008

    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

     $Id: OCManterContrato.php 66521 2016-09-12 17:02:36Z michel $

    * Casos de uso :
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include padrão do framework
include_once(CAM_GP_COM_MAPEAMENTO."TComprasContratoCompraDireta.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once(CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumentosAtributos.class.php" );
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/TableTree.class.php';

$stPrograma = "ManterContrato";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obRCadastroDinamico = new RCadastroDinamico();
$obRCadastroDinamico->setCodCadastro( 1 );

$stJs=""; $js="";

switch ($_REQUEST['stCtrl']) {

    case "MontaUnidade":
        include_once CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php";
        if ($_REQUEST["inNumOrgao"]) {
            $stCombo  = "inNumUnidade";
            $stComboTxt  = "inNumUnidadeTxt";
            $stJs .= "limpaSelect(f.$stCombo,0); \n";
            $stJs .= "f.$stComboTxt.value='".$_REQUEST["inNumUnidade"]."'; \n";
            $stJs .= "f.$stCombo.value='".$_REQUEST["inNumUnidade"]."'; \n";
            $stJs .= "f.$stCombo.options[0] = new Option('Selecione','', 'selected');\n";
            
            $obREmpenhoPreEmpenho = new REmpenhoPreEmpenho;
            $obREmpenhoPreEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->obROrcamentoOrgaoOrcamentario->setNumeroOrgao($_REQUEST["inNumOrgao"]);
            $obREmpenhoPreEmpenho->obREmpenhoPermissaoAutorizacao->obROrcamentoUnidade->consultar( $rsCombo, $stFiltro,"", $boTransacao );
            
            $inCount = 0;
            while (!$rsCombo->eof()) {
                $inCount++;
                $inId   = $rsCombo->getCampo("num_unidade");
                $stDesc = $rsCombo->getCampo("nom_unidade");
                if( $_REQUEST["inNumUnidade"] == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stCombo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsCombo->proximo();
            }
        }
        
        echo $stJs;
    break;

    case "carregaValorContratoCompraDireta":
        preencheValorContratoCompraDireta();
    break;

    case 'carregaValorFornecedorCompraDireta':
        buscaValorFornecedor();
    break;

    case "preencheObjetoCompraDireta":
        if ( ($_REQUEST['inCodModalidade'] != "") && ($_REQUEST['inCodEntidade'] != "") ) {
            $stJs.= buscaValorFornecedorCompraDireta();
        } else {
            $stJs = " d.getElementById('stDescObjeto').innerHTML = '&nbsp;';\n";
            $stJs.= " f.hdnDescObjeto.value = '';\n";
            $stJs.= " d.getElementById('vlContrato').value = '';";
            $stJs.= " d.getElementById('hdnValorContrato').value = '';";
            $stJs.= " f.inCGMContratado.selectedIndex =  0;\n";
            $stJs.= " limpaSelect(f.inCGMContratado,1);\n";
        }
        echo $stJs;
    break;

    case 'carregaListaDocumentos' :
        $arDocumentos = Sessao::read('arDocumentos');
        echo montaListaDocumentos($arDocumentos);
    break;

    //Carrega itens da listagem de documentos de publicacao utilizados em seus determinados campos no Form.
    case 'alteraDocumentos':
        $i = 0;
        $arDocumentos = Sessao::read('arDocumentos');
        foreach ($arDocumentos as $key => $value) {
               if (($key+1) == $_REQUEST['id']) {

                $dataValidade = $arDocumentos[$i]['dtValidade'];
                $dataEmissao = $arDocumentos[$i]['dtEmissao'];

                $js ="f.HdnCodDocumento.value = '".$_REQUEST['id']."';";
                $js.="f.inCodDocumento.value = '".$arDocumentos[$i]['inCodDocumento']."';";
                $js.="f.stNumDocumento.value = '".$arDocumentos[$i]['stNumDocumento']."';";
                $js.="f.stDataValidade.value = '".$arDocumentos[$i]['dtValidade']."';";
                $js.="f.stDataEmissao.value = '".$arDocumentos[$i]['dtEmissao']."';";
                $js.="f.btIncluirDocumentos.disabled = true;";
                $js.="f.btAlterarDocumentos.disabled = false;";
                $js.= "f.stDataValidade.disabled = '';";
                $js.= "f.inNumDiasValido.disabled = '';";
            }
            $i++;
        }
        sincronizaDiasValidosDocumento($dataValidade,$dataEmissao);
        Sessao::write('arDocumentos', $arDocumentos);
        echo $js;
    break;

    case "sincronizaDataValida":
        sincronizarDataValidaDocumento($_REQUEST['inNumDiasValido'], $_REQUEST['stDataEmissao']);
    break;

    case "sincronizaDiasValidos":
        sincronizaDiasValidosDocumento($_REQUEST['stDataValidade'], $_REQUEST['stDataEmissao']);
    break;

    //Inclui itens na listagem de documentos de publicacao utilizados
    case 'incluirDocumentos':
        include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php");
        $obTLicitacaoDocumento = new TLicitacaoDocumento;
        $obTLicitacaoDocumento->setDado('cod_documento', $_REQUEST["inCodDocumento"]);
        $obTLicitacaoDocumento->recuperaPorChave($rsDocumentos);
        $stNomDocumento = $rsDocumentos->getCampo('nom_documento');

        $boDocumentoRepetido = false;
        $arDocumentos = Sessao::read('arDocumentos');
        if (is_array($arDocumentos) == true) {
            foreach ($arDocumentos as $arTEMP) {
                if ($arTEMP['inCodDocumento'] == $_REQUEST["inCodDocumento"]) {
                    $boDocumentoRepetido = true ;
                    break;
                }
            }
        }

        if (!($boDocumentoRepetido)) {
            $inCount = sizeof($arDocumentos);
            $arDocumentos[$inCount]['id'               ] = $inCount + 1;
            $arDocumentos[$inCount]['boNovo'           ] = true;
            $arDocumentos[$inCount]['inCodDocumento'      ] = $_REQUEST[ "inCodDocumento"];
            $arDocumentos[$inCount]['stNumDocumento'   ] = $_REQUEST[ "stNumDocumento" ];
            $arDocumentos[$inCount]['stNomDocumento'   ] = $stNomDocumento;
            $arDocumentos[$inCount]['dtValidade'       ] = $_REQUEST[ "stDataValidade" ];
            $arDocumentos[$inCount]['dtEmissao'        ] = $_REQUEST[ "stDataEmissao"  ];

        } else {
            echo "alertaAviso('Este documento já consta nesse contrato.','form','erro','".Sessao::getId()."');";
        }

        echo 'limpaFormularioDocumentos();';
        echo 'document.getElementById("inNumDiasValido").value = "";';
        Sessao::write('arDocumentos', $arDocumentos);
        echo montaListaDocumentos( $arDocumentos);
    break;

    //Confirma itens alterados da listagem de documentos de publicacao utilizados
    case "alterarDocumentos":
        $inCount = 0;
        include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoDocumento.class.php");
        $obTLicitacaoDocumento = new TLicitacaoDocumento;
        $obTLicitacaoDocumento->setDado('cod_documento', $_REQUEST["inCodDocumento"]);
        $obTLicitacaoDocumento->recuperaPorChave($rsDocumentos);
        $stNomDocumento = $rsDocumentos->getCampo('nom_documento');
        $arDocumentos = Sessao::read('arDocumentos');

        $boDocumentoRepetido = false;
        foreach ($arDocumentos as $key=>$value) {
            if ($value['inCodDocumento'] == $_REQUEST["inCodDocumento"]) {
                if ($value['id'] != $_REQUEST['HdnCodDocumento']) {
                    $boDocumentoRepetido = true;
                }
            }
        }

        if (!$boDocumentoRepetido) {
            foreach ($arDocumentos as $key=>$value) {
                if (($key+1) == $_REQUEST['HdnCodDocumento']) {
                    $arDocumentos[$inCount]['id'            ] = $inCount + 1;
                    $arDocumentos[$inCount]['boAlterado'    ] = true;
                    $arDocumentos[$inCount]['inCodDocumento'] = $_REQUEST[ "inCodDocumento"];
                    $arDocumentos[$inCount]['stNumDocumento'] = $_REQUEST[ "stNumDocumento" ];
                    $arDocumentos[$inCount]['stNomDocumento'] = $stNomDocumento;
                    $arDocumentos[$inCount]['dtValidade'    ] = $_REQUEST[ "stDataValidade" ];
                    $arDocumentos[$inCount]['dtEmissao'     ] = $_REQUEST[ "stDataEmissao"  ];
                }
                $inCount++;
            }
            Sessao::write('arDocumentos', $arDocumentos);
            echo 'limpaFormularioDocumentos();';
            echo 'document.getElementById("inNumDiasValido").value = "";';
            $js.= montaListaDocumentos($arDocumentos);
            $js.= "f.btIncluirDocumentos.disabled = false;";
            $js.= "f.btAlterarDocumentos.disabled = true;";
            $js.= "f.stDataValidade.disabled = 'disabled';";
            $js.= "f.inNumDiasValido.disabled = 'disabled';";
            echo $js;
        } else {
            echo "alertaAviso('Este documento já consta nesse contrato.','form','erro','".Sessao::getId()."');";
        }
    break;

    case 'excluirDocumentos':
        $boDocumentoRepetido = false;
        $arTEMP            = array();
        $inCount           = 0;
        $arDocumentos = Sessao::read('arDocumentos');
        foreach ($arDocumentos as $key => $value) {
            if (($key+1) != $_REQUEST['id']) {
                $arTEMP[$inCount]['id'            ] = $inCount + 1;
                $arTEMP[$inCount]['inCodDocumento'] = $value[ "inCodDocumento" ];
                $arTEMP[$inCount]['stNumDocumento'] = $value[ "stNumDocumento" ];
                $arTEMP[$inCount]['stNomDocumento'] = $value[ "stNomDocumento" ];
                $arTEMP[$inCount]['dtValidade'    ] = $value[ "dtValidade"     ];
                $arTEMP[$inCount]['dtEmissao'     ] = $value[ "dtEmissao"      ];
                $inCount++;
            }
        }
        Sessao::write('arDocumentos', $arTEMP);
        echo montaListaDocumentos($arTEMP);
    break;

    //Carrega itens vazios na listagem de aditivos de publicacao utilizados no carregamento do Form.
    case 'carregaListaAditivos' :
        echo montaListaAditivos(Sessao::read('arAditivos'));
    break;

    case 'excluirAditivos':
        $arTEMP            = array();
        $inCount           = 0;
        $arAditivos = Sessao::read('arAditivos');
        foreach ($arAditivos as $key => $value) {
            if (($key+1) != $_REQUEST['id']) {
                $arTEMP[$inCount]['id'               ] = $inCount + 1;
                $arTEMP[$inCount]['inCodNorma'     ] = $value[ "inCodNorma"   ];
                $arTEMP[$inCount]['dtVencimento'   ] = $value[ "dtVencimento"     ];
                $inCount++;
            }
        }
        Sessao::write('arAditivos', $arTEMP);
        echo montaListaAditivos($arTEMP);
    break;

    case 'limparTela':
        Sessao::remove('arDocumentos');
        $stJs  = montaListaDocumentos( array() );
        $stJs .= "frm.inCodCompraDireta.options[0].selected = true; \n";
           $stJs .= "frm.inCGMContratado.options[0].selected = true; \n";
        echo $stJs;
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaVeiculos' :
        $arValores = Sessao::read('arValores');
        echo montaListaVeiculos($arValores);
    break;

    //Inclui itens na listagem de Aditivos de publicacao utilizados
    case 'incluirAditivos':
        $boAditivoRepetido = false;
        $arAditivos = Sessao::read('arAditivos');
        foreach ($arAditivos as $arTEMP) {
            if ($arTEMP['inCodNorma'] == $_REQUEST["inCodNorma"]) {
                $boAditivoRepetido = true ;
                break;
            }
        }
        if (!($boAditivoRepetido)) {
            $inCount = sizeof($arAditivos);
            $arAditivos[$inCount]['id'               ] = $inCount + 1;
            $arAditivos[$inCount]['inCodNorma'       ] = $_REQUEST[ "inCodNorma"];
            $arAditivos[$inCount]['dtVencimento'     ] = $_REQUEST[ "hdnDataVigencia"];

        } else {
              echo "alertaAviso('Este aditivo já consta nesse contrato.','form','erro','".Sessao::getId()."');";
        }
        echo 'limpaFormularioAditivos();';
        Sessao::write('arAditivos', $arAditivos);
        echo montaListaAditivos( $arAditivos);
    break;

    //Inclui itens na listagem de Aditivos de publicacao utilizados
    case 'incluiAditivos':

        $boAditivoRepetido = false;

        $arAditivos = Sessao::read('arAditivos');
        foreach ($arAditivos as $arTEMP) {
            if ($arTEMP['inCodNorma'] == $_REQUEST["inCodNorma"]) {
                $boAditivoRepetido = true ;
                break;
            }
        }

        if (!($boAditivoRepetido)) {
           $inCount = sizeof($arAditivos);
           $arAditivos[$inCount]['id'           ] = $inCount + 1;
           $arAditivos[$inCount]['inCodNorma'   ] = $_REQUEST[ "inCodNorma"];
           $arAditivos[$inCount]['dtVencimento' ] = $_REQUEST[ "hdnDataVigencia"];
        }
        Sessao::write('arAditivos', $arAditivos);
    break;

    //Inclui itens na listagem de veiculos de publicacao utilizados
    case 'incluirListaVeiculos':
        $arValores = Sessao::read('arValores');
        if ($_REQUEST['inVeiculo'] == '') {
            $stMensagem = 'Preencha o campo Veículo de Publicação!';
        }
        if ($_REQUEST['dtDataPublicacao'] == '') {
            $stMensagem = 'Preencha o campo Data de Publicação!';
        }
        $boPublicacaoRepetida = false;
        if ( is_array( $arValores ) ) {
            foreach ($arValores as $arTEMP) {
                if ($arTEMP['inVeiculo'] == $_REQUEST["inVeiculo"] & $arTEMP['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao']) {
                    $boPublicacaoRepetida = true ;
                    $stMensagem = "Este veículos de publicação já está na lista.";
                }
            }
        }
        if ( !$boPublicacaoRepetida AND !isset($stMensagem) ) {
           $inCount = sizeof($arValores);
           $arValores[$inCount]['id'             ] = $inCount + 1;
           $arValores[$inCount]['inVeiculo'      ] = $_REQUEST[ "inVeiculo"                  ];
           $arValores[$inCount]['stVeiculo'      ] = $_REQUEST[ "stNomCgmVeiculoPublicadade" ];
           $arValores[$inCount]['dtDataPublicacao' ] = $_REQUEST[ "dtDataPublicacao"             ];
           $arValores[$inCount]['inNumPublicacao' ] = $_REQUEST[ "inNumPublicacao"             ];
           $arValores[$inCount]['stObservacao'   ] = $_REQUEST[ "stObservacao"               ];
           $arValores[$inCount]['inCodCompraDireta' ] = $request->get("HdnCodCompraDireta");
        } else {
           echo "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
        }

        Sessao::write('arValores', $arValores);

        echo montaListaVeiculos( $arValores);
        $js.="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
        $js.="$('inNumPublicacao').value ='';";
        $js.="$('stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Carrega itens da listagem de veiculos de publicacao utilizados em seus determinados campos no Form.
    case 'alterarListaVeiculos':
        $i = 0;

        $arValores = Sessao::read('arValores');
        if ( is_array($arValores)) {
            foreach ($arValores as $key => $value) {
                if (($key+1) == $_REQUEST['id']) {
                    $js ="$('HdnCodVeiculo').value                      ='".$_REQUEST['id']."';                         ";
                    $js.="$('inVeiculo').value                          ='".$arValores[$i]['inVeiculo']."';             ";
                    $js.="$('dtDataPublicacao').value                   ='".$arValores[$i]['dtDataPublicacao']."';      ";
                    $js.="$('inNumPublicacao').value                    ='".$arValores[$i]['inNumPublicacao']."';       ";
                    $js.="$('stObservacao').value                       ='".$arValores[$i]['stObservacao']."';          ";
                    $js.="$('stNomCgmVeiculoPublicadade').innerHTML='".$arValores[$i]['stVeiculo']."';                  ";
                    $js.="$('incluiVeiculo').value    ='Alterar';                                                        ";
                    $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'alteradoListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta, HdnCodVeiculo\')');";
                }
                $i++;
            }
        }
        echo $js;
    break;

    //Confirma itens alterados da listagem de veiculos de publicacao utilizados
    case "alteradoListaVeiculos":
         $inCount = 0;
         $boDotacaoRepetida = false;
         $arValores = Sessao::read('arValores');
         foreach ($arValores as $key=>$value) {
            if ($value['inVeiculo'] == $_REQUEST["inVeiculo"] & $value['dtDataPublicacao'] == $_REQUEST['dtDataPublicacao'] AND ( $key+1 != $_REQUEST['HdnCodVeiculo'] ) ) {
                $boDotacaoRepetida = true ;
                break;
            }
         }
         if (!$boDotacaoRepetida) {
               foreach ($arValores as $key=>$value) {
                if (($key+1) == $_REQUEST['HdnCodVeiculo']) {
                  $arValores[$inCount]['id'            ] = $inCount + 1;
                  $arValores[$inCount]['inVeiculo'     ] = $_REQUEST[ "inVeiculo"                  ];
                  $arValores[$inCount]['stVeiculo'     ] = sistemaLegado::pegaDado('nom_cgm','sw_cgm',' WHERE numcgm = '.$_REQUEST['inVeiculo'].' ');
                  $arValores[$inCount]['dtDataPublicacao'] = $_REQUEST[ "dtDataPublicacao"         ];
                  $arValores[$inCount]['inNumPublicacao']  = $_REQUEST[ "inNumPublicacao"          ];
                  $arValores[$inCount]['stObservacao'  ]   = $_REQUEST[ "stObservacao"             ];
                }
                 $inCount++;
               }
               Sessao::write('arValores', $arValores);
               $js.=montaListaVeiculos($arValores);
               $js.="$('HdnCodVeiculo').value ='';";
               $js.="$('inVeiculo').value ='';";
               $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
               $js.="$('inNumPublicacao').value ='';";
               $js.="$('stObservacao').value = '';";
               $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
               $js.="$('incluiVeiculo').value = 'Incluir';";
               $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
               echo $js;

        } else {
           echo "alertaAviso('Este item já consta na listagem dessa publicação.','form','erro','".Sessao::getId()."');";
        }
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaVeiculos':

        $boDotacaoRepetida = false;
        $arTEMP            = array();
        $inCount           = 0;
        $arValores = Sessao::read('arValores');
        foreach ($arValores as $key => $value) {
            if (($key+1) != $_REQUEST['id']) {
                $arTEMP[$inCount]['id'               ] = $inCount + 1;
                $arTEMP[$inCount]['inVeiculo'        ] = $value[ "inVeiculo"      ];
                $arTEMP[$inCount]['stVeiculo'        ] = $value[ "stVeiculo"      ];
                $arTEMP[$inCount]['dtDataPublicacao' ] = $value[ "dtDataPublicacao" ];
                $arTEMP[$inCount]['inNumPublicacao'  ] = $value[ "inNumPublicacao"  ];
                $arTEMP[$inCount]['stObservacao'     ] = $value[ "stObservacao"   ];
                $arTEMP[$inCount]['inCodCompraDireta'   ] = $value[ "inCodCompraDireta" ];
                $inCount++;
            }
        }
        Sessao::write('arValores', $arTEMP);
        echo montaListaVeiculos($arTEMP);
     break;

    case 'limparVeiculo' :
        $js.="$('HdnCodVeiculo').value ='';";
        $js.="$('inVeiculo').value ='';";
        $js.="$('dtDataPublicacao').value ='".date('d/m/Y')."';";
        $js.="$('inNumPublicacao').value ='';";
        $js.="$('stObservacao').value = '';";
        $js.="$('stNomCgmVeiculoPublicadade').innerHTML = '&nbsp;';";
        $js.="$('incluiVeiculo').value = 'Incluir';";
        $js.="$('incluiVeiculo').setAttribute('onclick','montaParametrosGET(\'incluirListaVeiculos\', \'id, inVeiculo, stVeiculo, dtDataPublicacao, inNumPublicacao, stNomCgmVeiculoPublicadade, stObservacao, inCodCompraDireta, HdnCodCompraDireta\')');";
        echo $js;
    break;

    //Consulta Temporária enquanto o componente IPopUpNumeroContrato não fica pronto.
    case 'consultaContrato':
        if ($_REQUEST['inContrato']!="") {
            if ($_REQUEST['inCodEntidade']!="") {
                $rsRecordSetVeiculo  = new RecordSet;
                $obLicitacaoContrato = new TLicitacaoContrato();

                $stFiltro = "   AND contrato.cod_entidade = ".$_REQUEST['inCodEntidade']." \n";
                $stFiltro.= "   AND contrato.num_contrato = ".$_REQUEST['inContrato']."    \n";
                $stFiltro.= "   AND contrato.exercicio    = ".Sessao::getExercicio()."         \n";

                $obLicitacaoContrato->recuperaContratoCompraDireta($rsRecordSetVeiculo,$stFiltro);
                if (!($rsRecordSetVeiculo->EOF())) {
                    while (!($rsRecordSetVeiculo->EOF())) {
                        $codCompraDireta = $rsRecordSetVeiculo->getCampo("cod_compra_direta");
                        $codObjeto    = $rsRecordSetVeiculo->getCampo("cod_objeto");
                        $stObjeto     = nl2br(str_replace('\r\n', '\n', preg_replace('/(\r\n|\n|\r)/', ' ', $rsRecordSetVeiculo->getCampo("descricao"))));
                        $codModalidade= $rsRecordSetVeiculo->getCampo("cod_modalidade");

                        $js = "d.getElementById('inNroCompraDireta').innerHTML = '".$codCompraDireta."';             ";
                        $js.= "d.getElementById('inNroObjeto')   .innerHTML = '".$codObjeto." - ".$stObjeto."';";

                        $js.= "f.HdnCodContrato.value  = '".$_REQUEST['inContrato']."';                        ";
                        $js.= "f.HdnCodCompraDireta.value = '".$codCompraDireta."';                                  ";
                        $js.= "f.HdnCodModalidade.value= '".$codModalidade."';                                 ";

                        $rsRecordSetVeiculo->proximo();
                    }
                } else {
                    $js = "f.inContrato.value      = '';                                                      ";

                    $js.= "f.HdnCodContrato.value   = '';                                                     ";
                    $js.= "f.HdnCodCompraDireta.value  = '';                                                     ";
                    $js.= "f.HdnCodModalidade.value = '';                                                     ";

                    $js.="alertaAviso('Número do Contrato(".$_GET['inContrato'].") não encontrado!.','form','erro','".Sessao::getId()."');";
                }
            } else {
                $js ="f.inContrato.value      = '';                                          ";

                $js.="f.HdnCodContrato.value  = '';                                          ";
                $js.="f.HdnCodCompraDireta.value = '';                                          ";
                $js.="f.HdnCodModalidade.value= '';                                          ";

                $js.="alertaAviso('Selecione uma entidade.','form','erro','".Sessao::getId()."');";
            }
        }
        echo $js;
    break;

    //Carrega itens vazios na listagem de veiculos de publicacao utilizados no carregamento do Form.
    case 'carregaListaArquivos' :
    $arArquivos = Sessao::read('arArquivos');
    echo montaListaArquivos($arArquivos);
    break;

    case 'consultarListaArquivo' :
        consultarListaArquivo($request);
    break;

    case "addArquivo":
        $arArquivos = Sessao::read('arArquivos');

        $stErro = "";

        $inCount = (is_array($arArquivos)) ? count($arArquivos) : 0;
        $stName = $_FILES['stArquivo']['name'];

        if(empty($stName))
            $stErro = "Selecione o Arquivo Digital!";

        if(empty($stErro)){
            if ($_FILES["stArquivo"]["error"] > 0) {
                if ($_FILES["stArquivo"]["error"] == 1 )
                    $stErro = "Arquivo ultrapassa o valor maxímo de ".ini_get("upload_max_filesize");
                else
                    $stErro = "Erro no upload do arquivo.";
            }
        }

        $stDirTMP = CAM_GP_LICITACAO."tmp/";
        $stDirANEXO = CAM_GP_LIC_ANEXOS."contrato/";

        if(empty($stErro)){
            if (!is_writable($stDirTMP)) {
                $stErro = " O diretório ".$stDirTMP." não possui permissão de escrita!";
            }elseif (!is_writable($stDirANEXO)) {
                $stErro = " O diretório ".$stDirANEXO." não possui permissão de escrita!";
            }
        }

        if(empty($stErro)){
            $stNameArq = md5(microtime()).$stName;

            $stArquivoTMP = $stDirTMP.$stNameArq;
            $stArquivoANEXO = $stDirANEXO.$stNameArq;

            if(!move_uploaded_file($_FILES["stArquivo"]["tmp_name"],$stArquivoTMP))
                $stErro = "Erro no upload do arquivo.";
        }

        if(empty($stErro)){
            $arArquivosUpload['id']           = $inCount + 1;
            $arArquivosUpload['arquivo']      = $stNameArq;
            $arArquivosUpload['nom_arquivo']  = $stName;
            $arArquivosUpload['num_contrato'] = $request->get('inNumContrato');
            $arArquivosUpload['cod_entidade'] = $request->get('inCodEntidade');
            $arArquivosUpload['exercicio']    = $request->get('stExercicio');
            $arArquivosUpload['boCopiado']    = 'FALSE';
            $arArquivosUpload['boExcluido']   = 'FALSE';

            $arArquivos[] = $arArquivosUpload;
        }

        if(!empty($stErro)){
            $stJs = "alertaAviso('".urlencode("Erro ao Incluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        }else{
            Sessao::write('arArquivos', $arArquivos);
            $stJs  = montaListaArquivos($arArquivos, true);
            $stJs .= "f.stArquivo.value = ''; \n";
        }

        sistemaLegado::executaFrameOculto($stJs);
    break;

    //Exclui itens da listagem de veiculos de publicacao utilizados
    case 'excluirListaArquivo':
        $stErro = '';
        $stDirTMP = CAM_GP_LICITACAO."tmp/";

        $arTemp = array();
        $arArquivos = Sessao::read("arArquivos");

        foreach($arArquivos AS $chave => $arquivo){
            if($arquivo['id'] != $_REQUEST['inId']){
                $arTemp[] = $arquivo;
            }else{
                if($arquivo['boCopiado']=='TRUE'){
                    $arquivo['boExcluido'] = 'TRUE';
                    $arTemp[] = $arquivo;
                }else{
                    $stArquivo = $stDirTMP.$arquivo['arquivo'];

                    if (file_exists($stArquivo)) {
                        if(!unlink($stArquivo)){
                            $stErro = $arquivo['nom_arquivo']." não excluído!";
                            break;
                        }
                    }
                }
            }
        }

        if(empty($stErro)){
            Sessao::write("arArquivos",$arTemp);
            $stJs = montaListaArquivos($arTemp, true);
        }else{
            $stJs = "alertaAviso('".urlencode("Erro ao Incluir Arquivo Digital: ".$stErro)."','unica','erro','".Sessao::getId()."');";
        }

        sistemaLegado::executaFrameOculto($stJs);
    break;
}

    function sincronizarDataValidaDocumento($inDiasValidos, $inDataEmissao)
    {
        if ($inDataEmissao != "") {
            if ($inDiasValidos != "") {
                $diasValidos = $inDiasValidos;
            } else {
                $diasValidos = 0;
            }

            $arDataEmissao = explode('/',$inDataEmissao);
            //defino data de emissao
            $ano = $arDataEmissao[2];
            $mes = $arDataEmissao[1];
            $dia = $arDataEmissao[0];

            $dataEmissao = mktime(0,0,0,$mes,$dia,$ano);

            $dataValidade = strftime("%d/%m/%Y" , strtotime("+".$diasValidos." days",$dataEmissao));

            $stJs = "jQuery('#stDataValidade').val('".$dataValidade."');\n";
            $stJs .= "jQuery('#inNumDiasValido').val('".$diasValidos."');\n";
            echo $stJs;
        }
    }

    function sincronizaDiasValidosDocumento($inDataValidade, $inDataEmissao)
    {
        $stJs = "";

        if (strlen($inDataValidade) == 10) {

            if ($inDataValidade != "") {
                $arDataValidade = explode('/',$inDataValidade);
                $dataValidade = $inDataValidade;
            } else {
                $arDataValidade = explode('/',date('d/m/Y'));
                $dataValidade = date('d/m/Y');
            }

             //defino data de validade
            $ano1 = $arDataValidade[2];
            $mes1 = $arDataValidade[1];
            $dia1 = $arDataValidade[0];

            //defino data de emissão
            $arDtEmissao = explode('/',$inDataEmissao);
            $ano2 = $arDtEmissao[2];
            $mes2 = $arDtEmissao[1];
            $dia2 = $arDtEmissao[0];

            //calculo timestam das duas datas
            $timestamp1 = mktime(0,0,0,$mes1,$dia1,$ano1);
            $timestamp2 = mktime(0,0,0,$mes2,$dia2,$ano2);

            //diminuo a uma data a outra
            $segundos_diferenca = $timestamp1 - $timestamp2;

            //converto segundos em dias
            $diasValido = $segundos_diferenca / (60 * 60 * 24);

            //obtenho o valor absoluto dos dias (tiro o possível sinal negativo)
            $diasValido = abs($diasValido);

            //tiro os decimais aos dias de diferenca
            $diasValido = floor($diasValido);

            $stJs .= "jQuery('#stDataValidade').val('');\n";
            $stJs .= "jQuery('#stDataValidade').val('".$dataValidade."');\n";
            $stJs .= "jQuery('#inNumDiasValido').val('".$diasValido."');\n";
        } else {
            $stJs .= "jQuery('#stDataValidade').val('');\n";
            $stJs .= "jQuery('#inNumDiasValido').val('');\n";
        }

        echo $stJs;
    }

    function montaListaDocumentos($arRecordSet , $boExecuta = true)
    {
        if (is_array($arRecordSet) ) {

            $rsDocumentos = new RecordSet;
            $rsDocumentos->preenche( $arRecordSet );

            $obLista = new Lista;

            $obLista->setTitulo('Documentos Exigidos');
            $obLista->setMostraPaginacao( false );
            $obLista->setRecordSet( $rsDocumentos );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Documento");
            $obLista->ultimoCabecalho->setWidth( 35 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Número");
            $obLista->ultimoCabecalho->setWidth( 15 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Validade");
            $obLista->ultimoCabecalho->setWidth( 25 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Ação");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stNomDocumento" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "stNumDocumento" );
            $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
            $obLista->commitDado();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "dtValidade" );
            $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "ALTERAR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:alteraDocumentos();" );
            $obLista->ultimaAcao->addCampo("1","id");
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:excluirDocumentos();" );
            $obLista->ultimaAcao->addCampo("1","id");
            $obLista->commitAcao();

            $obLista->montaHTML();
            $stHTML = $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            if ($boExecuta) {
                return "d.getElementById('spnListaDocumentos').innerHTML = '".$stHTML."';";
            } else {
                return $stHTML;
            }
         } else {
                return "d.getElementById('spnListaDocumentos').innerHTML = '&nbsp;';";
         }
    }

    function buscaValorFornecedorCompraDireta()
    {
        include_once ( TCOM."TComprasCompraDireta.class.php" );
        $obTComprasCompraDireta = new TComprasCompraDireta();
        $obTComprasCompraDireta->setDado( 'cod_entidade' , $_REQUEST['inCodEntidade'] );
        $obTComprasCompraDireta->setDado( 'cod_modalidade' , $_REQUEST['inCodModalidade'] );
        $obTComprasCompraDireta->setDado( 'cod_compra_direta', $_REQUEST['inCodCompraDireta'] );
        $obTComprasCompraDireta->setDado( 'exercicio_entidade' , $_REQUEST['stExercicioCompraDireta'] );
        $obTComprasCompraDireta->recuperaObjetoCompraDireta( $rsCompraDireta );
        if ( $rsCompraDireta->getNumLinhas() > 0 ) {
            $stJs  = "d.getElementById('stDescObjeto').innerHTML = '".nl2br(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/","",$rsCompraDireta->getCampo('descricao'))))."';\n";
            $stJs .= "f.hdnDescObjeto.value = '".nl2br(str_replace("\r\n", "\n", preg_replace("/(\r\n|\n|\r)/","",$rsCompraDireta->getCampo('descricao'))))."';\n";
        } else {
            $stJs  = "d.getElementById('stDescObjeto').innerHTML = '';\n";
            $stJs .= "f.hdnDescObjeto.value = '';\n";
        }

        $stJs.= "f.inCGMContratado.selectedIndex =  0;\n";
        $stJs.= "limpaSelect(f.inCGMContratado,1);\n";

        $obTComprasCompraDireta->recuperaCompraDiretaFornecedores( $rsFornecedores );

        if ( $rsFornecedores->getNumLinhas() == 1 ) {
        $_REQUEST['inCGMFornecedor']=$rsFornecedores->getCampo('cgm_fornecedor');
            buscaValorFornecedor();
            $selected = 'selected';
        } else {
            $selected = '';
        }

        while ( !$rsFornecedores->eof() ) {
            $stJs .= "f.inCGMContratado[".$rsFornecedores->getCorrente()."] = new Option('".$rsFornecedores->getCampo('nom_cgm')."','".$rsFornecedores->getCampo('cgm_fornecedor')."','".$selected."');\n";
            $rsFornecedores->proximo();
        }

        return $stJs;
    }

    function buscaValorFornecedor()
    {
        $obTCompraDiretaContrato = new TComprasContratoCompraDireta;
        $obTCompraDiretaContrato->setDado('cod_compra_direta', $_REQUEST['inCodCompraDireta']);
        $obTCompraDiretaContrato->setDado('cod_modalidade', $_REQUEST['inCodModalidade']);
        $obTCompraDiretaContrato->setDado('cgm_fornecedor', $_REQUEST['inCGMFornecedor']);
        $obTCompraDiretaContrato->setDado('exercicio', Sessao::getExercicio());
        $obTCompraDiretaContrato->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        $obTCompraDiretaContrato->recuperaValorContrato($rsValorContrato);

        $vlContrato = $rsValorContrato->getCampo('valor_contrato');
        $vlContrato = number_format($vlContrato, 2, ',', '.');
        $stJs= " d.getElementById('vlContrato').value = '".$vlContrato."';";
        $stJs.= " d.getElementById('hdnValorContrato').value = '".$vlContrato."';";
        echo $stJs;
    }

    function preencheValorContratoCompraDireta()
    {
         $stJs.= buscaDocumentoFornecedor (  $_REQUEST['inCGMFornecedor'] , $_REQUEST['inCodDocumento'] );
         if ($_REQUEST['inCodCompraDireta'] && $_REQUEST['inCGMFornecedor']) {
            $stJs.= buscaValorFornecedorCompraDireta();
            echo $stJs;

        } else {

            $stJs.= " d.getElementById('vlContrato').value = '';";
            $stJs.= " d.getElementById('hdnValorContrato').value = '';";
            echo $stJs;
        }
    }

    function buscaDocumentoFornecedor($inCgmFornecedor, $inCodDocumento)
    {
        $stNumDoc     = '';
        $stDtValidade = '';
        $stDtEmissao  = '';
        if ($inCgmFornecedor and $inCodDocumento) {
            include_once ( TLIC.'TLicitacaoCertificacaoDocumentos.class.php' );
            $obTLicitacaoCertificacaoDocumentos = new TLicitacaoCertificacaoDocumentos;
            $stFiltro = " and  lcd.cod_documento  = $inCodDocumento
                          and  lcd.cgm_fornecedor = $inCgmFornecedor ";
            $obTLicitacaoCertificacaoDocumentos->recuperaDocumentos( $rsDocumentos, $stFiltro, "order by lcd.dt_validade desc" );

            if ( $rsDocumentos->getCampo ( 'num_documento' ) ) {
                $stNumDoc     = $rsDocumentos->getCampo ( 'num_documento' );
                $stDtValidade = $rsDocumentos->getCampo ( 'dt_validade'   );
                $stDtEmissao  = $rsDocumentos->getCampo ( 'dt_emissao'    );

                $stJs .= "f.stNumDocumento.value ='$stNumDoc    ';";
                $stJs .= "f.stDataValidade.value ='$stDtValidade';";
                $stJs .= "f.stDataEmissao.value  ='$stDtEmissao ';";
            }
        }

        return $stJs;
    }

    function buscaDocumentoAssinado()
    {
        $stNumDoc     = '';
        $stDtValidade = '';
        $stDtEmissao  = '';
        $inCount = 0;
        if ( trim($_REQUEST['inCGMFornecedor']) !="") {
            include_once ( CAM_GP_LIC_MAPEAMENTO."TLicitacaoLicitacaoDocumentos.class.php");
            $obTLicitacaoDocumentos = new TLicitacaoLicitacaoDocumentos;
            $obTLicitacaoDocumentos->setDado('cod_compra_direta', $_REQUEST["inCodCompraDireta"]);
            $obTLicitacaoDocumentos->setDado('cod_entidade', $_REQUEST["inCodEntidade"]);
            $obTLicitacaoDocumentos->setDado('exercicio', $_REQUEST["exercicio"]);
            $obTLicitacaoDocumentos->setDado('cod_modalidade', $_REQUEST["inCodModalidade"]);

            $stFiltro = " AND cgm_fornecedor=".$_REQUEST['inCGMFornecedor']."\n";

            $obTLicitacaoDocumentos->recuperaDocumentosCompraDiretaFornecedor( $rsDocumentos, $stFiltro, "order by ld.cod_documento desc" );
        }

        $arRsDocumentos = $rsDocumentos->arElementos;
        $arDocumentos = Sessao::read('arDocumentos');

        if (is_array($arRsDocumentos) ) {
            foreach ($arRsDocumentos as $chave => $dados) {
                if ($dados['cod_documento'] != $arDocumentos[$chave]['inCodDocumento']) {

                    $stNomDocumento = $dados['nom_documento'];
                    $inCodDocumento = $dados['cod_documento'];
                    $stDtEmissao = $dados['dt_emissao'];
                    $stDtValidade = $dados['dt_validade'];
                    $inNumDocumento = $dados['num_documento'];

                    $inCount = sizeof($arDocumentos);
                    $arDocumentos[$inCount]['id'               ] = $inCount + 1;
                    $arDocumentos[$inCount]['boNovo'           ] = true;
                    $arDocumentos[$inCount]['inCodDocumento'      ] = $inCodDocumento;
                    $arDocumentos[$inCount]['stNumDocumento'   ] = $inNumDocumento;
                    $arDocumentos[$inCount]['stNomDocumento'   ] = $stNomDocumento;
                    $arDocumentos[$inCount]['dtValidade'       ] = $stDtValidade;
                    $arDocumentos[$inCount]['dtEmissao'        ] = $stDtEmissao;
                    $inCount++;
                }
           }
        }
        Sessao::write('arDocumentos', $arDocumentos);
        $arrayDocumentos = $arDocumentos;
        echo 'limpaFormularioDocumentos();';
        echo montaListaDocumentos( $arrayDocumentos);
    }

    function montaListaAditivos($arRecordSet , $boExecuta = true)
    {

        $rsAditivos = new RecordSet;
        $rsAditivos->preenche( $arRecordSet );

        $obLista = new Lista;

        $obLista->setTitulo('Aditivos');
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsAditivos );

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Número do Aditivo");
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Vencimento");
        $obLista->ultimoCabecalho->setWidth( 15 );
        $obLista->commitCabecalho();

        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Ação");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "inCodNorma"   );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "dtVencimento" );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "EXCLUIR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirAditivos();" );
        $obLista->ultimaAcao->addCampo("1","id");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );

        if ($boExecuta) {
            return "d.getElementById('spnListaAditivos').innerHTML = '".$stHTML."';";
        } else {
            return $stHTML;
        }
    }

    function montaListaVeiculos($arRecordSet , $boExecuta = true)
    {

        if (is_array($arRecordSet)) {
            $rsRecordSet = new RecordSet;
            $rsRecordSet->preenche( $arRecordSet );

            $table = new Table();
            $table->setRecordset   ( $rsRecordSet  );
            $table->setSummary     ( 'Veículos de Publicação'  );

            $table->Head->addCabecalho( 'Veículo de Publicação' , 40  );
            $table->Head->addCabecalho( 'Data', 10  );
            $table->Head->addCabecalho( 'Número Publicação', 12  );
            $table->Head->addCabecalho( 'Observação'     , 40  );

            $table->Body->addCampo( '[inVeiculo]-[stVeiculo] ' , 'E');
            $table->Body->addCampo( 'dtDataPublicacao' );
            $table->Body->addCampo( 'inNumPublicacao' );
            $table->Body->addCampo( 'stObservacao'  );

            $table->Body->addAcao( 'alterar' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'alterarListaVeiculos', 'id' ) );
            $table->Body->addAcao( 'excluir' ,  'JavaScript:executaFuncaoAjax(\'%s\' , \'&id=%s\' )' , array( 'excluirListaVeiculos', 'id' ) );

            $table->montaHTML( true );

            if ($boExecuta) {
                return "d.getElementById('spnListaVeiculos').innerHTML = '".$table->getHTML()."';";
            } else {
                return $this->getHTML();
            }
        }
    }

    function montaListaArquivos($arRecordSet, $boExecuta = true)
    {
        if (is_array($arRecordSet)) {
            $arArquivos = array();
            foreach($arRecordSet AS $chave => $arquivo){
                if($arquivo['boExcluido'] != 'TRUE')
                    $arArquivos[] = $arquivo;
            }

            $rsRecordSet = new RecordSet;
            $rsRecordSet->preenche( $arArquivos );

            $obLista = new Lista();
            $obLista->setRecordset( $rsRecordSet  );
            $obLista->setTitulo('Arquivos Digitais');
            $obLista->setMostraPaginacao( false );

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("&nbsp;");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Arquivo");
            $obLista->ultimoCabecalho->setWidth( 50 );
            $obLista->commitCabecalho();

            $obLista->addCabecalho();
            $obLista->ultimoCabecalho->addConteudo("Ações");
            $obLista->ultimoCabecalho->setWidth( 5 );
            $obLista->commitCabecalho();

            $obLista->addDado();
            $obLista->ultimoDado->setCampo( "nom_arquivo" );
            $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
            $obLista->commitDado();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "CONSULTAR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('consultarListaArquivo');" );
            $obLista->ultimaAcao->addCampo("1","id");
            $obLista->commitAcao();

            $obLista->addAcao();
            $obLista->ultimaAcao->setAcao( "EXCLUIR" );
            $obLista->ultimaAcao->setFuncao( true );
            $obLista->ultimaAcao->setLink( "JavaScript:modificaDado('excluirListaArquivo');" );
            $obLista->ultimaAcao->addCampo("1","id");
            $obLista->commitAcao();

            $obLista->montaHTML();
            $stHTML = $obLista->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );

            if ($boExecuta)
                return "d.getElementById('spnListaArquivos').innerHTML = '".$stHTML."';";
            else
                return $stHTML;
        }
    }

    function consultarListaArquivo(Request $request)
    {
        $arArquivos = Sessao::read('arArquivos');

        $stDirTMP = CAM_GP_LICITACAO."tmp/";
        $stDirANEXO = CAM_GP_LIC_ANEXOS."contrato/";

        foreach($arArquivos AS $chave => $arquivo){
            if($arquivo['id'] == $request->get('inId')){
                if($arquivo['boCopiado'] == 'FALSE'){
                    $stArquivo = $stDirTMP.$arquivo['arquivo'];
                    $stNomArq = $arquivo['nom_arquivo'];
                }else{
                    $stArquivo = $stDirANEXO.$arquivo['arquivo'];
                    $stNomArq = $arquivo['nom_arquivo'];
                }

                break;
            }
        }

        if(is_readable($stArquivo)){
            $stLink = "../../../exportacao/instancias/processamento/download.php";

            $stJs  = "
                    function abrirArqDigital(stArq, stNom){
                        var stAction = f.action;
                        var stTarget = f.target;
                        f.action = '".$stLink."?boCompletaDir=false&arq='+stArq+'&label='+stNom;
                        f.target = 'oculto'
                        f.submit();
                        f.action = stAction; 
                        f.target = stTarget;
                    }
            ";
            $stJs .= " abrirArqDigital('".$stArquivo."','".$stNomArq."');";
        }else
            $stJs = "alertaAviso('Erro ao abrir o Arquivo Digital!','unica','erro','".Sessao::getId()."');";

        sistemaLegado::executaFrameOculto($stJs);
    }
