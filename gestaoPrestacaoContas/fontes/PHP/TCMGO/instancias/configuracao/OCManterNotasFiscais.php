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
    * Página do Oculto
    * Data de Criação   : 01/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ManterContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];
$stAcao = $request->get('stAcao');

switch ($stCtrl) {

    case "montaChave":

        if ($_REQUEST['inCodTipoNota'] == 3) {
            $obTxtChave = new TextBox;
            $obTxtChave->setNAme      ("inChave");
            $obTxtChave->setId        ("inChave");
            $obTxtChave->setValue     ($_REQUEST['inChave']);
            $obTxtChave->setRotulo    ("Chave de Acesso");
            $obTxtChave->setTitle     ("Informe a chave de acesso");
            $obTxtChave->setNull      (true);
            $obTxtChave->setInteiro   (true);
            $obTxtChave->setSize      (44);
            $obTxtChave->setMaxLength (44);

            $obFormulario = new Formulario;
            $obFormulario->addComponente( $obTxtChave );
            $obFormulario->montaInnerHTML();

            $stJs.= "jQuery('#spnChave').html('".$obFormulario->getHTML()."');";
        } else {
            $stJs.= "jQuery('#spnChave').html('');";
        }
        echo $stJs;

    break;

    case "carregaDados":

        if ($_REQUEST['inCodNota']) {

            include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscal.class.php" );
            $obTTCMGONotaFiscal = new TTCMGONotaFiscal;
            $stFiltro  = " WHERE cod_nota = ".$_REQUEST['inCodNota'];
            $obTTCMGONotaFiscal->recuperaTodos($rsNotaFiscal, $stFiltro);

            if (Sessao::getExercicio() > 2010) {
                include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenhoLiquidacao.class.php" );
                $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenhoLiquidacao;
            } else {
                include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGONotaFiscalEmpenho.class.php" );
                $obTTCMGONotaFiscalEmpenho = new TTCMGONotaFiscalEmpenho;
            }
            $stFiltro  = " WHERE cod_nota = ".$rsNotaFiscal->getCampo('cod_nota');
            $obTTCMGONotaFiscalEmpenho->recuperaTodos($rsNotaFiscalEmpenho, $stFiltro);

            $arEmpenhos = array();
            $inCount = 0;

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            while ( !$rsNotaFiscalEmpenho->eof()) {
                $stFiltro  = "   AND e.exercicio    = '".$rsNotaFiscalEmpenho->getCampo('exercicio')."'";
                $stFiltro .= "   AND e.cod_entidade =  ".$rsNotaFiscalEmpenho->getCampo('cod_entidade');
                $stFiltro .= "   AND e.cod_empenho  =  ".$rsNotaFiscalEmpenho->getCampo('cod_empenho');
                $obTEmpenhoEmpenho->recuperaEmpenhoPreEmpenhoCgm($rsEmpenho, $stFiltro);

                $arEmpenhos[$inCount]['cod_entidade']  = $rsEmpenho->getCampo('cod_entidade');
                $arEmpenhos[$inCount]['cod_empenho']   = $rsEmpenho->getCampo('cod_empenho');
                $arEmpenhos[$inCount]['exercicio']     = $rsEmpenho->getCampo('exercicio');
                $arEmpenhos[$inCount]['nom_cgm']       = $rsEmpenho->getCampo('credor');
                $arEmpenhos[$inCount]['nuVlAssociado'] = number_format($rsNotaFiscalEmpenho->getCampo('vl_associado'),2,',','.');
                if (Sessao::getExercicio() > 2010) {
                    $arEmpenhos[$inCount]['cod_nota_liquidacao']  = $rsNotaFiscalEmpenho->getCampo('cod_nota_liquidacao');
                    $arEmpenhos[$inCount]['exercicio_liquidacao'] = $rsNotaFiscalEmpenho->getCampo('exercicio_liquidacao');
                }

                $inCount++;
                $rsNotaFiscalEmpenho->proximo();
            }

            $stJs  = "f.data_emissao.value            = '".$rsNotaFiscal->getCampo('data_emissao')             ."';\n";
            $stJs .= "f.inCodTipoNota.value           = '".$rsNotaFiscal->getCampo('cod_tipo')                 ."';\n";
            $stJs .= "f.stTipoDocto.value           = '".$rsNotaFiscal->getCampo('cod_tipo')                 ."';\n";
            $stJs .= "f.inNumNota.value               = '".$rsNotaFiscal->getCampo('nro_nota')                 ."';\n";
            $stJs .= "f.inCodNota.value               = '".$rsNotaFiscal->getCampo('cod_nota')                 ."';\n";
            $stJs .= "f.insc_estadual.value           = '".$rsNotaFiscal->getCampo('inscricao_estadual')       ."';\n";
            $stJs .= "f.cod_entidade.value            = '".$arEmpenhos[0]['cod_entidade']                      ."';\n";
            $stJs .= "f.inCodEntidade.value           = '".$arEmpenhos[0]['cod_entidade']                      ."';\n";
            $stJs .= "f.inNumSerie.value              = '".addslashes($rsNotaFiscal->getCampo('nro_serie'))    ."';\n";
            $stJs .= "f.stAIFD.value                  = '".$rsNotaFiscal->getCampo('aidf')                     ."';\n";
            $stJs .= "f.dtEmissao.value               = '".$rsNotaFiscal->getCampo('data_emissao')             ."';\n";
            $stJs .= "f.data_emissao.value            = '".$rsNotaFiscal->getCampo('data_emissao')             ."';\n";
            $stJs .= "f.inNumInscricaoMunicipal.value = '".$rsNotaFiscal->getCampo('inscricao_municipal')      ."';\n";
            $stJs .= "f.inNumInscricaoEstadual.value  = '".$rsNotaFiscal->getCampo('inscricao_estadual')       ."';\n";
            $stJs .= "f.nuVlNotaFiscal.value          = '".number_format($rsNotaFiscal->getCampo('vl_nota'),2,',','.')."';\n";

            if ($rsNotaFiscal->getCAmpo('cod_tipo') == 3) {
                $obTxtChave = new TextBox;
                $obTxtChave->setNAme      ("inChave");
                $obTxtChave->setId        ("inChave");
                $obTxtChave->setValue     ($rsNotaFiscal->getCampo('chave_acesso'));
                $obTxtChave->setRotulo    ("Chave de Acesso");
                $obTxtChave->setTitle     ("Informe a chave de acesso");
                $obTxtChave->setNull      (true);
                $obTxtChave->setInteiro   (true);
                $obTxtChave->setSize      (44);
                $obTxtChave->setMaxLength (44);

                $obFormulario = new Formulario;
                $obFormulario->addComponente( $obTxtChave );
                $obFormulario->montaInnerHTML();

                $stJs.= "jQuery('#spnChave').html('".$obFormulario->getHTML()."');";
            }

            $stJs .= "f.cod_entidade.disabled         = true;                                                      \n";
            $stJs .= "f.stNomEntidade.disabled        = true;                                                      \n";

            Sessao::write('arEmpenhos', $arEmpenhos);
            $stJs .= montaListaEmpenhos();

        }
        echo $stJs;

    break;

    case "incluirEmpenhoLista":

        $arRegistro = array();
        $arEmpenhos = array();
        $numEmpenho = $_REQUEST['numEmpenho'];
        $boIncluir  = true;

        $arEmpenhos = Sessao::read('arEmpenhos');

        if ($_REQUEST['stExercicioEmpenho'] and $numEmpenho != "" and $_REQUEST['nuVlAssociado']) {

            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho                      );
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );
            if (Sessao::getExercicio() > 2010) {
                $arNotaLiquidacao = explode('||', $_REQUEST['cmbLiquidacao']);
                $inCodNotaLiquidacao   = $arNotaLiquidacao[0];
                $numEmpenho .= $inCodNotaLiquidacao;
                $stExercicioLiquidacao = $arNotaLiquidacao[1];
            }

            $obTEmpenhoEmpenho->recuperaEmpenhoNotaFiscal($rsRecordSet);

            if ( $rsRecordSet->getNumLinhas() > 0 ) {

                if ( count( $arEmpenhos ) > 0 ) {
                    foreach ($arEmpenhos as $key => $array) {
                        $stCod = $array['cod_empenho'];
                        if (Sessao::getExercicio() > 2010) {
                            $stCod .= $array['cod_nota_liquidacao'];
                        }

                        if ($numEmpenho == $stCod) {
                            $boIncluir = false;
                            $stJs .= "alertaAviso('Empenho já incluso na lista.','form','erro','".Sessao::getId()."');";
                            break;
                        }
                    }
                }
                if ($boIncluir) {

                    $arRegistro['cod_entidade'        ]  = $rsRecordSet->getCampo('cod_entidade');
                    $arRegistro['cod_empenho'         ]  = $rsRecordSet->getCampo('cod_empenho');
                    $arRegistro['data_empenho'        ]  = $rsRecordSet->getCampo('dt_empenho');
                    $arRegistro['nom_cgm'             ]  = $rsRecordSet->getCampo('credor');
                    $arRegistro['exercicio'           ]  = $rsRecordSet->getCampo('exercicio');
                    if (Sessao::getExercicio() > 2010) {
                        $arRegistro['cod_nota_liquidacao' ]  = $inCodNotaLiquidacao;
                        $arRegistro['exercicio_liquidacao']  = $stExercicioLiquidacao;
                    }
                    $arRegistro['nuVlAssociado'] = $_REQUEST['nuVlAssociado'];
                    $arEmpenhos[] = $arRegistro ;

                    Sessao::write('arEmpenhos', $arEmpenhos);
                    $stJs .= "f.cod_entidade.disabled = true; ";
                    $stJs .= "f.stNomEntidade.disabled = true; ";
                    $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                    if (Sessao::getExercicio() > 2010) {
                        $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                        $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
                    }
                    $stJs .= "f.stEmpenho.value = '';";
                    $stJs .= "f.numEmpenho.value = '';";
                    $stJs .= "f.nuVlAssociado.value = '';";
                    $stJs .= montaListaEmpenhos();
                }
            } else {
                $stJs .= "alertaAviso('Empenho informado inválido.','form','erro','".Sessao::getId()."');";
            }
        } else {
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs .= "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');";
            }
            if (!$numEmpenho) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.nuVlAssociado.value = '';";
                $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
            }
            if (!$_REQUEST['nuVlAssociado']) {
                $stJs .= "alertaAviso('Informe o valor associado.','form','erro','".Sessao::getId()."');";
            }
        }
        echo $stJs;
    break;

    case "excluirEmpenhoLista":

        $arTempEmp = array();
        $arEmpenhos = Sessao::read('arEmpenhos');

        foreach ($arEmpenhos as $registro) {
            if (Sessao::getExercicio() > 2010) {
                $stChaveRequest = $_REQUEST['codEmpenho'].$_REQUEST['codNotaLiquidacao'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio'];
                $stChaveRegistro = $registro['cod_empenho'].$registro['cod_nota_liquidacao'].$registro['cod_entidade'].$registro['exercicio'];
            } else {
                $stChaveRequest = $_REQUEST['codEmpenho'].$_REQUEST['codEntidade'].$_REQUEST['stExercicio'];
                $stChaveRegistro = $registro['cod_empenho'].$registro['cod_entidade'].$registro['exercicio'];
            }

            if ($stChaveRegistro != $stChaveRequest) {
                $arTempEmp[] = $registro;
            }
        }

        if (count($arTempEmp) <= 0) {

            $stJs  = "f.inCodEntidade.disabled = false; ";
            $stJs .= "f.stNomEntidade.disabled = false; ";

        }

        Sessao::write('arEmpenhos', $arTempEmp);
        $stJs .= montaListaEmpenhos();

        echo $stJs;
    break;

    case "limpar":

             $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
             $stJs .= "f.numEmpenho.value = '';";
             $stJs .= "f.nuVlAssociado.value = '';";
             $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
             $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";

        echo $stJs;
    break;

    case "preencheInner":

        $numEmpenho = $_REQUEST['numEmpenho'];

        if ($_REQUEST['inCodEntidade'] and $_REQUEST['stExercicioEmpenho'] and $numEmpenho) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho                      );
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicioEmpenho']  );
            if ($_REQUEST['inCodNota'] != '') {
                $obTEmpenhoEmpenho->setDado('cod_nota_fiscal', $_REQUEST['inCodNota']);
            }

            $obTEmpenhoEmpenho->recuperaEmpenhoLiquidacaoNotaFiscal($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "'.$rsRecordSet->getCampo('credor').'";';

                $stJs .= buscaLiquidacoes();
            } else {
                $stJs  = "alertaAviso('Não há liquidações para vinculo com a NF.','form','erro','".Sessao::getId()."');\n";
                $stJs .= 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
            }
        } else {
            if (!$_REQUEST['inCodEntidade']) {
                $stJs  = "alertaAviso('Informe a entidade.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.inCodEntidade.focus();\n";
            }
            if (!$_REQUEST['stExercicioEmpenho']) {
                $stJs  = "alertaAviso('Informe o exercício do empenho.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "f.stExercicioEmpenho.focus();\n";
            }
            if (!$numEmpenho) {
                $stJs  = 'd.getElementById("stEmpenho").innerHTML = "&nbsp;";';
                $stJs .= "f.numEmpenho.value = '';";
                $stJs .= "f.nuVlAssociado.value = '';";
                $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
                $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
            }
            $stJs .= "f.numEmpenho.value = '';";

        }

        echo $stJs;

    break;

    case "buscaEmpenho":

        $numEmpenho = $_REQUEST['numEmpenho'];

        if (!empty($numEmpenho)) {
            include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
            $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
            $obTEmpenhoEmpenho->setDado( 'exercicio'   , $_REQUEST['stExercicio']  );
            $obTEmpenhoEmpenho->setDado( 'cod_empenho' , $numEmpenho               );

            $obTEmpenhoEmpenho->recuperaEmpenhoBuscaInner($rsRecordSet, $stFiltro);

            if ($rsRecordSet->getNumLinhas() > 0) {

                $stJs = "jQuery('#stEmpenho').html('".$rsRecordSet->getCampo('credor')."');";

            } else {
                $stJs  = "alertaAviso('Não há liquidações para vinculo com a NF.','form','erro','".Sessao::getId()."');\n";
                $stJs .= "jQuery('#stEmpenho').html('&nbsp');";
                $stJs .= "jQuery('#numEmpenho').val('');";
            }
        } else {
            $stJs  = "jQuery('#numEmpenho').val('');";
            $stJs .= "jQuery('#stEmpenho').html('&nbsp');";
        }
        echo $stJs;

    break;

}

