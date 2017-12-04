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
* Página de Oculto
* Data de criação : 24/10/2006

* @author Analista: Leandro Oliveira
* @author Programador: Rafael Almeida

* @ignore

$Id: OCConsultarServidor.php 66023 2016-07-08 15:01:19Z michel $

* Casos de uso: uc-04.04.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function preencherSpanCedencia()
{
    $rsAdidoCedido = Sessao::read('rsAdidoCedido');
    if ( $rsAdidoCedido->getNumLinhas() > 0 ) {
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );
        $obTCGM = new TCGM;
        $stFiltro = " WHERE numcgm = ".$rsAdidoCedido->getCampo("cgm_cedente_cessionario");
        $obTCGM->recuperaTodos($rsCGM,$stFiltro);

        $stRotuloCGM       = ( $rsAdidoCedido->getCampo("tipo_cedencia") == 'a' ) ? "CGM Órgão/Entidade Cedente" : "CGM Órgão/Entidade Cessionário";
        $stIndicativoOnus = ($rsAdidoCedido->getCampo("indicativo_cedencia") == "c") ? "Cedente" : "Cessionário";
        $stValueCGM        = $rsAdidoCedido->getCampo("cgm_cedente_cessionario")."-".$rsCGM->getCampo("nom_cgm");

        $obLblDataInicialAto = new Label();
        $obLblDataInicialAto->setRotulo("Data Inicial do Ato");
        $obLblDataInicialAto->setValue($rsAdidoCedido->getCampo("data_inicial"));

        $obLblDataFinalAto = new Label();
        $obLblDataFinalAto->setRotulo("Data Final do Ato");
        $obLblDataFinalAto->setValue($rsAdidoCedido->getCampo("data_final"));

        $obLblCgmOrgaoEntidade = new Label();
        $obLblCgmOrgaoEntidade->setRotulo($stRotuloCGM);
        $obLblCgmOrgaoEntidade->setValue($stValueCGM);

        $obLblIndicativoOnus = new Label();
        $obLblIndicativoOnus->setRotulo("Indicativo de Ônus");
        $obLblIndicativoOnus->setValue($stIndicativoOnus);

        $obFormulario = new Formulario();
        $obFormulario->addTitulo("Informações de Cedência");
        $obFormulario->addComponente($obLblDataInicialAto);
        $obFormulario->addComponente($obLblDataFinalAto);
        $obFormulario->addComponente($obLblCgmOrgaoEntidade);
        $obFormulario->addComponente($obLblIndicativoOnus);
        $obFormulario->montaInnerHTML();
        $stJs .= "d.getElementById('spnCedencia').innerHTML = '".$obFormulario->getHTML()."'; \n";
    }

    return $stJs;
}