function buscaLiquidacoes()
{
    $numEmpenho = $_REQUEST['numEmpenho'];;
    include_once CAM_GF_EMP_NEGOCIO.'REmpenhoOrdemPagamento.class.php';
    $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
    $stJs .= "f = parent.frames['telaPrincipal'].document.frm;\n";
    $stJs .= "d = parent.frames['telaPrincipal'].document;\n";
    $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
    $stJs .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
    if ($numEmpenho && $_REQUEST["inCodEntidade"]) {
        include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );
        $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
        $obTEmpenhoEmpenho->setDado('cod_empenho' , $numEmpenho);
        $obTEmpenhoEmpenho->setDado('exercicio'   , $_REQUEST['stExercicioEmpenho']);
        $obTEmpenhoEmpenho->setDado('cod_entidade', $_REQUEST['inCodEntidade']);
        if ($_REQUEST['inCodNota'] != '') {
            $obTEmpenhoEmpenho->setDado('cod_nota_fiscal', $_REQUEST['inCodNota']);
        }
        $obTEmpenhoEmpenho->recuperaLiquidacoesNotaFiscal($rsLiquidacoes);

        $inContador = 1;
        while ( !$rsLiquidacoes->eof() ) {
            if ( $rsLiquidacoes->getCampo("cod_empenho") == $numEmpenho) {

                $flValorNota        = $rsLiquidacoes->getCampo( "vl_nota" );
                $flValorNotaTMP     = str_replace( '.','',$flValorNota );
                $flValorNotaTMP     = str_replace( ',','.',$flValorNotaTMP );
                if ($flValorNotaTMP > 0) {
                    $inCodigoLiquidacao = $rsLiquidacoes->getCampo('cod_nota');
                    $exercicioNota      = $rsLiquidacoes->getCampo('exercicio_nota');
                    $dtDataLiquidacao   = $rsLiquidacoes->getCampo('dt_liquidacao');
                    $inCodigoEmpenho    = $rsLiquidacoes->getCampo('cod_empenho');
                    $exercicioEmpenho   = $rsLiquidacoes->getCampo('exercicio_empenho');

                    $mixCombo = $inCodigoLiquidacao." - ".$dtDataLiquidacao;
                    $mixComboValor = $inCodigoLiquidacao."||".$exercicioNota."||".$dtDataLiquidacao."||".$inCodigoEmpenho."||".$exercicioEmpenho;
                    $stJs .= "f.cmbLiquidacao.options[$inContador] = new Option('".$mixCombo."','".$mixComboValor."'); \n";
                    $inContador++;
                }
            }
            $rsLiquidacoes->proximo();
        }

        if ($rsLiquidacoes->inNumLinhas < 1) {
                $stJs .= "alertaAvisoTelaPrincipal('Número do Empenho é inválido (".$numEmpenho.").','form','erro','" . Sessao::getId() . "', '../');";
                $stJs .= "f.numEmpenho.value='';";
                $stJs .= "d.getElementById('stEmpenho').innerHTML='&nbsp;';";

        }

        $rsLiquidacoes->anterior();
        $stFornecedor = ( $rsLiquidacoes->getCampo('credor')) ? str_replace("'","\'",$rsLiquidacoes->getCampo('credor')): '&nbsp;';
        $stJs .= "d.getElementById('stEmpenho').innerHTML='".$stFornecedor."';";
    } else {
        $stJs .= "f.numEmpenho.value='';";
        $stjs .= "d.getElementById('stEmpenho').innerHTML='&nbsp;';";
        if ($_REQUEST["inCodEntidade"] == "") {
            $stMensagem = "Digite um Número do Empenho para a Entidade Selecionada.";
            $stJs .= "alertaAvisoTelaPrincipal('".$stMensagem."','form','erro','" . Sessao::getId() . "', '../');";
        }
    }

    return $stJs;
}