function preencherSpanAposentadoria()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTPessoalContratoServidor = new TPessoalContratoServidor();
    $obTPessoalAposentadoria = new TPessoalAposentadoria();
    $stFiltro = " AND aposentadoria.cod_contrato = ".$_GET['inCodContrato'];
    $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltro);
    $obTPessoalContratoServidor->setDado("cod_contrato",$_GET['inCodContrato']);
    $obTPessoalContratoServidor->recuperaPorChave($rsContrato);
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $arCompetencia = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $dtCompetencia = $arCompetencia[2]."-".$arCompetencia[1];
    $arConcessao   = explode("/",$rsAposentadoria->getCampo("data_concessao"));
    $dtConcessao   = $arConcessao[2]."-".$arConcessao[1];
    $stHtml = "";
    if ( $rsContrato->getCampo("ativo") == "f" and $dtConcessao <= $dtCompetencia ) {
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalClassificacao.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalEnquadramento.class.php");
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoriaEncerramento.class.php");
        $obTPessoalClassificacao = new TPessoalClassificacao();
        $obTPessoalClassificacao->setDado("cod_classificacao",$rsAposentadoria->getCampo("cod_classificacao"));
        $obTPessoalClassificacao->recuperaPorChave($rsClassificacao);
        $obTPessoalEnquadramento = new TPessoalEnquadramento();
        $obTPessoalEnquadramento->setDado("cod_enquadramento",$rsAposentadoria->getCampo("cod_enquadramento"));
        $obTPessoalEnquadramento->recuperaPorChave($rsEnquadramento);
        $obTPessoalAposentadoriaEncerramento = new TPessoalAposentadoriaEncerramento();
        $obTPessoalAposentadoriaEncerramento->setDado("cod_contrato",$rsAposentadoria->getCampo("cod_contrato"));
        $obTPessoalAposentadoriaEncerramento->setDado("timestamp",$rsAposentadoria->getCampo("timestamp"));
        $obTPessoalAposentadoriaEncerramento->recuperaPorChave($rsEncerramento);

        $obLblDataConcessao = new Label();
        $obLblDataConcessao->setRotulo("Data da Concessão do Benefício");
        $obLblDataConcessao->setValue($rsAposentadoria->getCampo("data_concessao"));
        $obLblDataConcessao->setId("stDataConcessao");

        $obLblClassificacao = new Label();
        $obLblClassificacao->setRotulo("Classificação Regra Aposentadoria");
        $obLblClassificacao->setValue($rsClassificacao->getCampo("nome_classificacao"));
        $obLblClassificacao->setId("stClassificacao");

        $obLblEnquadramento = new Label();
        $obLblEnquadramento->setRotulo("Enquadramento da Aposentadoria");
        $obLblEnquadramento->setValue($rsEnquadramento->getCampo("descricao"));
        $obLblEnquadramento->setId("stEnquadramento");

        $obLblTipo = new Label();
        $obLblTipo->setRotulo("Tipo de Reajuste");
        $obLblTipo->setValue($rsEnquadramento->getCampo("reajuste"));
        $obLblTipo->setId("stTipo");

        $obLblPercentual = new Label();
        $obLblPercentual->setRotulo("Percentual do Benefício Recebido em Folha");
        $obLblPercentual->setValue(number_format($rsAposentadoria->getCampo("percentual"),2,',','.')."%");
        $obLblPercentual->setId("stPercentual");

        $obLblDataEncerramento = new Label();
        $obLblDataEncerramento->setRotulo("Data de Encerramento");
        $obLblDataEncerramento->setValue($rsEncerramento->getCampo("dt_encerramento"));
        $obLblDataEncerramento->setId("stDataEncerramento");

        $obLblMotivo = new Label();
        $obLblMotivo->setRotulo("Motivo do Encerramento");
        $obLblMotivo->setValue($rsEncerramento->getCampo("motivo"));
        $obLblMotivo->setId("stMotivo");

        $obFormulario = new Formulario();
        $obFormulario->addTitulo( "Informações da Aposentadoria"                                    );
        $obFormulario->addComponente($obLblDataConcessao);
        $obFormulario->addComponente($obLblClassificacao);
        $obFormulario->addComponente($obLblEnquadramento);
        $obFormulario->addComponente($obLblTipo);
        $obFormulario->addComponente($obLblPercentual);
        $obFormulario->addComponente($obLblDataEncerramento);
        $obFormulario->addComponente($obLblMotivo);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAposentadoria').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function preencherSpanRescisao()
{
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCasoCausa.class.php");
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCausaRescisao.class.php");
    $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
    $obTPessoalContratoServidorCasoCausa->setDado("cod_contrato",$_GET['inCodContrato']);
    $obTPessoalContratoServidorCasoCausa->recuperaPorChave($rsContrato);

    if ($rsContrato->getNumLinhas() > 0) {
        $obTPessoalCasoCausa = new TPessoalCasoCausa();
        $obTPessoalCasoCausa->setDado("cod_caso_causa",$rsContrato->getCampo("cod_caso_causa"));
        $obTPessoalCasoCausa->recuperaPorChave($rsCasoCausa);

        $obTPessoalCausaRescisao = new TPessoalCausaRescisao();
        $obTPessoalCausaRescisao->setDado("cod_causa_rescisao",$rsCasoCausa->getCampo("cod_causa_rescisao"));
        $obTPessoalCausaRescisao->recuperaPorChave($rsCausaRescisao);

        $obLblRescisao = new Label;
        $obLblRescisao->setName               ( "dtRescisao"           );
        $obLblRescisao->setRotulo             ( "Data Rescisão"         );
        $obLblRescisao->setValue              ( $rsContrato->getCampo("dt_rescisao")            );

        $obLblCausa = new Label();
        $obLblCausa->setRotulo("Causa");
        $obLblCausa->setValue($rsCausaRescisao->getCampo("num_causa")." - ".$rsCausaRescisao->getCampo("descricao"));

        $obFormulario = new Formulario();
        $obFormulario->addTitulo( "Informações da Rescisão"                                    );
        $obFormulario->addComponente($obLblRescisao);
        $obFormulario->addComponente($obLblCausa);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnRescisao').innerHTML = '".$stHtml."'; \n";

    return $stJs;
}

function preencherSpanTurnos()
{
    include_once(CAM_GRH_PES_NEGOCIO."RPessoalServidor.class.php");
    $obRPessoalServidor = new RPessoalServidor;
    $rsFaixaTurno = new RecordSet();
    if ($_GET['inCodGradeHorario']) {
        $obRPessoalServidor->addContratoServidor();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->setCodGrade( $_GET['inCodGradeHorario'] );
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->addFaixaTurno();
        $obRPessoalServidor->roUltimoContratoServidor->obRPessoalGradeHorario->roRPessoalFaixaTurno->listarFaixaTurno( $rsFaixaTurno,$boTransacao );
    }

    $obLista = new Lista;
    $obLista->setTitulo( "Turnos" );
    $obLista->setRecordSet( $rsFaixaTurno );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Horário de Entrada" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Horário de Saída" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_entrada" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "hora_saida" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs .= "d.getElementById('spnTurnos').innerHTML = '".$stHtml."';";

    return $stJs;
}

function preencherSpanPrevidencia()
{
    $rsLista = new RecordSet;
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addContratoServidor();
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->obTPrevidencia->setDado('cod_contrato',$_GET['inCodContrato']);
    $obRPessoalServidor->roUltimoContratoServidor->obRFolhaPagamentoPrevidencia->listarPrevidencia( $rsLista );
    $obLista = new Lista;

    $arPrevidencia = Sessao::read('PREVIDENCIA');
    while ( !$rsLista->eof() ) {
        if ( $rsLista->getCampo('booleano') == 'true' ) {
            $arPrevidencia[] = $rsLista->getCampo('cod_previdencia');
        }
        $rsLista->proximo();
    }
    Sessao::write('PREVIDENCIA', $arPrevidencia);

    $rsLista->setPrimeiroElemento();
    $obLista->setRecordSet( $rsLista );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Código" );
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Descrição" );
    $obLista->ultimoCabecalho->setWidth( 70 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Tipo" );
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obChkPrevidencia = new CheckBox;
    $obChkPrevidencia->setName           ( "inCodAbaPrevidencia_[tipo_previdencia]_[cod_previdencia]_"  );
    $obChkPrevidencia->setValue          ( "true");
    $obChkPrevidencia->obEvento->setOnClick( "buscaValor('validaPrevidencia',4)" );
    $obChkPrevidencia->setDisabled(true);

    $obLista->addDadoComponente( $obChkPrevidencia );
    $obLista->ultimoDado->setCampo( "booleano" );
    $obLista->ultimoDado->setAlinhamento('CENTRO');
    $obLista->commitDadoComponente();

    $obHdnPrevidencia = new Hidden;
    $obHdnPrevidencia->setName           ( "stTipoAbaPrevidencia"   );
    $obHdnPrevidencia->setValue          ( 'tipo_previdencia' );

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("DIREITA");
    $obLista->ultimoDado->setCampo( "cod_previdencia" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "descricao" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "tipo_previdencia" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    // preenche a lista com innerHTML

    $stJs .= "d.getElementById('spnPrevidencia').innerHTML = '".$stHtml."';";

    return $stJs;
}

function listarAlterarDependente()
{
    $obRPessoalServidor = new RPessoalServidor;
    $obRPessoalServidor->addDependente();
    $obRPessoalServidor->setCodServidor($_GET['inCodServidor']);
    $obRPessoalServidor->roUltimoDependente->listarPessoalDependente($rsDependente,$boTransacao);

    return $rsDependente;
}

function preencherSpanDependentes()
{
    $rsDependente = listarAlterarDependente();
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo( "Dependentes Cadastrados" );
    $obLista->setRecordSet( $rsDependente );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 3 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Nome" );
    $obLista->ultimoCabecalho->setWidth( 35 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Sexo" );
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Data de nascimento" );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( "Dependente IR" );
    $obLista->ultimoCabecalho->setWidth( 25 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_cgm" );
    $obLista->ultimoDado->setAlinhamento( 'LEFT' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "sexo" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_nascimento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "descricao_vinculo" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();

    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    $stHtml = str_replace(chr(13),"",$stHtml);
    $stHtml = str_replace(chr(13).chr(10),"",$stHtml);

    // preenche a lista com innerHTML
    $stJs .= "d.getElementById('spnDependente').innerHTML = '".$stHtml."';";

    return $stJs;
}

function montaListaArqDigital()
{
    $rsRecordSet = new Recordset;
    $arArquivosDocumentos = ( is_array( Sessao::read('arArquivosDocumentos') ) ) ? Sessao::read('arArquivosDocumentos') : array();

    $rsRecordSet->preenche( $arArquivosDocumentos );
    $stHtml = "";
    if ($rsRecordSet->getNumLinhas() > 0) {
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setTitulo( "Lista de Cópias digitais de documentos" );
        $obLista->setRecordSet( $rsRecordSet );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 3 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Tipo de documento" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo( "Arquivo" );
        $obLista->ultimoCabecalho->setWidth( 25 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "stTipoArqDocDigital" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "name" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();

        $obLista->addAcao();
        $obLista->ultimaAcao->setAcao( "VISUALIZAR" );
        $obLista->ultimaAcao->setFuncao( true );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarDado('abrirArqDigital', 0);" );
        $obLista->ultimaAcao->addCampo("1","inId");
        $obLista->commitAcao();

        $obLista->montaHTML();
        $stHtml = $obLista->getHTML();
        $stHtml = str_replace("\n","",$stHtml);
        $stHtml = str_replace("  ","",$stHtml);
        $stHtml = str_replace("'","\\'",$stHtml);
    }

    $stJs = "d.getElementById('spnListaArqDigital').innerHTML = '".$stHtml."';";

    return $stJs;
}

function processarForm()
{
    $stJs .= preencherSpanCedencia();
    $stJs .= preencherSpanAposentadoria();
    $stJs .= preencherSpanRescisao();
    $stJs .= preencherSpanTurnos();
    $stJs .= preencherSpanPrevidencia();
    $stJs .= preencherSpanDependentes();
    $stJs .= montaListaArqDigital();

    return $stJs;
}

switch ($request->get("stCtrl")) {
    case "processarForm":
        $stJs .= processarForm();
    break;
    case "abrirArqDigital":
        $arArquivosDocumentos = Sessao::read("arArquivosDocumentos");

        $stNomArq = "";
        foreach($arArquivosDocumentos AS $chave => $arquivo){
            if($arquivo['inId'] == $request->get('inLinha')){
                $stArquivo = $arquivo['stArquivo'];
                $stNomArq = $arquivo['name'];

                break;
            }
        }

        if($stNomArq != '' && file_exists($stArquivo)){
            header('Content-Description: File Transfer');
            header('Content-Type: application/force-download');
            header('Content-Length: '.filesize($stArquivo));
            header('Content-Disposition: attachment; filename='.$stNomArq);
            readfile($stArquivo);
        }else{
            $stNomArq = ($stNomArq != '') ? ' ('.$stNomArq.')' : '';
            $js .= "alertaAviso('Arquivo Digital".$stNomArq." Não Localizado!','unica','erro','".Sessao::getId()."'); ";
        }
    break;
}

if ($stJs)
    echo $stJs;

if ($js)
    sistemaLegado::executaFrameOculto($js);

?>