function montaListaEmpenhos()
{
    $obLista = new Lista;
    $rsLista = new RecordSet;
    $rsLista->preenche ( Sessao::read('arEmpenhos') );

    while (!$rsLista->eof()) {
        $vlTotal = str_replace('.','',$rsLista->getCampo('nuVlAssociado'));
        $vlTotal = str_replace(',','.',$vlTotal);
        $vlSoma  = $vlSoma + $vlTotal;
        $rsLista->proximo();
    }

    $vlTotal = number_format($vlSoma,2,',','.');

    $rsLista->setPrimeiroElemento();

    $obLista->setRecordset( $rsLista );
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ( 'Lista de empenhos' );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Empenho");
    $obLista->ultimoCabecalho->setWidth( 10);
    $obLista->commitCabecalho();

    if (Sessao::getExercicio() > 2010) {
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidação");
        $obLista->ultimoCabecalho->setWidth( 10);
        $obLista->commitCabecalho();
    }

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nome do Credor");
    $obLista->ultimoCabecalho->setWidth( 60 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Associado");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_empenho]/[exercicio]" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    if (Sessao::getExercicio() > 2010) {
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "[cod_nota_liquidacao]/[exercicio_liquidacao]" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
    }

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuVlAssociado" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "javascript: executaFuncaoAjax('excluirEmpenhoLista');" );
    if (Sessao::getExercicio() > 2010) {
        $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codNotaLiquidacao=[cod_nota_liquidacao]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");
    } else {
        $obLista->ultimaAcao->addCampo("","&codEmpenho=[cod_empenho]&codEntidade=[cod_entidade]&stExercicio=[exercicio]");
    }
    $obLista->commitAcao();

    $obLista->montaHTML();

    $html = $obLista->getHTML();
    $html = str_replace("\n","",$html);
    $html = str_replace("  ","",$html);
    $html = str_replace("'","\\'",$html);

    $stJs  = 'd.getElementById("nuSoma").innerHTML = "'.$vlTotal.'";';
    $stJs .= "f.nuVlTotal.value = '".$vlTotal."';";
    $stJs .= "d.getElementById('spnLista').innerHTML = '';\n";
    $stJs .= "d.getElementById('spnLista').innerHTML = '".$html."';\n";

    return $stJs;

}

?>
